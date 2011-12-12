<?php
/**
 * @package MediaElementJS
 * @version 2.2.5
 */
 
/*
Plugin Name: MediaElement.js - HTML5 Audio and Video
Plugin URI: http://mediaelementjs.com/
Description: Video and audio plugin for WordPress built on MediaElement.js HTML5 video and audio player library. Embeds media in your post or page using HTML5 with Flash or Silverlight fallback support for non-HTML5 browsers. Video support: MP4, Ogg, WebM, WMV. Audio support: MP3, WMA, WAV
Author: John Dyer
Version: 2.2.5
Author URI: http://j.hn/
License: GPLv3, MIT
*/

/*
Adapted from: http://videojs.com/ plugin
*/

$mediaElementPlayerIndex = 1;

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'mejs_install'); 

function mejs_install() {
	add_option('mep_video_skin', '');
	
	add_option('mep_default_video_height', 270);
	add_option('mep_default_video_width', 480);
	add_option('mep_default_video_type', '');
	
	add_option('mep_default_audio_height', 30);
	add_option('mep_default_audio_width', 400);
	add_option('mep_default_audio_type', '');
}

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'mejs_remove' );
function mejs_remove() {
	delete_option('mep_video_skin');

	delete_option('mep_default_video_height');
	delete_option('mep_default_video_width');
	delete_option('mep_default_video_type');	

	delete_option('mep_default_audio_height');
	delete_option('mep_default_audio_width');
	delete_option('mep_default_audio_type');
}

// create custom plugin settings menu
add_action('admin_menu', 'mejs_create_menu');

function mejs_create_menu() {

	//create new top-level menu
	add_options_page('MediaElement.js', 'MediaElement.js', 'administrator', __FILE__, 'mejs_settings_page');

	//call register settings function
	add_action( 'admin_init', 'mejs_register_settings' );
}


function mejs_register_settings() {
	//register our settings
	register_setting( 'mep_settings', 'mep_video_skin' );
	
	register_setting( 'mep_settings', 'mep_default_video_height' );
	register_setting( 'mep_settings', 'mep_default_video_width' );
	register_setting( 'mep_settings', 'mep_default_video_type' );

	register_setting( 'mep_settings', 'mep_default_audio_height' );
	register_setting( 'mep_settings', 'mep_default_audio_width' );
	register_setting( 'mep_settings', 'mep_default_audio_type' );
}


function mejs_settings_page() {
?>
<div class="wrap">
<h2>MediaElement.js HTML5 Player Options</h2>

<p>See <a href="http://mediaelementjs.com/">MediaElementjs.com</a> for more details on how the HTML5 player and Flash fallbacks work.</p>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>


	<h3 class="title"><span>Video Settings</span></h3>
		
	<table  class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="mep_default_video_width">Default Width</label>
			</th>
			<td >
				<input name="mep_default_video_width" type="text" id="mep_default_video_width" value="<?php echo get_option('mep_default_video_width'); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="mep_default_video_height">Default Height</label>
			</th>
			<td >
				<input name="mep_default_video_height" type="text" id="mep_default_video_height" value="<?php echo get_option('mep_default_video_height'); ?>" />
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="mep_default_video_type">Default Type</label>
			</th>
			<td >
				<input name="mep_default_video_type" type="text" id="mep_default_video_type" value="<?php echo get_option('mep_default_video_type'); ?>" /> <span class="description">such as "video/mp4"</span>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="mep_video_skin">Video Skin</label>
			</th>
			<td >
				<select name="mep_video_skin" id="mep_video_skin">
					<option value="" <?php echo (get_option('mep_video_skin') == '') ? ' selected' : ''; ?>>Default</option>
					<option value="wmp" <?php echo (get_option('mep_video_skin') == 'wmp') ? ' selected' : ''; ?>>WMP</option>
					<option value="ted" <?php echo (get_option('mep_video_skin') == 'ted') ? ' selected' : ''; ?>>TED</option>
				</select>
			</td>
		</tr>				
	</table>

	<h3 class="title"><span>Audio Settings</span></h3>
	

	<table  class="form-table">
		<tr valign="top">
		<tr valign="top">
			<th scope="row">
				<label for="mep_default_audio_width">Default Width</label>
			</th>
			<td >
				<input name="mep_default_audio_width" type="text" id="mep_default_audio_width" value="<?php echo get_option('mep_default_audio_width'); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="mep_default_audio_height">Default Height</label>
			</th>
			<td >
				<input name="mep_default_audio_height" type="text" id="mep_default_audio_height" value="<?php echo get_option('mep_default_audio_height'); ?>" />
			</td>
		</tr>			
			<th scope="row">
				<label for="mep_default_audio_type">Default Type</label>
			</th>
			<td >
				<input name="mep_default_audio_type" type="text" id="mep_default_audio_type" value="<?php echo get_option('mep_default_audio_type'); ?>" /> <span class="description">such as "audio/mp3"</span>
			</td>
		</tr>			
	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="mep_default_video_width,mep_default_video_height,mep_default_video_type,mep_default_audio_type,mep_default_audio_width,mep_default_audio_height,mep_video_skin" />

	<p>
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

</div>



</form>
</div>
<?php
}


