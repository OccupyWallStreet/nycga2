<?php

/**
* Generic object functions
*/
class Mappress_Obj {
	function Mappress_Obj($atts=null) {
		$this->update($atts);
	}

	function update($atts=null) {
		if (!$atts)
			return;

		$obj_atts = get_object_vars($this);

		foreach ($obj_atts as $key => $value ) {
			$newvalue = (isset($atts[$key])) ? $atts[$key] : null;

			// Allow attributes to be all lowercase to handle shortcodes
			if ($newvalue === null) {
				$lkey = strtolower($key);
				$newvalue = (isset($atts[$lkey])) ? $atts[$lkey] : null;
			}

			if ($newvalue === null)
				continue;

			// Convert any string versions of true/false
			if ($newvalue === "true")
				$newvalue = true;
			if ($newvalue === "false")
				$newvalue = false;

			$this->$key = $newvalue;
		}
	}
} // End class Mappress_Obj

/**
* POIs
*/
class Mappress_Poi extends Mappress_Obj {
	var $point = array('lat' => 0, 'lng' => 0),
		$title = '',
		$url = null,
		$body = '',
		$address = null,
		$correctedAddress = null,
		$iconid = null,
		$viewport = null,       // array('sw' => array('lat' => 0, 'lng' => 0), 'ne' => array('lat' => 0, 'lng' => 0))
		$user = false,          // If this marker represent's the user geolocation
		$showPoiList = true,    // True = show this marker in the marker list
		$poiListTemplate = null;

	/**
	* Geocode an address using http
	*
	* @param mixed $auto true = automatically update the poi, false = return raw geocoding results
	* @return true if auto=true and success | raw geocoding results if auto=false | WP_Error on failure
	*/
	function geocode($auto=true) {
		// If point was defined using only lat/lng then no geocoding
		if (!empty($this->point['lat']) && !empty($this->point['lng'])) {
			// Default title if empty
			if (empty($this->title))
				$this->title = $this->point['lat'] . ',' . $this->point['lng'];
			return;
		}

		$options = Mappress_Options::get();
		$language = $options->language;
		$country = $options->country;

		$address = urlencode($this->address);
		$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&output=json";
		if ($country)
			$url .= "&region=$country";
		if ($language)
			$url .= "&language=$language";

		$response = wp_remote_get($url);

		// If auto=false, then return the RAW result
		if (!$auto)
			return $response;

		// Check for http error
		if (is_wp_error($response))
			return $response;

		if (!$response)
			return new WP_Error('geocode', sprintf(__('No geocoding response from Google: %s', 'mappress'), $response));

		//Decode response and automatically use first address
		$response = json_decode($response['body']);

		// Discard empty results
		foreach((array)$response->results as $key=>$result) {
			if(empty($result->formatted_address))
				unset($response->results[$key]);
		}

		$status = isset($response->status) ? $response->status : null;
		if ($status != 'OK')
			return new WP_Error('geocode', sprintf(__("Google cannot geocode address: %s, status: %s", 'mappress'), $this->address, $status));

		if (!$response  || !isset($response->results) || empty($response->results[0]) || !isset($response->results[0]))
			return new WP_Error('geocode', sprintf(__("No geocoding result for address: %s", 'mappress'), $this->address));

		$placemark = $response->results[0];

		// Point
		$this->point = array('lat' => $placemark->geometry->location->lat, 'lng' => $placemark->geometry->location->lng);

		// Viewport
		// As of 7/27/11, Google has suddenly stopped returning viewports for street addresses
		if (isset($placemark->geometry->viewport)) {
			$this->viewport = array(
				'sw' => array('lat' => $placemark->geometry->viewport->southwest->lat, 'lng' => $placemark->geometry->viewport->southwest->lng),
				'ne' => array('lat' => $placemark->geometry->viewport->northeast->lat, 'lng' => $placemark->geometry->viewport->northeast->lng)
			);
		} else {
			$this->viewport = null;
		}

		// Corrected address
		$this->correctedAddress = $placemark->formatted_address;

		$parsed = Mappress_Poi::parse_address($this->correctedAddress);

		// If the title and body are not populated then default them
		if (!$this->title && !$this->body) {
			$this->title = $parsed[0];
			if ($parsed[1])
				$this->body = $parsed[1];
		}
	}

