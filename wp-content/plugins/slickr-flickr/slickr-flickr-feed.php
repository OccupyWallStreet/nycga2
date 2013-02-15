<?php
require_once(dirname(__FILE__).'/slickr-flickr-photo.php');
require_once(dirname(__FILE__).'/slickr-flickr-api-photo.php');
require_once(dirname(__FILE__).'/phpFlickr.php');

class slickr_flickr_feed{

  var $photos = array(); //results
  var $error = false; //is an error
  var $message = ''; //error message
  var $method = ''; //access method
  var $args = array(); //arguments
  var $use_rss = true;  //useRSS feed
  var $use_rest = false; //use REST access
  var $extras = 'description,date_taken,url_o,dims_o'; //extra params to getch when using API
  var $container = "photos"; //XML container of photo elements
  var $api_key = ''; //Flickr API Key
  var $user_id = ''; //Flickr NS ID 
  var $flickr = false; //phpFlickr Object
  var $cache = false; //plugin cache
  var $get_dims = false;
    
  function get_photos() { return $this->photos; }
  function get_count() { return count($this->photos); }  
  function is_error() { return $this->error; }  
  function get_message() { return $this->message; }
 
  function __construct($params) {
  	$this->get_dims = array_key_exists('restrict',$params) && ('orientation'==$params['restrict']);
    $this->build_command($params);  //set up method and args
   	if (!$this->use_rss) $this->set_php_flickr();
  }
  
  function set_php_flickr() {
	if ($this->flickr) return true; //set up already
	if (empty($this->api_key)) return false; //no key so can't set it up
	$this->flickr = new phpFlickr ($this->api_key, NULL, true); 
	if ($this->cache=="local") $this->flickr->enableCache ('db', 'mysql://'.DB_USER.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_NAME); 
	return true; //set it up now
  }

  function fetch_photos($page=0) {
    $this->photos = array();
	if ($page > 1)  $this->args['page'] = $page ;
 	if ($this->use_rss) {
 		$rss = fetch_feed($this->get_feed_url());  //use WordPress simple pie feed handler 
        if ( is_wp_error($rss) ) {
        	$this->message = "<p>Error fetching Flickr photos: ".$rss->get_error_message()."</p>";  
			$this->error = true;
		} else {
	    	$numitems = $rss->get_item_quantity($this->args["per_page"]);
	        if ($numitems == 0)  {
	        	$this->message = '<p>No photos available right now.</p><p>Please verify your settings, clear your RSS cache on the Slickr Flickr Admin page and check your <a target="_blank" href="'.$this->get_feed_url().'">Flickr feed</a></p>';
				$this->error = true;
			} else {
	        	$rss_items = $rss->get_items(0, $numitems);
	    		foreach ( $rss_items as $item ) {
	    	    	$this->photos[] = new slickr_flickr_photo($item);  //feed items and load into object
	    	    }
	    	}
	    }
 	} else {
 		$this->photos = $this->call_flickr_api();
    }
    return $this->photos;
}
 
  function call_flickr_api_with_pages($page=1) {
        $per_page = $this->args['per_page'];
        unset($this->args['per_page']);
  		$pager = new phpFlickr_pager($this->flickr, $this->method, $this->args, $per_page);
  		return array('photos' => $pager->get($page) ,'total' => $pager->total, 'pages' => $pager->pages);
  }
  
  function call_flickr_api() {
		$photos = array();
		$resp = $this->flickr->call($this->method, $this->args);
		if ($resp) {
			$results = $resp[$this->container];
    		foreach ($results['photo'] as $photo) { $photos[] = new slickr_flickr_api_photo($this->user_id,$photo,$this->get_dims); }
    	} else {
			$this->message = $this->flickr->error_msg ;
    		$this->error = true;
 		}
		return $photos;
  }

  function get_feed_url() { 
  	if ($this->use_rest) 
  		return 'http://api.flickr.com/services/rest/?method=' . $this->method . 
  			'&lang=en-us&format=feed-rss_200&api_key='.$this->api_key .$this->implode_args($this->args);
    else
  		return 'http://api.flickr.com/services/feeds/' . $this->method . '?lang=en-us&format=feed-rss_200' . $this->implode_args($this->args);
  }

