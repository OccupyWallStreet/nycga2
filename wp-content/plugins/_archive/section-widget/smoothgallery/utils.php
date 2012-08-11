<?php

#
# WordPress SmoothGallery plugin
# Copyright (C) 2008-2009 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#

/**
 * Returns an array with the parameters for the SmoothGallery.
 *
 * If the parameter $defaultValues is set to true the array contains just the
 * keys and the corresponding default values. If it's set to false it contains
 * the keys along with their meta-keys which may be used in the custom field.
 */
function get_default_smoothgallery_parameters($defaultValues = true) {
	# copied from jd.gallery.js
	# -> contains the default values and the keys that may be used along with the custom field
	$parameters = array('showArrows' => array('true', 'a', 'arrows', 'showArrows'),
	                    'showCarousel' => array('false', 'c', 'carousel', 'showCarousel'), # true by default
	                    'showInfopane' => array('true', 'i', 'info', 'infoPane', 'showInfopane'),
	                    'embedLinks' => array('true', 'l', 'links', 'embedLinks'),
	                    'timed' => array('false', 't', 'timed'),
	                    'delay' => array(9000, 'd', 'delay'),
	                    'fadeDuration' => array(500, 'fadeDuration'),
	                    'preloader' => array('true', 'preloader'),
	                    'preloaderImage' => array('true', 'preloaderImage'),
	                    'preloaderErrorImage' => array('true', 'preloaderErrorImage'),
	                    /* Data retrieval */
	                    'manualData' => array('[]', 'manualData'),
	                    'populateFrom' => array('false', 'populateFrom'),
	                    'populateData' => array('true', 'populateData'),
	                    'destroyAfterPopulate' => array('true', 'destroyAfterPopulate'),
	                    'elementSelector' => array('"div.imageElement"', 'elementSelector'),
	                    'titleSelector' => array('"h3"', 'titleSelector'),
	                    'subtitleSelector' => array('"p"', 'subtitleSelector'),
	                    'linkSelector' => array('"a.open"', 'linkSelector'),
	                    'imageSelector' => array('"img.full"', 'imageSelector'),
	                    'thumbnailSelector' => array('"img.thumbnail"', 'thumbnailSelector'),
	                    'defaultTransition' => array('"fade"', 'transition', 'defaultTransition'),
	                    /* InfoPane options */
	                    'slideInfoZoneOpacity' => array(0.7, 'slideInfoZoneOpacity'),
	                    'slideInfoZoneSlide' => array('true', 'slideInfoZoneSlide'),
	                    /* Carousel options */
	                    'carouselMinimizedOpacity' => array(0.4, 'carouselMinimizedOpacity'),
	                    'carouselMinimizedHeight' => array(20, 'carouselMinimizedHeight'),
	                    'carouselMaximizedOpacity' => array(0.9, 'carouselMaximizedOpacity'),
	                    'thumbHeight' => array(75, 'thumbHeight'),
	                    'thumbWidth' => array(100, 'thumbWidth'),
	                    'thumbSpacing' => array(10, 'thumbSpacing'),
	                    'thumbIdleOpacity' => array(0.2, 'thumbIdleOpacity'),
	                    'textShowCarousel' => array('"Pictures"', 'textShowCarousel'),
	                    'showCarouselLabel' => array('true', 'showCarouselLabel'),
	                    'thumbCloseCarousel' => array('true', 'thumbCloseCarousel'),
	                    'useThumbGenerator' => array('false', 'useThumbGenerator'),
	                    'thumbGenerator' => array('"resizer.php"', 'thumbGenerator'),
	                    'useExternalCarousel' => array('false', 'useExternalCarousel'),
	                    'carouselElement' => array('false', 'carouselElement'),
	                    'carouselHorizontal' => array('true', 'carouselHorizontal'),
	                    'activateCarouselScroller' => array('true',' activateCarouselScroller'),
	                    'carouselPreloader' => array('true', 'carouselPreloader'),
	                    'textPreloadingCarousel' => array('"Loading..."', 'textPreloadingCarousel'),
	                    /* CSS Classes */
	                    'baseClass' => array('jdGallery', 'baseClass'),
	                    'withArrowsClass' => array('withArrows', 'withArrowsClass'),
	                    /* Plugins: HistoryManager */
	                    'useHistoryManager' => array('false', 'useHistoryManager'),
	                    'customHistoryKey' => array('false', 'customHistoryKey'),
	                    /* Plugins: ReMooz */
	                    'useReMooz' => array('false', 'r', 'remooz', 'useReMooz'));

	# extra parameters (e.g. for the CSS)
	$parameters['height'] = array(345, 'h', 'height');
	$parameters['width'] = array(460, 'w', 'width');
	$parameters['bordercolor'] = array('000', 'b', 'border', 'bordercolor');

	# return either the default values or the meta-keys
	$tmpParameters = array();
	foreach ($parameters as $key => $value) $tmpParameters[$key] = ($defaultValues == true) ? $value[0] : array_slice($value, 1);
	return $tmpParameters;
}


