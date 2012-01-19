<?php


function more_fields($key, $before = '', $after = '', $content_filter = false) {
	$value = get_meta($key);
	if (!$value) return false;
	echo $before;
	if ($content_filter) echo apply_filters('the_content', $value);
	else echo $value;
	echo $after;
	return true;
}


/*
**  get_box (  )
**
*/
function get_box  ($name, $args = array()) {
	global $more_fields;

    global $post;
    $html = '';
    $nl = "\n";

	// Get our fields
	$more_fields = $more_fields->get_objects(array('_plugin_saved', '_plugin'));

	// Parse args
	$defaults = array('format' => 'ul', 'div' => '', 'show' => '', 'title' => '');
	$args = wp_parse_args($args, $defaults);

	if (!is_array($more_fields)) return '<!-- No More Fields are defined -->';
	if (!array_key_exists($name, $more_fields)) return "<!-- The box name '$name' does not exist! -->";

	// Make sure we've got someting to display
	$something = false;
	foreach ((array) $more_fields[$name]['field'] as $field) 
		if (get_post_meta($post->ID, $field['key'], true)) $something = true;
	if (!$something) return "<!-- Nothing to display for '$name' -->";

	// Iterate through our meta fields and generat some html
	for ($i=0; $i < count($more_fields[$name]['field']); $i++) {
		$key = $more_fields[$name]['field'][$i]['key'];
		$title = $more_fields[$name]['field'][$i]['title'];		

		// Set up the list
		if ($i == 0) {
			if ($args['div']) $html .= '<div id="' . $args['div'] .'">' . $nl;
			if ($args['format']) {
				$caption = ($args['title']) ? ($args['title']) : $name;
				$html .= '<h3 class="meta_widget_header">' . $caption . '</h3>' . $nl;
				$html .= '<' . $args['format'] . '>' . $nl;
			}
		}
		// Does this field qualify for being shown?
		$show = false;
		if (is_array($args['show'])) {
			for ($k = 0; $k < count($args['show']); $k++) 
				if ($args['show'][$k] == $key)
					$show =  true;
		} else if (!$args['show'] || ($args['show'] == $key)) $show = true;

		$value = get_post_meta($post->ID, $key, true);

		if ($show && $value) {

			// Amost the same as 'the_content' filter
			$value = preg_replace("/\n/", "<br />", $value);
			$value = wptexturize($value);
			$value = convert_chars($value);
			
			$style_li = ' class="meta_' . $key . '_ul"';
			$style_dt = ' class="meta_' . $key . '_dt"';
			$style_dd = ' class="meta_' . $key . '_dd"';
			if ($args['format'] == 'ul') $html .= "<li ${style_li}>" . $value . '</li>' . $nl;
			else if ($args['format'] == 'dl') $html .= "<dt ${style_dt}>" . $title . "</dt><dd ${style_dd}>" . $value . '</dd>' . $nl;
			else if ($args['format'] == 'p') $html .= $value . $nl;
			else $html .= $value . $nl;
		}
		// Close the list and the optional div
		if ($i == count($more_fields[$name]['field']) - 1) {
			if ($args['format']) $html .= '</' . $args['format'] . '>' . $nl;
			if ($args['div']) $html .= '</div>' . $nl;
		}
	}
    echo $html;
}

/*
**   get_meta ( )
**
*/
function get_meta ($meta, $id = '') {	
	global $post;
	if ($id) $meta = get_post_meta($id, $meta, true);
	else {
		$id = (get_the_id()) ? get_the_id() : $post->ID;
		$meta = get_post_meta($id, $meta, true);
	}
	return $meta;
}
function meta ($meta, $id = '') { echo get_meta($meta, $id); }

/*
**		more_fields_img()
**
*/
function more_fields_img($meta, $before = '', $after = '', $options = array()) {

	$defaults = array('height' => 0, 'width' => 0, 'size' => '', 'crop' => false);	
	$options = wp_parse_args( $options, $defaults );

	if ( ! ( $id = get_meta($meta) ) ) return false;

	// If the image size does not exist, make it
	if ( !$options['size'] && ($options['height'] || $options['width'] ) ) {
		$size = 'mf_h' . $options['height'] . '_w' . $options['width'];
		add_image_size( $size, $options['width'], $options['height'], $options['resize'] );
		$file = wp_get_attachment_url($id);
		$file = str_replace(get_option('siteurl'), ABSPATH, $file);
		$a = image_make_intermediate_size( $file, $options['width'], $options['height'], $options['crop']);
		$as = explode('/', $file);
		$original_file = $as[count($as) - 1];				
		// $b = 	image_get_intermediate_size($id, $size); //{
		$new_file = $a['file'];
	}

	// Churn out some HTML
	$attr = array('class' => 'mf_image attachment-' . $id, 'id' => 'attachment-' . $id);
	$b = wp_get_attachment_image($id, $size, false, $attr);
	if ($new_file) $b = str_replace($original_file, $new_file, $b);
	echo $before. $b . $after;
	
	return true;

}


/*
**    more_fields_template_action ()
**
**    Remplate action to get content of a box.
*/
function more_fields_template_action ($title, $options = array()) {
	get_box($title, $options);
}

add_action('more_fields', 'more_fields_template_action', 10, 2);

/*
// I'm duplicating this function here - it's in the admin object too. 
function mf_get_boxes() {
	global $more_fields_boxes, $more_fields;

	$more_fields = $more_fields->get_data() ; //get_option('more_fields_boxes');

	if (!is_array($more_fields)) $more_fields = array();
	if (!is_array($more_fields_boxes)) $more_fields_boxes = array();
		
	foreach (array_keys($more_fields_boxes) as $key)
		$more_fields[$key] = $more_fields_boxes[$key];
		return $more_fields;
	}	
*/
?>