<?php
/*
Simple:Press
Admin Profile Avatars Form
$LastChangedDate: 2010-12-02 05:15:10 -0700 (Thu, 02 Dec 2010) $
$Rev: 5033 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_profiles_avatars_form()
{
	global $SFPATHS;
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfavatarsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadav').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});

	jQuery("#sfavataroptions").sortable({
		handle : '.handle',
		update : function () {
			jQuery("input#sfavataropts").val(jQuery("#sfavataroptions").sortable('serialize'));
		}
	});

	var button = jQuery('#sf-upload-button'), interval;
	new AjaxUpload(button,{
		action: '<?php echo SFUPLOADER; ?>',
		name: 'uploadfile',
	    data: {
		    saveloc : '<?php echo addslashes(SF_STORE_DIR."/".$SFPATHS['avatar-pool']."/"); ?>'
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
                site = "<?php echo SFHOMEURL; ?>index.php?sf_ahah=profiles&action=delavatar&amp;file=" + file;
				jQuery('<table width="100%"></table>').appendTo('#sf-avatar-pool').html('<tr><td width="60%" align="center"><img class="sfavatarpool" src="<?php echo SFAVATARPOOLURL; ?>/' + file + '" alt="" /></td><td class="sflabel" align="center" width="30%">' + file + '</td><td class="sflabel" align="center" width="9%"><img src="<?php echo SFADMINIMAGES; ?>' + 'del_cfield.png' + '" title="<?php echo esc_js(__("Delete Avatar", "sforum")); ?>" alt="" onclick="sfjDelAvatar(\'' + site + '\');" /></td></tr>');
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-success"><?php echo esc_js(__("Avatar Uploaded!", "sforum")); ?></p>');
			} else if (response==="exists"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file already exists!", "sforum")); ?></p>');
			} else if (response==="invalid"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file has an invalid format!", "sforum")); ?></p>');
			} else {
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Error uploading file!!", "sforum")); ?></p>');
			}
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_avatars_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=profiles-loader&amp;saveform=avatars";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfavatarsform" name="sfavatars">
	<?php echo(sfc_create_nonce('forum-adminform_avatars')); ?>
<?php

	sfa_paint_options_init();

#== PROFILE OPTIONS Tab ============================================================

	sfa_paint_open_tab(__("Profiles", "sforum")." - ".__("Avatars", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Avatar Options", "sforum"), true, 'avatar-options');
				sfa_paint_checkbox(__("Display Avatars", "sforum"), "sfshowavatars", $sfoptions['sfshowavatars']);
				sfa_paint_checkbox(__("Enable Avatar Uploading", "sforum"), "sfavataruploads", $sfoptions['sfavataruploads']);
				sfa_paint_input(__("Maximum Avatar Upload File Size (bytes)", "sforum"), "sfavatarfilesize", $sfoptions['sfavatarfilesize'], false, false);
				sfa_paint_checkbox(__("Enable Avatar Pool Selection", "sforum"), "sfavatarpool", $sfoptions['sfavatarpool']);
				sfa_paint_checkbox(__("Enable Remote Avatars", "sforum"), "sfavatarremote", $sfoptions['sfavatarremote']);
				$values = array(__('G - Suitable for all', 'sforum'), __('PG- Suitable for 13 and above', 'sforum'), __('R - Suitable for 17 and above', 'sforum'),__('X - Suitable for all adults', 'sforum'));
				sfa_paint_radiogroup(__("Gravatar Max Rating", "sforum"), 'sfgmaxrating', $values, $sfoptions['sfgmaxrating'], false, true);
				sfa_paint_input(__("Maximum Avatar Display Width (pixels)", "sforum"), "sfavatarsize", $sfoptions['sfavatarsize'], false, false);
				sfa_paint_checkbox(__("Replace WP Avatar with SPF Avatar", "sforum"), "sfavatarreplace", $sfoptions['sfavatarreplace']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Avatar Priorities", "sforum"), true, 'avatar-priorities');
				echo '<tr>';
				echo '<td class="sflabel" colspan="2">';
				echo '<table class="form-table table-cbox">';
				echo '<tr>';
				echo '<td class="td-cbox" style="width:200px">';
				echo '<ul id="sfavataroptions">';
				$list = array(0 => __("Gravatars", "sforum"), 1 => __("WP Avatars", "sforum"), 2 => __("Uploaded Avatar", "sforum"), 3 => __("SPF Default Avatars", "sforum"), 4 => __("Avatar Pool", "sforum"), 5 => __("Remote Avatar", "sforum"));
				if($sfoptions['sfavatarpriority'])
				{
					foreach ($sfoptions['sfavatarpriority'] as $priority)
					{
						echo '<li id="aitem_'.$priority.'"><span class="handle">'.$list[$priority].'</span></li>';
					}
				}
				echo '</ul>';
				echo '<input type="text" class="inline_edit" size="70" id="sfavataropts" name="sfavataropts" />';
				echo '</td>';
				echo '<td class="td-cbox">';
				echo __("Select the Avatar dislay priority order by dragging and dropping the buttons in the column to the left.  The top of the list is the highest priority order.  When an avatar is found for the current priority, it is output.  If none is found, the next priority is checked and so on.  An SPF Default Avatar will always be found. Any avatar after the SPF Default Avatar is essentially ignored.", "sforum");
				echo '</td>';
				echo '</tr>';
				echo '</table>';
				echo '</td>';
				echo '</tr>';
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_panel();

		echo '<div class="sfform-panel-spacer"></div>';

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Avatar Pool Upload", "sforum"), true, 'avatar-pool-upload');
				$loc = SF_STORE_DIR."/".$SFPATHS['avatar-pool']."/";
				sfa_paint_file(__("Select Avatar to Upload", "sforum"), 'newavatar', false, true, $loc);
				echo '<tr>';
				echo '<td class="sflabel" colspan="2"><small>';
				echo __("Please be advised that Admin uploaded avatars for the avatar pool are NOT subject to the user uploaded avatar size limits.  So use caution when picking avatars for your avatar pool.", "sforum");
				echo '</small></td>';
				echo '</tr>';
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Avatar Pool", "sforum"), true, 'avatar-pool', true);
				sfa_paint_avatar_pool();
			sfa_paint_close_fieldset(true);
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Avatar Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>