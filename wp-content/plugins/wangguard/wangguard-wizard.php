<?php
//WangGuard Wizard
function wangguard_wizard() {
	global $wpdb,$wangguard_nonce, $wangguard_api_key;

	if (wangguard_is_multisite()) {
		$spamFieldName = "spam";
		$sqlSpamWhere = "spam = 1";
		$sqlNoSpamWhere = "spam = 0";
	}
	else {
		$spamFieldName = "user_status";
		$sqlSpamWhere = "user_status = 1";
		$sqlNoSpamWhere = "user_status <> 1";
	}

	
	
	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'wangguard'));
	
	
	if ( isset($_POST['submit']) ) {
		check_admin_referer( $wangguard_nonce );
	} 	
	
	
	$step = (int)$_REQUEST['wangguard_step'];
	?>

<div class="wrap" id="wangguard-wizard-cont">
	<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/wizard.png" alt="<?php echo htmlentities(__('WangGuard Wizard', 'wangguard')) ?>" /></div>
	<div class="icon32" id="icon-wangguard"><br></div>
	<h2><?php _e('WangGuard Wizard', 'wangguard'); ?></h2>
	
	<script type="text/javascript">
	function wangguard_progress() {
		jQuery("#wangguard-visible-step-status").hide();
		jQuery("#wangguard-hidden-step-status").show();
		return true;
	}

	jQuery(document).ready(function() {
		jQuery(".wangguard-hidewhendone").hide();
	});
	</script>



	<form action="admin.php" method="get" id="wangguardWizardForm" name="wangguardWizardForm" onsubmit="return wangguard_progress()">
		<input type="hidden" name="page" value="wangguard_wizard" />

		<?php
		switch ($step) {
			case "1":
				?>
				<div id="wangguard-visible-step-status">
					<h3><?php echo __( "Reporting spam users to WangGuard..." , "wangguard"); ?></h3>
					<?php
					$usersPerStint = 50;	//how many users to check on each iteration

					$fromUser = (int)$_REQUEST['wangguard_wiz_from'];
					if ($fromUser<0) $fromUser = 0;

					$spamUsersTotal = $wpdb->get_col("select count(*) from $wpdb->users where $sqlSpamWhere");
					$spamUsersTotal = $spamUsersTotal[0];

					$step1Finished = ($fromUser>0) && ($fromUser >= $spamUsersTotal);

					if (!$step1Finished) {
						$spamUsers = $wpdb->get_col("select ID from $wpdb->users where $sqlSpamWhere order by ID LIMIT $fromUser , $usersPerStint");
						$userCount = count($spamUsers);

						$reportingUserFrom = $fromUser + $usersPerStint;
						$reportingUserFrom = ($reportingUserFrom > $spamUsersTotal) ? $spamUsersTotal : $reportingUserFrom;

						if ($userCount == 0) {?>
							<p><?php echo __( "No spam users were found on your site. Click the button below to check your users." , "wangguard") ?></p>
							<input type="hidden" name="wangguard_step" value="2" />
							<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Continue', 'wangguard'); ?>" /></p>
							<?php
						}
						else {?>
							<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /><?php echo sprintf(__("The WangGuard wizard is reporting %d of %d spam users as Sploggers.", "wangguard") , $reportingUserFrom , $spamUsersTotal); ?></p>
							<?php flush(); ?>
							<?php ob_flush(); ?>

							<?php 
							$progress = 0;
							$reported = 0;
							$lastProgressSent = 0;
							foreach ($spamUsers as $userid) {

								//get the WangGuard user status, if status is force-checked then ignore the user
								$table_name = $wpdb->base_prefix . "wangguarduserstatus";
								$user_status = $wpdb->get_var( $wpdb->prepare("select user_status from $table_name where ID = %d" , $userid));
								if ($user_status == 'force-checked')
									continue;

								$dummyArr = array();
								$dummyArr[] = $userid;

								set_time_limit(15);
								wangguard_report_users($dummyArr , "email" , false);

								$reported++;
							}
							?>

							<input type="hidden" name="wangguard_wiz_from" value="<?php echo $fromUser + $usersPerStint?>" />
							<script type="text/javascript">
								document.getElementById('wangguardWizardForm').onsubmit='';
								jQuery(document).ready(function() {
									location.href='admin.php?page=wangguard_wizard&wangguard_step=1&wangguard_wiz_from=<?php echo $fromUser + $usersPerStint?>';
								});
							</script>
							<input type="hidden" name="wangguard_step" value="1" />

						<?php }	?>

					<?php }

					else {?>
						<p><?php echo __( "The WangGuard wizard has finished reporting spam users. Click the button below to check the rest of your users." , "wangguard") ?></p>
						<input type="hidden" name="wangguard_step" value="2" />
						<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Continue', 'wangguard'); ?>" /></p>
					<?php }	?>

				</div>

				<?php if ($step1Finished) {?>
				<div id="wangguard-hidden-step-status" style="display: none">
					<h3><?php echo __( "Verifying users against the WangGuard service..." , "wangguard"); ?></h3>
					<?php
					$goodUsers = $wpdb->get_col("select ID from $wpdb->users where $sqlNoSpamWhere");
					$userCount = count($goodUsers);
					if ($userCount == 0) {?>
						<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /></p>
					<?php
					}else {?>
						<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /><?php echo sprintf(__("The WangGuard wizard is verifying %d users against the WangGuard service.", "wangguard") , $userCount); ?></p>
					<?php
					}
					?>
				</div>
				<?php }?>

				<?php
				break;








			case "2":
				?>
				<div id="wangguard-visible-step-status">
					<h3><?php echo __( "Verifying users against the WangGuard service..." , "wangguard"); ?></h3>
					<?php
					$usersPerStint = 50;	//how many users to check on each iteration

					$fromUser = (int)$_REQUEST['wangguard_wiz_from'];
					if ($fromUser<0) $fromUser = 0;

					$goodUsersTotal = $wpdb->get_col("select count(*) from $wpdb->users where $sqlNoSpamWhere");
					$goodUsersTotal = $goodUsersTotal[0];

					$step2Finished = ($fromUser>0) && ($fromUser >= $goodUsersTotal);

					$reported = (int) $_REQUEST['reported'];

					$noUsersToCheck = false;

					if (!$step2Finished) {
						$goodUsers = $wpdb->get_col("select ID from $wpdb->users where $sqlNoSpamWhere ORDER BY ID LIMIT $fromUser , $usersPerStint");
						$userCount = count($goodUsers);

						$reportingUserFrom = $fromUser + $usersPerStint;
						$reportingUserFrom = ($reportingUserFrom > $goodUsersTotal) ? $goodUsersTotal : $reportingUserFrom;

						if ($userCount == 0) {
							$step2Finished = true;
							$noUsersToCheck = true;
							?>
							<p><?php echo __( "No users were found on your site." , "wangguard") ?></p>
							<?php
						}
						else {?>
							<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /><?php echo sprintf(__("The WangGuard wizard is verifying %d of %d users against the WangGuard service.", "wangguard") , $reportingUserFrom , $goodUsersTotal); ?></p>
							<?php flush(); ?>
							<?php ob_flush(); ?>

							<?php 
							$progress = 0;
							$verified = 0;
							$lastProgressSent = 0;
							foreach ($goodUsers as $userid) {


								//get the WangGuard user status, if status is force-checked then ignore the user
								$table_name = $wpdb->base_prefix . "wangguarduserstatus";
								$user_status = $wpdb->get_var( $wpdb->prepare("select user_status from $table_name where ID = %d" , $userid));
								if ($user_status == 'force-checked')
									continue;


								$dummyArr = array();
								$dummyArr[] = $userid;
								$user_object = new WP_User($userid);

								set_time_limit(15);
								$user_check_status = wangguard_verify_user($user_object);

								if ($user_check_status == "reported") {
									$reported++;
									if (function_exists("update_user_status"))
										update_user_status($userid, $spamFieldName, 1);	//when flagging the user as spam, the wangguard hook is called to report the user
									else
										$wpdb->query( $wpdb->prepare("udpate $wpdb->users set $spamFieldName = 1 where ID = %d" , $userid ) );
								}

								$verified++;
							}
							?>
							<input type="hidden" name="wangguard_wiz_from" value="<?php echo $fromUser + $usersPerStint?>" />
							<script type="text/javascript">
								document.getElementById('wangguardWizardForm').onsubmit='';
								jQuery(document).ready(function() {
									location.href='admin.php?page=wangguard_wizard&wangguard_step=2&reported=<?php echo $reported;?>&wangguard_wiz_from=<?php echo $fromUser + $usersPerStint?>';
								});
							</script>
							<input type="hidden" name="wangguard_step" value="2" />


						<?php }

					}

					if ($step2Finished) {
						$table_name = $wpdb->base_prefix . "wangguarduserstatus";
						$reportedUsers = $wpdb->get_col("select count(*) from $table_name where user_status = 'reported'");
						$reportedUsersCount = $reportedUsers[0];

						if (!$noUsersToCheck) {?>
						<p><?php echo sprintf(__( "The WangGuard wizard has finished verifying your users and found <strong>%d</strong> Sploggers." , "wangguard") , $reported) ?></p>
						<?php }?>

						<input type="hidden" name="wangguard_step" value="3" />
						<input type="hidden" name="wangguard_splogcnt" value="<?php echo $reportedUsersCount ?>" />
						<p>&nbsp;</p>
						<h3><?php echo __("Please tell WangGuard wizard what to do with the garbage and click the Finish button", "wangguard"); ?></h3>

						<div id="wangguard-visible-step-status">
							<p><input type="checkbox" value="1" name="wangguard_delete_splogguers" id="wangguard_delete_splogguers" /> <label for="wangguard_delete_splogguers"><?php echo __( "Delete the users marked as Sploggers from my site." , "wangguard") ?></label</p>
							<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Finish', 'wangguard'); ?>" /></p>
						</div>

						<div id="wangguard-hidden-step-status" style="display: none">
							<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /></p>
						</div>
					<?php }	?>
				</div>



				<?php 
				break;









			case "3":

				if ($_REQUEST['wangguard_delete_splogguers'] == 1) { 
					$usersPerStint = 10;	//how many users to check on each iteration

					$table_name = $wpdb->base_prefix . "wangguarduserstatus";
					$reportedUsers = $wpdb->get_col("select ID from $table_name where user_status = 'reported' LIMIT 0 , $usersPerStint");
					$reportedUsersCount = count($reportedUsers);

					$reportedUsersTotal = (int)$_REQUEST['wangguard_splogcnt'];
					$reportingUserFrom = (int)$_REQUEST['wangguard_wiz_from'];
					$reportingUserFrom = ($reportingUserFrom > $reportedUsersTotal) ? $reportedUsersTotal : $reportingUserFrom;

					$step3Finished = ($reportedUsersCount==0);


					if (!$step3Finished) {
						?>
						<h3><?php echo __( "Deleting Splogguers from your site..." , "wangguard"); ?></h3>
						<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /><?php echo sprintf(__("The WangGuard wizard is deleting %d of %d Splogguers from your site.", "wangguard") , $reportingUserFrom , $reportedUsersTotal); ?></p>
						<?php flush(); ?>
						<?php ob_flush(); ?>
						<?php

						foreach ($reportedUsers as $userid) {

							set_time_limit(15);

							if ((function_exists("get_blogs_of_user")) && (method_exists ($wpdb , 'get_blog_prefix'))) {
								$blogs = get_blogs_of_user( $userid, true );
								if (is_array($blogs))
									foreach ( (array) $blogs as $key => $details ) {

										$isMainBlog = false;
										if (isset ($current_site)) {
											$isMainBlog = ($details->userblog_id != $current_site->blog_id); // main blog not a spam !
										}
										elseif (defined("BP_ROOT_BLOG")) {
											$isMainBlog = ( 1 == $details->userblog_id || BP_ROOT_BLOG == $details->userblog_id );
										}
										else
											$isMainBlog = ($details->userblog_id == 1);

										$userIsAuthor = false;
										if (!$isMainBlog) {
											//Only works on WP 3+
											$blog_prefix = $wpdb->get_blog_prefix( $details->userblog_id );
											$authorcaps = $wpdb->get_var( sprintf("SELECT meta_value as caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = %d and u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities'" , $spuserID ));

											$caps = maybe_unserialize( $authorcaps );
											$userIsAuthor = ( !isset( $caps['subscriber'] ) && !isset( $caps['contributor'] ) );
										}

										//Update blog to spam if the user is the author and its not the main blog
										if ((!$isMainBlog) && $userIsAuthor) {
											@update_blog_status( $details->userblog_id, 'spam', '1' );

											//remove blog from queue
											$table_name = $wpdb->base_prefix . "wangguardreportqueue";
											$wpdb->query( $wpdb->prepare("delete from $table_name where blog_id = '%d'" , $details->userblog_id ) );
										}
									}
							}


							if (wangguard_is_multisite () && function_exists("wpmu_delete_user"))
								wpmu_delete_user($userid);
							else
								wp_delete_user($userid);
						}

						?>
						<script type="text/javascript">
							document.getElementById('wangguardWizardForm').onsubmit='';
							jQuery(document).ready(function() {
								location.href='admin.php?page=wangguard_wizard&wangguard_step=3&wangguard_delete_splogguers=1&wangguard_splogcnt=<?php echo $reportedUsersTotal;?>&wangguard_wiz_from=<?php echo $reportingUserFrom + $usersPerStint?>';
							});
						</script>
						<?php
					}
					else {
					?>
						<h3><?php echo __( "The WangGuard Wizard has finished" , "wangguard") ?></h3>
						<p><?php echo sprintf(__("%d sploggers users has been deleted from your site.", "wangguard") , $reportedUsersTotal); ?></p>
				<?php }
				}

				else {?>

					<h3><?php echo __( "The WangGuard Wizard has finished" , "wangguard") ?></h3>

				<?php
				}
				break;




			default:
				?>
				<div id="wangguard-visible-step-status">
					<h3><?php echo __( "Welcome to the WangGuard Wizard" , "wangguard") ?></h3>
					<p><?php echo __( "This wizard will perform the following actions on your WordPress installation" , "wangguard") ?></p>
					<ol>
						<li><?php echo __( "It will report to WangGuard all users you have flagged as 'spam' on your site." , "wangguard") ?></li>
						<li><?php echo __( "For the rest of the users, it will check against WangGuard service if any of them was reported as Splogger." , "wangguard") ?></li>
						<li><?php echo __( "It will let you know how many Sploggers the wizard found (if any) and, optionally, will let you delete your spam users and Sploggers from your site." , "wangguard") ?></li>
					</ol>
					<p><?php echo sprintf( __( "Note: The wizard will NOT verify the users flagged as %s, these are the users for which you've selected the &quot;Not a Splogger&quot; option from the Users admin or flagged as &quot;Not Spam&quot;." , "wangguard") ,   "<span class='wangguard-status-checked'>".__("Checked (forced)" , "wangguard")."</span>"  ) ?></p>
					<?php
					$valid = wangguard_verify_key($wangguard_api_key);
					if (($valid == 'failed') || ($valid == 'invalid')) {
						?>
						<p class="wangguard-info wangguard-error" style="margin-right: 20px;"><?php echo __('Your WangGuard API KEY is invalid.', 'wangguard'); ?></p>
						<?php
					}
					else {
						?>
						<p><?php echo __( "Click the button below when you're ready to clean your site!." , "wangguard") ?></p>
						<input type="hidden" name="wangguard_step" value="1" />
						<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Start cleaning my site!', 'wangguard'); ?>" /></p>
						<?php
					}
					?>
				</div>


				<div id="wangguard-hidden-step-status" style="display: none">
					<h3><?php echo __( "Reporting spam users to WangGuard..." , "wangguard"); ?></h3>
					<?php
					$spamUsers = $wpdb->get_col("select ID from $wpdb->users where $sqlSpamWhere");
					$userCount = count($spamUsers);
					if ($userCount == 0) {?>
						<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /></p>
					<?php
					}else {?>
						<p><img id="wangguard-progress-wait" style="vertical-align: middle; margin-right: 8px;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="..." /><?php echo sprintf(__("The WangGuard wizard is reporting %d spam users as Sploggers.", "wangguard") , $userCount); ?></p>
					<?php
					}
					?>
				</div>

				<?php
				break;
		}
		?>

	</form>
</div>
<?php
}
?>