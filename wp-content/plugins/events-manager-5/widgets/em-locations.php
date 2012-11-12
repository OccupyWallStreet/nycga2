<?php
/**
 * @author marcus
 * Standard events list widget
 */
class EM_Locations_Widget extends WP_Widget {
	
	var $defaults = array();
	
    /** constructor */
    function EM_Locations_Widget() {
    	$this->defaults = array(
    		'title' => __('Event Locations','dbem'),
    		'scope' => 'future',
    		'order' => 'ASC',
    		'limit' => 5,
    		'format' => '#_LOCATIONLINK<ul><li>#_ADDRESS</li><li>#_TOWN</li></ul>',
    		'orderby' => 'event_start_date,event_start_time,location_name'
    	);
    	$this->em_orderby_options = array(
    		'event_start_date, event_start_time, location_name' => __('Event start date/time, location name','dbem'),
    		'location_name' => __('Location name','dbem')
    	);
    	$widget_ops = array('description' => __( "Display a list of event locations on Events Manager.", 'dbem') );
        parent::WP_Widget(false, $name = 'Event Locations', $widget_ops);	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {	
    	$instance = array_merge($this->defaults, $instance);

    	echo $args['before_widget'];
    	if( !empty($instance['title']) ){
		    echo $args['before_title'];
		    echo $instance['title'];
		    echo $args['after_title'];
    	}
	    
		$instance['owner'] = false;
		$locations = EM_Locations::get(apply_filters('em_widget_locations_get_args',$instance));
		echo "<ul>";
		$li_wrap = !preg_match('/^<li>/i', trim($instance['format']));
		if ( count($locations) > 0 ){
			foreach($locations as $location){
				if( $li_wrap ){
					echo '<li>'. $location->output($instance['format']) .'</li>';
				}else{
					echo $location->output($instance['format']);
				}
			}
		}else{
			echo '<li>'.__('No locations', 'dbem').'</li>';
		}
		echo "</ul>";               
		
	    echo $args['after_widget'];
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	//filter the new instance and replace blanks with defaults
    	foreach($this->defaults as $key => $value){
    		if( !isset($new_instance[$key]) ){
    			$new_instance[$key] = $value;
    		}
    	}
    	return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    	$instance = array_merge($this->defaults, $instance);
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'dbem'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Show number of locations','dbem'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo esc_attr($instance['limit']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('scope'); ?>"><?php _e('Scope of the locations','dbem'); ?>:</label><br/>
			<select id="<?php echo $this->get_field_id('scope'); ?>" name="<?php echo $this->get_field_name('scope'); ?>" >
				<?php foreach( em_get_scopes() as $key => $value) : ?>   
				<option value='<?php echo $key ?>' <?php echo ($key == get_option('dbem_events_page_scope')) ? "selected='selected'" : ''; ?>>
					<?php echo $value; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order By','dbem'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
				<?php  
					echo $this->em_orderby_options;
				?>
				<?php foreach($this->em_orderby_options as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['orderby']) && $key == $instance['orderby']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order of the locations','dbem'); ?>:</label><br/>
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>" >
				<option value="ASC" <?php echo ($instance['order'] == 'ASC') ? 'selected="selected"':''; ?>><?php _e('Ascending','dbem'); ?></option>
				<option value="DESC" <?php echo ($instance['order'] == 'DESC') ? 'selected="selected"':''; ?>><?php _e('Descending','dbem'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php _e('List item format','dbem'); ?>: </label>
			<textarea rows="5" cols="24" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>"><?php echo $instance['format']; ?></textarea>
		</p>
        <?php 
    }
}
add_action('widgets_init', create_function('', 'return register_widget("EM_Locations_Widget");'));
?>