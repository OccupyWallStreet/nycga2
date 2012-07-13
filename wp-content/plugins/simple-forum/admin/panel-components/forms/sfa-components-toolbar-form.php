<?php
/*
Simple:Press
Admin Components Toolbar Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_toolbar_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sftoolbarform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadtb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery('#sftoolbarorderform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadtb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery('#sftoolbarrestore').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadtb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sftbextras = sfa_get_toolbar_extras();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=toolbar";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sftoolbarform" name="sftoolbar">
	<?php echo(sfc_create_nonce('forum-adminform_toolbar')); ?>
<?php

	sfa_paint_options_init();

#== TOOLBAR Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Editor Toolbar", "sforum"));

		sfa_paint_open_panel();

			sfa_paint_open_fieldset(__("Remove Editor Toolbar Buttons (TinyMCE)", "sforum"), true, 'remove-editor-buttons', false);
			# Remove buttons
			sfa_render_remove_toolbar();
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();

	sfa_paint_close_tab();
?>
	<input type="text" class="inline_edit" size="70" id="delbuttons" name="delbuttons" />

	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="savetb1" name="savetb2" value="<?php esc_attr_e(__('Update Editor Toolbar Component', 'sforum')); ?>" />

	</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=toolbar";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sftoolbarorderform" name="sftoolbarorderform">
	<?php echo(sfc_create_nonce('forum-adminform_toolbar')); ?>
<?php
	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Editor Toolbar", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Re-order Editor Toolbar Buttons (TinyMCE)", "sforum"), true, 'reorder-editor-buttons', false);
			# Drag/Drop/Sort
			sfa_render_drag_toolbar();
			sfa_paint_close_fieldset(false);

		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Second Toolbar", "sforum"), true, 'second-toolbar');
				sfa_paint_input(__("Toolbar Button Names for Second Toolbar (separate buttons with commas)", "sforum"), "sftbextras", $sftbextras);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Editor Toolbar Component', 'sforum')); ?>" />
	</div>
	</form>

	<br /><br />
<?php
    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=tbrestore";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sftoolbarrestore" name="sftoolbarrestore">
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Restore Editor Toolbar Component Defaults', 'sforum')); ?>" />
	</div>
	</form>

<?php
	return;
}

# Display the toolbar as one line

# Display the draggable/sortable toolbars (standard and plugins additions
function sfa_render_drag_toolbar()
{
	$ipath = SFADMINIMAGES.'toolbar/';
	echo '<label for="sftbarstan" class="sublabel">'.__("To re-order, select buttons with the mouse cursor and drag to new position. Finally click on the Update Toolbar to save", "sforum").'<br /><br />'.__("Standard Buttons", "sforum").'</label>';
	?>

	<ul id="sftbarstan">
	<?php
	$tbmeta = sf_get_sfmeta('tinymce_toolbar', 'user');
	$tbdata = unserialize($tbmeta[0]['meta_value']);
	$toolbar = $tbdata['tbar_buttons'];
	$thisb = 0;
	foreach($toolbar as $button)
	{
		if($button == "|")
		{
			$img = "separator.gif";
			$bname = 'separator';
		} else {
			$img = $button.'.gif';
			$bname = $button;
		}

		?>
		<li id="sItem_<?php echo($thisb); ?>"><img src="<?php echo($ipath.$img); ?>" class="handle" alt="<?php esc_attr_e(__("move", "sforum")); ?>" title="<?php esc_attr_e($bname); ?>" /></li>
		<?php
		$thisb++;
	}
	?>
	</ul>
	<?php
	echo '<label for="sftbarplug" class="sublabel">'.__("Plugin Buttons", "sforum").'</label>';
	?>
	<ul id="sftbarplug">
	<?php
	$tbmeta = sf_get_sfmeta('tinymce_toolbar', 'user');
	$tbdata = unserialize($tbmeta[0]['meta_value']);
	$toolbar = $tbdata['tbar_buttons_add'];
	$thisb = 0;
	foreach($toolbar as $button)
	{
		if($button == "|")
		{
			$img = "separator.gif";
			$bname = 'separator';
		} else {
			$img = $button.'.gif';
			$bname = $button;
		}

		?>
		<li id="pItem_<?php echo($thisb); ?>"><img src="<?php echo($ipath.$img); ?>" class="handle" alt="<?php esc_attr_e(__("move", "sforum")); ?>" title="<?php esc_attr_e($bname); ?>" /></li>
		<?php
		$thisb++;
	}
	?>
	</ul>

	<input type="text" class="inline_edit" size="70" id="stan_buttons" name="stan_buttons" />
	<input type="text" class="inline_edit" size="70" id="plug_buttons" name="plug_buttons" />
	<?php
	return;
}

?>