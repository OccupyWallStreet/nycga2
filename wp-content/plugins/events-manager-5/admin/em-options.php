<?php

//Function composing the options subpanel
function em_options_save(){
	global $EM_Notices;
	/*
	 * Here's the idea, we have an array of all options that need super admin approval if in multi-site mode
	 * since options are only updated here, its one place fit all
	 */
	if( current_user_can('activate_plugins') && !empty($_POST['em-submitted']) && check_admin_referer('events-manager-options','_wpnonce') ){
		//Build the array of options here
		$post = $_POST;
		foreach ($_POST as $postKey => $postValue){
			if( substr($postKey, 0, 5) == 'dbem_' ){
				//TODO some more validation/reporting
				$numeric_options = array('dbem_locations_default_limit','dbem_events_default_limit');
				if( in_array($postKey,$numeric_options) && !is_numeric($postValue) ){
					//Do nothing, keep old setting.
				}else{
					//TODO slashes being added?
					update_option($postKey, stripslashes($postValue));
				}
			}
		}
		//set capabilities
		if( !empty($_POST['em_capabilities']) && is_array($_POST['em_capabilities']) && (!is_multisite() || is_multisite() && is_super_admin()) ){
			global $em_capabilities_array, $wp_roles;
			if( is_multisite() && is_network_admin() && $_POST['dbem_ms_global_caps'] == 1 ){
			    //apply_caps_to_blog
				global $current_site,$wpdb;
				$blog_ids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs.' WHERE site_id='.$current_site->id);
				foreach($blog_ids as $blog_id){
					switch_to_blog($blog_id);
				    //normal blog role application
					foreach( $wp_roles->role_objects as $role_name => $role ){
						foreach( array_keys($em_capabilities_array) as $capability){
							if( !empty($_POST['em_capabilities'][$role_name][$capability]) ){
								$role->add_cap($capability);
							}else{
								$role->remove_cap($capability);
							}
						}
					}
					restore_current_blog();
				}
			}elseif( !is_network_admin() ){
			    //normal blog role application
				foreach( $wp_roles->role_objects as $role_name => $role ){
					foreach( array_keys($em_capabilities_array) as $capability){
						if( !empty($_POST['em_capabilities'][$role_name][$capability]) ){
							$role->add_cap($capability);
						}else{
							$role->remove_cap($capability);
						}
					}
				}
			}
		}
		update_option('dbem_flush_needed',1);
		do_action('em_options_save');
		$EM_Notices->add_confirm('<strong>'.__('Changes saved.', 'dbem').'</strong>', true);
		wp_redirect(wp_get_referer());
		exit();
	}
	//Migration
	if( !empty($_GET['em_migrate_images']) && check_admin_referer('em_migrate_images','_wpnonce') && get_option('dbem_migrate_images') ){
		include(plugin_dir_path(__FILE__).'../em-install.php');
		$result = em_migrate_uploads();
		if($result){
			$failed = ( $result['fail'] > 0 ) ? $result['fail'] . ' images failed to migrate.' : '';
			$EM_Notices->add_confirm('<strong>'.$result['success'].' images migrated successfully. '.$failed.'</strong>');
		}
		wp_redirect(admin_url().'edit.php?post_type=event&page=events-manager-options&em_migrate_images');
	}elseif( !empty($_GET['em_not_migrate_images']) && check_admin_referer('em_not_migrate_images','_wpnonce') ){
		delete_option('dbem_migrate_images_nag');
		delete_option('dbem_migrate_images');
	}
	//Uninstall
	if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'uninstall' && !empty($_REQUEST['confirmed']) && check_admin_referer('em_uninstall_'.get_current_user_id().'_wpnonce') && is_super_admin() ){
		if( check_admin_referer('em_uninstall_'.get_current_user_id().'_confirmed','_wpnonce2') ){
			//We have a go to uninstall
			global $wpdb;
			//delete EM posts
			remove_action('before_delete_post',array('EM_Location_Post_Admin','before_delete_post'),10,1);
			remove_action('before_delete_post',array('EM_Event_Post_Admin','before_delete_post'),10,1);
			remove_action('before_delete_post',array('EM_Event_Recurring_Post_Admin','before_delete_post'),10,1);
			$post_ids = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts." WHERE post_type IN ('".EM_POST_TYPE_EVENT."','".EM_POST_TYPE_LOCATION."','event-recurring')");
			foreach($post_ids as $post_id){
				wp_delete_post($post_id);
			}
			//delete categories
			$cat_terms = get_terms(EM_TAXONOMY_CATEGORY, array('hide_empty'=>false));
			foreach($cat_terms as $cat_term){
				wp_delete_term($cat_term->term_id, EM_TAXONOMY_CATEGORY);
			}
			$tag_terms = get_terms(EM_TAXONOMY_TAG, array('hide_empty'=>false));
			foreach($tag_terms as $tag_term){
				wp_delete_term($tag_term->term_id, EM_TAXONOMY_TAG);
			}
			//delete EM tables
			$wpdb->query('DROP TABLE '.EM_EVENTS_TABLE);
			$wpdb->query('DROP TABLE '.EM_BOOKINGS_TABLE);
			$wpdb->query('DROP TABLE '.EM_LOCATIONS_TABLE);
			$wpdb->query('DROP TABLE '.EM_TICKETS_TABLE);
			$wpdb->query('DROP TABLE '.EM_TICKETS_BOOKINGS_TABLE);
			$wpdb->query('DROP TABLE '.EM_RECURRENCE_TABLE);
			$wpdb->query('DROP TABLE '.EM_CATEGORIES_TABLE);
			$wpdb->query('DROP TABLE '.EM_META_TABLE);
			
			//delete options
			$wpdb->query('DELETE FROM '.$wpdb->options.' WHERE option_name LIKE \'em_%\' OR option_name LIKE \'dbem_%\'');
			//deactivate and go!
			deactivate_plugins(array('events-manager/events-manager.php','events-manager-pro/events-manager-pro.php'), true);
			wp_redirect(admin_url('plugins.php?deactivate=true'));
			exit();
		}
	}
	//Reset
	if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'reset' && !empty($_REQUEST['confirmed']) && check_admin_referer('em_reset_'.get_current_user_id().'_wpnonce') && is_super_admin() ){
		if( check_admin_referer('em_reset_'.get_current_user_id().'_confirmed','_wpnonce2') ){
			//We have a go to uninstall
			global $wpdb;
			//delete options
			$wpdb->query('DELETE FROM '.$wpdb->options.' WHERE option_name LIKE \'em_%\' OR option_name LIKE \'dbem_%\'');
			//reset capabilities
			global $em_capabilities_array, $wp_roles;
			foreach( $wp_roles->role_objects as $role_name => $role ){
				foreach( array_keys($em_capabilities_array) as $capability){
					$role->remove_cap($capability);
				}
			}
			//go back to plugin options page
			$EM_Notices->add_confirm(__('Settings have been reset back to default. Your events, locations and categories have not been modified.','dbem'), true);
			wp_redirect(EM_ADMIN_URL.'&page=events-manager-options');
			exit();
		}
	}
	//Force Update Recheck - Workaround for now
	if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'recheck_updates' && check_admin_referer('em_recheck_updates_'.get_current_user_id().'_wpnonce') && is_super_admin() ){
		//force recheck of plugin updates, to refresh dl links
		delete_transient('update_plugins');
		delete_site_transient('update_plugins');
		$EM_Notices->add_confirm(__('If there are any new updates, you should now see them in your Plugins or Updates admin pages.','dbem'), true);
		wp_redirect(wp_get_referer());
		exit();
	}
	//Flag version checking to look at trunk, not tag
	if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'check_devs' && check_admin_referer('em_check_devs_wpnonce') && is_super_admin() ){
		//delete transients, and add a flag to recheck dev version next time round
		delete_transient('update_plugins');
		delete_site_transient('update_plugins');
		update_option('em_check_dev_version', true);
		$EM_Notices->add_confirm(__('Checking for dev versions.','dbem').' '. __('If there are any new updates, you should now see them in your Plugins or Updates admin pages.','dbem'), true);
		wp_redirect(wp_get_referer());
		exit();
	}
	
}
add_action('admin_init', 'em_options_save');

function em_admin_email_test_ajax(){
    if( wp_verify_nonce($_REQUEST['_check_email_nonce'],'check_email') && current_user_can('activate_plugins') ){
        $subject = __("Events Manager Test Email",'dbem');
        $content = __('Congratulations! Your email settings work.','dbem');
        $current_user = get_user_by('id', get_current_user_id());
        $EM_Event = new EM_Event();
        if( $EM_Event->email_send($subject,$content,$current_user->user_email) ){
        	$result = array(
        		'result' => true,
        		'message' => sprintf(__('Email sent succesfully to %s','dbem'),$current_user->user_email)
        	);
        }else{
            $result = array(
            	'result' => false,
            	'message' => __('Email not sent.','dbem')." <ul><li>".implode('</li><li>',$EM_Event->get_errors()).'</li></ul>'
            );
        }
        echo json_encode($result);
    }
    exit();
}
add_action('wp_ajax_em_admin_test_email','em_admin_email_test_ajax');

function em_admin_options_reset_page(){
	if( check_admin_referer('em_reset_'.get_current_user_id().'_wpnonce') && is_super_admin() ){
		?>
		<div class="wrap">		
			<div id='icon-options-general' class='icon32'><br /></div>
			<h2><?php _e('Reset Events Manager','dbem'); ?></h2>
			<p style="color:red; font-weight:bold;"><?php _e('Are you sure you want to reset Events Manager?','dbem')?></p>
			<p style="font-weight:bold;"><?php _e('All your settings, including email templates and template formats for Events Manager will be deleted.','dbem')?></p>
			<p>
				<a href="<?php echo add_query_arg(array('_wpnonce2' => wp_create_nonce('em_reset_'.get_current_user_id().'_confirmed'), 'confirmed'=>1)); ?>" class="button-primary"><?php _e('Reset Events Manager','dbem'); ?></a>
				<a href="<?php echo wp_get_referer(); ?>" class="button-secondary"><?php _e('Cancel','dbem'); ?></a>
			</p>
		</div>		
		<?php
	}
}
function em_admin_options_uninstall_page(){
	if( check_admin_referer('em_uninstall_'.get_current_user_id().'_wpnonce') && is_super_admin() ){
		?>
		<div class="wrap">		
			<div id='icon-options-general' class='icon32'><br /></div>
			<h2><?php _e('Uninstall Events Manager','dbem'); ?></h2>
			<p style="color:red; font-weight:bold;"><?php _e('Are you sure you want to uninstall Events Manager?','dbem')?></p>
			<p style="font-weight:bold;"><?php _e('All your settings and events will be permanently deleted. This cannot be undone.','dbem')?></p>
			<p><?php echo sprintf(__('If you just want to deactivate the plugin, <a href="%s">go to your plugins page</a>.','dbem'), wp_nonce_url(admin_url('plugins.php'))); ?></p>
			<p>
				<a href="<?php echo add_query_arg(array('_wpnonce2' => wp_create_nonce('em_uninstall_'.get_current_user_id().'_confirmed'), 'confirmed'=>1)); ?>" class="button-primary"><?php _e('Uninstall and Deactivate','dbem'); ?></a>
				<a href="<?php echo wp_get_referer(); ?>" class="button-secondary"><?php _e('Cancel','dbem'); ?></a>
			</p>
		</div>		
		<?php
	}
}

