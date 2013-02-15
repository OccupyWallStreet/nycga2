<?php

define('SLICKR_FLICKR_PUBLIC', 'slickr-flickr-public');
require_once(dirname(__FILE__).'/slickr-flickr-display.php');

class slickr_flickr_public {

	static private $jquery_data; //cache all jquery and write in one chunk
	
	static private $galleria_themes; //galleria themes
		
	static public function add_jquery($line) {
		self::$jquery_data[]= $line;
	}

	static public function add_galleria_theme($theme) {
		self::$galleria_themes[]= $theme;
	}

	static function start_show() {
		$in_footer = SlickrFlickrUtils::scripts_in_footer();
		if ($in_footer && (count(self::$jquery_data)==0)) return;
		echo ('<script type="text/javascript">'."\r\n");
    	self::load_galleria_theme($in_footer);
    	echo ('jQuery.noConflict(); jQuery(document).ready(function() {'."\r\n");
    	if (count(self::$jquery_data)>0) foreach (self::$jquery_data as $data) echo ($data."\r\n");
    	echo("slickr_flickr_start();\r\n");
    	echo ("});\r\n");
 		echo ("</script>\r\n");
	}

	static function load_galleria_theme($in_footer = false) {
		$galleria = SlickrFlickrUtils::get_option('galleria');
	    if ('galleria-latest'==$galleria) {
			if (count(self::$galleria_themes) > 0) 
				$theme = self::$galleria_themes[0]; //use the first theme
			else
				$theme = $in_footer ? '' : SlickrFlickrUtils::get_option('galleria_theme');
			if (!empty($theme)) {
				if ('classic'==$theme) 
    	    		$themepath = SLICKR_FLICKR_PLUGIN_URL. '/galleria/themes/classic/galleria.classic.js';
				else  //premium themes are located outside the plugin folder
    	    		$themepath = site_url( SlickrFlickrUtils::get_option('galleria_themes_folder'). '/' . 
    	    			$theme .'/galleria.'. $theme . '.min.js');
    	    	echo 'Galleria.loadTheme("'.$themepath.'");'."\r\n";  //load galleria theme
			}
		}
	}

	static function init() {
		self::$galleria_themes=array(); //initialize galleria themes
		self::$jquery_data=array(); //initialize jquery config

	    $path = SLICKR_FLICKR_PLUGIN_URL;
	    $options = SlickrFlickrUtils::get_options();
	    $footer_scripts = SlickrFlickrUtils::scripts_in_footer() ;

    	$deps = array('jquery');
    	switch ($options['lightbox']) {
    		 case 'sf-lbox-manual':
		     case 'sf-lbox-auto':  {
		        wp_enqueue_style('slickr-flickr-lightbox', $path."/lightbox/lightbox.css", array(),"0.5");
		        wp_enqueue_script('slickr-flickr-lightbox', $path."/lightbox/jquery.lightbox.js", array('jquery'),"0.5",$footer_scripts);
		        $deps[] = 'slickr-flickr-lightbox';
        	}
    		case 'thickbox': { //preinstalled by wordpress but needs to be activated
    		   wp_enqueue_style('thickbox');
    		   wp_enqueue_script('thickbox');
    		   $deps[] = 'thickbox';
 			   break;
    		}
    		default: { break; } //use another lightbox plugin
    	}
     
		$gname = 'galleria';
    	$galleria = array_key_exists('galleria',$options) ? $options['galleria'] : 'galleria-latest';
    	$gfolder = $path . "/galleria/";    

	    switch ($galleria) {
		    case 'galleria-none': { break; }
		    case 'galleria-original':
		    case 'galleria-1.0': {
    			wp_enqueue_style($gname, $gfolder.'galleria-1.0.css',array(),'1.0');
    			wp_enqueue_script($gname, $gfolder.'galleria-1.0.noconflict.js', array('jquery'), SLICKR_FLICKR_VERSION, $footer_scripts);
        		break;
			}
		    default: {
				$gversion = '1.2.8';
				$gscript = $gfolder . 'galleria-'.$gversion.'.min.js';
		    	wp_enqueue_script($gname, $gscript, array('jquery'), $gversion, $footer_scripts); //enqueue loading of core galleria script
    		    break;
    		}
		}
    	wp_enqueue_style('slickr-flickr', $path.'/slickr-flickr.css', array(), SLICKR_FLICKR_VERSION);
    	wp_enqueue_script('slickr-flickr', $path.'/slickr-flickr.js', $deps, SLICKR_FLICKR_VERSION, $footer_scripts);
    	add_filter($footer_scripts ? 'print_footer_scripts' : 'print_head_scripts' , array('slickr_flickr_public','start_show'),100); //start slickr flickr last
		if ($footer_scripts) add_action('wp_footer', array('slickr_flickr_public','dequeue_redundant_scripts'),1);

	}

	static function dequeue_redundant_scripts() {
		if (count(self::$galleria_themes)==0) wp_dequeue_script('galleria'); 
		if (count(self::$jquery_data)==0) {  
			wp_dequeue_script('slickr-flickr'); 
			wp_dequeue_script('slickr-flickr-lightbox');
		}
	}
	
	static function display($attr) {
		$disp = new slickr_flickr_display();
		return $disp->show($attr);
	}
}
add_action('init', array('slickr_flickr_public','init'));
add_shortcode('slickr-flickr', array('slickr_flickr_public','display'));
add_filter('widget_text', 'do_shortcode', 11);
?>