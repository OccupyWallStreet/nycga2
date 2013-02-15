<?php
require_once(dirname(__FILE__).'/slickr-flickr-feed.php');

class slickr_flickr_display {

	function __construct() {}

	function show($attr) {
  		$params = shortcode_atts( SlickrFlickrUtils::get_options(), $attr ); //apply plugin defaults    
  		foreach ( $params as $k => $v ) if (($k != 'id') && ($k != 'options') && ($k != 'galleria_options') && ($k != 'attribution')) $params[$k] = strtolower($v); //set all params as lower case
  		if (empty($params['id'])) return "<p>Please specify a Flickr ID for this ".$params['type']."</p>";
  		if ( (!empty($params['tagmode'])) && empty($params['tag']) && ($params['search']=="photos")) return "<p>Please set up a Flickr tag for this ".$params['type']."</p>";
  		if (empty($params['api_key']) && ($params['use_key'] == "y")) return "<p>Please add your Flickr API Key in Slickr Flickr Admin settings to fetch more than 20 photos.</p>";
  		if (empty($params['use_key'])) $this->force_api_key($params); //set api_key if required by other parameters
  		$rand_id = rand(1,1000);
  		$unique_id = $this->get_unique_id($params,$rand_id);
  		$divclear = '<div style="clear:both"></div>';
  		$attribution = empty($params['attribution'])?"":('<p class="slickr-flickr-attribution align'.$params['align'].'">'.$params['attribution'].'</p>');
  		$bottom = empty($params['bottom'])?"":(' style="margin-bottom:'.$params['bottom'].'px;"');
  		$lightboxrel =""; $thumb_scale ="";
  		switch ($params['type']) {
   		case "slideshow": {
	    		if (empty($params['ptags'])) $params['ptags'] = "on"; //paragraph tags arounds titles	
    		    $divstart = $attribution.'<div id="'.$unique_id.'" class="slickr-flickr-slideshow '.$params['orientation'].' '.$params['size'].($params['descriptions']=="on" ? " descriptions" : "").($params['captions']=="off" ? " nocaptions " : " ").$params['align'].'"'.$bottom.'>';
 				$divend = '</div>'.$this->set_options($unique_id,$this->slideshow_options($params)).$divclear;
 		       	$element='div';
    		    $element_style='';
        break;
        }
   		case "galleria": {
    		if (empty($params['thumbnail_size'])) $params['thumbnail_size'] = 'square'; //set default thumbnail size as Square
    		if ($params['galleria'] == 'galleria-original') {
				$params['galleria_theme'] = 'original'; //set a default value
				if (empty($bottom))
					$style = ' style="visibility:hidden;"';
        	    else
        	    	$style = substr($bottom,0,strlen($bottom-2)).'visibility:hidden;"';
        	    $startstop = $params['pause']== 'off' ? '' : ('| <a href="#" class="startSlide">start</a> | <a href="#" class="stopSlide">stop</a>');
 			    $nav = <<<NAV
<p class="nav {$params['size']}"><a href="#" class="prevSlide">&laquo; previous</a> {$startstop} | <a href="#" class="nextSlide">next &raquo;</a></p>
NAV;
			} else {		
				$style = $bottom;
				$nav= '';
			}
			switch ($params['nav']) {
				case "above": { $nav_below = ''; $nav_above = $nav; break; }
				case "below": { $nav_below = $nav; $nav_above = ''; break; }
				case "none": { $nav_below = ''; $nav_above = ''; break; } 	
				default: { $nav_below = $nav; $nav_above = $nav; break; }
			}
    	    $divstart = '<div id="'.$unique_id.'" class="slickr-flickr-galleria '.$params['orientation'].' '.$params['size'].' '.$params['galleria_theme'].'"'.$style.'>'.$attribution.$nav_above.'<ul>';
    	    $divend = '</ul>'.$divclear.$attribution.$nav_below.'</div>'.$this->set_options($unique_id,$this->galleria_options($params));
    	    $element='li';
    	    $element_style='';
			slickr_flickr_public::add_galleria_theme($params['galleria_theme']); //add count of gallerias on page		
    	    break;
    	    }
   		default: {
    	    $this->set_thumbnail_params($params);
    	    $this->set_lightboxrel($params,$rand_id);
    	    $divstart = '<div id="'.$unique_id.'" class="slickr-flickr-gallery"'.$bottom.'>'. $attribution . '<ul'.$params['gallery_class'].$params['gallery_style'].'>';
    	    $divend = '</ul></div>'.$this->set_options($unique_id,$this->lightbox_options($params)).$divclear;
    	    $element='li';
    	    $element_style = $params['thumbnail_style'];
    	    }
  		}
      	$photos = $this->fetch_feed($params);
      	if (! is_array($photos)) return $photos; //return error message if an array of photos is not returned
  		$start = $this->get_start($params, count($photos));
  		$s = "";
  		$i = 0;
  		if (empty($element)) {
    		foreach ( $photos as $photo ) $s .= $this->get_image($photo, $params);
  		} else {
    		foreach ( $photos as $photo ) {
      		$i++;
    		  $s .= '<'.$element.$element_style.($start==$i?' class="active"':'').'>'.$this->get_image($photo, $params).'</'.$element.'>';
    		}
  		}
  		return $divstart . $s . $divend;
	}