function em_admin_options_page() {
	global $wpdb, $EM_Notices;
	//Check for uninstall/reset request
	if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'uninstall' ){
		em_admin_options_uninstall_page();
		return;
	}	
	if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'reset' ){
		em_admin_options_reset_page();
		return;
	}	
	//TODO place all options into an array
	global $events_placeholder_tip, $locations_placeholder_tip, $categories_placeholder_tip, $bookings_placeholder_tip;
	$events_placeholders = '<a href="'.EM_ADMIN_URL .'&amp;page=events-manager-help#event-placeholders">'. __('Event Related Placeholders','dbem') .'</a>';
	$locations_placeholders = '<a href="'.EM_ADMIN_URL .'&amp;page=events-manager-help#location-placeholders">'. __('Location Related Placeholders','dbem') .'</a>';
	$bookings_placeholders = '<a href="'.EM_ADMIN_URL .'&amp;page=events-manager-help#booking-placeholders">'. __('Booking Related Placeholders','dbem') .'</a>';
	$categories_placeholders = '<a href="'.EM_ADMIN_URL .'&amp;page=events-manager-help#category-placeholders">'. __('Category Related Placeholders','dbem') .'</a>';
	$events_placeholder_tip = " ". sprintf(__('This accepts %s and %s placeholders.','dbem'),$events_placeholders, $locations_placeholders);
	$locations_placeholder_tip = " ". sprintf(__('This accepts %s placeholders.','dbem'), $locations_placeholders);
	$categories_placeholder_tip = " ". sprintf(__('This accepts %s placeholders.','dbem'), $categories_placeholders);
	$bookings_placeholder_tip = " ". sprintf(__('This accepts %s, %s and %s placeholders.','dbem'), $bookings_placeholders, $events_placeholders, $locations_placeholders);
	
	global $save_button;
	$save_button = '<tr><th>&nbsp;</th><td><p class="submit" style="margin:0px; padding:0px; text-align:right;"><input type="submit" id="dbem_options_submit" name="Submit" value="'. __( 'Save Changes', 'dbem') .' ('. __('All','dbem') .')" /></p></ts></td></tr>';
	?>
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function($){
			//Meta Box Options
			var close_text = '<?php _e('Collapse All','dbem'); ?>';
			var open_text = '<?php _e('Expand All','dbem'); ?>';
			var open_close = $('<a href="#" style="display:block; float:right; clear:right; margin:10px;">'+open_text+'</a>');
			$('#em-options-title').before(open_close);
			open_close.click( function(e){
				e.preventDefault();
				if($(this).text() == close_text){
					$(".postbox").addClass('closed');
					$(this).text(open_text);
				}else{
					$(".postbox").removeClass('closed');
					$(this).text(close_text);
				} 
			});
			$(".postbox > h3").click(function(){ $(this).parent().toggleClass('closed'); });
			$(".postbox").addClass('closed');
			//Navigation Tabs
			$('.nav-tab-wrapper .nav-tab').click(function(){
				$('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
				el = $(this);
				elid = el.attr('id');
				$('.em-menu-group').hide(); 
				$('.'+elid).show();
				el.addClass('nav-tab-active');
				$(".postbox").addClass('closed');
				open_close.text(open_text);
			});
			var navUrl = document.location.toString();
			if (navUrl.match('#')) { //anchor-based navigation
				var current_tab = 'a#em-menu-' + navUrl.split('#')[1];
				$(current_tab).trigger('click');
			}
			$('.nav-tab-link').click(function(){ $($(this).attr('rel')).trigger('click'); }); //links to mimick tabs
			//Page Options
			$('input[name="dbem_cp_events_has_archive"]').change(function(){ //event archives
				if( $('input:radio[name="dbem_cp_events_has_archive"]:checked').val() == 1 ){
					$('tbody.em-event-archive-sub-options').show();
				}else{
					$('tbody.em-event-archive-sub-options').hide();
				}
			}).trigger('change');
			$('select[name="dbem_events_page"]').change(function(){
				if( $('select[name="dbem_events_page"]').val() == 0 ){
					$('tbody.em-event-page-options').hide();
				}else{
					$('tbody.em-event-page-options').show();
				}
			}).trigger('change');
			$('input[name="dbem_cp_locations_has_archive"]').change(function(){ //location archives
				if( $('input:radio[name="dbem_cp_locations_has_archive"]:checked').val() == 1 ){
					$('tbody.em-location-archive-sub-options').show();
				}else{
					$('tbody.em-location-archive-sub-options').hide();
				}
			}).trigger('change');
			//For rewrite titles
			$('input:radio[name=dbem_disable_title_rewrites]').live('change',function(){
				checked_check = $('input:radio[name=dbem_disable_title_rewrites]:checked');
				if( checked_check.val() == 1 ){
					$('#dbem_title_html_row').show();
				}else{
					$('#dbem_title_html_row').hide();	
				}
			});
			$('input:radio[name=dbem_disable_title_rewrites]').trigger('change');
		});
	</script>
	<style type="text/css">.postbox h3 { cursor:pointer; }</style>
	<div class="wrap">		
		<div id='icon-options-general' class='icon32'><br /></div>
		<h2 class="nav-tab-wrapper">
			<a href="#general" id="em-menu-general" class="nav-tab nav-tab-active"><?php _e('General','dbem'); ?></a>
			<a href="#pages" id="em-menu-pages" class="nav-tab"><?php _e('Pages','dbem'); ?></a>
			<a href="#formats" id="em-menu-formats" class="nav-tab"><?php _e('Formatting','dbem'); ?></a>
			<?php if( get_option('dbem_rsvp_enabled') ): ?>
			<a href="#bookings" id="em-menu-bookings" class="nav-tab"><?php _e('Bookings','dbem'); ?></a>
			<?php endif; ?>
			<a href="#emails" id="em-menu-emails" class="nav-tab"><?php _e('Emails','dbem'); ?></a>
		</h2>
		<h3 id="em-options-title"><?php _e ( 'Event Manager Options', 'dbem' ); ?></h3>
		<form id="em-options-form" method="post" action="">
			<div class="metabox-holder">         
			<!-- // TODO Move style in css -->
			<div class='postbox-container' style='width: 99.5%'>
			<div id="">
		  
		  	<div class="em-menu-general em-menu-group">
			  
			  	<!-- GENERAL OPTIONS -->
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'General Options', 'dbem' ); ?> </span></h3>
				<div class="inside">
		            <table class="form-table">
			            <?php em_options_radio_binary ( __( 'Disable thumbnails?', 'dbem' ), 'dbem_thumbnails_enabled', __( 'Select yes to disable Events Manager from enabling thumbnails (some themes may already have this enabled, which we cannot be turned off here).','dbem' ) );  ?>					
						<tr>
							<td colspan="2">
								<h4><?php echo sprintf(__('%s Settings','dbem'),__('Event','dbem')); ?></h4>
							</td>
						</tr>
						<?php
						em_options_radio_binary ( __( 'Enable recurrence?', 'dbem' ), 'dbem_recurrence_enabled', __( 'Select yes to enable the recurrence features feature','dbem' ) ); 
						em_options_radio_binary ( __( 'Enable bookings?', 'dbem' ), 'dbem_rsvp_enabled', __( 'Select yes to allow bookings and tickets for events.','dbem' ) );     
						em_options_radio_binary ( __( 'Enable tags?', 'dbem' ), 'dbem_tags_enabled', __( 'Select yes to enable the tag features','dbem' ) );
						if( !(EM_MS_GLOBAL && !is_main_blog()) ){
							em_options_radio_binary ( __( 'Enable categories?', 'dbem' ), 'dbem_categories_enabled', __( 'Select yes to enable the category features','dbem' ) );     
							if( get_option('dbem_categories_enabled') ){
								/*default category*/
								$category_options = array();
								$category_options[0] = __('no default category','dbem');
								$EM_Categories = EM_Categories::get();
								foreach($EM_Categories as $EM_Category){
							 		$category_options[$EM_Category->id] = $EM_Category->name;
							 	}
							 	echo "<tr><td>".__( 'Default Category', 'dbem' )."</td><td>";
								wp_dropdown_categories(array( 'hide_empty' => 0, 'name' => 'dbem_default_category', 'hierarchical' => true, 'taxonomy' => EM_TAXONOMY_CATEGORY, 'selected' => get_option('dbem_default_category'), 'show_option_none' => __('None','dbem'), 'class'=>''));
								echo "</br><em>" .__( 'This option allows you to select the default category when adding an event.','dbem' ).' '.__('If an event does not have a category assigned when editing, this one will be assigned automatically.','dbem')."</em>";
								echo "</td></tr>";
							}
						}
						em_options_radio_binary ( sprintf(__( 'Enable %s attributes?', 'dbem' ),__('event','dbem')), 'dbem_attributes_enabled', __( 'Select yes to enable the attributes feature','dbem' ) );
						em_options_radio_binary ( sprintf(__( 'Enable %s custom fields?', 'dbem' ),__('event','dbem')), 'dbem_cp_events_custom_fields', __( 'Custom fields are the same as attributes, except you cannot restrict specific values, users can add any kind of custom field name/value pair. Only available in the WordPress admin area.','dbem' ) );
						if( get_option('dbem_attributes_enabled') ){
							em_options_textarea ( sprintf(__( '%s Attributes', 'dbem' ),__('Event','dbem')), 'dbem_placeholders_custom', sprintf(__( "You can also add event attributes here, one per line in this format <code>#_ATT{key}</code>. They will not appear on event pages unless you insert them into another template below, but you may want to store extra information about an event for other uses. <a href='%s'>More information on placeholders.</a>", 'dbem' ), EM_ADMIN_URL .'&amp;page=events-manager-help') );
						}
						if( get_option('dbem_locations_enabled') ){
							/*default location*/
							$location_options = array();
							$location_options[0] = __('no default location','dbem');
							$EM_Locations = EM_Locations::get();
							foreach($EM_Locations as $EM_Location){
						 		$location_options[$EM_Location->location_id] = $EM_Location->location_name;
						 	}
							em_options_select ( __( 'Default Location', 'dbem' ), 'dbem_default_location', $location_options, __( 'This option allows you to select the default location when adding an event.','dbem' )." ".__('(not applicable with event ownership on presently, coming soon!)','dbem') );
							
							/*default location country*/
							em_options_select ( __( 'Default Location Country', 'dbem' ), 'dbem_location_default_country', em_get_countries(__('no default country', 'dbem')), __('If you select a default country, that will be pre-selected when creating a new location.','dbem') );
						}
						?>
						<tr>
							<td colspan="2">
								<h4><?php echo sprintf(__('%s Settings','dbem'),__('Location','dbem')); ?></h4>
							</td>
						</tr>
						<?php
						em_options_radio_binary ( __( 'Enable locations?', 'dbem' ), 'dbem_locations_enabled', __( 'If you disable locations, bear in mind that you should remove your location page, shortcodes and related placeholders from your formats.','dbem' ) );
						if( get_option('dbem_locations_enabled') ){ 
							em_options_radio_binary ( __( 'Require locations for events?', 'dbem' ), 'dbem_require_location', __( 'Setting this to no will allow you to submit events without locations. You can use the <code>{no_location}...{/no_location}</code> or <code>{has_location}..{/has_location}</code> conditional placeholder to selectively display location information.','dbem' ) );
							em_options_radio_binary ( __( 'Use dropdown for locations?', 'dbem' ), 'dbem_use_select_for_locations', __( 'Select yes to select location from a drow-down menu; location selection will be faster, but you will lose the ability to insert locations with events','dbem' ) );
							em_options_radio_binary ( sprintf(__( 'Enable %s attributes?', 'dbem' ),__('location','dbem')), 'dbem_location_attributes_enabled', __( 'Select yes to enable the attributes feature','dbem' ) );
							em_options_radio_binary ( sprintf(__( 'Enable %s custom fields?', 'dbem' ),__('location','dbem')), 'dbem_cp_locations_custom_fields', __( 'Custom fields are the same as attributes, except you cannot restrict specific values, users can add any kind of custom field name/value pair. Only available in the WordPress admin area.','dbem' ) );
							if( get_option('dbem_location_attributes_enabled') ){
								em_options_textarea ( sprintf(__( '%s Attributes', 'dbem' ),__('Location','dbem')), 'dbem_location_placeholders_custom', sprintf(__( "You can also add location attributes here, one per line in this format <code>#_LATT{key}</code>. They will not appear on location pages unless you insert them into another template below, but you may want to store extra information about an event for other uses. <a href='%s'>More information on placeholders.</a>", 'dbem' ), EM_ADMIN_URL .'&amp;page=events-manager-help') );
							}
						}
						?>
						<tr>
							<td colspan="2">
								<h4><?php echo sprintf(__('%s Settings','dbem'),__('Other','dbem')); ?></h4>
							</td>
						</tr>
						<?php
						em_options_radio_binary ( __('Show some love?','dbem'), 'dbem_credits', __( 'Hundreds of free hours have gone into making this free plugin, show your support and add a small link to the plugin website at the bottom of your event pages.','dbem' ) );
						echo $save_button;
						?>
					</table>
					    
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->
				
				<?php if ( !is_multisite() ){ em_admin_option_box_image_sizes(); } ?>
				
				<?php if ( !is_multisite() || (is_super_admin() && !get_site_option('dbem_ms_global_caps')) ){ em_admin_option_box_caps(); } ?>
				
				<div  class="postbox" >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Event Submission Forms', 'dbem' ); ?></span></h3>
				<div class="inside">
			            <table class="form-table">
			            <tr><td colspan="2">
			            	<?php echo sprintf(__('You can allow users to publicly submit events on your blog by using the %s shortcode, and enabling anonymous submissions below.','dbem'), '<code>[event_form]</code>'); ?>
						</td></tr>
						<?php
							em_options_radio_binary ( __( 'Use Visual Editor?', 'dbem' ), 'dbem_events_form_editor', __( 'Users can now use the WordPress editor for easy HTML entry in the submission form.', 'dbem' ) );
							em_options_radio_binary ( __( 'Show form again?', 'dbem' ), 'dbem_events_form_reshow', __( 'When a user submits their event, you can display a new event form again.', 'dbem' ) );
							em_options_textarea ( __( 'Success Message', 'dbem' ), 'dbem_events_form_result_success', __( 'Customize the message your user sees when they submitted their event.', 'dbem' ).$events_placeholder_tip );
						?>
			            <tr><td colspan="2">
			            	<strong><?php echo sprintf(__('Anonymous event submissions','dbem'), '<code>[event_form]</code>'); ?></strong>
						</td></tr>
			            <?php
							em_options_radio_binary ( __( 'Allow anonymous event submissions?', 'dbem' ), 'dbem_events_anonymous_submissions', __( 'Would you like to allow users to submit bookings anonymously? If so, you can use the new [event_form] shortcode or <code>em_event_form()</code> template tag with this enabled.', 'dbem' ) );
			            	em_options_select ( __('Guest Default User', 'dbem'), 'dbem_events_anonymous_user', em_get_wp_users (), __( 'Events require a user to own them. In order to allow events to be submitted anonymously you need to assign that event a specific user. We recommend you create a "Anonymous" subscriber with a very good password and use that. Guests will have the same event permissions as this user when submitting.', 'dbem' ) );
			            	em_options_textarea ( __( 'Success Message', 'dbem' ), 'dbem_events_anonymous_result_success', __( 'Anonymous submitters cannot see or modify their event once submitted. You can customize the success message they see here.', 'dbem' ).$events_placeholder_tip );
						?>
				        <?php echo $save_button; ?>
					</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox --> 

				<?php do_action('em_options_page_footer'); ?>
				
				<?php if ( !is_multisite() ) { em_admin_option_box_uninstall(); } ?>
				
				<?php if( get_option('dbem_migrate_images') ): ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span>Migrate Images From Version 4</span></h3>
				<div class="inside">
					<?php /* Not translating as it's temporary */ ?>
				   <p>You have the option of migrating images from version 4 so they become the equivalent of 'featured images' like with regular WordPress posts and pages and are also available in your media library.</p>
				   <p>Your event and location images will still display correctly on the front-end even if you don't migrate, but will not show up within your edit location/event pages in the admin area.</p>
				   <p>
				      <a href="<?php echo $_SERVER['REQUEST_URI'] ?>&amp;em_migrate_images=1&amp;_wpnonce=<?php echo wp_create_nonce('em_migrate_images'); ?>" />Migrate Images</a><br />
				      <a href="<?php echo $_SERVER['REQUEST_URI'] ?>&amp;em_not_migrate_images=1&amp;_wpnonce=<?php echo wp_create_nonce('em_not_migrate_images'); ?>" />Do Not Migrate Images</a>
				   </p>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->
				<?php endif; ?>
			</div> <!-- .em-menu-general -->
			
			<!-- PAGE OPTIONS -->
		  	<div class="em-menu-pages em-menu-group" style="display:none;">			
            	<?php
            	$template_page_tip = __( "Many themes display extra meta information on post pages such as 'posted by' or 'post date' information, which may not be desired. Usually, page templates contain less clutter.", 'dbem' );
            	$template_page_tip .= str_replace('#','http://codex.wordpress.org/Post_Types#Template_Files',__("Be aware that some themes will not work with this option, if so (or you want to make your own changes), you can create a file named <code>single-%s.php</code> <a href='#'>as shown on the wordpress codex</a>, and leave this set to Posts.", 'dbem'));
            	$format_override_tip = __("By using formats, you can control how your %s are displayed from within the Events Manager <a href='#formats' class='nav-tab-link' rel='#em-menu-formats'>Formats</a> tab above without having to edit your theme files.")
            	?>
            	<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__('Permalink Slugs','dbem')); ?></span></h3>
				<div class="inside">
					<p><?php _e('You can change the permalink structure of your events, locations, categories and tags here. Be aware that you may want to set up redirects if you change your permalink structures to maintain SEO rankings.','dbem'); ?></p>
	            	<table class="form-table">
	            	<?php
	            	em_options_input_text ( __( 'Events', 'dbem' ), 'dbem_cp_events_slug', sprintf(__('e.g. %s - you can use / Separators too', 'dbem' ), '<strong>'.home_url().'/<code>'.get_option('dbem_cp_events_slug',EM_POST_TYPE_EVENT_SLUG).'</code>/2012-olympics/</strong>'), EM_POST_TYPE_EVENT_SLUG );
					if( get_option('dbem_locations_enabled') ){
		            	em_options_input_text ( __( 'Locations', 'dbem' ), 'dbem_cp_locations_slug', sprintf(__('e.g. %s - you can use / Separators too', 'dbem' ), '<strong>'.home_url().'/<code>'.get_option('dbem_cp_locations_slug',EM_POST_TYPE_LOCATION_SLUG).'</code>/wembley-stadium/</strong>'), EM_POST_TYPE_LOCATION_SLUG );
					}
	            	if( get_option('dbem_categories_enabled') && !(EM_MS_GLOBAL && !is_main_blog()) ){
	            		em_options_input_text ( __( 'Event Categories', 'dbem' ), 'dbem_taxonomy_category_slug', sprintf(__('e.g. %s - you can use / Separators too', 'dbem' ), '<strong>'.home_url().'/<code>'.get_option('dbem_taxonomy_category_slug',EM_TAXONOMY_CATEGORY_SLUG).'</code>/sports/</strong>'), EM_TAXONOMY_CATEGORY_SLUG );
	            	}
	            	if( get_option('dbem_tags_enabled') ){
		            	em_options_input_text ( __( 'Event Tags', 'dbem' ), 'dbem_taxonomy_tag_slug', sprintf(__('e.g. %s - you can use / Separators too', 'dbem' ), '<strong>'.home_url().'/<code>'.get_option('dbem_taxonomy_tag_slug',EM_TAXONOMY_TAG_SLUG).'</code>/running/</strong>'), EM_TAXONOMY_TAG_SLUG );
	            	}
	            	echo $save_button;
	            	?>
	            	</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->	

				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__('%s Pages','dbem'),__('Event','dbem')); ?></span></h3>
				<div class="inside">
	            	<table class="form-table">
	            	<?php
	            	em_options_radio_binary ( sprintf(__( 'Display %s as', 'dbem' ),__('events','dbem')), 'dbem_cp_events_template_page', sprintf($template_page_tip, EM_POST_TYPE_EVENT), array(__('Posts'),__('Pages')) );
	            	em_options_radio_binary ( __( 'Override with Formats?', 'dbem' ), 'dbem_cp_events_formats', sprintf($format_override_tip,__('events','dbem')));
	            	em_options_radio_binary ( __( 'Enable Comments?', 'dbem' ), 'dbem_cp_events_comments', sprintf(__('If you would like to disable comments entirely, disable this, otherwise you can disable comments on each single %s. Note that %s with comments enabled will still be until you resave them.','dbem'),__('event','dbem'),__('events','dbem')));
					echo $save_button;
	            	?>
	            	</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->	
            		
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__('%s List/Archives','dbem'),__('Event','dbem')); ?></span></h3>
				<div class="inside">
	            	<table class="form-table">
	            	<?php //WordPress Pages
				 	$get_pages = get_pages();
				 	$events_page_options = array();
				 	$events_page_options[0] = sprintf(__('[No %s Page]', 'dbem'),__('Events','dbem'));
				 	//TODO Add the hierarchy style ddm, like when choosing page parents
				 	foreach($get_pages as $page){
				 		$events_page_options[$page->ID] = $page->post_title;
				 	}
				   	em_options_select ( __( 'Events page', 'dbem' ), 'dbem_events_page', $events_page_options, __( 'This option allows you to select which page to use as an events page. If you do not select an events page, to display event lists you can enable event archives or use the appropriate shortcodes and/or template tags.','dbem' ) );
					?>
					<tbody class="em-event-page-options">
						<?php 
						em_options_radio_binary ( __( 'Show events search?', 'dbem' ), 'dbem_events_page_search', __( "If set to yes, a search form will appear just above your list of events.", 'dbem' ) );
						em_options_radio_binary ( __( 'Display calendar in events page?', 'dbem' ), 'dbem_display_calendar_in_events_page', __( 'This options allows to display the calendar in the events page, instead of the default list. It is recommended not to display both the calendar widget and a calendar page.','dbem' ).' '.__('If you would like to show events that span over more than one day, see the Calendar section on this page.','dbem') );
						em_options_radio_binary ( __( 'Disable title rewriting?', 'dbem' ), 'dbem_disable_title_rewrites', __( "Some WordPress themes don't follow best practices when generating navigation menus, and so the automatic title rewriting feature may cause problems, if your menus aren't working correctly on the event pages, try setting this to 'Yes', and provide an appropriate HTML title format below.",'dbem' ) );
						em_options_input_text ( __( 'Event Manager titles', 'dbem' ), 'dbem_title_html', __( "This only setting only matters if you selected 'Yes' to above. You will notice the events page titles aren't being rewritten, and you have a new title underneath the default page name. This is where you control the HTML of this title. Make sure you keep the #_PAGETITLE placeholder here, as that's what is rewritten by events manager. To control what's rewritten in this title, see settings further down for page titles.", 'dbem' ) );
						?>				
					</tbody>
					<tr>
						<td colspan="2">
							<h4><?php echo sprintf(__('WordPress %s Archives','dbem'), __('Event','dbem')); ?></h4>
							<p><?php echo sprintf(__('%s custom post types can have archives, just like normal WordPress posts. If enabled, should you visit your base slug url %s and you will see an post-formatted archive of previous %s'), __('Event','dbem'), '<code>'.home_url().'/'.get_option('dbem_cp_events_slug',EM_POST_TYPE_EVENT_SLUG).'/</code>', __('events','dbem')); ?></p>
							<p><?php echo sprintf(__('Note that assigning a %s page above will override this archive if the URLs collide (which is the default settings, and is recommended). You can have both at the same time, but you must ensure that your page and %s slugs are different.'), __('events','dbem'), __('event','dbem')); ?></p>
						</td>
					</tr>
					<tbody class="em-event-archive-options">
						<?php
						em_options_radio_binary ( __( 'Enable Archives?', 'dbem' ), 'dbem_cp_events_has_archive', __( "Allow WordPress post-style archives.", 'dbem' ) );
						?>
					</tbody>
					<tbody class="em-event-archive-options em-event-archive-sub-options">
						<tr valign="top">
					   		<th scope="row"><?php _e('Default event archive ordering','dbem'); ?></th>
					   		<td>   
								<select name="dbem_events_default_archive_orderby" >
									<?php 
										$event_archive_orderby_options = apply_filters('em_settings_events_default_archive_orderby_ddm', array(
											'_start_ts' => __('Order by start date, start time','dbem'),
											'title' => __('Order by name','dbem')
										)); 
									?>
									<?php foreach($event_archive_orderby_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_events_default_archive_orderby')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select> 
								<select name="dbem_events_default_archive_order" >
									<?php 
									$ascending = __('Ascending','dbem');
									$descending = __('Descending','dbem');
									$event_archive_order_options = apply_filters('em_settings_events_default_archive_order_ddm', array(
										'ASC' => __('Ascending','dbem'),
										'DESC' => __('Descending','dbem')
									)); 
									?>
									<?php foreach( $event_archive_order_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_events_default_archive_order')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select>
								<br/>
								<em><?php _e('When Events Manager displays lists of events the default behaviour is ordering by start date in ascending order. To change this, modify the values above.','dbem'); ?></em>
							</td>
					   	</tr>	
					</tbody>
					<tr>
						<td colspan="2">
							<h4><?php echo _e('General settings','dbem'); ?></h4>
						</td>
					</tr>	
					<?php
					em_options_radio_binary ( __( 'Override with Formats?', 'dbem' ), 'dbem_cp_events_archive_formats', sprintf($format_override_tip,__('events','dbem')));
					em_options_radio_binary ( __( 'Are current events past events?', 'dbem' ), 'dbem_events_current_are_past', __( "By default, events that are have an end date later than today will be included in searches, set this to yes to consider events that started 'yesterday' as past.", 'dbem' ) );
					em_options_radio_binary ( __( 'Include in WordPress Searches?', 'dbem' ), 'dbem_cp_events_search_results', sprintf(__( "Allow %s to appear in the built-in search results.", 'dbem' ),__('events','dbem')) );
					?>
					<tr>
						<td colspan="2">
							<h4><?php echo sprintf(__('Default %s list options','dbem'), __('event','dbem')); ?></h4>
							<p><?php _e('These can be overriden when using shortcode or template tags.','dbem'); ?></p>
						</td>
					</tr>							
					<tr valign="top" id='dbem_events_default_orderby_row'>
				   		<th scope="row"><?php _e('Default event list ordering','dbem'); ?></th>
				   		<td>   
							<select name="dbem_events_default_orderby" >
								<?php 
									$orderby_options = apply_filters('em_settings_events_default_orderby_ddm', array(
										'event_start_date,event_start_time,event_name' => __('Order by start date, start time, then event name','dbem'),
										'event_name,event_start_date,event_start_time' => __('Order by name, start date, then start time','dbem'),
										'event_name,event_end_date,event_end_time' => __('Order by name, end date, then end time','dbem'),
										'event_end_date,event_end_time,event_name' => __('Order by end date, end time, then event name','dbem'),
									)); 
								?>
								<?php foreach($orderby_options as $key => $value) : ?>   
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_events_default_orderby')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select> 
							<select name="dbem_events_default_order" >
								<?php 
								$ascending = __('Ascending','dbem');
								$descending = __('Descending','dbem');
								$order_options = apply_filters('em_settings_events_default_order_ddm', array(
									'ASC' => __('All Ascending','dbem'),
									'DESC,ASC,ASC' => __("$descending, $ascending, $ascending",'dbem'),
									'DESC,DESC,ASC' => __("$descending, $descending, $ascending",'dbem'),
									'DESC' => __('All Descending','dbem'),
									'ASC,DESC,ASC' => __("$ascending, $descending, $ascending",'dbem'),
									'ASC,DESC,DESC' => __("$ascending, $descending, $descending",'dbem'),
									'ASC,ASC,DESC' => __("$ascending, $ascending, $descending",'dbem'),
									'DESC,ASC,DESC' => __("$descending, $ascending, $descending",'dbem'),
								)); 
								?>
								<?php foreach( $order_options as $key => $value) : ?>   
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_events_default_order')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select>
							<br/>
							<em><?php _e('When Events Manager displays lists of events the default behaviour is ordering by start date in ascending order. To change this, modify the values above.','dbem'); ?></em>
						</td>
				   	</tr>										   	
					<tr valign="top" id='dbem_events_display_time_limit'>
				   		<th scope="row"><?php _e('Event list scope','dbem'); ?></th>
						<td>
							<select name="dbem_events_page_scope" >
								<?php foreach( em_get_scopes() as $key => $value) : ?>   
								<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_events_page_scope')) ? "selected='selected'" : ''; ?>>
									<?php echo $value; ?>
								</option>
								<?php endforeach; ?>
							</select>
							<br />
							<em><?php _e('Only show events starting within a certain time limit on the events page. Default is future events with no end time limit.','dbem'); ?></em>
						</td>
					</tr>
					<?php
					em_options_input_text ( __( 'Event List Limits', 'dbem' ), 'dbem_events_default_limit', __( "This will control how many events are shown on one list by default.", 'dbem' ) );
					echo $save_button;
	            	?>
	            	</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->	
				
				<?php if( get_option('dbem_locations_enabled') ): ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__('%s Pages','dbem'),__('Location','dbem')); ?></span></h3>
				<div class="inside">
	            	<table class="form-table">
	            	<?php 
	            	em_options_radio_binary ( sprintf(__( 'Display %s as', 'dbem' ),__('locations','dbem')), 'dbem_cp_locations_template_page', sprintf($template_page_tip, EM_POST_TYPE_LOCATION), array(__('Posts'),__('Pages')) );
	            	em_options_radio_binary ( __( 'Override with Formats?', 'dbem' ), 'dbem_cp_locations_formats', sprintf($format_override_tip,__('locations','dbem')));
	            	em_options_radio_binary ( __( 'Enable Comments?', 'dbem' ), 'dbem_cp_locations_comments', sprintf(__('If you would like to disable comments entirely, disable this, otherwise you can disable comments on each single %s. Note that %s with comments enabled will still be until you resave them.','dbem'),__('location','dbem'),__('locations','dbem')));
	            	echo $save_button;
					?>
	            	</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->	
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__('%s List/Archives','dbem'),__('Location','dbem')); ?></span></h3>
				<div class="inside">
	            	<table class="form-table">
	            	<?php
				 	$events_page_options[0] = sprintf(__('[No %s Page]', 'dbem'),__('Locations','dbem'));
	            	em_options_select ( sprintf(__( '%s page', 'dbem' ),__('Locations','dbem')), 'dbem_locations_page', $events_page_options, sprintf(__( 'This option allows you to select which page to use as the %s page. If you do not select no %s page, to display lists you can enable archives or use the appropriate shortcodes and/or template tags.','dbem' ),__('locations','dbem'),__('locations','dbem')) );
					?>
					<tr>
						<td colspan="2">
							<h4><?php echo sprintf(__('WordPress %s Archives','dbem'), __('Location','dbem')); ?></h4>
							<p><?php echo sprintf(__('%s custom post types can have archives, just like normal WordPress posts. If enabled, should you visit your base slug url %s and you will see an post-formatted archive of previous %s'), __('Location','dbem'), '<code>'.home_url().'/'.get_option('dbem_cp_events_slug',EM_POST_TYPE_EVENT_SLUG).'/</code>', __('locations','dbem')); ?></p>
							<p><?php echo sprintf(__('Note that assigning a %s page above will override this archive if the URLs collide (which is the default settings, and is recommended for maximum plugin compatability). You can have both at the same time, but you must ensure that your page and %s slugs are different.'), __('locations','dbem'), __('location','dbem')); ?></p>
						</td>
					</tr>
					<tbody class="em-location-archive-options">
						<?php
						em_options_radio_binary ( __( 'Enable Archives?', 'dbem' ), 'dbem_cp_locations_has_archive', __( "Allow WordPress post-style archives.", 'dbem' ) );						
						?>
					</tbody>
					<tbody class="em-location-archive-options em-location-archive-sub-options">
						<tr valign="top">
					   		<th scope="row"><?php _e('Default archive ordering','dbem'); ?></th>
					   		<td>   
								<select name="dbem_locations_default_archive_orderby" >
									<?php 
										$orderby_options = apply_filters('em_settings_locations_default_archive_orderby_ddm', array(
											'_country' => sprintf(__('Order by %s','dbem'),__('Country','dbem')),
											'_town' => sprintf(__('Order by %s','dbem'),__('Town','dbem')),
											'title' => sprintf(__('Order by %s','dbem'),__('Name','dbem'))
										)); 
									?>
									<?php foreach($orderby_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_locations_default_archive_orderby')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select> 
								<select name="dbem_locations_default_archive_order" >
									<?php 
									$ascending = __('Ascending','dbem');
									$descending = __('Descending','dbem');
									$order_options = apply_filters('em_settings_locations_default_archive_order_ddm', array(
										'ASC' => __('Ascending','dbem'),
										'DESC' => __('Descending','dbem')
									)); 
									?>
									<?php foreach( $order_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_locations_default_archive_order')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select>
							</td>
					   	</tr>	
					</tbody>
					<tr>
						<td colspan="2">
							<h4><?php echo _e('General settings','dbem'); ?></h4>
						</td>
					</tr>
					<?php 
					em_options_radio_binary ( __( 'Override with Formats?', 'dbem' ), 'dbem_cp_locations_archive_formats', sprintf($format_override_tip,__('locations','dbem')));
	            	em_options_radio_binary ( __( 'Include in WordPress Searches?', 'dbem' ), 'dbem_cp_locations_search_results', sprintf(__( "Allow %s to appear in the built-in search results.", 'dbem' ),__('locations','dbem')) );
					?>
					<tr>
						<td colspan="2">
							<h4><?php echo sprintf(__('Default %s list options','dbem'), __('location','dbem')); ?></h4>
							<p><?php _e('These can be overriden when using shortcode or template tags.','dbem'); ?></p>
						</td>
					</tr>							
					<tr valign="top" id='dbem_locations_default_orderby_row'>
				   		<th scope="row"><?php _e('Default list ordering','dbem'); ?></th>
				   		<td>   
							<select name="dbem_locations_default_orderby" >
								<?php 
									$orderby_options = apply_filters('em_settings_locations_default_orderby_ddm', array(
										'location_country' => sprintf(__('Order by %s','dbem'),__('Country','dbem')),
										'location_town' => sprintf(__('Order by %s','dbem'),__('Town','dbem')),
										'location_name' => sprintf(__('Order by %s','dbem'),__('Name','dbem'))
									)); 
								?>
								<?php foreach($orderby_options as $key => $value) : ?>
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_locations_default_orderby')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select> 
							<select name="dbem_locations_default_order" >
								<?php 
								$ascending = __('Ascending','dbem');
								$descending = __('Descending','dbem');
								$order_options = apply_filters('em_settings_locations_default_order_ddm', array(
									'ASC' => __('Ascending','dbem'),
									'DESC' => __('Descending','dbem')
								)); 
								?>
								<?php foreach( $order_options as $key => $value) : ?>   
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_locations_default_order')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select>
						</td>
				   	</tr>
					<?php
					em_options_input_text ( __( 'List Limits', 'dbem' ), 'dbem_locations_default_limit', sprintf(__( "This will control how many %s are shown on one list by default.", 'dbem' ),__('locations','dbem')) );
	            	echo $save_button;
					?>
	            	</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->
				<?php endif; ?>
				
				<?php if( get_option('dbem_categories_enabled') && !(EM_MS_GLOBAL && !is_main_blog()) ): ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo __('Event Categories','dbem'); ?></span></h3>
				<div class="inside">
	            	<table class="form-table">
	            	<?php
				 	$events_page_options[0] = sprintf(__('[No %s Page]', 'dbem'),__('Categories','dbem'));
	            	em_options_select ( sprintf(__( '%s page', 'dbem' ),__('Categories','dbem')), 'dbem_categories_page', $events_page_options, sprintf(__( 'This option allows you to select which page to use as the %s page.','dbem' ),__('categories','dbem'),__('categories','dbem')) );
					?>
					<tr>
						<td colspan="2">
							<h4><?php echo _e('General settings','dbem'); ?></h4>
						</td>
					</tr>
					<?php
					em_options_radio_binary ( __( 'Override with Formats?', 'dbem' ), 'dbem_cp_categories_formats', sprintf($format_override_tip,__('categories','dbem'))." ".__('Setting this to yes will make categories display as a page rather than an archive.', 'dbem'));
					?>
					<tr valign="top">
				   		<th scope="row"><?php _e('Default archive ordering','dbem'); ?></th>
				   		<td>   
							<select name="dbem_categories_default_archive_orderby" >
								<?php foreach($event_archive_orderby_options as $key => $value) : ?>   
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_categories_default_archive_orderby')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select> 
							<select name="dbem_categories_default_archive_order" >
								<?php foreach( $event_archive_order_options as $key => $value) : ?>   
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_categories_default_archive_order')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select>
							<br /><?php echo __('When listing events for a category, this order is applied.', 'dbem'); ?>
						</td>
				   	</tr>
					<tr>
						<td colspan="2">
							<h4><?php echo sprintf(__('Default %s list options','dbem'), __('category','dbem')); ?></h4>
							<p><?php _e('These can be overriden when using shortcode or template tags.','dbem'); ?></p>
						</td>
					</tr>							
					<tr valign="top" id='dbem_categories_default_orderby_row'>
				   		<th scope="row"><?php _e('Default list ordering','dbem'); ?></th>
				   		<td>   
							<select name="dbem_categories_default_orderby" >
								<?php 
									$orderby_options = apply_filters('em_settings_categories_default_orderby_ddm', array(
										'id' => sprintf(__('Order by %s','dbem'),__('ID','dbem')),
										'count' => sprintf(__('Order by %s','dbem'),__('Count','dbem')),
										'name' => sprintf(__('Order by %s','dbem'),__('Name','dbem')),
										'slug' => sprintf(__('Order by %s','dbem'),__('Slug','dbem')),
										'term_group' => sprintf(__('Order by %s','dbem'),'term_group'),
									)); 
								?>
								<?php foreach($orderby_options as $key => $value) : ?>
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_categories_default_orderby')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select> 
							<select name="dbem_categories_default_order" >
								<?php 
								$ascending = __('Ascending','dbem');
								$descending = __('Descending','dbem');
								$order_options = apply_filters('em_settings_categories_default_order_ddm', array(
									'ASC' => __('Ascending','dbem'),
									'DESC' => __('Descending','dbem')
								)); 
								?>
								<?php foreach( $order_options as $key => $value) : ?>   
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_categories_default_order')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select>
							<br /><?php echo __('When listing categories, this order is applied.', 'dbem'); ?>
						</td>
				   	</tr>
					<?php
					em_options_input_text ( __( 'List Limits', 'dbem' ), 'dbem_categories_default_limit', sprintf(__( "This will control how many %s are shown on one list by default.", 'dbem' ),__('categories','dbem')) );
	            	echo $save_button;
					?>
	            	</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->
				<?php endif; ?>	
				
				<?php if( get_option('dbem_tags_enabled') ): //disabled for now, will add tag stuff later ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo __('Event Tags','dbem'); ?></span></h3>
				<div class="inside">
		            	<table class="form-table">
						<?php
						em_options_radio_binary ( __( 'Override with Formats?', 'dbem' ), 'dbem_cp_tags_formats', sprintf($format_override_tip,__('tags','dbem')));
						?>
						<tr valign="top">
					   		<th scope="row"><?php _e('Default archive ordering','dbem'); ?></th>
					   		<td>   
								<select name="dbem_tags_default_archive_orderby" >
									<?php foreach($event_archive_orderby_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_tags_default_archive_orderby')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select> 
								<select name="dbem_tags_default_archive_order" >
									<?php foreach( $event_archive_order_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_tags_default_archive_order')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select>
							</td>
					   	</tr>
				   		<?php echo $save_button; ?>
		            	</table>					    
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->
				<?php endif; ?>
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__('%s Pages','dbem'),__('Other','dbem')); ?></span></h3>
				<div class="inside">
					<p><?php _e('These pages allow you to provide an event management interface outside the admin area on whatever page you want on your website. Bear in mind that this is overriden by BuddyPress if activated.'); ?></p>
	            	<table class="form-table">
					<?php
					$other_pages_tip = 'Using the %s shortcode, you can allow users to manage %s outside the admin area.';
					$events_page_options[0] = '['.__('None', 'dbem').']';
					?><tr><td colspan="2"><h4><?php echo _e('My Bookings','dbem'); ?></h4></td></tr><?php
	            	em_options_select ( sprintf(__( '%s page', 'dbem' ),__('My bookings','dbem')), 'dbem_my_bookings_page', $events_page_options, sprintf(__('Users can view their bookings for other events on this page.','dbem' ),'<code>[my_bookings]</code>',__('bookings','dbem')) );
					?>	
					<tr valign="top" id='dbem_bookings_default_orderby_row'>
				   		<th scope="row"><?php _e('Default list ordering','dbem'); ?></th>
				   		<td>   
							<select name="dbem_bookings_default_orderby" >
								<?php 
									$orderby_options = apply_filters('em_settings_bookings_default_orderby_ddm', array(
										'event_name' => sprintf(__('Order by %s','dbem'),__('Event Name','dbem')),
										'event_start_date' => sprintf(__('Order by %s','dbem'),__('Start Date','dbem')),
										'booking_date' => sprintf(__('Order by %s','dbem'),__('Booking Date','dbem'))
									)); 
								?>
								<?php foreach($orderby_options as $key => $value) : ?>
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_bookings_default_orderby')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select> 
							<select name="dbem_bookings_default_order" >
								<?php 
								$ascending = __('Ascending','dbem');
								$descending = __('Descending','dbem');
								$order_options = apply_filters('em_settings_bookings_default_order_ddm', array(
									'ASC' => __('Ascending','dbem'),
									'DESC' => __('Descending','dbem')
								));
								?>
								<?php foreach( $order_options as $key => $value) : ?>   
				 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_bookings_default_order')) ? "selected='selected'" : ''; ?>>
				 					<?php echo $value; ?>
				 				</option>
								<?php endforeach; ?>
							</select>
						</td>
				   	</tr>
					<tr><td colspan="2"><h4><?php echo _e('Front-end management pages','dbem'); ?></h4></td></tr>
					<tr><td colspan="2"><?php echo _e('Users can create and edit events and locations, as well as managing bookings for their events.','dbem'); ?></td></tr>
					<?php
	            	em_options_select ( sprintf(__( '%s page', 'dbem' ),__('Edit events','dbem')), 'dbem_edit_events_page', $events_page_options, sprintf(__('Users can view, add and edit their %s on this page.','dbem'),__('events','dbem')) );
	            	em_options_select ( sprintf(__( '%s page', 'dbem' ),__('Edit locations','dbem')), 'dbem_edit_locations_page', $events_page_options, sprintf(__('Users can view, add and edit their %s on this page.','dbem'),__('locations','dbem')) );
	            	em_options_select ( sprintf(__( '%s page', 'dbem' ),__('Manage bookings','dbem')), 'dbem_edit_bookings_page', $events_page_options, __('Users can manage bookings for their events on this page.','dbem') );
					echo $save_button;
					?>
	            	</table>
				</div> <!-- . inside --> 
				</div> <!-- .postbox -->
				
				<?php do_action('em_options_page_footer_pages'); ?>
				
			</div> <!-- .em-menu-pages -->
			
			<!-- FORMAT OPTIONS -->
		  	<div class="em-menu-formats em-menu-group" style="display:none;">				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Events', 'dbem' ); ?> </span></h3>
				<div class="inside">
	            	<table class="form-table">
					 	<tr><td colspan="2">
					 		<strong><?php echo sprintf(__('%s Page','dbem'),__('Events','dbem')); ?></strong>
					 		<p><?php _e('These formats will be used on your events page. This will also be used if you do not provide specified formats in other event lists, like in shortcodes.','dbem'); ?></p>
					 	</td></tr>
						<?php
						$grouby_modes = array(0=>__('None','dbem'), 'yearly'=>__('Yearly','dbem'), 'monthly'=>__('Monthly','dbem'), 'weekly'=>__('Weekly','dbem'), 'daily'=>__('Daily','dbem'));
						em_options_select(__('Events page grouping','dbem'), 'dbem_event_list_groupby', $grouby_modes, __('If you choose a group by mode, your events page will ','dbem'));
						em_options_input_text(__('Events page grouping','dbem'), 'dbem_event_list_groupby_format', __('Choose how to format your group headings. Leave blank for defaults.','dbem').' '. sprintf(__('Date and Time formats follow the <a href="%s">WordPress time formatting conventions</a>', 'dbem'), 'http://codex.wordpress.org/Formatting_Date_and_Time'));
						em_options_textarea ( __( 'Default event list format header', 'dbem' ), 'dbem_event_list_item_format_header', __( 'This content will appear just above your code for the default event list format. Default is blank', 'dbem' ) );
					 	em_options_textarea ( __( 'Default event list format', 'dbem' ), 'dbem_event_list_item_format', __( 'The format of any events in a list.', 'dbem' ).$events_placeholder_tip );
						em_options_textarea ( __( 'Default event list format footer', 'dbem' ), 'dbem_event_list_item_format_footer', __( 'This content will appear just below your code for the default event list format. Default is blank', 'dbem' ) );
						em_options_input_text ( __( 'No events message', 'dbem' ), 'dbem_no_events_message', __( 'The message displayed when no events are available.', 'dbem' ) );
						em_options_input_text ( __( 'List events by date title', 'dbem' ), 'dbem_list_date_title', __( 'If viewing a page for events on a specific date, this is the title that would show up. To insert date values, use <a href="http://www.php.net/manual/en/function.date.php">PHP time format characters</a>  with a <code>#</code> symbol before them, i.e. <code>#m</code>, <code>#M</code>, <code>#j</code>, etc.<br/>', 'dbem' ) );
						?>
					 	<tr><td><strong><?php echo sprintf(__('Single %s Page','dbem'),__('Event','dbem')); ?></strong></td></tr>
					 	<?php
						if( EM_MS_GLOBAL ){
						 	em_options_input_text ( __( 'Single event page title format', 'dbem' ), 'dbem_event_page_title_format', __( 'The format of a single event page title.', 'dbem' ).$events_placeholder_tip );
						}
						em_options_textarea ( __( 'Default single event format', 'dbem' ), 'dbem_single_event_format', __( 'The format of a single event page.', 'dbem' ).$events_placeholder_tip );
						echo $save_button;
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->

				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Search Form', 'dbem' ); ?> </span></h3>
				<div class="inside">
	            	<table class="form-table">
					    <?php 
						em_options_input_text ( __( 'Search button text', 'dbem' ), 'dbem_serach_form_submit' );
						em_options_radio_binary ( __( 'Show text search?', 'dbem' ), 'dbem_search_form_text', '' );
						em_options_input_text ( __( 'Text search label', 'dbem' ), 'dbem_search_form_text_label', __('Appears within the input box.','dbem') );
						em_options_radio_binary ( __( 'Show date range?', 'dbem' ), 'dbem_search_form_dates', '' );
						em_options_radio_binary ( __( 'Show categories?', 'dbem' ), 'dbem_search_form_categories', '' );
						em_options_input_text ( __( 'Categories label', 'dbem' ), 'dbem_search_form_categories_label', __('Appears as the first default search option.','dbem') );
						em_options_radio_binary ( __( 'Show countries?', 'dbem' ), 'dbem_search_form_countries', '' );
						em_options_input_text ( __( 'All countries text', 'dbem' ), 'dbem_search_form_countries_label', __('Appears as the first default search option.','dbem') );
						em_options_radio_binary ( __( 'Show regions?', 'dbem' ), 'dbem_search_form_regions', '' );
						em_options_input_text ( __( 'All regions text', 'dbem' ), 'dbem_search_form_regions_label', __('Appears as the first default search option.','dbem') );
						em_options_radio_binary ( __( 'Show states?', 'dbem' ), 'dbem_search_form_states', '' );
						em_options_input_text ( __( 'All states text', 'dbem' ), 'dbem_search_form_states_label', __('Appears as the first default search option.','dbem') );
						em_options_radio_binary ( __( 'Show towns/cities?', 'dbem' ), 'dbem_search_form_towns', '' );
						em_options_input_text ( __( 'All towns/cities text', 'dbem' ), 'dbem_search_form_towns_label', __('Appears as the first default search option.','dbem') );
					    echo $save_button;
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->

				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Date/Time', 'dbem' ); ?> </span></h3>
				<div class="inside">
					<p><?php echo sprintf(__('Date and Time formats follow the <a href="%s">WordPress time formatting conventions</a>', 'dbem'), 'http://codex.wordpress.org/Formatting_Date_and_Time'); ?></p>
					<table class="form-table">
	            		<?php
						em_options_input_text ( __( 'Date Format', 'dbem' ), 'dbem_date_format', sprintf(__('For use with the %s placeholder'),'<code>#_EVENTDATES</code>') );
						em_options_input_text ( __( 'Date Picker Format', 'dbem' ), 'dbem_date_format_js', sprintf(__( 'Same as <em>Date Format</em>, but this is used for the datepickers used by Events Manager. This uses a slightly different format to the others on here, for a list of characters to use, visit the <a href="%s">jQuery formatDate reference</a>', 'dbem' ),'http://docs.jquery.com/UI/Datepicker/formatDate') );
						em_options_input_text ( __( 'Date Separator', 'dbem' ), 'dbem_dates_Separator', sprintf(__( 'For when start/end %s are present, this will seperate the two (include spaces here if necessary).', 'dbem' ), __('dates','dbem')) );
						em_options_input_text ( __( 'Time Format', 'dbem' ), 'dbem_time_format', sprintf(__('For use with the %s placeholder'),'<code>#_EVENTTIMES</code>') );
						em_options_input_text ( __( 'Time Separator', 'dbem' ), 'dbem_times_Separator', sprintf(__( 'For when start/end %s are present, this will seperate the two (include spaces here if necessary).', 'dbem' ), __('times','dbem')) );
						em_options_input_text ( __( 'All Day Message', 'dbem' ), 'dbem_event_all_day_message', sprintf(__( 'If an event lasts all day, this text will show if using the %s placeholder', 'dbem' ), '<code>#_EVENTTIMES</code>') );
						em_options_radio_binary ( __( 'Use 24h Format?', 'dbem' ), 'dbem_time_24h', __( 'When creating events, would you like your times to be shown in 24 hour format?', 'dbem' ) );
						echo $save_button;
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				      
	           	<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Calendar', 'dbem' ); ?></span></h3>
				<div class="inside">
	            	<table class="form-table">
	            		<?php
					    em_options_radio_binary ( __( 'Link directly to event on day with single event?', 'dbem' ), 'dbem_calendar_direct_links', __( "If a calendar day has only one event, you can force a direct link to the event (recommended to avoid duplicate content).",'dbem' ) );
					    em_options_radio_binary ( __( 'Show list on day with single event?', 'dbem' ), 'dbem_display_calendar_day_single', __( "By default, if a calendar day only has one event, it display a single event when clicking on the link of that calendar date. If you select Yes here, you will get always see a list of events.",'dbem' ) );
	            		?>
	            		<tr><td colspan="2"><strong><?php _e('Small Calendar','dbem'); ?></strong></td></tr>
						<?php
					    em_options_input_text ( __( 'Event titles', 'dbem' ), 'dbem_small_calendar_event_title_format', __( 'The format of the title, corresponding to the text that appears when hovering on an eventful calendar day.', 'dbem' ).$events_placeholder_tip );
					    em_options_input_text ( __( 'Title separator', 'dbem' ), 'dbem_small_calendar_event_title_separator', __( 'The separator appearing on the above title when more than one events are taking place on the same day.', 'dbem' ) );
					    em_options_radio_binary( __( 'Abbreviated weekdays', 'dbem' ), 'dbem_small_calendar_abbreviated_weekdays', __( 'The calendar headings uses abbreviated weekdays') );
					    em_options_input_text ( __( 'Initial lengths', 'dbem' ), 'dbem_small_calendar_initials_length', __( 'Shorten the calendar headings containing the days of the week, use 0 for the full name.', 'dbem' ).$events_placeholder_tip );
					    ?>
	            		<tr><td colspan="2"><strong><?php _e('Full Calendar','dbem'); ?></strong></td></tr>
					    <?php         
					    em_options_input_text ( __( 'Event format', 'dbem' ), 'dbem_full_calendar_event_format', __( 'The format of each event when displayed in the full calendar. Remember to include <code>li</code> tags before and after the event.', 'dbem' ).$events_placeholder_tip );
					    em_options_radio_binary( __( 'Abbreviated weekdays?', 'dbem' ), 'dbem_full_calendar_abbreviated_weekdays', __( 'Use abbreviations, e.g. Friday = Fri. Useful for certain languages where abbreviations differ from full names.','dbem') );
					    em_options_input_text ( __( 'Initial lengths', 'dbem' ), 'dbem_full_calendar_initials_length', __( 'Shorten the calendar headings containing the days of the week, use 0 for the full name.', 'dbem' ).$events_placeholder_tip);
					    ?>		
					    <tr><td colspan="2"><strong><?php echo __('Calendar Day Event List Settings','dbem'); ?></strong></td></tr>			
						<tr valign="top" id='dbem_display_calendar_orderby_row'>
					   		<th scope="row"><?php _e('Default event list ordering','dbem'); ?></th>
					   		<td>   
								<select name="dbem_display_calendar_orderby" >
									<?php 
										$orderby_options = apply_filters('dbem_display_calendar_orderby_ddm', array(
											'event_name,event_start_time' => __('Order by event name, then event start time','dbem'),
											'event_start_time,event_name' => __('Order by event start time, then event name','dbem')
										)); 
									?>
									<?php foreach($orderby_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_display_calendar_orderby')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select> 
								<select name="dbem_display_calendar_order" >
									<?php 
									$ascending = __('Ascending','dbem');
									$descending = __('Descending','dbem');
									$order_options = apply_filters('dbem_display_calendar_order_ddm', array(
										'ASC' => __('All Ascending','dbem'),
										'DESC,ASC' => "$descending, $ascending",
										'DESC,DESC' => "$descending, $descending",
										'DESC' => __('All Descending','dbem')
									)); 
									?>
									<?php foreach( $order_options as $key => $value) : ?>   
					 				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_display_calendar_order')) ? "selected='selected'" : ''; ?>>
					 					<?php echo $value; ?>
					 				</option>
									<?php endforeach; ?>
								</select>
								<br/>
								<em><?php _e('When Events Manager displays lists of events the default behaviour is ordering by start date in ascending order. To change this, modify the values above.','dbem'); ?></em>
							</td>
					   	</tr>
					   	<?php 
					   		em_options_input_text ( __( 'Calendar events/day limit', 'dbem' ), 'dbem_display_calendar_events_limit', __( 'Limits the number of events on each calendar day. Leave blank for no limit.', 'dbem' ) );
					   		em_options_input_text ( __( 'More Events message', 'dbem' ), 'dbem_display_calendar_events_limit_msg', __( 'Text with link to calendar day page with all events for that day if there are more events than the limit above, leave blank for no link as the day number is also a link.', 'dbem' ) );
					   	?>
					    <tr><td><strong><?php echo sprintf(__('iCal Feed Settings','dbem'),__('Event','dbem')); ?></strong></td></tr>
					    <?php 
						em_options_input_text ( __( 'iCal Title', 'dbem' ), 'dbem_ical_description_format', __( 'The title that will appear in the calendar.', 'dbem' ).$events_placeholder_tip );
						em_options_input_text ( __( 'iCal Limit', 'dbem' ), 'dbem_ical_limit', __( 'Limits the number of future events shown (0 = unlimited).', 'dbem' ) );         
					    echo $save_button;        
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				
				<?php if( get_option('dbem_locations_enabled') ): ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Locations', 'dbem' ); ?> </span></h3>
				<div class="inside">
	            	<table class="form-table">
					 	<tr><td><strong><?php echo sprintf(__('%s Page','dbem'),__('Locations','dbem')); ?></strong></td></tr>
						<?php
						em_options_textarea ( sprintf(__('%s list header format','dbem'),__('Locations','dbem')), 'dbem_location_list_item_format_header', sprintf(__( 'This content will appear just above your code for the %s list format below. Default is blank', 'dbem' ), __('locations','dbem')) );
					 	em_options_textarea ( sprintf(__('%s list item format','dbem'),__('Locations','dbem')), 'dbem_location_list_item_format', sprintf(__( 'The format of a single %s in a list.', 'dbem' ), __('locations','dbem')).$locations_placeholder_tip );
						em_options_textarea ( sprintf(__('%s list footer format','dbem'),__('Locations','dbem')), 'dbem_location_list_item_format_footer', sprintf(__( 'This content will appear just below your code for the %s list format above. Default is blank', 'dbem' ), __('locations','dbem')) );
						em_options_input_text ( sprintf(__( 'No %s message', 'dbem' ),__('Locations','dbem')), 'dbem_no_locations_message', sprintf( __( 'The message displayed when no %s are available.', 'dbem' ), __('locations','dbem')) );
					 	?>
					 	<tr><td><strong><?php echo sprintf(__('Single %s Page','dbem'),__('Location','dbem')); ?></strong></td></tr>
					 	<?php
						em_options_input_text (sprintf( __( 'Single %s title format', 'dbem' ),__('location','dbem')), 'dbem_location_page_title_format', __( 'The format of a single location page title.', 'dbem' ).$locations_placeholder_tip );
						em_options_textarea ( sprintf(__('Single %s page format', 'dbem' ),__('location','dbem')), 'dbem_single_location_format', __( 'The format of a single location page.', 'dbem' ).$locations_placeholder_tip );
						 ?>
					 	<tr><td><strong><?php echo sprintf(__('%s List Formats','dbem'),__('Event','dbem')); ?></strong></td></tr>
					 	<?php
					 	em_options_input_text ( __( 'Default event list format header', 'dbem' ), 'dbem_location_event_list_item_header_format', __( 'This content will appear just above your code for the default event list format. Default is blank', 'dbem' ) );
					 	em_options_textarea ( sprintf(__( 'Default %s list format', 'dbem' ),__('events','dbem')), 'dbem_location_event_list_item_format', __( 'The format of the events the list inserted in the location page through the <code>#_LOCATIONNEXTEVENTS</code>, <code>#_LOCATIONNEXTEVENTS</code> and <code>#_LOCATIONALLEVENTS</code> element.', 'dbem' ).$locations_placeholder_tip );
						em_options_input_text ( __( 'Default event list format footer', 'dbem' ), 'dbem_location_event_list_item_footer_format', __( 'This content will appear just below your code for the default event list format. Default is blank', 'dbem' ) );
						em_options_textarea ( sprintf(__( 'No %s message', 'dbem' ),__('events','dbem')), 'dbem_location_no_events_message', __( 'The message to be displayed in the list generated by <code>#_LOCATIONNEXTEVENTS</code>, <code>#_LOCATIONNEXTEVENTS</code> and <code>#_LOCATIONALLEVENTS</code> when no events are available.', 'dbem' ) );
						echo $save_button;
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				<?php endif; ?>
				
				<?php if( get_option('dbem_categories_enabled') && !(EM_MS_GLOBAL && !is_main_blog()) ): ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Event Categories', 'dbem' ); ?> </span></h3>
				<div class="inside">
	            	<table class="form-table">
					 	<tr><td><strong><?php echo sprintf(__('%s Page','dbem'),__('Categories','dbem')); ?></strong></td></tr>
						<?php
						em_options_textarea ( sprintf(__('%s list header format','dbem'),__('Categories','dbem')), 'dbem_categories_list_item_format_header', sprintf(__( 'This content will appear just above your code for the %s list format below. Default is blank', 'dbem' ), __('categories','dbem')) );
					 	em_options_textarea ( sprintf(__('%s list item format','dbem'),__('Categories','dbem')), 'dbem_categories_list_item_format', sprintf(__( 'The format of a single %s in a list.', 'dbem' ), __('categories','dbem')).$categories_placeholder_tip );
						em_options_textarea ( sprintf(__('%s list footer format','dbem'),__('Categories','dbem')), 'dbem_categories_list_item_format_footer', sprintf(__( 'This content will appear just below your code for the %s list format above. Default is blank', 'dbem' ), __('categories','dbem')) );
						em_options_input_text ( sprintf(__( 'No %s message', 'dbem' ),__('Categories','dbem')), 'dbem_no_categories_message', sprintf( __( 'The message displayed when no %s are available.', 'dbem' ), __('categories','dbem')) );
					 	?>
					 	<tr><td><strong><?php echo sprintf(__('Single %s Page','dbem'),__('Category','dbem')); ?></strong></td></tr>
					 	<?php
						em_options_input_text ( sprintf(__( 'Single %s title format', 'dbem' ),__('category','dbem')), 'dbem_category_page_title_format', __( 'The format of a single category page title.', 'dbem' ).$categories_placeholder_tip );
						em_options_textarea ( sprintf(__('Single %s page format', 'dbem' ),__('category','dbem')), 'dbem_category_page_format', __( 'The format of a single category page.', 'dbem' ).$categories_placeholder_tip );
					 	?>
					 	<tr><td><strong><?php echo sprintf(__('%s List Formats','dbem'),__('Event','dbem')); ?></strong></td></tr>
					 	<?php
					 	em_options_input_text ( __( 'Default event list format header', 'dbem' ), 'dbem_category_event_list_item_header_format', __( 'This content will appear just above your code for the default event list format. Default is blank', 'dbem' ) );
					 	em_options_textarea ( sprintf(__( 'Default %s list format', 'dbem' ),__('events','dbem')), 'dbem_category_event_list_item_format', __( 'The format of the events the list inserted in the category page through the <code>#_CATEGORYNEXTEVENTS</code>, <code>#_CATEGORYNEXTEVENTS</code> and <code>#_CATEGORYALLEVENTS</code> element.', 'dbem' ).$categories_placeholder_tip );
						em_options_input_text ( __( 'Default event list format footer', 'dbem' ), 'dbem_category_event_list_item_footer_format', __( 'This content will appear just below your code for the default event list format. Default is blank', 'dbem' ) );
						em_options_textarea ( sprintf(__( 'No %s message', 'dbem' ),__('events','dbem')), 'dbem_category_no_events_message', __( 'The message to be displayed in the list generated by <code>#_CATEGORYNEXTEVENTS</code>, <code>#_CATEGORYNEXTEVENTS</code> and <code>#_CATEGORYALLEVENTS</code> when no events are available.', 'dbem' ) );
						echo $save_button;
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				<?php endif; ?>
				
				<?php if( get_option('dbem_tags_enabled') ): ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Event Tags', 'dbem' ); ?> </span></h3>
				<div class="inside">
	            	<table class="form-table">
					 	<tr><td><strong><?php echo sprintf(__('Single %s Page','dbem'),__('Tag','dbem')); ?></strong></td></tr>
					 	<?php
						em_options_input_text ( sprintf(__( 'Single %s title format', 'dbem' ),__('tag','dbem')), 'dbem_tag_page_title_format', __( 'The format of a single tag page title.', 'dbem' ).$categories_placeholder_tip );
						em_options_textarea ( sprintf(__('Single %s page format', 'dbem' ),__('tag','dbem')), 'dbem_tag_page_format', __( 'The format of a single tag page.', 'dbem' ).$categories_placeholder_tip );
					 	?>
					 	<tr><td><strong><?php echo sprintf(__('%s List Formats','dbem'),__('Event','dbem')); ?></strong></td></tr>
					 	<?php
						em_options_input_text ( __( 'Default event list format header', 'dbem' ), 'dbem_tag_event_list_item_header_format', __( 'This content will appear just above your code for the default event list format. Default is blank', 'dbem' ) );
					 	em_options_textarea ( sprintf(__( 'Default %s list format', 'dbem' ),__('events','dbem')), 'dbem_tag_event_list_item_format', __( 'The format of the events the list inserted in the tag page through the <code>#_TAGNEXTEVENTS</code>, <code>#_TAGNEXTEVENTS</code> and <code>#_TAGALLEVENTS</code> element.', 'dbem' ).$categories_placeholder_tip );
						em_options_input_text ( __( 'Default event list format footer', 'dbem' ), 'dbem_tag_event_list_item_footer_format', __( 'This content will appear just below your code for the default event list format. Default is blank', 'dbem' ) );
						em_options_textarea ( sprintf(__( 'No %s message', 'dbem' ),__('events','dbem')), 'dbem_tag_no_events_message', __( 'The message to be displayed in the list generated by <code>#_TAGNEXTEVENTS</code>, <code>#_TAGNEXTEVENTS</code> and <code>#_TAGALLEVENTS</code> when no events are available.', 'dbem' ) );
						echo $save_button;
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				<?php endif; ?>
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'RSS', 'dbem' ); ?> </span></h3>
				<div class="inside">
	            	<table class="form-table">
						<?php				
						em_options_input_text ( __( 'RSS main title', 'dbem' ), 'dbem_rss_main_title', __( 'The main title of your RSS events feed.', 'dbem' ).$events_placeholder_tip );
						em_options_input_text ( __( 'RSS main description', 'dbem' ), 'dbem_rss_main_description', __( 'The main description of your RSS events feed.', 'dbem' ) );
						em_options_input_text ( __( 'RSS title format', 'dbem' ), 'dbem_rss_title_format', __( 'The format of the title of each item in the events RSS feed.', 'dbem' ).$events_placeholder_tip );
						em_options_input_text ( __( 'RSS description format', 'dbem' ), 'dbem_rss_description_format', __( 'The format of the description of each item in the events RSS feed.', 'dbem' ).$events_placeholder_tip );
						echo $save_button;
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Maps', 'dbem' ); ?> </span></h3>
				<div class="inside">
					<p><?php echo sprintf(__('You can use Google Maps to show where your events are located. For more information on using maps, <a href="%s">see our documentation</a>.','dbem'),'http://wp-events-plugin.com/documentation/google-maps/'); ?>
					<table class='form-table'> 
						<?php $gmap_is_active = get_option ( 'dbem_gmap_is_active' ); ?>
						<tr valign="top">
							<th scope="row"><?php _e ( 'Enable Google Maps integration?', 'dbem' ); ?></th>
							<td>
								<?php _e ( 'Yes' ); ?> <input id="dbem_gmap_is_active_yes" name="dbem_gmap_is_active" type="radio" value="1" <?php echo ($gmap_is_active) ? "checked='checked'":''; ?> />
								<?php _e ( 'No' ); ?> <input name="dbem_gmap_is_active" type="radio" value="0" <?php echo ($gmap_is_active) ? '':"checked='checked'"; ?> /><br />
								<em><?php _e ( 'Check this option to enable Goggle Map integration.', 'dbem' )?></em>
							</td>
						</tr>
						<tr><td colspan="2">
							<strong><?php _e('Global Map Format','dbem'); ?></strong>
							<p><?php echo sprintf(__('If you use the %s <a href="%s">shortcode</a>, you can display a map of all your locations and events, the settings below will be used.','dbem'), '<code>[locations_map]</code>','http://wp-events-plugin.com/documentation/shortcodes/'); ?></p>
						</td></tr>
						<?php
						em_options_textarea ( __( 'Location balloon format', 'dbem' ), 'dbem_map_text_format', __( 'The format of of the text appearing in the balloon describing the location and the first upcoming event.', 'dbem' ).' '.__( 'Event.', 'dbem' ).$events_placeholder_tip );
						?>
						<tr><td colspan="2">
							<strong><?php _e('Single Location/Event Map Format','dbem'); ?></strong>
							<p><?php echo sprintf(_e('If you use the <code>#_LOCATIONMAP</code> <a href="%s">placeholder</a> when displaying individual event and location information, the settings below will be used.','dbem'), '<code>[locations_map]</code>','http://wp-events-plugin.com/documentation/placeholders/'); ?></p>
						</td></tr>
						<?php
						em_options_textarea ( __( 'Location balloon format', 'dbem' ), 'dbem_location_baloon_format', __( 'The format of of the text appearing in the balloon describing the location.', 'dbem' ).$locations_placeholder_tip );
						echo $save_button;     
						?> 
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				
				<?php do_action('em_options_page_footer_formats'); ?>
				
			</div> <!-- .em-menu-formats -->
			
			<?php if( get_option('dbem_rsvp_enabled') ): ?>
			<!-- BOOKING OPTIONS -->
		  	<div class="em-menu-bookings em-menu-group" style="display:none;">	
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__( '%s Options', 'dbem' ),__('General','dbem')); ?> </span></h3>
				<div class="inside">
					<table class='form-table'> 
						<?php 
						em_options_radio_binary ( __( 'Allow guest bookings?', 'dbem' ), 'dbem_bookings_anonymous', __( 'If enabled, guest visitors can supply an email address and a user account will automatically be created for them along with their booking. They will be also be able to log back in with that newly created account.', 'dbem' ) );
						em_options_radio_binary ( __( 'Approval Required?', 'dbem' ), 'dbem_bookings_approval', __( 'Bookings will not be confirmed until the event administrator approves it.', 'dbem' ) );
						em_options_radio_binary ( __( 'Reserved unconfirmed spaces?', 'dbem' ), 'dbem_bookings_approval_reserved', __( 'By default, event spaces become unavailable once there are enough CONFIRMED bookings. To reserve spaces even if unnapproved, choose yes.', 'dbem' ) );
						em_options_radio_binary ( __( 'Can users cancel their booking?', 'dbem' ), 'dbem_bookings_user_cancellation', __( 'If enabled, users can cancel their bookings themselves from their bookings page.', 'dbem' ) );
						em_options_radio_binary ( __( 'Allow overbooking when approving?', 'dbem' ), 'dbem_bookings_approval_overbooking', __( 'If you get a lot of pending bookings and you decide to allow more bookings than spaces allow, setting this to yes will allow you to override the event space limit when manually approving.', 'dbem' ) );
						em_options_radio_binary ( __( 'Allow double bookings?', 'dbem' ), 'dbem_bookings_double', __( 'If enabled, users can book an event more than once.', 'dbem' ) );
						echo $save_button; 
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__( '%s Options', 'dbem' ),__('Pricing','dbem')); ?> </span></h3>
				<div class="inside">
					<table class='form-table'>
						<?php
						/* Tax & Currency */
						em_options_select ( __( 'Currency', 'dbem' ), 'dbem_bookings_currency', em_get_currencies()->names, __( 'Choose your currency for displaying event pricing.', 'dbem' ) );
						em_options_input_text ( __( 'Thousands Separator', 'dbem' ), 'dbem_bookings_currency_thousands_sep', '<code>'.get_option('dbem_bookings_currency_thousands_sep')." = ".em_get_currency_symbol().'100<strong>'.get_option('dbem_bookings_currency_thousands_sep').'</strong>000<strong>'.get_option('dbem_bookings_currency_decimal_point').'</strong>00</code>' );
						em_options_input_text ( __( 'Decimal Point', 'dbem' ), 'dbem_bookings_currency_decimal_point', '<code>'.get_option('dbem_bookings_currency_decimal_point')." = ".em_get_currency_symbol().'100<strong>'.get_option('dbem_bookings_currency_decimal_point').'</strong>00</code>' );
						em_options_input_text ( __( 'Currency Format', 'dbem' ), 'dbem_bookings_currency_format', __('Choose how prices are displayed. <code>@</code> will be replaced by the currency symbol, and <code>#</code> will be replaced by the number.','dbem').' <code>'.get_option('dbem_bookings_currency_format')." = ".em_get_currency_formatted('10000000').'</code>');
						em_options_input_text ( __( 'Tax Rate', 'dbem' ), 'dbem_bookings_tax', __( 'Add a tax rate to your ticket prices (entering 10 will add 10% to the ticket price).', 'dbem' ) );
						em_options_radio_binary ( __( 'Add tax to ticket price?', 'dbem' ), 'dbem_bookings_tax_auto_add', __( 'When displaying ticket prices and booking totals, include the tax automatically?', 'dbem' ) );
						echo $save_button; 
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox --> 
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e( 'Customize Feedback Messages', 'dbem' ); ?> </span></h3>
				<div class="inside">
					<p><?php _e('Below you will find texts that will be displayed to users in various areas during the bookings process, particularly on booking forms.','dbem'); ?></p>
					<table class='form-table'>
						<tr><td colspan='2'><h4><?php _e('My Bookings messages','dbem') ?></h4></td></tr>
						<?php 
						em_options_input_text ( __( 'Booking Cancelled', 'dbem' ), 'dbem_booking_feedback_cancelled', __( 'When a user cancels their booking, this message will be displayed confirming the cancellation.', 'dbem' ) );
						em_options_input_text ( __( 'Booking Cancellation Warning', 'dbem' ), 'dbem_booking_warning_cancel', __( 'When a user chooses to cancel a booking, this warning is displayed for them to confirm.', 'dbem' ) );
						?>
						<tr><td colspan='2'><h4><?php _e('Booking form texts/messages','dbem') ?></h4></td></tr>
						<?php
						em_options_input_text ( __( 'Bookings disabled', 'dbem' ), 'dbem_bookings_form_msg_disabled', __( 'An event with no bookings.', 'dbem' ) );
						em_options_input_text ( __( 'Bookings closed', 'dbem' ), 'dbem_bookings_form_msg_closed', __( 'Bookings have closed (e.g. event has started).', 'dbem' ) );
						em_options_input_text ( __( 'Fully booked', 'dbem' ), 'dbem_bookings_form_msg_full', __( 'Event is fully booked.', 'dbem' ) );
						em_options_input_text ( __( 'Already attending', 'dbem' ), 'dbem_bookings_form_msg_attending', __( 'If already attending and double bookings are disabled, this message will be displayed, followed by a link to the users booking page.', 'dbem' ) );
						em_options_input_text ( __( 'Manage bookings link text', 'dbem' ), 'dbem_bookings_form_msg_bookings_link', __( 'Link text used for link to user bookings.', 'dbem' ) );
						?>
						<tr><td colspan='2'><h4><?php _e('Booking form feedback messages','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php _e('When a booking is made by a user, a feedback message is shown depending on the result, which can be customized below.','dbem'); ?></td></tr>
						<?php
						em_options_input_text ( __( 'Successful booking', 'dbem' ), 'dbem_booking_feedback', __( 'When a booking is registered and confirmed.', 'dbem' ) );
						em_options_input_text ( __( 'Successful pending booking', 'dbem' ), 'dbem_booking_feedback_pending', __( 'When a booking is registered but pending.', 'dbem' ) );
						em_options_input_text ( __( 'Not enough spaces', 'dbem' ), 'dbem_booking_feedback_full', __( 'When a booking cannot be made due to lack of spaces.', 'dbem' ) );
						em_options_input_text ( __( 'Errors', 'dbem' ), 'dbem_booking_feedback_error', __( 'When a booking cannot be made due to an error when filling the form. Below this, there will be a dynamic list of errors.', 'dbem' ) );
						em_options_input_text ( __( 'Email Exists', 'dbem' ), 'dbem_booking_feedback_email_exists', __( 'When a guest tries to book using an email registered with a user account.', 'dbem' ) );
						em_options_input_text ( __( 'User must log in', 'dbem' ), 'dbem_booking_feedback_log_in', __( 'When a user must log in before making a booking.', 'dbem' ) );
						em_options_input_text ( __( 'Error mailing user', 'dbem' ), 'dbem_booking_feedback_nomail', __( 'If a booking is made and an email cannot be sent, this is added to the success message.', 'dbem' ) );
						em_options_input_text ( __( 'Already booked', 'dbem' ), 'dbem_booking_feedback_already_booked', __( 'If the user made a previous booking and cannot double-book.', 'dbem' ) );
						em_options_input_text ( __( 'No spaces booked', 'dbem' ), 'dbem_booking_feedback_min_space', __( 'If the user tries to make a booking without requesting any spaces.', 'dbem' ) );$notice_full = __('Sold Out', 'dbem');
						?>
						<tr><td colspan='2'><h4><?php _e('Booking button feedback messages','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php echo sprintf(__('When the %s placeholder, the below texts will be used.','dbem'),'<code>#_BOOKINGBUTTON</code>'); ?></td></tr>
						<?php			
						em_options_input_text ( __( 'User can book', 'dbem' ), 'dbem_booking_button_msg_book', '');
						em_options_input_text ( __( 'Booking in progress', 'dbem' ), 'dbem_booking_button_msg_booking', '');
						em_options_input_text ( __( 'Booking complete', 'dbem' ), 'dbem_booking_button_msg_booked', '');
						em_options_input_text ( __( 'Booking error', 'dbem' ), 'dbem_booking_button_msg_error', '');
						em_options_input_text ( __( 'Event fully booked', 'dbem' ), 'dbem_booking_button_msg_full', '');
						em_options_input_text ( __( 'Cancel', 'dbem' ), 'dbem_booking_button_msg_cancel', '');
						em_options_input_text ( __( 'Cancelation in progress', 'dbem' ), 'dbem_booking_button_msg_canceling', '');
						em_options_input_text ( __( 'Cancelation complete', 'dbem' ), 'dbem_booking_button_msg_cancelled', '');
						em_options_input_text ( __( 'Cancelation error', 'dbem' ), 'dbem_booking_button_msg_cancel_error', '');
						
						echo $save_button; 
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox --> 
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__( '%s Options', 'dbem' ),__('Booking Form','dbem')); ?> </span></h3>
				<div class="inside">
					<table class='form-table'>
						<?php
						em_options_radio_binary ( __( 'Display login form?', 'dbem' ), 'dbem_bookings_login_form', __( 'Choose whether or not to display a login form in the booking form area to remind your members to log in before booking.', 'dbem' ) );
						em_options_input_text ( __( 'Submit button text', 'dbem' ), 'dbem_bookings_submit_button', sprintf(__( 'The text used by the submit button. To use an image instead, enter the full url starting with %s or %s.', 'dbem' ), '<code>http://</code>','<code>https://</code>') );
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox --> 
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php echo sprintf(__( '%s Options', 'dbem' ),__('Ticket','dbem')); ?> </span></h3>
				<div class="inside">
					<table class='form-table'>
						<?php
						em_options_radio_binary ( __( 'Single ticket mode?', 'dbem' ), 'dbem_bookings_tickets_single', __( 'In single ticket mode, users can only create one ticket per booking (and will not see options to add more tickets).', 'dbem' ) );
						em_options_radio_binary ( __( 'Show ticket table in single ticket mode?', 'dbem' ), 'dbem_bookings_tickets_single_form', __( 'If you prefer a ticket table like with multiple tickets, even for single ticket events, enable this.', 'dbem' ) );
						em_options_radio_binary ( __( 'Show unavailable tickets?', 'dbem' ), 'dbem_bookings_tickets_show_unavailable', __( 'You can choose whether or not to show unavailable tickets to visitors.', 'dbem' ) );
						em_options_radio_binary ( __( 'Show multiple tickets if logged out?', 'dbem' ), 'dbem_bookings_tickets_show_loggedout', __( 'If logged out, a user will be asked to register in order to book. However, we can show available tickets if you have more than one ticket.', 'dbem' ) );
						$ticket_orders = array(
							'ticket_price DESC, ticket_name ASC'=>__('Ticket Price (Descending)','dbem'),
							'ticket_price ASC, ticket_name ASC'=>__('Ticket Price (Ascending)','dbem'),
							'ticket_name ASC, ticket_price DESC'=>__('Ticket Name (Ascending)','dbem'),
							'ticket_name DESC, ticket_price DESC'=>__('Ticket Name (Descending)','dbem')
						);
						em_options_select ( __( 'Order Tickets By', 'dbem' ), 'dbem_bookings_tickets_orderby', $ticket_orders, __( 'Choose which order your tickets appear.', 'dbem' ) );
						echo $save_button; 
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox --> 
					
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e('No-User Booking Mode','dbem'); ?> </span></h3>
				<div class="inside">
					<table class='form-table'>
						<tr><td colspan='2'>
							<p><?php _e('By default, when a booking is made by a user, this booking is tied to a user account, if the user is not registered nor logged in and guest bookings are enabled, an account will be created for them.','dbem'); ?></p>
							<p><?php _e('The option below allows you to disable user accounts and assign all bookings to a parent user, yet you will still see the supplied booking personal information for each booking. When this mode is enabled, extra booking information about the person is stored alongside the booking record rather than as a WordPress user.','dbem'); ?></p>
							<p><?php _e('<strong>Warning : </strong> Various features afforded to users with an account will not be available, e.g. viewing bookings. Once you enable this and select a user, modifying these values will prevent older non-user bookings from displaying the correct information.','dbem'); ?></p>
						</td></tr>
						<?php
						em_options_radio_binary ( __( 'Enable No-User Booking Mode?', 'dbem' ), 'dbem_bookings_registration_disable', __( 'This disables user registrations for bookings.', 'dbem' ) );
						$current_user = array();
						if( get_option('dbem_bookings_registration_user') ){
							$user = get_user_by('id',get_option('dbem_bookings_registration_user'));
							$current_user[$user->ID] = $user->display_name;
						}
						em_options_select ( __( 'Assign bookings to', 'dbem' ), 'dbem_bookings_registration_user', em_get_wp_users(array('role' => 'subscriber'), $current_user), __( 'Choose a parent user to assign bookings to. People making their booking will be unaware of this and will never have access to those user details. This should be a subscriber user you do not use to log in with yourself.', 'dbem' ) );
						echo $save_button; 
						?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				
				<?php do_action('em_options_page_footer_bookings'); ?>
				
			</div> <!-- .em-menu-bookings -->
			<?php endif; ?>
			
			<!-- EMAIL OPTIONS -->
		  	<div class="em-menu-emails em-menu-group" style="display:none;">
				
				<?php if ( !is_multisite() ) { em_admin_option_box_email(); } ?>
		  	
		  		<?php if( get_option('dbem_rsvp_enabled') ): ?>
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Booking Email Templates', 'dbem' ); ?> </span></h3>
				<div class="inside">
					<table class='form-table'>
						<?php
						$email_subject_tip = __('You can disable this email by leaving the subject blank.','dbem');
						em_options_select ( __( 'Default contact person', 'dbem' ), 'dbem_default_contact_person', em_get_wp_users(), __( 'Select the default contact person. This user will be employed whenever a contact person is not explicitly specified for an event', 'dbem' ) );
						em_options_input_text ( __( 'Email events admin?', 'dbem' ), 'dbem_bookings_notify_admin', __( "If you would like every event booking confirmation email sent to an administrator write their email here (leave blank to not send an email).", 'dbem' ).' '.__('For multiple emails, seperate by commas (e.g. email1@test.com,email2@test.com,etc.)','dbem') );
						em_options_radio_binary ( __( 'Email contact person?', 'dbem' ), 'dbem_bookings_contact_email', __( 'Check this option if you want the event contact to receive an email when someone books places. An email will be sent when a booking is first made (regardless if confirmed or pending)', 'dbem' ) );
						em_options_radio_binary ( __( 'Disable new registration email?', 'dbem' ), 'dbem_email_disable_registration', __( 'Check this option if you want to prevent the WordPress registration email from going out when a user anonymously books an event.', 'dbem' ) );
						?>
						<tr><td colspan='2'><h4><?php _e('Contact person booking confirmed','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php echo __('An email will be sent to the event contact when a booking is first made.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Contact person email subject', 'dbem' ), 'dbem_bookings_contact_email_subject', $email_subject_tip );
						em_options_textarea ( __( 'Contact person email', 'dbem' ), 'dbem_bookings_contact_email_body', '' );
						?>
						<tr><td colspan='2'><h4><?php _e('Contact person booking cancelled','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php echo __('An email will be sent to the event contact if someone cancels their booking.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Contact person cancellation subject', 'dbem' ), 'dbem_contactperson_email_cancelled_subject', $email_subject_tip );
						em_options_textarea ( __( 'Contact person cancellation email', 'dbem' ), 'dbem_contactperson_email_cancelled_body', '' );
						?>
						<tr><td colspan='2'><h4><?php _e('Confirmed booking email','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php echo __('This is sent when a person\'s booking is confirmed. This will be sent automatically if approvals are required and the booking is approved. If approvals are disabled, this is sent out when a user first submits their booking.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Booking confirmed email subject', 'dbem' ), 'dbem_bookings_email_confirmed_subject', $email_subject_tip );
						em_options_textarea ( __( 'Booking confirmed email', 'dbem' ), 'dbem_bookings_email_confirmed_body', '' );
						?>
						<tr><td colspan='2'><h4><?php _e('Pending booking email','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php echo __( 'This will be sent to the person when they first submit their booking. Not relevant if bookings don\'t require approval.', 'dbem' ).$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Booking pending email subject', 'dbem' ), 'dbem_bookings_email_pending_subject', $email_subject_tip);
						em_options_textarea ( __( 'Booking pending email', 'dbem' ), 'dbem_bookings_email_pending_body','') ;
						?>
						<tr><td colspan='2'><h4><?php _e('Rejected booking email','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php echo __( 'This will be sent automatically when a booking is rejected. Not relevant if bookings don\'t require approval.', 'dbem' ).$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Booking rejected email subject', 'dbem' ), 'dbem_bookings_email_rejected_subject', $email_subject_tip );
						em_options_textarea ( __( 'Booking rejected email', 'dbem' ), 'dbem_bookings_email_rejected_body', '' );
						?>
						<tr><td colspan='2'><h4><?php _e('Booking cancelled','dbem') ?></h4></td></tr>
						<tr><td colspan='2'><?php echo __('This will be sent when a user cancels their booking.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Booking cancelled email subject', 'dbem' ), 'dbem_bookings_email_cancelled_subject', $email_subject_tip );
						em_options_textarea ( __( 'Booking cancelled email', 'dbem' ), 'dbem_bookings_email_cancelled_body', '' );
						?>
						<?php echo $save_button; ?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				<?php endif; ?>
				
				<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Event Submission Templates', 'dbem' ); ?> </span></h3>
				<div class="inside">
					<table class='form-table'>
						<?php 
						em_options_input_text ( __( 'Administrator Email', 'dbem' ), 'dbem_event_submitted_email_admin', __('Event submission notifications will be sent to emails added here.','dbem').' '.__('If left blank, no emails will be sent. Seperate emails with commas for more than one email.','dbem') );
						?>
						<tr><td colspan='2'><strong><?php _e('Event Submitted','dbem') ?></strong></td></tr>
						<tr><td colspan='2'><?php echo __('An email will be sent to your administrator emails when an event is submitted and pending approval.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Event submitted subject', 'dbem' ), 'dbem_event_submitted_email_subject', __('If left blank, this email will not be sent.','dbem') );
						em_options_textarea ( __( 'Event submitted email', 'dbem' ), 'dbem_event_submitted_email_body', '' );
						?>
						<tr><td colspan='2'><strong><?php _e('Event Re-Submitted','dbem') ?></strong></td></tr>
						<tr><td colspan='2'><?php echo __('When a user modifies a previously published event, it will be put back into pending review status and will not be publisehd until you re-approve it.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Event resubmitted subject', 'dbem' ), 'dbem_event_resubmitted_email_subject', __('If left blank, this email will not be sent.','dbem') );
						em_options_textarea ( __( 'Event resubmitted email', 'dbem' ), 'dbem_event_resubmitted_email_body', '' );
						?>
						<tr><td colspan='2'><strong><?php _e('Event Published','dbem') ?></strong></td></tr>
						<tr><td colspan='2'><?php echo __('An email will be sent to an administrator of your choice when an event is published by users who are not administrators.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Event published subject', 'dbem' ), 'dbem_event_published_email_subject', __('If left blank, this email will not be sent.','dbem') );
						em_options_textarea ( __( 'Event published email', 'dbem' ), 'dbem_event_published_email_body', '' );
						?>
						<tr><td colspan='2'><strong><?php _e('Event Approved','dbem') ?></strong></td></tr>
						<tr><td colspan='2'><?php echo __('An email will be sent to the event owner when their event is approved. Users requiring event approval do not have the <code>publish_events</code> capability.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Event approved subject', 'dbem' ), 'dbem_event_approved_email_subject', __('If left blank, this email will not be sent.','dbem') );
						em_options_textarea ( __( 'Event approved email', 'dbem' ), 'dbem_event_approved_email_body', '' );
						?>
						<tr><td colspan='2'><?php echo __('When a user modifies a previously published event, it will be put back into pending review status and will not be publisehd until you re-approve it.','dbem').$bookings_placeholder_tip ?></td></tr>
						<?php
						em_options_input_text ( __( 'Event reapproved subject', 'dbem' ), 'dbem_event_reapproved_email_subject', __('If left blank, this email will not be sent.','dbem') );
						em_options_textarea ( __( 'Event reapproved email', 'dbem' ), 'dbem_event_reapproved_email_body', '' );						
						?>
						<?php echo $save_button; ?>
					</table>
				</div> <!-- . inside -->
				</div> <!-- .postbox -->
				
				<?php do_action('em_options_page_footer_emails'); ?>
				
			</div><!-- .em-group-emails --> 
			<?php /*
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Debug Modes', 'dbem' ); ?> </span></h3>
			<div class="inside">
				<table class='form-table'>
					<?php
					em_options_radio_binary ( __( 'EM Debug Mode?', 'dbem' ), 'dbem_debug', __( 'Setting this to yes will display different content to admins for event pages and emails so you can see all the available placeholders and their values.', 'dbem' ) );
					em_options_radio_binary ( __( 'WP Debug Mode?', 'dbem' ), 'dbem_wp_debug', __( 'This will turn WP_DEBUG mode on. Useful if you want to troubleshoot php errors without looking at your logs.', 'dbem' ) );
					?>
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			*/ ?>

			<p class="submit">
				<input type="submit" id="dbem_options_submit" name="Submit" value="<?php _e ( 'Save Changes' )?>" />
				<input type="hidden" name="em-submitted" value="1" />
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('events-manager-options'); ?>" />
			</p>  
			
			</div> <!-- .metabox-sortables -->
			</div> <!-- .postbox-container -->
			
			</div> <!-- .metabox-holder -->	
		</form>
	</div>
	<?php
}

