<?php

add_action( 'add_meta_boxes', 'cc_post_metabox' );
function cc_post_metabox() {
    add_meta_box( 
        'cc_post_metabox',
        __( 'Custom Community Post Settings', 'cc_pro' ),
        'cc_post_metabox_inner',
        'post' 
    );
}

function cc_post_metabox_inner( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'cc_post_metabox_noncename' );

  // The actual fields for data entry
 	global $post;
	
   	$cc_post_options=cc_get_post_meta();
   	
	$option_post_templates[0] = "img-left-content-right";
	$option_post_templates[1] = "more options in the pro version";
	
	?>

	<b>Use a post template for this post</b>
	<p>You can select a predefined post template:<br />
		<label for="cc_post_template"><?php _e('Post template on','cc')?>:</label>
		<input name="cc_post_template_on" id="cc_post_template_on" type="checkbox" <?php checked( $cc_post_options['cc_post_template_on'], 1 ); ?> value="1" />
		Select a template to use:<select id="cc_post_template_type" name="cc_post_template_type">
		<?php foreach($option_post_templates as $option_template){?>
			<option <?php selected( $cc_post_options['cc_post_template_type'], $option_template ); ?>><?php echo $option_template; ?></option>		
		<?php }?>
		</select>
	</p>
	<b>Schow/hide meta info</b>
	<p>
	<label for="cc_post_templater"><?php _e('Hide avatar','cc')?>:</label>
	<input name="cc_post_template_avatar" id="cc_post_template_avatar" type="checkbox" <?php checked( $cc_post_options['cc_post_template_avatar'], 1 ); ?> value="1" />
	<label for="cc_post_templater"><?php _e('Hide date/category','cc')?>:</label>
	<input name="cc_post_template_date" id="cc_post_template_date" type="checkbox" <?php checked( $cc_post_options['cc_post_template_date'], 1 ); ?> value="1" />
	<label for="cc_post_templater"><?php _e('Hide tags','cc')?>:</label>
	
	<input name="cc_post_template_tags" id="cc_post_template_tags" type="checkbox" <?php checked( $cc_post_options['cc_post_template_tags'], 1 ); ?> value="1" />
	<label for="cc_post_templater"><?php _e('Hide comment-info','cc')?>:</label>
	<input name="cc_post_template_comments_info" id="cc_post_template_comments_info" type="checkbox" <?php checked( $cc_post_options['cc_post_template_comments_info'], 1 ); ?> value="1" />
	</p>
	
	<?php
}