	function get_unique_id($params,$rand_id) {
	  $unique_id = array_key_exists('tag',$params) ? $params['tag'] : (
               array_key_exists('set',$params) ? $params['set'] : (
               array_key_exists('gallery',$params) ? $params['gallery'] : 'recent'));
	  return "flickr_".strtolower(preg_replace("{[^A-Za-z0-9_]}","",$unique_id)).'_'.$rand_id; //strip spaces, backticks, dashes and commas
	}

	function fetch_feed($params) {
	  	$feed = new slickr_flickr_feed($params);
	  	$page=$params['page'];
      	$photos = $feed->fetch_photos($page) ;
      	if (!is_array($photos))  return $feed->get_message(); //return error
	  	if (!empty($params['restrict'])) $photos = $this->restrict_photos($photos, $params);
	  	if (!empty($params['sort'])) $photos = $this->sort_photos ($photos, $params['sort'], $params['direction']);
	  	return $photos; //return array of photos
	}

	function force_api_key(&$params) {
	  if ((empty($params['use_key'])) 
	  && (! empty($params['api_key'])) 
	  && (($params['items'] > 20 ) || $this->api_required($params))) 
	   	$params['use_key'] = "y"; // set use_key if API key is available and is either required or request is for over 20 photos
	}

	function api_required($params) {
		return (! empty($params['license'])) || (! empty($params['date'])) || (! empty($params['before'])) || (! empty($params['after']))
			|| (! empty($params['private'])) || ($params['page'] > 1) || ($params['search'] == 'galleries') 
			|| ( !empty($params['tag']) && ($params["search"]=="groups")); 
	}

	function set_slideshow_onclick($params) {
	  $link='';
	  if (empty($params['link']))
	    if ($params['pause'] == "on")
	        $link = "toggle" ;
	     else
	        $link = "next";
	  else
	    $link = $params['link'];
	  return $link;
	}

	function set_lightboxrel(&$params, $rand_id) {
		$ptags = "off";
	    switch ($params['lightbox']) {
	      case "sf-lbox-manual":
	      case "sf-lbox-auto": 	$lightboxrel = 'rel="sf-lightbox"' ; $ptags = "on"; break;
	      case "evolution": 	$lightboxrel = 'class="lightbox" rel="group'.$rand_id.'" ';  break;
	      case "fancybox": 		$lightboxrel = 'rel="fancybox_'.$rand_id.'" class="fancybox"'; $ptags = "on";  break;
	      case "prettyphotos": 	$lightboxrel = 'rel="wp-prettyPhoto[g'.$rand_id.']"' ; break; 
	      case "prettyphoto": 	$lightboxrel = 'rel="wp-prettyPhoto"' ;  break; 
	      case "shadowbox": 	$lightboxrel = 'rel="shadowbox['.$rand_id.']"'; $ptags = "on"; break;
	      case "highslide":
	      case "slimbox":
	      case "colorbox":		$lightboxrel = 'rel="lightbox['.$rand_id.']"';  break;
	      case "shutter":  		$lightboxrel = 'class="shutterset_'.$rand_id.'"';  break;
	      case "thickbox": 		$lightboxrel = 'rel="thickbox-'.$rand_id.'" class="thickbox"'; break;
	      case "none":
	      case "norel": $lightboxrel = '' ; break;      
	      default: 	$lightboxrel = 'rel="'.$params['lightbox'].'['.$rand_id.']"'; 
	      }
 	   $params['lightboxrel'] = $lightboxrel;
 	   $params['lightbox_id'] = $rand_id;
       if (empty($params['ptags'])) $params['ptags'] = $ptags; //paragraph tags arounds titles?

	}

