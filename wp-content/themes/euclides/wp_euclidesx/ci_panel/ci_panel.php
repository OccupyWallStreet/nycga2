<?php
define('WP_THEME_URL', get_bloginfo('stylesheet_directory'));
define('WP_UPLOADIFY_URL', get_bloginfo('stylesheet_directory') . "/ci_panel/uploadify");
define('WP_UPLOAD_URL', get_template_directory() . "/ci_panel/uploads");


// Load our default options.
load_ci_defaults();



add_action('admin_init','ci_scripts');
function ci_scripts() {

	wp_enqueue_style('css_igniter', WP_THEME_URL .'/ci_panel/panel.css');


	wp_register_script('ci-swfobject', WP_THEME_URL .'/ci_panel/uploadify/swfobject.js', array('jquery'));
	wp_enqueue_script('ci-swfobject');

	wp_register_script('ci-uploadify', WP_THEME_URL .'/ci_panel/uploadify/jquery.uploadify.v2.1.0.min.js', array('jquery'));
	wp_enqueue_script('ci-uploadify');
	wp_enqueue_style('css_uploadify', WP_THEME_URL .'/ci_panel/uploadify/uploadify.css');

	wp_register_script('ci-colorpicker', WP_THEME_URL .'/ci_panel/scripts/colorpicker/js/colorpicker.js', array('jquery'));
	wp_enqueue_script('ci-colorpicker');
	wp_enqueue_style('css-ci-colorpicker', WP_THEME_URL .'/ci_panel/scripts/colorpicker/css/colorpicker.css');

	wp_register_script('ci-scripts', WP_THEME_URL .'/ci_panel/scripts/panelscripts.js', array('jquery'));
	wp_enqueue_script('ci-scripts');
	

}


add_action('admin_menu', 'ci_create_menu');
function ci_create_menu() {
	add_action( 'admin_init', 'ci_register_settings' );

	// Handle reset before anything is outputed in the browser.
	// This is here because it needs the settings to be registered, but because it
	// redirects, it should be called before the ci_settings_page.
	global $pagenow;
	if (is_admin() and isset($_POST['reset']) and ($pagenow == "themes.php") )
	{
		delete_option(THEME_OPTIONS); 
		global $ci;
		$ci=array();
		ci_default_options(true);
		wp_redirect( 'themes.php?page=ci_panel.php' );
	}

	add_theme_page(__('CSSIgniter Settings', CI_DOMAIN), __('CSSIgniter Settings', CI_DOMAIN), 'edit_themes', basename(__FILE__), 'ci_settings_page');
}

function ci_register_settings() {
	register_setting( 'ci-settings-group', THEME_OPTIONS, 'ci_options_validate');
}


