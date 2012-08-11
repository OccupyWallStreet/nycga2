<?php
/************************** showcase custom post types ************************/
add_action('init', 'gallery_register');

function gallery_register() {
	$labels = array(
	    'name' => _x('Exhibitions', 'exhibition'),
	    'singular_name' => _x('Exhibitions', 'exhibition'),
	    'add_new' => _x('Add Exhibition', 'exhibition'),
	    'add_new_item' => __('Add New Exhibition - Add title and a gallery to your exhibition'),
	    'edit_item' => __('Edit Exhibition'),
	    'new_item' => __('New Exhibition'),
	    'view_item' => __('View Exhibition'),
	    'search_items' => __('Search Exhibition'),
	    'not_found' =>  __('No Exhibition found'),
	    'not_found_in_trash' => __('No exhibitions found in Trash'), 
	    'parent_item_colon' => '',
	    'menu_name' => 'Exhibitions'

	  );
	
	$args = array(
    	'labels' => $labels,
		'public' => true,
		'show_ui' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
	    'capability_type' => 'post',
	    'hierarchical' => false,
		'rewrite' => array( 'slug' => 'exhibition'),
		'menu_position' => 200,
		'show_in_menu' => 'gallery-theme',
		'show_in_nav_menus' => true,
    	'supports' => array('title', 'editor')
    );

	register_post_type( 'exhibition' , $args );
	
}

function add_exhibition(){
	add_meta_box("exhibition_help", __("Help", 'gallery'), "exhibition_help", "exhibition", "side", "high");
 }

function exhibition_help(){
	 ?>
	<p>
		<?php _e("Here you can set up your exhibition and add a gallery to it.  The gallery template is automatically set up.", 'gallery'); ?>
	</p>
	<br />
<a class="button" target="_blank" href="admin.php?page=step-functions.php">
	<?php _e("Step by step guides", 'gallery'); ?>
	</a><br /><br /><br />
<a class="button" target="_blank" href="admin.php?page=gallery-theme">
	<?php _e("Getting started guide", 'gallery'); ?>
	</a><br /><br /><br />
	<?php
}

add_action("admin_init", "add_exhibition");

function add_menuhelp(){
	add_meta_box("menu_help", __("Help", 'gallery'), "menu_help", "nav-menus", "side", "high");
 }

function menu_help(){
		 ?>
		<p>
			<?php _e("Here you can create a new menu and add exhibition items to that menu.  <br />* Should you not see Exhibitions listed turn them on under Screen options to the right of this page", 'gallery'); ?>
		</p>
		<br />
	<a class="button" target="_blank" href="admin.php?page=step-functions.php">
		<?php _e("Step by step guides", 'gallery'); ?>
		</a><br /><br /><br />
	<a class="button" target="_blank" href="admin.php?page=gallery-theme">
		<?php _e("Getting started guide", 'gallery'); ?>
		</a><br /><br /><br />
		<?php
}

add_action("admin_init", "add_menuhelp");


add_filter("manage_edit-exhibition_columns", "exhibition_edit_columns");
add_action("manage_posts_exhibition_column",  "exhibition_custom_columns");

function exhibition_edit_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => __("Exhibition name", 'gallery'),
			
		);

		return $columns;
}

function exhibition_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "description":
				$custom = get_post_custom();
				echo $custom["gallerycaption"][0];
				break;
					case "gallerycat":
						echo get_the_term_list($post->ID, 'gallerycat', '', ', ','');
						break;
		}
}

 function my_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', get_bloginfo('template_url') . '/library/scripts/myupload.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
	}
	
function my_admin_styles() {
wp_enqueue_style('thickbox');
}
add_action('admin_print_scripts', 'my_admin_scripts');
add_action('admin_print_styles', 'my_admin_styles');

add_action( 'admin_head', 'my_custom_posttype_icon' );
function my_custom_posttype_icon() {
    ?>
    <style type="text/css" media="screen">
    /*<![CDATA[*/
        #toplevel_page_gallery-theme .wp-menu-image, #menu-posts-slideshow .wp-menu-image, #menu-posts-exhibition .wp-menu-image{
            background: url(<?php bloginfo('template_url') ?>/library/styles/images/portfolio-icon.png) no-repeat 6px !important;
        }
		
		#icon-edit.icon32-posts-exhibition, #icon-gallery-theme.icon32-posts-exhibition, #icon-edit.icon32-posts-slideshow, #icon-gallery-theme.icon32-posts-slideshow {background: url(<?php bloginfo('template_url') ?>/library/styles/images/exhibitions_large.png) no-repeat;}
    /*]]>*/
    </style>
    <?php
}

/******************* end gallery custom post types ******************************/
?>