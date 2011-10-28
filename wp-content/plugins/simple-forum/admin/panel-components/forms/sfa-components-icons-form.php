<?php
/*
Simple:Press
Admin Components Custom Icons Form
$LastChangedDate: 2010-12-02 05:15:10 -0700 (Thu, 02 Dec 2010) $
$Rev: 5033 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_icons_form()
{
	global $SFPATHS;
?>
<script type= "text/javascript">/*<![CDATA[*/
jQuery(document).ready(function() {
	jQuery('#sficonsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadci').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});

	var button = jQuery('#sf-upload-button'), interval;
	new AjaxUpload(button,{
		action: '<?php echo SFUPLOADER; ?>',
		name: 'uploadfile',
	    data: {
		    saveloc : '<?php echo addslashes(SF_STORE_DIR."/".$SFPATHS['custom-icons']."/"); ?>'
	    },
		onSubmit : function(file, ext){
            /* check for valid extension */
			if (! (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext))){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Only JPG, PNG or GIF files are allowed!", "sforum")); ?></p>');
				return false;
			}
			/* change button text, when user selects file */
			utext = '<?php echo esc_js(__("Uploading", "sforum")); ?>';
			button.text(utext);
			/* If you want to allow uploading only 1 file at time, you can disable upload button */
			this.disable();
			/* Uploding -> Uploading. -> Uploading... */
			interval = window.setInterval(function(){
				var text = button.text();
				if (text.length < 13){
					button.text(text + '.');
				} else {
					button.text(utext);
				}
			}, 200);
		},
		onComplete: function(file, response){
			jQuery('#sf-upload-status').html('');
			button.text('<?php echo esc_js(__("Browse", "sforum")); ?>');
			window.clearInterval(interval);
			/* re-enable upload button */
			this.enable();
			/* add file to the list */
			if (response==="success"){
                site = "<?php echo SFHOMEURL; ?>index.php?sf_ahah=components&action=delicon&amp;file=" + file;
				jQuery('<table width="100%"></table>').appendTo('#sf-custom-icons').html('<tr><td width="60%" align="center"><img class="sfcustomicon" src="<?php echo SFCUSTOMURL; ?>/' + file + '" alt="" /></td><td class="sflabel" align="center" width="30%">' + file + '</td><td class="sflabel" align="center" width="9%"><img src="<?php echo SFADMINIMAGES; ?>' + 'del_cfield.png' + '" title="<?php echo esc_js(__("Delete Custom Icon", "sforum")); ?>" alt="" onclick="sfjDelIcon(\'' + site + '\');" /></td></tr>');
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-success"><?php echo esc_js(__("Custom Icon Uploaded!", "sforum")); ?></p>');
			} else if (response==="exists"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file already exists!", "sforum")); ?></p>');
			} else if (response==="invalid"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file has an invalid format!", "sforum")); ?></p>');
			} else {
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Error uploading file!!", "sforum")); ?></p>');
			}
		}
	});
});/*]]>*/</script>
<?php

	$sfcomps = sfa_get_icons_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=icons";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sficonsform" name="sficons">
	<?php echo(sfc_create_nonce('forum-adminform_icons')); ?>
<?php

	sfa_paint_options_init();

#== CUSTOM ICONS Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Custom Icons", "sforum"));

		sfa_paint_open_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Icon 1", "sforum"), true, 'custom-icon');

				if(!empty($sfcomps['cusicon0']))
				{
					$path = SFCUSTOM.$sfcomps['cusicon0'];
					if (!file_exists($path))
					{
						echo('<tr><td colspan="2"><div class="sfoptionerror">'.sprintf(__("Custom Icon '%s' does not exist", "sforum"), $sfcomps['cusicon0']).'</div></td></tr>');
					}
				}

				sfa_paint_input(__("Display Text (Optional)", "sforum"), "custext0", $sfcomps['custext0'], false, true);
				sfa_paint_input(__("Target URL", "sforum"), "cuslink0", $sfcomps['cuslink0'], false, true);
				echo '<tr><td class="sflabel">'.__("Custom Icon", "sforum").'</td>';
				echo '<td class="sflabel">';
				sfa_select_icon_dropdown('cusicon0', __("Select Icon", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/', $sfcomps['cusicon0']);
				echo '</td></tr>';
				sfa_paint_icon($sfcomps['cusicon0']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Icon 2", "sforum"), false, 'custom-icon');

				if(!empty($sfcomps['cusicon1']))
				{
					$path = SFCUSTOM.$sfcomps['cusicon1'];
					if(!file_exists($path))
					{
						echo('<tr><td colspan="2"><div class="sfoptionerror">'.sprintf(__("Custom Icon '%s' does not exist", "sforum"), $sfcomps['cusicon1']).'</div></td></tr>');
					}
				}

				sfa_paint_input(__("Display Text (Optional)", "sforum"), "custext1", $sfcomps['custext1'], false, true);
				sfa_paint_input(__("Target URL", "sforum"), "cuslink1", $sfcomps['cuslink1'], false, true);
				echo '<tr><td class="sflabel">'.__("Custom Icon", "sforum").'</td>';
				echo '<td class="sflabel">';
				sfa_select_icon_dropdown('cusicon1', __("Select Icon", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/', $sfcomps['cusicon1']);
				echo '</td></tr>';
				sfa_paint_icon($sfcomps['cusicon1']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Icon 3", "sforum"), false, 'custom-icon');

				if(!empty($sfcomps['cusicon2']))
				{
					$path = SFCUSTOM.$sfcomps['cusicon2'];
					if(!file_exists($path))
					{
						echo('<tr><td colspan="2"><div class="sfoptionerror">'.sprintf(__("Custom Icon '%s' does not exist", "sforum"), $sfcomps['cusicon2']).'</div></td></tr>');
					}
				}

				sfa_paint_input(__("Display Text (Optional)", "sforum"), "custext2", $sfcomps['custext2'], false, true);
				sfa_paint_input(__("Target URL", "sforum"), "cuslink2", $sfcomps['cuslink2'], false, true);
				echo '<tr><td class="sflabel">'.__("Custom Icon", "sforum").'</td>';
				echo '<td class="sflabel">';
				sfa_select_icon_dropdown('cusicon2', __("Select Icon", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/', $sfcomps['cusicon2']);
				echo '</td></tr>';
				sfa_paint_icon($sfcomps['cusicon2']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_close_panel();

		echo '<div class="sfform-panel-spacer"></div>';

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Icon Upload", "sforum"), true, 'icons-upload');
				$loc = SF_STORE_DIR."/".$SFPATHS['custom-icons']."/";
				sfa_paint_file(__("Select Icon to Upload", "sforum"), 'newcustomicon', false, true, $loc);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Icons", "sforum"), true, 'custom-icons', true);
			sfa_paint_custom_icons();
			sfa_paint_close_fieldset(true);
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Custom Icons Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>