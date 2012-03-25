<?php
//Add image upload metaboxes to Slider items


//	Define Metabox Fields
$prefix = 'tj_';

$meta_box_slider = array(
	'id' => 'tj-meta-box-slider',
	'title' =>  __('Slider Settings', 'themejunkie'),
	'page' => 'slider',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
	array(
			'name' =>  __('Slider Image ', 'themejunkie'),
			'desc' => __('Upload an image or enter an URL to your slide image', 'themejunkie'),
			'id' => $prefix.'slider_image',
			'type' => 'text',
			'std' => ''
		),
	array(
			'name' => '',
			'desc' => '',
			'id' => $prefix.'slider_image_button',
			'type' => 'button',
			'std' => 'Browse'
		),
    array( 'name' => __('Video Embed Code','themejunkie'),
				'desc' => __('Enter the video embed code for your video (YouTube, Vimeo or similar). Will show instead of your image.','themejunkie'),
				'id' => $prefix.'slider_video',
				'type' => 'textarea'
	    ),
    array( 'name' => __('URL','themejunkie'),
				'desc' => __('Enter URL if you want to add a link to the uploaded image and title. (optional) ','themejunkie'),
				'id' => $prefix.'slider_url',
				'type' => 'text'
		),
	),


);
add_action('admin_menu', 'tj_add_box_slider');
/*-----------------------------------------------------------------------------------*/
/*	Add metabox to edit page
/*-----------------------------------------------------------------------------------*/

function tj_add_box_slider() {
	global $meta_box_slider;

	add_meta_box($meta_box_slider['id'], $meta_box_slider['title'], 'tj_show_box_slider', $meta_box_slider['page'], $meta_box_slider['context'], $meta_box_slider['priority']);

}
/*-----------------------------------------------------------------------------------*/
/*	Callback function to show fields in meta box
/*-----------------------------------------------------------------------------------*/
function tj_show_box_slider() {
	global $meta_box_slider, $post;

    echo '<p style="padding:10px 0 0 0;">'.__('', 'themejunkie').'</p>';
    // Use nonce for verification
    echo '<input type="hidden" name="tj_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<table class="form-table">';

    foreach ($meta_box_slider['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        switch ($field['type']) {


            //If Text
            case 'text':

            echo '<tr style="border-top:1px solid #eeeeee;">',
                '<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style="line-height:20px; display:block; color:#999; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                '<td>';
            echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'],'" size="30" style="width:75%; margin-right: 20px; float:left;" />';

            break;

            //If textarea
            case 'textarea':

            echo '<tr style="border-top:1px solid #eeeeee;">',
                '<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style="line-height:18px; display:block; color:#999; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                '<td>';
            echo '<textarea name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" rows="8" cols="5" style="width:100%; margin-right: 20px; float:left;">', $meta ? $meta : $field['std'], '</textarea>';

            break;

            //If Button
            case 'button':
                echo '<input style="float: left;" type="button" class="button" name="', $field['id'], '" id="', $field['id'], '"value="', $meta ? $meta : $field['std'], '" />';
                echo 	'</td>',
            '</tr>';

            break;
        }
    }

    echo '</table>';

}

add_action('save_post', 'tj_save_data_slider');
/*-----------------------------------------------------------------------------------*/
/*	Save data when post is edited
/*-----------------------------------------------------------------------------------*/

function tj_save_data_slider($post_id) {
	global $meta_box_slider;

	// verify nonce
	if (!wp_verify_nonce($_POST['tj_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach ($meta_box_slider['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];

		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], stripslashes(htmlspecialchars($new)));
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}
 
