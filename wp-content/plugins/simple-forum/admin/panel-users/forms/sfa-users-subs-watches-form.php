<?php
/*
Simple:Press
Admin Users Watches Subs Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_users_subs_watches_form()
{
    $site = SFHOMEURL."index.php?sf_ahah=subswatches&action=swlist&amp;page=1";
	$gif = SFADMINIMAGES."working.gif";
	echo '<form action="'.SFADMINUSER.'" method="post" name="sfwatchessubs" id="sfwatchessubs" onsubmit="return sfjshowSubsList(this, \''.$site.'\', \''.$gif.'\');" >';
		sfa_paint_options_init();
		sfa_paint_open_tab(__("Users", "sforum")." - ".__("Subscriptions and Watches", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Subscriptions and Watches", "sforum"), 'true', 'users-watches-subs', false);
					sfa_paint_open_panel();
						echo '<tr><td><fieldset style="border:1px solid #3b446e;padding:10px;">';
						echo '<legend style="border:none;">'.__("Select Format and Filters", "sforum").'</legend>';
						echo "<table class='form-table' width='100%'>\n";
							$subs = isset($_POST['showsubs']);
							$watches = isset($_POST['showwatches']);
							if (!$subs && !$watches) # when form not submitted, default both to checked
							{
								$subs = true;
								$watches = true;
							}
							sfa_paint_checkbox(__("Show Subscriptions", "sforum"), "showsubs", $subs, false, true);
							sfa_paint_checkbox(__("Show Watches", "sforum"), "showwatches", $watches, false, true);
							echo "<tr valign='top'>";
							echo "<td width='30%' class='sflabel'>";
							echo __("Filter by All, Groups or Forums", "sforum");
							echo ":</td>";
							echo "<td>";
							echo "<table width='100%'><tr>";
							echo "<td width='125' class='sflabel'>";
							if (isset($_POST['watchessubsfilter'])) $filter = $_POST['watchessubsfilter'];
							if (!isset($filter)) $filter = 'All';
							$check = '';
							if ($filter == 'All') $check = " checked='checked'";
							echo "<input type='radio' id='sffilterall' name='watchessubsfilter' value='All'".$check." />";
							echo "<label class='sfradio' for='sffilterall'>&nbsp;".__('All', 'sforum')."</label><br />";
							$check = '';
							if ($filter == 'Groups') $check = " checked='checked'";
                            $site = SFHOMEURL."index.php?sf_ahah=user&action=display-groups";
							$gif = SFADMINIMAGES."working.gif";
							$string =__("Show Groups", "sforum");
							echo '<input type="radio" id="sffiltergroups" name="watchessubsfilter" value="Groups"'.$check.' onchange="sfjshowGroupList(\''.$site.'\', \''.$gif.'\');" />';
							echo "<label class='sfradio' for='sffiltergroups'>&nbsp;".__('Groups', 'sforum')."</label><br />";
							$check = '';
							if ($filter == 'Forums') $check = " checked='checked'";
                            $site = SFHOMEURL."index.php?sf_ahah=user&action=display-forums";
							$gif = SFADMINIMAGES."working.gif";
							$string =__("Show Forums", "sforum");
							echo '<input type="radio" id="sffilterforums" name="watchessubsfilter" value="Forums"'.$check.' onchange="sfjshowForumList(\''.$site.'\', \''.$gif.'\');" />';
							echo "<label class='sfradio' for='sffilterforums'>&nbsp;".__('Forums', 'sforum')."</label>";
							echo "</td>";
							echo "<td align='left'>";
							echo '<div id="select-group"  class="inline_edit" style="margin: 3px; padding: 2px;">';
							echo '<p>'.__("Select Groups", "sforum").'</p>';
							echo '<div id="selectgroup"></div>';
							echo '<div class="clearboth"></div>';
							echo '</div>';
							echo "</td>";
							echo "<td align='left'>";
							echo '<div id="select-forum"  class="inline_edit" style="margin: 3px; padding: 2px;">';
							echo '<p>'.__("Select Forums", "sforum").'</p>';
							echo '<div id="selectforum"></div>';
							echo '<div class="clearboth"></div>';
							echo '</div>';
							echo "</td>";
							echo "</tr></table>";
							echo "</td>";
							echo "</tr>";
						sfa_paint_close_fieldset();
					sfa_paint_close_panel();

				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();

        $site = SFHOMEURL."index.php?sf_ahah=subswatches&action=swlist&amp;page=1";
		$gif = SFADMINIMAGES."working.gif";
	?>
		<input type="hidden" class="sfhiddeninput" name="watchessubs" value="submit" />
		<div class="sfform-submit-bar">
			<input type="button" class="sfform-panel-button" value="<?php esc_attr_e(__("Show Watches and Subscriptions", "sforum")); ?>" onclick="sfjshowSubsList('sfwatchessubs', '<?php echo $site; ?>', '<?php echo $gif; ?>');" />
		</div>
		<div class="sfform-panel-spacer"></div>
		<div id="subsdisplayspot"></div>
	</form>
<?php

	return;
}

?>