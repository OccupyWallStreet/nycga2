<?php

$contact_admin = new CONTACT_ADMIN();

class CONTACT_ADMIN{

	function CONTACT_ADMIN() {
		add_filter('screen_layout_columns', array( &$this, 'on_screen_layout_columns'), 10, 2 );
		if (is_multisite()){
			add_action('network_admin_menu', array( &$this, 'on_admin_menu') );
		}else{
			add_action('admin_menu', array( &$this, 'on_admin_menu') );
		}
	}

	function on_screen_layout_columns( $columns, $screen ) {
		if ( $screen == $this->pagehook ) {
			if (is_multisite()){
				$columns[ $this->pagehook ] = 1;
			}else{
				$columns[ $this->pagehook ] = 2;
			}
$columns[ $this->pagehook ] = 1;
		}
		return $columns;
	}
	
	function on_admin_menu() {
		$this->pagehook = add_submenu_page('bp-general-settings', __('Contact Tab', 'contact'), __('Contact Tab', 'contact'), 'manage_options', 'contact-admin', array( &$this, 'on_show_page') );
		add_action('load-'.$this->pagehook, array( &$this, 'on_load_page') );
	}
	
	//will be executed if wordpress core detects this page has to be rendered
	function on_load_page() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		if (is_multisite()){
			$position = 'normal';
			$priority = 'low';
		}else{
			$position = 'side';
			$priority = 'core';
		}

		// sidebar
		//add_meta_box('cd-ab-admin-debug', __('Debug', 'contact'), array(&$this, 'on_contact_admin_debug'), $this->pagehook, $position, $priority );
		// main content - normal
		add_meta_box('contact-admin-groups', __('Groups Management', 'contact'), array( &$this, 'on_contact_admin_groups'), $this->pagehook, 'normal', 'core');
	}

	function on_contact_admin_debug($contact){
		print_var($contact);
	}
	
	//executed to show the plugins complete admin page
	function on_show_page() {
		global $bp, $wpdb, $screen_layout_columns;
		
		//define some data can be given to each metabox during rendering
		$contact = get_option('contact');
		?>
		
		<div id="contact-admin-general" class="wrap">
			<?php screen_icon('options-general'); ?>
			<style>table.link-group li{margin:0 0 0 25px}</style>
			<h2><?php _e('Contact Tab','contact') ?> <sup><?php echo 'v' . contact_VERSION; ?></sup> &rarr; <?php _e('Extend Your Groups', 'contact') ?></h2>
		
			<?php 
			if ( isset($_POST['saveData']) ) {
				$contact['groups'] = $_POST['contact_groups'] ? $_POST['contact_groups'] : array();

				update_option('contact', $contact);

				echo "<div id='message' class='updated fade'><p>" . __('All changes were saved. Go and check results!', 'contact') . "</p></div>";
			}
			?>

			<form action="" id="contact-form" method="post">
				<?php 
				wp_nonce_field('contact-admin-general');
				wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
				wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			
				<div id="poststuff" class="metabox-holder<?php echo (2 == $screen_layout_columns) ? ' has-right-sidebar' : ''; ?>">
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes($this->pagehook, 'side', $contact); ?>
					</div>
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php do_meta_boxes($this->pagehook, 'normal', $contact); ?>
							<p>
								<input type="submit" value="<?php _e('Save Changes', 'contact') ?>" class="button-primary" name="saveData"/>	
							</p>
						</div>
					</div>
				</div>  
			</form>
		</div>
		<script type="text/javascript">
			jQuery(document).ready( function() {
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
		</script>
		
	<?php
	}
	
	function on_contact_admin_groups($contact){
		global $bp;
		?>
		<table id="bp-gtm-admin-table" class="widefat link-group">
			<thead>
				<tr class="header">
					<td colspan="2"><p><?php _e('Which groups do you allow to create custom fields?', 'contact') ?></p></td>
				</tr>
			</thead>
			<tbody id="the-list">
				<tr>
					<td><input type="checkbox" class="contact_allgroups" name="contact_groups" <?php echo ('all' == $contact['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></td>
					<td><?php _e('All groups', 'contact') ?></td>
				</tr>
				<?php
				$arg['type'] = 'alphabetical';
				$arg['per_page'] = '1000';
				if ( bp_has_groups($arg) ){
					while ( bp_groups() ) : bp_the_group();
						$description = preg_replace( array('<<p>>', '<</p>>', '<<br />>', '<<br>>'), '', bp_get_group_description_excerpt() );
						echo '<tr>
								<td><input name="contact_groups['.bp_get_group_id().']" class="contact_groups" type="checkbox" '.( ('all' == $contact['groups'] || in_array(bp_get_group_id(), $contact['groups']) ) ? 'checked="checked" ' : '').'value="'.bp_get_group_id().'" /></td>
								<td><a href="'.bp_get_group_permalink().'" target="_blank">'. bp_get_group_name() .'</a> &rarr; '.$description.'</td>
							</tr>';
					endwhile;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="header">
					<td><input type="checkbox" class="contact_allgroups" name="contact_groups" <?php echo ('all' == $contact['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></td>
					<td><?php _e('All groups', 'contact') ?></td>
					<td>&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	<?php
	}
	
}