/**
 * Returns an array containing the names of some extra parameters that aren't
 * part of SmoothGallery's core parameters.
 *
 * Hint: if you add extra parameters to get_default_smoothgallery_parameters(),
 * don't forget to add them here too.
 */
function get_extra_smoothgallery_parameters() {
	return array('height', 'width', 'bordercolor');
}


/**
 * Attributes that may be used along with the shortcode.
 */
function get_smoothgallery_shortcode_atts() {
	return array('id', 'prefix', 'imgsize', 'iframebgcolor', 'dir', 'filter', 'randomize',
	             'flickrusername', 'flickrphotoset',
	             'picasaurl');
}


/**
 * Returns the parameters that may be used inside an iFrame. These options will
 * be used for the widget too.
 */
function get_smoothgallery_iframe_atts() {
	# In this order:
	# - Shortcode specials
	# - Flickr
	# - Picasa
	# - all the parameters
	$atts = array_merge(array('id' => NULL, 'prefix' => NULL, 'imgsize' => NULL, 'iframebgcolor' => NULL, 'dir' => NULL, 'filter' => NULL, 'randomize' => NULL),
	                    array('flickrusername' => NULL, 'flickrphotoset' => NULL),
	                    array('picasaurl' => NULL),
	                    get_default_smoothgallery_parameters());
	return $atts;
}


/**
 * Returns a list of those parameters that need quoting in the JavaScript, i.e.
 * defaultTransition = 'fade' instead of defaultTransition = fade.
 */
function get_smoothgallery_parameters_in_need_of_quoting() {
	return array('elementSelector', 'titleSelector', 'subtitleSelector', 'linkSelector', 'imageSelector',
	             'thumbnailSelector', 'defaultTransition', 'textShowCarousel', 'thumbGenerator', 'textPreloadingCarousel');
}


/**
 * Tries to fix the quoting for strings like:
 * - \"text\", \'text\'
 * - "text", 'text'
 * to just "text".
 */
function fix_quoting($value, $quote = '"') {
	foreach (array('\\"', '"', "\\'", "'") as $search) {
		$value = str_replace($search, '', $value);
	}
	return '"'.trim($value).'"';
}


/**
 * Returns the value from a string in the following format:
 * '<$param><delimiter><value>        [...]\n
 *  <$anotherparam><delimiter><value> [...]'
 * where delimiter will be either ':' or '='.
 *
 * @param string $data the string with the data
 * @param array $params the parameter we're searching for
 * @return mixed the value from the string, otherwise false
 */
function get_smoothgallery_parameter($data, $params) {
	$metas = explode("\n", $data);
	foreach ($metas as $meta) {
		foreach ($params as $param) {
			if (preg_match('/'.$param.' *[:=]{1} *(\S*)/i', $meta, $matches)) {
				if (empty($matches[1])) continue;
				return rtrim($matches[1]);
			}
		}
	}
	return false;
}


