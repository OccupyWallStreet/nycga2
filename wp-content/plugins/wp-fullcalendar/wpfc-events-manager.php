<?php
/*
* EM Integration Stuff
* We'll start moving stuff away here for now to decouple it completely from the plugin
*/
/**
 * Initiallizes EM stuff by overriding some shortcodes, filters and actions 
 */
function wpfc_em_init(){
    //overrides the ajax calls for event data
    if( !empty($_REQUEST['type']) && $_REQUEST['type'] == 'event' ){ //only needed during ajax requests anyway
		remove_action('wp_ajax_WP_FullCalendar', array('WP_FullCalendar','ajax'));
		remove_action('wp_ajax_nopriv_WP_FullCalendar', array('WP_FullCalendar','ajax'));
		add_action('wp_ajax_WP_FullCalendar', 'wpfc_em_ajax');
		add_action('wp_ajax_nopriv_WP_FullCalendar', 'wpfc_em_ajax');
    }
	add_filter('wpfc_fullcalendar_args', 'wpfc_em_fullcalendar_args');
    //overrides some EM stuff with FullCalendar depending on some extra settings
	if(  defined('EM_VERSION') ){
		if ( get_option('dbem_emfc_override_shortcode') ){
			remove_shortcode('events_calendar');
			add_shortcode('events_calendar', array('WP_FullCalendar','calendar'));
		}
		if( get_option('dbem_emfc_override_calendar') ){
			add_filter ('em_content_pre', 'wpfc_em_content',10,2);
		}
	}
}
add_action('init','wpfc_em_init');

function wpfc_em_fullcalendar_args($args){
    if( !empty($args['type']) && $args['type'] == 'event'){
	    if( !empty($args['category']) ){
		    $args[EM_TAXONOMY_CATEGORY] = $args['category'];
	    }
	    if( !empty($args['tag']) ){
		    $args[EM_TAXONOMY_TAG] = $args['tag'];
	    }
    }
    return $args;
}

/**
 * Adds a note to the event post type in the admin area, so it's obvious EM is interfering.
 */
function wpfc_admin_options_post_type_event(){
	echo " - <i>powered by Events Manager</i>";
}
add_action('wpfc_admin_options_post_type_event','wpfc_admin_options_post_type_event');

function wpfc_em_admin_notice(){
    if( !empty($_REQUEST['page']) && $_REQUEST['page'] == 'wp-fullcalendar'){
    ?>
    <div class="updated"><p><?php echo sprintf(__('If you choose the Event post type whilst Events Manager is activated, you can also visit the <a href="%s">Events Manager settings page</a> for a few more options when displaying event information on your calendar.','dbem'), admin_url('edit.php?post_type='.EM_POST_TYPE_EVENT.'&page=events-manager-options')); ?></p></div>
    <?php
    }
}
add_action('admin_notices', 'wpfc_em_admin_notice');

/**
 * Replaces the event page with the FullCalendar if requested in settings
 * @param unknown_type $page_content
 * @return Ambigous <mixed, string>
 */
function wpfc_em_content($content = '', $page_content=''){
	global $wpdb, $post;
	if ( em_is_events_page() ){
		$calendar_content = WP_FullCalendar::calendar();
		//Now, we either replace CONTENTS or just replace the whole page
		if( preg_match('/CONTENTS/', $page_content) ){
			$content = str_replace('CONTENTS',$calendar_content,$page_content);
		}else{
			$content = $calendar_content;
		}
	}
	return $content;
}

/**
 * Adds extra non-taxonomy fields to the calendar search
 * @param array $args
 */
