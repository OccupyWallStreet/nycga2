<?php
/**
 * BP_Links Embed Service classes
 *
 * @package BP_Links
 * @author Marshall Sorenson
 */

/**
 * Video sites by reach:
 * YouTube*, Flickr*, MetaCafe*, Hulu (no api), Veoh, Vimeo, ustream.tv, BlinkX, Revver
 *
 * Photo sites by reach:
 * Flickr*, PhotoBucket, PicasaWeb, TwitPic, BuzzNet, twitgoo, imageshack.us?
 *
 * Just keeping track of thumb sizes here...
 * (so far the sweet spot is 100 to 120 pixels)
 * ---------------------------------
 * YouTube = 120x90 or 130x97 wtf guys??
 * MetaCafe = 136x81
 * Flickr = 75x75, 100xN, 240xN
 * PicasaWeb = tons of options
 *		http://picasaweb.google.com/data/feed/api/user/joe.geiger/photoid/5410429216279597890?thumbsize=150u&imgmax=512u
 * PicApp = 140xN
 * Fotoglif = 128x90
 */

/**
 * Generic web page embedding service
 *
 * @package BP_Links
 * @author Marshall Sorenson
 */
final class BP_Links_Embed_Service_WebPage
	extends BP_Links_Embed_Service
		implements	BP_Links_Embed_From_Url,
					BP_Links_Embed_From_Html,
					BP_Links_Embed_Has_Selectable_Image
{

	// max number of images to grab from page
	const WEBPAGE_MAX_IMAGES = 12;
	const WEBPAGE_MAX_IMAGE_HEAD = 24;
	const WEBPAGE_MIN_IMAGE_BYTES = 2048;
	const WEBPAGE_MAX_IMAGE_BYTES = 51200;

	/**
	 * @var BP_Links_Embed_Page_Parser
	 */
	private $parser;

	//
	// required concrete methods
	//

	final public function from_url( $url )
	{
		if ( bp_links_is_url_valid( $url ) ) {

			$this->data()->url = $url;

			$page_parser = BP_Links_Embed_Page_Parser::GetInstance();

			if ( $page_parser->from_url( $url ) ) {
				$this->parser = $page_parser;
				return $this->find_elements();
			}
		}
		
		return false;
	}

	final public function from_html( $html )
	{
		$page_parser = BP_Links_Embed_Page_Parser::GetInstance();

		if ( $page_parser->from_html( $html ) ) {
			$this->parser = $page_parser;
			return $this->find_elements();
		}

		return false;
	}

	final public function url()
	{
		return $this->data()->url;
	}

	final public function title()
	{
		return $this->data()->title;
	}

	final public function description()
	{
		return $this->data()->description;
	}

	final public function image_url()
	{
		if ( isset( $this->data()->images_idx ) ) {
			$idx = $this->data()->images_idx;
			return $this->data()->images[$idx]['src'];
		}
		
		return null;
	}

	final public function image_thumb_url()
	{
		return $this->image_url();
	}

	final public function image_large_thumb_url()
	{
		return $this->image_url();
	}

	final public function service_name()
	{
		return __( 'Web Page', 'buddypress-links' );
	}

	final public function from_url_pattern()
	{
		return '/^http:\/\/([a-z0-9-]+\.)+[a-z0-9-]{2,4}\/?.*/i';
	}

	final public function image_selection()
	{
		$image_array = array();

		foreach ( $this->data()->images as $image ) {
			if ( isset( $image['bytes'] ) && $image['bytes'] >= self::WEBPAGE_MIN_IMAGE_BYTES && $image['bytes'] <= self::WEBPAGE_MAX_IMAGE_BYTES ) {
				$image_array[] = $image['src'];
			}
		}

		return $image_array;
	}

	final public function image_set_selected( $index )
	{
		// do some sanity checking
		if ( is_numeric( $index ) && $index <= self::WEBPAGE_MAX_IMAGES ) {
			// cast to integer
			$idx = (integer) $index;
			// must exist in found images array
			if ( array_key_exists( $idx, $this->data()->images ) ) {
				// ok, set the index
				$this->data()->images_idx = (integer) $idx;
				return true;
			}
		} else {
			$this->data()->images_idx = null;
		}

		return false;
	}

	final public function image_get_selected()
	{
		return ( isset( $this->data()->images_idx ) ) ? $this->data()->images_idx : null;
	}

	//
	// private methods
	//
	
	private function find_elements()
	{
		//
		// try to get the title
		//
		$page_title = $this->parser->title();

		if ( !empty( $page_title ) ) {
			$this->data()->title = $page_title;
		} else {
			return false;
		}

		//
		// try to get the description
		//
		$page_desc = $this->parser->description();

		if ( !empty( $page_desc ) ) {
			$this->data()->description = $page_desc;
		} else {
			$this->data()->description = null;
		}

		//
		// try to find some images
		//
		$page_images = $this->parser->images( 100, 800, 2, 50 );
		$page_images_sorted = $this->filter_images( $page_images );
		$page_images_bytes = $this->get_images_bytes( $page_images_sorted );

		if ( is_array( $page_images_bytes ) && count( $page_images_bytes ) ) {

			// use the array as is
			$this->data()->images = $page_images_bytes;

			// set the default index if image selection has at least one entry
			if ( count( $this->image_selection() ) ) {
				// set index to first key from array
				$this->data()->images_idx = key( $page_images_bytes );
			} else {
				// no images returned by selection method
				$this->data()->images_idx = null;
			}
			
		} else {
			return false;
		}

		return true;
	}

	// TODO implement a timeout based on total seconds elapsed
	private function get_images_bytes( $images )
	{
		global $bp;

		if ( count( $images ) < 1 ) {
			return $images;
		}
		
		$return_array = array();
		$checked_count = 0;
		$good_count = 0;

		foreach ( $images as $image ) {

			// don't loop forever
			$checked_count++;

			// bytes are null by default
			$image['bytes'] = null;

			// run checks only if we are under thresholds
			if ( $checked_count < self::WEBPAGE_MAX_IMAGE_HEAD && $good_count < self::WEBPAGE_MAX_IMAGES ) {

				// do a head request for the image
				$response =
					wp_remote_head(
						$image['src'],
						array( 'timeout' => 2, 'headers' => array( 'Referer' => $bp->root_domain ) )
					);

				// did we get a valid response?
				if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
					// yes, grab content length (remote file size)
					$content_length = (integer) wp_remote_retrieve_header( $response, 'content-length' );
					// did we get the length header?
					if ( $content_length > 0 ) {
						// set the bytes
						$image['bytes'] = $content_length;
						// check if size is within range
						if ( $content_length >= self::WEBPAGE_MIN_IMAGE_BYTES && $content_length <= self::WEBPAGE_MAX_IMAGE_BYTES ) {
							// increment found count
							$good_count++;
						}
					}
				}
			}

			// append modified image details to return array
			$return_array[] = $image;
		}

		return $return_array;
	}
	
	private function filter_images( $images )
	{
		if ( count( $images ) < 1 ) {
			return $images;
		}

		$array_high = array();
		$array_med = array();
		$array_low = array();
		$array_lowest = array();

		foreach ( $images as $image ) {

			$url = $image['src'];
			$width = $image['width'];
			$height = $image['height'];

			// try to parse url
			$url_parsed = parse_url( $url );

			if ( isset( $url_parsed['path'] ) ) {
				// parsed url successfully
				if ( preg_match( '/\.jpe?g/i', $url_parsed['path'] ) ) {
					// its a JPEG
					if ( $width > 0 && $height > 0 ) {
						// width and height were set in DOM
						$array_high[] = $image;
					} else {
						// width and/or height missing from DOM
						$array_med[] = $image;
					}
				} elseif ( preg_match( '/\.png/i', $url_parsed['path'] ) ) {
					// PNG, lower priority
					$array_low[] = $image;
				} elseif ( preg_match( '/\.gif/i', $url_parsed['path'] ) ) {
					// GIF, lowest priority
					$array_lowest[] = $image;
				} else {
					// some other image type, we don't want it
					continue;
				}
			} else {
				// unable to parse url
				continue;
			}
		}

		return array_merge( $array_high, $array_med, $array_low, $array_lowest );
	}
}


