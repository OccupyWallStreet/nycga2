<?php
/**
 * dynwid_admin_edit.php - Options settings
 *
 * @version $Id: dynwid_admin_edit.php 532982 2012-04-18 17:35:12Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	// Plugins support
	DW_BP::detect();
	DW_QT::detect();
	DW_WPSC::detect();
	DW_WPML::detect();

	// Sanitizing some stuff
	$widget_id = ( isset($_GET['id']) && ! empty($_GET['id']) ) ? esc_attr($_GET['id']) : '';
	$return_url = ( isset($_GET['returnurl']) && ! empty($_GET['returnurl']) ) ? esc_url($_GET['returnurl']) : '';
	
	if (! array_key_exists($widget_id, $DW->registered_widgets) ) {
  	wp_die('WidgetID is not valid');
  }
?>

<style type="text/css">
label {
  cursor : default;
}

.condition-select {
  width : 300px;
  -moz-border-radius-topleft : 6px;
  -moz-border-radius-topright : 6px;
  -moz-border-radius-bottomleft : 6px;
  -moz-border-radius-bottomright : 6px;
  border-style : solid;
  border-width : 1px;
  border-color : #E3E3E3;
  padding : 5px;
}

.infotext {
  width : 98%;
  display : none;
  color : #666666;
  font-style : italic;
}

h4 {
	text-indent : 30px;
}

.hasoptions {
	color : #ff0000;
}

#dynwid {
	font-family : 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif;
	font-size : 13px;
}

.ui-datepicker {
	font-size : 10px;
}
</style>

<script type="text/javascript">
/* <![CDATA[ */
  function chkChild(prefix, pid) {
  	if ( jQuery('#'+prefix+'_act_'+pid).is(':checked') == false ) {
  		jQuery('#'+prefix+'_childs_act_'+pid).attr('checked', false);
  	}
  }

  function chkParent(prefix, pid) {
  	if ( jQuery('#'+prefix+'_childs_act_'+pid).is(':checked') == true ) {
  		jQuery('#'+prefix+'_act_'+pid).attr('checked', true);
  	}
  }

  function chkCPChild(type, pid) {
  	if ( jQuery('#'+type+'_act_'+pid).is(':checked') == false ) {
  		jQuery('#'+type+'_childs_act_'+pid).attr('checked', false);
  	}
  }

  function chkCPParent(type, pid) {
  	if ( jQuery('#'+type+'_childs_act_'+pid).is(':checked') == true ) {
  		jQuery('#'+type+'_act_'+pid).attr('checked', true);
  	}
  }

  function divToggle(div) {
    var div = '#'+div;
    jQuery(div).slideToggle(400);
  }

  function swChb(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    jQuery('#'+c[i]).attr('checked', false);
  	  }
      jQuery('#'+c[i]).attr('disabled', s);
    }
  }

  function saveandreturn() {
		var returnurl = '<?php echo trailingslashit(admin_url()) . 'themes.php?page=dynwid-config'; ?>';
		jQuery('#returnurl').val(returnurl);
		jQuery('#dwsave').submit();
  }

  function swTxt(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    jQuery('#'+c[i]).val('');
  	  }
      jQuery('#'+c[i]).attr('disabled', s);
    }
  }

  function setOff() {
  	jQuery(':radio').each( function() {
  		if ( jQuery(this).val() == 'no' && jQuery.inArray(jQuery(this).attr('name'), exclOff) == -1 ) {
  			jQuery(this).attr('checked', true);
  		};
  	});
  	alert('All options set to \'No\'.\nDon\'t forget to make changes, otherwise you\'ll receive an error when saving.');
  }

  jQuery(document).ready(function() {
		jQuery('#dynwid').accordion({
			header: 'h4',
			autoHeight: false,
		});
	});
/* ]]> */
</script>

<?php
	if ( isset($_POST['dynwid_save']) && $_POST['dynwid_save'] == 'yes' ) {
		$lead = __('Widget options saved.', DW_L10N_DOMAIN);
		$msg = '<a href="themes.php?page=dynwid-config">' . __('Return', DW_L10N_DOMAIN) . '</a> ' . __('to Dynamic Widgets overview', DW_L10N_DOMAIN);
		DWMessageBox::create($lead, $msg);
	} else if ( isset($_GET['work']) && $_GET['work'] == 'none' ) {
		DWMessageBox::setTypeMsg('error');
		$text = __('Dynamic does not mean static hiding of a widget.', DW_L10N_DOMAIN) . ' ' . __('Hint', DW_L10N_DOMAIN) . ': <a href="widgets.php">' . __('Remove', DW_L10N_DOMAIN) . '</a>' . ' ' . __('the widget from the sidebar', DW_L10N_DOMAIN) . '.';
		DWMessageBox::setMessage($text);
		DWMessageBox::output();
	} else if ( isset($_GET['work']) && $_GET['work'] == 'nonedate' ) {
		DWMessageBox::setTypeMsg('error');
		$text = __('The From date can\'t be later than the To date.', DW_L10N_DOMAIN);
		DWMessageBox::setMessage($text);
		DWMessageBox::output();
	}
