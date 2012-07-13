<?php
/*
Simple:Press
Admin Users Show Spam Registration Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_users_show_spam_registrations_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfspamreg').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadsr').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $wpdb;

    $ahahURL = SFHOMEURL."index.php?sf_ahah=users-loader&amp;saveform=killspamreg";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfspamreg" name="sfspamreg">
	<?php echo(sfc_create_nonce('forum-adminform_spamkill')); ?>
<?php
	sfa_paint_options_init();

		sfa_paint_open_tab(__("Users", "sforum")." - ".__("Spam Registrations", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Current Spam Registrations", "sforum"), false, '', false);
                    $ahahURL = SFHOMEURL."index.php?sf_ahah=users-loader&amp;saveform=killspamreg";
					$target = "sfmsgspot";
					$wait = SFADMINIMAGES.'waitbox.gif';
?>
						<table style="width:auto;" align="center" class="sfsubtable" cellpadding="0" cellspacing="0">
							<tr>
								<th align="center" scope="col" style="padding:5px 5px;"><?php _e("User ID", "sforum"); ?></th>
								<th style="padding:5px 5px;"><?php _e("User Name", "sforum"); ?></th>
								<th align="center" style="padding:5px 5px;"><?php _e("Delete", "sforum"); ?></th>
								<th align="center" width="30"></th>
								<th align="center" scope="col" style="padding:5px 5px;"><?php _e("User ID", "sforum"); ?></th>
								<th style="padding:5px 5px;"><?php _e("User Name", "sforum"); ?></th>
								<th align="center" style="padding:5px 5px;"><?php _e("Delete", "sforum"); ?></th>
								<th align="center" width="30"></th>
								<th align="center" scope="col" style="padding:5px 5px;"><?php _e("User ID", "sforum"); ?></th>
								<th style="padding:5px 5px;"><?php _e("User Name", "sforum"); ?></th>
								<th align="center" style="padding:5px 5px;"><?php _e("Delete", "sforum"); ?></th>
							</tr>
	<?php
							$numspam = 0;

							# first out select users registered more than X days ago
							$registrations = $wpdb->get_results("SELECT ID, user_registered FROM ".SFUSERS." WHERE user_registered < DATE_SUB(CURDATE(), INTERVAL 5 DAY);");
							if ($registrations)
							{
								$curcol = 0;
								# second select all users who have never posted to the forum
								$badusers = $wpdb->get_results("SELECT user_id, display_name FROM ".SFMEMBERS." WHERE posts = 0 ORDER BY display_name;");
								if ($badusers)
								{
									$candelete = false;
									$found = false;

									foreach ($badusers as $baduser)
									{
										# OK so they have never posted but are they in the old registrations list?
										foreach ($registrations as $registration)
										{
											if ($baduser->user_id == $registration->ID)
											{
												$found = true;
												$candelete = true;
											}
										}
										# if they were then have they ever authored a post?
										if ($found)
										{
											$found = $wpdb->get_results("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_author = ".$baduser->user_id);
											if ($found)
											{
												$candelete = false;
											} else {
												# if no - what about left a comment?
												$found = $wpdb->get_results("SELECT comment_id FROM ".$wpdb->prefix."comments WHERE user_id = ".$baduser->user_id);
												if ($found)
												{
													$candelete = false;
												}
											}
										}
										# so? can we delete them?
										if ($candelete)
										{
											# do NOT remove an admin that does not post
											if (!sf_is_forum_admin($baduser->user_id))
											{
												if ($curcol == 0)
												{
													echo '<tr>';
												}
?>
												<td align="center"><?php echo($baduser->user_id); ?></td>
												<td><?php echo(sf_filter_name_display($baduser->display_name)); ?></td>
												<td align="center">
												<label for="sfkill-<?php echo($baduser->user_id); ?>"></label>
												<input type="checkbox" name="kill[<?php echo($baduser->user_id); ?>]" id="sfkill-<?php echo($baduser->user_id); ?>" checked="checked" />
												</td>
<?php
												$curcol++;
												if ($curcol == 3)
												{
													$curcol = 0;
													echo '</tr>';
												} else {
													echo '<td></td>';
												}
												$numspam++;
											}
										}
									}
								}
							}
						if ($curcol != 0)
						{
							if ($curcol == 1) echo '<td></td>';
							echo '<td></td></tr>';
						}
						echo '</table>';
						$checkcontainer = '#spam-reg';
						echo '<br />';
?>
						<table>
						<tr>
						<td>

						<input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Check All', 'sforum')); ?>" onclick="sfjcheckAll(jQuery('<?php echo($checkcontainer); ?>'))" />

						</td>
						<td />
						<td>

						<input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Uncheck All', 'sforum')); ?>" onclick="sfjuncheckAll(jQuery('<?php echo($checkcontainer); ?>'))" />

						</td>
						</tr>
						</table>
		<?php



						echo '<br /><br /><br />';
						echo('<strong>'.$numspam.__(" registered users eligable for removal", "sforum").'</strong><br /><br />');

				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();

						if ($numspam != 0)
						{
	?>
							<div class="sfform-submit-bar">
							<input type="submit" class="sfform-submit-button" id="sfspamreg" name="sfspamreg" value="<?php esc_attr_e(__('Remove Spam Registrations', 'sforum')); ?>" />
							<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#spam-reg').html('');" id="sfspamregcancel" name="sfspamregcancel" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
							</div>
	<?php
						}
?>
	</form>
	<div class="sfform-panel-spacer"></div>
<?php
}

?>