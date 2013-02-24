<?php 
	function cc_post_metabox(){ 	
		$option_post_templates = array();
        
	   	$cc_post_options=cc_get_post_meta();

		if(isset($cc_post_options['cc_post_template_on']) && $cc_post_options['cc_post_template_on'] == 1){
			$checked_post_template = "checked";
		} else {
			$checked_post_template = "";
		}
		if(isset($cc_post_options['cc_post_template_avatar']) && $cc_post_options['cc_post_template_avatar'] == 1){
			$checked_post_template_avatar = "checked";
		} else {
			$checked_post_template_avatar = "";
		}
		if(isset($cc_post_options['cc_post_template_date']) && $cc_post_options['cc_post_template_date'] == 1){
			$checked_post_template_date = "checked";
		} else {
			$checked_post_template_date = "";
		}
		if(isset($cc_post_options['cc_post_template_tags']) && $cc_post_options['cc_post_template_tags'] == 1){
			$checked_post_template_tags = "checked";
		} else {
			$checked_post_template_tags = "";
		}
		if(isset($cc_post_options['cc_post_template_comments_info']) && $cc_post_options['cc_post_template_comments_info'] == 1){
			$checked_post_template_comments_info = "checked";
		} else {
			$checked_post_template_comments_info = "";
		}

		$option_post_templates[0] = "img-left-content-right";
		$option_post_templates[1] = "img-right-content-left";
		$option_post_templates[2] = "img-over-content";
		$option_post_templates[3] = "img-under-content";
		
		?>
		
	<div id="cc_page_metabox" class="postbox">
		<div class="handlediv" title="<?php _e('click','cc'); ?>">
			<br />
		</div>
		<h3 class="hndle"><?php _e('Custom Community settings','cc')?></h3>
		<div class="inside">
			<b><?php _e('Use a post template for this post', 'cc'); ?></b>
			<p><?php _e('You can select a predefined post template', 'cc'); ?>:<br />
				<label for="cc_post_template"><?php _e('Post template on','cc')?>:</label>
				<input name="cc_post_template_on" id="cc_post_template_on" type="checkbox" <?php echo $checked_post_template ?> value="1" />
				<?php _e('Select a template to use', 'cc'); ?>:<select id="cc_post_template_type" name="cc_post_template_type">
						<?php foreach($option_post_templates as $option_template){?>
							<option <?php if($cc_post_options['cc_post_template_type'] == $option_template){?>selected="selected"<?php }?>><?php echo $option_template; ?></option>
						<?php }?>
				</select>
			</p>
		<b><?php _e('Show/hide meta info', 'cc'); ?></b>
		<p>
		<label for="cc_post_templater"><?php _e('Hide avatar','cc')?>:</label>
		<input name="cc_post_template_avatar" id="cc_post_template_avatar" type="checkbox" <?php echo $checked_post_template_avatar ?> value="1" />
		<label for="cc_post_templater"><?php _e('Hide date/category','cc')?>:</label>
		<input name="cc_post_template_date" id="cc_post_template_date" type="checkbox" <?php echo $checked_post_template_date ?> value="1" />
		<label for="cc_post_templater"><?php _e('Hide tags','cc')?>:</label>

		<input name="cc_post_template_tags" id="cc_post_template_tags" type="checkbox" <?php echo $checked_post_template_tags ?> value="1" />
		<label for="cc_post_templater"><?php _e('Hide comment-info','cc')?>:</label>
		<input name="cc_post_template_comments_info" id="cc_post_template_comments_info" type="checkbox" <?php echo $checked_post_template_comments_info ?> value="1" />
		</p>

		</div>	
	</div>
<?php
 }
 
function cc_post_meta_add($id){
    if(!empty($_POST) && !empty($_POST['action']) && $_POST['action'] == 'inline-save')
    return;
    
	if (isset($_POST['cc_post_template_on']) && $_POST['cc_post_template_on'] == "1") {
	 	update_post_meta($id,"_cc_post_template_on",1);
	}else{
	 	update_post_meta($id,"_cc_post_template_on",0);
	}

	if (isset($_POST['cc_post_template_type']) === true) {
	    update_post_meta($id,"_cc_post_template_type",$_POST["cc_post_template_type"]);
	}
	
	if (isset($_POST['cc_post_template_avatar']) && $_POST['cc_post_template_avatar'] == "1") {
	 	update_post_meta($id,"_cc_post_template_avatar",1);
	} else {
	 	update_post_meta($id,"_cc_post_template_avatar",0);
	}
	if (isset($_POST['cc_post_template_date']) && $_POST['cc_post_template_date'] == "1") {
	 	update_post_meta($id,"_cc_post_template_date",1);
	}else{
	 	update_post_meta($id,"_cc_post_template_date",0);
	}
	if (isset($_POST['cc_post_template_tags']) && $_POST['cc_post_template_tags'] == "1") {
	 	update_post_meta($id,"_cc_post_template_tags",1);
	}else{
	 	update_post_meta($id,"_cc_post_template_tags",0);
	}
	if (isset($_POST['cc_post_template_comments_info']) && $_POST['cc_post_template_comments_info'] == "1") {
	 	update_post_meta($id,"_cc_post_template_comments_info",1);
	}else{
	 	update_post_meta($id,"_cc_post_template_comments_info",0);
	}
}
 
function cc_get_post_meta(){
  	global $post;
    $cc_page = array();
	$cc_page['cc_post_template_on']=get_post_meta($post->ID,"_cc_post_template_on", true);
	$cc_page['cc_post_template_type']=get_post_meta($post->ID,"_cc_post_template_type", true);
	$cc_page['cc_post_template_avatar']=get_post_meta($post->ID,"_cc_post_template_avatar", true);
	$cc_page['cc_post_template_date']=get_post_meta($post->ID,"_cc_post_template_date", true);
	$cc_page['cc_post_template_tags']=get_post_meta($post->ID,"_cc_post_template_tags", true);
	$cc_page['cc_post_template_comments_info']=get_post_meta($post->ID,"_cc_post_template_comments_info", true);
	return $cc_page;
} 
add_action('edit_form_advanced', 'cc_post_metabox');
add_action('save_post','cc_post_meta_add');