/**
 * PicApp photo embedding service
 *
 * @package BP_Links
 * @author Marshall Sorenson
 */
final class BP_Links_Embed_Service_PicApp
	extends BP_Links_Embed_Service
		implements	BP_Links_Embed_From_Html,
					BP_Links_Embed_Has_Html,
					BP_Links_Embed_Avatar_Only
{
	//
	// required concrete methods
	//

	final public function from_html( $html )
	{
		if ( $this->check( $html ) ) {
			// clean it
			$html = $this->deep_clean_string( $html );
			// parse it
			if ( $this->parse_tag_href( $html ) === true && $this->parse_tag_img( $html ) === true ) {
				return true;
			} else {
				throw new BP_Links_Embed_User_Exception( $this->err_embed_code() );
			}
		} else {
			return false;
		}
	}

	final public function url()
	{
		return sprintf(
			'http://view.picapp.com/default.aspx?term=%s&iid=%d',
			$this->data()->href_term,
			$this->data()->href_iid
		);
	}

	final public function title()
	{
		return $this->data()->img_alt;
	}

	final public function description()
	{
		// no description available
		return null;
	}

	final public function image_url()
	{
		return sprintf(
			'http://cdn.picapp.com%s?adImageId=%d&imageId=%d',
			$this->data()->img_path,
			$this->data()->img_adImageId,
			$this->data()->img_imageId
		);
	}

	final public function image_thumb_url()
	{
		return sprintf(
			'http://cdn.picapp.com%s',
			preg_replace( '/\/ftp\/images\//i', '/ftp/thumbnails/', $this->data()->img_path )
		);
	}

	final public function image_large_thumb_url()
	{
		return $this->image_thumb_url();
	}

	final public function html()
	{
		return $this->html_tag_href( $this->html_tag_img() ) . $this->html_tag_script();
	}

	public function service_name()
	{
		return __( 'PicApp', 'buddypress-links' );
	}

	//
	// optional concrete methods
	//

	final public function image_width()
	{
		return $this->data()->img_width;
	}

	final public function image_height()
	{
		return $this->data()->img_width;
	}

	final public function avatar_class()
	{
		return 'avatar-embed-picapp';
	}

	final public function avatar_max_width()
	{
		return 140;
	}

	final public function avatar_max_height()
	{
		return 140;
	}

	/**
	 * Handle deprecated PicApp data array
	 *
	 * @param string|array $embed_data deprecated PicApp data array or serialized string
	 * @return boolean
	 */
	final public function from_deprecated_data( $embed_data )
	{
		if ( is_array( $embed_data ) ) {
			// copy data from the array into the embed data object
			$this->data()->href_term = $this->deep_clean_string( $embed_data['href']['term'] );
			$this->data()->href_iid = $embed_data['href']['iid'];
			$this->data()->img_path = $embed_data['img']['path'];
			$this->data()->img_adImageId = $embed_data['img']['adImageId'];
			$this->data()->img_imageId = $embed_data['img']['imageId'];
			$this->data()->img_width = $embed_data['img']['width'];
			$this->data()->img_height = $embed_data['img']['height'];
			$this->data()->img_alt = $this->deep_clean_string( $embed_data['img']['alt'] );
			return true;
		} else {
			throw new BP_Links_Embed_Fatal_Exception( 'Deprecated data must be an array' );
		}
	}

	//
	// private methods
	//

	private function check( $string )
	{
		return preg_match( '/picapp\.com/', $string );
	}

	private function parse_tag_href( $string )
	{
		if  ( preg_match( '/<a\shref="http:\/\/view\.picapp\.com\/default\.aspx\?(term=([^&]{1,50})&)?iid=(\d+)"[^>]*>/', $string, $matches ) ) {
			$this->data()->href_term = $this->deep_clean_string( $matches[2] );
			$this->data()->href_iid = $matches[3];
			return true;
		} else {
			return false;
		}
	}

	private function parse_tag_img( $string )
	{
		// match img tag and find path
		if  ( preg_match( '/<img\ssrc="http:\/\/cdn\.picapp\.com([\/a-zA-z0-9]{1,50}\/[\w-]{1,100}\.jpg)\?[^"]+"[^>]+>/', $string, $matches ) ) {
			$img_tag = $matches[0];
			$this->data()->img_path = $matches[1];
		} else {
			return false;
		}

		// match adImageId
		if  ( preg_match( '/adImageId=(\d{1,50})/', $img_tag, $matches ) ) {
			$this->data()->img_adImageId = $matches[1];
		} else {
			return false;
		}

		// match adImageId
		if  ( preg_match( '/imageId=(\d{1,50})/', $img_tag, $matches ) ) {
			$this->data()->img_imageId = $matches[1];
		} else {
			return false;
		}

		// match width
		if  ( preg_match( '/width="(\d{1,4})"/', $img_tag, $matches ) ) {
			$this->data()->img_width = $matches[1];
		} else {
			return false;
		}

		// match height
		if  ( preg_match( '/height="(\d{1,4})"/', $img_tag, $matches ) ) {
			$this->data()->img_height = $matches[1];
		} else {
			return false;
		}

		// match alt text
		if  ( preg_match( '/alt="([^"]{1,100})"/', $img_tag, $matches ) ) {
			$this->data()->img_alt = $this->deep_clean_string( $matches[1] );
		} else {
			return false;
		}

		return true;
	}

	private function html_tag_href( $content )
	{
		return sprintf(
			'<a href="%s" target="_blank">%s</a>',
			esc_url( $this->url() ),
			$content
		);
	}

	private function html_tag_img()
	{
		return sprintf(
			'<img src="%s" width="%d" height="%d"  border="0" alt="%s"/>',
			esc_url( $this->image_url() ),
			$this->data()->img_width,
			$this->data()->img_height,
			esc_attr( $this->data()->img_alt )
		);
	}

	private function html_tag_script()
	{
		return '<script type="text/javascript" src="http://cdn.pis.picapp.com/IamProd/PicAppPIS/JavaScript/PisV4.js"></script>';
	}
}