/**
 * Meta options box for image sizes. Shared in both MS and Normal options page, hence it's own function 
 */
function em_admin_option_box_image_sizes(){
	global $save_button;
	?>
	<div  class="postbox " >
	<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Image Sizes', 'dbem' ); ?> </span></h3>
	<div class="inside">
		<table class='form-table'>
			<?php
			em_options_input_text ( __( 'Maximum width (px)', 'dbem' ), 'dbem_image_max_width', __( 'The maximum allowed width for images uploades', 'dbem' ) );
			em_options_input_text ( __( 'Minimum width (px)', 'dbem' ), 'dbem_image_min_width', __( 'The minimum allowed width for images uploades', 'dbem' ) );
			em_options_input_text ( __( 'Maximum height (px)', 'dbem' ), 'dbem_image_max_height', __( "The maximum allowed height for images uploaded, in pixels", 'dbem' ) );
			em_options_input_text ( __( 'Minimum height (px)', 'dbem' ), 'dbem_image_min_height', __( "The minimum allowed height for images uploaded, in pixels", 'dbem' ) );
			em_options_input_text ( __( 'Maximum size (bytes)', 'dbem' ), 'dbem_image_max_size', __( "The maximum allowed size for images uploaded, in bytes", 'dbem' ) );
			echo $save_button;
			?>
		</table>
	</div> <!-- . inside -->
	</div> <!-- .postbox -->
	<?php	
}

