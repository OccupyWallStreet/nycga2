<?php
/*
Simple:Press
Admin Options Global Display Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_display_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfdisplayform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $wpdb;

	$sfoptions = sfa_get_display_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=display";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfdisplayform" name="sfdisplay">
	<?php echo(sfc_create_nonce('forum-adminform_display')); ?>
<?php

	sfa_paint_options_init();

#== GLOBAL Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Global Display Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum Page Title", "sforum"), true, 'forum-page-title');
				sfa_paint_checkbox(__("Remove Page Title Completely", "sforum"), "sfnotitle", $sfoptions['sfnotitle']);
				sfa_paint_input(__("Graphic Replacement URL", "sforum"), "sfbanner", $sfoptions['sfbanner'], false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Display Search Form", "sforum"), true, 'display-search-form');
				sfa_paint_checkbox(__("Display Search at Top", "sforum"), "sfsearchtop", $sfoptions['sfsearchtop']);
				sfa_paint_checkbox(__("Display Search at Bottom", "sforum"), "sfsearchbottom", $sfoptions['sfsearchbottom']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("QuickLinks Dropdowns", "sforum"), true, 'quicklinks-dropdowns');
				sfa_paint_checkbox(__("Display 'QuickLinks' at Top", "sforum"), "sfqltop", $sfoptions['sfqltop'], false, false);
				sfa_paint_checkbox(__("Display 'QuickLinks' at Bottom", "sforum"), "sfqlbottom", $sfoptions['sfqlbottom'], false, false);
				sfa_paint_input(__("Number of New Posts to show", "sforum"), "sfqlcount", $sfoptions['sfqlcount'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("PageLinks Strip", "sforum"), true, 'pagelinks');
				sfa_paint_checkbox(__("Display 'PageLinks' at Top (False setting removes entire strip)", "sforum"), "sfptop", $sfoptions['sfptop'], false, false);
				sfa_paint_checkbox(__("Display 'PageLinks' at Bottom (False setting removes entire strip)", "sforum"), "sfpbottom", $sfoptions['sfpbottom'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("All First and Last Post Displays", "sforum"), true, 'first-and-last-post-display');
				sfa_paint_checkbox(__("Display Post Date", "sforum"), "fldate", $sfoptions['fldate']);
				sfa_paint_checkbox(__("Display Post Time", "sforum"), "fltime", $sfoptions['fltime']);
				sfa_paint_checkbox(__("Display User Who Posted", "sforum"), "fluser", $sfoptions['fluser']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Breadcrumb Links", "sforum"), true, 'breadcrumb-home-link');
				sfa_paint_checkbox(__("Display Breadcrumbs", "sforum"), "sfshowbreadcrumbs", $sfoptions['sfshowbreadcrumbs'], false, true);
				sfa_paint_checkbox(__("Display As Tree Style", "sforum"), "tree", $sfoptions['tree'], false, true);
				sfa_paint_checkbox(__("Display Home Link", "sforum"), "sfshowhome", $sfoptions['sfshowhome'], false, true);
				sfa_paint_input(__("Home", "sforum"), "sfhome", $sfoptions['sfhome'], false, false);
				sfa_paint_checkbox(__("Display Group Name in Breadcrumbs", "sforum"), "sfshowgroup", $sfoptions['sfshowgroup'], false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Members Unread Post Count", "sforum"), true, 'unread-posts');
				sfa_paint_checkbox(__("Display Member's Unread Post Count", "sforum"), "sfunread", $sfoptions['sfunread'], false, false);
				sfa_paint_checkbox(__("Display 'Mark All As Read' Icon", "sforum"), "sfmarkall", $sfoptions['sfmarkall'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Display Forum About Box", "sforum"), true, 'display-forum-statistics');
				sfa_paint_checkbox(__("Display Forum About Box", "sforum"), "sfstats", $sfoptions['sfstats']);
				sfa_paint_checkbox(__("Display Most Ever Online", "sforum"), "mostusers", $sfoptions['mostusers']);
				sfa_paint_checkbox(__("Display Who Is Online", "sforum"), "online", $sfoptions['online']);
				sfa_paint_checkbox(__("Display Forum Statistics", "sforum"), "forumstats", $sfoptions['forumstats']);
				sfa_paint_checkbox(__("Display Member Statistics", "sforum"), "memberstats", $sfoptions['memberstats']);
				sfa_paint_checkbox(__("Display Top Posters", "sforum"), "topposters", $sfoptions['topposters']);
				sfa_paint_input(__("Display How Many Top Posters", "sforum"), "showtopcount", $sfoptions['showtopcount'], false, false);
				sfa_paint_checkbox(__("Display New User List:", "sforum"), "newusers", $sfoptions['newusers']);
				sfa_paint_input(__("Display How Many New Users", "sforum"), "shownewcount", $sfoptions['shownewcount'], false, false);
				sfa_paint_checkbox(__("Display Admins and Moderators", "sforum"), "admins", $sfoptions['admins']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Display Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>