add_action( 'save_post', 'cc_post_save_postdata' );
function cc_post_save_postdata( $post_id ) {
	global $post;
	
	if(empty($post->post_type))
		return;
	
	if ($post->post_type != 'post')
		return;
	
	
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['cc_post_metabox_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  
  // Check permissions
  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  if(empty($_POST["cc_post_template_on"]))
		$_POST["cc_post_template_on"] = 0;
		
	update_post_meta($post->ID, "_cc_post_template_on",cc_clean_input( $_POST["cc_post_template_on"], 'checkbox') );
	
	if(!empty($_POST["cc_post_template_type"]))
		update_post_meta($post->ID, "_cc_post_template_type",cc_clean_input( $_POST["cc_post_template_type"], 'text') );

	if(empty($_POST["cc_post_template_avatar"]))
		$_POST["cc_post_template_avatar"] = 0;
	
	update_post_meta($post->ID, "_cc_post_template_avatar",cc_clean_input( $_POST["cc_post_template_avatar"], 'checkbox') );
	
	if(empty($_POST["cc_post_template_date"]))
		$_POST["cc_post_template_date"] = 0;
		
	update_post_meta($post->ID, "_cc_post_template_date",cc_clean_input( $_POST["cc_post_template_date"], 'checkbox') );

	if(empty($_POST["cc_post_template_tags"]))
		$_POST["cc_post_template_tags"]  = 0;
	
	update_post_meta($post->ID, "_cc_post_template_tags",cc_clean_input( $_POST["cc_post_template_tags"], 'checkbox') );

	if(empty($_POST["cc_post_template_comments_info"]))
		$_POST["cc_post_template_comments_info"]  = 0;
	
	update_post_meta($post->ID, "_cc_post_template_comments_info",cc_clean_input( $_POST["cc_post_template_comments_info"], 'checkbox') );
  
  
}

function cc_get_post_meta(){
	global $post;
	$cc_page['cc_post_template_on']=get_post_meta($post->ID,"_cc_post_template_on", true);
	$cc_page['cc_post_template_type']=get_post_meta($post->ID,"_cc_post_template_type", true);
	$cc_page['cc_post_template_avatar']=get_post_meta($post->ID,"_cc_post_template_avatar", true);
	$cc_page['cc_post_template_date']=get_post_meta($post->ID,"_cc_post_template_date", true);
	$cc_page['cc_post_template_tags']=get_post_meta($post->ID,"_cc_post_template_tags", true);
	$cc_page['cc_post_template_comments_info']=get_post_meta($post->ID,"_cc_post_template_comments_info", true);
	return $cc_page;
} 

add_action('edit_page_form', 'cc_page_metabox');
function cc_page_metabox(){ 	
		global $post;
		
		$cc_page_options=cc_get_page_meta();
    	
		$args = array('echo' => '0','hide_empty' => '0');
		$categories = get_categories($args);
	    $option = Array();
		$option[0] = Array (
            'name' => 'All categories',
            'slug' => 'all-categories'
        );
	    $i = 1;
		foreach($categories as $category) { 
			$option[$i]['name'] = $category->name;
			$option[$i]['slug'] = $category->slug;
			$i++;	
		}
    	$option_categories = $option;
		
	//	$cc_page_slider_time = $cc_page_options['cc_page_slider_time'];
		$cc_page_slider_orderby = $cc_page_options['cc_page_slider_orderby'];	
		$cc_page_slider_amount = $cc_page_options['cc_page_slider_amount'];	
		$cc_page_slider_post_type = $cc_page_options['cc_page_slider_post_type'];	
		$cc_page_slider_show_page = $cc_page_options['cc_page_slider_show_page'];	

		$cc_page_template_amount = $cc_page_options['cc_page_template_amount'];
			
		$option_post_templates[0] = "img-mouse-over";
		$option_post_templates[1] = "more options in the pro version";
				
		$option_styles[0] = "default";
		$option_styles[1] = "full-width-slider in the pro version";
		?>
		
	<style type="text/css">
	#cc_page_template_amount{
		width:40px;
	}
	</style>
	<div id="cc_page_metabox" class="postbox">
		<div class="handlediv" title="<?php _e('klick','cc','cc'); ?>">
			<br />
		</div>
		<h3 class="hndle"><?php _e('Custom Community settings','cc')?></h3>
		<div class="inside">
		
		<?php wp_nonce_field('cc_page_metabox','cc_page_meta_nonce'); ?>
		
		
		<p>
			<b>Slideshow</b><br />
			<label for="cc_page_slider"><?php _e('Slideshow on','cc')?>:</label>
			<input name="cc_page_slider_on" id="cc_page_slider_on" type="checkbox" <?php checked( $cc_page_options['cc_page_slider_on'], 1 ); ?> value="1" />
			Select a category to display in slideshow: <select id="cc_page_slider_cat" name="cc_page_slider_cat">
					<?php foreach($option_categories as $option_cat){?>
						<option <?php selected( $cc_page_options['cc_page_slider_cat'], $option_cat['slug'] ); ?> value="<?php echo $option_cat['slug'] ?>"><?php echo $option_cat['name']; ?></option>
					<?php }?>
			</select>
			Select a slideshow style: <select id="cc_page_slider_style" name="cc_page_slider_style">
					<?php foreach($option_styles as $option_style){?>
						<option <?php selected( $cc_page_options['cc_page_slider_style'], $option_style ); ?>><?php echo $option_style; ?></option>
					<?php }?>
			</select><br />
			<label for="cc_page_slider_post_type"><b><?php _e('Get The PRO Version for more options:','cc')?></b></label><br />
		
			<label for="cc_page_slider_caption"><?php _e('Use custom post types and pages in the slideshow, show/hide caption, define the sliding time in ms, order and amount','cc')?></label>
		
			<br /><br /><b>Lists Posts under this Page</b>
			<p>You can show your posts in a predefined template:<br />
			<label for="cc_page_template"><?php _e('Post template on','cc')?>:</label>
			<input name="cc_page_template_on" id="cc_page_template_on" type="checkbox" <?php checked( $cc_page_options['cc_page_template_on'], 1 ); ?> value="1" />
			Select a template to use: <select id="cc_posts_on_page_type" name="cc_posts_on_page_type">
					<?php foreach($option_post_templates as $option_template){?>
						<option <?php selected( $cc_page_options['cc_posts_on_page_type'], $option_template ); ?>><?php echo $option_template; ?></option>
					<?php }?>
			</select><br />
			Select a category to display: <select id="cc_page_template_cat" name="cc_page_template_cat">
			
					<?php foreach($option_categories as $option_cat){?>
						<option <?php selected( $cc_page_options['cc_page_template_cat'], $option_cat['slug'] ); ?> value="<?php echo $option_cat['slug'] ?>"><?php echo $option_cat['name']; ?></option>
					<?php }?>
			</select>
				How many posts to display? <input type="text" name="cc_page_template_amount" id="cc_page_template_amount" value="<?php echo $cc_page_template_amount; ?>" />
			</p>
		</p>
		</div>	
	</div>
<?php
 }