/**
 * Meta options box for email settings. Shared in both MS and Normal options page, hence it's own function 
 */
function em_admin_option_box_email(){
	global $save_button;
	?>
	<div  class="postbox " >
	<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Email Settings', 'dbem' ); ?></span></h3>
	<div class="inside em-email-form">
		<p class="em-email-settings-check">
			<?php _e('Before you save your changes, you can quickly send yourself a test email by clicking this button.'); ?>
			<input type="button" id="em-admin-check-email" class="secondary-button" value="<?php _e('Test Email Settings','dbem'); ?>" />
			<input type="hidden" name="_check_email_nonce" value="<?php echo wp_create_nonce('check_email'); ?>" />
			<span id="em-email-settings-check-status"></span>
		</p>
		<table class="form-table">
			<?php
			em_options_input_text ( __( 'Notification sender name', 'dbem' ), 'dbem_mail_sender_name', __( "Insert the display name of the notification sender.", 'dbem' ) );
			em_options_input_text ( __( 'Notification sender address', 'dbem' ), 'dbem_mail_sender_address', __( "Insert the address of the notification sender.", 'dbem' ) );
			em_options_select ( __( 'Mail sending method', 'dbem' ), 'dbem_rsvp_mail_send_method', array ('smtp' => 'SMTP', 'mail' => __( 'PHP mail function', 'dbem' ), 'sendmail' => 'Sendmail', 'qmail' => 'Qmail', 'wp_mail' => 'WP Mail' ), __( 'Select the method to send email notification.', 'dbem' ) );
			em_options_radio_binary ( __( 'Send HTML Emails?', 'dbem' ), 'dbem_smtp_html', __( 'If set to yes, your emails will be sent in HTML format, otherwise plaintext.', 'dbem' ).' '.__( 'Depending on server settings, some sending methods may ignore this settings.', 'dbem' ) );
			?>
			<tbody class="em-email-settings-smtp">
				<?php
				em_options_input_text ( 'Mail sending port', 'dbem_rsvp_mail_port', __( "The port through which you e-mail notifications will be sent. Make sure the firewall doesn't block this port", 'dbem' ) );
				em_options_radio_binary ( __( 'Use SMTP authentication?', 'dbem' ), 'dbem_rsvp_mail_SMTPAuth', __( 'SMTP authentication is often needed. If you use GMail, make sure to set this parameter to Yes', 'dbem' ) );
				em_options_input_text ( 'SMTP host', 'dbem_smtp_host', __( "The SMTP host. Usually it corresponds to 'localhost'. If you use GMail, set this value to 'ssl://smtp.gmail.com:465'.", 'dbem' ) );
				em_options_input_text ( __( 'SMTP username', 'dbem' ), 'dbem_smtp_username', __( "Insert the username to be used to access your SMTP server.", 'dbem' ) );
				em_options_input_password ( __( 'SMTP password', 'dbem' ), "dbem_smtp_password", __( "Insert the password to be used to access your SMTP server", 'dbem' ) );
				?>
			</tbody>
			<?php
			echo $save_button;
			?>
		</table>
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function($){
				$('#dbem_rsvp_mail_send_method_row select').change(function(){
					el = $(this);
					if( el.find(':selected').val() == 'smtp' ){
						$('.em-email-settings-smtp').show();
					}else{
						$('.em-email-settings-smtp').hide();
					}
				}).trigger('change');
				$('input#em-admin-check-email').click(function(e,el){
					var email_data = $('.em-email-form input').serialize();
					$.ajax({
						url: EM.ajaxurl,
						dataType: 'json',
						data: email_data+"&action=em_admin_test_email",
						success: function(data){
							if(data.result && data.message){
								$('#em-email-settings-check-status').css({'color':'green','display':'block'}).html(data.message);
							}else{
								var msg = (data.message) ? data.message:'Email not sent';
								$('#em-email-settings-check-status').css({'color':'red','display':'block'}).html(msg);
							}
						},
						error: function(){ $('#em-email-settings-check-status').css({'color':'red','display':'block'}).html('Server Error'); },
						beforeSend: function(){ $('input#em-admin-check-email').val('<?php _e('Checking...','dbem') ?>'); },
						complete: function(){ $('input#em-admin-check-email').val('<?php _e('Test Email Settings','dbem'); ?>');  }
					});
				});
			});
		</script>
	</div> <!-- . inside -->
	</div> <!-- .postbox --> 
	<?php
}

