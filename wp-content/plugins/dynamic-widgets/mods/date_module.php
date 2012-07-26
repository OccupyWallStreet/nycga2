<?php
/**
 * Date Module
 * Can't use DWOpts object because value = the actual date
 *
 * @version $Id: date_module.php 437634 2011-09-13 19:19:13Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Date extends DWModule {
		public static $option = array( 'date' => 'Date' );
		protected static $overrule = TRUE;
		protected static $type = 'custom';

		public static function admin() {
			$DW = $GLOBALS['DW'];

			parent::admin();

			$date_yes_selected = 'checked="checked"';
			$opt_date = $DW->getOpt($_GET['id'], 'date');

			if ( count($opt_date) > 0 ) {
				foreach ( $opt_date as $value ) {
					switch ( $value->name ) {
						case 'date_start':
							$date_start = $value->value;
							break;

						case 'date_end':
							$date_end = $value->value;
							break;
					}
				}

				$date_no_selected = $date_yes_selected;
				unset($date_yes_selected);
			}
?>

<h4><b><?php _e('Date'); ?></b><?php echo ( count($opt_date) > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget always?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('date');" /><br />
<?php $DW->dumpOpt($opt_date); ?>
<div>
	<div id="date" class="infotext">
  <?php _e('Next to the above role option, the date option is also very powerfull. You\'ve been warned!', DW_L10N_DOMAIN); ?><br />
  <?php _e('Enter dates in the YYYY-MM-DD format. You can also use the calender by clicking on the', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/calendar.gif" alt="Calendar" /><br />
  <?php _e('Date ranges can be made by entering a From AND a To date<br />
  					When you want the widget to be displayed from a specific date, only fill in the From date<br />
  					When you want the widget to stop displaying on a specific date, only fill in the To date.
  				', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="date" value="yes" id="date-yes" <?php echo ( isset($date_yes_selected) ) ? $date_yes_selected : ''; ?> onclick="swTxt(cDate, true);" /> <label for="date-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="date" value="no" id="date-no" <?php echo ( isset($date_no_selected) ) ? $date_no_selected : ''; ?> onclick="swTxt(cDate, false)" /> <label for="date-no"><?php _e('No'); ?>, <?php _e('only', DW_L10N_DOMAIN); ?>:</label><br />
<div id="date-select" class="condition-select">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td style="width:45px;"><?php _e('From', DW_L10N_DOMAIN); ?></td>
  <td><input id="date_start" type="text" name="date_start" value="<?php echo ( isset($date_start) ) ? $date_start : ''; ?>" size="10" maxlength="10" /> <img src="<?php echo $DW->plugin_url; ?>img/calendar.gif" alt="Calendar" onclick="showCalendar('date_start')" /></td>
</tr>
<tr>
  <td style="width:45px;"><?php _e('To', DW_L10N_DOMAIN); ?></td>
  <td><input id="date_end" type="text" name="date_end" value="<?php echo ( isset($date_end) ) ? $date_end : ''; ?>" size="10" maxlength="10" /> <img src="<?php echo $DW->plugin_url; ?>img/calendar.gif" alt="Calendar" onclick="showCalendar('date_end')" /></td>
</tr>
</table>
</div>
</div><!-- end dynwid_conf -->

<script type="text/javascript">
/* <![CDATA[ */
  function showCalendar(id) {
    if ( jQuery('#date-no').is(':checked') ) {
      var id = '#'+id;
      jQuery(function() {
  		  jQuery(id).datepicker({
  		    dateFormat: 'yy-mm-dd',
  		    minDate: new Date(<?php echo date('Y, n - 1, j'); ?>),
  		    onClose: function() {
  		    	jQuery(id).datepicker('destroy');
  		    }
  		  });
        jQuery(id).datepicker('show');
    	});
    } else {
      jQuery('#date-no').attr('checked', true);
      swTxt(cDate, false);
      showCalendar(id);
    }
  }

  var cDate =  new Array('date_start', 'date_end');

  if ( jQuery('#date-yes').is(':checked') ) {
  	swTxt(cDate, true);
  }
/* ]]> */
</script>
<?php
		}
	}
?>