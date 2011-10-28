<?php
/*
Simple:Press
Admin integration Page and Permalink Form
$LastChangedDate: 2010-08-08 10:29:39 -0700 (Sun, 08 Aug 2010) $
$Rev: 4362 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_integration_page_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#wppageform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadpp').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $sfglobals, $wpdb;

	$sfoptions = sfa_get_integration_page_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=integration-loader&amp;saveform=page";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="wppageform" name="wppage">
	<?php echo(sfc_create_nonce('forum-adminform_integration')); ?>
<?php

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Integration", "sforum")." - ".__("Page and Permalink", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("WP Forum Page Details", "sforum"), true, 'forum-page-details');
				if($sfoptions['sfpage'] == 0)
				{
					echo('<tr><td colspan="2"><div class="sfoptionerror">'.__('ERROR: The Page Slug is either missing or incorrect. The forum will not display until this is corrected', 'sforum').'</div></td></tr>');
				}
				sfa_paint_select_start(__("Select the WP Page to be used to display your forum", "sforum"), 'slug', 'slug');
				echo(sfa_create_page_select($sfoptions['sfpage']));
				sfa_paint_select_end();
			sfa_paint_close_fieldset();

			if($sfoptions['sfpage'] != 0)
			{
				$title = $wpdb->get_var("SELECT post_title FROM ".$wpdb->posts." WHERE ID=".$sfoptions['sfpage']);
				$template = $wpdb->get_var("SELECT meta_value FROM ".$wpdb->postmeta." WHERE  meta_key='_wp_page_template' AND post_id=".$sfoptions['sfpage']);
				sfa_paint_open_fieldset(__("Current WP Forum Page", "sforum"), false, '', true);
					echo "<tr>";
					echo "<th>".__("Forum Page ID","sforum")."</th>";
					echo "<th>".__("Page Title","sforum")."</th>";
					echo "<th>".__("Page Template","sforum")."</th>";
					echo "</tr>";
					echo "<tr>";
					echo "<td class='sflabel'>".$sfoptions['sfpage']."</td>";
					echo "<td class='sflabel'>".$title."</td>";
					echo "<td class='sflabel'>".$template."</td>";
					echo "</tr>";
				sfa_paint_close_fieldset();

				sfa_paint_open_fieldset(__("Update Forum Permalink", "sforum"), true, 'forum-permalink', false);
					echo('<p class="sublabel">'.__("Current Permalink:", "sforum").'<br /></p><div class="subhead" id="adminupresult"><p>'.$sfoptions["sfpermalink"].'</p></div><br />');
					echo "<table class='form-table' width='100%'>\n";
					sfa_paint_update_permalink();
				sfa_paint_close_fieldset();
			}

			sfa_paint_open_fieldset(__("Integration Options", "sforum"), true, 'integration-options');
				sfa_paint_checkbox(__("Limit Forum Display to Within WP Loop", "sforum"), "sfinloop", $sfoptions['sfinloop']);
				sfa_paint_checkbox(__("Allow Multiple Loading of Forum Content", "sforum"), "sfmultiplecontent", $sfoptions['sfmultiplecontent']);
				sfa_paint_checkbox(__("Load JavaScript in Footer", "sforum"), "sfscriptfoot", $sfoptions['sfscriptfoot']);
			sfa_paint_close_fieldset();

		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update WP Integration', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_create_page_select($currentpageid)
{
	global $wpdb;

	$pages = $wpdb->get_results("SELECT ID, post_name, post_parent FROM ".$wpdb->posts." WHERE post_type='page' ORDER BY menu_order");
	if($pages)
	{
		$default='';
		$out='';
		$spacer='&nbsp;&nbsp;&nbsp;&nbsp;';
		$out.='<optgroup label="'.__("Select the WP Page", "sforum").':">'."\n";
		foreach ($pages as $page)
		{
			$sublevel=0;
			if($page->post_parent)
			{
				$parent = $page->post_parent;
				$pageslug = $page->post_name;
				while($parent)
				{
					$thispage = $wpdb->get_row("SELECT ID, post_name, post_parent FROM ".$wpdb->posts." WHERE ID = ".$parent);
					$pageslug = $thispage->post_name.'/'.$pageslug;
					$parent = $thispage->post_parent;
					$sublevel++;
				}
			} else {
				$pageslug = $page->post_name;
			}

			if($currentpageid == $page->ID)
			{
				$default = 'selected="selected" ';
			} else {
				$default - null;
			}
			$out.='<option '.$default.'value="'.$page->ID.'">'.$spacer.str_repeat('&rarr;&nbsp;', $sublevel).$pageslug.'</option>'."\n";
			$default='';
		}
		$out.= '</optgroup>';
	} else {
		$out.='<option value="0">'.__("No WP Pages Found - Please Create One", "sforum").'</option>'."\n";
	}
	return $out;
}

function sfa_paint_update_permalink()
{
	echo "<tr valign='top'>\n";
	echo "<td width='50%'>\n";
    $site = SFHOMEURL."index.php?sf_ahah=integration-perm&item=upperm";
	$target = 'adminupresult';
	$gif = SFADMINIMAGES."working.gif";

	echo '<input type="button" class="button button-highlighted" value="'.esc_attr(__("Update Forum Permalink", "sforum")).'" onclick="sfjadminTool(\''.$site.'\', \''.$target.'\', \''.$gif.'\');" />';

	echo '</td>';
	echo '</tr>';

	return;
}

?>