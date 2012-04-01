<?php


/********* Admin CP v1.0 ************/


/* Adds jquery script */
add_action('wp_print_scripts', 'jquery_script',8);
function jquery_script(){
	if ( function_exists('esc_attr') ) wp_enqueue_script('jquery'); 
	else { 
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js', false, '1.3.2'); 
	}
}


/* Admin scripts + ajax jquery code */
function admin_js(){
	$admincp_jsfolder = get_bloginfo('template_directory') . '/functions/js';
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-form');
	wp_enqueue_script('admincp_checkbox',$admincp_jsfolder . '/checkbox.js');
	wp_enqueue_script('admincp_functions_init',$admincp_jsfolder . '/functions-init.js');
	wp_localize_script( 'admincp_functions_init', 'admincpSettings', array(
			'clearpath' => get_bloginfo('template_directory') . '/functions/images/empty.png',
			'admincp_nonce' => wp_create_nonce('admincp_nonce')
	));
	wp_enqueue_script('admincp_colorpicker',$admincp_jsfolder . '/colorpicker.js');
	wp_enqueue_script('admincp_eye',$admincp_jsfolder . '/eye.js');
	wp_enqueue_script('admincp_layout',$admincp_jsfolder . '/layout.js');	
}
/* --------------------------------------------- */

/* Adds additional AdminCP css */
function css_admin() { ?> 
	<link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/functions/css/admin-style.css" type="text/css" />
	<style type="text/css">
	.lightboxclose { background: url("<?php bloginfo('template_directory') ?>/functions/images/description-close.png") no-repeat; width: 19px; height: 20px; }
	</style>
	<!--[if IE 8]>
	<style type="text/css">
			#admincp-save, #admincp-reset { font-size: 0px; display:block; line-height: 0px; bottom: 18px;}
	</style>
	<![endif]-->  
<?php }
/* --------------------------------------------- */

/* Save/Reset actions | Adds theme options to WP-Admin menu */
add_action('admin_menu', 'mytheme_add_admin');
function mytheme_add_admin() {

    global $themename, $shortname, $options;
	$admincp = basename(__FILE__);
	
	if ( isset($_GET['page']) && $_GET['page'] == $admincp ) {
		admincp_save_data();
	}
	
    $core_page = add_theme_page($themename." Options", $themename." Theme Options", 'switch_themes', basename(__FILE__), 'mytheme_admin');
	
	add_action( "admin_print_scripts-{$core_page}", 'admin_js' );
	
	add_action("admin_head-{$core_page}", 'css_admin');
}
/* --------------------------------------------- */

