<?php
/**
 * Slickr Flickr
 *
 * Display a Flickr slideshow or a gallery in a post or widget
 *
 * @param id -> the Flickr ID of user
 * @param group -> set to y if the Flickr ID is the id of a group and not a user - default is n
 * @param use_key -> set to y to force use of API key - default is n
 * @param api_key -> 32 character alphanumeric API key
 * @param search -> photos, groups, friends, favorites, sets - default is photos
 * @param tag -> identifies what photos to select
 * @param tagmode -> set to ANY for fetching photos with different tags - default is ALL
 * @param set -> used in searching sets
 * @param license -> used to filter photos according to the license, 1-7
 * @param date_type -> (date) taken or upload 
 * @param date -> get photos for this date
 * @param after -> get photos on or after this date
 * @param before -> get photos on or before this date
 * @param items -> maximum number photos to display in the gallery or slideshow - default is 20
 * @param type -> gallery, galleria or slideshow - default is gallery
 * @param captions -> whether captions are on or off - default is on
 * @param delay -> delay in seconds between each image in the slideshow - default is 5
 * @param transition -> slideshow transition - default is 0.5
 * @param start -> first slide in the slideshow - default is 1
 * @param autoplay -> on or off - default is on
 * @param pause -> on or off - default is off 
 * @param orientation -> landscape or portrait - default is landscape
 * @param size -> small, medium, m640, small, large, original - default is medium
 * @param width -> width of slideshow
 * @param height -> height of slideshow
 * @param bottom -> margin at the bottom of the slideshow/gallery/galleria
 * @param thumbnail_size -> square, thumbnail, small - default is square
 * @param thumbnail_scale -> scaling factor - default is 100
 * @param thumbnail_captions -> on or off - default is off 
 * @param thumbnail_border -> alternative hightlight color for thumbnail
 * @param photos_per_row -> maximum number number of thumbnails in a gallery row
 * @param align -> left, right or center
 * @param border -> whether slideshow border is on or off - default is off
 * @param descriptions -> show descriptions beneath title caption - default is off
 * @param flickr_link -> include a link to the photo on Flickr on the lightbox - default is off
 * @param link -> url to visit on clicking slideshow
 * @param target -> name of window for showing the slideshow link url - default is the same window: _self
 * @param attribution -> credit the photographer
 * @param nav -> galleria navigation - none, above, below (if not supplied navigation is both above and below)
 * @param sort -> sort photos by date, title or description
 * @param direction -> sort ascending or descending 
 * @param per_page -> photos per page 
 * @param page -> page number  
 * @param restrict -> filter results based on orientation  
*/
class SlickrFlickrUtils {

	protected static $standard_options_name = 'slickr_flickr_options';
	protected static $standard_options_prefix = 'flickr_';
	protected static $standard_options  = array();
	protected static $defaults = array(
	    'id' => '',
	    'group' => 'n',
	    'use_key' => '',
	    'api_key' => '',
	    'search' => 'photos',
	    'tag' => '',
	    'tagmode' => '',
	    'set' => '',
	    'gallery' => '',
	    'license' => '',
	    'date_type' => '',
	    'date' => '',
	    'before' => '',
	    'after' => '',
	    'cache' => 'on',
	    'items' => '20',
	    'type' => 'gallery',
	    'captions' => 'on',
	    'lightbox' => 'sf-lbox-manual',
	    'galleria'=> 'galleria-latest',
	    'galleria_theme'=> 'classic',
    	'galleria_themes_folder'=> 'galleria/themes',
    	'galleria_options' => '',
    	'options' => '',
    	'delay' => '5',
    	'transition' => '0.5',
    	'start' => '1',
    	'autoplay' => 'on',
    	'pause' => '',
    	'orientation' => 'landscape',
    	'size' => 'medium',
    	'width' => '',
    	'height' => '',
    	'bottom' => '',
    	'thumbnail_size' => '',
    	'thumbnail_scale' => '',
    	'thumbnail_captions' => '',
    	'thumbnail_border' => '',
    	'photos_per_row' => '',
    	'align' => '',
    	'border' => '',
    	'descriptions' => '',
    	'ptags' => '',
    	'flickr_link' => '',
    	'link' => '',
    	'target' => '_self',
    	'attribution' => '',
    	'nav' => '',
    	'sort' => '',
    	'direction' => '',
    	'per_page' => 50,
    	'page' => 1,
    	'restrict' => '',
    	'scripts_in_footer' => false
    );

	static function save_options($new_options) {
		$updated = update_option(self::$standard_options_name,$new_options);
		if ($updated) self::get_options(false);
		return $updated;
	}

	static function get_options ($cache = true) {
	   if ($cache && (count(self::$standard_options) > 0)) return self::$standard_options;
	
	   $flickr_options = array();
	   $options = get_option(self::$standard_options_name);
	   if (empty($options)) {
	      self::$standard_options = self::$defaults;
	   } else {
	     foreach ($options as $key => $option) {
	       if (isset($options[$key]) && strpos($key,self::$standard_options_prefix)==0)  $flickr_options[substr($key,7)] = $option;
	     }
	     self::$standard_options = shortcode_atts( self::$defaults, $flickr_options);
	   }
	   return self::$standard_options;
	}

	static function get_option($option_name) {
    	$options = self::get_options();
    	if ($option_name && $options && array_key_exists($option_name,$options))
        	return $options[$option_name];
    	else
        	return false;
	}

	static function scripts_in_footer() {
    	return self::get_option('scripts_in_footer');
	}

	static function clear_rss_cache() {
    	global $wpdb, $table_prefix;
    	$prefix = $table_prefix ? $table_prefix : "wp_";
    	$sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE 'rss_%' and LENGTH(option_name) IN (36, 39)";
    	$wpdb->query($sql);
	}

	static function clear_rss_cache_transient() {
    	global $wpdb, $table_prefix;
    	$prefix = $table_prefix ? $table_prefix : "wp_";
    	$sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE '_transient_feed_%' or option_name LIKE '_transient_rss_%' or option_name LIKE '_transient_timeout_%'";
    	$wpdb->query($sql);
	}

	static function clear_cache() {
    	self::clear_rss_cache();
    	self::clear_rss_cache_transient();
	}

}
?>