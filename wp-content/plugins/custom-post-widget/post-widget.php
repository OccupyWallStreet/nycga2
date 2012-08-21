<?php

// First create the widget for the admin panel
class custom_post_widget extends WP_Widget {
	function custom_post_widget() {
		$widget_ops = array( 'description' => __( 'Displays custom post content in a widget', 'custom-post-widget' ) );
		$this->WP_Widget( 'custom_post_widget', __( 'Content Block', 'custom-post-widget' ), $widget_ops );
	}

	function form( $instance ) {
		$custom_post_id = esc_attr( $instance['custom_post_id'] );
		$show_custom_post_title  = isset( $instance['show_custom_post_title'] ) ? $instance['show_custom_post_title'] : true;
		$apply_content_filters  = isset( $instance['apply_content_filters'] ) ? $instance['apply_content_filters'] : true;
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'custom_post_id' ); ?>"> <?php echo __( 'Content Block to Display:', 'custom-post-widget' ) ?>
				<select class="widefat" id="<?php echo $this->get_field_id( 'custom_post_id' ); ?>" name="<?php echo $this->get_field_name( 'custom_post_id' ); ?>">
				<?php query_posts('post_type=content_block&orderby=ID&order=ASC&showposts=-1');
				if ( have_posts() ) : while ( have_posts() ) : the_post();
					$currentID = get_the_ID();
					if( $currentID == $custom_post_id )
						$extra = 'selected' and
						$widgetExtraTitle = get_the_title();
					else
						$extra = '';
						echo '<option value="'.$currentID.'" '.$extra.'>'.get_the_title().'</option>';
					endwhile; else:
					echo '<option value="empty">' . __( 'No content blocks available', 'custom-post-widget' ) . '</option>';
				endif; ?>
				</select>
			</label>
		</p>
		
		<input type="hidden" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $widgetExtraTitle; ?>" />

		<p>
			<?php
				echo '<a href="post.php?post=' . $custom_post_id . '&action=edit">' . __( 'Edit Content Block', 'custom-post-widget' ) . '</a>' ;
			?>
		</p>

				<?php wp_reset_query(); ?>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_custom_post_title'], true ); ?> id="<?php echo $this->get_field_id( 'show_custom_post_title' ); ?>" name="<?php echo $this->get_field_name( 'show_custom_post_title' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_custom_post_title' ); ?>"><?php echo __( 'Show Post Title', 'custom-post-widget' ) ?></label>
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['apply_content_filters'], true ); ?> id="<?php echo $this->get_field_id( 'apply_content_filters' ); ?>" name="<?php echo $this->get_field_name( 'apply_content_filters' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'apply_content_filters' ); ?>"><?php echo __( 'Do not apply content filters', 'custom-post-widget' ) ?></label>
		</p> <?php 
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['custom_post_id'] = strip_tags( $new_instance['custom_post_id'] );
		$instance['show_custom_post_title'] = $new_instance['show_custom_post_title'];
		$instance['apply_content_filters'] = $new_instance['apply_content_filters'];
		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$custom_post_id  = ( $instance['custom_post_id'] != '' ) ? esc_attr($instance['custom_post_id']) : __('Find', 'custom-post-widget');
		// Add support for WPML Plugin.
		if ( function_exists( 'icl_object_id' ) ){ 
			$custom_post_id = icl_object_id( $custom_post_id, 'content_block', true );
		}
		// Variables from the widget settings.
		$show_custom_post_title = isset( $instance['show_custom_post_title'] ) ? $instance['show_custom_post_title'] : false;
		$apply_content_filters  = isset($instance['apply_content_filters']) ? $instance['apply_content_filters'] : false;
		$content_post = get_post($custom_post_id);
		$content = $content_post->post_content;
		if ( !$apply_content_filters ) { // Don't apply the content filter if checkbox selected
			$content = apply_filters('the_content', $content);
		}
		echo $before_widget;
		if ( $show_custom_post_title ) {
			echo $before_title . apply_filters('widget_title',$content_post->post_title) . $after_title; // This is the line that displays the title (only if show title is set) 
		} 
		echo do_shortcode($content); // This is where the actual content of the custom post is being displayed
		echo $after_widget;
	}
	
}

// Create the Content Block custom post type
add_action('init', 'my_content_block_post_type_init');

function my_content_block_post_type_init() {
	$labels = array(
		'name' => _x('Content Blocks', 'post type general name', 'custom-post-widget'),
		'singular_name' => _x('Content Block', 'post type singular name', 'custom-post-widget'),
		'plural_name' => _x('Content Blocks', 'post type plural name', 'custom-post-widget'),
		'add_new' => _x('Add Content Block', 'block', 'custom-post-widget'),
		'add_new_item' => __('Add New Content Block', 'custom-post-widget'),
		'edit_item' => __('Edit Content Block', 'custom-post-widget'),
		'new_item' => __('New Content Block', 'custom-post-widget'),
		'view_item' => __('View Content Block', 'custom-post-widget'),
		'search_items' => __('Search Content Blocks', 'custom-post-widget'),
		'not_found' =>  __('No Content Blocks Found', 'custom-post-widget'),
		'not_found_in_trash' => __('No Content Blocks found in Trash', 'custom-post-widget'),
		'parent_item_colon' => ''
	);
	$options = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','revisions','thumbnail','author')
	);
	register_post_type('content_block',$options);
}