/**
 * Generates the SmoothGallery markup for an array of images.
 *
 * Expects $images to be of this structure:
 * Array
 * (
 *     [0] => stdClass Object
 *         (
 *             [description] => Some description
 *             [title] => Some title
 *             [caption] => Some caption
 *             [url] => http://example.com/wp-content/uploads/some-image.jpg
 *             [thumb] => http://example.com/wp-content/plugins/smoothgallery/extra/resizer.php?src=http://example.com/wp-content/uploads/some-image.jpg&amp;w=100&amp;h=75
 *             [link] => http://www.example.com/user-defined-url
 *             [alt] => Array
 *                 (
 *                     [0] => http://example.com/wp-content/uploads/some-image-150x150.jpg
 *                     [1] => http://example.com/wp-content/uploads/some-image-300x200.jpg
 *                 )
 * 
 *         )
 * [...]
 * )
 * The alternative images denoted by "alt" are optional.
 *
 * @returns false if the array is empty, otherwise the HTML markup
 */
function generate_markup($images, $imgsize = NULL) {
	if (empty($images) or ! is_array($images)) return false;

	$markup .= '<div id="myGallery">'."\n";
	foreach ($images as $image) {
		$imageSrc = $image->url;
		$link = (!empty($image->link)) ? $image->link : $image->url;

		# search for another image size
		$altImage = find_alternative_image($image->alt, $imgsize);
		if ($altImage !== false) $imageSrc = $altImage;

		$markup .= '<div class="imageElement">'."\n".
		           '<h3>'.$image->title.'</h3>'."\n".
		           '<p>'.$image->description.'</p>'."\n".
		           '<a href="'.$link.'" title="open image" class="open"></a>'."\n".
		           '<img src="'.$imageSrc.'" class="full" alt="'.$image->caption.'"/>'."\n".
		           ((ENABLE_GENERATED_THUMBNAILS == true) ? '<img src="'.$image->thumb.'" class="thumbnail" alt="'.$image->caption.'"/>'."\n" : '').
		           '</div>'."\n";
	}
	$markup .= '</div>';

	return $markup;
}


/**
 * Searches an image that matches the given dimensions.
 *
 * Comparison goes as follows:
 * - if the dimensions of $imgsize are equal to the ones of the alternative
 *   image, we'll use the alternative one instead
 * - if $imgsize doesn't match the dimensions of the alternative image we'll
 *   try to find the image with the closest dimensions possible
 *
 * @returns false in case no alternative image was found, otherwise alternative image
 */
function find_alternative_image($images, $imgsize) {
	if ($imgsize == NULL or empty($images) or !is_array($images))
		return false;

	preg_match('/[0-9]*x[0-9]*/', $imgsize, $matches);
	$imgsize_x = substr($matches[0], 0, stripos($matches[0], 'x'));
	$imgsize_y = substr($matches[0], stripos($matches[0], 'x') + 1, strlen($matches[0]));

	$possible_altImages = array();
	foreach ($images as $altImage) {
		# exact match found?
		# -> in this case we're done
		if (strpos($altImage, $imgsize) !== false)
			return $altImage;

		# in case $imgsize has no x and y
		if (empty($imgsize_x) or empty($imgsize_y))
			continue;

		# compare x/y from $imgsize with the dimensions of $altImage
		preg_match('/[0-9]*x[0-9]*/', $altImage, $matches);
		$altImage_x = substr($matches[0], 0, stripos($matches[0], 'x'));
		$altImage_y = substr($matches[0], stripos($matches[0], 'x') + 1, strlen($matches[0]));
		if ($imgsize_x == $altImage_x or $imgsize_y == $altImage_y)
			$possible_altImages[] = array($altImage_x, $altImage_y, $altImage);
	}

	if (empty($possible_altImages)) return false;
	foreach ($possible_altImages as $key => $value) {
		if ($imgsize_x == $value[0] or $imgsize_y == $value[1])
			return $value[2];
	}

	return false;
}


/**
 * Generates either the content for an iFrame or the tag itself that references
 * the iFrame.
 */
