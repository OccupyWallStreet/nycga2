<?php
/*
Plugin Name: Scheduled Content
Description: Allows you to make certain post or page content available only at scheduled periods via a simple shortcode.
Plugin URI: http://premium.wpmudev.org/project/scheduled-content
Version: 1.0
Author: Aaron Edwards (Incsub)
Author URI: http://premium.wpmudev.org/
WDP ID: 215
*/

/* 
Copyright 2007-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


class ScheduledContent {

  function ScheduledContent() {

    //shortcodes
    add_shortcode( 'scheduled', array(&$this, 'shortcode') );
    
    //localize the plugin
	  add_action( 'plugins_loaded', array(&$this, 'localization') );

    
    // TinyMCE options
		add_action( 'wp_ajax_scheduledTinymceOptions', array(&$this, 'tinymce_options') );
    add_action( 'admin_init', array(&$this, 'load_tinymce') );

  }

  function localization() {
    // Load up the localization file if we're using WordPress in a different language
  	// Place it in this plugin's "languages" folder and name it "sc-[value in wp-config].mo"
    load_plugin_textdomain( 'sc', false, '/scheduled-content/languages/' );
  }

  function shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
      'monthly' => false, //day of month
      'weekly' => false,  //week days comma separated, 0-6 = Sunday-Saturday
      'onetime' => false, //date string, ie "08/15/2010"
      'time' => false, //time string, ie "8:00 PM"
      'length' => false, //how long to keep open in years:days:hours:minutes, ie "0:0:12:35"
      'msg' => false
  	), $atts ) );

		//skip check for no content
    if ( is_null( $content ) )
      return;

		//if required fields not set don't protect
		if ( (!$monthly && !$weekly && !$onetime) || !$time || !$length )
    	return do_shortcode( $content );
		
		//calculate how long to keep open
    @list($yrs, $dys, $hrs, $mns) = explode(':', $length);
    $length = strtotime("+$yrs Years $dys Days $hrs Hours $mns Minutes") - time();

		//do our checks for if its open
    $open = false;
		if ($monthly) {
			$start = strtotime(date('F').' '.$monthly.' '.$time);
			$end = $start + $length;
			if ($start <= time() && $end >= time()) {
				$open = true;
			} else if ($end < time()) {
			  $start = strtotime(date('F', strtotime('+1 month')).' '.$monthly.' '.$time);
				$end = $start + $length;
		    if ($start <= time() && $end >= time()) {
					$open = true;
				} else {
					$open = false;
				}
			}
		} else if ($weekly) {
			$weekdays = explode(',', $weekly);
			sort($weekdays);

			$days = array(0=>'sun', 1=>'mon', 2=>'tue', 3=>'wed', 4=>'thu', 5=>'fri', 6=>'sat');
			foreach ($weekdays as $day) {
				$periods[strtotime("last $days[$day] $time")] = strtotime("last $days[$day] $time") + $length;
				$periods[strtotime("this $days[$day] $time")] = strtotime("this $days[$day] $time") + $length;
    		$periods[strtotime("next $days[$day] $time")] = strtotime("next $days[$day] $time") + $length;
			}

			ksort($periods);
			//see if we are in one of the periods
			foreach ($periods as $start => $end) {
    		if ($start <= time() && $end >= time()) {
					$open = true;
					break;
				}
			}
			
			//not in a period, figure out the next one
			if (!$open) {
        foreach ($periods as $start => $end) {
	    		if ($start > time())
						break;
				}
			}

		} else if ($onetime) {
      $start = strtotime("$onetime $time");
			$end = $start + $length;
			if ($start <= time() && $end >= time()) {
				$open = true;
			}
		}

		//set default closed messages
		if (!$msg)
			$msg = ($onetime && $end <= time()) ? __("This content is not currently available.", 'sc') : __("This content is not currently available. It will be available in:", 'sc');
			
		//check cookie for password
		if ( $open ) {
		
		  //refresh page at end of period
			if ($end >= time())
			  $content .= '<script language="javascript">setTimeout("location.reload(true)", '.(($end - time()) * 1000).');</script>';
			  
   		return do_shortcode( $content );
		} else {
		  $return = '<p class="scheduled-closed">' . $msg . '</p>';
		
		  $var = 'cd_' . rand();
			$countdown = '<script language="javascript" src="'.plugins_url('scheduled-content/includes/countdown.js').'"></script>
<div class="scheduled-timer" id="clock_'.$var.'"></div>
<script language="javascript">
	var '.$var.' = new countdown("'.$var.'");
	'.$var.'.Div			= "clock_'.$var.'";
	'.$var.'.TargetDate		= "'.date("m/d/Y g:i A", $start).' GMT";
	'.$var.'.DisplayFormat	= "'.__("%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds", 'sc').'";
	'.$var.'.Setup();
</script>
			';
			
			//show countdown if start is in the future
			if ($start > time())
			  $return .= $countdown;
			  
			return $return;
		}
  }
	
	function load_tinymce() {
    if ( (current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing') == 'true') {
   		add_filter( 'mce_external_plugins', array(&$this, 'tinymce_add_plugin') );
			add_filter( 'mce_buttons', array(&$this,'tinymce_register_button') );
		}
	}
	
		/**
	 * TinyMCE dialog content
	 */
	function tinymce_options() {
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce_popup.js?ver=327-1235"></script>
				<script type="text/javascript" src="../wp-includes/js/tinymce/utils/form_utils.js?ver=327-1235"></script>
				<script type="text/javascript" src="../wp-includes/js/tinymce/utils/editable_selects.js?ver=327-1235"></script>

				<script type="text/javascript" src="../wp-includes/js/jquery/jquery.js"></script>

				<script type="text/javascript">

          tinyMCEPopup.storeSelection();
          
          jQuery(document).ready(function($) {
            $('#sc-type').change(function() {
							if ($(this).val() == 'monthly') {
                $('#sc-weekly-row').hide();
                $('#sc-onetime-row').hide();
                $('#sc-monthly-row').show();
							} else if ($(this).val() == 'weekly') {
                $('#sc-monthly-row').hide();
                $('#sc-onetime-row').hide();
                $('#sc-weekly-row').show();
							} else if ($(this).val() == 'onetime') {
                $('#sc-monthly-row').hide();
                $('#sc-weekly-row').hide();
                $('#sc-onetime-row').show();
							}
						});
					});

          
					var insertSchedule = function (ed) {

						tinyMCEPopup.restoreSelection();
						
						output = '[scheduled';
						
						//insert schedule type
						if (jQuery('#sc-type').val() == 'monthly') {
							output += ' monthly="'+ jQuery('#sc-monthdate').val() +'"';
						} else if (jQuery('#sc-type').val() == 'weekly') {
							var weekdays = '';
							jQuery('.sc-weekdays:checked').each(function(index) {
                weekdays = weekdays + ',' + jQuery(this).val();
							});
							if (!weekdays) {
								alert("<?php _e("You must select at least one weekday.", 'sc'); ?>");
								return false;
							}
              output += ' weekly="'+ weekdays.substring(1) +'"';
						} else if (jQuery('#sc-type').val() == 'onetime') {
              output += ' onetime="'+ jQuery('#sc-year').val() +'/'+ jQuery('#sc-month').val() +'/'+ jQuery('#sc-day').val() +'"';
						}
						
						//insert start time
						output += ' time="'+ jQuery('#sc-hours').val() +':'+ jQuery('#sc-minutes').val() +' '+ jQuery('#sc-ampm').val() + '"';
						
						//insert open length
						output += ' length="'+ jQuery('#sc-lyears').val() +':'+ jQuery('#sc-ldays').val() +':'+ jQuery('#sc-lhours').val() + ':' + jQuery('#sc-lmins').val() +'"';

						//insert msg
						if (jQuery('#sc-msg').val())
							output += ' msg="'+ jQuery('#sc-msg').val() +'"';
						
						output += ']'+tinyMCEPopup.editor.selection.getContent()+'[/scheduled]';

						tinyMCEPopup.execCommand('mceInsertContent', 0, output);
						tinyMCEPopup.editor.execCommand('mceRepaint');
            tinyMCEPopup.editor.focus();
						// Return
						tinyMCEPopup.close();
					};
				</script>
				<style type="text/css">
				td.info {
					vertical-align: top;
					color: #777;
					width: 150px;
				}
				</style>

				<title><?php _e("Scheduled Content", 'sc'); ?></title>
			</head>
			<body style="display: none">
				<form onsubmit="insertSchedule();return false;" action="#">

					<div id="general_panel" class="panel current">
							<fieldset>
						  <table border="0" cellpadding="4" cellspacing="0">
                <tr>
									<td><label for="sc-type"><?php _e("Schedule", 'sc'); ?></label></td>
									<td>
										<select id="sc-type" name="sc-type">
										  <option value="monthly"><?php _e("Monthly", 'sc'); ?></option>
										  <option value="weekly"><?php _e("Weekly", 'sc'); ?></option>
										  <option value="onetime"><?php _e("Onetime", 'sc'); ?></option>
										</select>
									</td>
								</tr>
								<tr id="sc-monthly-row">
									<td><label for="sc-monthdate"><?php _e("Date", 'sc'); ?></label></td>
									<td>
										<select id="sc-monthdate" name="sc-monthdate">
										  <?php
											for ($i = 1; $i <= 31; $i++) {
												echo '<option value="'.$i.'">'.$i.'</option>';
											}
											?>
										</select>
									</td>
									<td class="info"><?php _e("Choose the day of each month to display the content.", 'sc'); ?></td>
								</tr>
								<tr id="sc-weekly-row" style="display:none;">
									<td><label for="sc-weekdays-0"><?php _e("Weekdays", 'sc'); ?></label></td>
									<td>
										<label><input type="checkbox" class="sc-weekdays" name="sc-weekdays[]" value="0"/><?php _e("Sun.", 'sc'); ?></label>
										<label><input type="checkbox" class="sc-weekdays" name="sc-weekdays[]" value="1"/><?php _e("Mon.", 'sc'); ?></label>
										<label><input type="checkbox" class="sc-weekdays" name="sc-weekdays[]" value="2"/><?php _e("Tues.", 'sc'); ?></label><br />
										<label><input type="checkbox" class="sc-weekdays" name="sc-weekdays[]" value="3"/><?php _e("Wed.", 'sc'); ?></label>
										<label><input type="checkbox" class="sc-weekdays" name="sc-weekdays[]" value="4"/><?php _e("Thurs.", 'sc'); ?></label>
										<label><input type="checkbox" class="sc-weekdays" name="sc-weekdays[]" value="5"/><?php _e("Fri.", 'sc'); ?></label><br />
										<label><input type="checkbox" class="sc-weekdays" name="sc-weekdays[]" value="6"/><?php _e("Sat.", 'sc'); ?></label>
									</td>
									<td class="info"><?php _e("Choose what days of each week to display the content.", 'sc'); ?></td>
								</tr>
								<tr id="sc-onetime-row" style="display:none;">
									<td><label for="sc-date"><?php _e("Onetime Date", 'sc'); ?></label></td>
									<td>
										<select id="sc-year" name="sc-year">
										  <?php
											for ($i = 2011; $i <= 2021; $i++) {
												echo '<option value="'.$i.'">'.$i.'</option>';
											}
											?>
										</select> -
										<select id="sc-month" name="sc-month">
										  <?php
											for ($i = 1; $i <= 12; $i++) {
											  $num = (strlen($i) < 2) ? '0'.$i : $i;
												echo '<option value="'.$num.'">'.$num.'</option>';
											}
											?>
										</select> -
										<select id="sc-day" name="sc-day">
										  <?php
											for ($i = 1; $i <= 31; $i++) {
											  $num = (strlen($i) < 2) ? '0'.$i : $i;
												echo '<option value="'.$num.'">'.$num.'</option>';
											}
											?>
										</select>
									</td>
									<td class="info"><?php _e("Choose the onetime date to display the content.", 'sc'); ?></td>
								</tr>
							</table>
					</fieldset>
					<br />
        	<fieldset>
						  <table border="0" cellpadding="4" cellspacing="0">
								<tr>
									<td><label for="sc-hours"><?php _e("Open Time", 'sc'); ?></label></td>
									<td>
										<select id="sc-hours" name="sc-hours">
										  <?php
											for ($i = 1; $i <= 12; $i++) {
											  $num = (strlen($i) < 2) ? '0'.$i : $i;
												echo '<option value="'.$num.'">'.$num.'</option>';
											}
											?>
										</select>:
										<select id="sc-minutes" name="sc-minutes">
										  <?php
											for ($i = 0; $i <= 59; $i++) {
											  $num = (strlen($i) < 2) ? '0'.$i : $i;
												echo '<option value="'.$num.'">'.$num.'</option>';
											}
											?>
										</select>
										<select id="sc-ampm" name="sc-ampm">
										  <option value="AM"><?php _e("AM", 'sc'); ?></option>
										  <option value="PM"><?php _e("PM", 'sc'); ?></option>
										</select>
										<span id="sc-utcoffset">GMT</span>
									</td>
									<td class="info"><?php _e("Choose the time you want to begin displaying the content.", 'sc'); ?></td>
								</tr>
								<tr>
									<td><label for="sc-date"><?php _e("Open Length", 'sc'); ?></label></td>
									<td>
										<label><select id="sc-lyears" name="sc-lyears">
										  <?php
											for ($i = 0; $i <= 10; $i++) {
												echo '<option value="'.$i.'">'.$i.'</option>';
											}
											?>
										</select> <?php _e("Year(s)", 'sc'); ?></label>
										<label><select id="sc-ldays" name="sc-ldays">
										  <?php
											for ($i = 0; $i <= 364; $i++) {
												echo '<option value="'.$i.'">'.$i.'</option>';
											}
											?>
										</select> <?php _e("Day(s)", 'sc'); ?></label><br />
										<label><select id="sc-lhours" name="sc-lhours">
										  <?php
											for ($i = 0; $i <= 23; $i++) {
											  $selected = ($i==1) ? ' selected="selected"' : '';
												echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
											}
											?>
										</select> <?php _e("Hour(s)", 'sc'); ?></label>
										<label><select id="sc-lmins" name="sc-lmins">
										  <?php
											for ($i = 0; $i <= 59; $i++) {
												echo '<option value="'.$i.'">'.$i.'</option>';
											}
											?>
										</select> <?php _e("Min", 'sc'); ?></label>
									</td>
									<td class="info"><?php _e("Choose how long to display the content.", 'sc'); ?></td>
								</tr>
								<tr>
									<td><label for="sc-msg"><?php _e("Message", 'sc'); ?></label></td>
									<td>
										<label><input type="text" id="sc-msg" name="sc-msg" value="" style="width:100%" /></label>
									</td>
									<td class="info"><?php _e("Optional - This message displays when the content is not available.", 'sc'); ?></td>
								</tr>
							</table>
						</fieldset>
					</div>

					<div class="mceActionPanel">
						<div style="float: left">
							<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'sc'); ?>" onclick="tinyMCEPopup.close();" />
						</div>

						<div style="float: right">
							<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'sc'); ?>" />
						</div>
					</div>
				</form>
			</body>
		</html>
		<?php
		exit(0);
	}
	
	/**
	 * @see		http://codex.wordpress.org/TinyMCE_Custom_Buttons
	 */
	function tinymce_register_button($buttons) {
		array_push($buttons, "separator", "scheduled");
		return $buttons;
	}

	/**
	 * @see		http://codex.wordpress.org/TinyMCE_Custom_Buttons
	 */
	function tinymce_add_plugin($plugin_array) {
		$plugin_array['scheduled'] = plugins_url('scheduled-content/includes/editor_plugin.js');
		return $plugin_array;
	}
	
} //end class

//load class
$sc = &new ScheduledContent();


///////////////////////////////////////////////////////////////////////////
/* -------------------- Update Notifications Notice -------------------- */
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'install_plugins' ) )
      echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
  }
}
/* --------------------------------------------------------------------- */
?>