?>

<h3><?php _e('Edit options for the widget', DW_L10N_DOMAIN); ?>: <em><?php echo $DW->getName($widget_id); ?></em></h3>
<?php echo ( DW_DEBUG ) ? '<pre>ID = ' . $widget_id . '</pre><br />' : ''; ?>

<div style="border-color: #E3E3E3;border-radius: 6px 6px 6px 6px;border-style: solid;border-width: 1px;padding: 5px;">
<b><?php _e('Quick settings', DW_L10N_DOMAIN); ?></b>
<p>
<a href="#" onclick="setOff(); return false;"><?php _e('Set all options to \'No\'', DW_L10N_DOMAIN); ?></a> (<?php _e('Except overriding options like Role, Date, etc.', DW_L10N_DOMAIN); ?>)
</p>
</div><br />

<form id="dwsave" action="<?php echo trailingslashit(admin_url()) . 'themes.php?page=dynwid-config&action=edit&id=' . $widget_id; ?>" method="post">
<?php wp_nonce_field('plugin-name-action_edit_' . $widget_id); ?>
<input type="hidden" name="dynwid_save" value="yes" />
<input type="hidden" name="widget_id" value="<?php echo $widget_id; ?>" />
<input type="hidden" id="returnurl" name="returnurl" value="<?php echo ( (! empty($return_url)) ? trailingslashit(admin_url()) . $return_url : '' ); ?>" />

<div id="dynwid">
<?php
	$DW_Role = new DW_Role();
	$DW_Role->admin();

	$DW_Date = new DW_Date();
	$DW_Date->admin();

	$DW_WPML = new DW_WPML();
	$DW_WPML->admin();

	$DW_QT = new DW_QT();
	$DW_QT->admin();

	$DW_Browser = new DW_Browser();
	$DW_Browser->admin();

	$DW_Tpl = new DW_Tpl();
	$DW_Tpl->admin();

	$DW_Front_page = new DW_Front_page();
	$DW_Front_page->admin();

	$DW_Single = new DW_Single();
	$DW_Single->admin();

	$DW_Attachment = new DW_Attachment();
	$DW_Attachment->admin();

	$DW_Page = new DW_Page();
	$DW_Page->admin();

	$DW_Author = new DW_Author();
	$DW_Author->admin();

	$DW_Category = new DW_Category();
	$DW_Category->admin();

	$DW_Tag = new DW_Tag();
	$DW_Tag->admin();

	$DW_Archive = new DW_Archive();
	$DW_Archive->admin();

	$DW_E404 = new DW_E404();
	$DW_E404->admin();

	$DW_Search = new DW_Search();
	$DW_Search->admin();

	$DW_CustomPost = new DW_CustomPost();
	$DW_CustomPost->admin();

	$DW_WPSC = new DW_WPSC();
	$DW_WPSC->admin();

	$DW_BP = new DW_BP();
	$DW_BP->admin();
	
	$DW_bbPress = new DW_bbPress();
	$DW_bbPress->admin();

	$DW_Pods = new DW_Pods();
	$DW_Pods->admin();

	// For JS exclOff
	$excl = array();
	foreach ( $DW->overrule_maintype as $m ) {
		$excl[ ] = "'" . $m . "'";
	}
?>

</div><!-- end dynwid -->
<br /><br />

<!-- <div>
Save as a quick setting <input type="text" name="qsetting" value="" />
</div> //-->

<br />
<div style="float:left">
<input class="button-primary" type="submit" value="<?php _e('Save'); ?>" /> &nbsp;&nbsp;
</div>
<?php $url = (! empty($return_url) ) ? trailingslashit(admin_url()) . $return_url : trailingslashit(admin_url()) . 'themes.php?page=dynwid-config'; ?>

<?php if ( empty($return_url) ) { ?>
<div style="float:left">
<input class="button-primary" type="button" value="<?php _e('Save'); ?> & <?php _e('Return', DW_L10N_DOMAIN); ?>" onclick="saveandreturn()" /> &nbsp;&nbsp;
</div>
<?php } ?>

<div style="float:left">
<input class="button-secondary" type="button" value="<?php _e('Return', DW_L10N_DOMAIN); ?>" onclick="location.href='<?php echo $url; ?>'" />
</div>

</form>

<script type="text/javascript">
/* <![CDATA[ */
	var exclOff = new Array(<?php echo implode(', ', $excl); ?>);
/* ]]> */
</script>
