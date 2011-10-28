<?php
/*
Simple:Press
Admin Forums Global RSS Settings Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the add global permission set form. It is hidden until user clicks the add global permission set link
function sfa_forums_global_rss_form()
{
    global $wpdb;
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfnewglobalrss').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadfd').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=globalrss";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfnewglobalrss" name="sfnewglobalrss">
<?php
		echo(sfc_create_nonce('forum-adminform_globalrss'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Global RSS Settings", "sforum"));
			sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Globally Enable/Disable RSS Feeds", "sforum"), true, 'global-rss', true);
?>
				<tr>
					<td class="sflabel"><?php _e('Replacement External RSS URL for All RSS', 'sforum') ?>:<br /><?php _e("Default", "sforum"); ?>: <strong><?php echo sf_build_qurl('xfeed=all'); ?></strong></td>
					<td><input class="sfpostcontrol" type="text" name="sfallrssurl" size="45" value="<?php echo(sf_get_option('sfallRSSurl')); ?>" /></td>
				</tr>
<?php
				echo '<tr><td colspan="2" class="sflabel">';
                        $base = SFHOMEURL."index.php?sf_ahah=forums-loader";
						$target = "sfallrss";
						$image = SFADMINIMAGES;

                        $rss_count = $wpdb->get_var("SELECT COUNT(*) FROM ".SFFORUMS." where forum_rss_private=0");
                        echo __("Active Forum RSS Feeds", "sforum").': '.$rss_count.'&nbsp;&nbsp;&nbsp;&nbsp;';
?>
						<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Disable All RSS Feeds", "sforum")),0,0); ?>" onclick="sfjLoadForm('globalrssset', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '1', '1');" />
						<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Enable All RSS Feeds", "sforum")),0,0); ?>" onclick="sfjLoadForm('globalrssset', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '0', '1');" />
<?php
				echo '</td></tr>';
				echo '<tr>  <!-- This row will hold ahah forms for the all rss -->';
	  			echo '<td class="sfinline-form" colspan="2" style="height:0px">';
				echo '<div id="sfallrss">';
				echo '</div>';
				echo '</td>';
				echo '</tr>';
			sfa_paint_close_fieldset();
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Global RSS Settings', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>