<?php
/*
Simple:Press
Admin Components Links Form
$LastChangedDate: 2010-11-11 17:51:01 -0700 (Thu, 11 Nov 2010) $
$Rev: 4917 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_links_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sflinksform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_links_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=links";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sflinksform" name="sflinks">
	<?php echo(sfc_create_nonce('forum-adminform_links')); ?>
<?php

	sfa_paint_options_init();

#== LINKS Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Blog Linking", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Post Linking", "sforum"), true, 'post-linking');
				echo('<tr><td colspan="2"><p class="subhead">'.__("If you are using Post Linking", "sforum").':</p></td></tr>');
				sfa_paint_checkbox(__("Auto-Create Blog Post to Topic Linking In Post Editor (default On)", "sforum"), "sfautocreate", $sfoptions['sfautocreate']);

				sfa_paint_select_start(__("Default Forum for Link Auto-Creation", "sforum"), "sfautoforum", "sfautoforum");
				echo(sfa_create_autoforum_select($sfoptions['sfautoforum']));
				sfa_paint_select_end();

				foreach($sfoptions['posttypes'] as $key=>$value)
				{
					sfa_paint_checkbox(sprintf(__("Use Linking on Type: %s", "sforum"), '<strong>'.$key.'</strong>'), "posttype_".$key, $value);
				}

				sfa_paint_checkbox(__("Set Post-Edit Updating 'On' by Default", "sforum"), "sfautoupdate", $sfoptions['sfautoupdate']);
				$values = array(__('Entire Post Content', 'sforum'), __('Excerpt From Post Content', 'sforum'), __('WP Post Excerpt Field', 'sforum'));
				sfa_paint_radiogroup(__("Post Linking Type", "sforum"), 'sflinkexcerpt', $values, $sfoptions['sflinkexcerpt'], false, true);
				sfa_paint_input(__("Use Excerpt - How many Words", "sforum"), "sflinkwords", $sfoptions['sflinkwords'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Show Topic Posts as Comments", "sforum"), true, 'show-as-comments');
				$values = array(__('Do Not Add to Comments', 'sforum'), __('Display Mixed in Standard Comment Block', 'sforum'), __('Display in Separate Comment Block', 'sforum'));
				sfa_paint_radiogroup(__("Add Topic Posts to Blog Post Comments", "sforum"), 'sflinkcomments', $values, $sfoptions['sflinkcomments'], false, true);
				sfa_paint_checkbox(__("If Creating Posts from Blog Comments - Hide Duplicates", "sforum"), "sfhideduplicate", $sfoptions['sfhideduplicate']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Create Topic Posts from Comments", "sforum"), true, 'posts-from-comments');
				echo('<tr><td colspan="2"><div class="sfoptionerror">'.__('Note: Topic posts from comments will only be created upon comment approval', 'sforum').'</div></td></tr>');
				sfa_paint_checkbox(__("Create New Topic Posts from Blog Post Comments", "sforum"), "sfpostcomment", $sfoptions['sfpostcomment']);
				sfa_paint_checkbox(__("Delete Original Comment Upon Topic Post Creation", "sforum"), "sfkillcomment", $sfoptions['sfkillcomment']);
				sfa_paint_checkbox(__("Update Topic Post on Comment Edit or Delete", "sforum"), "sfeditcomment", $sfoptions['sfeditcomment']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Link Display Text", "sforum"), true, 'link-text-display');
				sfa_paint_checkbox(__("Create Blog Post to Topic Link Automatically", "sforum"), "sfuseautolabel", $sfoptions['sfuseautolabel']);
				sfa_paint_checkbox(__("Display Blog Post Link Above Post Content", "sforum"), "sflinkabove", $sfoptions['sflinkabove']);
				sfa_paint_checkbox(__("Show Post/Forum Link on Single Pages Only", "sforum"), "sflinksingle", $sfoptions['sflinksingle']);
				$submessage=sprintf(__("Text can include HTML, class name and the optional placeholders %s", "sforum"), ":<br />%ICON%, %FORUMNAME%, %TOPICNAME%, %POSTCOUNT%, %LINKSTART% and %LINKEND%");
				sfa_paint_wide_textarea(__("Blog Post - Link Text to Display", "sforum"), "sflinkblogtext", sf_filter_text_edit($sfoptions['sflinkblogtext']), $submessage);
				$submessage=sprintf(__("Text can include HTML, class name and the optional placeholders %s", "sforum"), ":<br />%ICON%, %BLOGTITLE%, %LINKSTART% and %LINKEND%");
				sfa_paint_wide_textarea(__("Forum Post - Link Text to Display", "sforum"), "sflinkforumtext", sf_filter_text_edit($sfoptions['sflinkforumtext']), $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Blog Linking Canonical URLs", "sforum"), true, 'link-urls');
				$values = array(__('Blog Post and Linked Topic have their own Canonical URL', 'sforum'), __('Point Blog Post to Linked Topic', 'sforum'), __('Point Linked Topic to Blog Post', 'sforum'));
				sfa_paint_radiogroup(__("Canonical URL for Linked Posts/Topic", "sforum"), 'sflinkurls', $values, $sfoptions['sflinkurls'], false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Blog Link Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_create_autoforum_select($forumid)
{
	$space = '&nbsp;&nbsp;';
	$groups = sf_get_combined_groups_and_forums_bloglink();
	if($groups)
	{
		$out = '';

		foreach($groups as $group)
		{
			$out.= '<optgroup label="'.sf_create_name_extract(sf_filter_title_display($group['group_name'])).'">'."\n";
			if($group['forums'])
			{
				foreach($group['forums'] as $forum)
				{
					if(intval($forumid) == intval($forum['forum_id']))
					{
						$text = 'selected="selected" ';
					} else {
						$text = '';
					}
					$out.='<option '.$text.'value="'.$forum['forum_id'].'">'.$space.sf_create_name_extract(sf_filter_title_display($forum['forum_name'])).'</option>'."\n";
				}
			}
			$out.='</optgroup>';
		}
	}
	return $out;
}

?>