<?php

/* PRINT ADMIN JAVASCRIPTS */
/*------------------------------------------------------------------*/
	
add_action('admin_print_scripts', 'bizz_print_admin_scripts');
function bizz_print_admin_scripts() {

    global $pagenow;
	
	if ( is_admin() && ('admin.php' == $pagenow) && ( $_GET['page'] == 'bizzthemes' || $_GET['page'] == 'bizz-design' ) ) {
	
	    // theme options admin header
		wp_deregister_script( 'jquery' ); //deregister current jquery
		wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'); // header
		// wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js'); // header
		// wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('ajaxupload', BIZZ_FRAME_JS .'/ajaxupload.js'); // header
		
		// theme options admin footer
		wp_enqueue_script('jscolor', BIZZ_FRAME_ROOT .'/jscolor/jscolor.js', '', '', true); // footer
		wp_enqueue_script('jquery-bizzthemes', BIZZ_FRAME_JS .'/bizzthemes.js', '', '', true); // footer
		wp_enqueue_script('jquery-jwysiwyg', BIZZ_FRAME_ROOT .'/jwysiwyg/jquery.wysiwyg.min.js', '', '', true); // footer
		
	} elseif ( is_admin() && ('widgets.php' == $pagenow) ) {
	
	    // theme widgets admin footer
	    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'); // header
		// wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-jwysiwyg', BIZZ_FRAME_ROOT .'/jwysiwyg/jquery.wysiwyg.min.js', '', '', true); // footer
		wp_enqueue_script('ajaxupload', BIZZ_FRAME_JS .'/ajaxupload.js'); // header
	
	}

}

/* PRINT ADMIN STYLESHEETS */
/*------------------------------------------------------------------*/

if ( is_admin() ) {
	
    add_action('admin_print_styles', 'bizz_print_admin_styles');
	function bizz_print_admin_styles() {
	
	    // header
		wp_enqueue_style('admin_style', BIZZ_FRAME_CSS .'/admin_style.css');
		wp_enqueue_style('jwysiwyg_style', BIZZ_FRAME_ROOT .'/jwysiwyg/jquery.wysiwyg.css');
	
	}

}

/* ADMIN HEADER SCRIPTS */
/*------------------------------------------------------------------*/

add_action('admin_head', 'bizzthemes_admin_head');

function bizzthemes_admin_head() {
    
	global $pagenow;
	
    // THEME OPTIONS PANEL ADMIN HEADER
    if ( is_admin() && ('admin.php' == $pagenow) && ( $_GET['page'] == 'bizzthemes' || $_GET['page'] == 'bizz-design' ) ) {
	
?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
						
		// UPDATE MESSAGE POPUP
		
		    // animation positioning
		    jQuery.fn.center = function () {
			    this.animate({"top":( jQuery(window).height() - this.height() - 200 ) / 2+jQuery(window).scrollTop() + "px"},100);
				this.css("left", 250 );
				return this;
			}
			// animation class to call
			jQuery('#bizz-popup-save').center();
			jQuery(window).scroll(function() {
			    jQuery('#bizz-popup-save').center();
			});
		
		// AJAX Upload
		 
		    //jQuery('.upload_button').live('click', function(e) { 
			jQuery('.upload_button').each(function() {
			    var clickedObject = jQuery(this);
				var clickedID = jQuery(this).attr('id');
				new AjaxUpload(clickedID, {
				    action: '<?php echo admin_url("admin-ajax.php"); ?>',
					name: clickedID, // File upload name
					data: { // Additional data to send
					    action: 'bizz_ajax_post_action',
						type: 'upload',
						data: clickedID },
					autoSubmit: true, // Submit file after selection
					responseType: false,
					onChange: function(file, extension){},
					onSubmit: function(file, extension){
					    clickedObject.text('Choose File'); // change button text, when user selects file
						this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
						interval = window.setInterval(function(){
						    var text = clickedObject.text();
							if (text.length < 13){	
							    clickedObject.text(text + '.'); 
							} else { 
							    clickedObject.text('Choose File'); 
							}
						}, 200);
					},
					onComplete: function(file, response) {
					    window.clearInterval(interval);
						clickedObject.text('Choose File');
						this.enable(); // enable upload button
						
						// AJAX response before saving
						var buildReturn = '<a class="img-preview" href="'+response+'"><img src="'+response+'" width="20" height="20" alt="Image Preview" /></a>';
						jQuery(".upload-error").remove();
						jQuery("#image_" + clickedID).remove();
						clickedObject.next('input').after(buildReturn);
						jQuery('img#image_'+clickedID).fadeIn();
						clickedObject.next('input').val(response);
					}
				});
			});
			
		// SAVING ALL OPTIONS
			
			jQuery('#bizz_form').submit(function(){
			    function newValues() {
				  var serializedValues = jQuery("#bizz_form").serialize();
				  return serializedValues;
				}
				jQuery(":checkbox, :radio").click(newValues);
				jQuery("select").change(newValues);
				jQuery('.ajax-loading').center().fadeIn();
				var serializedReturn = newValues();
				var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
				//var data = {data : serializedReturn};
				var data = {
				    <?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'bizz-design'){ ?>
					type: 'bizz-design',
					<?php } else { ?>
					type: 'bizz-all',
					<?php } ?>
					action: 'bizz_ajax_post_action',
					data: serializedReturn
				};
				jQuery.post(ajax_url, data, function(response) {
					var success = jQuery('#bizz-popup-save');
					var loading = jQuery('.ajax-loading');
					loading.fadeOut();  
					success.fadeIn();
					window.setTimeout(function(){
					   success.fadeOut(); 
					}, 2000);
					// alert("Data Loaded: " + response);
				});
				return false; 
			});   	 	
			
		});
	    </script>
