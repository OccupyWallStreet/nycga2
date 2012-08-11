<?php

class WPV_Widget extends WP_Widget{
    
    function WPV_Widget(){
        $widget_ops = array('classname' => 'widget_wp_views', 'description' => __( 'Displays a View', 'wpv-views') );
        $this->WP_Widget('wp_views', __('WP Views', 'wpv-views'), $widget_ops);
    }
    
    function widget( $args, $instance ) {
        global $WP_Views;
        extract($args);
        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		$WP_Views->set_widget_view_id($instance['view']);
		
        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

        $out = $WP_Views->render_view_ex($instance['view'], $instance['view']);
        $out = wpv_do_shortcode($out);
        
    	$post_type_object = get_post_type_object( 'view' );
    	if ( current_user_can( $post_type_object->cap->edit_post, $instance['view'] ) ) {
            $out .= widget_view_link( $instance['view']);
        }
        
        echo $out;

        echo $after_widget;

		$WP_Views->set_widget_view_id(0);
    }
    
    function form( $instance ) {
        global $WP_Views;
        $views = $WP_Views->get_views();        
        $instance = wp_parse_args( (array) $instance, 
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $title = $instance['title'];
        $view  = $instance['view'];
         ?>
        
        <?php if($views): ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
        
            <p style="float: right;">
            <?php _e('View:', 'wpv-views'); ?> <select name="<?php echo $this->get_field_name('view'); ?>">
            <?php foreach($views as $v): ?>
				<option value="<?php echo $v->ID ?>"<?php if($view == $v->ID): ?> selected="selected"<?php endif;?>><?php echo esc_html($v->post_title) ?></option>
            <?php endforeach;?>             
            </select>
            </p>

            <br clear="all">
        <?php else: ?>
            <?php
                if (!$WP_Views->is_embedded()) {
                    printf(__('No views defined. You can add them <a%s>here</a>.'), ' href="' . admin_url('edit.php?post_type=view'). '"');
                }
            ?>
        <?php endif;?>
        <?php
    }
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $new_instance = wp_parse_args((array) $new_instance, 
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['view']  = $new_instance['view'];
        
        return $instance;
    }
    
}

/**
 * class WPV_Widget_filter
 *
 * Displays only the filter section of a View
 * Can be used for a search
 *
 */

class WPV_Widget_filter extends WP_Widget{
    
    function WPV_Widget_filter(){
        $widget_ops = array('classname' => 'widget_wp_views_filter',
							'description' => __( 'Displays the filter section of a View.', 'wpv-views') 
							);
        $this->WP_Widget('wp_views_filter', __('WP Views Filter', 'wpv-views'), $widget_ops);
    }
    
    function widget( $args, $instance ) {
        global $WP_Views;
        extract($args);
        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		$WP_Views->set_widget_view_id($instance['view']);
		
        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

		$atts = array();
		$atts['id'] = $instance['view'];
		$atts['target_id'] = $instance['target_id'];
        $out = $WP_Views->short_tag_wpv_view_form($atts);
        $out = wpv_do_shortcode($out);
        
    	$post_type_object = get_post_type_object( 'view' );
    	if ( current_user_can( $post_type_object->cap->edit_post, $instance['view'] ) ) {
            $out .= widget_view_link($instance['view']);
        }
        
        echo $out;

        echo $after_widget;

		$WP_Views->set_widget_view_id(0);
    }
    
    function form( $instance ) {
        global $WP_Views, $wpdb;
        $views = $WP_Views->get_views();        

        $posts = $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_content LIKE '%[wpv-view%' AND post_type NOT IN ('revision')");

        $instance = wp_parse_args( (array) $instance, 
            array( 
                'title' => '',
                'view'  => false,
				'target_id' => '0'
            ) 
        );
        $title = $instance['title'];
        $view  = $instance['view'];
		$target_id = $instance['target_id'];
         ?>
        
        <?php if($views): ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
        
            <table width="100%">
				<tr>
					<td>
						<?php _e('View:', 'wpv-views'); ?>
					</td>
					<td>
						<select name="<?php echo $this->get_field_name('view'); ?>" style="width:100%">
						<?php foreach($views as $v): ?>
							<option value="<?php echo $v->ID ?>"<?php if($view == $v->ID): ?> selected="selected"<?php endif;?>><?php echo esc_html($v->post_title) ?></option>
						<?php endforeach;?>             
						</select>
					</td>
				</tr>

				<tr>
					<td>
						<?php _e('Target page:', 'wpv-views'); ?>
					</td>
					<td>
						<select name="<?php echo $this->get_field_name('target_id'); ?>" style="width:100%">
						<?php foreach($posts as $post): ?>
							<option value="<?php echo $post->ID ?>"<?php if($target_id == $post->ID): ?> selected="selected"<?php endif;?>><?php echo esc_html($post->post_title) ?></option>
						<?php endforeach;?>             
						</select>
					</td>
				</tr>
            </table>

            <br clear="all">

        <?php else: ?>
            <?php
                if (!$WP_Views->is_embedded()) {
                    printf(__('No views defined. You can add them <a%s>here</a>.'), ' href="' . admin_url('edit.php?post_type=view'). '"');
                }
            ?>
        <?php endif;?>
        <?php
    }
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $new_instance = wp_parse_args((array) $new_instance, 
            array( 
                'title' => '',
                'view'  => false,
				'target_id' => '0'
            ) 
        );
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['view']  = $new_instance['view'];
        $instance['target_id']  = $new_instance['target_id'];
        
        return $instance;
    }
    
}
  

function widget_view_link($view_id) {
	
	global $WP_Views;

	remove_filter('edit_post_link', array($WP_Views, 'edit_post_link'), 10, 2);
		
	ob_start();
		
	edit_post_link(__('Edit view', 'wpv-views'), '', '', $view_id);
		
	$link = ob_get_clean();
		
	add_filter('edit_post_link', array($WP_Views, 'edit_post_link'), 10, 2);
	
	return $link;
}

  
?>
