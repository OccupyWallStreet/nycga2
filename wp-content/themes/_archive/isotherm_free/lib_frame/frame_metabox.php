<?php 

// Custom fields for WP write panel
// Some parts of code used from Thesis class 'Head' @Chris Pearson & Custom Write Panel
// Plugin URI: http://wefunction.com/2008/10/tutorial-create-custom-write-panels-in-wordpress

//Custom Settings

function bizzthemes_custom_meta_box() {	bizzthemes_add_meta_box('custom'); }
function bizzthemes_seo_meta_box() { bizzthemes_add_meta_box('seo'); }
function bizzthemes_thumbnail_meta_box() { bizzthemes_add_meta_box('thumbnail'); }

function bizzthemes_meta_boxes($meta_name = false) {
	global $bizzthemes, $meta_boxes;

	if ($meta_name)
		return $meta_boxes[$meta_name];
	else
		return $meta_boxes;
}

function bizzthemes_add_meta_box($box_name) {
	global $post;
	
	?>
	<SCRIPT LANGUAGE="JavaScript">
		<!-- Begin
		function countChars(field,cntfield) {
		cntfield.value = field.value.length;
		}
		//  End -->
	</script>
	<?php
	
	// Grab this meta box item's information from the construct array
	$meta_box = bizzthemes_meta_boxes($box_name);
	
	// Spit out the actual form on the WordPress post page
	foreach ($meta_box['fields'] as $meta_id => $meta_field) {
		// Grab the existing value for this field from the database
		$existing_value = get_post_meta($post->ID, $meta_field['name'], true);
		$value = ($existing_value != '') ? $existing_value : $meta_field['default'];

		echo '<div id="' . $meta_id . '" class="bizz_metaboxes_table">' . "\n";
		
		if ($meta_field['title'])
			echo '<p class="title"><strong>' . $meta_field['title'] . '</strong></p>' . "\n";
		
		if ($meta_field['description']) {
			echo '<p class="description">' . $meta_field['description'] . '</p>' . "\n";
		} else {
			echo '';
		}
		
		if (is_array($meta_field['type'])) {
			if ($meta_field['type']['type'] == 'radio') {
				$options = $meta_field['type']['options'];
				$default = $meta_field['default'];

				echo '<ul class="opt_p">' . "\n";
				
				foreach ($options as $option_value => $label) {
					if ($existing_value)
						$checked = ($existing_value == $option_value) ? ' checked="checked"' : '';
					elseif ($option_value == $default)
						$checked = ' checked="checked"';
					else
						$checked = '';
						
					if ($option_value == $default)
						$option_value = '';

					echo '	<li><input type="radio" class="opt_input" name="' . $meta_field['name'] . '" value="' . $option_value . '"' . $checked .' /> <label>' . $label . '</label></li>' . "\n";
				}
				
				echo '</ul>' . "\n";
			}
			
		} elseif ($meta_field['type'] == 'text') {
			echo '<p>' . "\n";
			echo '	<input type="text" class="text_input" id="' . $meta_field['name'] . '" name="' . $meta_field['name'] . '" value="' . $value . '" />' . "\n";
			echo '	<label for="' . $meta_field['name'] . '">' . $meta_field['label'] . '</label>' . "\n";
			echo '</p>' . "\n";
		} elseif ($meta_field['type'] == 'text_counter') {
			echo '<p>' . "\n";
			echo '	<input type="text" class="text_input" id="' . $meta_field['name'] . '" name="' . $meta_field['name'] . '" value="' . $value . '" onKeyDown="countChars(document.post.' . $meta_field['name'] . ',document.post.lengthT)" onKeyUp="countChars(document.post.' . $meta_field['name'] . ',document.post.lengthT)" />' . "\n";
			echo '	<label for="' . $meta_field['name'] . '">' . $meta_field['label'] . '</label>' . "\n";
			echo '  <input readonly class="counter" type="text" name="lengthT" size="3" maxlength="3" value="'.strlen($value).'" />' . "\n";
			echo '  <span class="counter">' . $meta_field['counter_desc'] . '</span>' . "\n";
			echo '</p>' . "\n";
		} elseif ($meta_field['type'] == 'textarea') {
			echo '<p>' . "\n";
			echo '	<textarea id="' . $meta_field['name'] . '" name="' . $meta_field['name'] . '">' . $value . '</textarea>' . "\n";
			echo '	<label for="' . $meta_field['name'] . '">' . $meta_field['label'] . '</label>' . "\n";
			echo '</p>' . "\n";
		} elseif ($meta_field['type'] == 'textarea_counter') {
			echo '<p>' . "\n";
			echo '	<textarea id="' . $meta_field['name'] . '" name="' . $meta_field['name'] . '" onKeyDown="countChars(document.post.' . $meta_field['name'] . ',document.post.length1)" onKeyUp="countChars(document.post.' . $meta_field['name'] . ',document.post.length1)">' . $value . '</textarea>' . "\n";
			echo '	<label for="' . $meta_field['name'] . '">' . $meta_field['label'] . '</label>' . "\n";
			echo '  <input readonly class="counter" type="text" name="length1" size="3" maxlength="3" value="'.strlen($value).'" />' . "\n";
			echo '  <span class="counter">' . $meta_field['counter_desc'] . '</span>' . "\n";
			echo '</p>' . "\n";
		} elseif ($meta_field['type'] == 'checkbox') {
			$checked = ($value) ? ' checked="checked"' : '';		
			echo '<p class="chk_p">' . "\n";
			echo '	<label for="' . $meta_field['name'] . '">' . "\n";
			echo '  <input type="checkbox" class="chk_input" id="' . $meta_field['name'] . '" name="' . $meta_field['name'] . '" value="1"' . $checked . ' />' . "\n";
			echo '	' . $meta_field['label'] . '</label>' . "\n";
			echo '</p>' . "\n";
		} elseif($meta_field['type'] == 'upload') {
		    echo '<p>' . "\n";
			echo '	<input type="text" class="text_input" id="' . $meta_field['name'] . '" name="' . $meta_field['name'] . '" value="' . $value . '" />' . "\n";
			echo '	<label for="' . $meta_field['name'] . '">' . $meta_field['label'] . '</label>' . "\n";
			echo '  <input class="file_input" type="file" name="attachment_' . $meta_field['name'] . '" />' . "\n";
			echo '  <div class="clear"></div>'."\n";
			if ($existing_value <> '') {
			    echo '  <a href="'. $existing_value .'"><img class="feat" src="'.BIZZ_FRAME_ROOT.'/thumb.php?src='. $existing_value .'&w=75&h=75&zc=1" alt="" /></a>' . "\n";
			    echo '  <div class="preview_p">&rarr; Current Image</div>'."\n";
			}
			echo '  <div class="clear"></div>'."\n";
			echo '</p>' . "\n";
                
        }
		
		echo '</div>' . "\n";
	}
	
	echo '	<input type="hidden" name="' . $meta_box['noncename'] . '_noncename" id="' . $meta_box['noncename'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />' . "\n";
}

add_action('admin_menu', 'bizzthemes_add_meta_boxes');

function bizzthemes_save_meta($post_id) {
	
	$meta_boxes = bizzthemes_meta_boxes();
	$pID = $_POST['post_ID'];
	
	// We have to make sure all new data came from the proper bizzthemes entry fields
	foreach($meta_boxes as $meta_box) {
		if (!wp_verify_nonce($_POST[$meta_box['noncename'] . '_noncename'], plugin_basename(__FILE__)))
			return $post_id;
	}

	if ($_POST['post_type'] == 'page') {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
	} else {
		if (!current_user_can('edit_post', $post_id))
			return $post_id;
	}

	// If we reach this point in the code, that means we're authenticated. Proceed with saving the new data
	foreach ($meta_boxes as $meta_box) {

        foreach ($meta_box['fields'] as $meta_field) {
			$current_data = get_post_meta($post_id, $meta_field['name'], true);	
			$new_data = $_POST[$meta_field['name']]; 
					
            if($meta_field['type'] == 'upload') { //start upload
			    
				$override['action'] = 'editpost';
                if(!empty($_FILES['attachment_'.$meta_field['name']]['name'])) {
                    $uploaded_file = wp_handle_upload($_FILES['attachment_' . $meta_field['name'] ],$override); 
                    $uploaded_file['option_name']  = $meta_field['label'];
                    update_post_meta($post_id, $meta_field['name'], $uploaded_file['url']);

                } elseif(empty( $_FILES['attachment_'.$meta_field['name']]['name']) && isset($new_data)){
                    update_post_meta($post_id, $meta_field['name'], $new_data);
                } elseif($new_data == '') { 
					delete_post_meta($post_id, $meta_field['name'], $current_data);
                }
				
			} else { //other than upload
			
			    if ($current_data) {
				    if ($new_data == '')
					    delete_post_meta($post_id, $meta_field['name']);
				    elseif ($new_data == $meta_field['default'])
					    delete_post_meta($post_id, $meta_field['name']);
				    elseif ($new_data != $current_data)
					    update_post_meta($post_id, $meta_field['name'], $new_data);
			    } elseif ($new_data != '')
				    add_post_meta($post_id, $meta_field['name'], $new_data, true);
			
			} //end update/add/delete
		}
	}
}

function bizzthemes_add_meta_boxes() {
	$meta_boxes = bizzthemes_meta_boxes();
	
	foreach ($meta_boxes as $meta_box) {
		add_meta_box($meta_box['id'], $meta_box['title'], $meta_box['function'], 'post', 'normal', 'high');
		add_meta_box($meta_box['id'], $meta_box['title'], $meta_box['function'], 'page', 'normal', 'high');
	}
	
	add_action('edit_post', 'bizzthemes_save_meta'); // post meta data saved only once, including all post data, not just post ID
	//add_action('save_post', 'bizzthemes_save_meta');
}

?>