<?php
		
	// POST or PAGE ADMIN HEADER
	} else {
?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
		    jQuery('form#post').attr('enctype','multipart/form-data');
			jQuery('form#post').attr('encoding','multipart/form-data');
		});
		</script>
<?php	
	}
}

/* ADMIN FOOTER SCRIPTS */
/*------------------------------------------------------------------*/
add_action('admin_footer', 'bizzthemes_admin_foot');

function bizzthemes_admin_foot() {
global $pagenow;
   
    // WIDGETS ADMIN HEADER	
	if ( is_admin() && ('widgets.php' == $pagenow) ) {
		
?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
		
		// Rich Textarea Widget
		jQuery('span.richtext').live('click', function() {
		    jQuery('.xwysiwyg').wysiwyg();
			jQuery(this).removeClass('richtext');
			jQuery(this).addClass('richon');
			return false;
		});
		jQuery('span.richon').live('click', function() {
			jQuery('div.wysiwyg').remove();
			jQuery(this).removeClass('richon');
			jQuery(this).addClass('richtext');
			jQuery("textarea.xwysiwyg").css("display", "block");			
			return false;
		});
		jQuery('span.titicon').live('click', function() {
			jQuery(this).parents('.widget-content').find('.wid_icon').toggle();
			jQuery(this).parents('.widget-content').find('.wid_upload_button').click();
			return false;
		});
		// Contact Form Widget
		jQuery('span.translate').live('click', function() {
			jQuery(this).parents('.widget-content').find('.tog').toggle();
			return false;
		});

		
		// AJAX Upload for Widgets		
		jQuery('.wid_upload_button').live('click', function() {
		// jQuery('.wid_upload_button').each(function() {
			var clickedObject = jQuery(this);
			var clickedID = jQuery(this).attr('id');
			new AjaxUpload(clickedID, {
				action: '<?php echo admin_url("admin-ajax.php"); ?>',
				name: clickedID, // File upload name
				data: { // Additional data to send
					action: 'bizz_ajax_post_action',
					type: 'upload',
					data: clickedID },
				autoSubmit: true, // Submit file after selection
				responseType: false,
				onChange: function(file, extension){},
				onSubmit: function(file, extension){
					clickedObject.text('Choose File'); // change button text, when user selects file
					this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
					interval = window.setInterval(function(){
						var text = clickedObject.text();
						if (text.length < 13){	
							clickedObject.text(text + '.'); 
						} else { 
							clickedObject.text('Choose File'); 
						}
					}, 200);
				},
				onComplete: function(file, response) {
					window.clearInterval(interval);
					clickedObject.text('Choose File');
					this.enable(); // enable upload button
						
					// AJAX response before saving
					jQuery(".upload-error").remove();
					clickedObject.next('input').val(response);
				}
			});
		});
		
		});
	    </script>
