<?php
/*
Plugin Name: Convert Post Types
Plugin URI: http://sillybean.net/plugins/convert-post-types
Version: 1.1
Author: Stephanie Leary
Author URI: http://sillybean.net
Description: A bulk conversion utility for post types.
License: GPL2
*/

add_action('admin_menu', 'bulk_convert_posts_add_pages');

function bulk_convert_posts_add_pages() {
	$css = add_management_page(__('Convert Post Types', 'convert-post-types'), __('Convert Post Types', 'convert-post-types'), 'manage_options', __FILE__, 'bulk_convert_post_type_options');
	add_action("admin_head-$css", 'bulk_convert_post_type_css');
}

function bulk_convert_post_type_css() {
	echo '<style type="text/css">
		div.categorychecklistbox { float: left; margin: 1em 1em 1em 0; }
		ul.categorychecklist { height: 15em; width: 20em; overflow-y: scroll; border: 1px solid #dfdfdf; padding: 0 1em; background: #fff; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
		ul.categorychecklist ul.children { margin-left: 1em; }
		p.taginput { float: left; margin: 1em 1em 1em 0; width: 22em; }
		p.taginput input { width: 100%; }
		p.filters select { width: 24em; margin: 1em 1em 1em 0;  }
		p.submit { clear: both; }
	</style>';
}

function bulk_convert_post_type_options() {
	if ( current_user_can('edit_posts') && current_user_can('edit_pages') ) {  
		$hidden_field_name = 'bulk_convert_post_submit_hidden';
		if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
			bulk_convert_posts();
		    ?>
			<div class="updated"><p><strong><?php _e('Posts converted.', 'convert-post-types'); ?></strong></p></div>
		<?php } ?>
	
    <div class="wrap">
    <?php if( !isset($_POST[ $hidden_field_name ]) || $_POST[ $hidden_field_name ] != 'Y' ) { ?>
	<form method="post">
    <h2><?php _e( 'Convert Post Types', 'convert-post-types'); ?></h2>
	<p><?php _e('With great power comes great responsibility. This process could <strong>really</strong> screw up your database. Please <a href="http://www.ilfilosofo.com/blog/wp-db-backup">make a backup</a> before proceeding.', 'convert-post-types'); ?></p>
	<input type="hidden" name="<?php echo esc_attr($hidden_field_name); ?>" value="Y">
	<p class="filters">
	<?php
	$typeselect = '';
	if (isset($_POST['convert_cat'])) $convert_cat = $_POST['convert_cat']; else $convert_cat = '';
	$post_types = get_post_types(array('public'=>true));
	foreach ($post_types as $type) {
		$typeselect .= "<option value=\"" . esc_attr($type) . "\">";
		$typeselect .= esc_html($type);
		$typeselect .= "</option>";
	}
	?>
		<select name="post_type">
		<option value="-1"><?php _e("Convert from...", 'convert-post-types'); ?></option>
		<?php echo $typeselect; ?>
		</select>
		
		<select name="new_post_type">
		<option value="-1"><?php _e("Convert to...", 'convert-post-types'); ?></option>
		<?php echo $typeselect; ?>
		</select>
		
	<?php wp_dropdown_categories('name=convert_cat&show_option_none=Limit posts to category...&hide_empty=0&hierarchical=1&selected='.$convert_cat); ?>
	
	<?php wp_dropdown_pages('name=page_parent&show_option_none=Limit pages to children of...'); ?>
	
	</p>
	<?php global $wp_taxonomies; ?>
	<?php if ( is_array( $wp_taxonomies ) ) : ?>
	<h4><?php _e('Assign custom taxonomy terms', 'convert-post-types'); ?></h4>
			<?php foreach ( $wp_taxonomies as $tax ) :
			if (!in_array($tax->name, array('nav_menu', 'link_category', 'podcast_format'))) : ?>
				<?php 
				if (!is_taxonomy_hierarchical($tax->name)) :
				// non-hierarchical
					$nonhierarchical .= '<p class="taginput"><label>'.esc_html($tax->label).'<br />';
					$nonhierarchical .= '<input type="text" name="'.esc_attr($tax->name).'" class="widefloat" /></label></p>';
				else:
				// hierarchical 
				?>
				 	<div class="categorychecklistbox">
						<label><?php echo esc_html($tax->label); ?><br />
			        <ul class="categorychecklist">
			     	<?php
					wp_terms_checklist(0, array(
						           'descendants_and_self' => 0,
						           'selected_cats' => false,
						           'popular_cats' => false,
						           'walker' => null,
						           'taxonomy' => $tax->name,
						           'checked_ontop' => true,
						       )
						); 
				?>
				</ul>  </div>
				<?php
				endif;
			    ?>
			<?php
			endif;
			endforeach; 
			echo '<br class="clear" />'.$nonhierarchical;
			?>
	</div>
	<?php endif; ?>

	<p class="submit">
	<input type="submit" name="submit" value="<?php _e('Convert &raquo;', 'convert-post-types'); ?>" />
	</p>
	</form>
    <?php } // if ?>

    </div>
    
<?php } // if user can
} 

function bulk_convert_posts() {
	global $wpdb, $wp_taxonomies, $wp_rewrite;
	$q = 'numberposts=-1&post_status=any&post_type='.$_POST['post_type'];
	if (!empty($_POST['convert_cat'])) $q .= '&category='.$_POST['convert_cat'];
	if (!empty($_POST['page_parent'])) $q .= '&post_parent='.$_POST['page_parent'];
	
	$items = get_posts($q);
	foreach ($items as $item) {
		// Update the post into the database
		$wpdb->update( $wpdb->posts, array( 'post_type' => $_POST['new_post_type']), array( 'ID' => $item->ID, 'post_type' => $_POST['post_type']), array( '%s' ), array( '%d', '%s' ) );
//		$wpdb->print_error(); 
		echo '<p>'.__("Converted ", 'convert-post-types').$item->ID.": ".$item->post_title.'.</p>';		
		
		foreach ( $wp_taxonomies as $tax ) :
			// set new taxonomies
			if (!empty($_POST['tax_input'][$tax->name]))
			foreach ($_POST['tax_input'][$tax->name] as $taxid)  // hierarchical
			{	
				$term = get_term($taxid, $tax->name);
				wp_set_post_terms( $item->ID, $term->term_id, $tax->name, false );
				echo '<p style="text-indent: 1em;">'.__('Set ', 'convert-post-types').$tax->label.__("to", 'convert-post-types'). $term->name.'</p>';
			}
			if (!empty($_POST[$tax->name]))
			foreach ($_POST[$tax->name] as $taxid)
			{	$term = get_term($taxid, $tax->name);
				wp_set_post_terms( $item->ID, $term->name, $tax->name, false );
				echo '<p style="text-indent: 1em;">'.__('Set ', 'convert-post-types').$tax->label.__("to", 'convert-post-types'). $term->name.'</p>';
			}
		endforeach;
		
	}
	$wp_rewrite->flush_rules();
}

// i18n
$plugin_dir = basename(dirname(__FILE__)). '/languages';
load_plugin_textdomain( 'convert-post-types', WP_PLUGIN_DIR.'/'.$plugin_dir, $plugin_dir );
?>