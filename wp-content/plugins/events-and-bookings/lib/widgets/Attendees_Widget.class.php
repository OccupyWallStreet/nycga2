<?php

class Eab_Attendees_Widget extends Eab_Widget {
    
    function __construct() {
	$widget_ops = array( 'description' => __('Display Attendees of an event', $this->translation_domain) );
        $control_ops = array( 'title' => __('Attendees', $this->translation_domain));
        
	parent::WP_Widget( 'incsub_event_attendees', __('Event Attendees', $this->translation_domain), $widget_ops, $control_ops );
    }
    
    function widget($args, $instance) {
	global $wpdb, $current_site, $post, $wiki_tree;
	
	extract($args);
	
	if ($post->post_type != 'incsub_event') {
	    return;
	}
	
	$options = $instance;
	$event = new Eab_EventModel($post);
	
	$title = apply_filters('widget_title', empty($instance['title']) ? __('Attendees', $this->translation_domain) : $instance['title'], $instance, $this->id_base);
	
	?>
        <?php if (is_single() && $event->has_bookings()) {?>
	<?php echo $before_widget; ?>
	<?php echo $before_title . $title . $after_title; ?>
            <div id="event-bookings">
                <div id="event-booking-yes">
                    <?php echo Eab_Template::get_bookings(Eab_EventModel::BOOKING_YES, $event); ?>
                </div>
                <div class="clear"></div>
                <div id="event-booking-maybe">
                    <?php echo Eab_Template::get_bookings(Eab_EventModel::BOOKING_MAYBE, $event); ?>
                </div>
            </div>
        <br />
        <?php echo $after_widget; ?>
        <?php } ?>
	<?php
    }
    
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
        $new_instance = wp_parse_args( (array) $new_instance, array( 'title' => __('Attendees', $this->translation_domain), 'hierarchical' => 'yes') );
        $instance['title'] = strip_tags($new_instance['title']);
	
        return $instance;
    }
    
    function form($instance) {
	$instance = wp_parse_args( (array) $instance, array( 'title' => __('Attendees', $this->translation_domain)));
        $options = array('title' => strip_tags($instance['title']));
	
	?>
	<div style="text-align:left">
            <label for="<?php echo $this->get_field_id('title'); ?>" style="line-height:35px;display:block;"><?php _e('Title', $this->translation_domain); ?>:<br />
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $options['title']; ?>" type="text" style="width:95%;" />
            </label>
	    <input type="hidden" name="eab-submit" id="eab-submit" value="attendees" />
	</div>
	<?php
    }
}