define('MEDIAELEMENTJS_DIR', WP_PLUGIN_URL.'/media-element-html5-video-and-audio-player/mediaelement/');
// Javascript 
function mejs_add_scripts(){
    if (!is_admin()){
        // the scripts
        wp_enqueue_script("mediaelementjs-scripts", MEDIAELEMENTJS_DIR ."mediaelement-and-player.min.js", array('jquery'), "2.1.3", false);
    }
}
add_action('wp_print_scripts', 'mejs_add_scripts');

// css
function mejs_add_styles(){
    if (!is_admin()){
        // the style
        wp_enqueue_style("mediaelementjs-styles", MEDIAELEMENTJS_DIR ."mediaelementplayer.css");
        
        if (get_option('mep_video_skin') != '') {
			wp_enqueue_style("mediaelementjs-skins", MEDIAELEMENTJS_DIR ."mejs-skins.css");
		}
    }
}
add_action('wp_print_styles', 'mejs_add_styles');

function mejs_add_header(){
/*

	$dir = WP_PLUGIN_URL.'/media-element-html5-video-and-audio-player/mediaelement/';
	
	echo <<<_end_
<link rel="stylesheet" href="{$dir}mediaelementplayer.min.css" type="text/css"  />
<script src="{$dir}mediaelement-and-player.min.js" type="text/javascript"></script>
_end_;
*/

}

// If this happens in the <head> tag it fails in iOS. Boo.
function mejs_add_footer(){
/*
	$defaultVideoWidth = get_option('mep_default_video_width');
	$defaultVideoHeight = get_option('mep_default_video_height');

	echo <<<_end_
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('video[class=mep],audio[class=mep]').mediaelementplayer({defaultVideoWidth:{$defaultVideoWidth},defaultVideoHeight:{$defaultVideoHeight}});
});
</script>
_end_;
*/
}

add_action('wp_head','mejs_add_header');
add_action('wp_footer','mejs_add_footer');