/* Displays AdminCP */
function mytheme_admin() {

    global $themename, $shortname, $options;
	
	if (isset($_REQUEST['saved'])) {
		if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	};
	if (isset($_REQUEST['reset'])) {
		if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
	};
    
?>

<div id="wrapper">
  <div id="panel-wrap">
	<form method="post" id="main_options_form" enctype="multipart/form-data">
		<div id="admincp-wrapper">

			<div id="admincp">
				<div id="admincp-logo"><?php echo $themename; ?></div>

				<div id="admincp-content-wrap">
					<div id="admincp-content">
						<?php global $admincpMainTabs; ?>
						<ul id="admincp-mainmenu">
							<?php if(in_array('general',$admincpMainTabs)) { ?>
								<li><a href="#nav-general"><img src="<?php bloginfo('template_directory') ?>/functions/images/ico-general.png" class="pngfix" alt="" />General Settings</a></li>
							<?php }; ?>
							<?php if(in_array('navigation',$admincpMainTabs)) { ?>
								<li><a href="#nav-navigation"><img src="<?php bloginfo('template_directory') ?>/functions/images/ico-navigation.png" class="pngfix" alt="" />Navigation</a></li>
							<?php }; ?>
							<?php if(in_array('layout',$admincpMainTabs)) { ?>
								<li><a href="#nav-layout"><img src="<?php bloginfo('template_directory') ?>/functions/images/ico-layout.png" class="pngfix" alt="" />Layout Settings</a></li>
							<?php }; ?>
							<?php if(in_array('ad',$admincpMainTabs)) { ?>
								<li><a href="#nav-advertisements"><img src="<?php bloginfo('template_directory') ?>/functions/images/ico-ad.png" class="pngfix" alt="" />Ad Management</a></li>
							<?php }; ?>

							<?php if(in_array('seo',$admincpMainTabs)) { ?>
								<li><a href="#nav-seo"><img src="<?php bloginfo('template_directory') ?>/functions/images/ico-seo.png" class="pngfix" alt="" />SEO Options</a></li>
							<?php }; ?>
							<?php if(in_array('integration',$admincpMainTabs)) { ?>
								<li><a href="#nav-integration"><img src="<?php bloginfo('template_directory') ?>/functions/images/ico-integration.png" class="pngfix" alt="" />Integration</a></li>
							<?php }; ?>
							<?php if(in_array('doc',$admincpMainTabs)) { ?>
								<li><a href="#nav-doc"><img src="<?php bloginfo('template_directory') ?>/functions/images/ico-doc.png" class="pngfix" alt="" />Documentation</a></li>
							<?php }; ?>
						</ul><!-- end admincp mainmenu -->

<?php foreach ($options as $value) {
if (($value['type'] == "text") || ($value['type'] == "textlimit") || ($value['type'] == "textarea") || ($value['type'] == "select") || ($value['type'] == "checkboxes") || ($value['type'] == "different_checkboxes") || ($value['type'] == "colorpicker") || ($value['type'] == "textcolorpopup") || ($value['type'] == "upload")) { ?>
			<div class="admincp-box">
			  <div class="box-title">
				<h3><?php echo $value['name']; ?></h3>
				<img src="<?php bloginfo('template_directory') ?>/functions/images/help.png" alt="description" class="box-description" />
				<div class="box-descr">
					<p><?php echo $value['desc']; ?></p>
				</div> <!-- end box-desc-content div -->
		      </div> <!-- end div box-title -->
				<div class="box-content">
		
		<?php if ($value['type'] == "text") { ?>
		
			<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
			
		<?php } elseif ($value['type'] == "textlimit") { ?>
		
			<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" maxlength="<?php echo $value['max']; ?>" size="<?php echo $value['max']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
			
		<?php } elseif ($value['type'] == "colorpicker") { ?>
		
			<div id="colorpickerHolder"></div>
			
		<?php } elseif ($value['type'] == "textcolorpopup") { ?>
		
			<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="colorpopup" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
			
		<?php } elseif ($value['type'] == "textarea") { ?>
		
			<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'] )); } else { echo stripslashes($value['std']); } ?></textarea>
			
		<?php } elseif ($value['type'] == "upload") { ?>
				
				<input id="<?php echo $value['id']; ?>" class="uploadfield" type="text" size="90" name="<?php echo $value['id']; ?>" value="<?php echo(get_option($value['id'])); ?>" />
				<div class="upload_buttons">
					<span class="upload_image_reset">Reset</span>
					<input class="upload_image_button" type="button" value="Upload Image" />
				</div>
				
				<div class="clear"></div>
						
		<?php } elseif ($value['type'] == "select") { ?>
		
			<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
            <?php foreach ($value['options'] as $option) { ?>
                <option<?php if ( htmlspecialchars(get_option( $value['id'] )) == trim(htmlspecialchars($option)) ) { echo ' selected="selected"'; } elseif (isset($value['std']) && $option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo trim($option); ?></option>
            <?php } ?>
            </select>
			
		<?php } elseif ($value['type'] == "checkboxes") {
		
			if (empty($value['options'])) {
				echo("You don't have pages");
			} else {
				$i = 1;
				$className = 'inputs';
				if ( isset($value['excludeDefault']) && $value['excludeDefault'] == 'true' ) $className = $className . ' different';
				
				foreach ($value['options'] as $option) {
					$checked = "";
					
					if (get_option( $value['id'])) {
						if (in_array($option, get_option( $value['id'] ))) $checked = "checked=\"checked\"";
					} ?>
					
					<p class="<?php echo $className; ?><?php if ($i%3 == 0) echo(' last'); ?>"><input type="checkbox" class="usual-checkbox" name="<?php echo $value['id']; ?>[]" id="<?php echo $value['id'],"-",$option; ?>" value="<?php echo ($option); ?>" <?php echo $checked; ?> />
					<label for="<?php echo $value['id'],"-",$option; ?>"><?php if ($value['usefor']=='pages') echo get_pagename($option); else echo get_categname($option); ?></label>
					</p>
					<?php if ($i%3 == 0) echo('<br class="clearfix"/>');?>
			  <?php $i++; }
			}; ?>
				<br class="clearfix"/>
			
		<?php } elseif ($value['type'] == "different_checkboxes") {
		
			foreach ($value['options'] as $option) {
				$checked = "";
				if (get_option( $value['id'])) {
					if (in_array($option, get_option( $value['id'] ))) $checked = "checked=\"checked\"";
				} ?>
				<p class="<?php echo ("postinfo-".$option) ?>"><input type="checkbox" class="usual-checkbox" name="<?php echo $value['id']; ?>[]" id="<?php echo ($value['id']."-".$option); ?>" value="<?php echo ($option); ?>" <?php echo $checked; ?> />
				</p>
		  <?php } ?>
			<br class="clearfix"/>
			
		<?php } ?>
		
				</div> <!-- end box-content div -->
			</div> <!-- end admincp-box div -->
			
<?php } elseif (($value['type'] == "checkbox") || ($value['type'] == "checkbox2")) { ?>

			<div class="admincp-box <?php if ($value['type'] == "checkbox") {echo('admincp-box-small-1');} else {echo('admincp-box-small-2');} ?>">
			  <div class="box-title"><h3><?php echo $value['name']; ?></h3>
				<img src="<?php bloginfo('template_directory') ?>/functions/images/help.png" alt="description" class="box-description" />
				<div class="box-descr">
					<p><?php echo $value['desc']; ?></p>
				</div> <!-- end box-desc-content div -->
			  </div> <!-- end div box-title -->
				<div class="box-content">
	<?php $checked = '';
	if((get_option($value['id'])) <> '') {
		if((get_option($value['id'])) == 'on') { $checked = 'checked="checked"'; }
		else { $checked = ''; }
	}
	elseif ($value['std'] == 'on') { $checked = 'checked="checked"'; }
?>
    <input type="checkbox" class="checkbox" name="<?php echo($value['id']);?>" id="<?php echo($value['id']);?>" <?php echo($checked); ?> />
				</div> <!-- end box-content div -->
			</div> <!-- end admincp-box-small div -->
			
	<?php } elseif ($value['type'] == "doc") { ?>
				
				<div class="inner-content">
					<?php include(TEMPLATEPATH . "/includes/docs/".$value['name'].".php"); ?>
				</div>
				
	<?php } elseif (($value['type'] == "contenttab-wrapstart") || ($value['type'] == "subcontent-start")) { ?>

				<div id="<?php echo $value['name']; ?>" class="<?php if ($value['type'] == "contenttab-wrapstart") {echo('content-div');} else {echo('tab-content');} ?>">
				
	<?php } elseif (($value['type'] == "contenttab-wrapend") || ($value['type'] == "subcontent-end")) { ?>

				</div> <!-- end <?php echo $value['name']; ?> div -->
				
	<?php } elseif ($value['type'] == "subnavtab-start") { ?>

				<ul class="idTabs">
				
	<?php } elseif ($value['type'] == "subnavtab-end") { ?>

				</ul>
				
	<?php } elseif ($value['type'] == "subnav-tab") { ?>

				<li><a href="#<?php echo $value['name']; ?>"><span class="pngfix"><?php echo $value['desc']; ?></span></a></li>
				
	<?php } elseif ($value['type'] == "clearfix") { ?>
				
				<div class="clearfix"></div>

	<?php } ?>

<?php } //end foreach ($options as $value) ?>
		
					</div> <!-- end admincp-content div -->
				</div> <!-- end admincp-content-wrap div -->
			</div> <!-- end admincp div -->
		</div> <!-- end admincp-wrapper div -->
		
		<div id="admincp-bottom">
        			<input name="save" type="submit" value="Save changes" id="admincp-save" />
			<input type="hidden" name="action" value="save_admincp" />
		
        <img src="<?php bloginfo('template_directory') ?>/functions/images/reset.png" class="defaults-button" alt="no" />
               
        </div><!-- end admincp-bottom div -->
		
    </form>
     
	<div style="clear: both;"></div>
        <div style="position: relative;">
			<div class="defaults-hover">
				This will return all of the settings throughout the options page to their default values. <strong>Are you sure you want to do this?</strong>
				<div class="clearfix"></div>
				<form method="post">
					<input name="reset" type="submit" value="Reset" id="admincp-reset" />
					<input type="hidden" name="action" value="reset" />
				</form>
				<img src="<?php bloginfo('template_directory') ?>/functions/images/no.png" class="no" alt="no" />
			</div> 
        </div>
        
	   </div> <!-- end panel-wrap div -->
	</div> <!-- end wrapper div -->
	
	<div id="admincp-ajax-saving">
		<img src="<?php bloginfo('template_directory') ?>/functions/images/loading.gif" alt="loading" id="loading" />
		<span>Saving...</span>
	</div>
	
<?php
}
/* --------------------------------------------- */


global $options, $value, $shortname;
foreach ($options as $value) {
	if (isset($value['id'])) {
		if ( get_option( $value['id'] ) === FALSE) {
			if (array_key_exists('std', $value)) { 
				update_option( $value['id'], $value['std'] );
				$$value['id'] = $value['std'];
			}
		} else {
			$$value['id'] = get_option( $value['id'] ); }
	}
}


add_action('wp_ajax_save_admincp', 'my_action_callback');
function my_action_callback() {
    check_ajax_referer("admincp_nonce");
	admincp_save_data();

	die();
}

function admincp_save_data(){
	global $options;
	
	$admincp = basename(__FILE__);
	
	if ( isset($_REQUEST['action']) ) {
		
		$valuesArray = array();
		
		if ( 'save_admincp' == $_REQUEST['action'] ) {
			foreach ($options as $value) {
				if( isset( $value['id'] ) ) { 
					if( isset( $_REQUEST[ $value['id'] ] ) ) {
						if ($value['type'] == 'textarea' || $value['type'] == 'text' || $value['type'] == 'textlimit' || $value['type'] == 'upload') update_option( $value['id'], stripslashes($_REQUEST[$value['id']]) );
						elseif ($value['type'] == 'select') update_option( $value['id'], htmlspecialchars($_POST[$value['id']]) );
						else update_option( $value['id'], $_POST[$value['id']] );
					}
					else {
						if ($value['type'] == 'checkbox' || $value['type'] == 'checkbox2') update_option( $value['id'] , 'false' );
						elseif ($value['type'] == 'different_checkboxes') {
							update_option( $value['id'] , $_POST[$value['id']] );
						}
						else delete_option( $value['id'] );
					}
				}
				
				$valuesArray[$value['id']] = $_POST[$value['id']];
			}
			#print_r(base64_encode(serialize($valuesArray))); exit;
			header("Location: themes.php?page=$admincp&saved=true");
			die;
			
		} else if( 'reset' == $_REQUEST['action'] ) {

			foreach ($options as $value) {
				if (isset($value['id'])) {
					delete_option( $value['id'] );
					if (isset($value['std'])) $$value['id'] = $value['std'];
				};
			}
			
			header("Location: themes.php?page=$admincp&reset=true");
			die;
		}
	}
	
}

function upload_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', get_bloginfo('template_directory').'/functions/js/custom_uploader.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
}
	 
function upload_styles() {
	wp_enqueue_style('thickbox');
}

global $pagenow; 
if ( 'themes.php' == $pagenow && isset($_GET['page']) && ($_GET['page'] == basename(__FILE__)) ) {
	add_action('admin_print_scripts', 'upload_scripts');
	add_action('admin_print_styles', 'upload_styles');
} ?>