// Add custom styles to admin screen and menu
add_action('admin_head', 'content_block_header');

function content_block_header() {
	global $post_type; ?>
	<style type="text/css">
	<!--
	<?php if (($post_type == 'content_block')) : ?>
		#icon-edit { background:transparent url('<?php echo CUSTOM_POST_WIDGET_URL; ?>images/contentblock-32.png') no-repeat 0 0 !important;}
		#minor-publishing-actions { display:none; /* Hide the Save Draft and Preview buttons */}
	<?php endif; ?>
		#adminmenu #menu-posts-content_block div.wp-menu-image{background:transparent url('<?php echo CUSTOM_POST_WIDGET_URL;?>images/contentblock.png') no-repeat center -32px;}
		#adminmenu #menu-posts-content_block:hover div.wp-menu-image,#adminmenu #menu-posts-content_block.wp-has-current-submenu div.wp-menu-image{background:transparent url('<?php echo CUSTOM_POST_WIDGET_URL;?>images/contentblock.png') no-repeat center 0px;}
	-->
	</style>
<?php
}

add_filter('post_updated_messages', 'content_block_messages');

function content_block_messages( $messages ) {
	$messages['content_block'] = array(
		0 => '', 
		1 => current_user_can( 'edit_theme_options' ) ? sprintf( __('Content Block updated. <a href="%s">Manage Widgets</a>', 'custom-post-widget'), esc_url( 'widgets.php' ) ) : sprintf( __('Content Block updated.', 'custom-post-widget'), esc_url( 'widgets.php' ) ),
		2 => __('Custom field updated.', 'custom-post-widget'),
		3 => __('Custom field deleted.', 'custom-post-widget'),
		4 => __('Content Block updated.', 'custom-post-widget'),
		5 => isset($_GET['revision']) ? sprintf( __('Content Block restored to revision from %s', 'custom-post-widget'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => current_user_can( 'edit_theme_options' ) ? sprintf( __('Content Block published. <a href="%s">Manage Widgets</a>', 'custom-post-widget'), esc_url( 'widgets.php' ) ) : sprintf( __('Content Block published.', 'custom-post-widget'), esc_url( 'widgets.php' ) ),
		7 => __('Block saved.', 'custom-post-widget'),
		8 => current_user_can( 'edit_theme_options' ) ? sprintf( __('Content Block submitted. <a href="%s">Manage Widgets</a>', 'custom-post-widget'), esc_url( 'widgets.php' ) ) : sprintf( __('Content Block submitted.', 'custom-post-widget'), esc_url( 'widgets.php' ) ),
		9 => sprintf( __('Content Block scheduled for: <strong>%1$s</strong>.', 'custom-post-widget'), date_i18n( __( 'M j, Y @ G:i' , 'custom-post-widget'), strtotime(isset($post->post_date) ? $post->post_date : null) ), esc_url( 'widgets.php' ) ),
		10 => current_user_can( 'edit_theme_options' ) ? sprintf( __('Content Block draft updated. <a href="%s">Manage Widgets</a>', 'custom-post-widget'), esc_url( 'widgets.php' ) ) : sprintf( __('Content Block draft updated.', 'custom-post-widget'), esc_url( 'widgets.php' ) ),
	);
	return $messages;
}

// Add the ability to display the content block in a reqular post using a shortcode
function custom_post_widget_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '',
		'class' => 'content_block'
	), $atts));
	
	$content = "";
	
	if($id != "") {
		$args = array(
			'post__in' => array($id),
			'post_type' => 'content_block',
		);
		
		$content_post = get_posts($args);
		
		foreach( $content_post as $post ) :
			$content .= '<div class="'. esc_attr($class) .'">';
			$content .= apply_filters('the_content', $post->post_content);
			$content .= '</div>';
		endforeach;
	}
	
	return $content;
}
add_shortcode('content_block', 'custom_post_widget_shortcode');

// Add button above editor
function add_content_block_icon($initcontext) {
	return $initcontext.
	'<a id="add_content_block" style="text-decoration:none;" class="thickbox" title="' . __("Add Content Block", 'custom-post-widget') . '" href="' . CUSTOM_POST_WIDGET_URL . 'popup.php?type=add_content_block_popup&amp;TB_inline=true&amp;inlineId=content_block_form">
		<img onclick="return false;" alt="' . __("Add Content Block", 'custom-post-widget') . '" src="' . CUSTOM_POST_WIDGET_URL . 'images/contentblock-13.png">
	</a>';
}

add_filter('media_buttons_context', 'add_content_block_icon');

require_once( CUSTOM_POST_WIDGET_DIR . '/popup.php' );

if(!defined( 'CUSTOM_POST_WIDGET_CURRENT_PAGE' ))
	define( 'CUSTOM_POST_WIDGET_CURRENT_PAGE', basename($_SERVER['PHP_SELF']) );
if(in_array(CUSTOM_POST_WIDGET_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'))) {
	add_action('admin_footer', 'add_content_block_popup');
}
?>