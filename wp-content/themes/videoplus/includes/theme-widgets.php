<?php

/*---------------------------------------------------------------------------------*/
/* Loads all the .php files found in /includes/widgets/ directory */
/*---------------------------------------------------------------------------------*/

	$preview_template = _preview_theme_template_filter();

	if(!empty($preview_template)){
		$tj_widgets_dir = WP_CONTENT_DIR . "/themes/".$preview_template."/includes/widgets/";
	} else {
    	$tj_widgets_dir = WP_CONTENT_DIR . "/themes/".get_option('template')."/includes/widgets/";
    }
    
    if (@is_dir($tj_widgets_dir)) {
		$tj_widgets_dh = opendir($tj_widgets_dir);
		while (($tj_widgets_file = readdir($tj_widgets_dh)) !== false) {
  	
			if(strpos($tj_widgets_file,'.php') && $tj_widgets_file != "widget-blank.php") {
				include_once($tj_widgets_dir . $tj_widgets_file);
			
			}
		}
		closedir($tj_widgets_dh);
	}
	
	
/*---------------------------------------------------------------------------------*/
/* Deregister Default Widgets */
/*---------------------------------------------------------------------------------*/
if (!function_exists('tj_deregister_widgets')) {
	function tj_deregister_widgets(){
	    unregister_widget('WP_Widget_Search');         
	}
}
add_action('widgets_init', 'tj_deregister_widgets');  


?>