	/**
	* Static function to parse an address.  It will split the address into 1 or 2 lines, as appropriate
	*
	* @param mixed $address
	* @return array $result - array containing 1 or 2 address lines
	*/
	function parse_address($address) {
		// USA Addresses
		if (strstr($address, ', USA')) {
			// Remove 'USA'
			$address = str_replace(', USA', '', $address);

			// If there's exactly ONE comma left then return a single line, e.g. "New York, NY"
			if (substr_count($address, ',') == 1) {
				return array($address);
			}
		}

		// If NO commas then use a single line, e.g. "France" or "Ohio"
		if (!strpos($address, ','))
			return array($address);

		// Otherwise return first line from before first comma+space, second line after, e.g. "Paris, France" => "Paris<br>France"
		// Or "1 Main St, Brooklyn, NY" => "1 Main St<br>Brooklyn, NY"
		return array(
			substr($address, 0, strpos($address, ",")),
			substr($address, strpos($address, ",") + 2)
		);
	}
} // End class Mappress_Poi



/**
* Map class
*/
class Mappress_Map extends Mappress_Obj {
	var $mapid = null,
		$width = 425,
		$height = 350,
		$zoom = null,
		$center = array('lat' => 0, 'lng' => 0),
		$mapTypeId = 'roadmap',
		$title = 'Untitled',
		$metaKey = null,
		$pois = array();

	function Mappress_Map($atts=null) {
		$parent = get_parent_class($this);
		$this->$parent($atts);
		$this->_fixup_pois();
	}

	function _fixup_pois() {
		// Convert POIs from arrays to objects if needed
		foreach((array)$this->pois as $index => $poi) {
			if (is_array($poi))
				$this->pois[$index] = new Mappress_Poi($poi);
		}
	}

	function db_create() {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$wpdb->show_errors(true);

		$result = $wpdb->query ("CREATE TABLE IF NOT EXISTS $maps_table (
								mapid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
								obj LONGTEXT)
								CHARACTER SET utf8;");

		$result = $wpdb->query ("CREATE TABLE IF NOT EXISTS $posts_table (
								postid INT,
								mapid INT,
								PRIMARY KEY (postid, mapid) )
								CHARACTER SET utf8;");

		$wpdb->show_errors(false);
	}

	/**
	* Get a map.  Called statically.
	*
	* @param mixed $mapid
	* @return mixed false if failure, or a map object on success
	*/
	function get($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $maps_table WHERE mapid = %d", $mapid) );  // May return FALSE or NULL

		if (!$result)
			return false;

		// Fix up mapid
		$map = unserialize($result->obj);
		$map->mapid = $result->mapid;
		return $map;
	}

	/**
	* Returns ALL maps
	*
	* @return mixed false if failure, array of maps if success
	*
	*/
	function get_list() {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $maps_table"));

		if ($results === false)
			return false;

		// Fix up mapid
		foreach ($results as $result) {
			$map = unserialize($result->obj);
			$map->mapid = $result->mapid;
			$maps[] = $map;
		}

		return $maps;
	}

	function save($postid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$map = serialize($this);

		// Update map
		if (!$this->mapid) {
			// If no ID then autonumber
			$result = $wpdb->query($wpdb->prepare("INSERT INTO $maps_table (obj) VALUES(%s)", $map));
			$this->mapid = (int)$wpdb->get_var("SELECT LAST_INSERT_ID()");
		} else {
			// Id provided, so insert or update
			$result = $wpdb->query($wpdb->prepare("INSERT INTO $maps_table (mapid, obj) VALUES(%d, '%s') ON DUPLICATE KEY UPDATE obj = %s", $this->mapid, $map, $map));
		}

		if ($result === false || !$this->mapid)
			return false;

		// Update posts
		$result = $wpdb->query($wpdb->prepare("INSERT INTO $posts_table (postid, mapid) VALUES(%d, %d) ON DUPLICATE KEY UPDATE postid = %d, mapid = %d", $postid, $this->mapid,
			$postid, $this->mapid));

		if ($result === false)
			return false;

		$wpdb->query("COMMIT");
		return $this->mapid;
	}

	/**
	* Delete a map and all of its post assignments
	*
	* @param mixed $mapid
	*/
	function delete($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Delete from posts table
		$result = $wpdb->query($wpdb->prepare("DELETE FROM $posts_table WHERE mapid = %d", $mapid));
		if ($result === false)
			return false;

		$result = $wpdb->query($wpdb->prepare("DELETE FROM $maps_table WHERE mapid = %d", $mapid));
		if ($result === false)
			return false;

		$wpdb->query("COMMIT");
		return true;
	}

