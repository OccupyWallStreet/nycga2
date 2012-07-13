<?php
/*
Simple:Press
Admin Users Spam Registration Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_users_spam_registrations_form()
{
	global $wpdb;

	sfa_paint_options_init();

		sfa_paint_open_tab(__("Users", "sforum")." - ".__("Spam Registrations", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Remove Spam Registrations", "sforum"), 'true', 'remove-spam-registrations', false);
?>
					<p class="subhead"><?php _e("This option should be used with great care! It will remove ALL user registrations that", "sforum") ?>:</strong></p>
					<ul>
						<li><?php _e("are now over 7 days old", "sforum") ?></li>
						<li><?php _e("where the user has never posted to the forum", "sforum") ?></li>
						<li><?php _e("where the user has never authored a post", "sforum") ?></li>
						<li><?php _e("where the user has never left a comment", "sforum") ?></li>
					</ul>
					<p><?php _e("Use at your own risk!", "sforum") ?></p>
<?php
                    $base = SFHOMEURL."index.php?sf_ahah=users-loader";
					$target = "spam-reg";
					$image = SFADMINIMAGES;
?>
					<input type="button" class="button button-highlighted" value="<?php echo esc_attr(__("Show Spam Registrations", "sforum")); ?>" onclick="sfjLoadForm('showspamreg', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '');" />

					<div class="sfform-panel-spacer"></div>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
	</form>

	<div class="sfform-panel-spacer"></div>
	<div id='spam-reg' class="sfinline-form" >
	</div>

<?php
}

?>