/**
 * Fotoglif photo embedding service
 *
 * @package BP_Links
 * @author Marshall Sorenson
 */
final class BP_Links_Embed_Service_Fotoglif
	extends BP_Links_Embed_Service
		implements	BP_Links_Embed_From_Html,
					BP_Links_Embed_From_Json,
					BP_Links_Embed_Avatar_Only,
					BP_Links_Embed_Has_Html
{
	//
	// required concrete methods
	//

	final public function from_html( $html )
	{
		if ( $this->check( $html ) ) {

			// clean it up
			$html = $this->deep_clean_string( $html );

			// try to get the basic image info first
			if ( $this->check_publisher($html) ) {
				$result = $this->parse_tag_img( $html );
			} else {
				$result = $this->parse_tag_div( $html );
			}

			// try to get the rest of the data
			if ( $result === true && $this->parse_tag_script( $html ) === true ) {
				return $this->from_json( $this->api_fetch_json() );
			} else {
				throw new BP_Links_Embed_User_Exception( $this->err_embed_code() );
			}
		} else {
			return false;
		}
	}

	final public function from_json( $json )
	{
		// decode json data
		$api_data = json_decode( $json, true );

		// if decoding successful, add details to embed data
		if ( empty( $api_data ) === false && isset( $api_data['response'][0]['image_hash'] ) ) {
			$this->data()->api_image_uid = $api_data['response'][0]['image_uid'];
			$this->data()->api_image_hash = $api_data['response'][0]['image_hash'];
			$this->data()->api_image_date = $api_data['response'][0]['image_date'];
			$this->data()->api_height = $api_data['response'][0]['height'];
			$this->data()->api_width = $api_data['response'][0]['width'];
			$this->data()->api_description = $this->deep_clean_string( $api_data['response'][0]['description'] );
			$this->data()->api_album_name = $this->deep_clean_string( $api_data['response'][0]['album_name'] );
			$this->data()->api_album_uid = $api_data['response'][0]['album_uid'];
			$this->data()->api_album_hash = $api_data['response'][0]['album_hash'];
			// awesome, return true
			return true;
		} else {
			throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
		}
	}

	final public function url()
	{
		return sprintf( 'http://www.fotoglif.com/f/%s/%s', $this->data()->api_album_hash, $this->data()->api_image_hash );
	}

	final public function title()
	{
		return ( isset( $this->data()->api_album_name ) ) ? $this->data()->api_album_name : null;
	}

	final public function description()
	{
		return ( isset( $this->data()->api_description ) ) ? $this->data()->api_description : null;
	}

	final public function image_url()
	{
		return sprintf( 'http://gallery.fotoglif.com/images/large/%s.jpg', $this->data()->api_image_hash );
	}

	final public function image_thumb_url()
	{
		return sprintf( 'http://gallery.fotoglif.com/thumbnails/thumbnail_%s.jpg', $this->data()->api_image_uid );
	}

	final public function image_large_thumb_url()
	{
		return $this->image_thumb_url();
	}

	final public function html()
	{
		return $this->html_div_tag() . $this->html_script_tag();
	}

	public function service_name()
	{
		return __( 'Fotoglif', 'buddypress-links' );
	}

	//
	// optional concrete methods
	//

	final public function image_width()
	{
		return $this->data()->div_width;
	}

	final public function image_height()
	{
		return $this->data()->div_height;
	}

	/**
	 * Handle deprecated Fotoglif data array
	 *
	 * @param string|array $embed_data deprecated PicApp data array or serialized string
	 * @return boolean
	 */
	final public function from_deprecated_data( $embed_data )
	{
		if ( is_array( $embed_data ) ) {
			// copy data from the array into the embed data object
			$this->data()->div_width = $embed_data['div']['width'];
			$this->data()->div_height = $embed_data['div']['height'];
			$this->data()->script_album_hash = $embed_data['script']['album_hash'];
			$this->data()->script_size = $embed_data['script']['size'];
			$this->data()->script_imageuid = $embed_data['script']['imageuid'];
			$this->data()->script_layout = $embed_data['script']['layout'];
			$this->data()->script_jpgembed = $embed_data['script']['jpgembed'];
			$this->data()->script_pubID = $embed_data['script']['pubID'];
			$this->data()->script_pubid = $embed_data['script']['pubid'];
			$this->data()->api_image_uid = $embed_data['script']['imageuid'];
			$this->data()->api_image_hash = $embed_data['api']['hash'];
			$this->data()->api_height = $embed_data['api']['height'];
			$this->data()->api_width = $embed_data['api']['width'];
			$this->data()->api_album_uid = $embed_data['api']['album_uid'];
			return true;
		} else {
			throw new BP_Links_Embed_Fatal_Exception( 'Deprecated data must be an array' );
		}
	}

	//
	// private methods
	//

	private function check( $string )
	{
		return preg_match( '/fotoglif\.com/', $string );
	}

	private function check_publisher( $string )
	{
		return preg_match( '/embed_login\.js/', $string );
	}

	private function check_iframe( $string )
	{
		return preg_match( '/iframe/i', $string );
	}

	private function api_fetch_json()
	{
		// build the URL we will be querying for image data
		$api_url = sprintf( 'http://api.fotoglif.com/image/get?image_uid=%s', $this->data()->script_imageuid );

		// grab JSON data
		return $this->api_fetch( $api_url );
	}

	private function parse_tag_div( $string )
	{
		// match img tag and find path
		if  ( preg_match( '/<div\sid="fotoglif_place_holder_\d+"([^>]+)>/', $string, $matches ) ) {
			$div_attributes = $matches[1];
		} else {
			return false;
		}

		// match width
		if  ( preg_match( '/[^-]width:\s*(\d{1,4})px/', $div_attributes, $matches ) ) {
			$this->data()->div_width = $matches[1];
		} else {
			return false;
		}

		// match height
		if  ( preg_match( '/[^-]height:\s*(\d{1,4})px/', $div_attributes, $matches ) ) {
			$this->data()->div_height = $matches[1];
		} else {
			return false;
		}

		return true;
	}

	private function parse_tag_img( $string )
	{
		// match img tag and find get src URL
		if  ( preg_match( '/<img([^>]+)>/', $string, $matches ) ) {
			$img_attributes = $matches[1];
		} else {
			return false;
		}

		// match width
		if  ( preg_match( '/width:\s*(\d+)px/', $img_attributes, $matches ) ) {
			$this->data()->div_width = $matches[1];
		} else {
			return false;
		}

		// no height given by this embed method
		$this->data()->div_height = null;

		return true;
	}

	private function parse_tag_script( $string )
	{
		// match img tag and find path
		if  ( preg_match( '/<script\stype="[^"]+"\ssrc="http:\/\/www\.fotoglif\.com\/(embed\/)?embed(\.py|_login\.js)\?([^"]+)">\s*<\/script>/', $string, $matches ) ) {
			$query_string = $matches[3];
		} else {
			return false;
		}

		// match hash
		if  ( preg_match( '/hash=([a-z0-9]{1,20})/', $query_string, $matches ) ) {
			$this->data()->script_album_hash = $matches[1];
		} else {
			return false;
		}

		// match size
		if  ( preg_match( '/size=(small|medium|large)/', $query_string, $matches ) ) {
			$this->data()->script_size = $matches[1];
		} else {
			$this->data()->script_size = 'medium';
		}

		// match imageuid
		if  ( preg_match( '/imageuid=(\d{1,20})/', $query_string, $matches ) ) {
			$this->data()->script_imageuid = $matches[1];
		} else {
			return false;
		}

		// match layout
		if  ( preg_match( '/layout=([\w-])/', $query_string, $matches ) ) {
			$this->data()->script_layout = $matches[1];
		} else {
			$this->data()->script_layout = null;
		}

		// match jpgembed
		if  ( preg_match( '/jpgembed=(yes|no)/', $query_string, $matches ) ) {
			$this->data()->script_jpgembed = $matches[1];
		} else {
			return false;
		}

		// match pubID
		if  ( preg_match( '/pubID=([a-z0-9]{1,20})/', $query_string, $matches ) ) {
			$this->data()->script_pubID = $matches[1];
		} else {
			$this->data()->script_pubID = null;
		}

		// match pubid
		if  ( preg_match( '/pubid=([a-z0-9]{1,20})/', $query_string, $matches ) ) {
			$this->data()->script_pubid = $matches[1];
		} else {
			$this->data()->script_pubid = null;
		}

		return true;
	}

	private function html_script_url()
	{
		return sprintf(
			'http://www.fotoglif.com/embed/embed.py?hash=%1$s&size=%2$s&imageuid=%3$d&layout=%4$s&jpgembed=%5$s&pubID=&pubid=%6$s',
			$this->data()->script_album_hash, // arg 1
			$this->data()->script_size, // arg 2
			$this->data()->script_imageuid, // arg 3
			$this->data()->script_layout, // arg 4
			$this->data()->script_jpgembed, // arg 5
			BP_LINKS_EMBED_FOTOGLIF_PUBID // arg 6
		);
	}

	private function html_div_tag()
	{
		$width_style = ( empty( $this->data()->div_width ) ) ? '' : sprintf( ' width: %1$dpx;', $this->data()->div_width );
		$height_style = ( empty( $this->data()->div_height ) ) ? '' : sprintf( ' height: %1$dpx;', $this->data()->div_height );

		return sprintf(
			'<div id="fotoglif_place_holder_%1$d" style="border-style: double; border-width: 5px; border-color: #bbbbbb; background-color: rgb(122, 122, 122);"%2$s%3$s></div>',
			$this->data()->script_imageuid, // arg 1
			$width_style, // arg 2
			$height_style // arg 3
		);
	}

	private function html_script_tag()
	{
		return sprintf(
			'<script type="text/javascript" src="%s"></script>',
			esc_url( $this->html_script_url() )
		);
	}
}