	/**
	* Delete a map assignment(s) for a post
	* If $mapid is null, then ALL maps will be removed from the post
	*
	* @param int $mapid Map to remove
	* @param int $postid Post to remove from
	* @return TRUE if map has been removed, FALSE if map wasn't assigned to the post
	*/
	function delete_post_map($postid, $mapid=null) {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		if (!$postid)
			return true;

		if ($mapid)
			$results = $wpdb->get_results($wpdb->query("DELETE FROM $posts_table WHERE postid = %d AND mapid = %d", $postid, $mapid));
		else
			$results = $wpdb->get_results($wpdb->query("DELETE FROM $posts_table WHERE postid = %d", $postid));

		$wpdb->query("COMMIT");

		if ($results === false)
			return false;

		return true;
	}

	/**
	* Get a single map attached to a post
	*
	* @param int $postid Post for which to get the list
	* @param int $mapid Map id of the map to retrieve
	* @param string $meta_key (optional) retrieve map for a given meta_key (assumption is that there can be only one)
	* @param int $postid Post for which to get the list
	*
	* @return a single Map object or FALSE if no map exist for the given criteria
	*/
	function get_post_map ($postid, $mapid = null, $meta_key = null) {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Search by map ID
		if ($mapid) {
			$result = $wpdb->get_row($wpdb->prepare("SELECT postid, mapid FROM $posts_table WHERE postid = %d AND mapid = %d", $postid, $mapid));
			if ($result !== false)
				return Mappress_Map::get($mapid);
			else
				return false;
		}

		// Search by meta_key
		$results = $wpdb->get_results($wpdb->prepare("SELECT postid, mapid FROM $posts_table WHERE postid = %d", $postid));

		if ($results === false)
			return false;

		// Find which map, if any, has the given meta_key
		foreach($results as $key => $result) {
			$map = Mappress_Map::get($result->mapid);
			if ($map->metaKey == $meta_key)
				return $map;
		}
		return false;
	}


	/**
	* Get a list of maps attached to the post
	*
	* @param int $postid Post for which to get the list
	* @return an array of all maps for the post or FALSE if no maps
	*/
	function get_post_map_list($postid) {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$results = $wpdb->get_results($wpdb->prepare("SELECT postid, mapid FROM $posts_table WHERE postid = %d", $postid));

		if ($results === false)
			return false;

		// Get all of the maps
		$maps = array();
		foreach($results as $key => $result) {
			$maps[] = Mappress_Map::get($result->mapid);
		}
		return $maps;
	}

