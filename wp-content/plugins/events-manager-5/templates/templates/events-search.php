<?php 
/* 
 * By modifying this in your theme folder within plugins/events-manager/templates/events-search.php, you can change the way the search form will look.
 * To ensure compatability, it is recommended you maintain class, id and form name attributes, unless you now what you're doing. 
 * You also must keep the _wpnonce hidden field in this form too.
 */
?>
<div class="em-events-search">
	<?php 
	global $em_localized_js;
	$s_default = get_option('dbem_search_form_text_label');	
	$s = !empty($_REQUEST['em_search']) ? $_REQUEST['em_search']:$s_default;
	if( !isset($_REQUEST['country']) ){
		$country = get_option('dbem_location_default_country');
	}elseif( !empty($_REQUEST['country']) ){
		$country = $_REQUEST['country'];
	}
	//convert scope to an array in event of pagination
	if(!empty($_REQUEST['scope']) && !is_array($_REQUEST['scope'])){ $_REQUEST['scope'] = explode(',',$_REQUEST['scope']); }
	//get the events page to display search results
	?>
	<form action="<?php echo EM_URI; ?>" method="post" class="em-events-search-form">
		<?php do_action('em_template_events_search_form_header'); ?>
		
		<?php if( get_option('dbem_search_form_text') ): ?>
		<!-- START General Search -->
		<?php /* This general search will find matches within event_name, event_notes, and the location_name, address, town, state and country. */ ?>
		<input type="text" name="em_search" class="em-events-search-text" value="<?php echo $s; ?>" onfocus="if(this.value=='<?php echo $s_default; ?>')this.value=''" onblur="if(this.value=='')this.value='<?php echo $s_default; ?>'" />
		<!-- END General Search -->
		<?php endif; ?>
		
		<?php if( get_option('dbem_search_form_dates') ): ?>
		<!-- START Date Search -->
		<span class="em-events-search-dates em-date-range">
			<?php _e('between','dbem'); ?>:
			<input type="text" class="em-date-input-loc em-date-start" />
			<input type="hidden" class="em-date-input" name="scope[0]" value="<?php if( !empty($_REQUEST['scope'][0]) ) echo $_REQUEST['scope'][0]; ?>" />
			<?php _e('and','dbem'); ?>
			<input type="text" class="em-date-input-loc em-date-end" />
			<input type="hidden" class="em-date-input" name="scope[1]" value="<?php if( !empty($_REQUEST['scope'][1]) ) echo $_REQUEST['scope'][1]; ?>" />
		</span>
		<!-- END Date Search -->
		<?php endif; ?>
		
		<?php if( get_option('dbem_search_form_categories') ): ?>	
		<!-- START Category Search -->
			<?php 
				$selected = !empty($_REQUEST['category']) ? $_REQUEST['category'] : 0;
				wp_dropdown_categories(array( 'hide_empty' => 0, 'name' => 'category', 'hierarchical' => true, 'taxonomy' => EM_TAXONOMY_CATEGORY, 'selected' => $selected, 'show_option_none' => get_option('dbem_search_form_categories_label'), 'class'=>'em-events-search-category'));		
			?>
		<!-- END Category Search -->
		<?php endif; ?>
		
		<?php if( get_option('dbem_search_form_countries') ): ?>
		<!-- START Country Search -->
		<select name="country" class="em-events-search-country">
			<option value=''><?php echo get_option('dbem_search_form_countries_label'); ?></option>
			<?php 
			//get the counties from locations table
			global $wpdb;
			$countries = em_get_countries();
			$em_countries = $wpdb->get_results("SELECT DISTINCT location_country FROM ".EM_LOCATIONS_TABLE." WHERE location_country IS NOT NULL AND location_country != '' AND location_status=1 ORDER BY location_country ASC", ARRAY_N);
			foreach($em_countries as $em_country): 
			?>
			 <option value="<?php echo $em_country[0]; ?>" <?php echo (!empty($country) && $country == $em_country[0]) ? 'selected="selected"':''; ?>><?php echo $countries[$em_country[0]]; ?></option>
			<?php endforeach; ?>
		</select>
		<!-- END Country Search -->	
		<?php endif; ?>
		
		<?php if( get_option('dbem_search_form_regions') ): ?>
		<!-- START Region Search -->
		<select name="region" class="em-events-search-region">
			<option value=''><?php echo get_option('dbem_search_form_regions_label'); ?></option>
			<?php 
			if( !empty($country) ){
				//get the counties from locations table
				global $wpdb;
				$em_states = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_region FROM ".EM_LOCATIONS_TABLE." WHERE location_region IS NOT NULL AND location_region != '' AND location_country=%s AND location_status=1 ORDER BY location_region", $country), ARRAY_N);
				foreach($em_states as $state){
					?>
					 <option <?php echo (!empty($_REQUEST['region']) && $_REQUEST['region'] == $state[0]) ? 'selected="selected"':''; ?>><?php echo $state[0]; ?></option>
					<?php 
				}
			}
			?>
		</select>	
		<!-- END Region Search -->	
		<?php endif; ?>
		
		<?php if( get_option('dbem_search_form_states') ): ?>
		<!-- START State/County Search -->
		<select name="state" class="em-events-search-state">
			<option value=''><?php echo get_option('dbem_search_form_states_label'); ?></option>
			<?php 
			if( !empty($country) ){
				//get the counties from locations table
				global $wpdb;
				$cond = !empty($_REQUEST['region']) ? $wpdb->prepare(" AND location_region=%s ", $_REQUEST['region']):'';
				$em_states = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_state FROM ".EM_LOCATIONS_TABLE." WHERE location_state IS NOT NULL AND location_state != '' AND location_country=%s $cond AND location_status=1 ORDER BY location_state", $country), ARRAY_N);
				foreach($em_states as $state){
					?>
					 <option <?php echo (!empty($_REQUEST['state']) && $_REQUEST['state'] == $state[0]) ? 'selected="selected"':''; ?>><?php echo $state[0]; ?></option>
					<?php 
				}
			}
			?>
		</select>
		<!-- END State/County Search -->
		<?php endif; ?>
		
		<?php if( get_option('dbem_search_form_towns') ): ?>
		<!-- START City Search -->
		<select name="town" class="em-events-search-town">
			<option value=''><?php echo get_option('dbem_search_form_towns_label'); ?></option>
			<?php 
			if( !empty($country) ){
				//get the counties from locations table
				global $wpdb;
				$cond = !empty($_REQUEST['region']) ? $wpdb->prepare(" AND location_region=%s ", $_REQUEST['region']):'';
				$cond .= !empty($_REQUEST['state']) ? $wpdb->prepare(" AND location_state=%s ", $_REQUEST['state']):'';
				$em_towns = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_town FROM ".EM_LOCATIONS_TABLE." WHERE location_town IS NOT NULL AND location_town != '' AND location_country=%s $cond AND location_status=1 ORDER BY location_town", $country), ARRAY_N);
				foreach($em_towns as $town){
					?>
					 <option <?php echo (!empty($_REQUEST['town']) && $_REQUEST['town'] == $town[0]) ? 'selected="selected"':''; ?>><?php echo $town[0]; ?></option>
					<?php 
				}
			}
			?>
		</select>
		<!-- END City Search -->
		<?php endif; ?>
		
		<?php do_action('em_template_events_search_form_ddm'); //depreciated, don't hook, use the one below ?>
		<?php do_action('em_template_events_search_form_footer'); ?>
		<input type="hidden" name="action" value="search_events" />
		<input type="submit" value="<?php echo get_option('dbem_serach_form_submit','Search'); ?>" class="em-events-search-submit" />		
	</form>	
</div>