/**
 * YouTube video embedding service
 *
 * @package BP_Links
 * @author Marshall Sorenson
 */
final class BP_Links_Embed_Service_YouTube
	extends BP_Links_Embed_Service
		implements BP_Links_Embed_From_Url, BP_Links_Embed_From_Xml, BP_Links_Embed_Has_Html
{
	// thumb constants
	const YT_TH_DEFAULT = 0;
	const YT_TH_SMALL_1 = 1;
	const YT_TH_SMALL_2 = 2;
	const YT_TH_SMALL_3 = 3;

	//
	// required concrete methods
	//

	final public function from_url( $url )
	{
		if ( $this->check_url( $url ) ) {
			if ( $this->parse_url( $url ) === true ) {
				return $this->from_xml( $this->api_xml_fetch() );
			} else {
				throw new BP_Links_Embed_User_Exception( $this->err_embed_url() );
			}
		} else {
			return false;
		}
	}

	final public function from_xml( $xml )
	{
		// load xml string into a SimpleXML object
		libxml_use_internal_errors(true);
		$sxml = simplexml_load_string( $xml );

		if ( $sxml instanceof SimpleXMLElement ) {

			// set title and content
			$this->data()->api_title = $this->deep_clean_string( (string) $sxml->title );
			$this->data()->api_content = $this->deep_clean_string( (string) $sxml->content );

			// find alternate link
			foreach ( $sxml->link as $link ) {
				if ( 'alternate' == (string) $link['rel'] ) {
					$this->data()->api_link_alt = (string) $link['href'];
					break;
				}
			}

			// make sure we have an alternate link
			if ( empty( $this->data()->api_link_alt ) === false ) {
				// set video hash if missing
				if ( empty( $this->data()->video_hash ) ) {
					$this->parse_url( $this->data()->api_link_alt );
				}
			} else {
				throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
			}

			return true;

		} else {
			// could not load feed data
			throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
		}
	}

	final public function url()
	{
		return $this->data()->api_link_alt;
	}

	final public function title()
	{
		return $this->data()->api_title;
	}

	final public function description()
	{
		return $this->data()->api_content;
	}

	final public function image_url()
	{
		return $this->yt_thumb_url();
	}

	final public function image_thumb_url()
	{
		return $this->yt_thumb_url( self::YT_TH_SMALL_2 );
	}

	final public function image_large_thumb_url()
	{
		return $this->yt_thumb_url( self::YT_TH_DEFAULT );
	}

	final public function html()
	{
		return sprintf(
			'<object width="640" height="385">' .
			'<param name="movie" value="%1$s"></param>' .
			'<param name="allowFullScreen" value="true"></param>' .
			'<param name="allowscriptaccess" value="always"></param>' .
			'<embed src="%1$s" type="application/x-shockwave-flash" ' .
				'allowscriptaccess="always" allowfullscreen="true" ' .
				'width="640" height="385"></embed>' .
			'</object>',
			esc_url( $this->yt_player_url() )
		);
	}

	public function service_name()
	{
		return __( 'YouTube', 'buddypress-links' );
	}

	public function from_url_pattern()
	{
		return '/^http:\/\/(www\.)?youtube.com\/watch/';
	}

	//
	// optional concrete methods
	//

	final public function avatar_play_video()
	{
		return true;
	}

	//
	// private methods
	//

	private function check_url( $url )
	{
		return preg_match( '/^http:\/\/(www\.)?youtube\.com\/watch.+$/', $url );
	}

	private function parse_url( $url )
	{
		// parse the url
		$url_parsed = parse_url( $url );

		// make sure we got something
		if ( !empty( $url_parsed['query'] ) ) {

			// parse the query string
			parse_str( $url_parsed['query'], $qs_vars );

			// get the video hash
			if ( !empty( $qs_vars['v'] ) ) {
				$this->data()->video_hash = $qs_vars['v'];
				return true;
			}
		}

		return false;
	}

	private function api_xml_url()
	{
		return sprintf( 'http://gdata.youtube.com/feeds/api/videos/%s', $this->data()->video_hash );
	}

	private function api_xml_fetch()
	{
		// get ATOM feed data for this video
		return $this->api_fetch( $this->api_xml_url() );
	}

	private function api_xml_thumbs( SimpleXMLElement $sxml )
	{
		// get nodes in media: namespace for media information
		$media = $sxml->children('http://search.yahoo.com/mrss/');

		// do we have thumbs to look at?
		if ( $media instanceof SimpleXMLElement && $media->group->thumbnail instanceof SimpleXMLElement ) {
			return $media->group->thumbnail;
		} else {
			return false;
		}
	}

	private function yt_player_url()
	{
		return sprintf( 'http://www.youtube.com/v/%s&hl=%s&fs=1&&autoplay=1', $this->data()->video_hash, get_locale() );
	}

	private function yt_thumb_url( $num = self::YT_TH_DEFAULT )
	{
		if ( is_numeric( $num ) && $num >= self::YT_TH_DEFAULT && $num <= self::YT_TH_SMALL_3 ) {
			return sprintf( 'http://img.youtube.com/vi/%s/%d.jpg', $this->data()->video_hash, $num );
		} else {
			throw new BP_Links_Embed_Fatal_Exception( 'YouTube thumbnail number must 0, 1, 2, or 3.' );
		}
	}
}