  function build_command($params) {
  	$tags = strtolower(str_replace(" ","",$params['tag']));
	$this->user_id = $params['id'];	
  	$group = strtolower(substr($params['group'],0,1));
  	if ($params['use_key'] == 'y') {
  	  	$this->use_rest = true;
 		$this->use_rss = true; 
 		$this->api_key = $params['api_key'];		
        switch($params['search']) {
           case "favorites": {
                $this->method = "flickr.favorites.getPublicList";
                $this->args = array("user_id" => $params['id']);
                break;
          }
          case "groups": {
                $this->method = "flickr.groups.pools.getPhotos";
                $this->args = array("group_id" => $params['id']);
                if (!empty($tags)) $this->args["tags"] = $tags;
                break;
           }
           case "galleries": {
                $this->method = "flickr.galleries.getPhotos";
                $this->args = array("gallery_id" => $this->verify_gallery_id($params['gallery']));
                break;
           }
           case "sets": {
                $this->method = "flickr.photosets.getPhotos";
                $this->args = array('photoset_id' => $params["set"], 'extras' => $this->extras, 'per_page' => $params['per_page']);
				$this->container = 'photoset';
				$this->use_rss = false;
                break;
           }
          default: {
                $this->method = "flickr.photos.search";
                $id = $group=='y' ? 'group_id' : 'user_id'; 
                $this->args[$id] = $params['id'];
                if (!empty($params['license'])) $this->args["license"] = $params['license'];
                $dates = $this->get_dates($params);
                if (count($dates)>0) $this->args = $this->args + $dates;
                if (!empty($params['tagmode'])) $this->args["tag_mode"] = $params['tagmode']=="all"?"all":"any";
                if (!empty($tags)) $this->args['tags'] = $tags;
          }
        }
   } else {
  		$this->use_rss = true;
 	  	$this->use_api = false;   
        switch($params['search']) {
           case "favorites": { $this->method = "photos_faves.gne"; $this->args = array("nsid" => $params['id']); break; }
           case "groups": { $this->method = "groups_pool.gne"; $this->args = array("id" => $params['id']);  break;}
           case "friends": { $this->method = "photos_friends.gne"; $this->args = array("id" => $params['user_id'], "display_all" => "1");  break;}
           case "sets": {$this->method = "photoset.gne"; $this->args = array("nsid" => $params['id'], "set" => $params['set']);  break;}
           default: {
	           	$this->method = "photos_public.gne";
               	$id = $group=='y' ? 'g' : 'id'; 
               	$this->args[$id] = $params['id'];
                if (!empty($params['tagmode'])) $this->args["tagmode"] = $params['tagmode']=="any"?"any":"all";
                if (!empty($tags)) $this->args['tags'] = $tags;
           }
        }
   }
   $this->args['per_page']= min($params['items'],50);
}

  function get_dates($params) {
	    $args= array();
	    $date_type = $params['date_type']=='upload'?"upload":"taken";
	    $sort_type = $params['date_type']=='upload'?"posted":"taken";
	    $min_param = 'min_'.$date_type.'_date';
	    $max_param = 'max_'.$date_type.'_date';
	    if (empty($params['date'])) {
	    	$after = $this->convert_date_to_timestamp($params['after']);
	    	if ($after)  $args[$min_param] = $after;
	   		$before = $this->convert_date_to_timestamp($params['before'],false);
	    	if ($before) { 
	    		$args[$max_param] = $before;
	    		$args['sort'] = 'date-'.$sort_type.'-desc';
	    	} else {
	    		$args['sort'] = 'date-'.$sort_type.'-asc';
				}
	    } else {
	    	if ($params['date']=='publish') {
				global $post;
				$date = $post->post_date;
	    		$after = $this->convert_date_to_timestamp($date);
	    		if ($after) $before = $after+(24*60*60)-1;
	 		} else {
	    		$after = $this->convert_date_to_timestamp($params['date']);
	    		if ($after) $before = $after+(24*60*60)-1;
	    	}
	    	if ($after && $before) {
	    		$args[$min_param] = $after;
	    		$args[$max_param] = $before;
			}
	    }
		return $args;
 	 }

  function convert_date_to_timestamp($date, $start=true) {
		if (empty($date)) return false;
		if (strpos($date,':') === FALSE) {
			return strtotime($date. ($start?' 00:00:00':' 23:59:59'));
		} else {
			return strtotime($date);
		}
  }

  function implode_args($args) {
        $return = '';
        foreach ($args as $k => $v) {
            $return .= '&' . $k . '=' . $v;
        }
        return $return;
    }
    
  function verify_gallery_id($gallery) { //replace short gallery id by full gallery_id
		if (strpos($gallery,'-') === false) {
			if ($this->set_php_flickr()) {
				$resp = $this->flickr->urls_lookupGallery ('/photos/'.$this->user_id.'/galleries/'.$gallery);
				if ($resp) {
					$result = $resp['gallery'];
	    			$gallery = $result['id'];
				} else {
					$this->message = $this->flickr->error_msg ;
    				$this->error = true;
 				}
    		}
    	}
    	return $gallery;
    }

}    