	function set_thumbnail_params(&$params) {
	    $thumb_rescale= false;
	    switch ($params["thumbnail_size"]) {
	      case "thumbnail": $thumb_width = 100; $thumb_height = 75; $thumb_rescale = true; break;
	      case "s150": $thumb_width = 150; $thumb_height = 150; $thumb_rescale = true; break;
	      case "small": $thumb_width = 240; $thumb_height = 180; $thumb_rescale = true; break;
	      case "s320": $thumb_width = 320; $thumb_height = 240; $thumb_rescale = true; break;
	      case "medium": $thumb_width = 500; $thumb_height = 375; $thumb_rescale = true; break;
	      case "m640": $thumb_width = 640; $thumb_height = 480; $thumb_rescale = true; break;
	      case "m800": $thumb_width = 800; $thumb_height = 640; $thumb_rescale = true; break;
	      default: $thumb_width = 75; $thumb_height = 75; $params["thumbnail_size"] = 'square';
	    }
	    if ($params["orientation"]=="portrait" ) { $swp = $thumb_width; $thumb_width = $thumb_height; $thumb_height = $swp; }

	    if ($params["thumbnail_scale"] > 0) {
	        $thumb_rescale = true;
	        $thumb_width = round($thumb_width * $params["thumbnail_scale"] / 100);
	        $thumb_height = round($thumb_height * $params["thumbnail_scale"] / 100);
	    }
    	$params['image_style'] = $thumb_rescale ? (' style="height:'.$thumb_height.'px; max-width:'.$thumb_width.'px;"') : '';

    	if (($params['type'] == "gallery") && ($params['photos_per_row'] > 0)) {
    	    $li_width = ($thumb_width + 10);
    	    $li_height = ($thumb_height + 10);
    	    $gallery_width = 1 + (($li_width + 4) *  $params['photos_per_row']);
    	    $params['gallery_style'] = ' style=" width:'.$gallery_width.'px"';
    	    $params['thumbnail_style'] = ' style="width:'.$li_width.'px; height:'.$li_height.'px;"';
    	} else {
    	    $params['gallery_style'] = '';
    	    $params['thumbnail_style'] = '';
    	}
    	$params['gallery_class'] = $params['align'] ? (' class="'.$params['align'].'"'):'';
	}

	function get_image($photo, $params) {
	    $link = $photo->get_link();
	    $full_url = $params['size']=="original" ? $photo->get_original() : $photo->resize($params['size']);
	    $thumb_url = $photo->resize($params['thumbnail_size']);
	    $oriented = $photo->get_orientation();
	    $title = $photo->get_title();
	    $description = $photo->get_description(); 
	    if ($description == '<p></p>') $description = '';
	    $border = $params['border']=='on'?' class="border"':'';
		$ptags = ('on'==$params['ptags']); //paragraph tags around title?
		//separator is required if title and description end up together on the same line
	    $sep = (($params["descriptions"] =='on') && ($params["type"] !='galleria') && ! $ptags) ? '.&nbsp;' : ''; 
	    $ptitle = empty($title) ? '' : sprintf(($ptags ? '<p%2$s>%1$s</p>' : '<span%2$s>%1$s</span>').$sep ,$title, $border);
	    $plink = sprintf($ptags ? '<p>%1$s</p>' : '%1$s' ,'<a title="Click to see photo on Flickr" href="'. $link . '">'.$title.'</a>'.$sep);
	    $captiontitle = $params["flickr_link"]=="on"?$plink:$ptitle;
	    $alt = $params["descriptions"]=="on"? ($ptags ? $description : strip_tags($description)) : "";
	    switch ($params['type']) {
	       case "slideshow": {
	            $caption = $params['captions']=="off"?"":($captiontitle.$alt);
	            return  sprintf('<img src="%1$s" title="%2$s" alt="%3$s" %4$s />%5$s',
	            $full_url, htmlspecialchars($title), htmlspecialchars($alt), $border, $caption);
	        }
    	   case "galleria": {
    	   		$caption = $params['captions']=="off"?"":$captiontitle;
    	   		return sprintf('<a href="%1$s"><img src="%2$s" title="%3$s" alt="%4$s" /></a>',
    	   				$full_url, $thumb_url, htmlspecialchars($caption), htmlspecialchars($alt));
    	    }
    	    default: {
				return $this->get_lightbox_html ($params, $full_url, $link, $thumb_url, $captiontitle, $title, $alt);
    	    }
    	}
	}