/**
 * Flickr photo and video embedding service
 *
 * @link http://www.flickr.com/services/api/
 * @package BP_Links
 * @author Marshall Sorenson
 */
final class BP_Links_Embed_Service_Flickr
	extends BP_Links_Embed_Service
		implements BP_Links_Embed_From_Url, BP_Links_Embed_From_Json, BP_Links_Embed_Has_Html
{
	// Flickr API keys
	const FLICKR_API_KEY = 'e5fe3652529c0f75332019c3605cd46e';
	const FLICKR_API_SECRET = '7600876afd78c7a2';

	// Flickr media types
	const FLICKR_MEDIA_PHOTO = 'photo';
	const FLICKR_MEDIA_VIDEO = 'video';

	// Flickr image sizes
	const FLICKR_IMAGE_SQUARE = 's'; // 75 x 75
	const FLICKR_IMAGE_THUMB = 't'; // 100 x N
	const FLICKR_IMAGE_SMALL = 'm'; // 240 x N
	const FLICKR_IMAGE_MEDIUM = null; // 500 x N
	const FLICKR_IMAGE_LARGE = 'b'; // 1024 x N
	
	//
	// required concrete methods
	//

	final public function from_url( $url )
	{
		if ( $this->check_url( $url ) ) {
			if ( $this->parse_url( $url ) === true ) {
				return $this->from_json( $this->api_json_fetch( 'flickr.photos.getInfo' ) );
			} else {
				throw new BP_Links_Embed_User_Exception( $this->err_embed_url() );
			}
		} else {
			return false;
		}
	}

	final public function from_json( $json )
	{
		// decode json data
		$api_data = json_decode( $json, true );

		// if decoding successful, add details to embed data
		if ( !empty( $api_data[self::FLICKR_MEDIA_PHOTO] ) ) {

			// photo array
			$photo = $api_data[self::FLICKR_MEDIA_PHOTO];

			// copy values
			$this->data()->api_id = $photo['id'];
			$this->data()->api_secret = $photo['secret'];
			$this->data()->api_server = $photo['server'];
			$this->data()->api_farm = $photo['farm'];
			$this->data()->api_license = $photo['license'];
			$this->data()->api_title = $this->deep_clean_string( $photo['title']['_content'] );
			$this->data()->api_description = $this->deep_clean_string( $photo['description']['_content'] );

			// try for media type
			switch ( $photo['media'] ) {
				case self::FLICKR_MEDIA_PHOTO:
					$this->data()->api_media = self::FLICKR_MEDIA_PHOTO;
					break;
				case self::FLICKR_MEDIA_VIDEO:
					$this->data()->api_media = self::FLICKR_MEDIA_VIDEO;
					break;
				default:
					throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
			}
			
			// try for the url
			if ( !empty( $photo['urls']['url'][0]['type'] ) && $photo['urls']['url'][0]['type'] == 'photopage' ) {
				$this->data()->api_url = $this->deep_clean_string( $photo['urls']['url'][0]['_content'] );
			}

			// make sure we REALLY have a good url
			if ( $this->parse_url( $this->data()->api_url ) !== true ) {
				throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
			}
			
			// made it!
			return true;
			
		} else {
			throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
		}
	}

	final public function url()
	{
		return $this->data()->api_url;
	}

	final public function title()
	{
		return $this->data()->api_title;
	}

	final public function description()
	{
		return $this->data()->api_description;
	}

	final public function image_url()
	{
		return $this->flickr_image( self::FLICKR_IMAGE_MEDIUM );
	}

	final public function image_thumb_url()
	{
		return $this->flickr_image( self::FLICKR_IMAGE_THUMB );
	}

	final public function image_large_thumb_url()
	{
		return $this->flickr_image( self::FLICKR_IMAGE_SMALL );
	}

	final public function html()
	{
		switch ( $this->data()->api_media ) {
			case self::FLICKR_MEDIA_PHOTO:
				return $this->html_photo();
			case self::FLICKR_MEDIA_VIDEO:
				return $this->html_video();
			default:
				// this should never happen!
				return null;
		}
	}

	final public function service_name()
	{
		return __( 'Flickr', 'buddypress-links' );
	}

	final public function from_url_pattern()
	{
		return '/^http:\/\/(www\.)?flickr\.com\/photos\/[^\/]+\/\d+\//';
	}

	//
	// optional concrete methods
	//

	final public function avatar_play_photo()
	{
		return ( self::FLICKR_MEDIA_PHOTO == $this->data()->api_media );
	}

	final public function avatar_play_video()
	{
		return ( self::FLICKR_MEDIA_VIDEO == $this->data()->api_media );
	}

	//
	// private methods
	//

	private function check_url( $url )
	{
		return preg_match( $this->from_url_pattern(), $url );
	}

	private function parse_url( $url )
	{
		// parse the url
		$url_parsed = parse_url( $url );

		// make sure we got something
		if ( !empty( $url_parsed['path'] ) ) {

			// get the video id
			if ( preg_match( '/^\/photos\/[^\/]+\/(\d+)\//', $url_parsed['path'], $matches ) ) {
				// must save this as a string, as its too long to be an integer
				$this->data()->photo_id = (string) $matches[1];
				return true;
			}
		}

		return false;
	}

	private function flickr_image( $size = self::FLICKR_IMAGE_MEDIUM )
	{
		$suffix = ( $size ) ? '_' . $size : null;
		
		return sprintf(
			'http://farm%1$s.static.flickr.com/%2$s/%3$s_%4$s%5$s.jpg',
			$this->data()->api_farm, // arg 1
			$this->data()->api_server, // arg 2
			$this->data()->api_id, // arg 3
			$this->data()->api_secret, // arg 4
			$suffix // arg 5
		);
	}

	private function html_photo()
	{
		return sprintf(
			'<img src="%1$s" alt="%2$s">',
			esc_url( $this->image_url() ),
			esc_attr( $this->data()->api_title )
		);
	}

	private function html_video()
	{
		return sprintf(
			'<object type="application/x-shockwave-flash" width="400" height="300"
				data="http://www.flickr.com/apps/video/stewart.swf?v=71377"
				classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
				<param name="flashvars" value="photo_secret=%2$s&photo_id=%1$s&flickr_show_info_box=true"></param>
				<param name="movie" value="http://www.flickr.com/apps/video/stewart.swf?v=71377"></param>
				<param name="bgcolor" value="#000000"></param>
				<param name="allowFullScreen" value="true"></param>
				<embed type="application/x-shockwave-flash" height="300" width="400"
					src="http://www.flickr.com/apps/video/stewart.swf?v=71377"
					bgcolor="#000000" allowfullscreen="true"
					flashvars="photo_secret=%2$s&photo_id=%1$s&flickr_show_info_box=true">
				</embed>
			</object>',
			esc_attr( $this->data()->api_id ), // arg 1
			esc_attr( $this->data()->api_secret ) // arg 2
		);
	}

	private function api_rest_url( $method, $format = 'rest' )
	{
		return sprintf( 'http://www.flickr.com/services/rest/?method=%1$s&photo_id=%2$s&format=%3$s&api_key=%4$s&nojsoncallback=1', $method, $this->data()->photo_id, $format, self::FLICKR_API_KEY );
	}

	private function api_json_fetch( $method )
	{
		// get RSS2 feed data for this video
		return $this->api_fetch( $this->api_rest_url( $method, 'json' ) );
	}
}

