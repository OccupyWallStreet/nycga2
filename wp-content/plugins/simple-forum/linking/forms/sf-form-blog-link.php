<?php
/*
Simple:Press
Blog Linking - blog post form
$LastChangedDate: 2011-03-05 07:42:11 -0700 (Sat, 05 Mar 2011) $
$Rev: 5631 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_blog_link_form()
#
# Filter call
# Sets up the forum post linking form in the Post/Page Write screen
# ------------------------------------------------------------------
function sf_blog_link_form()
{
	global $current_user;

	if(function_exists('add_meta_box'))
	{
		sf_initialise_globals();

		# can the user do this?
		if (!$current_user->sflinkuse) return;

		$sflinkposttype = array();
		$sflinkposttype = sf_get_option('sflinkposttype');

		if($sflinkposttype)
		{
			foreach($sflinkposttype as $key=>$value)
			{
				if($value == true)
				{
					add_meta_box('sfforumlink', esc_attr(__("Link To Forum", "sforum")), 'sf_populate_post_form', $key, 'advanced');
				}
			}
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_populate_post_form()
#
# Callback functin to display form in blog post/page panels
# ------------------------------------------------------------------
function sf_populate_post_form()
{
	global $post;

	$forumid = 0;
	$checked = 'checked="checked"';
	$linkchecked = '';
	$editchecked = '';

	$sfpostlinking = sf_get_option('sfpostlinking');

	if(isset($post->ID))
	{
		$links = sf_blog_links_control('read', $post->ID);
		if($links)
		{
			$linkchecked = $checked;
			if($links->syncedit || $sfpostlinking['sfautoupdate']) $editchecked=$checked;
			$forumid = $links->forum_id;
		} else {
			if($sfpostlinking['sfautocreate'])
			{
				$linkchecked = 'checked="checked"';
				$forumid = $sfpostlinking['sfautoforum'];
				if($sfpostlinking['sfautoupdate']) $editchecked=$checked;
			}
		}
	}

	echo '<div id="spf-linking">';
	if(!$links)
	{
		# No current link or new
		?>
		<p><label for="sflink" class="selectit">
		<input type="checkbox" <?php echo($linkchecked); ?> name="sflink" id="sflink" />
		<?php _e("Create forum topic", "sforum"); ?></label><br /><br />

		<label for="sfforum" class="selectit"><?php esc_attr_e("Select forum:", "sforum"); ?><br />
		<?php echo(sf_blog_links_list($forumid)).'</label>';

	} else {
		# existing link
        $ahahURL = SFHOMEURL."index.php?sf_ahah=linking&action=breaklink&postid=".$post->ID;
		$target = 'spf-linking';
		$confirm = esc_js(__("Are you sure you want to break this link?", "sforum"));
		echo '<p>'.sprintf(__("This post is linked to the forum %s", "sforum"), '<br /><b>'.sf_get_forum_name_from_id($links->forum_id)).'</b><br /><br />';
		echo '<a target="_blank" class="button" href="'.sf_build_url(sf_get_forum_slug($links->forum_id), sf_get_topic_slug($links->topic_id), 1, 0).'">'.__("View Topic in Forum", "sforum").'</a>&nbsp;';
		echo '<a class="button" href="javascript:void(0);" onclick="javascript: if(confirm(\''.$confirm.'\')) {sfjBreakBlogLink(\''.$ahahURL.'\', \''.$target.'\');}">'.__("Break Forum Link", "sforum").'</a>';
	}
	?>
	<br /><br />
	<label for="sfedit" class="selectit">
	<input type="checkbox" <?php echo($editchecked); ?> name="sfedit" id="sfedit" />
	<?php _e("Update forum topic with subsequent edits", "sforum"); ?></label><br /><br />
	<?php
	echo '</p>';
	echo '</div>';
?>
<script type="text/javascript">
function sfjBreakBlogLink(ahahurl, target)
{
	jQuery(document).ready(function() {
		jQuery('#'+<?php echo(target); ?>).load(<?php echo(ahahurl); ?>);
	});
}
</script>
<?php

	return;
}

# ------------------------------------------------------------------
# sf_blog_links_list()
#
# Support Routine
# Lists forums for the post write link box
#	$forumid		ID of the forum if already linked (Edit mode)
# ------------------------------------------------------------------
function sf_blog_links_list($forumid)
{
	$space = '&nbsp;&nbsp;';
	$groups = sf_get_combined_groups_and_forums_bloglink();
	if($groups)
	{
		$out = '';
		$out.= '<select id="sfforum" name="sfforum">'."\n";

		foreach($groups as $group)
		{
			$out.= '<optgroup label="'.sf_create_name_extract(sf_filter_title_display($group['group_name'])).'">'."\n";
			if($group['forums'])
			{
				foreach($group['forums'] as $forum)
				{
					if($forumid == $forum['forum_id'])
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
		$out.='</select>'."\n";
	}
	return $out;
}

?>