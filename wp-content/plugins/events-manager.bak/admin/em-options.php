<?php

//Function composing the options subpanel
function em_options_save(){
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
					//$postValue = EM_Object::sanitize($postValue)
					update_option($postKey, stripslashes($postValue));
				}
			}
		}
		
		//set capabilities
		if( !empty($_POST['em_capabilities']) && is_array($_POST['em_capabilities']) && (!is_multisite() || is_multisite() && is_super_admin()) ){
			global $em_capabilities_array, $wp_roles;
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
		do_action('em_options_save');
		function em_options_saved_notice(){
			?>
			<div class="updated"><p><strong><?php _e('Changes saved.', 'dbem'); ?></strong></p></div>
			<?php
		}
		add_action ( 'admin_notices', 'em_options_saved_notice' );
		if( get_option('dbem_debug') ){
			include_once( WP_PLUGIN_DIR.'/events-manager/em-debug.php');
		}
	}   
}
add_action('admin_head', 'em_options_save');          



function em_admin_options_page() {
	global $wpdb;
	//TODO place all options into an array
	$events_placeholders = '<a href="admin.php?page=events-manager-help#event-placeholders">'. __('Event Related Placeholders','dbem') .'</a>';
	$locations_placeholders = '<a href="admin.php?page=events-manager-help#location-placeholders">'. __('Location Related Placeholders','dbem') .'</a>';
	$bookings_placeholders = '<a href="admin.php?page=events-manager-help#booking-placeholders">'. __('Booking Related Placeholders','dbem') .'</a>';
	$categories_placeholders = '<a href="admin.php?page=events-manager-help#category-placeholders">'. __('Category Related Placeholders','dbem') .'</a>';
	$events_placeholder_tip = " ". sprintf(__('This accepts %s and %s placeholders.','dbem'),$events_placeholders, $locations_placeholders);
	$locations_placeholder_tip = " ". sprintf(__('This accepts %s placeholders.','dbem'), $locations_placeholders);
	$categories_placeholder_tip = " ". sprintf(__('This accepts %s placeholders.','dbem'), $categories_placeholders);
	$bookings_placeholder_tip = " ". sprintf(__('This accepts %s, %s and %s placeholders.','dbem'), $bookings_placeholders, $events_placeholders, $locations_placeholders);
	
	$save_button = '<tr><th>&nbsp;</th><td><p class="submit" style="margin:0px; padding:0px; text-align:right;"><input type="submit" id="dbem_options_submit" name="Submit" value="'. __( 'Save Changes', 'dbem') .' ('. __('All','dbem') .')" /></p></ts></td></tr>';
	//Do some multisite checking here for reuse
	$multisite_view = (is_multisite() && is_super_admin()) ? ' - ('.__('Only Network Admins see this','dbem').')':'';
	?>	
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function($){
			var close_text = '<?php _e('Collapse All','dbem'); ?>';
			var open_text = '<?php _e('Expand All','dbem'); ?>';
			var open_close = $('<a href="#" style="display:block; float:right; clear:right; margin:10px;">'+close_text+'</a>');
			$('#icon-options-general').after(open_close);
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
	<div class="wrap">
		<div id='icon-options-general' class='icon32'><br />
		</div>		
		<h2><?php _e ( 'Event Manager Options', 'dbem' ); ?></h2>
		<?php 
		/*
		 * START MIGRATION BIT
		 */
			if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'bookings_migrate' ){
				require_once(dirname(__FILE__).'/../em-install.php');
				em_migrate_bookings();
			}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'bookings_migrate_delete'){
				require_once( dirname(__FILE__).'/../em-install.php');
				em_migrate_bookings_delete();
			}
		?>
		<?php if( $wpdb->get_var("SHOW TABLES LIKE '".EM_PEOPLE_TABLE."'") == EM_PEOPLE_TABLE ): ?>
			<?php if( $wpdb->get_var('SELECT COUNT(*) FROM '.EM_TICKETS_BOOKINGS_TABLE) > 0 ): ?>
				<div class='updated'>
					<p>
					It looks like you've already tried reimporting some bookings into the new version (or new bookings have been made since you installed). 
					If everything looks correct, you can 
					<a href="admin.php?page=events-manager-options&amp;_wpnonce=<?php echo wp_create_nonce('bookings_migrate_delete'); ?>&amp;action=bookings_migrate_delete">delete the unused tables</a>, 
					or you can also safely try 
					<a href="admin.php?page=events-manager-options&amp;_wpnonce=<?php echo wp_create_nonce('bookings_migrate'); ?>&amp;action=bookings_migrate">re-importing again</a>.
					</p>
				</div>
			<?php else: ?>
				<div class='updated'>
					<p>It looks like you've upgraded from Events Manager version 3.0, meaning your old bookings won't work until you re-import them. 
					Events Manager 3.0 kept booking user info in a simple database table, whereas now people that make bookings get a subscriber account so 
					they can access private booking information. You have to choice to 
					<a href="admin.php?page=events-manager-options&amp;_wpnonce=<?php echo wp_create_nonce('bookings_migrate'); ?>&amp;action=bookings_migrate">import all your old bookings</a> 
					and create wordpress accounts for bookers, or you can also start afresh and 
					<a href="admin.php?page=events-manager-options&amp;_wpnonce=<?php echo wp_create_nonce('bookings_migrate_delete'); ?>&amp;action=bookings_migrate_delete">delete the old bookings</a>.
					</p>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php
		/*
		 *  END MIGRATION BIT
		 */
		?>
		<form id="dbem_options_form" method="post" action="">
			<div class="metabox-holder">         
			<!-- // TODO Move style in css -->
			<div class='postbox-container' style='width: 99.5%'>
			<div id="">
		  
			<?php if ( is_multisite() && is_super_admin() ) : ?>
			<div  class="postbox " >
				<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Multi Site Options', 'dbem' ); ?> <?php echo $multisite_view; ?></span></h3>
				<div class="inside">
		            <table class="form-table">
						<?php 
						em_options_radio_binary ( __( 'Enable global tables mode?', 'dbem'), 'dbem_ms_global_table', __( 'Setting this to yes will make all events save in the main site event tables (EM must also be activated). This allows you to share events across different blogs, such as showing events in your network whilst allowing users to display and manage their events within their own blog. Bear in mind that activating this will mean old events created on the sub-blogs will not be accessible anymore, and if you switch back they will be but new events created during global events mode will only remain on the main site.','dbem' ) );
						em_options_radio_binary ( __( 'Display global events on main blog?', 'dbem'), 'dbem_ms_global_events', __( 'Displays events from all sites on the network by default. You can still restrict events by blog using shortcodes and template tags coupled with the <code>blog</code> attribute. Requires global tables to be turned on.','dbem' ) );
						em_options_radio_binary ( __( 'Link sub-site events directly to sub-site?', 'dbem'), 'dbem_ms_global_events_links', __( 'When displaying global events on the main site you have the option of users viewing the event details on the main site or being directed to the sub-site.','dbem' ) );
						echo $save_button;
						?>
					</table>
					    
				</div> <!-- . inside --> 
			</div> <!-- .postbox --> 
			<?php endif; ?>
		  
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'General options', 'dbem' ); ?> </span></h3>
			<div class="inside">
	            <table class="form-table">
					<?php 
					em_options_radio_binary ( __( 'Use dropdown for locations?', 'dbem' ), 'dbem_use_select_for_locations', __( 'Select yes to select location from a drow-down menu; location selection will be faster, but you will lose the ability to insert locations with events','dbem' ) );  
					em_options_radio_binary ( __( 'Use recurrence?', 'dbem' ), 'dbem_recurrence_enabled', __( 'Select yes to enable the recurrence features feature','dbem' ) ); 
					em_options_radio_binary ( __( 'Enable bookings?', 'dbem' ), 'dbem_rsvp_enabled', __( 'Select yes to allow bookings and tickets for events.','dbem' ) );     
					em_options_radio_binary ( __( 'Use categories?', 'dbem' ), 'dbem_categories_enabled', __( 'Select yes to enable the category features','dbem' ) );     
					em_options_radio_binary ( __( 'Use event attributes?', 'dbem' ), 'dbem_attributes_enabled', __( 'Select yes to enable the attributes feature','dbem' ) );
					
					/*default category*/
					$category_options = array();
					$category_options[0] = __('no default category','dbem');
					$EM_Categories = EM_Categories::get();
					foreach($EM_Categories as $EM_Category){
				 		$category_options[$EM_Category->id] = $EM_Category->name;
				 	}
					em_options_select ( __( 'Default Category', 'dbem' ), 'dbem_default_category', $category_options, __( 'This option allows you to select the default category when adding an event.','dbem' )." ".__('(not applicable with event ownership on presently, coming soon!)','dbem') );
					
					/*default location*/
					$location_options = array();
					$location_options[0] = __('no default location','dbem');
					$EM_Locations = EM_Locations::get();
					foreach($EM_Locations as $EM_Location){
				 		$location_options[$EM_Location->id] = $EM_Location->name;
				 	}
					em_options_select ( __( 'Default Location', 'dbem' ), 'dbem_default_location', $location_options, __( 'This option allows you to select the default location when adding an event.','dbem' )." ".__('(not applicable with event ownership on presently, coming soon!)','dbem') );
					
					/*default location country*/
					em_options_select ( __( 'Default Location Country', 'dbem' ), 'dbem_location_default_country', em_get_countries(__('no default country', 'dbem')), __('If you select a default country, that will be pre-selected when creating a new location.','dbem') );
										
					em_options_textarea ( __( 'Event Attributes', 'dbem' ), 'dbem_placeholders_custom', sprintf(__( "You can also add event attributes here, one per line in this format <code>#_ATT{key}</code>. They will not appear on event pages unless you insert them into another template below, but you may want to store extra information about an event for other uses. <a href='%s'>More information on placeholders.</a>", 'dbem' ), 'admin.php?page=events-manager-help') );
					
					em_options_radio_binary ( __('Show some love?','dbem'), 'dbem_credits', __( 'Hundreds of free hours have gone into making this free plugin, show your support and add a small link to the plugin website at the bottom of your event pages.','dbem' ) );
					echo $save_button;
					?>
				</table>
				    
			</div> <!-- . inside --> 
			</div> <!-- .postbox --> 
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Events page', 'dbem' ); ?> </span></h3>
			<div class="inside">
                 <table class="form-table">         
				 	<?php
				 	//Wordpress Pages
				 	global $em_disable_filter; //Using a flag here instead
				 	$em_disable_filter = true;     
				 	$get_pages = get_pages();
				 	$events_page_options = array();
				 	$events_page_options[0] = __('[No Events Page]', 'dbem');
				 	//TODO Add the hierarchy style ddm, like when choosing page parents
				 	foreach($get_pages as $page){
				 		$events_page_options[$page->ID] = $page->post_title;
				 	}
				   	em_options_select ( __( 'Events page', 'dbem' ), 'dbem_events_page', $events_page_options, __( 'This option allows you to select which page to use as an events page','dbem' ) );
					$em_disable_filter = false;
					//Rest
					em_options_radio_binary ( __( 'Show events page in lists?', 'dbem' ), 'dbem_list_events_page', __( 'Check this option if you want the events page to appear together with other pages in pages lists.', 'dbem' ) ); 
					em_options_radio_binary ( __( 'Display calendar in events page?', 'dbem' ), 'dbem_display_calendar_in_events_page', __( 'This options allows to display the calendar in the events page, instead of the default list. It is recommended not to display both the calendar widget and a calendar page.','dbem' ).' '.__('If you would like to show events that span over more than one day, see the Calendar section on this page.','dbem') );
					em_options_radio_binary ( __( 'Disable title rewriting?', 'dbem' ), 'dbem_disable_title_rewrites', __( "Some wordpress themes don't follow best practices when generating navigation menus, and so the automatic title rewriting feature may cause problems, if your menus aren't working correctly on the event pages, try setting this to 'Yes', and provide an appropriate HTML title format below.",'dbem' ) );
					em_options_input_text ( __( 'Event Manager titles', 'dbem' ), 'dbem_title_html', __( "This only setting only matters if you selected 'Yes' to above. You will notice the events page titles aren't being rewritten, and you have a new title underneath the default page name. This is where you control the HTML of this title. Make sure you keep the #_PAGETITLE placeholder here, as that's what is rewritten by events manager. To control what's rewritten in this title, see settings further down for page titles.", 'dbem' ) );
					em_options_input_text ( __( 'Event List Limits', 'dbem' ), 'dbem_events_default_limit', __( "This will control how many events are shown on one list by default.", 'dbem' ) );
					em_options_radio_binary ( __( 'Are current events past events?', 'dbem' ), 'dbem_events_current_are_past', __( "By default, events that are have an end date later than today will be included in searches, set this to yes to consider events that started 'yesterday' as past.", 'dbem' ) );
					em_options_radio_binary ( __( 'Show events search?', 'dbem' ), 'dbem_events_page_search', __( "If set to yes, a search form will appear just above your list of events.", 'dbem' ) );
					?>
					<tr valign="top" id='dbem_events_default_orderby_row'>
				   		<th scope="row"><?php _e('Default event list ordering','dbem'); ?></th>
				   		<td>   
							<select name="dbem_events_default_orderby" >
								<?php 
									$orderby_options = apply_filters('em_settings_events_default_orderby_ddm', array(
										'start_date,start_time,name' => __('Order by start date, start time, then event name','dbem'),
										'name,start_date,start_time' => __('Order by name, start date, then start time','dbem'),
										'name,end_date,end_time' => __('Order by name, end date, then end time','dbem'),
										'end_date,end_time,name' => __('Order by end date, end time, then event name','dbem'),
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
					echo $save_button;
					?>				
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Events format', 'dbem' ); ?> </span></h3>
			<div class="inside">
            	<table class="form-table">
				 	<tr><td><strong><?php echo sprintf(__('%s Page','dbem'),__('Events','dbem')); ?></strong></td></tr>
					<?php
					em_options_input_text ( __( 'Events page title', 'dbem' ), 'dbem_events_page_title', __( 'The title on the multiple events page.', 'dbem' ) );
					em_options_textarea ( __( 'Default event list format header', 'dbem' ), 'dbem_event_list_item_format_header', __( 'This content will appear just above your code for the default event list format. Default is blank', 'dbem' ) );
				 	em_options_textarea ( __( 'Default event list format', 'dbem' ), 'dbem_event_list_item_format', __( 'The format of any events in a list.', 'dbem' ).$events_placeholder_tip );
					em_options_textarea ( __( 'Default event list format footer', 'dbem' ), 'dbem_event_list_item_format_footer', __( 'This content will appear just below your code for the default event list format. Default is blank', 'dbem' ) );
					em_options_input_text ( __( 'No events message', 'dbem' ), 'dbem_no_events_message', __( 'The message displayed when no events are available.', 'dbem' ) );
					em_options_input_text ( __( 'List events by date title', 'dbem' ), 'dbem_list_date_title', __( 'If viewing a page for events on a specific date, this is the title that would show up. To insert date values, use <a href="http://www.php.net/manual/en/function.date.php">PHP time format characters</a>  with a <code>#</code> symbol before them, i.e. <code>#m</code>, <code>#M</code>, <code>#j</code>, etc.<br/>', 'dbem' ) );
					?>
				 	<tr><td><strong><?php echo sprintf(__('Single %s Page','dbem'),__('Event','dbem')); ?></strong></td></tr>
				 	<?php
					em_options_input_text ( __( 'Single event page title format', 'dbem' ), 'dbem_event_page_title_format', __( 'The format of a single event page title.', 'dbem' ).$events_placeholder_tip );
					em_options_textarea ( __( 'Default single event format', 'dbem' ), 'dbem_single_event_format', __( 'The format of a single event page.', 'dbem' ).$events_placeholder_tip );
					echo $save_button;
					?>
				</table> 	
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			      
           	<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Calendar format', 'dbem' ); ?></span></h3>
			<div class="inside">
            	<table class="form-table">
					<?php
				    em_options_input_text ( __( 'Small calendar title', 'dbem' ), 'dbem_small_calendar_event_title_format', __( 'The format of the title, corresponding to the text that appears when hovering on an eventful calendar day.', 'dbem' ).$events_placeholder_tip );
					em_options_input_text ( __( 'Small calendar title separator', 'dbem' ), 'dbem_small_calendar_event_title_separator', __( 'The separator appearing on the above title when more than one events are taking place on the same day.', 'dbem' ) );         
				    em_options_input_text ( __( 'Full calendar events format', 'dbem' ), 'dbem_full_calendar_event_format', __( 'The format of each event when displayed in the full calendar. Remember to include <code>li</code> tags before and after the event.', 'dbem' ).$events_placeholder_tip );
				    em_options_radio_binary ( __( 'Show long events on calendar pages?', 'dbem' ), 'dbem_full_calendar_long_events', __( "If you are showing a calendar on the events page (see Events format section on this page), you have the option of showing events that span over days on each day it occurs.",'dbem' ) );
				    em_options_radio_binary ( __( 'Show list on day with single event?', 'dbem' ), 'dbem_display_calendar_day_single', __( "By default, if a calendar day only has one event, it display a single event when clicking on the link of that calendar date. If you select Yes here, you will get always see a list of events.",'dbem' ) );
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
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Locations format', 'dbem' ); ?> </span></h3>
			<div class="inside">
            	<table class="form-table">
				 	<tr><td><strong><?php echo sprintf(__('%s Page','dbem'),__('Locations','dbem')); ?></strong></td></tr>
					<?php
					em_options_input_text ( sprintf(__('%s page title','dbem'),__('Locations','dbem')), 'dbem_locations_page_title', sprintf(__( 'The title on the multiple %s page.', 'dbem' ), __('locations','dbem')) );
					em_options_textarea ( sprintf(__('%s list header format','dbem'),__('Locations','dbem')), 'dbem_location_list_item_format_header', sprintf(__( 'This content will appear just above your code for the %s list format below. Default is blank', 'dbem' ), __('locations','dbem')) );
				 	em_options_textarea ( sprintf(__('%s list item format','dbem'),__('Locations','dbem')), 'dbem_location_list_item_format', sprintf(__( 'The format of a single %s in a list.', 'dbem' ), __('locations','dbem')).$locations_placeholder_tip );
					em_options_textarea ( sprintf(__('%s list footer format','dbem'),__('Locations','dbem')), 'dbem_location_list_item_format_footer', sprintf(__( 'This content will appear just below your code for the %s list format above. Default is blank', 'dbem' ), __('locations','dbem')) );
					em_options_input_text ( sprintf(__( 'No %s message', 'dbem' ),__('Locations','dbem')), 'dbem_no_locations_message', sprintf( __( 'The message displayed when no %s are available.', 'dbem' ), __('locations','dbem')) );
				 	?>
				 	<tr><td><strong><?php echo sprintf(__('Single %s Page','dbem'),__('Location','dbem')); ?></strong></td></tr>
				 	<?php
					em_options_input_text (sprintf( __( 'Single %s title format', 'dbem' ),__('location','dbem')), 'dbem_location_page_title_format', __( 'The format of a single location page title.', 'dbem' ).$locations_placeholder_tip );
					em_options_textarea ( sprintf(__('Single %s page format', 'dbem' ),__('location','dbem')), 'dbem_single_location_format', __( 'The format of a single location page.', 'dbem' ).$locations_placeholder_tip );
					em_options_textarea ( __( 'Default location balloon format', 'dbem' ), 'dbem_location_baloon_format', __( 'The format of of the text appearing in the baloon describing the location a single location map.', 'dbem' ).$locations_placeholder_tip );
					 ?>
				 	<tr><td><strong><?php echo sprintf(__('%s List Formats','dbem'),__('Event','dbem')); ?></strong></td></tr>
				 	<?php
					em_options_textarea ( sprintf(__( 'Default %s list format', 'dbem' ),__('events','dbem')), 'dbem_location_event_list_item_format', __( 'The format of the events the list inserted in the location page through the <code>#_LOCATIONNEXTEVENTS</code>, <code>#_LOCATIONNEXTEVENTS</code> and <code>#_LOCATIONALLEVENTS</code> element.', 'dbem' ).$locations_placeholder_tip );
					em_options_textarea ( sprintf(__( 'No %s message', 'dbem' ),__('events','dbem')), 'dbem_location_no_events_message', __( 'The message to be displayed in the list generated by <code>#_LOCATIONNEXTEVENTS</code>, <code>#_LOCATIONNEXTEVENTS</code> and <code>#_LOCATIONALLEVENTS</code> when no events are available.', 'dbem' ) );
					echo $save_button;
					?>
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Categories format', 'dbem' ); ?> </span></h3>
			<div class="inside">
            	<table class="form-table">
				 	<tr><td><strong><?php echo sprintf(__('%s Page','dbem'),__('Categories','dbem')); ?></strong></td></tr>
					<?php
					em_options_input_text ( sprintf(__('%s page title','dbem'),__('Categories','dbem')), 'dbem_categories_page_title', sprintf(__( 'The title on the multiple %s page.', 'dbem' ), __('categories','dbem')) );
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
					em_options_textarea ( sprintf(__( 'Default %s list format', 'dbem' ),__('events','dbem')), 'dbem_category_event_list_item_format', __( 'The format of the events the list inserted in the category page through the <code>#_CATEGORYNEXTEVENTS</code>, <code>#_CATEGORYNEXTEVENTS</code> and <code>#_CATEGORYALLEVENTS</code> element.', 'dbem' ).$categories_placeholder_tip );
					em_options_textarea ( sprintf(__( 'No %s message', 'dbem' ),__('events','dbem')), 'dbem_category_no_events_message', __( 'The message to be displayed in the list generated by <code>#_CATEGORYNEXTEVENTS</code>, <code>#_CATEGORYNEXTEVENTS</code> and <code>#_CATEGORYALLEVENTS</code> when no events are available.', 'dbem' ) );
					echo $save_button;
					?>
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'RSS feed format', 'dbem' ); ?> </span></h3>
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
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Maps and geotagging', 'dbem' ); ?> </span></h3>
			<div class="inside">
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
					<?php
					em_options_textarea ( __( 'Map text format', 'dbem' ), 'dbem_map_text_format', __( 'The text format inside the map balloons.', 'dbem' ).$events_placeholder_tip );
					echo $save_button;     
					?> 
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Booking and Ticketing Options', 'dbem' ); ?> </span></h3>
			<div class="inside">
				<table class='form-table'> 
					<?php 
					$ticket_orders = array(
						'ticket_price DESC, ticket_name ASC'=>__('Ticket Price (Descending)','dbem'),
						'ticket_price ASC, ticket_name ASC'=>__('Ticket Price (Ascending)','dbem'),
						'ticket_name DESC, ticket_price DESC'=>__('Ticket Name (Ascending)','dbem'),
						'ticket_name ASC, ticket_price DESC'=>__('Ticket Name (Descending)','dbem')
					);
					em_options_radio_binary ( __( 'Approval Required?', 'dbem' ), 'dbem_bookings_approval', __( 'Bookings will not be confirmed until the event administrator approves it.', 'dbem' ) );
					em_options_radio_binary ( __( 'Can users cancel their booking?', 'dbem' ), 'dbem_bookings_user_cancellation', __( 'If enabled, users can cancel their bookings themselves from their bookings page.', 'dbem' ) );
					em_options_select ( __( 'Currency', 'dbem' ), 'dbem_bookings_currency', em_get_currencies()->names, __( 'Choose your currency for displaying event pricing.', 'dbem' ) );
					em_options_radio_binary ( __( 'Single ticket mode?', 'dbem' ), 'dbem_bookings_tickets_single', __( 'In single ticket mode, users can only create one ticket per booking (and will not see options to add more tickets).', 'dbem' ) );
					em_options_radio_binary ( __( 'Show ticket table in single ticket mode?', 'dbem' ), 'dbem_bookings_tickets_single_form', __( 'If you prefer a ticket table like with multiple tickets, even for single ticket events, enable this.', 'dbem' ) );
					em_options_radio_binary ( __( 'Show unavailable tickets?', 'dbem' ), 'dbem_bookings_tickets_show_unavailable', __( 'You can choose whether or not to show unavailable tickets to visitors.', 'dbem' ) );
					em_options_radio_binary ( __( 'Reserved unconfirmed spaces?', 'dbem' ), 'dbem_bookings_approval_reserved', __( 'By default, event spaces become unavailable once there are enough CONFIRMED bookings. To reserve spaces even if unnapproved, choose yes.', 'dbem' ) );
					em_options_radio_binary ( __( 'Show multiple tickets if logged out?', 'dbem' ), 'dbem_bookings_tickets_show_loggedout', __( 'If logged out, a user will be asked to register in order to book. However, we can show available tickets if you have more than one ticket.', 'dbem' ) );
					em_options_select ( __( 'Order Tickets By', 'dbem' ), 'dbem_bookings_tickets_orderby', $ticket_orders, __( 'Choose which order your tickets appear.', 'dbem' ) );
					em_options_radio_binary ( __( 'Allow overbooking when approving?', 'dbem' ), 'dbem_bookings_approval_overbooking', __( 'If you get a lot of pending bookings and you decide to allow more bookings than spaces allow, setting this to yes will allow you to override the event space limit when manually approving.', 'dbem' ) );
					em_options_radio_binary ( __( 'Allow guest bookings?', 'dbem' ), 'dbem_bookings_anonymous', __( 'If enabled, guest visitors can supply an email address and a user account will automatically be created for them along with their booking. They will be also be able to log back in with that newly created account.', 'dbem' ) );
					em_options_radio_binary ( __( 'Allow double bookings?', 'dbem' ), 'dbem_bookings_double', __( 'If enabled, users can book an event more than once.', 'dbem' ) );
					em_options_radio_binary ( __( 'Display login form?', 'dbem' ), 'dbem_bookings_login_form', __( 'Choose whether or not to display a login form in the booking form area to remind your members to log in before booking.', 'dbem' ) );
					?>
					<tr><td colspan='2'><h4><?php _e('Booking form feedback messages','dbem') ?></h4></td></tr>
					<tr><td colspan='2'><?php _e('When a booking is made by a user, a feedback message is shown depending on the result, which can be customized below.','dbem'); ?></td></tr>
					<?php
					em_options_input_text ( __( 'Successful booking', 'dbem' ), 'dbem_booking_feedback', __( 'When a booking is registered and confirmed.', 'dbem' ) );
					em_options_input_text ( __( 'Successful pending booking', 'dbem' ), 'dbem_booking_feedback_pending', __( 'When a booking is registered but pending.', 'dbem' ) );
					em_options_input_text ( __( 'Not enough spaces', 'dbem' ), 'dbem_booking_feedback_full', __( 'When a booking cannot be made due to lack of spaces.', 'dbem' ) );
					em_options_input_text ( __( 'Errors', 'dbem' ), 'dbem_booking_feedback_error', __( 'When a booking cannot be made due to an error when filling the form. Below this, there will be a dynamic list of errors.', 'dbem' ) );
					em_options_input_text ( __( 'User must log in', 'dbem' ), 'dbem_booking_feedback_log_in', __( 'When a user must log in before making a booking.', 'dbem' ) );
					em_options_input_text ( __( 'Error mailing user', 'dbem' ), 'dbem_booking_feedback_nomail', __( 'If a booking is made and an email cannot be sent, this is added to the success message.', 'dbem' ) );
					em_options_input_text ( __( 'Already booked', 'dbem' ), 'dbem_booking_feedback_already_booked', __( 'If the user made a previous booking and cannot double-book.', 'dbem' ) );
					em_options_input_text ( __( 'No spaces booked', 'dbem' ), 'dbem_booking_feedback_min_space', __( 'If the user tries to make a booking without requesting any spaces.', 'dbem' ) );
					echo $save_button; 
					?>
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Booking Email Templates', 'dbem' ); ?> </span></h3>
			<div class="inside">
				<table class='form-table'>
					<?php
					em_options_select ( __( 'Default contact person', 'dbem' ), 'dbem_default_contact_person', em_get_wp_users(), __( 'Select the default contact person. This user will be employed whenever a contact person is not explicitly specified for an event', 'dbem' ) );
					em_options_input_text ( __( 'Email events admin?', 'dbem' ), 'dbem_bookings_notify_admin', __( "If you would like every event booking confirmation email sent to an administrator write their email here (leave blank to not send an email).", 'dbem' ) );
					em_options_radio_binary ( __( 'Email contact person?', 'dbem' ), 'dbem_bookings_contact_email', __( 'Check this option if you want the event contact to receive an email when someone books places. An email will be sent when a booking is first made (regardless if confirmed or pending)', 'dbem' ) );
					em_options_radio_binary ( __( 'Disable new registration email?', 'dbem' ), 'dbem_email_disable_registration', __( 'Check this option if you want to prevent the wordpress registration email from going out when a user anonymously books an event.', 'dbem' ) );
					?>
					<tr><td colspan='2'><h4><?php _e('Contact person booking confirmed','dbem') ?></h4></td></tr>
					<tr><td colspan='2'><?php echo __('An email will be sent to the event contact when a booking is first made.','dbem').$bookings_placeholder_tip ?></td></tr>
					<?php
					em_options_input_text ( __( 'Contact person email subject', 'dbem' ), 'dbem_bookings_contact_email_subject', '' );
					em_options_textarea ( __( 'Contact person email', 'dbem' ), 'dbem_bookings_contact_email_body', '' );
					?>
					<tr><td colspan='2'><h4><?php _e('Contact person booking cancelled','dbem') ?></h4></td></tr>
					<tr><td colspan='2'><?php echo __('An email will be sent to the event contact if someone cancels their booking.','dbem').$bookings_placeholder_tip ?></td></tr>
					<?php
					em_options_input_text ( __( 'Contact person cancellation subject', 'dbem' ), 'dbem_contactperson_email_cancelled_subject', '' );
					em_options_textarea ( __( 'Contact person cancellation email', 'dbem' ), 'dbem_contactperson_email_cancelled_body', '' );
					?>
					<tr><td colspan='2'><h4><?php _e('Confirmed booking email','dbem') ?></h4></td></tr>
					<tr><td colspan='2'><?php echo __('This is sent when a person\'s booking is confirmed. This will be sent automatically if approvals are required and the booking is approved. If approvals are disabled, this is sent out when a user first submits their booking.','dbem').$bookings_placeholder_tip ?></td></tr>
					<?php
					em_options_input_text ( __( 'Booking confirmed email subject', 'dbem' ), 'dbem_bookings_email_confirmed_subject', '' );
					em_options_textarea ( __( 'Booking confirmed email', 'dbem' ), 'dbem_bookings_email_confirmed_body', '' );
					?>
					<tr><td colspan='2'><h4><?php _e('Pending booking email','dbem') ?></h4></td></tr>
					<tr><td colspan='2'><?php echo __( 'This will be sent to the person when they first submit their booking. Not relevant if bookings don\'t require approval.', 'dbem' ).$bookings_placeholder_tip ?></td></tr>
					<?php
					em_options_input_text ( __( 'Booking pending email subject', 'dbem' ), 'dbem_bookings_email_pending_subject', '');
					em_options_textarea ( __( 'Booking pending email', 'dbem' ), 'dbem_bookings_email_pending_body','') ;
					?>
					<tr><td colspan='2'><h4><?php _e('Rejected booking email','dbem') ?></h4></td></tr>
					<tr><td colspan='2'><?php echo __( 'This will be sent automatically when a booking is rejected. Not relevant if bookings don\'t require approval.', 'dbem' ).$bookings_placeholder_tip ?></td></tr>
					<?php
					em_options_input_text ( __( 'Booking rejected email subject', 'dbem' ), 'dbem_bookings_email_rejected_subject', __( "The subject of the email sent to the person making a booking that is awaiting administrator approval. Not relevant if bookings don't require approval.", 'dbem' ).$bookings_placeholder_tip );
					em_options_textarea ( __( 'Booking rejected email', 'dbem' ), 'dbem_bookings_email_rejected_body', __( 'The body of the email which will be sent to the person if the booking is rejected. Not relevant if bookings don\'t require approval.', 'dbem' ).$bookings_placeholder_tip );
					echo $save_button;
					?>
					<tr><td colspan='2'><h4><?php _e('Booking cancelled','dbem') ?></h4></td></tr>
					<tr><td colspan='2'><?php echo __('This will be sent when a user cancels their booking.','dbem').$bookings_placeholder_tip ?></td></tr>
					<?php
					em_options_input_text ( __( 'Booking cancelled email subject', 'dbem' ), 'dbem_bookings_email_cancelled_subject', '' );
					em_options_textarea ( __( 'Booking cancelled email', 'dbem' ), 'dbem_bookings_email_cancelled_body', '' );
					?>
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<?php if ( !is_multisite() || (is_multisite() && is_super_admin()) ) : ?>
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Email Settings', 'dbem' ); ?> <?php echo $multisite_view; ?></span></h3>
			<div class="inside">
				<table class='form-table'>
					<?php
					em_options_input_text ( __( 'Notification sender name', 'dbem' ), 'dbem_mail_sender_name', __( "Insert the display name of the notification sender.", 'dbem' ) );
					em_options_input_text ( __( 'Notification sender address', 'dbem' ), 'dbem_mail_sender_address', __( "Insert the address of the notification sender.", 'dbem' ) );
					em_options_input_text ( 'Mail sending port', 'dbem_rsvp_mail_port', __( "The port through which you e-mail notifications will be sent. Make sure the firewall doesn't block this port", 'dbem' ) );
					em_options_select ( __( 'Mail sending method', 'dbem' ), 'dbem_rsvp_mail_send_method', array ('smtp' => 'SMTP', 'mail' => __( 'PHP mail function', 'dbem' ), 'sendmail' => 'Sendmail', 'qmail' => 'Qmail', 'wp_mail' => 'WP Mail' ), __( 'Select the method to send email notification.', 'dbem' ) );
					em_options_radio_binary ( __( 'Use SMTP authentication?', 'dbem' ), 'dbem_rsvp_mail_SMTPAuth', __( 'SMTP authentication is often needed. If you use GMail, make sure to set this parameter to Yes', 'dbem' ) );
					em_options_input_text ( 'SMTP host', 'dbem_smtp_host', __( "The SMTP host. Usually it corresponds to 'localhost'. If you use GMail, set this value to 'ssl://smtp.gmail.com:465'.", 'dbem' ) );
					em_options_input_text ( __( 'SMTP username', 'dbem' ), 'dbem_smtp_username', __( "Insert the username to be used to access your SMTP server.", 'dbem' ) );
					em_options_input_password ( __( 'SMTP password', 'dbem' ), "dbem_smtp_password", __( "Insert the password to be used to access your SMTP server", 'dbem' ) );
					echo $save_button;
					?>
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Images size', 'dbem' ); ?> <?php echo $multisite_view; ?> </span></h3>
			<div class="inside">
				<table class='form-table'>
					<?php
					em_options_input_text ( __( 'Maximum width (px)', 'dbem' ), 'dbem_image_max_width', __( 'The maximum allowed width for images uploades', 'dbem' ) );
					em_options_input_text ( __( 'Maximum height (px)', 'dbem' ), 'dbem_image_max_height', __( "The maximum allowed height for images uploaded, in pixels", 'dbem' ) );
					em_options_input_text ( __( 'Maximum size (bytes)', 'dbem' ), 'dbem_image_max_size', __( "The maximum allowed size for images uploaded, in bytes", 'dbem' ) );
					echo $save_button;
					?>
				</table>
			</div> <!-- . inside -->
			</div> <!-- .postbox -->
			
			<div  class="postbox" >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'User Capabilities', 'dbem' ); ?> <?php echo $multisite_view; ?></span></h3>
			<div class="inside">
	            <table class="form-table">
	            	<tr><td colspan="2">
	            		<strong><?php _e('Warning: Changing these values may result in exposing previously hidden information to all users.', 'dbem')?></strong><br />
	            	</td></tr>
					<?php
            		global $wp_roles;
            		global $em_capabilities_array;
	            	?>
	            	<tr><td colspan="2">
	            		<p><em><?php _e('You can now give fine grained control with regards to what your users can do with events. Each user role can have perform different sets of actions.','dbem'); ?></em></p>
			            <table class="em-caps-table" style="width:auto;" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<td>&nbsp;</td>
									<?php 
									$odd = 0;
									$cap_docs = array(
										'publish_events' => __('Users can publish events and skip any admin approval.','dbem'),
										'edit_categories' => __('User can edit the global categories.','dbem'),
										'delete_others_events' => __('User can delete other users events.','dbem'),
										'delete_others_locations' => __('User can delete other users locations.','dbem'),
										'edit_others_locations' => __('User can edit other users locations.','dbem'),
										'manage_others_bookings' => __('User can manage other users individual bookings and event booking settings.','dbem'),
										'edit_others_events' => __('User can edit other users events.','dbem'),
										'delete_locations' => __('User can delete their own locations.','dbem'),
										'delete_events' => __('User can delete their events.','dbem'),
										'edit_locations' => __('User can edit their locations.','dbem'),
										'manage_bookings' => __('User can use and manage bookings with their events.','dbem'),
										'read_others_locations' => __('User can view other users locations, to make locations shared by all users allow all event user roles to view all locations.','dbem'),
										'edit_recurrences' => __('User can create recurrent events.','dbem'),
										'edit_events' => __('User can create and edit their events.','dbem')
									);
									foreach(array_keys($em_capabilities_array) as $capability){
										?><th class="<?php echo $capability ?> <?php echo ( !is_int($odd/2) ) ? 'odd':''; ?>">&nbsp;<a href="#" title="<?php echo $cap_docs[$capability]; ?>">?</a></th><?php
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
									foreach(array_keys($em_capabilities_array) as $capability){
			            				?><td class="<?php echo ( !is_int($odd/2) ) ? 'odd':''; ?>"><input type="checkbox" name="em_capabilities[<?php echo $role->name; ?>][<?php echo $capability ?>]" value="1" <?php echo $role->has_cap($capability) ? 'checked="checked"':''; ?> /></td><?php
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
			<?php endif; ?> 
			
			<div  class="postbox" >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Anonymous Event Submission', 'dbem' ); ?> (Beta)<?php echo $multisite_view; ?></span></h3>
			<div class="inside">
	            <table class="form-table">
	            	<tr><td colspan="2">
	            		<strong><?php _e('You can allow users to publicly submit events on your blog by using the [event_form] shortcode, and enabling anonymous submissions below.','dbem')?> (beta)</strong><br />
					</td></tr>
            		<?php
						em_options_radio_binary ( __( 'Allow anonymous event submissions?', 'dbem' ), 'dbem_events_anonymous_submissions', __( 'Would you like to allow users to submit bookings anonymously? If so, you can use the new [event_form] shortcode or <code>em_event_form()</code> template tag with this enabled.', 'dbem' ) );
		            	em_options_select ( __('Guest Default User', 'dbem'), 'dbem_events_anonymous_user', em_get_wp_users (), __( 'Events require a user to own them. In order to allow events to be submitted anonymously you need to assign that event a specific user. We recommend you create a "Anonymous" subscriber with a very good password and use that.', 'dbem' ) );
		            	em_options_textarea ( __( 'Success Message', 'dbem' ), 'dbem_events_anonymous_result_success', __( 'Anonymous submitters cannot see or modify their event once submitted. You can customize the success message they see here.', 'dbem' ).$events_placeholder_tip );
					?>
			        <?php echo $save_button; ?>
				</table>
			</div> <!-- . inside --> 
			</div> <!-- .postbox --> 
			
			<?php /*
			<div  class="postbox " >
			<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Debug Modes', 'dbem' ); ?> </span></h3>
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
			
			<?php do_action('em_options_page_footer'); ?>

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
?>