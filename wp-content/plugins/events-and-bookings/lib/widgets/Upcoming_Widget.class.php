<?php

class Eab_Upcoming_Widget extends Eab_Widget {
	
	private $_defaults = array();
    
    function __construct() {
    	$this->_defaults = array( 
			'title' => __('Upcoming', $this->translation_domain),
			'excerpt' => false,
			'thumbnail' => false,
			'limit' => 5,
			'dates' => false,
		);
		$widget_ops = array('description' => __('Display List of Upcoming Events', $this->translation_domain));
        $control_ops = array('title' => __('Upcoming', $this->translation_domain));        
		parent::WP_Widget( 'incsub_event_upcoming', __('Upcoming Events', $this->translation_domain), $widget_ops, $control_ops );
    }
    
    function widget($args, $instance) {
		global $wpdb, $current_site, $post, $wiki_tree;
		
		extract($args);
		
		$options = wp_parse_args((array)$instance, $this->_defaults);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Upcoming', $this->translation_domain) : $instance['title'], $instance, $this->id_base);
		$query_args = array(
			'posts_per_page' => $options['limit'],
		);
		if ($options['category']) {
			$query_args['tax_query'] = array(array(
				'taxonomy' => 'eab_events_category',
				'field' => 'id',
				'terms' => (int)$options['category'],
			));
		}
		$_events = Eab_CollectionFactory::get_upcoming_weeks_events(time(), $query_args);
	
		if (is_array($_events) && count($_events) > 0) {
		?>
		<?php echo $before_widget; ?>
		<?php echo $before_title . $title . $after_title; ?>
	            <div id="event-popular">
			<ul>
			    <?php
				foreach ($_events as $_event) {
					$thumbnail = $excerpt = false;
					if ($options['thumbnail']) {
						$raw = wp_get_attachment_image_src(get_post_thumbnail_id($_event->get_id()));
						$thumbnail = $raw ? @$raw[0] : false;
					}
					if ($options['excerpt']) {
						$excerpt = $_event->get_excerpt() ? $_event->get_excerpt() : substr(strip_tags($_event->get_content()), 0, 250);
					}
			    ?>
					<li>
						<a href="<?php print get_permalink($_event->get_id()); ?>" class="<?php print ($_event->get_id() == $post->ID)?'current':''; ?>" >
							<?php if ($options['thumbnail'] && $thumbnail) { ?>
								<img src="<?php echo $thumbnail; ?>" /><br />
							<?php } ?>
							<?php print $_event->get_title(); ?>
						</a>
						<?php if ($options['dates']) { ?>
							<div class="wpmudevevents-date">
								<?php echo Eab_Template::get_event_dates($_event); ?>
							</div>
						<?php } ?>
						<?php if ($options['excerpt'] && $excerpt) { ?>
							<p><?php echo $excerpt; ?></p>
						<?php } ?>
					</li>
			    <?php
				}
			    ?>
			</ul>
	            </div>
	        <?php echo $after_widget; ?>
		<?php
		}
    }
    
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
        $new_instance = wp_parse_args((array)$new_instance, $this->_defaults);
        
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['excerpt'] = (int)$new_instance['excerpt'];
        $instance['thumbnail'] = (int)$new_instance['thumbnail'];
        $instance['limit'] = (int)$new_instance['limit'];
        $instance['dates'] = (int)$new_instance['dates'];
        $instance['category'] = (int)$new_instance['category'];
	
        return $instance;
    }
    
    function form($instance) {
		$options = wp_parse_args((array)$instance, $this->_defaults);
        $options['title'] = strip_tags($instance['title']);	
		
		$categories = get_terms('eab_events_category');
	?>
	<div style="text-align:left">
            <label for="<?php echo $this->get_field_id('title'); ?>" style="line-height:35px;display:block;">
            	<?php _e('Title', $this->translation_domain); ?>:<br />
				<input class="widefat" 
					id="<?php echo $this->get_field_id('title'); ?>" 
					name="<?php echo $this->get_field_name('title'); ?>" 
					value="<?php echo $options['title']; ?>" type="text" style="width:95%;" 
				/>
            </label>
            <label for="<?php echo $this->get_field_id('dates'); ?>" style="display:block;">
				<input type="checkbox" 
					id="<?php echo $this->get_field_id('dates'); ?>" 
					name="<?php echo $this->get_field_name('dates'); ?>" 
					value="1" <?php echo ($options['dates'] ? 'checked="checked"' : ''); ?> 
				/>
            	<?php _e('Show dates', $this->translation_domain); ?>
            </label>
            <label for="<?php echo $this->get_field_id('excerpt'); ?>" style="display:block;">
				<input type="checkbox" 
					id="<?php echo $this->get_field_id('excerpt'); ?>" 
					name="<?php echo $this->get_field_name('excerpt'); ?>" 
					value="1" <?php echo ($options['excerpt'] ? 'checked="checked"' : ''); ?> 
				/>
            	<?php _e('Show excerpt', $this->translation_domain); ?>
            </label>
            <label for="<?php echo $this->get_field_id('thumbnail'); ?>" style="display:block;">
				<input type="checkbox" 
					id="<?php echo $this->get_field_id('thumbnail'); ?>" 
					name="<?php echo $this->get_field_name('thumbnail'); ?>" 
					value="1" <?php echo ($options['thumbnail'] ? 'checked="checked"' : ''); ?> 
				/>
            	<?php _e('Show thumbnail', $this->translation_domain); ?>
           </label>
           <label for="<?php echo $this->get_field_id('limit'); ?>" style="line-height:35px;display:block;">
            	<?php _e('Limit', $this->translation_domain); ?>:
				<select id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>">
					<?php for ($i=1; $i<=10; $i++) { ?>
						<?php $selected = ($i == $options['limit']) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo $i; ?>" <?php echo $selected;?>><?php echo $i;?></option>
					<?php } ?>
				</select> 
           </label>
           <label for="<?php echo $this->get_field_id('category'); ?>" style="line-height:35px;display:block;">
            	<?php _e('Only Events from this category', $this->translation_domain); ?>:
				<select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
					<option><?php _e('Any', $this->translation_domain);?></option>
					<?php foreach ($categories as $category) { ?>
						<?php $selected = ($category->term_id == $options['category']) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo $category->term_id; ?>" <?php echo $selected;?>><?php echo $category->name;?></option>
					<?php } ?>
				</select> 
           </label>
	</div>
	<?php
    }
}
