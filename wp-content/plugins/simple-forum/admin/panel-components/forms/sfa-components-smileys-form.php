<?php
/*
Simple:Press
Admin Components Smileys Form
$LastChangedDate: 2010-12-02 05:15:10 -0700 (Thu, 02 Dec 2010) $
$Rev: 5033 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_smileys_form()
{
	global $SFPATHS;
?>
<script type= "text/javascript">/*<![CDATA[*/
jQuery(document).ready(function(){
    jQuery('#sfsmileysform').ajaxForm({
        target: '#sfmsgspot',
        success: function() {
            jQuery('#sfreloadsm').click();
            jQuery('#sfmsgspot').fadeIn();
            jQuery('#sfmsgspot').fadeOut(6000);
        }
    });

	var button = jQuery('#sf-upload-button'), interval;
	new AjaxUpload(button,{
		action: '<?php echo SFUPLOADER; ?>',
		name: 'uploadfile',
	    data: {
		    saveloc : '<?php echo addslashes(SF_STORE_DIR."/".$SFPATHS['smileys']."/"); ?>'
	    },
		onSubmit : function(file, ext){
            /* check for valid extension */
			if (! (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext))){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-text"><?php echo esc_js(__("Only JPG, PNG or GIF files are allowed!", "sforum")); ?></p>');
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
                site = "<?php echo SFHOMEURL; ?>index.php?sf_ahah=components&action=delsmiley&amp;file=" + file;
				jQuery('<table width="100%"></table>').appendTo('#sf-smiley-imgs').html('<tr><td width="5%" align="center"><img class="sfsmiley" src="<?php echo SFSMILEYS; ?>' + file + '" alt="" /></td><td class="sflabel" align="center" width="30%"><input type="hidden" name="smfile[]" value="' + file + '" />' + file + '</td><td width="30%" align="center"><input type="text" class="sfpostcontrol" size="20" name="smname[]" value="" /></td><td width="30%" align="center"><input type="text" class="sfpostcontrol" size="20" name="smcode[]" value="" /></td><td class="sflabel" align="center" width="5%"><img src="<?php echo SFADMINIMAGES; ?>' + 'del_cfield.png' + '" title="<?php echo esc_js(__("Delete Smiley", "sforum")); ?>" alt="" onclick="sfjDelSmiley(\'' + site + '\');" /></td></tr>');
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-success"><?php echo esc_js(__("Smiley Uploaded!", "sforum")); ?></p>');
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
	$sfcomps = sfa_get_smileys_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=smileys";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfsmileysform" name="sfsmileys" enctype="multipart/form-data">
	<?php echo(sfc_create_nonce('forum-adminform_smileys')); ?>
<?php

	sfa_paint_options_init();

#== SMILEYS Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Smileys", "sforum"));

		sfa_paint_open_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Smiley Options", "sforum"), true, 'smiley-options');
				sfa_paint_checkbox(__("Allow Smileys", "sforum"), "sfsmallow", $sfcomps['sfsmallow'], false, true, true);
				$values = array(__("Custom Smileys (All Editors)", "sforum"), __("TinyMCE Smileys (TinyMCE Editor Only)", "sforum"), __("WordPress Smileys (All Editors)", "sforum"));
				sfa_paint_radiogroup(__("Select Smiley Set", "sforum"), 'smileytype', $values, $sfcomps['sfsmtype'], false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Smiley Upload", "sforum"), true, 'smiley-upload');
				$loc = SF_STORE_DIR."/".$SFPATHS['smileys']."/";
				sfa_paint_file(__("Select Smiley File(s) to Upload", "sforum"), 'newsmileyfile', false, true, $loc);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Smileys", "sforum"), true, 'custom-smileys', true);
			sfa_paint_custom_smileys();
			sfa_paint_close_fieldset(true);
		sfa_paint_close_panel();

		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="updatesmileys" name="saveit" value="<?php esc_attr_e(__('Update Smileys Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>