function generate_iframe($atts = NULL, $isTag = true) {
	# overwrite parameters with supplied $atts
	$parameters = get_smoothgallery_iframe_atts();
	foreach ($atts as $key => $value) {
		# since WP converts all atts to lowercase search for matching keys
		$realKey = $key;
		foreach ($parameters as $paramKey => $paramValue) {
			if (strtolower($key) == strtolower($paramKey)) {
				$realKey = $paramKey;
				break;
			}
		}
		$parameters[$realKey] = $value;
	}

	if ($isTag === true) {
		$defaults = get_default_smoothgallery_parameters();

		return '<iframe src="'.get_smoothgallery_action_url($parameters, SMOOTHGALLERY_ACTION_IFRAME).'&amp;'.generate_smoothgallery_query_string($parameters, $defaults, array('picasaurl')).
		       '" width="'.($parameters['width'] + ADD_PIXELS_TO_IFRAME).
		       '" height="'.($parameters['height'] + ADD_PIXELS_TO_IFRAME).
		       '" frameborder="0"></iframe>';
	}

	return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n".
	       '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n".
	       "<head>\n".
	       "<title>SmoothGallery</title>\n".
	       get_smoothgallery_css($parameters).
	       '<style type="text/css">'."\n".
	       "body { margin: 0px; padding: 0px; ".
	       "background: ".((strlen($parameters['iframebgcolor']) == 0) ? '#FFF' : $parameters['iframebgcolor'])."; }\n".
	       "</style>\n".
	       "</head>\n\n".
	       "<body>\n".
	       generate_markup(get_smoothgallery_images($parameters['id'], $parameters['dir'], $parameters['filter'], $parameters['randomize'], $parameters['flickrusername'], $parameters['flickrphotoset'], $parameters['picasaurl']), $parameters['imgsize']).
	       "\n\n".
	       get_smoothgallery_js($parameters).
	       "\n</body>".
	       "\n</html>";
}


/**
 * Returns a link to our stylesheet.
 */
function get_smoothgallery_css($parameters = NULL) {
	require_once('config.php');
	$parameters[SMOOTHGALLERY_ACTION_CSS_TYPE] = '1'; # xxx: hack - needs improvement
	$css = '<link rel="stylesheet" type="text/css" href="'.get_smoothgallery_action_url($parameters, SMOOTHGALLERY_ACTION_CSS).'" />'."\n";
	if (SMOOTHGALLERY_VERSION != '') {
		$parameters[SMOOTHGALLERY_ACTION_CSS_TYPE] = '2'; # xxx: hack - needs improvement
		$css .= '<link rel="stylesheet" type="text/css" href="'.get_smoothgallery_action_url($parameters, SMOOTHGALLERY_ACTION_CSS).'" />'."\n";
	}
	return $css;
}


/**
 * Returns the JavaScript.
 */
function get_smoothgallery_js($parameters) {
	require_once('config.php');
	$url = get_smoothgallery_url($parameters);
	$sg_version = ((SMOOTHGALLERY_VERSION != '') ? '.'.SMOOTHGALLERY_VERSION : '');
	$moo_version = ((MOOTOOLS_VERSION != '') ? '.'.MOOTOOLS_VERSION : '');

	$result = '<script src="'.$url.'/scripts/mootools'.$moo_version.'.js" type="text/javascript"></script>'."\n".
	          '<script src="'.$url.'/scripts/jd.gallery'.$sg_version.'.js" type="text/javascript"></script>'."\n".
	          ((ENABLE_TRANSITIONS) ? '<script src="'.$url.'/scripts/jd.gallery.transitions.js" type="text/javascript"></script>'."\n" : '').
	          ((SMOOTHGALLERY_VERSION != '') ? '<script src="'.$url.'/scripts/ReMooz.js" type="text/javascript"></script>'."\n" : '').
	          '<script type="text/javascript">'."\n".
	          'function startGallery() {'."\n".
	          "\tvar myGallery = new gallery($('myGallery'), {\n";

	$defaults = get_default_smoothgallery_parameters();
	$jsparams = "\t\t";
	foreach ($parameters as $key => $value) {
		# skip these extra keys
		if (in_array($key, get_extra_smoothgallery_parameters())) continue;
		if (in_array($key, get_smoothgallery_shortcode_atts())) continue;
		# deactivates the carousel by default (little hack)
		if ($key == 'showCarousel' and $value == 'false') $jsparams .= "$key: $value, ";
		# correct quoting
		if (in_array($key, get_smoothgallery_parameters_in_need_of_quoting())) $value = fix_quoting($value);
		# include a key and its value if it's different from the defaults
		if ($defaults[$key] != $value) $jsparams .= "$key: $value, ";
	}
	$result .= rtrim($jsparams, ' ,')."\n".
	           "\t})\n".
	           "}\n".
	           "window.addEvent('domready', startGallery);\n".
	           '</script>';

	return $result;
}