<?php
		
	}
}

/* PRINT THEME JAVASCRIPTS */
/*------------------------------------------------------------------*/

// Print scripts for theme files
if (!is_admin()) add_action( 'wp_print_scripts', 'bizzthemes_theme_head_scripts' );

function bizzthemes_theme_head_scripts( ) {

    // Pretty Photo script
	if ( isset($GLOBALS['opt']['bizzthemes_prettyphoto']) ) {
		wp_enqueue_script( 'prettyPhoto', BIZZ_FRAME_JS .'/prettyPhoto.js', array( 'jquery' ), '', true ); // footer
	}
	// jQuery tools
	wp_enqueue_script( 'jquery-tools', BIZZ_FRAME_JS .'/jquery.tools.min.js', array( 'jquery' ), '', true ); // footer
	// Frame scripts
	wp_enqueue_script( 'theme-scripts', BIZZ_FRAME_JS .'/frame.js', array( 'jquery' ) ); // header
	// wp_enqueue_script( 'superfish', BIZZ_FRAME_JS .'/superfish.js', array( 'jquery' ) ); // header
}


/* PRINT THEME STYLESHEETS */
/*------------------------------------------------------------------*/

if (!is_admin()) add_action('wp_print_styles', 'bizzthemes_theme_head_styles');

function bizzthemes_theme_head_styles( ) {
	
	$date_modified_style = filemtime(TEMPLATEPATH . '/style.css');
	$date_modified_custom = filemtime(BIZZ_LIB_CUSTOM . '/custom.css');
	$date_modified_layout = filemtime(BIZZ_LIB_CUSTOM . '/layout.css');
	
	// Main stylesheet
	wp_register_style('main_stylesheet', get_bloginfo('stylesheet_url') . '?' . date('mdy-Gis', $date_modified_style), '', '', 'screen, projection');
	wp_enqueue_style('main_stylesheet');
	
	// Shortcodes
	wp_register_style('css_shortcodes', BIZZ_FRAME_CSS .'/shortcodes.css');
	wp_enqueue_style('css_shortcodes');
	
	if ( isset($GLOBALS['optd']['bizzthemes_layout_css']) ) {} else { // hide layout.css output
		// Layout stylesheet
		wp_register_style('layout_stylesheet',  get_bloginfo('template_url') .'/custom/layout.css?' . date('mdy-Gis', $date_modified_layout), '', '', 'screen, projection');
		wp_enqueue_style('layout_stylesheet');
	}
		
	if ( isset($GLOBALS['optd']['bizzthemes_custom_css']) ) {} else { // hide custom.css output
		// Custom stylesheet
		wp_register_style('custom_stylesheet', get_bloginfo('template_url') .'/custom/custom.css?' . date('mdy-Gis', $date_modified_custom), '', '', 'screen, projection');
		wp_enqueue_style('custom_stylesheet');
	}
	
	// Skin stylesheet
	$stylesheet = (isset($GLOBALS['optd']['bizzthemes_alt_stylesheet'])) ? $GLOBALS['optd']['bizzthemes_alt_stylesheet'] : '';
	if($stylesheet != ''){
	    wp_register_style('skinStylesheet', BIZZ_THEME_SKINS .'/'. $stylesheet);
	    wp_enqueue_style('skinStylesheet');
	}
	
	// Pretty Photo stylesheet
	if ( isset($GLOBALS['opt']['bizzthemes_prettyphoto']) ) {
	    wp_register_style('prettyphotoStylesheet', BIZZ_FRAME_CSS .'/prettyPhoto.css');
	    wp_enqueue_style('prettyphotoStylesheet');
	}

}

/* THEME HEAD CODE */
/*------------------------------------------------------------------*/
add_action('wp_head', 'bizzthemes_theme_head');