	/**
	* Display a map
	*
	* @param mixed $atts - override attributes.  Attributes applied from options -> map -> $atts
	*/
	function display($atts = null) {
		global $mappress;
		static $div = 0;

		$options = Mappress_Options::get();

		// Update the options and map settings with any passed attributes
		$options->update($atts);
		$this->update($atts);

		// For anyone using WPML (wpml.org): set the selected language if it wasn't specified in the options screen
		if (defined('ICL_LANGUAGE_CODE') && !$options->language)
			$options->language = ICL_LANGUAGE_CODE;

		$width = $this->_px($this->width);
		$height = $this->_px($this->height);

		// Container holds the map + poi list + directions
		// It requires a width (otherwise it'll default to 100% and can't be centered) but no height (so it can expand for directions/poilist)
		$container_style = "width:$width; ";
		$container_class = "mapp-container";

		// The canvas is just the map
		$canvas_style = "width:$width; height:$height; ";
		$canvas_class = "mapp-canvas";

		// The canvas panel is a container for the map
		$canvas_panel_style = "width:$width; height:$height; ";
		$canvas_panel_class = "mapp-canvas-panel";

		$poi_list_class = "mapp-poi-list";
		$poi_list_style = "width:$width;max-height:$height; ";      // POI list has a max-height to encourage scrollbars if too tall


		// Apply border to canvas
		// Note that chrome will allow map edges to stick out past rounded corners
		if ($options->border && isset($options->border['style']) && !empty($options->border['style'])) {
			$radius = $this->_px( (int)$options->border['radius'] );
			$canvas_panel_style .= sprintf("border: %s %s %s; ", $options->border['width'], $options->border['style'], $options->border['color']);
			$canvas_panel_style .= " border-radius: $radius; -moz-border-radius: $radius; -webkit-border-radius: $radius; -o-border-radius:$radius ";
			$canvas_panel_class .= ($options->border['shadow']) ? " mapp-canvas-panel-shadow" : "";
		}

		switch ($options->alignment) {
			case 'left' :
				$container_style .= "float:left ";
				break;
			case 'right' :
				$container_style .= "float:right;";
				break;
			case 'center' :
				$container_style .= "margin-left:auto !important; margin-right:auto !important; ";
				break;
		}

		// Assign a map name if none provided
		if (!isset($options->mapName)) {
			$options->mapName = "mapp$div";
			$div++;
		}

		// Use default POI list template for each row if no alternate template was provided
		if ($options->poiList) {
			foreach($this->pois as $i => $poi) {
				if (!$poi->poiListTemplate)
					$this->pois[$i]->poiListTemplate = $options->poiListTemplate;
			}
		}

		// Apply filters to override map data before display
		// 2.39 $this->pois = apply_filters('mapp_map_pois', $this->pois, $this, $options);

		Mappress_Map::_load($options);

		echo "<script type='text/javascript'>"
			. "/* <![CDATA[ */"
			. "var mapdata = " . json_encode($this) . ";"
			. "var options = " . json_encode($options) . ";"
			. "var $options->mapName = new MappMap(mapdata, options);"
			. "$options->mapName.display();"
			. "/* ]]> */"
			. "</script>";

		$html = "<div class='$container_class' style='$container_style'>"
			. "<div class='$canvas_panel_class' style='$canvas_panel_style'>"
			. "<div id='$options->mapName' class='$canvas_class' style='$canvas_style'></div>"
			. "</div>";

		// List of locations
		if ($options->poiList) {
			$html .= "<div id='{$options->mapName}_poi_list' class='$poi_list_class' style='$poi_list_style'></div>";
		}

		if ($options->directions == 'inline')
			$html .= apply_filters('mapp_directions_html', null, $this, $options);

		$html .= "</div>";
		return $html;
	}

