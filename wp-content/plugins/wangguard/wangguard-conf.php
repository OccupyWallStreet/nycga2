<?php
//Configuration page
function wangguard_conf() {
	global $wpdb;
	global $wangguard_nonce, $wangguard_api_key;

	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'wangguard'));

	$key_status = "";

	
	$selectedTab = 0;
	
	if ( isset($_POST['submit']) ) {
		check_admin_referer( $wangguard_nonce );
		$key = preg_replace( '/[^a-h0-9]/i', '', $_POST['key'] );

		if ( empty($key) ) {
			$key_status = 'empty';
			$ms[] = 'new_key_empty';
			wangguard_update_option('wangguard_api_key' , "");
		} else {
			$key_status = wangguard_verify_key( $key );
		}

		if ( $key_status == 'valid' ) {
			wangguard_update_option('wangguard_api_key', $key);
			$ms[] = 'new_key_valid';
		} else if ( $key_status == 'invalid' ) {
			$ms[] = 'new_key_invalid';
		} else if ( $key_status == 'failed' ) {
			$ms[] = 'new_key_failed';
		}
		
	} elseif ( isset($_POST['saveblockeddomain']) ) {
		echo "<div id='wangguard-warning' class='updated fade'><p><strong>".__('Blocked domains has been saved.', 'wangguard')."</strong></p></div>";

		$selectedDomains = array();
		if (is_array($_POST['domains'])) {
			foreach ($_POST['domains'] as $domain) {
				$selectedDomains[$domain] = true;
			}
		}
		
		wangguard_update_option('blocked-list-domains' , $selectedDomains) ;

		
		$selectedTab = 3;

	} elseif ( isset($_POST['check']) ) {

		wangguard_get_server_connectivity(0);
		$selectedTab = 4;

	} elseif ( isset($_POST['optssave']) ) {

			wangguard_update_option('wangguard-expertmode', $_POST['wangguardexpertmode']=='1' ? 1 : 0 );

			wangguard_update_option('wangguard-report-posts', $_POST['wangguardreportposts']=='1' ? 1 : 0 );

			wangguard_update_option('wangguard-delete-users-on-report', $_POST['wangguard-delete-users-on-report']=='1' ? 1 : -1 );
			
			wangguard_update_option('wangguard-enable-bp-report-btn', $_POST['wangguardenablebpreportbtn']=='1' ? 1 : -1 );
			
			wangguard_update_option('wangguard-enable-bp-report-blog', $_POST['wangguardenablebpreportblog']=='1' ? 1 : -1 );

			wangguard_update_option('wangguard-verify-gmail', $_POST['wangguard-verify-gmail']=='1' ? 1 : 0 );
			
			wangguard_update_option('wangguard-verify-dns-mx', $_POST['wangguard-verify-dns-mx']=='1' ? 1 : 0 );

			$selectedTab = 2;
			
			echo "<div id='wangguard-warning' class='updated fade'><p><strong>".__('WangGuard settings has been saved.', 'wangguard')."</strong></p></div>";

	}

	if ( $key_status != 'valid' ) {
		$key = wangguard_get_option('wangguard_api_key');
		if ( empty( $key ) ) {
			if ( $key_status != 'failed' ) {
				if ( wangguard_verify_key( '1234567890ab' ) == 'failed' )
					$ms[] = 'no_connection';
				else
					$ms[] = 'key_empty';
			}
			$key_status = 'empty';
		} else {
			$key_status = wangguard_verify_key( $key );
		}
		if ( $key_status == 'valid' ) {
			$ms[] = 'key_valid';
		} else if ( $key_status == 'invalid' ) {
			delete_option('wangguard_api_key');
			$ms[] = 'key_empty';
		} else if ( !empty($key) && $key_status == 'failed' ) {
			$ms[] = 'key_failed';
		}
	}


	$messages = array(
		'new_key_empty' => array('class' => 'wangguard-info', 'text' => __('Your key has been cleared.', 'wangguard')),
		'new_key_valid' => array('class' => 'wangguard-info wangguard-success', 'text' => __('Your key has been verified!', 'wangguard')),
		'new_key_invalid' => array('class' => 'wangguard-info wangguard-error', 'text' => __('The key you entered is invalid. Please double-check it.', 'wangguard')),
		'new_key_failed' => array('class' => 'wangguard-info wangguard-error', 'text' => __('The key you entered could not be verified because a connection to wangguard.com could not be established. Please check your server configuration.', 'wangguard')),
		'no_connection' => array('class' => 'wangguard-info wangguard-error', 'text' => __('There was a problem connecting to the WangGuard server. Please check your server configuration.', 'wangguard')),
		'key_empty' => array('class' => 'wangguard-info', 'text' => sprintf(__('Please enter an API key. (<a href="%s" style="color:#fff">Get your key here.</a>)', 'wangguard'), 'http://wangguard.com/getapikey')),
		'key_valid' => array('class' => 'wangguard-info wangguard-success', 'text' => __('This key is valid.', 'wangguard')),
		'key_failed' => array('class' => 'wangguard-info wangguard-error', 'text' => __('The key below was previously validated but a connection to wangguard.com can not be established at this time. Please check your server configuration.', 'wangguard')));

	
	wp_enqueue_script("jquery-ui-tabs");
	wp_print_scripts("jquery-ui-tabs");