add_action('save_post','cc_add_page_meta');
 
function cc_add_page_meta($id){

	global $post;

	if(empty($post->post_type))
		return;
	
	if ($post->post_type != 'page')
		return;
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	{
	     return $post_id;
	}
	
	if(!empty($_POST["cc_page_slider_on"]))
		update_post_meta($post->ID, "_cc_page_slider_on",cc_clean_input( $_POST["cc_page_slider_on"], 'checkbox') );	

	if(!empty($_POST["cc_page_slider_cat"]))
		update_post_meta($post->ID, "_cc_page_slider_cat",cc_clean_input( $_POST["cc_page_slider_cat"], 'text') );

	if(!empty($_POST["cc_page_template_on"]))
		update_post_meta($post->ID, "_cc_page_template_on",cc_clean_input( $_POST["cc_page_template_on"], 'checkbox') );

	if(!empty($_POST["cc_page_template_cat"]))
		update_post_meta($post->ID, "_cc_page_template_cat",cc_clean_input( $_POST["cc_page_template_cat"], 'text') );
	
	if(!empty($_POST["cc_page_template_amount"]))
		update_post_meta($post->ID, "_cc_page_template_amount",cc_clean_input( $_POST["cc_page_template_amount"], 'text') );
	
	if(!empty($_POST["cc_page_slider_orderby"]))
		update_post_meta($post->ID, "_cc_page_slider_orderby",cc_clean_input( $_POST["cc_page_slider_orderby"], 'text') );
	
	if(!empty($_POST["cc_page_slider_amount"]))
		update_post_meta($post->ID, "_cc_page_slider_amount",cc_clean_input( $_POST["cc_page_slider_amount"], 'text') );
	
	if(!empty($_POST["cc_page_slider_post_type"]))
		update_post_meta($post->ID, "_cc_page_slider_post_type",cc_clean_input( $_POST["cc_page_slider_post_type"], 'text') );
	
	if(!empty($_POST["cc_page_slider_show_page"]))
		update_post_meta($post->ID, "_cc_page_slider_show_page",cc_clean_input( $_POST["cc_page_slider_show_page"], 'text') );
	
	if(!empty($_POST["cc_posts_on_page_type"]))
		update_post_meta($post->ID, "_cc_posts_on_page_type",cc_clean_input( $_POST["cc_posts_on_page_type"], 'text') );
	
	if(!empty($_POST["cc_page_slider_style"]))
		update_post_meta($post->ID, "_cc_page_slider_style",cc_clean_input( $_POST["cc_page_slider_style"], 'text') );
}
 
  function cc_get_page_meta(){
  	global $post;
  	if(is_object($post)){
		$cc_page['cc_page_slider_on']=get_post_meta($post->ID,"_cc_page_slider_on", true);
		$cc_page['cc_page_slider_cat']=get_post_meta($post->ID,"_cc_page_slider_cat", true);
		$cc_page['cc_page_template_on']=get_post_meta($post->ID,"_cc_page_template_on", true);
		$cc_page['cc_page_template_cat']=get_post_meta($post->ID,"_cc_page_template_cat", true);
		$cc_page['cc_page_template_amount']=get_post_meta($post->ID,"_cc_page_template_amount", true);
		$cc_page['cc_page_slider_time']=get_post_meta($post->ID,"_cc_page_slider_time", true);
		$cc_page['cc_page_slider_amount']=get_post_meta($post->ID,"_cc_page_slider_amount", true);
		$cc_page['cc_page_slider_post_type']=get_post_meta($post->ID,"_cc_page_slider_post_type", true);
		$cc_page['cc_page_slider_show_page']=get_post_meta($post->ID,"_cc_page_slider_show_page", true);
		$cc_page['cc_page_slider_orderby']=get_post_meta($post->ID,"_cc_page_slider_orderby", true);
		$cc_page['cc_posts_on_page_type']=get_post_meta($post->ID,"_cc_posts_on_page_type", true);	
		$cc_page['cc_page_slider_style']=get_post_meta($post->ID,"_cc_page_slider_style", true);
		$cc_page['cc_page_slider_caption']=get_post_meta($post->ID,"_cc_page_slider_caption", true);
		return $cc_page;
  	}
  	return false;
  } 
  
  
function cc_clean_input( $input, $type ) {
	global $allowedposttags;
	$cleanInput = false;
	
	switch ($type) {
	  case 'text':
	    $cleanInput = wp_filter_nohtml_kses ( $input );
	    break;
          case 'checkbox':
            $input === '1'? $cleanInput = '1' : $cleanInput = '';
	    break;
          case 'html':
            $cleanInput = wp_kses( $input, $allowedposttags);
	    break;
	default:
	    $cleanInput = false;
	    break;
	}
	return $cleanInput;
}
?>