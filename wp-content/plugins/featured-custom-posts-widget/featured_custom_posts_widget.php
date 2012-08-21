<?
/*
Plugin Name: Featured Custom Posts Widget
Plugin URI: http://wordpress.org/extend/plugins/featured-custom-posts-widget/
Description: Widget that allows custom post types and taxonomies to be displayed.  Works well with Custom Post Type UI and Taxonomy Images plugins (displays image thumbnail using Taxonomy Images)
Author: Jason Rosewell
Version: 1.1.0
Author URI: http://linkhousemedia.com
*/

// Featured Custom Posts Widget
class featuredCustomPosts extends WP_Widget {
	
	function featuredCustomPosts() {
		parent::WP_Widget(false, 'Featured Custom Posts');
	}
	
	function form($instance) {
		$title = esc_attr($instance['title']);
		$featured_tag_name = esc_attr($instance['featured_tag_name']); 
		$num = esc_attr($instance['num']); 
		$post_type = esc_attr($instance['post_type']); 
		$taxonomy = esc_attr($instance['taxonomy']);
		$taxonomy_term = esc_attr($instance['taxonomy_term']);
		$permalink_base = esc_attr($instance['permalink_base']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('num'); ?>"><?php _e('Number of posts to display:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" type="text" value="<?php echo $num; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" type="text" value="<?php echo $post_type; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Custom Taxonomy:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" type="text" value="<?php echo $taxonomy; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('taxonomy_term'); ?>"><?php _e('Custom Taxonomy Term:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('taxonomy_term'); ?>" name="<?php echo $this->get_field_name('taxonomy_term'); ?>" type="text" value="<?php echo $taxonomy_term; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('permalink_base'); ?>"><?php _e('Permalink Base:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('permalink_base'); ?>" name="<?php echo $this->get_field_name('permalink_base'); ?>" type="text" value="<?php echo $permalink_base; ?>" /></label></p>
        <p><small><em>If using permalinks, the post name (slug) will be used in the url. Set the permalink base like so: example.com/permalink-base-here/post-name (using "/permalink-base-here"). Use "/" for example.com/post-name</em></small></p>
		<?php
	}
	
	function update($new_instance, $old_instance) {
			return $new_instance;
		}
	
	function widget($args, $instance) {
		$args['title'] = $instance['title'] ? $instance['title'] : 'Featured Posts';
		$args['num'] = $instance['num'] ? $instance['num'] : 10;
		$args['post_type'] = $instance['post_type'] ? $instance['post_type'] : 'post';
		$args['taxonomy'] = $instance['taxonomy'] ? $instance['taxonomy'] : '';
		$args['taxonomy_term'] = $instance['taxonomy_term'] ? $instance['taxonomy_term'] : '';
					
		echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title']; 
		echo '<ul class="featured-custom-posts">';
		
		if ( !empty($args['taxonomy']) ) {
			$tax_query = array(
				array(
					'taxonomy' => $args['taxonomy'],
					'field' => 'slug',
					'terms' => $args['taxonomy_term']
				)
			);
		}
		
		$args = array( 'post_type' => $args['post_type'], 'posts_per_page' => $args['num'], 'tax_query' => $tax_query);
		$loop = new WP_Query( $args );
		
		while ( $loop->have_posts() ) : $loop->the_post();
			
			$post = $loop->post;
			$meta = get_post_meta(get_the_ID(), false);
			$post_thumbnail_id = get_post_thumbnail_id( $post_id );
			$args['permalink_base'] = $instance['permalink_base'] ? $instance['permalink_base'] : '';
			$url = $args['permalink_base'] ? get_bloginfo('wpurl').$args['permalink_base']."/".$post->post_name : $post->guid;			
			
			//If using "Taxonomy Images" plugin
			$featured_image = ( function_exists('wp_get_attachment_image') && $post_thumbnail_id ) ? wp_get_attachment_image( $post_thumbnail_id, array(120,120) ) : $post->post_title;
			$text = $featured_image == '' ? $post->title : $featured_image;
			
			printf ( '	<li><a href="%s">%s</a></li>', $url, $text );				
		
		endwhile;

		echo '</ul>';
		echo $args['after_widget'];
	} //widget()
	
	
}
// Init function for registering the widget
function widget_fcp_init() {
  register_widget('featuredCustomPosts');
}
add_action('init', 'widget_fcp_init', 1);

?>