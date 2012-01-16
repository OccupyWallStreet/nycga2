<?php

$info_tab_plus_admin = new info_tab_plus_ADMIN();

class info_tab_plus_ADMIN{

	function info_tab_plus_ADMIN() {
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
		$this->pagehook = add_submenu_page('bp-general-settings', __('Contact Tab', 'info'), __('Contact Tab', 'info'), 'manage_options', 'contact-admin', array( &$this, 'on_show_page') );
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
		//add_meta_box('cd-ab-admin-debug', __('Debug', 'info'), array(&$this, 'on_info_tab_plus_admin_debug'), $this->pagehook, $position, $priority );
		// main content - normal
		add_meta_box('contact-admin-groups', __('Groups Management', 'info'), array( &$this, 'on_info_tab_plus_admin_groups'), $this->pagehook, 'normal', 'core');
	}

	function on_info_tab_plus_admin_debug($info_tab){
		print_var($info_tab);
	}
	
	//executed to show the plugins complete admin page
	function on_show_page() {
		global $bp, $wpdb, $screen_layout_columns;
		
		//define some data can be given to each metabox during rendering
		$info_tab = get_option('info');
		?>
		
		<div id="contact-admin-general" class="wrap">
			<?php screen_icon('options-general'); ?>
			<style>table.link-group li{margin:0 0 0 25px}</style>
			<h2><?php _e('Contact Tab','info') ?> <sup><?php echo 'v' . info_tab_plus_VERSION; ?></sup> &rarr; <?php _e('Extend Your Groups', 'info') ?></h2>
		
			<?php 
			if ( isset($_POST['saveData']) ) {
				$info_tab['groups'] = $_POST['info_tab_plus_groups'] ? $_POST['info_tab_plus_groups'] : array();

				update_option('info', $info_tab);

				echo "<div id='message' class='updated fade'><p>" . __('All changes were saved. Go and check results!', 'info') . "</p></div>";
			}
			?>

			<form action="" id="contact-form" method="post">
				<?php 
				wp_nonce_field('contact-admin-general');
				wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
				wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			
				<div id="poststuff" class="metabox-holder<?php echo (2 == $screen_layout_columns) ? ' has-right-sidebar' : ''; ?>">
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes($this->pagehook, 'side', $info_tab); ?>
					</div>
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php do_meta_boxes($this->pagehook, 'normal', $info_tab); ?>
							<p>
								<input type="submit" value="<?php _e('Save Changes', 'info') ?>" class="button-primary" name="saveData"/>	
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
	
	function on_info_tab_plus_admin_groups($info_tab){
		global $bp;
		?>
		<table id="bp-gtm-admin-table" class="widefat link-group">
			<thead>
				<tr class="header">
					<td colspan="2"><p><?php _e('Which groups do you allow to create custom fields?', 'info') ?></p></td>
				</tr>
			</thead>
			<tbody id="the-list">
				<tr>
					<td><input type="checkbox" class="info_tab_plus_allgroups" name="info_tab_plus_groups" <?php echo ('all' == $info_tab['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></td>
					<td><?php _e('All groups', 'info') ?></td>
				</tr>
				<?php
				$arg['type'] = 'alphabetical';
				$arg['per_page'] = '1000';
				if ( bp_has_groups($arg) ){
					while ( bp_groups() ) : bp_the_group();
						$description = preg_replace( array('<<p>>', '<</p>>', '<<br />>', '<<br>>'), '', bp_get_group_description_excerpt() );
						echo '<tr>
								<td><input name="info_tab_plus_groups['.bp_get_group_id().']" class="info_tab_plus_groups" type="checkbox" '.( ('all' == $info_tab['groups'] || in_array(bp_get_group_id(), $info_tab['groups']) ) ? 'checked="checked" ' : '').'value="'.bp_get_group_id().'" /></td>
								<td><a href="'.bp_get_group_permalink().'" target="_blank">'. bp_get_group_name() .'</a> &rarr; '.$description.'</td>
							</tr>';
					endwhile;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="header">
					<td><input type="checkbox" class="info_tab_plus_allgroups" name="info_tab_plus_groups" <?php echo ('all' == $info_tab['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></td>
					<td><?php _e('All groups', 'info') ?></td>
					<td>&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	<?php
	}
	
}