/**
 * Meta options box for user capabilities. Shared in both MS and Normal options page, hence it's own function 
 */
function em_admin_option_box_caps(){
	global $save_button, $wpdb;
	?>
	<div  class="postbox" >
	<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'User Capabilities', 'dbem' ); ?></span></h3>
	<div class="inside">
            <table class="form-table">
            <tr><td colspan="2">
            	<strong><?php _e('Warning: Changing these values may result in exposing previously hidden information to all users.', 'dbem')?></strong><br />
            </td></tr>
			<?php
            global $wp_roles;
			$cap_docs = array(
				sprintf(__('%s Capabilities','dbem'),__('Event','dbem')) => array(
					/* Event Capabilities */
					'publish_events' => sprintf(__('Users can publish %s and skip any admin approval','dbem'),__('events','dbem')),
					'delete_others_events' => sprintf(__('User can delete other users %s','dbem'),__('events','dbem')),
					'edit_others_events' => sprintf(__('User can edit other users %s','dbem'),__('events','dbem')),
					'delete_events' => sprintf(__('User can delete their own %s','dbem'),__('events','dbem')),
					'edit_events' => sprintf(__('User can create and edit %s','dbem'),__('events','dbem')),
					'read_private_events' => sprintf(__('User can view private %s','dbem'),__('events','dbem')),
					/*'read_events' => sprintf(__('User can view %s','dbem'),__('events','dbem')),*/
				),
				sprintf(__('%s Capabilities','dbem'),__('Recurring Event','dbem')) => array(
					/* Recurring Event Capabilties */
					'publish_recurring_events' => sprintf(__('Users can publish %s and skip any admin approval','dbem'),__('recurring events','dbem')),
					'delete_others_recurring_events' => sprintf(__('User can delete other users %s','dbem'),__('recurring events','dbem')),
					'edit_others_recurring_events' => sprintf(__('User can edit other users %s','dbem'),__('recurring events','dbem')),
					'delete_recurring_events' => sprintf(__('User can delete their own %s','dbem'),__('recurring events','dbem')),
					'edit_recurring_events' => sprintf(__('User can create and edit %s','dbem'),__('recurring events','dbem'))						
				),
				sprintf(__('%s Capabilities','dbem'),__('Location','dbem')) => array(
					/* Location Capabilities */
					'publish_locations' => sprintf(__('Users can publish %s and skip any admin approval','dbem'),__('locations','dbem')),
					'delete_others_locations' => sprintf(__('User can delete other users %s','dbem'),__('locations','dbem')),
					'edit_others_locations' => sprintf(__('User can edit other users %s','dbem'),__('locations','dbem')),
					'delete_locations' => sprintf(__('User can delete their own %s','dbem'),__('locations','dbem')),
					'edit_locations' => sprintf(__('User can create and edit %s','dbem'),__('locations','dbem')),
					'read_private_locations' => sprintf(__('User can view private %s','dbem'),__('locations','dbem')),
					'read_others_locations' => __('User can use other user locations for their events.','dbem'),
					/*'read_locations' => sprintf(__('User can view %s','dbem'),__('locations','dbem')),*/
				),
				sprintf(__('%s Capabilities','dbem'),__('Other','dbem')) => array(
					/* Category Capabilities */
					'delete_event_categories' => sprintf(__('User can delete %s categories and tags.','dbem'),__('event','dbem')),
					'edit_event_categories' => sprintf(__('User can edit %s categories and tags.','dbem'),__('event','dbem')),
					/* Booking Capabilities */
					'manage_others_bookings' => __('User can manage other users individual bookings and event booking settings.','dbem'),
					'manage_bookings' => __('User can use and manage bookings with their events.','dbem'),
					'upload_event_images' => __('User can upload images along with their events and locations.','dbem')
				)
			);
            ?>
            <tr><td colspan="2">
            	<p><em><?php _e('You can now give fine grained control with regards to what your users can do with events. Each user role can have perform different sets of actions.','dbem'); ?></em></p>
            </td></tr>
            <tr><td colspan="2">
            	<?php 
            	if( is_multisite() && is_network_admin() ){
		            echo em_options_radio_binary(__('Apply global capabilities?','dbem'), 'dbem_ms_global_caps', __('If set to yes the capabilities will be applied all your network blogs and you will not be able to set custom capabilities each blog. You can select no later and visit specific blog settings pages to add/remove capabilities.','dbem') );
		        } 
		        ?>
		    </td></tr>
            <tr><td colspan="2">
	            <table class="em-caps-table" style="width:auto;" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<td>&nbsp;</td>
							<?php 
							$odd = 0;
							foreach(array_keys($cap_docs) as $capability_group){
								?><th class="<?php echo ( !is_int($odd/2) ) ? 'odd':''; ?>"><?php echo $capability_group ?></th><?php
								$odd++;
							} 
							?>
						</tr>
					</thead>
					<tbody>
            			<?php foreach($wp_roles->role_objects as $role): ?>
	            		<tr>
	            			<td class="cap"><strong><?php echo $role->name; ?></strong></td>
							<?php 
							$odd = 0;
							foreach($cap_docs as $capability_group){
								?>
	            				<td class="<?php echo ( !is_int($odd/2) ) ? 'odd':''; ?>">
									<?php foreach($capability_group as $cap => $cap_help){ ?>
	            					<input type="checkbox" name="em_capabilities[<?php echo $role->name; ?>][<?php echo $cap ?>]" value="1" id="<?php echo $role->name.'_'.$cap; ?>" <?php echo $role->has_cap($cap) ? 'checked="checked"':''; ?> />
	            					&nbsp;<label for="<?php echo $role->name.'_'.$cap; ?>"><?php echo $cap; ?></label>&nbsp;<a href="#" title="<?php echo $cap_help; ?>">?</a>
	            					<br />
	            					<?php } ?>
	            				</td>
	            				<?php
								$odd++;
							} 
							?>
	            		</tr>
			            <?php endforeach; ?>
			        </tbody>
	            </table>
	        </td></tr>
	        <?php echo $save_button; ?>
		</table>
	</div> <!-- . inside -->
	</div> <!-- .postbox -->
	<?php
}

