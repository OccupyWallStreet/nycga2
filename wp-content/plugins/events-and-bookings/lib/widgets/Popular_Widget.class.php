<?php

class Eab_Popular_Widget extends Eab_Widget {
	
	private $_defaults;
    
    function __construct() {
    	$this->_defaults = array( 
			'title' => __('Most Popular', $this->translation_domain),
			'excerpt' => false,
			'thumbnail' => false,
			'limit' => 5,
		);
		$widget_ops = array( 'description' => __('Display List of Popular events', $this->translation_domain) );
        $control_ops = array( 'title' => __('Most Popular', $this->translation_domain));     
		parent::WP_Widget( 'incsub_event_popular', __('Most Popular Events', $this->translation_domain), $widget_ops, $control_ops );
    }
    
    function widget($args, $instance) {
		global $wpdb, $current_site, $post, $wiki_tree;
		
		extract($args);
		
		$options = wp_parse_args((array)$instance, $this->_defaults);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Most Popular', $this->translation_domain) : $instance['title'], $instance, $this->id_base);
		
		//$_events = get_posts('post_type=incsub_event&meta_key=incsub_event_attending_count&orderby=meta_value&order=DESC&numberposts=10');
		$_events = Eab_CollectionFactory::get_popular_events(array(
			'posts_per_page' => $options['limit'],
		));
		
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
					<?php if ($options['excerpt'] && $excerpt) { ?>
						<p><?php echo $excerpt; ?></p>
					<?php } ?>
				</li>
			    <?php
				}
			    ?>
			</ul>
	            </div>
	        <br />
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
	
        return $instance;
    }
    
    function form($instance) {
		$options = wp_parse_args((array)$instance, $this->_defaults);
        $options['title'] = strip_tags($instance['title']);	
	
	?>
	<div style="text-align:left">
            <label for="<?php echo $this->get_field_id('title'); ?>" style="line-height:35px;display:block;"><?php _e('Title', $this->translation_domain); ?>:<br />
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $options['title']; ?>" type="text" style="width:95%;" />
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
	</div>
	<?php
    }
}