function ci_settings_page() {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	//uploadify
	$('.browse').each( function() {
		var el = '#' + $(this).attr('id');
		$(el).uploadify({
			'auto': true,
			'uploader':   '<?php echo WP_UPLOADIFY_URL; ?>/uploadify.swf',
			'script':     '<?php echo WP_UPLOADIFY_URL; ?>/uploadify.php',
			'cancelImg':  '<?php echo WP_UPLOADIFY_URL; ?>cancel.png',
			'folder':     '<?php echo WP_UPLOAD_URL; ?>',
			'onComplete': function(event, queueID, fileObj)	{ 
				$(el).siblings('.uploaded').val("<?php echo WP_THEME_URL ?>/ci_panel/uploads/"+fileObj.name);
				$(el).siblings('.up-preview').html("<img src='"+ "<?php echo WP_THEME_URL ?>/ci_panel/uploads/" + fileObj.name +"' />");
			}
		});
	});
	
});
</script>
<div class="wrap">
	<h2><?php _e('Euclides Settings', CI_DOMAIN); ?></h2>

	<div id="ci_panel">
		<form method="post" action="options.php" id="theform" enctype="multipart/form-data">
			<?php 
				 settings_fields('ci-settings-group');
				 $theme_options = get_option(THEME_OPTIONS); 
			?>
			<div id="ci_header"><img src="<?php bloginfo('stylesheet_directory') ?>/ci_panel/img/logo.png" /></div>
	
			<?php if (isset($_POST['reset'])) { ?> <div class="resetbox"><?php _e('Settings reset!', CI_DOMAIN); ?></div> <?php } ?>
			<div class="success"></div>
	 
			<div class="ci_save"><input type="submit" class="button-primary save" value="<?php _e('Save Changes', CI_DOMAIN) ?>" /></div>
			<div id="ci_main" class="group">

				<?php
					// Set the panel tabs here, in key value pairs. They are displayed in the order they are defined.
					// key is the filename without the .php extension.
					// value is the tabs' title.
					$paneltabs = array(
						'site_options' => __('Site Options', CI_DOMAIN),
						'background_options' => __('Background Options', CI_DOMAIN),
						'display_options' => __('Display Options', CI_DOMAIN),
						'pagination_options' => __('Pagination Options', CI_DOMAIN),
						'archive_options' => __('Archive Options', CI_DOMAIN),
						'google_options' => __('Google Options', CI_DOMAIN),
						'feedburner_options' => __('FeedBurner Options', CI_DOMAIN),
						'buysellads_options' => __('BuySellAds Options', CI_DOMAIN)
					);
				?>
				
				<div id="ci_sidebar">
					<ul>
						<?php $tabNum = 1; ?>
						<?php foreach($paneltabs as $name => $title): ?>
							<?php if ($tabNum==1): ?>
								<li><a href="#tab<?php echo $tabNum; ?>" rel="tab<?php echo $tabNum; ?>" class="active"><?php echo $title ?></a></li>
							<?php else: ?>
								<li><a href="#tab<?php echo $tabNum; ?>" rel="tab<?php echo $tabNum; ?>"><?php echo $title ?></a></li>
							<?php endif; $tabNum++; ?>
						<?php endforeach; ?>
					</ul>
				</div><!-- /sidebar -->
				
				<div id="ci_options">
					<?php $tabNum = 1; ?>
					<?php foreach($paneltabs as $name => $title): ?>
						<?php if ($tabNum==1): ?>
							<div id="tab<?php echo $tabNum; ?>" class="tab one"><?php require('includes/'.$name.'.php'); ?></div>
						<?php else: ?>
							<div id="tab<?php echo $tabNum; ?>" class="tab"><?php require('includes/'.$name.'.php'); ?></div>
						<?php endif; $tabNum++; ?>
					<?php endforeach; ?>
				</div><!-- #ci_options -->
	
			</div><!-- #ci_main -->
			<div class="ci_save"><input type="submit" class="button-primary save" value="<?php _e('Save Changes', CI_DOMAIN); ?>" /></div>
		</form>
	</div><!-- #ci_panel -->
  
	<div id="ci-reset-box">
		<form method="post" action="">
			<input type="hidden" name="reset" value="reset" />
			<input type="submit" class="button" value="<?php _e('Reset Settings', CI_DOMAIN) ?>" onclick="return confirm('Are you sure? All settings will be lost!'); " />
		</form>
	</div>
</div><!-- wrap -->
<?php } 



function ci_options_validate($set)
{
	$set = (array)$set;
	foreach ($set as &$item)
	{
		if (is_string($item))
			$item = htmlentities($item,ENT_COMPAT,'UTF-8',false);
	}
	
	return $set;
}



function load_ci_defaults()
{
	global $load_defaults;
	$load_defaults = TRUE;

	$path = dirname(__FILE__).'/includes';
	
	if ($handle = opendir($path)) {
	    while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
	        	$file_info = pathinfo($path.'/'.$file);
	        	if($file_info['extension']=='php')
	        		include($path.'/'.$file);
	        }
	    }
		closedir($handle);
	}

	$load_defaults = FALSE;
}



?>