function em_admin_option_box_uninstall(){
	if( is_multisite() ){
		$uninstall_url = admin_url().'network/admin.php?page=events-manager-options&amp;action=uninstall&amp;_wpnonce='.wp_create_nonce('em_uninstall_'.get_current_user_id().'_wpnonce');
		$reset_url = admin_url().'network/admin.php?page=events-manager-options&amp;action=reset&amp;_wpnonce='.wp_create_nonce('em_reset_'.get_current_user_id().'_wpnonce');
		$recheck_updates_url = admin_url().'network/admin.php?page=events-manager-options&amp;action=recheck_updates&amp;_wpnonce='.wp_create_nonce('em_recheck_updates_'.get_current_user_id().'_wpnonce');
		$check_devs = admin_url().'network/admin.php?page=events-manager-options&amp;action=check_devs&amp;_wpnonce='.wp_create_nonce('em_check_devs_wpnonce');
	}else{
		$uninstall_url = EM_ADMIN_URL.'&amp;page=events-manager-options&amp;action=uninstall&amp;_wpnonce='.wp_create_nonce('em_uninstall_'.get_current_user_id().'_wpnonce');
		$reset_url = EM_ADMIN_URL.'&amp;page=events-manager-options&amp;action=reset&amp;_wpnonce='.wp_create_nonce('em_reset_'.get_current_user_id().'_wpnonce');
		$recheck_updates_url = EM_ADMIN_URL.'&amp;page=events-manager-options&amp;action=recheck_updates&amp;_wpnonce='.wp_create_nonce('em_recheck_updates_'.get_current_user_id().'_wpnonce');
		$check_devs = EM_ADMIN_URL.'&amp;page=events-manager-options&amp;action=check_devs&amp;_wpnonce='.wp_create_nonce('em_check_devs_wpnonce');
	}
	?>
	<div  class="postbox" >
		<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Admin Tools', 'dbem' ); ?> (<?php _e ( 'Uninstall/Reset', 'dbem' ); ?>)</span></h3>
		<div class="inside">
			<h4 style="font-size:1.1em;"><?php _e ( 'Development Versions &amp; Updates', 'dbem' ); ?></h4>
			<p><?php _e('We\'re always making improvements, adding features and fixing bugs between releases. We incrementally make these changes in between updates and make it available as a development version. You can download these manually, but we\'ve made it easy for you. <strong>Warning:</strong> Development versions are not always fully tested before release, use wisely!'); ?></p>
			<table class="form-table">
				<?php em_options_radio_binary ( __( 'Enable Dev Updates?', 'dbem' ), 'dbem_pro_dev_updates', __('If enabled, the latest dev version will always be checked instead of the latest stable version of the plugin.') ); ?>
			</table>
			<p><a href="<?php echo $recheck_updates_url; ?>" class="button-secondary"><?php _e('Re-Check Updates','dbem'); ?></a> - <?php _e('If you would like to check and see if there is a new stable update.','dbem'); ?></p>
			<p><a href="<?php echo $check_devs; ?>" class="button-secondary"><?php _e('Check Dev Versions','dbem'); ?></a> - <?php _e('If you would like to download a dev version, but just as a one-off, you can force a dev version check by clicking the button below. If there is one available, it should appear in your plugin updates page as a regular update.','dbem'); ?></p>
			
			<h4 style="font-size:1.1em;"><?php _e ( 'Uninstall/Reset', 'dbem' ); ?></h4>
			<p><?php _e('Use the buttons below to uninstall Events Manager completely from your system or reset Events Manager to original settings and keep your event data.','dbem'); ?></p>
			<a href="<?php echo $uninstall_url; ?>" class="button-secondary"><?php _e('Uninstall','dbem'); ?></a>
			<a href="<?php echo $reset_url; ?>" class="button-secondary"><?php _e('Reset','dbem'); ?></a>
		</div>
	</div>
	<?php	
}
?>