/**
 * Returns a URL that calls the smoothgallery.php script with some parameters.
 * Used to generate content like JavaScript, CSS or the content for an iFrame.
 */
function get_smoothgallery_action_url($parameters, $action) {
	if (empty($parameters) or !is_array($parameters)) return '';
	if ($action != SMOOTHGALLERY_ACTION_CSS and $action != SMOOTHGALLERY_ACTION_IFRAME) return 'Unknown action!';
	$url = get_smoothgallery_url($parameters);
	$extraParameters = ($action == SMOOTHGALLERY_ACTION_CSS ? '&amp;'.SMOOTHGALLERY_ACTION_CSS_TYPE.'='.$parameters[SMOOTHGALLERY_ACTION_CSS_TYPE].'&amp;height='.$parameters['height'].'&amp;width='.$parameters['width'].'&amp;bordercolor='.$parameters['bordercolor'] : '');
	return $url.'/smoothgallery.php?'.SMOOTHGALLERY_ACTION.'='.$action.'&amp;prefix='.urlencode($url).$extraParameters;
}


/**
 * Returns the absolute URL to our plugin's folder. If this function gets
 * called outside "the loop" it tries to find the URL in the supplied array.
 */
function get_smoothgallery_url($parameters) {
	$wpUrl = (function_exists('get_bloginfo') ? get_bloginfo('wpurl') : $parameters['prefix']);
	if (strpos($wpUrl, SMOOTHGALLERY_URL) === false) return $wpUrl.SMOOTHGALLERY_URL;
	else return $wpUrl;
}


/**
 * Generates a query string from the given array. Only adds them if they're
 * different from the defaults.
 */
function generate_smoothgallery_query_string($atts, $defaults, $urlencode = array()) {
	if (!is_array($atts) or !is_array($defaults)) return '';
	$queryString = '';
	$delim = '&amp;';
	foreach ($atts as $key => $value) {
		if (strlen(trim($value)) == 0 or $defaults[$key] == $value) continue;
		$value = (in_array($key, $urlencode) ? urlencode($value) : $value);
		$queryString .= $key.'='.$value.$delim;
	}
	return substr($queryString, 0, strlen($queryString) - strlen($delim));
}


/**
 * This function dispatches the request to retrieve some images to various
 * implementations that get images attached to a particular post/page or found
 * in some directory.
 *
 * Once some images are found it searches for images with the same name in
 * another size that were created by WordPress during upload. After that we can
 * shuffle the images, reorder them, filter some images, i.e. do whatever we
 * want with them before they'll be displayed by SmoothGallery.
 */