/**
 * MetaCafe video embedding service
 *
 * @link http://help.metacafe.com/?page_id=181
 * @package BP_Links
 * @author Marshall Sorenson
 */
final class BP_Links_Embed_Service_MetaCafe
	extends BP_Links_Embed_Service
		implements BP_Links_Embed_From_Url, BP_Links_Embed_From_Xml, BP_Links_Embed_Has_Html
{

	//
	// required concrete methods
	//

	final public function from_url( $url )
	{
		if ( $this->check_url( $url ) ) {
			if ( $this->parse_url( $url ) === true ) {
				return $this->from_xml( $this->api_xml_fetch() );
			} else {
				throw new BP_Links_Embed_User_Exception( $this->err_embed_url() );
			}
		} else {
			return false;
		}
	}

	final public function from_xml( $xml )
	{
		// load xml string into a SimpleXML object
		libxml_use_internal_errors(true);
		$sxml = simplexml_load_string( $xml );

		if ( $sxml instanceof SimpleXMLElement ) {

			// get nodes in media: namespace for media information
			$media = $sxml->channel->item->children('http://search.yahoo.com/mrss/');

			// do we have media namespace to look at?
			if ( $media instanceof SimpleXMLElement ) {
				// set title and content
				$this->data()->api_title = $this->deep_clean_string( (string) $media->title );
				$this->data()->api_description = $this->deep_clean_string( (string) $media->description );

				$cont_attrs = $media->content->attributes();
				$this->data()->api_content_url = $this->deep_clean_string( (string) $cont_attrs['url'] );
			} else {
				return false;
			}

			// set alternate link
			$this->data()->api_link_alt = (string) $sxml->channel->item->link;

			// make sure we have an alternate link
			if ( empty( $this->data()->api_link_alt ) === false ) {
				// set video id if missing
				if ( empty( $this->data()->video_id ) ) {
					$this->parse_url( $this->data()->api_link_alt );
				}
			} else {
				throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
			}

			return true;

		} else {
			// could not load feed data
			throw new BP_Links_Embed_User_Exception( $this->err_api_fetch() );
		}
	}

	final public function url()
	{
		return $this->data()->api_link_alt;
	}

	final public function title()
	{
		return $this->data()->api_title;
	}

	final public function description()
	{
		return $this->data()->api_description;
	}

	final public function image_url()
	{
		return $this->image_thumb_url();
	}

	final public function image_thumb_url()
	{
		return sprintf( 'http://www.metacafe.com/thumb/%s.jpg', $this->data()->video_id );
	}

	final public function image_large_thumb_url()
	{
		return $this->image_thumb_url();
	}

	final public function html()
	{
		return sprintf(
			'<embed src="%1$s"
				width="498" height="423" wmode="transparent"
				pluginspage="http://www.macromedia.com/go/getflashplayer"
				type="application/x-shockwave-flash" allowFullScreen="true"
				allowScriptAccess="always" name="Metacafe_%2$s">
			</embed>',
			esc_url( $this->data()->api_content_url ),
			$this->data()->video_id
		);
	}

	public function service_name()
	{
		return __( 'MetaCafe', 'buddypress-links' );
	}

	public function from_url_pattern()
	{
		return '/^http:\/\/(www\.)?metacafe.com\/watch\//';
	}

	//
	// optional concrete methods
	//

	final public function avatar_play_video()
	{
		return true;
	}

	//
	// private methods
	//

	private function check_url( $url )
	{
		return preg_match( $this->from_url_pattern(), $url );
	}

	private function parse_url( $url )
	{
		// parse the url
		$url_parsed = parse_url( $url );

		// make sure we got something
		if ( !empty( $url_parsed['path'] ) ) {

			// get the video id
			if ( preg_match( '/^\/watch\/(\d+)\//', $url_parsed['path'], $matches ) ) {
				// save this as a string in case its a huge integer!
				$this->data()->video_id = (string) $matches[1];
				return true;
			}
		}

		return false;
	}

	private function api_xml_url()
	{
		return sprintf( 'http://www.metacafe.com/api/item/%s', $this->data()->video_id );
	}

	private function api_xml_fetch()
	{
		// get RSS2 feed data for this video
		return $this->api_fetch( $this->api_xml_url() );
	}
}
?>