	function get_lightbox_html ($params, $full_url, $link_url, $thumb_url, $a_title, $img_title, $img_alt) {
    	//if (($params['lightbox']=='thickbox') && (!empty($lightbox_title))) $title = " title='". str_replace("'",'"',$lightbox_title)."'";
    	if ($params['lightbox']=="none") $full_url = empty($params['link']) ? $link_url : $params['link']; //link to flickr if no lightbox
    	$thumbcaption = $params['thumbnail_captions']=="on"?('<br/><span class="slickr-flickr-caption">'.$img_title.'</span>'):"";
    	$full_caption= ($params["captions"]=="off" ? '' : $a_title) . ($params["descriptions"]=="on" ? $img_alt : "");
		$img_title = empty($img_title) ? '' : sprintf('title="%1$s"',htmlspecialchars($img_title));
		$img_alt = empty($img_alt) ? '' : sprintf('alt="%1$s"',htmlspecialchars($img_alt));
		$title = ''; 
		if (! empty($full_caption)) switch ($params['lightbox']) {
	      case "prettyphotos":      
	      case "prettyphoto": $img_alt = sprintf('alt="%1$s"', htmlspecialchars($full_caption)); break; //use alt
	      case "fancybox":    $img_title = sprintf('title="%1$s"', htmlspecialchars($full_caption)); break; //use title
	      case "thickbox": $title = sprintf('title=\'%1$s\'', str_replace("'","&acute;",$full_caption)); break; //avoid thickbox issue with apostrophes
		  default: $title = sprintf('title="%1$s"', htmlspecialchars($full_caption));
		}
    	return sprintf('<a href="%1$s" %2$s %3$s><img src="%4$s" %5$s %6$s %7$s />%8$s</a>',
			$full_url, $params['lightboxrel'], $title, 
			$thumb_url, $params['image_style'], $img_alt, $img_title, $thumbcaption);
	}

	function get_start($params,$numitems) {
	  $r = 1;
	  if ($numitems > 1) {
	     if ($params['start'] == "random")
	        $r = rand(1,$numitems);
	     else
	        $r = is_numeric($params['start']) && ($params['start'] < $numitems) ? $params['start'] : $numitems;
	     }
	   return $r;
	}

	function restrict_photos ($items, $params) {
	    $filtered_items = array();
	    if ($params['restrict']=='orientation') { 
	    	$orientation = $params['orientation'];    
	    	foreach ($items as $item)  if ($item->get_orientation()==$orientation) $filtered_items[] = $item;
	    	return $filtered_items;
		} else {
		    return $items;
		}
	}

	function sort_photos ($items, $sort, $direction) {
		$do_sort = ($sort=="date") || ($sort=="title") || ($sort=="description");
	    $direction = strtolower(substr($direction,0,3))=="des"?"descending":"ascending";
	    if ($sort=="date") { foreach ($items as $item) { if (!$item->get_date()) { $do_sort = false; break; } } }
	    if ($sort=="description") { foreach ($items as $item) { if (!$item->get_description()) { $do_sort = false; break; } } }
	    $ordered_items = $items;
	    if ($do_sort) usort($ordered_items, array(&$this,'sort_by_'.$sort.'_'.$direction));
	    return $ordered_items;
	}

	function sort_by_description_descending($a, $b) { return strcmp($b->get_description(),$a->get_description()); }

	function sort_by_description_ascending($a, $b) { return strcmp($a->get_description(),$b->get_description()); }

	function sort_by_title_descending($a, $b) { return strcmp($b->get_title(),$a->get_title()); }

	function sort_by_title_ascending($a, $b) { return strcmp($a->get_title(),$b->get_title()); }

	function sort_by_date_ascending($a, $b) { return ($a->get_date() <= $b->get_date()) ? -1 : 1; }

	function sort_by_date_descending($a, $b) { return ($a->get_date() > $b->get_date()) ? -1 : 1; }

	function slideshow_options($params) {
    	$options['delay'] = $params['delay'] * 1000;
    	$options['autoplay'] = $params['autoplay']=="off"?false:true;
    	$options['transition'] = 500;
    	$options['link'] = $this->set_slideshow_onclick($params);
    	$options['target'] = $params['target'];    
		return $options;
	}

