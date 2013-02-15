<?php
class slickr_flickr_api_photo {

  var $url;
  var $width;
  var $height;
  var $orientation;
  var $title;
  var $description;
  var $date;
  var $link;
  var $original;

  function __construct($user_id, $item, $must_get_dims=false) {
    $farmid = $item['farm']; 
    $serverid = $item['server'];
    $id = $item['id'];
    $secret = $item['secret'];
    $owner = array_key_exists('owner',$item) ? $item['owner'] : $user_id;
    $this->url = "http://farm{$farmid}.static.flickr.com/{$serverid}/{$id}_{$secret}.jpg";
    $this->link = "http://www.flickr.com/photos/{$owner}/{$id}";
    $this->original= array_key_exists('url_o',$item) ? $item['url_o'] : '' ;
    $this->date = array_key_exists('date_taken',$item) ? $item['date_taken'] : '' ;
    $this->title = $this->cleanup($item['title']);
    $this->description = array_key_exists('description',$item) ? $this->cleanup($item['description']) : '' ;
    $this->height = array_key_exists('o_height',$item) ? $item['o_height'] : 0 ;
    $this->width = array_key_exists('o_width',$item) ? $item['o_width'] : 0 ;
    if ($must_get_dims && (($this->height==0) || ($this->width==0))) $this->get_dims();
    $this->orientation = $this->height > $this->width ? "portrait" : "landscape" ;
  }

  function get_url() { return $this->url; }
  function get_width() { return $this->width; }
  function get_height() { return $this->height; }
  function get_orientation() { return $this->orientation; }
  function get_title() { return $this->title; }
  function get_description() { return stripos($this->description,'<p>') === FALSE ? ('<p>'.$this->description.'</p>') : $this->description; }
  function get_date() { return $this->date; }
  function get_link() { return $this->link; }
  function get_original() { return $this->original; }

  /* Function that removes all quotes */
  function cleanup($s = null) {
    return $s?str_replace("\n", "<br/>",$this->handle_quotes($s)):false;
  }
  
  function handle_quotes($s='',$recurring=0) {
  	if ($s && ($recurring < 4) && (substr_count($s,'"') >= 2)) {
		$pattern = '/(.*)\"(.*)\"(.*)/';
		$replace = '${1}&ldquo;${2}&rdquo;${3}';
		$s = preg_replace($pattern, $replace, $s);  		
		return $this->handle_quotes($s,$recurring+1);
  	} else {
		return str_replace('"', '&quot;', $s); 
  	}
  }

  /* Function that returns the correctly sized photo URL. */
  function resize($size) {

    $url_array = explode('/', $this->url);
    $photo = array_pop($url_array); //strip the filename

    switch($size)  {
      case 'square': $suffix = '_s.';  break;
      case 'thumbnail': $suffix = '_t.';  break;
      case 'small': $suffix = '_m.';  break;
      case 'm640': $suffix = '_z.';  break;
      case 'm800': $suffix = '_c.';  break;
      case 's320': $suffix = '_n.';  break; 
      case 's150': $suffix = '_q.';  break; 
      case 'large': $suffix = '_b.';  break;
      default:  $suffix = '.';  break; // Medium
      }

    $url_array[] =  preg_replace('/(_(s|t|c|n|q|m|b|z))?\./i', $suffix, $photo); //
    return implode('/', $url_array);
  }
  
	function get_dims(){
    	$headers = array("Range: bytes=0-32768");
	    $curl = curl_init($this->url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($curl);
	    curl_close($curl);
		$im = imagecreatefromstring($data);
		$this->width = imagesx($im);
		$this->height = imagesy($im);
     }
}
?>