function wpfc_em_calendar_search($args){
	if( defined('EM_VERSION') && $args['type'] == 'event' ){
		$country = '';
		if( !empty($_REQUEST['country']) ){
			$country = !empty($_REQUEST['country']) ? $_REQUEST['country']:'';
		}
		?>
		<?php if( empty($country) ): ?>
		<!-- START Country Search -->
		<select name="country" class="em-events-search-country wpfc-taxonomy">
			<option value=''><?php echo get_option('dbem_search_form_countries_label'); ?></option>
			<?php
			//get the counties from locations table
			global $wpdb;
			$countries = em_get_countries();
			$em_countries = $wpdb->get_results("SELECT DISTINCT location_country FROM ".EM_LOCATIONS_TABLE." WHERE location_country IS NOT NULL AND location_country != '' ORDER BY location_country ASC", ARRAY_N);
			foreach($em_countries as $em_country):
			?>
			 <option value="<?php echo $em_country[0]; ?>" <?php echo (!empty($country) && $country == $em_country[0]) ? 'selected="selected"':''; ?>><?php echo $countries[$em_country[0]]; ?></option>
			<?php endforeach; ?>
		</select>
		<!-- END Country Search -->
		<?php endif; ?>

		<?php if( !empty($country) ): ?>
		<!-- START Region Search -->
		<select name="region" class="em-events-search-region wpfc-taxonomy">
			<option value=''><?php echo get_option('dbem_search_form_regions_label'); ?></option>
			<?php
			if( !empty($country) ){
				//get the counties from locations table
				global $wpdb;
				$em_states = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_region FROM ".EM_LOCATIONS_TABLE." WHERE location_region IS NOT NULL AND location_region != '' AND location_country=%s ORDER BY location_region", $country), ARRAY_N);
				foreach($em_states as $state){
					?>
					 <option <?php echo (!empty($_REQUEST['region']) && $_REQUEST['region'] == $state[0]) ? 'selected="selected"':''; ?>><?php echo $state[0]; ?></option>
					<?php
				}
			}
			?>
		</select>
		<!-- END Region Search -->

		<!-- START State/County Search -->
		<select name="state" class="em-events-search-state wpfc-taxonomy">
			<option value=''><?php echo get_option('dbem_search_form_states_label'); ?></option>
			<?php
			if( !empty($country) ){
				//get the counties from locations table
				global $wpdb;
				$cond = !empty($_REQUEST['region']) ? $wpdb->prepare(" AND location_region=%s ", $_REQUEST['region']):'';
				$em_states = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_state FROM ".EM_LOCATIONS_TABLE." WHERE location_state IS NOT NULL AND location_state != '' AND location_country=%s $cond ORDER BY location_state", $country), ARRAY_N);
				foreach($em_states as $state){
					?>
					 <option <?php echo (!empty($_REQUEST['state']) && $_REQUEST['state'] == $state[0]) ? 'selected="selected"':''; ?>><?php echo $state[0]; ?></option>
					<?php
				}
			}
			?>
		</select>
		<!-- END State/County Search -->

		<!-- START City Search -->
		<select name="town" class="em-events-search-town wpfc-taxonomy">
			<option value=''><?php echo get_option('dbem_search_form_towns_label'); ?></option>
			<?php
			if( !empty($country) ){
				//get the counties from locations table
				global $wpdb;
				$cond = !empty($_REQUEST['region']) ? $wpdb->prepare(" AND location_region=%s ", $_REQUEST['region']):'';
				$cond .= !empty($_REQUEST['state']) ? $wpdb->prepare(" AND location_state=%s ", $_REQUEST['state']):'';
				$em_towns = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_town FROM ".EM_LOCATIONS_TABLE." WHERE location_town IS NOT NULL AND location_town != '' AND location_country=%s $cond ORDER BY location_town", $country), ARRAY_N);
				foreach($em_towns as $town){
					?>
					 <option <?php echo (!empty($_REQUEST['town']) && $_REQUEST['town'] == $town[0]) ? 'selected="selected"':''; ?>><?php echo $town[0]; ?></option>
					<?php
				}
			}
			?>
		</select>
		<!-- END City Search -->
		<?php endif;
	}
}
add_action('wpfc_calendar_search','wpfc_em_calendar_search', 10, 1);

/**
 * Replaces the normal WPFC ajax and uses the EM query system to provide event specific results. 
 */
