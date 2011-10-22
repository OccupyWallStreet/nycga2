<?php
/**
 * Dynamic Content Widget class.
 *
 * @since 0.1
 * 
 * Copyright (C) 2011  Dikhoff Software
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 */

require_once("dcw-common.php");

class dcw_Dynamic_Content_Widget extends WP_Widget {

	/**
	 * Widget constructor.
	 */
	function dcw_Dynamic_Content_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Dynamic Content Widget', 'description' => __('A widget that renders content with a template.', 'dynamic content') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'dcw-dynamic-content-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'dcw-dynamic-content-widget', __('Dynamic Content Widget', 'dynamic content'), $widget_ops, $control_ops );
	}

	/**
	 * Display widget in area.
	 * @see WP_Widget::widget()
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );
		$dcw_slug = $instance['slug'];
		$dcw_template = $instance['subtemplate'];
		$dcw_id = $instance['id'];
		
		echo $before_widget;
		
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// if no id, try finding the content by the slug
		if (!$dcw_id) {
			if ($dcw_slug) {
				$rows = dcw_find_content_id($dcw_slug);
				$dcw_id = $rows[0]->ID;
				$instance['id'] = $dcw_id;
			}
		}
		if (!$dcw_id) {
			echo "No content found with id '$dcw_id' or identifier '$dcw_slug'.";
		}

		$content = new WP_Query();
		$content->query('p=' . $dcw_id . '&post_type=any');
		
		if (!$content->have_posts()) {
			echo "No content found with id '$dcw_id'.";
		}
		
		if ($dcw_template == '') {
			echo "Error: No subtemplate selected";
		}

		while ($content->have_posts()) {
			$content->the_post();
			
			get_template_part($dcw_template);
		}

		echo $after_widget;
	}
	
	/**
	 * Update fields.
	 * @see WP_Widget::update()
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['slug'] = strip_tags( $new_instance['slug'] );
		$instance['subtemplate'] = strip_tags( $new_instance['subtemplate'] );
		$instance['id'] = strip_tags( $new_instance['id'] );
		
		return $instance;
	}
	
	/**
	 * Display form.
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {
		$defaults = array( 'title' => __('Dynamic content', 'dynamic content'),
		                   'slug' => __('about', 'about'), 
		                   'subtemplate' => '',
		                   'id' => ''
		                   );
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
<script type="text/javascript">
	checkField('<?php echo $this->get_field_id( 'slug' ); ?>', '<?php echo $this->get_field_id( 'id' ); ?>');
</script>
<input id="<?php echo $this->get_field_id( 'id' ); ?>"
	type="hidden" 
	name="<?php echo $this->get_field_name( 'id' ); ?>"
	value="<?php echo $instance['id']; ?>" 
	/>
<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
<input id="<?php echo $this->get_field_id( 'title' ); ?>"
	name="<?php echo $this->get_field_name( 'title' ); ?>"
	value="<?php echo $instance['title']; ?>" style="width: 100%;" /></p>

<p><label for="<?php echo $this->get_field_id( 'slug' ); ?>"><?php _e('Slug or id:', 'slug'); ?></label>
<input id="<?php echo $this->get_field_id( 'slug' ); ?>"
	name="<?php echo $this->get_field_name( 'slug' ); ?>"
	value="<?php echo $instance['slug']; ?>" style="width: 275px;"
	onblur="checkField('<?php echo $this->get_field_id( 'slug' ); ?>', '<?php echo $this->get_field_id( 'id' ); ?>');"
	/><span style="float:right;padding:4px 0 0 0;" id="<?php echo $this->get_field_id( 'slug' ); ?>-result">&nbsp;</span>	
</p>
	<?php dcw_write_subtemplates($this, $instance); ?>
<?php
	}
} // class


/**
 * Register widgets.
 * @since 0.1
 */
function dcw_load_widget() {
	register_widget( 'dcw_Dynamic_Content_Widget' );
}

/**
 * Load CSS.
 */
function dcw_add_css() {
	global $pagenow;
	if (is_admin() && $pagenow == 'widgets.php') {
		$cssurl = plugins_url('/css/dynamic-content-widget.css', __FILE__ );
		wp_register_style("dcw_css", $cssurl);
		wp_enqueue_style("dcw_css");
	}
}

/**
 * Load scripts.
 */
function dcw_print_scripts() {
	global $pagenow;
	if (is_admin() && $pagenow == 'widgets.php') {
		$scripturl = plugins_url('/js/dynamic-content-widget.js', __FILE__ );
		wp_enqueue_script("dcw_scripts", $scripturl, Array("suggest"));
	}
}

add_action( 'widgets_init', 'dcw_load_widget' );

add_action( 'admin_print_scripts', 'dcw_print_scripts');
add_action( 'admin_init', 'dcw_add_css');

?>