	function edit($maps = null, $postid) {
		global $mappress;

		// Set options for editing
		$options = Mappress_Options::get();
		$options->postid = $postid;
		$options->mapName = 'mapp0';
		$options->directions = 'none';
		$options->mapTypeControl = true;
		$options->navigationControlOptions = array('style' => 0);
		$options->initialOpenInfo = false;
		$options->traffic = false;
		$options->editable = true;
		$options->overviewMapControl = true;
		$options->overviewMapControlOptions = array('opened' => false);

		Mappress_Map::_load($options);
		echo "<script type='text/javascript'>"
			. "/* <![CDATA[ */"
			. "var mapdata = " . json_encode($maps) . ";"
			. "var options = " . json_encode($options) . ";"
			. "var version = '" . $mappress->get_version() . "';"
			. "var mappEditor = new MappEditor(mapdata, options);"
			. "/* ]]> */"
			. "</script>";

		?>
		<div id='mapp_metabox'>
			<div style='border-bottom:1px solid black; overflow: auto'>
				<div>
					<br/>
					<a target='_blank' style='vertical-align: middle;text-decoration:none'  href='http://wphostreviews.com/mappress'>
						<img alt='MapPress' title='MapPress' src='<?php echo plugins_url('images/mappress_logo_small.png', __FILE__); ?>' />
					</a>
					<?php echo $mappress->get_version(); ?>
					| <a target='_blank' href='http://wphostreviews.com/mappress/mappress-documentation'><?php _e('Documentation', 'mappress')?></a>
					| <a target='_blank' href='http://wphostreviews.com/mappress/mappress-faq'><?php _e('FAQ', 'mappress')?></a>
					| <a target='_blank' href='http://wphostreviews.com/mappress/chris-contact'><?php _e('Report a bug', 'mappress')?></a>

					<?php if (!class_exists('Mappress_Pro')) { ?>
							<input id='mapp_paypal' style='vertical-align: middle;width:92px;height:26px' type='image' src='<?php echo plugins_url('images/btn_donate_LG.gif', __FILE__);?>' name='donate' alt='PayPal - The safer, easier way to pay online!' />
					<?php } ?>

				</div>

				<div id='mapp_add_panel' style='visibility:hidden'>
					<p>
						<span class='submit' style='padding: 0; float: none' >
							<input class='button-primary' type='button' id='mapp_add_btn' value='<?php _e('Add', 'mappress'); ?>' />
						</span>

						<span  id='mapp_add_address'>
							<b><?php _e('Location', 'mappress') ?>: </b>
							<input size='50' type='text' id='mapp_saddr' />
						</span>

						<br/><span id='mapp_saddr_corrected' class='mapp-address-corrected'></span>
					</p>
				</div>
			</div>

			<table style='width:100%'>
				<tr>
					<td valign="top">
						<div id='mapp_left_panel'>
							<div id='mapp_maplist_panel'>
								<p>
									<b><?php _e('Current Maps', 'mappress')?></b>
									<input class='button-primary' type='button' id='mapp_create_btn' value='<?php _e('New Map', 'mappress')?>' />
								</p>

								<div id='mapp_maplist'></div>
							</div>

							<div id='mapp_adjust_panel' style='display:none'>
								<div id='mapp_adjust'>
									<p>
										<b><?php _e('Map ID', 'mappress')?>: </b><span id='mapp_mapid'></span>
									</p>
									<p>
										<b><?php _e('Title')?>: </b><input id='mapp_title' type='text' size='20' />
									</p>
									<p>
										<?php
											foreach($options->mapSizes as $i => $size) {
												echo ($i > 0) ? " | " : "";
												$wh = $size['width'] . 'x' . $size['height'];
												echo "<a href='#' class='mapp-edit-size' title='$wh'>$wh</a>";
											}
										?>
										<br/><input type='text' id='mapp_width' size='2' value='' /> x <input type='text' id='mapp_height' size='2' value='' />
									</p>
									<p class='submit' style='padding: 0; float: none' >
										<input class='button-primary' type='button' id='mapp_save_btn' value='<?php _e('Save', 'mappress'); ?>' />
										<input type='button' id='mapp_recenter_btn' value='<?php _e('Center', 'mappress'); ?>' />
									</p>
									<hr/>
								</div>
								<div id='<?php echo $options->mapName?>_poi_list' class='mapp-edit-poi-list'></div>
							</div>
						</div>
					</td>
					<td id='mapp_preview_panel' valign='top'>
						<div id='<?php echo $options->mapName?>' class='mapp-edit-canvas'></div>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	function _load($options) {
		global $mappress;
		static $loaded;

		if ($loaded)
			return;
		else
			$loaded = true;

		$url = (isset($_GET['mp_remote'])) ? "http://localhost/wordpress/wp-content/plugins/mappress-google-maps-for-wordpress" : plugins_url('', __FILE__);
		$min = ($mappress->debug) ? "" : ".min";

		echo "<script type='text/javascript' src='http://www.google.com/jsapi'></script>";
		echo "<script type='text/javascript' src='$url/mappress_lib.min.js?version=" . Mappress::$version . "'></script>";
		echo "<script type='text/javascript' src='$url/mappress$min.js?version=" . Mappress::$version . "'></script>";

		if (class_exists('Mappress_Pro')) {
			Mappress_Pro::_load_icons( plugins_url('', __FILE__), $options );
		}

		$script = "var mappl10n = " . json_encode(Mappress_Map::_localize()) . ";"
			. "var s = document.createElement('LINK'); s.rel = 'stylesheet'; s.type = 'text/css';"
			. "s.href = '$url/css/mappress.css?version=" . Mappress::$version . "'; document.getElementsByTagName('head').item(0).appendChild(s);";

		echo "<script type='text/javascript'>/* <![CDATA[ */ $script /* ]]> */</script>";
	}

	function _localize() {
		// Localize script texts
		return array(
			'maps_in_post' => __('Maps in this post', 'mappress'),
			'no_maps_in_post' => __('There are no maps yet for this post', 'mappress'),
			'create_map' => __('Create a new map', 'mappress'),
			'map_id' => __('Map ID', 'mappress'),
			'untitled' => __('Untitled', 'mappress'),
			'dir_not_found' => __('The starting or ending address could not be found.', 'mappress'),
			'dir_zero_results' => __('Google cannot return directions between those addresses.  There is no route between them or the routing information is not available.', 'mappress'),
			'dir_default' => __('Unknown error, unable to return directions.  Status code = ', 'mappress'),
			'enter_address' => __('Enter address', 'mappress'),
			'no_address' => __('No matching address', 'mappress'),
			'did_you_mean' => __('Did you mean: ', 'mappress'),
			'directions' => __('Directions', 'mappress'),
			'edit' => __('Edit', 'mappress'),
			'save' => __('Save', 'mappress'),
			'cancel' => __('Cancel', 'mappress'),
			'del' => __('Delete', 'mappress'),
			'view' => __('View', 'mappress'),
			'back' => __('Back', 'mappress'),
			'insert_into_post' => __('Insert into post', 'mappress'),
			'select_a_map' => __('Select a map', 'mappress'),
			'title' => __('Title', 'mappress'),
			'delete_prompt' => __('Delete this map marker?', 'mappress'),
			'delete_map_prompt' => __('Delete this map?', 'mappress'),
			'del' => __('Delete', 'mappress'),
			'map_saved' => __('Map saved', 'mappress'),
			'map_deleted' => __('Map deleted', 'mappress'),
			'ajax_error' => __('Error: AJAX failed!  ', 'mappress'),
			'click_and_drag' => __('Click & drag to move this marker', 'mappress'),
			'zoom' => __('Zoom', 'mappress'),
			'traffic' => __('Traffic', 'mappress'),
			'standard_icons' => __('Standard icons', 'mappress'),
			'my_icons' => __('My icons', 'mappress')
		);
	}


	/**
	* Filter to alter map POIs before display
	*
	* Default filter adds a marker for the user's location
	*
	* @param mixed $pois
	* @param mixed $map
	* @param mixed $options
	*/
	function _mapp_map_pois($pois, $map, $options) {
		$options = Mappress_Options::get();

		if ($options->user) {
			$user_poi = new Mappress_Poi(array(
				'title' => $options->userTitle,
				'body' => $options->userBody,
				'iconid' => 'user',
				'user' => true,
				'showPoiList' => false
			));

			$pois[] = $user_poi;
		}

		return $pois;
	}

	/**
	* Filter HTML for directions
	*
	* Default filter to generate the directions panel HTML
	*
	* @param mixed $html
	* @param mixed $map
	* @param mixed $options
	*/
	function _mapp_directions_html($html, $map, $options) {
		$html = "
			<div id='{$options->mapName}_directions' class='mapp-directions'>
				<form action=''>
					<table>
						<col class='mapp-directions-table-col1'/>
						<col/>
						<tr>
							<td colspan='2'>
								<span id='{$options->mapName}_car_button' class='mapp-car-button mapp-travelmode selected' title='" . __('By car', 'mappress') . "' ></span>
								<span id='{$options->mapName}_walk_button' class='mapp-walk-button mapp-travelmode' title='" . __('Walking', 'mappress') . "' ></span>
								<span id='{$options->mapName}_bike_button' class='mapp-bike-button mapp-travelmode' title='" . __('Bicycling', 'mappress') . "' ></span>

							</td>
						</tr>
						<tr>
							<td>
								<span class='mapp-a' title='" . __('Start', 'mappress') . "'></span>
							</td>
							<td>
								<input style='width:90%' type='text' id='{$options->mapName}_saddr' value='' />
							</td>
						</tr>
						<tr>
							<td><span class='mapp-swap' id='{$options->mapName}_addrswap' title='" . __('Swap start and end', 'mappress') . "'></span></td>
							<td>
								<span id='{$options->mapName}_saddr_corrected' class='mapp-address-corrected'></span>
							</td>
						</tr>
						<tr>
							<td><span class='mapp-b' title='" . __('End', 'mappress') . "'></span></td>
							<td><input style='width:90%' type='text' id='{$options->mapName}_daddr' value='' /></td>
						</tr>
						<tr>
							<td></td>
							<td><span id='{$options->mapName}_daddr_corrected' class='mapp-address-corrected'></span></td>
						</tr>
					</table>
					<p>
						<input type='submit' class='mapp-button' value='" . __('Get Directions', 'mappress'). "' id='{$options->mapName}_get_directions' />
						<input type='button' class='mapp-button' value='" . __('Print Directions', 'mappress') . "' id='{$options->mapName}_print_directions' />
						<input type='button' class='mapp-button' value ='" . __('Close', 'mappress') . "' id='{$options->mapName}_close_directions' />
					</p>
				</form>
				<div id='{$options->mapName}_directions_renderer'></div>
			</div>
		";

		return $html;
	}

	/**
	* Append 'px' to a dimension (width/height)
	* Some browsers like Chrome are fussy about the 'px' suffix and won't render correctly with just a number
	*
	* If there is a 'px' or '%' suffix already present, the original value is returned unchanged
	*
	* @param mixed $size
	*/
	function _px($size) {
		return ( stripos($size, 'px') || strpos($size, '%')) ? $size : $size . 'px';
	}
} // End class Mappress_Map
?>