function wpfc_em_ajax() {
	$limit = get_option('wpfc_limit',3);
    $_REQUEST['month'] = false; //no need for these two
    $_REQUEST['year'] = false;
	$year = date ( "Y", $_REQUEST['start'] );
	$temp = date("Y-m-d", $_REQUEST ['start']);
	$tomorrow = mktime ( 0, 0, 0, date ( "m", strtotime ( $temp ) ) + 1, date ( "d", strtotime ( $temp ) ), date ( "Y", strtotime ( $temp ) ) );

	$month = date ( "m", $tomorrow );

	$args = array ('scope'=>array(date("Y-m-d", $_REQUEST['start']), date("Y-m-d", $_REQUEST['end'])), 'owner'=>false, 'status'=>1, 'orderby'=>'event_start_date, event_start_time');
	//do some corrections for EM query
	if( isset($_REQUEST[EM_TAXONOMY_CATEGORY]) || empty($_REQUEST['category']) ) $_REQUEST['category'] = !empty($_REQUEST[EM_TAXONOMY_CATEGORY]) ? $_REQUEST[EM_TAXONOMY_CATEGORY]:false;
	$_REQUEST['tag'] = !empty($_REQUEST[EM_TAXONOMY_TAG]) ? $_REQUEST[EM_TAXONOMY_TAG]:false;
	$args = apply_filters('wpfc_fullcalendar_args', array_merge($_REQUEST, $args));
	$EM_Events = EM_Events::get( $args );

	$parentArray = array ();
	$events = array ();
	$event_date_counts = array();
	$event_dates_more = array();

	//get day link template
	global $wp_rewrite;
	if( get_option("dbem_events_page") > 0 ){
		$event_page_link = get_permalink(get_option("dbem_events_page")); //PAGE URI OF EM
		if( $wp_rewrite->using_permalinks() ){ $event_page_link = trailingslashit($event_page_link); } 
	}else{
		if( $wp_rewrite->using_permalinks() ){
			$event_page_link = trailingslashit(home_url()).EM_POST_TYPE_EVENT_SLUG.'/'; //don't use EM_URI here, since ajax calls this before EM_URI is defined.
		}else{
			$event_page_link = home_url().'?post_type='.EM_POST_TYPE_EVENT; //don't use EM_URI here, since ajax calls this before EM_URI is defined.
		}
	}
	if( $wp_rewrite->using_permalinks() && !defined('EM_DISABLE_PERMALINKS') ){
		$event_page_link .= "%s/";
	}else{
		$joiner = (stristr($event_page_link, "?")) ? "&" : "?";
		$event_page_link .= $joiner."calendar_day=%s";
	}

	foreach ( $EM_Events as $EM_Event ) {
		/* @var $EM_Event EM_Event */
		$color = "#a8d144";
		$textColor = '#fff';
		$borderColor = '#a8d144';
		if ( !empty ( $EM_Event->get_categories()->categories )) {
			foreach($EM_Event->get_categories()->categories as $EM_Category){
				/* @var $EM_Category EM_Category */
				if( $EM_Category->get_color() != '' ){
					$color = $borderColor = $EM_Category->get_color();
					if( preg_match("/#fff(fff)?/i",$color) ){
						$textColor = '#777';
						$borderColor = '#ccc';
					}
					break;
				}
			}
		}
		$add_event = true;
		$date_iterator = $EM_Event->start;
		while( $date_iterator <= $EM_Event->end ){
			$date_iterator_date = date('Y-m-d', $date_iterator);
			if( empty( $event_date_counts[$date_iterator_date] ) ){
				$event_date_counts[$date_iterator_date] = 1;
			}else{
				$event_date_counts[$date_iterator_date]++;
			}
			if( $event_date_counts[$date_iterator_date] > $limit ){ //limit reached
				$add_event = false;
			}
			$date_iterator = $date_iterator + 86400;
		}
		$event_date = date('Y-m-d', $EM_Event->start);
		if($add_event && $event_date_counts[$event_date] <= $limit ){
			$title = $EM_Event->output(get_option('dbem_emfc_full_calendar_event_format', '#_EVENTNAME'), 'raw');
			$events[] = array ("title" => $title, "color" => $color, 'textColor'=>$textColor, 'borderColor'=>$borderColor, "start" => date('Y-m-d\TH:i:s', $EM_Event->start), "end" => date('Y-m-d\TH:i:s', $EM_Event->end), "url" => $EM_Event->get_permalink(), 'post_id' => $EM_Event->post_id, 'event_id' => $EM_Event->event_id, 'allDay' => $EM_Event->event_all_day == true );
		}elseif( empty($event_dates_more[$event_date]) ){
			$event_dates_more[$event_date] = 1;
			$day_ending = $event_date."T23:59:59";
			$events[] = apply_filters('wpfc_events_more', array ("title" => get_option('wpfc_limit_txt','more ...'), "color" => get_option('wpfc_limit_color','#fbbe30'), "start" => $day_ending, "url" => str_replace('%s',$event_date,$event_page_link), 'post_id' => 0, 'event_id' => 0 ,'allDay' => true), $event_date);
		}
	}
	echo EM_Object::json_encode( apply_filters('wpfc_events', $events) );
	die();
}

/**
 * Overrides the original qtip_content function and provides Event Manager formatted event information
 * @param string $content
 * @return string
 */