	function lightbox_options($params) {
    	$options = array();
    	if ($params['lightbox'] == "sf-lbox-auto") {
			if (!empty($params['options'])) $this->parse_json_options($params['options'], $options);
    		if (!array_key_exists('nextSlideDelay',$options)) $options['nextSlideDelay'] = $params['delay'] * 1000;
    		if (!array_key_exists('autoPlay',$options)) $options['autoPlay'] = $params['autoplay']=="on"?true:false;
		}
    	if (array_key_exists('thumbnail_border',$params) && (!empty($params['thumbnail_border']))) $options['border'] = $params['thumbnail_border']; 
		return $options;
	}

	function parse_json_options($json, &$options ) {
		$options_list = str_replace(';;',';',trim($json).';');
    	$more_options = array();
		if ((preg_match_all("/([^:\s]+):([^;]+);/i", $options_list, $pairs)) && (count($pairs)>2)) $more_options = array_combine($pairs[1], $pairs[2]);
		foreach ($more_options as $key => $value) {
			if (is_numeric($value)) {
				$options[$key] = $value + 0;
			} else {
			    $val = strtolower(trim($value));
				switch ($val) {
					case "false": { $options[$key] = false; break; }
					case "true": { $options[$key] = true; break; } 
					default:  $options[$key] = $val;
        	    }
			}
		}
	}

	function galleria_options($params) {
	    $options = array();
		if ($params['galleria'] == 'galleria-original') {
			$options['delay'] = $params['delay'] * 1000;
			$options['autoPlay'] = $params['autoplay']=='on'?true:false;
			$options['captions'] = $params['captions']=='off'?false:true;
			$options['descriptions'] = $params['descriptions']=='on'?true:false;
	    } else {
			if (!empty($params['galleria_options'])) $this->parse_json_options($params['galleria_options'], $options);
			if (!empty($params['options'])) $this->parse_json_options($params['options'], $options);
    		if (!array_key_exists('autoplay',$options)) $options['autoplay'] = $params['delay']*1000; 
  		  	if (!array_key_exists('transition',$options)) $options['transition'] = 'fade';
  		  	if (!array_key_exists('transitionSpeed',$options)) $options['transitionSpeed'] = $params['transition']*1000;
  		  	if (!array_key_exists('showInfo',$options)) $options['showInfo'] = $params['captions']=='off' ? false: true;
  		  	if (!array_key_exists('imageCrop',$options)) $options['imageCrop'] = true;
  		  	if (!array_key_exists('carousel',$options)) $options['carousel'] = true;    	
  		  	if (!array_key_exists('debug',$options)) $options['debug'] = false;  
		    $gtheme = $params['galleria_theme'];
	        if (('folio'!= $gtheme) && ('fullscreen' != $gtheme)) {	
	            $p = $params['orientation']=="portrait";
				switch ($params['size']) {
					case "small": { $h=$p?300:220; $w=$p?200:240; break;} 
					case "m640": {  $h=$p?720:520; $w=$p?500:640;  break;}
					case "m800": {  $h=$p?880:640; $w=$p?660:800;  break;}
					case "s320": {  $h=$p?400:280; $w=$p?260:320;  break;}
					case "large": {  $h=$p?1100:808; $w=$p?788:1024;  break;}
					default : { $h=$p?560:480; $w=$p?420:480;  break;}
				}
				if (!array_key_exists('width',$options)) $options['width'] = $w;
				if (!array_key_exists('height',$options)) $options['height'] = $h;
    	    }    	    
    	}
		return $options;
	}

	function set_options($unique_id, $options) {
	    if (count($options) > 0) {
	    	$s = 'jQuery("#'.$unique_id.'").data("options",'.json_encode($options).');'; 
	        if (SlickrFlickrUtils::scripts_in_footer()) {
	    		SLICKR_FLICKR_PUBLIC::add_jquery($s); //save for later
			} else {
				return '<script type="text/javascript">'.$s.'</script>'; //output it now
			}
		}
		return '';
	}

	function get_cache($unique_id) {
		return get_transient($unique_id);
	}

	function set_cache($unique_id, $photos, $expiry) {
		return set_transient($unique_id, $photos, $expiry);
	}

	function select_random($photos,$random) {
		$subset = array();
    	$keys = $random == 1 ? array(array_rand($photos, 1)) : array_rand($photos, $random); 
    	shuffle($keys);
    	foreach ($keys as $key) $subset[] = $photos[$key];
		return $subset;
	}

}
?>