?>


<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'wangguard') ?></strong></p></div>
<?php endif; ?>


<div class="wrap">
	<div class="icon32" id="icon-wangguard"><br></div>
	<h2><?php _e('WangGuard Configuration', 'wangguard'); ?></h2>
	<div class="">


		<div id="wangguard-conf-tabs">
			<ul id="wangguard-tabs">
				<li><a href="#wangguard-conf-apikeys"><?php _e('WangGuard API Key', 'wangguard'); ?></a></li>
				<li><a href="#wangguard-conf-questions"><?php _e('Security questions', 'wangguard'); ?></a></li>
				<li><a href="#wangguard-conf-settings"><?php _e('WangGuard settings', 'wangguard'); ?></a></li>
				<li><a href="#wangguard-conf-domains"><?php _e('Blocked domains', 'wangguard'); ?></a></li>
				<li><a href="#wangguard-conf-conectivity"><?php _e('Server Connectivity', 'wangguard'); ?></a></li>
			</ul>
			<div id="wangguard-tabs-container">
				

			<!--WANGGUARD API KEY-->
			<div id="wangguard-conf-apikeys">
					<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/apikey.png" alt="<?php echo htmlentities(__('WangGuard API Key', 'wangguard')) ?>" /></div>
				<form action="" method="post" id="wangguard-conf" style="margin: auto;">
					<p><?php printf(__('For many people, <a href="%1$s">WangGuard</a> will greatly reduce or even completely eliminate the Sploggers you get on your site. If one does happen to get through, simply mark it as Splogger on the Users screen. If you don\'t have an API key yet, <a href="%2$s" target="_new">get one here</a>.', 'wangguard'), 'http://wangguard.com/', 'http://wangguard.com/getapikey'); ?></p>

					<h3><label for="key"><?php _e('WangGuard API Key', 'wangguard'); ?></label></h3>
					<?php foreach ( $ms as $m ) : ?>
						<p class="<?php echo $messages[$m]['class']; ?>"><?php echo $messages[$m]['text']; ?></p>
					<?php endforeach; ?>
					<p><input id="key" name="key" type="text" size="35" maxlength="32" value="<?php echo wangguard_get_option('wangguard_api_key'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /> (<?php _e('<a href="http://wangguard.com/faq" target="_new">What is this?</a>', 'wangguard'); ?>)</p>

					<?php if ( $invalid_key ) { ?>
						<h3><?php _e('Why might my key be invalid?', 'wangguard'); ?></h3>
						<p><?php _e('This can mean one of two things, either you copied the key wrong or that the plugin is unable to reach the WangGuard servers, which is most often caused by an issue with your web host around firewalls or similar.', 'wangguard'); ?></p>
					<?php } ?>


					<?php wangguard_nonce_field($wangguard_nonce) ?>

					<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Update options &raquo;', 'wangguard'); ?>" /></p>
				</form>
			</div>



			<!--WANGGUARD QUESTIONS-->
			<div id="wangguard-conf-questions" style="margin: auto;">

				<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/security.png" alt="<?php echo htmlentities(__('Security questions', 'wangguard')) ?>" /></div>
				
				<h3><?php _e('Security questions', 'wangguard'); ?></h3>
				<p><?php _e('Security questions are randomly asked on the registration form to prevent automated signups.', 'wangguard')?></p>
				<p><?php _e('Security questions are optional, it\'s up to you whether to use them or not.', 'wangguard')?></p>
				<p><?php _e('Create you own security questions from the form below, or delete the questions you don\'t want anymore.', 'wangguard')?></p>
				<?php
				$table_name = $wpdb->base_prefix . "wangguardquestions";
				$wgquestRs = $wpdb->get_results("select * from $table_name order by id");

				if (!empty ($wgquestRs)) {
					?><h4><?php _e('Existing security questions', 'wangguard')?></h4><?php
				}
				foreach ($wgquestRs as $question) {?>
					<div class="wangguard-question" id="wangguard-question-<?php echo $question->id?>">
					<?php _e("Question", 'wangguard')?>: <strong><?php echo $question->Question?></strong><br/>
					<?php _e("Answer", 'wangguard')?>: <strong><?php echo $question->Answer?></strong><br/>
					<?php _e("Replied OK / Wrong", 'wangguard')?>: <strong><?php echo $question->RepliedOK?> / <?php echo $question->RepliedWRONG?></strong><br/>
					<a href="javascript:void(0)" rel="<?php echo $question->id?>" class="wangguard-delete-question"><?php _e('delete question', 'wangguard')?></a>
					</div>
				<?php } ?>
				<div id="wangguard-new-question-container">
				</div>

				<h4><?php _e('Add a new security question', 'wangguard')?></h4>
				<?php _e("Question", 'wangguard')?><br/><input type="text" name="wangguardnewquestion" id="wangguardnewquestion" class="wangguard-input" maxlength="255" value="" />
				<br/><br style="line-height: 5px"/>
				<?php _e("Answer", 'wangguard')?><br/><input type="text" name="wangguardnewquestionanswer" id="wangguardnewquestionanswer" class="wangguard-input" maxlength="50" value="" />
				<div id="wangguardnewquestionerror">
					<?php _e('Fill in both the question and the answer fields to create a new security question', 'wangguard')?>
				</div>
				<p class="submit"><input type="button" id="wangguardnewquestionbutton" class="button-primary" name="submit" value="<?php _e('Create question &raquo;', 'wangguard'); ?>" /></p>
			</div>




			<!--WANGGUARD SETTINGS-->
			<div id="wangguard-conf-settings" style="margin: auto;">
				
				<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/settings.png" alt="<?php echo htmlentities(__('WangGuard settings', 'wangguard')) ?>" /></div>
				
				<?php 
				$wangguard_edit_prefix = "";
				if (function_exists( 'is_network_admin' )) 
					if (is_network_admin())
						$wangguard_edit_prefix = "../";
				?>
				<form action="" method="post" id="wangguard-settings" style="margin:0px auto 0 auto; ">
					<h3><?php _e("WangGuard settings", 'wangguard') ?></h3>
					<p>
						<input type="checkbox" name="wangguardreportposts" id="wangguardreportposts" value="1" <?php echo wangguard_get_option("wangguard-report-posts")=='1' ? 'checked' : ''?> />
						<label for="wangguardreportposts"><?php _e( sprintf("<strong>Allow reporting users from Posts admin screen.</strong><br/>By checking this option a new link to report a post's author will be added for each post on the <a href=\"%s\">Posts admin screen</a>." , $wangguard_edit_prefix . "edit.php"), 'wangguard') ?></label>
					</p>
					<p>
						<input type="checkbox" name="wangguard-delete-users-on-report" id="wangguard-delete-users-on-report" value="1" <?php echo wangguard_get_option("wangguard-delete-users-on-report")=='1' ? 'checked' : ''?> />
						<label for="wangguard-delete-users-on-report"><?php _e("<strong>Delete users when reporting them to WangGuard.</strong><br/>By checking this option, the users you report as Sploggers will be deleted from your site.", 'wangguard') ?></label>
					</p>
					<p>
						<input type="checkbox" name="wangguardenablebpreportbtn" id="wangguardenablebpreportbtn" value="1" <?php echo wangguard_get_option("wangguard-enable-bp-report-btn")=='1' ? 'checked' : ''?> />
						<label for="wangguardenablebpreportbtn"><?php _e("<strong>Show the 'report user' button on BuddyPress.</strong><br/>BuddyPress only. By checking this option a link called 'report user' will be shown on each user's activity and profile page.", 'wangguard') ?></label>
					</p>
					<p>
						<input type="checkbox" name="wangguardenablebpreportblog" id="wangguardenablebpreportblog" value="1" <?php echo wangguard_get_option("wangguard-enable-bp-report-blog")=='1' ? 'checked' : ''?> />
						<label for="wangguardenablebpreportblog"><?php _e("<strong>Show the 'Report blog and author' menu item in the Admin Bar.</strong><br/>BuddyPress only. By checking this option a new menu item on the Admin Bar called 'Report blog and author' will be shown on each blog.", 'wangguard') ?></label>
					</p>

					<p>
						<input type="checkbox" name="wangguard-verify-gmail" id="wangguard-verify-gmail" value="1" <?php echo wangguard_get_option("wangguard-verify-gmail")=='1' ? 'checked' : ''?> />
						<label for="wangguard-verify-gmail"><?php _e("<strong>Check for duplicated gmail.com and googlemail.com emails on sign up.</strong><br/>Checks that duplicated accounts @gmail.com and @googlemail.com accounts doesn't exists, also takes in count that gMail ignores the dots and what's after a + sign on the left side of the @.", 'wangguard') ?></label>
					</p>

					<?php 
					//verifies if the getmxrr() function is availabe
					$wangguard_mx_ok = function_exists('getmxrr');?>
					<p>
						<input <?php echo (!$wangguard_mx_ok ? "disabled = 'disabled'" : "") ?> type="checkbox" name="wangguard-verify-dns-mx" id="wangguard-verify-dns-mx" value="1" <?php echo $wangguard_mx_ok && wangguard_get_option("wangguard-verify-dns-mx")=='1' ? 'checked' : ''?> />
						<label for="wangguard-verify-dns-mx"><?php _e("<strong>Check email domains agains the DNS server.</strong><br/>Verifies that an associated Mail eXchange (MX) record exists for the email domain, if the verification fails, the sign up process is stopped. Recommeded for non multi user or BuddyPress WordPress installations. Users may notice a small delay of around 1 or 2 seconds on the sign up process due to the DNS verification.", 'wangguard') ?></label>
						<?php if (!$wangguard_mx_ok) {
							echo "<div>";
							_e("<strong>Warning:</strong> PHP function <strong>getmxrr()</strong> is not available on your server. Contact your server admin to enable it in order to activate this feature." , "wangguard");
							echo "</div>";
						} ?>
					</p>

					<p>
						<input type="checkbox" name="wangguardexpertmode" id="wangguardexpertmode" value="1" <?php echo wangguard_get_option("wangguard-expertmode")=='1' ? 'checked' : ''?> />
						<label for="wangguardexpertmode"><?php _e("<strong>Ninja mode.</strong><br/>By checking this option no confirmation message will be asked for report operations on the Users manager. Just remember that users gets deleted when reported and the option 'Delete users when reporting them to WangGuard' is selected.", 'wangguard') ?></label>
					</p>

					<p class="submit"><input class="button-primary" type="submit" name="optssave" value="<?php _e('Save options &raquo;', 'wangguard'); ?>" /></p>

				</form>
			</div>

			

			<!--WANGGUARD BLOCKED DOMAINS-->
			<div id="wangguard-conf-domains" style="margin: auto;">
				
				<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/blocked.png" alt="<?php echo htmlentities(__('Blocked domains', 'wangguard')) ?>" /></div>
				
				<h3><?php _e('Blocked domains', 'wangguard'); ?></h3>
				<p><?php _e('Here are different domain lists maintained by WangGuard.', 'wangguard'); ?></p>
				<p><?php _e('You should never block all domains contained in these listings or no one can register on your site.', 'wangguard'); ?></p>
				<p><?php _e('If a domain is in this list, it is because one or more of its users have used for undesirable activities, but that does not mean they are entirely sploggers.', 'wangguard'); ?></p>
				<p><?php _e('These lists are updated automatically each time you enter this screen.', 'wangguard'); ?></p>
				
				<?php
				$lang = substr(WPLANG, 0,2);
				$response = wangguard_http_post("wg=<in><apikey>$wangguard_api_key</apikey><lang>$lang</lang></in>", 'get-domain-list.php');
				$xml = XML_unserialize($response);
				if (!is_array($xml)) {
					?><p><?php _e("There was an error while pulling the domains list from WangGuard, please try again later. If the problem persists please contact WangGuard to report it.", 'wangguard') ?></p><?php
				}
				else {
					
					$selectedDomains = maybe_unserialize( wangguard_get_option('blocked-list-domains') );
					if (!is_array($selectedDomains)) $selectedDomains = array();
					?>
					<form action="" method="post" id="wangguard-blockeddomainsform">
						<?php
						$first = true;
						$lists = $xml['out']['list'];
						$domainix = 0;
						foreach ($lists as $ix => $list) {
							
							$domainQ = 0;
							if (@is_array($list['domains']['domain']))
								$domainQ = count($list['domains']['domain']);
							elseif (isset($list['domains']['domain']))
								$domainQ = 1;
							
							echo "<div class='wangguard-blockeddomain-header' id='wangguard-blockeddomain-header-".$ix."'><a href='javascript:void(0)' rel='".$ix."'>".$list['name']."&nbsp;&nbsp;&nbsp;(<span id='wangguard-domain-count-".$ix."'>0</span> ".sprintf(__('out of %d domains selected', 'wangguard') , $domainQ).")</a></div>";
							echo "<div class='wangguard-blockeddomain-domains' id='wangguard-blockeddomain-domains-".$ix."' wgix='".$ix."' ".("style='display:none'").">";
							echo "<p>".$list['description']."</p>";
							echo "<div class='wangguard-blockeddomain-list'>";

							echo '<table class="wp-list-table widefat" cellspacing="0">';

							echo '<thead>';
							echo '<tr>';
							echo '<td colspan="4" class="check-column" style="padding: 4px 7px 2px; font-style:italic; font-weight:bold">';
							echo "<input type='checkbox' wgix='".$ix."' id='wangguard-selectall-".$ix."' /> <label for='wangguard-selectall-".$ix."'>" . __('select / unselect all domains', 'wangguard') . "</label><br/>";
							echo "</td>";
							echo "</tr>";
							echo '</thead>';

							echo '<tbody>';
							
							if (@is_array($list['domains']['domain'])) {
								echo '<tr>';
								$colIX = 1;
								foreach ($list['domains']['domain'] as $domain) {
									$checked = isset( $selectedDomains[$domain] ) ? "checked" : "";
									echo '<td class="check-column wangguard-domain" style="padding: 4px 7px 2px;">';
									echo "<input wgix='".$ix."' name='domains[]' type='checkbox' value='$domain' $checked id='wangguard-domain-".(++$domainix)."' /> <label for='wangguard-domain-".($domainix)."'>" . $domain . "</label><br/>";
									echo "</td>";
									if (++$colIX > 4) {
										echo '</tr><tr>';
										$colIX = 1;
									}
									
								}
								echo "</tr>";
							}
							elseif (isset($list['domains']['domain'])) {
								$domain = $list['domains']['domain'];
								$checked = isset( $selectedDomains[$domain] ) ? "checked" : "";
								echo '<tr>';
								echo '<td class="check-column wangguard-domain" style="padding: 4px 7px 2px;">';
								echo "<input wgix='".$ix."' name='domains[]' type='checkbox' value='$domain' $checked id='wangguard-domain-".(++$domainix)."' /> <label for='wangguard-domain-".($domainix)."'>" . $domain . "</label><br/>";
								echo "</td>";
								echo "</tr>";
							}
							
							echo "</tbody>";
							echo "</table>";
							echo "</div>";
							echo "</div>";
							$first = false;
						}
						?>
						<p class="submit"><input class="button-primary" type="submit" name="saveblockeddomain" value="<?php _e('Save blocked domains &raquo;', 'wangguard'); ?>" /></p>
					</form>
					<script type="text/javascript">
						
						function wangguard_update_domain_count(ix) {
							var q = jQuery("#wangguard-blockeddomain-domains-"+ix+" td.wangguard-domain input[type=checkbox]:checked").length;
							jQuery('#wangguard-domain-count-' + ix).html(q);
						}
						
						var wangguardDomainBlockIX = -1;
						jQuery(document).ready(function() {
							
							jQuery(".wangguard-blockeddomain-domains input[type=checkbox]").change(function() {
								wangguard_update_domain_count(jQuery(this).attr("wgix"));
							});
							
							jQuery(".wangguard-blockeddomain-header a").click(function() {
								var ix = jQuery(this).attr('rel');
								
								if (wangguardDomainBlockIX == ix) {
									jQuery('#wangguard-blockeddomain-domains-'+wangguardDomainBlockIX).slideUp('fast');
									wangguardDomainBlockIX = -1;
								}
								else {
									if (wangguardDomainBlockIX != -1)
										jQuery('#wangguard-blockeddomain-domains-'+wangguardDomainBlockIX).slideUp('fast');
									
									jQuery('#wangguard-blockeddomain-domains-'+ix).slideDown('fast');
									wangguardDomainBlockIX = ix;
								}
							});
							
							jQuery(".wangguard-blockeddomain-domains").each(function() {
								wangguard_update_domain_count(jQuery(this).attr("wgix"));
							});
						});
					</script>
					<?php
				}
				?>
				
			</div>
			<!--WANGGUARD BLOCKED DOMAINS-->

			

			<!--WANGGUARD SERVERS-->
			<div id="wangguard-conf-conectivity" style="margin: auto;">

				<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/connectivity.png" alt="<?php echo htmlentities(__('Server Connectivity', 'wangguard')) ?>" /></div>
			
				<form action="" method="post" id="wangguard-connectivity" style="margin:0px auto 0 auto;  ">

					<h3 style="margin-bottom: 30px;"><?php _e('Server Connectivity', 'wangguard'); ?></h3>
				<?php
					if ( !function_exists('fsockopen') || !function_exists('gethostbynamel') ) {
						?>
							<p style="padding: .5em; background-color: #00a000; color: #fff; font-weight:bold;"><?php _e('Network functions are disabled.', 'wangguard'); ?></p>
							<p><?php echo sprintf( __('Your web host or server administrator has disabled PHP\'s <code>fsockopen</code> or <code>gethostbynamel</code> functions.  <strong>WangGuard cannot work correctly until this is fixed.</strong>  Please contact your web host or firewall administrator.', 'wangguard')); ?></p>
						<?php
					} else {
						$servers = wangguard_get_server_connectivity();
						$fail_count = count($servers) - count( array_filter($servers) );

						if ( is_array($servers) && count($servers) > 0 ) {
							// some connections work, some fail
							if ( $fail_count > 0 && $fail_count < count($servers) ) { ?>
								<p class="wangguard-info wangguard-error"><?php _e('Unable to reach some WangGuard servers.', 'wangguard'); ?></p>
								<p><?php echo sprintf( __('A network problem or firewall is blocking some connections from your web server to WangGuard.com.  WangGuard is working but this may cause problems during times of network congestion.', 'wangguard')); ?></p>
							<?php
							// all connections fail
							} elseif ( $fail_count > 0 ) { ?>
								<p class="wangguard-info wangguard-error"><?php _e('Unable to reach any WangGuard servers.', 'wangguard'); ?></p>
								<p><?php echo sprintf( __('A network problem or firewall is blocking all connections from your web server to WangGuard.com.  <strong>WangGuard cannot work correctly until this is fixed.</strong>', 'wangguard')); ?></p>
							<?php
							// all connections work
							} else { ?>
								<p class="wangguard-info wangguard-success"><?php  _e('All WangGuard servers are available.', 'wangguard'); ?></p>
								<p><?php _e('WangGuard is working correctly.  All servers are accessible.', 'wangguard'); ?></p>
							<?php
							}
						} else {
							?>
								<p class="wangguard-info wangguard-error"><?php _e('Unable to find WangGuard servers.', 'wangguard'); ?></p>
								<p><?php echo sprintf( __('A DNS problem or firewall is preventing all access from your web server to wangguard.com.  <strong>WangGuard cannot work correctly until this is fixed.</strong>', 'wangguard')); ?></p>
							<?php
						}
					}

					if ( !empty($servers) ) {
					?>
						<table style="width: 100%;"> 
						<thead>
							<th style="border-bottom: 1px solid #999; padding-bottom: 5px; margin-bottom: 5px;"><?php _e('WangGuard server', 'wangguard'); ?></th>
							<th style="border-bottom: 1px solid #999; padding-bottom: 5px; margin-bottom: 5px;"><?php _e('Network Status', 'wangguard'); ?></th>
						</thead>
						<tbody>
							<?php
							asort($servers);
							foreach ( $servers as $ip => $status ) {
								$class = ( $status ? 'wangguard-info wangguard-success' : 'wangguard-info wangguard-error');?>
							<tr>
								<td style="text-align:center; font-weight:bold;"><?php echo htmlspecialchars($ip); ?></td>
								<td><p class="<?php echo $class?>"><?php echo ($status ? __('No problems', 'wangguard') : __('Obstructed', 'wangguard') ); ?></p></td>
							</tr>
							<?php
							}?>
						</tbody>
						</table>
						<?php
					}
					?>
					<p><?php if ( wangguard_get_option('wangguard_connectivity_time') ) echo sprintf( __('Last checked %s ago.', 'wangguard'), human_time_diff( wangguard_get_option('wangguard_connectivity_time') ) ); ?></p>
					<p class="submit"><input type="submit" name="check" class="button-primary" value="<?php _e('Check network status &raquo;', 'wangguard'); ?>" /></p>
				</form>
			</div>

			</div>
			
		</div>


		<script type="text/javascript">
		  jQuery(document).ready(function() {
			  jQuery('#wangguard-conf-tabs').tabs();
			  jQuery('#wangguard-conf-tabs').tabs("select" , <?php echo $selectedTab?>);
		  });
		</script>

	</div>
</div>
<?php
}
?>