function wpfc_em_qtip_content( $content='' ){
	if( !empty($_REQUEST['event_id'] ) && trim(get_option('dbem_emfc_qtips_format')) != '' ){
		global $EM_Event;
		$EM_Event = em_get_event($_REQUEST['event_id']);
		if( !empty($EM_Event->event_id) ){
			$content = $EM_Event->output(get_option('dbem_emfc_qtips_format', '#_EXCERPT'));
		}
	}
	return $content;
}
add_filter('wpfc_qtip_content', 'wpfc_em_qtip_content');

/**
 * Changes the walker object so we can inject color values into the options
 * @param array $args
 * @param object $taxonomy
 * @return EM_Categories_Walker
 */
function wpmfc_em_taxonomy_args($args, $taxonomy){
	if( $taxonomy->name == EM_TAXONOMY_CATEGORY ){
		$args['walker'] = new EM_Categories_Walker;
	}
	return $args;
}
add_filter('wpmfc_calendar_taxonomy_args', 'wpmfc_em_taxonomy_args',10,2);

/**
 * Copy and alteration of the Walker_CategoryDropdown object
 * @author marcus
 *
 */
class EM_Categories_Walker extends Walker {
	var $tree_type = EM_TAXONOMY_CATEGORY;
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int $depth Depth of category. Used for padding.
	 * @param array $args Uses 'selected', 'show_count', and 'show_last_update' keys, if they exist.
	 */
	function start_el(&$output, $category, $depth, $args) {
		global $wpdb;
		$pad = str_repeat('&nbsp;', $depth * 3);

		$cat_name = apply_filters('list_cats', $category->name, $category);
		$color = $wpdb->get_var('SELECT meta_value FROM '.EM_META_TABLE." WHERE object_id='{$category->term_id}' AND meta_key='category-bgcolor' LIMIT 1");
		$color = ($color != '') ? $color:'#a8d144';
		$output .= "<option class=\"level-$depth\" value=\"".$category->term_id."\"";
		if ( $category->term_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$color.' - '.$cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '&nbsp;&nbsp;' . gmdate($format, $category->last_update_timestamp);
		}
		$output .= "</option>";
	}
}

function wpfc_em_admin_options(){
	?>
	<div  class="postbox " >
		<div class="handlediv" title="<?php __('Click to toggle', 'dbem'); ?>"><br /></div><h3 class='hndle'><span><?php _e ( 'Full Calendar Options', 'dbem' ); ?> </span></h3>
		<div class="inside">
			<p><?php echo sprintf(__('Looking for the rest of the FullCalendar Options? They\'ve moved <a href="%s">here</a>, the options below are for overriding specific bits relevant to Events Manager.','dbem'), admin_url('options-general.php?page=wp-fullcalendar')); ?></p>
			<table class='form-table'>
				<?php
				global $events_placeholder_tip, $save_button;
				em_options_radio_binary ( __( 'Override calendar on events page?', 'dbem' ), 'dbem_emfc_override_calendar', __( 'If set to yes, the FullCalendar will be used instead of the standard calendar on the events page.', 'dbem' ) );
				em_options_radio_binary ( __( 'Override calendar shortcode?', 'dbem' ), 'dbem_emfc_override_shortcode', __( 'Overrides the default calendar shortcode. You can also use [events_fullcalendar] instead.','dbem' ) );
				em_options_input_text ( __( 'Event title format', 'wpfc' ), 'dbem_emfc_full_calendar_event_format', __('HTML is not accepted.','wpfc').' '.$events_placeholder_tip, '#_EVENTNAME' );
				em_options_textarea( __( 'Event tooltips format', 'wpfc' ), 'dbem_emfc_qtips_format', __('If you enable tips, this information will be shwon, which can include HTML.','wpfc').' '.$events_placeholder_tip, '#_EVENTNAME' );$positions_options = array();
				?>
			</table>
			<?php echo $save_button; ?>
		</div> <!-- . inside -->
		</div> <!-- .postbox -->
	<?php
}
add_action('em_options_page_footer', 'wpfc_em_admin_options');

//check for updates
if( version_compare(WPFC_VERSION, get_option('wpfc_version',0)) > 0 && current_user_can('activate_plugins') ){
	add_option('dbem_emfc_full_calendar_event_format','#_EVENTTIMES - #_EVENTNAME');
	add_option('dbem_emfc_qtips_format', '{has_image}<div style="float:left; margin:0px 5px 5px 0px;">#_EVENTIMAGE{75,75}</div>{/has_image}#_EVENTEXCERPT');
}