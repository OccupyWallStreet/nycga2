<?php

//
// Featured Post Type related functions.
//
add_action('admin_init', 'ci_add_post_meta');
add_action('save_post', 'ci_update_post_meta');



function ci_add_post_meta(){
	add_meta_box("ci_post_meta", __('Tutorial Details', CI_DOMAIN), "ci_add_post_meta_box", "post", "normal", "high");
}

function ci_update_post_meta(){
	global $post;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

	if (isset($_POST['post_type']) && $_POST['post_type'] == "post")
	{
		update_post_meta($post->ID, "ci_post_tutorial", (isset($_POST["ci_post_tutorial"]) ? $_POST["ci_post_tutorial"] : '') );
		update_post_meta($post->ID, "ci_post_demo_link", (isset($_POST["ci_post_demo_link"]) ? $_POST["ci_post_demo_link"] : '') );
		update_post_meta($post->ID, "ci_post_download_link", (isset($_POST["ci_post_download_link"]) ? $_POST["ci_post_download_link"] : '') );
		update_post_meta($post->ID, "ci_post_level", (isset($_POST["ci_post_level"]) ? $_POST["ci_post_level"] : '') );
		update_post_meta($post->ID, "ci_post_duration", (isset($_POST["ci_post_duration"]) ? $_POST["ci_post_duration"] : '') );
		update_post_meta($post->ID, "ci_post_description", (isset($_POST["ci_post_description"]) ? $_POST["ci_post_description"] : '') );
	}
}

function ci_add_post_meta_box(){
	global $post;
	$tutorial = get_post_meta($post->ID, 'ci_post_tutorial', true);
	$demolink = get_post_meta($post->ID, 'ci_post_demo_link', true);
	$downloadlink = get_post_meta($post->ID, 'ci_post_download_link', true);
	$level = get_post_meta($post->ID, 'ci_post_level', true);
	$duration = get_post_meta($post->ID, 'ci_post_duration', true);
	$description = get_post_meta($post->ID, 'ci_post_description', true);
	?>

	<p>
		<input id="ci_post_tutorial" type="checkbox" class="code" name="ci_post_tutorial" value="selected" <?php checked($tutorial, 'selected'); ?> />
		<label for="ci_post_tutorial"><?php _e('Is this a tutorial post?', CI_DOMAIN); ?></label>
	</p>
	<p>
		<label for="ci_post_description"><?php _e('Tutorial introduction', CI_DOMAIN); ?></label>
		<textarea id="ci_post_description" name="ci_post_description" rows="5" cols="80" style="width:99%"><?php echo $description; ?></textarea>	
	</p>

	<p>
		<label for="ci_post_duration"><?php _e('Tutorial duration', CI_DOMAIN); ?></label>
		<input id="ci_post_duration" type="text" class="code" name="ci_post_duration" value="<?php echo $duration; ?>" style="width:99%" />
	</p>

	<p>
		<label for="ci_post_level"><?php _e('Tutorial level of difficulty', CI_DOMAIN); ?></label>
		<select id="ci_post_level" name="ci_post_level">
			<option value="" <?php selected($level, ''); ?>></option>
			<option value="Very Easy" <?php selected($level, 'Very Easy'); ?>><?php _e('Very Easy', CI_DOMAIN); ?></option>
			<option value="Easy" <?php selected($level, 'Easy'); ?>><?php _e('Easy', CI_DOMAIN); ?></option>
			<option value="Normal" <?php selected($level, 'Normal'); ?>><?php _e('Normal', CI_DOMAIN); ?></option>
			<option value="Intermediate" <?php selected($level, 'Intermediate'); ?>><?php _e('Intermediate', CI_DOMAIN); ?></option>
			<option value="Hard" <?php selected($level, 'Hard'); ?>><?php _e('Hard', CI_DOMAIN); ?></option>
			<option value="Very Hard" <?php selected($level, 'Very Hard'); ?>><?php _e('Very Hard', CI_DOMAIN); ?></option>
			<option value="Guru" <?php selected($level, 'Guru'); ?>><?php _e('Guru', CI_DOMAIN); ?></option>
		</select>
	</p>

	<p>
		<label for="ci_post_demo_link"><?php _e('Tutorial demo link URL', CI_DOMAIN); ?></label>
		<input id="ci_post_demo_link" type="text" class="code" name="ci_post_demo_link" value="<?php echo $demolink; ?>" style="width:99%" />
	</p>
	
	<p>
		<label for="ci_post_download_link"><?php _e('Tutorial download link URL', CI_DOMAIN); ?></label>
		<input id="ci_post_download_link" type="text" class="code" name="ci_post_download_link" value="<?php echo $downloadlink; ?>" style="width:99%" />
	</p>
	
	<?php

}

?>