function get_smoothgallery_images($id = NULL, $dir = NULL, $filter = NULL, $randomize = false, $flickrusername = NULL, $flickrphotoset = NULL, $picasaurl = NULL) {
	$images = array();
	if ($dir !== NULL) {
		$images = get_images_from_directory($dir);
	} else if ($flickrusername !== NULL) {
		$images = get_flickr_images($flickrusername, $flickrphotoset);
	} else if ($picasaurl !== NULL) {
		$images = get_picasa_images($picasaurl);
	} else {
		# aggregate images from multiple posts or use the images from one post
		if (strstr($id, ',') !== false) {
			foreach (explode(',', $id) as $ids_id) {
				$images = array_merge($images, get_images_for_post($ids_id));
			}
		} else {
			$images = get_images_for_post($id);
		}
	}
	# if we haven't found any images we'll exit here
	if (empty($images) or !is_array($images) or !is_object($images[0])) return array();

	# tries to find images with another size
	if ($flickrusername == NULL and $picasaurl == NULL) {
		for ($i = 0; $i < count($images); $i++) {
			$altImages = get_all_sizes_for_image($images[$i]->url, $dir);
			if (empty($altImages)) continue;
			$images[$i]->alt = $altImages;
		}
	}

	# generate thumbnails with resizer.php
	if (ENABLE_GENERATED_THUMBNAILS) {
		foreach ($images as $key => $value) {
			$images[$key]->thumb = get_smoothgallery_url(array()).'/extra/resizer.php?src='.$value->url.'&amp;w=100&amp;h=75';
		}
	}

	# filter the given images
	# TODO:
	# - idea: specify s:2;e:10 meaning: skip the first image, showing 9 images til image nr. 10 is reached
	if (!empty($filter)) {
		$excludes = array();
		foreach ($images as $key => $value) {
			if (stripos($value->url, $filter) !== false)
				$excludes[] = $key;
		}
		foreach ($excludes as $exclude) {
			removeElementFromArray($exclude, $images);
		}
	}

	# randomize the order of the images
	if ($randomize) shuffle($images);

	# Reorders the images, so the image given in the request is the first image
	# in the gallery. Hint: we could have used "myGallery.goTo(<nr of image>)"
	# in the JavaScript too. This way it's currently easier though, because we
	# don't know the number of the image at the time the JavaScript is created.
	if (!empty($_REQUEST['sgfi'])) {
		$firstImage = $_REQUEST['sgfi'];
		$tmpImages = array(); $tmpImages[] = '';
		for ($i = 0; $i < count($images); $i++) {
			if ($images[$i]->title == $firstImage) $tmpImages[0] = $images[$i];
			else $tmpImages[] = $images[$i];
		}
		$images = $tmpImages;
	}

	return $images;
}


/**
 * Fetches images from a directory. For various reasons the exact location of
 * the directory isn't encoded in the URL but supplied via a user implemented
 * method (getImageDirectory()); this doesn't apply for the widget, as you can
 * supply a directory here without hard coding it in the config.php.
 */
function get_images_from_directory($dirId) {
	require_once('config.php');
	$dir = getImageDirectory($dirId);
	if (empty($dir)) return array();

	$files = listFiles($dir);
	if (empty($files) and !is_array($files)) return array();

	# iFrame hack
	$siteurl = 'http://'.$_SERVER['SERVER_NAME'];
	if (function_exists('get_option')) $siteurl = get_option('siteurl');

	$images = array(); $i = 0;
	foreach ($files as $file) {
		$images[$i++]->url = str_replace(dirname(__FILE__).'/../../../', $siteurl.'/', $file);
		# TODO: maybe we can set title, caption and description to something useful too
	}
	return $images;
}


/**
 * Returns an array with pictures attached to the current post/page. You can
 * supply the ID of another post, so you don't have to search for the images
 * attached to the current post ($post->ID) only.
 *
 * TODO: this could be rewritten to use wp_get_attachment_metadata etc.
 */