function mejs_media_shortcode($tagName, $atts){

	global $mediaElementPlayerIndex;	
	$dir = WP_PLUGIN_URL.'/media-element-html5-video-and-audio-player/mediaelement/';

	extract(shortcode_atts(array(
		'src' => '',  
		'mp4' => '',
		'mp3' => '',
		'wmv' => '',    
		'webm' => '',
		'flv' => '',
		'ogg' => '',
		'poster' => '',
		'width' => get_option('mep_default_'.$tagName.'_width'),
		'height' => get_option('mep_default_'.$tagName.'_height'),
		'type' => get_option('mep_default_'.$tagName.'_type'),
		'preload' => 'none',
		'skin' => get_option('mep_video_skin'),
		'autoplay' => '',
		'loop' => '',
		
		// old ones
		'duration' => 'true',
		'progress' => 'true',
		'fullscreen' => 'true',
		'volume' => 'true',
		
		// captions
		'captions' => '',
		'captionslang' => 'en'
	), $atts));

	if ($type) {
		$type_attribute = 'type="'.$type.'"';
	}

/*
	if ($src) {
		$src_attribute = 'src="'.htmlspecialchars($src).'"';
		$flash_src = htmlspecialchars($src);
	}
*/

	if ($src) {
	
		// does it have an extension?
		if (substr($src, strlen($src)-4, 1)=='.') {
			$src_attribute = 'src="'.htmlspecialchars($src).'"';
			$flash_src = htmlspecialchars($src);
		} else {
			
			// for missing extension, we try to find all possible files in the system
			
			if (substr($src, 0, 4)!='http') 
				$filename = WP_CONTENT_DIR . substr($src, strlen(WP_CONTENT_DIR)-strrpos(WP_CONTENT_DIR, '/'));
			else 
				$filename = WP_CONTENT_DIR . substr($src, strlen(WP_CONTENT_URL));

			if ($tagName == 'video') {
				// MP4
				if (file_exists($filename.'.mp4')) {
					$mp4=$src.'.mp4';
				} elseif (file_exists($filename.'.m4v')) {
					$mp4=$src.'.m4v';
				}

				// WEBM
				if (file_exists($filename.'.webm')) {
					$webm=$src.'.webm';
				}

				// OGG
				if (file_exists($filename.'.ogg')) {
					$ogg=$src.'.ogg';
				} elseif (file_exists($filename.'.ogv')) {
					$ogg=$src.'.ogv';
				}

				// FLV
				if (file_exists($filename.'.flv')) {
					$flv=$src.'.flv';
				}

				// WMV
				if (file_exists($filename.'.wmv')) {
					$wmv=$src.'.wmv';
				}				
				
				// POSTER
				if (file_exists($filename.'.jpg')) {
					$poster=$src.'.jpg';
				}
				
			} elseif ($tagName == 'audio') {
				
				// MP3
				if (file_exists($filename.'.mp3')) {
					$mp3=$src.'.mp3';
				}
				
				// OGG
				if (file_exists($filename.'.ogg')) {
					$ogg=$src.'.ogg';
				} elseif (file_exists($filename.'.oga')) {
					$ogg=$src.'.oga';
				}				
				
			}
		}
	}	
	
	

	if ($mp4) {
		$mp4_source = '<source src="'.htmlspecialchars($mp4).'" type="'.$tagName.'/mp4" />';
		$flash_src = htmlspecialchars($mp4);
	}
	
	if ($mp3) {
		$mp3_source = '<source src="'.htmlspecialchars($mp3).'" type="'.$tagName.'/mp3" />';
		$flash_src = htmlspecialchars($mp3);
	}	

	if ($webm) {
		$webm_source = '<source src="'.htmlspecialchars($webm).'" type="'.$tagName.'/webm" />';
	}

	if ($ogg) {
		$ogg_source = '<source src="'.htmlspecialchars($ogg).'" type="'.$tagName.'/ogg" />';
	}
	
	if ($flv) {
		$flv_source = '<source src="'.htmlspecialchars($flv).'" type="'.$tagName.'/flv" />';
	}	

	if ($wmv) {
		$wmv_source = '<source src="'.htmlspecialchars($wmv).'" type="'.$tagName.'/wmv" />';
	}	


	if ($captions) {
		$captions_source = '<track src="'.$captions.'" kind="subtitles" srclang="'.$captionslang.'" />';
	}  

	if ($width && $tagName == 'video') {
		$width_attribute = 'width="'.$width.'"';
	}

	if ($height && $tagName == 'video') {
		$height_attribute = 'height="'.$height.'"';
	}    

	if ($poster) {
		$poster_attribute = 'poster="'.htmlspecialchars($poster).'"';
	}

	if ($preload) {
		$preload_attribute = 'preload="'.$preload.'"';
	}

	if ($autoplay) {
		$autoplay_attribute = 'autoplay="'.$autoplay.'"';
	}

	if ($loop) {
		$loop_option = ', loop: ' . $loop;
	}

	// CONTROLS
	$controls_option = ",features: ['playpause'";
	if ($progress == 'true')
		$controls_option .= ",'current','progress'";
	if ($duration == 'true')
		$controls_option .= ",'duration'";
	if ($volume == 'true')
		$controls_option .= ",'volume'";
	$controls_option .= ",'tracks'";
	if ($fullscreen == 'true')
		$controls_option .= ",'fullscreen'";		
	$controls_option .= "]";
	
	// AUDIO SIZE
	$audio_size = '';
	if ($tagName == 'audio') {
		$audio_size = ',audioWidth:'.$width.',audioHeight:'.$height;
	}
	
	// VIDEO class (skin)
	$video_skin_attribute = '';
	if ($skin != '' && $tagName == 'video') {
		$video_skin_attribute = 'class="mejs-'.$skin.'"';
	}

	$mediahtml .= <<<_end_
	<{$tagName} id="wp_mep_{$mediaElementPlayerIndex}" {$src_attribute} {$type_attribute} {$width_attribute} {$height_attribute} {$poster_attribute} controls="controls" {$preload_attribute} {$autoplay_attribute} $video_skin_attribute>
		{$mp4_source}
		{$mp3_source}
		{$webm_source}
		{$flv_source}
		{$wmv_source}
		{$ogg_source}
		{$captions_source}
		<object width="{$width}" height="{$height}" type="application/x-shockwave-flash" data="{$dir}flashmediaelement.swf">
			<param name="movie" value="{$dir}flashmediaelement.swf" />
			<param name="flashvars" value="controls=true&amp;file={$flash_src}" />			
		</object>		
	</{$tagName}>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#wp_mep_$mediaElementPlayerIndex').mediaelementplayer({
		m:1
		{$loop_option}
		{$controls_option}
		{$audio_size}
	});
});
</script>

_end_;

	$mediaElementPlayerIndex++;

  return $mediahtml;
}



function mejs_audio_shortcode($atts){
	return mejs_media_shortcode('audio',$atts);
}
function mejs_video_shortcode($atts){
	return mejs_media_shortcode('video',$atts);
}

add_shortcode('audio', 'mejs_audio_shortcode');
add_shortcode('mejsaudio', 'mejs_audio_shortcode');
add_shortcode('video', 'mejs_video_shortcode');
add_shortcode('mejsvideo', 'mejs_video_shortcode');	

function mejs_init() {
    
	wp_enqueue_script( 'jquery' );
    
}    
 
add_action('init', 'mejs_init');
	
?>
