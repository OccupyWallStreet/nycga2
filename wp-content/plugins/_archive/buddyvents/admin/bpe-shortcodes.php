<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

class Buddyvents_Admin_Shortcodes
{
	/**
	 * Initialize the shortcodes
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function init()
	{
		add_action( 'media_buttons_context', array( __CLASS__, 'event_button' )	   );
		add_action( 'admin_init', 			 array( __CLASS__, 'mce_popup' 	  ), 0 );
	}

	/**
	 * Add the events mce button
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function event_button( $context )
	{
		$out  = '<a href="'. wp_nonce_url( admin_url( '?shortcodes=buddyvents' ), 'bpe_show_shortcodes_content' ) .'" class="thickbox" title="'.  __( 'Add Events &amp; Calendars', 'events' ) .'">';
		$out .= '<img src="'. EVENT_URLPATH .'admin/images/cal.png" alt="' . __( 'Add Events &amp; Calendars', 'events' ) . '" />';
		$out .= '</a>';
		
		return $context . $out;
	}
	
	
	/**
	 * Add the events mce popup thickbox
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function mce_popup()
	{
		if( ! is_admin() )
			return false;
	
		if( isset( $_GET['shortcodes'] ) && $_GET['shortcodes'] == 'buddyvents' )
		{
			check_admin_referer( 'bpe_show_shortcodes_content' );
			?>
			<script type="text/javascript">
			function InsertCalendar(){
				var month 	 = jQuery("#cal-month").val();
				var year 	 = jQuery("#cal-year").val();
				var calMonth = (month != '') ? ' month="'+ month +'"' : '';
				var calYear  = (year != '') ? ' year="'+ year +'"' : '';
		
				window.send_to_editor('[eventcal'+ calMonth + calYear +']');
			}
			function InsertEvents(){
				var data = {
					'ids': 			jQuery("#e-ids").val(),
					'user_id': 		jQuery("#e-userid").val(),
					'group_id': 	jQuery("#e-groupid").val(),
					'start_date': 	jQuery("#e-startdate").val(),
					'start_time': 	jQuery("#e-starttime").val(),
					'end_date':   	jQuery("#e-enddate").val(),
					'end_time':   	jQuery("#e-endtime").val(),
					'timezone':   	jQuery("#e-timezone").val(),
					'day': 		  	jQuery("#e-day").val(),
					'month': 	  	jQuery("#e-month").val(),
					'year': 	  	jQuery("#e-year").val(),
					'future': 	  	jQuery("#e-future").val(),
					'past': 	  	jQuery("#e-past").val(),
					'location':   	jQuery("#e-location").val(),
					'venue': 	  	jQuery("#e-venue").val(),
					'radius': 	  	jQuery("#e-radius").val(),
					'category':  	jQuery("#e-category").val(),
					'search_terms': jQuery("#e-search").val(),
					'per_page': 	jQuery("#e-perpage").val(),
					'begin': 		jQuery("#e-begin").val(),
					'end': 			jQuery("#e-end").val(),
					'max': 			jQuery("#e-max").val(),
					'sort': 		jQuery("#e-sortby").val(),
					'meta': 		jQuery("#e-meta").val(),
					'meta_key': 	jQuery("#e-metakey").val(),
					'operator': 	jQuery("#e-operator").val()
				}
				
				var string = '';
				jQuery.each( data, function(key,val){
					string += (val != '') ? ' '+ key +'="'+ val +'"' : '';
				});
		
				window.send_to_editor('[events'+ string +']');
			}
			jQuery(document).ready(function() {
				jQuery('.events-shortcodes').hide();
				jQuery('#which-whortcode').change( function() {
					var id = jQuery(this).val();
					jQuery('.events-shortcodes').hide();
					jQuery(id).show();
				});
			});
			</script>
			<style type="text/css">#sce p{width:48%;padding-right:2%;float:left;}</style>
	        <form class="wrap">
	            <p>
	                <label for="which-whortcode"><?php _e( 'Pick the type of shortcode you want to add:', 'events' ) ?></label>
	                <select id="which-whortcode">
	                    <option value=""></option>
	                    <option value="#sce"><?php _e( 'Events', 'events' ) ?></option>
	                    <option value="#scc"><?php _e( 'Calendar', 'events' ) ?></option>
	                </select>
	            </p>
	            
	            <div id="sce" class="events-shortcodes">
	                <div>
	                    <h3><?php _e( 'Insert events', 'events' ); ?></h3>
	                    <span><?php _e( 'All attributes are optional', 'events' ); ?></span>
	                </div>
	                <p>
	                    <label for="e-ids"><?php _e( 'IDs', 'events' ) ?></label><br />
	                    <input type="text" id="e-ids" value="" /><br />
	                    <span class="description"><?php _e( 'Enter a comma seperated list of event IDs.', 'events' ) ?></span>               
	                </p>
	                <p>
	                    <label for="e-userid"><?php _e( 'User ID', 'events' ) ?></label><br />
	                    <input type="text" id="e-userid" value="" /><br />
	                    <span class="description"><?php _e( 'Enter a single user ID.', 'events' ) ?></span>               
	                </p>
	                <p style="clear:both">
	                    <label for="e-groupid"><?php _e( 'Group ID', 'events' ) ?></label><br />
	                    <input type="text" id="e-groupid" value="" /><br />
	                    <span class="description"><?php _e( 'Enter a single group ID.', 'events' ) ?></span>               
	                </p>
	                <p>
	                    <label for="e-startdate"><?php _e( 'Start Date', 'events' ) ?></label><br />
	                    <input type="text" id="e-startdate" value="" /><br />
	                    <span class="description"><?php _e( 'Format: YYYY-MM-DD', 'events' ) ?></span>               
	                </p>
	                <p style="clear:both">
	                    <label for="e-starttime"><?php _e( 'Start Time', 'events' ) ?></label><br />
	                    <input type="text" id="e-starttime" value="" /><br />
	                    <span class="description"><?php _e( 'Format: HH:MM', 'events' ) ?></span>               
	                </p>
	                <p>
	                    <label for="e-enddate"><?php _e( 'End Date', 'events' ) ?></label><br />
	                    <input type="text" id="e-enddate" value="" /><br />
	                    <span class="description"><?php _e( 'Format: YYYY-MM-DD', 'events' ) ?></span>               
	                </p>
	                <p style="clear:both">
	                    <label for="e-endtime"><?php _e( 'End Time', 'events' ) ?></label><br />
	                    <input type="text" id="e-endtime" value="" /><br />
	                    <span class="description"><?php _e( 'Format: HH:MM', 'events' ) ?></span>               
	                </p>
	                <p>
	                    <label for="e-timezone"><?php _e( 'Timezone', 'events' ) ?></label><br />
	                    <input type="text" id="e-timezone" value="" /><br />
	                    <span class="description"><?php _e( 'Chose a timezone.', 'events' ) ?></span>               
	                </p>
	                <p style="clear:both">
	                    <label for="e-day"><?php _e( 'Day', 'events' ) ?></label><br />
	                    <input type="text" id="e-day" value="" /><br />
	                    <span class="description"><?php _e( 'Format: YYYY-MM-DD', 'events' ) ?></span>               
	                </p>
	                <p>
	                    <label for="e-month"><?php _e( 'Month', 'events' ) ?></label><br />
	                    <input type="text" id="e-month" value="" /><br />
	                    <span class="description"><?php _e( 'Format: MM-YYYY', 'events' ) ?></span>               
	                </p>
	                <p style="clear:both">
	                    <label for="e-year"><?php _e( 'Year', 'events' ) ?></label><br />
	                    <input type="text" id="e-year" value="" /><br />
	                    <span class="description"><?php _e( 'Format: YYYY', 'events' ) ?></span>               
	                </p>
	                <p>
	                    <label for="e-future"><?php _e( 'Future events only', 'events' ) ?></label><br />
	                    <input type="text" id="e-future" value="" />
	                </p>
	                <p style="clear:both">
	                    <label for="e-past"><?php _e( 'Past events only', 'events' ) ?></label><br />
	                    <input type="text" id="e-past" value="" />
	                </p>
	                <p>
	                    <label for="e-location"><?php _e( 'Location', 'events' ) ?></label><br />
	                    <input type="text" id="e-location" value="" />
	                </p>
	                <p style="clear:both">
	                    <label for="e-venue"><?php _e( 'Venue', 'events' ) ?></label><br />
	                    <input type="text" id="e-venue" value="" />
	                </p>
	                <p>
	                    <label for="e-radius"><?php _e( 'Radius', 'events' ) ?></label><br />
	                    <input type="text" id="e-radius" value="" />
	                </p>
	                <p style="clear:both">
	                    <label for="e-category"><?php _e( 'Category', 'events' ) ?></label><br />
	                    <input type="text" id="e-category" value="" />
	                </p>
	                <p>
	                    <label for="e-search"><?php _e( 'Search terms', 'events' ) ?></label><br />
	                    <input type="text" id="e-search" value="" /><br />
	                    <span class="description"><?php _e( 'Comma seperate search terms.', 'events' ) ?></span>               
	                </p>
	                <p style="clear:both">
	                    <label for="e-perpage"><?php _e( 'Events per page', 'events' ) ?></label><br />
	                    <input type="text" id="e-perpage" value="" />
	                </p>
	                <p>
	                    <label for="ee-begin"><?php _e( 'Begin', 'events' ) ?></label><br />
	                    <input type="text" id="e-begin" value="" /><br />
	                    <span class="description"><?php _e( 'Format: YYYY-MM-DD', 'events' ) ?></span>               
	                </p>
	                <p style="clear:both">
	                    <label for="e-end"><?php _e( 'End', 'events' ) ?></label><br />
	                    <input type="text" id="e-end" value="" /><br />
	                    <span class="description"><?php _e( 'Format: YYYY-MM-DD', 'events' ) ?></span>               
	                </p>
	                <p>
	                    <label for="e-max"><?php _e( 'Max', 'events' ) ?></label><br />
	                    <input type="text" id="e-max" value="" />
	                </p>
	                <p style="clear:both">
	                    <label for="e-sortby"><?php _e( 'Sort by', 'events' ) ?></label><br />
	                    <input type="text" id="e-sortby" value="" />
	                </p>
	                <p>
	                    <label for="e-meta"><?php _e( 'Meta', 'events' ) ?></label><br />
	                    <input type="text" id="e-meta" value="" />
	                </p>
	                <p style="clear:both">
	                    <label for="e-metakey"><?php _e( 'Meta Key', 'events' ) ?></label><br />
	                    <input type="text" id="e-metakey" value="" />
	                </p>
	                <p>
	                    <label for="e-operator"><?php _e( 'Operator', 'events' ) ?></label><br />
	                    <input type="text" id="e-operator" value="" />
	                </p>
	                <p style="clear:both">
	                    <input type="button" class="button-primary" value="<?php _e( 'Insert Events', 'events' ); ?>" onclick="InsertEvents();"/>
	                    <a class="button" href="#" onclick="tb_remove(); return false;"><?php _e( 'Cancel', 'events' ); ?></a>
	                </p>
	            </div>
	
	            <div id="scc" class="events-shortcodes">
	                <div>
	                    <h3><?php _e( 'Insert an event calendar', 'events' ); ?></h3>
	                    <span><?php _e( 'Current month will be used if month and year are empty.', 'events' ); ?></span>
	                </div>
	                <p>
	                    <label for="cal-month"><?php _e( 'Pick a month', 'events' ) ?></label><br />
	                    <select id="cal-month">
	                        <option value=""></option>
	                        <?php for( $i = 1; $i <= 12; $i++ ) : ?>
	                            <option value="<?php echo esc_attr( $i ) ?>"><?php echo date( 'M', mktime( 0, 0, 0, $i, 1, date( 'Y' ) ) ) ?></option>
	                        <?php endfor; ?>
	                    </select>
	                </p>
	                <p>    
	                    <label for="cal-year"><?php _e( 'Enter a year (YYYY)', 'events' ) ?></label><br />
	                    <input type="text" id="cal-year" value="" />
	                </p>
	                <p>
	                    <input type="button" class="button-primary" value="<?php _e( 'Insert Calendar', 'events' ); ?>" onclick="InsertCalendar();"/>
	                    <a class="button" href="#" onclick="tb_remove(); return false;"><?php _e( 'Cancel', 'events' ); ?></a>
	                </p>
	            </div>
	        </form>
			<?php
			exit;
		}
	}
}
Buddyvents_Admin_Shortcodes::init();
?>