function get_images_for_post($postId = NULL) {
	global $wpdb, $post;

	# sanity check FIXME
	if (!is_numeric($postId)) $postId = NULL;
	# use this post ID FIXME
	$real_post_id = ($postId == NULL) ? $post->ID : $postId;

	# retrieve information about images
	$sql = 'SELECT id, post_content AS description, post_title AS title, post_excerpt AS caption, guid AS url
	        FROM '.$wpdb->posts.'
	        WHERE post_parent = '.$real_post_id.'
	              AND post_type = "attachment"
	              AND post_mime_type like "%image%"
	        ORDER BY menu_order';
	$result = $wpdb->get_results($sql);

	# retrieve meta data
	$sql = 'SELECT DISTINCT post_id
            FROM '.$wpdb->posts.', '.$wpdb->postmeta.'
            WHERE post_parent = '.$real_post_id.'
                  AND post_type = "attachment"
                  AND post_mime_type like "%image%"
	              AND id = post_id';
	$meta = $wpdb->get_results($sql);

	# insert retrieved meta data into $result
	if (!empty($meta) and is_array($meta)) {
		foreach ($meta as $key => $attachment_id) {
			$attachment_id = $attachment_id->post_id;
			$attachment_meta = wp_get_attachment_metadata($attachment_id);

			# Hack: "Cannot use string offset as an array"
			if (!empty($attachment_meta) and !is_array($attachment_meta))
				$attachment_meta = unserialize($attachment_meta);

			$meta_link = $attachment_meta['image_meta']['smoothgallery_link'];
			if (empty($meta_link)) continue;

			foreach ($result as $res_key => $image) {
				if ($image->id != $attachment_id) continue;
				$result[$res_key]->link = $meta_link;
			}
		}
	}

	return $result;
}


/**
 * Searches the uploads folder for images denoted by the given URL but with
 * other sizes. Let's say the URL points at the image 'test.jpg', then we'll
 * try to find images like 'test-150x150.jpg' and the like.
 *
 * Won't work if the upload path isn't inside wp-content but somewhere else.
 * But that's no big problem we just won't find any thumbnails.
 */
function get_all_sizes_for_image($url, $dirId = NULL) {
	# iFrame hack
	$uploadPath = 'wp-content/uploads';
	if (function_exists('get_option')) $uploadPath = get_option('upload_path');
	# get upload directory
	$imgDir = ($uploadPath[0] != '/') ? dirname(__FILE__).'/../../../'.$uploadPath : $uploadPath;
	if ($dirId !== NULL) {
		require_once('config.php');
		$dir = getImageDirectory($dirId);
		if (!empty($dir)) $imgDir = $dir;
	}

	# extract the image's name
	$imgNameWithExt = end(explode('/', $url));
	$imgName = substr($imgNameWithExt, 0, strrpos($imgNameWithExt, '.'));

	$images = array();
	$allImages = listFiles($imgDir);
	if ($allImages !== false) {
		foreach ($allImages as $image) {
			# the filename must match the name of the image followed by a dash
			# which is typically followed by the size of the image, e.g. 150x150.
			$image = end(explode('/', $image));
			if (strpos($image, $imgName.'-') === false) continue;
			# insert the filename into the given URL
			$images[] = str_replace($imgNameWithExt, $image, $url);
		}
	}

	return $images;
}


/**
 * Returns an array with all the files in a certain directory and it's
 * subdirectories.
 *
 * Copied from here:
 * http://php.net/manual/function.opendir.php#83990
 */
function listFiles($from = '.') {
	if(!is_dir($from)) return false;

	$files = array();
	$dirs = array($from);
	while(NULL !== ($dir = array_pop($dirs))) {
		if($dh = opendir($dir)) {
			while(false !== ($file = readdir($dh))) {
				if($file == '.' || $file == '..') continue;

				$path = $dir . '/' . $file;
				if(is_dir($path)) {
					$dirs[] = $path;
				} else {
					$files[] = $path;
				}
			}
			closedir($dh);
		}
	}
	return $files;
}

/**
 * Removes an element from an array. I don't know whether there's a cleaner way
 * to do this... I got the original idea from here:
 * http://frish.nl/archives/2007/05/17/how-to-remove-an-element-from-an-array#comment-74677
 */
function removeElementFromArray($element, &$array) {
	if (empty($array) or !is_array($array)) return;
	$pos = 0;
	foreach ($array as $key => $value) {
		if ($key == $element) {
			array_splice($array, $pos, 1);
			break;
		}
		$pos++;
	}
}

?>