function bizzthemes_theme_head() {
	
	// Custom theme Favicon
	if (isset($GLOBALS['opt']['bizzthemes_favicon']) && $GLOBALS['opt']['bizzthemes_favicon'] != ''){
	    echo '<link rel="shortcut icon" href="'.$GLOBALS['opt']['bizzthemes_favicon'].'"/>'."\n";
	}
	
	// RSS Feed Settings
	echo '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="';
	if ( isset($GLOBALS['opt']['bizzthemes_feedburner_url']) && $GLOBALS['opt']['bizzthemes_feedburner_url'] <> "" ) { 
	    echo strip_tags(stripslashes($GLOBALS['opt']['bizzthemes_feedburner_url']));
	} else { 
	    echo get_bloginfo_rss('rss2_url');
	}
	echo '" />'."\n";
	
	// Embeded Theme Scripts in Header
	echo '<!--[if lt IE 8]><script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE8.js" type="text/javascript"></script><![endif]-->'."\n"; 
	
	// Theme header scripts (like Mint tracking code)
	if (isset($GLOBALS['opt']['bizzthemes_scripts_header']) && $GLOBALS['opt']['bizzthemes_scripts_header'] <> "" ) { 
	    echo stripslashes($GLOBALS['opt']['bizzthemes_scripts_header']); 
	}
	
	// Google Font Settings
	$font_stacks = bizz_get_fonts();
	$ffamiliy = array();
	if ( isset($GLOBALS['optd']) && $GLOBALS['optd'] <> '' ){
	foreach ($GLOBALS['optd'] as $key => $value){
		if ( isset($GLOBALS['optd'][$key]['face']) && isset($font_stacks[$GLOBALS['optd'][$key]['face']]['google']) ){
		    $face_id = $font_stacks[$GLOBALS['optd'][$key]['face']]['name'];
			// echo '<link href="http://fonts.googleapis.com/css?family='.$face_id.'" rel="stylesheet" type="text/css" />'."\n";
			$ffamiliy[$key] = $face_id;
		}
	}
	}
	$ffamiliy_u = array_unique($ffamiliy);
	foreach ($ffamiliy_u as $key => $value){
	    echo '<link href="http://fonts.googleapis.com/css?family='.$value.'" rel="stylesheet" type="text/css" />'."\n";
	}
	
}

/* THEME BODY CODE */
/*------------------------------------------------------------------*/
add_action('bizz_body_after', 'bizzthemes_theme_body');

function bizzthemes_theme_body() {
		
	// Theme header scripts (like Mint tracking code)
	if (isset($GLOBALS['opt']['bizzthemes_scripts_body']) && $GLOBALS['opt']['bizzthemes_scripts_body'] <> "" ) { 
	    echo stripslashes($GLOBALS['opt']['bizzthemes_scripts_body']); 
	}
	
}

/* THEME FOOTER CODE */
/*------------------------------------------------------------------*/
add_action('wp_footer', 'bizzthemes_theme_foot');

function bizzthemes_theme_foot() { 
	
?>
	<script type="text/javascript">
	
	<?php if ( isset($GLOBALS['opt']['bizzthemes_prettyphoto']) ) { ?>
	// PrettyPhoto (lightbox)
	    jQuery(document).ready(function($){
	        jQuery("a[href*='.jpg'],a[href*='.gif'],a[href*='.png'],a[href*='.bmp'],a[href*='.swf'],a[href*='.mov'],a[rel^='prettyPhoto']").prettyPhoto({
			    animationSpeed: 'fast', /* fast/slow/normal */
				theme: 'facebook' /* light_rounded / dark_rounded / light_square / dark_square / facebook */
			});
	    });
	<?php } ?>
	
	</script>
<?php
	
	// Theme footer scripts (like Google Analytics)
	if ( isset($GLOBALS['opt']['bizzthemes_google_analytics']) && $GLOBALS['opt']['bizzthemes_google_analytics'] <> "" ) { 
	    echo stripslashes($GLOBALS['opt']['bizzthemes_google_analytics']); 
	}
	
}

?>