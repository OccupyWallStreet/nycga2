<?php
//Configuration page
function wangguard_conf() {
	global $wpdb;
	global $wangguard_nonce, $wangguard_api_key;

	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'wangguard'));

	$key_status = "";
	
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

	} elseif ( isset($_POST['check']) ) {

		wangguard_get_server_connectivity(0);

	} elseif ( isset($_POST['optssave']) ) {

			wangguard_update_option('wangguard-expertmode', $_POST['wangguardexpertmode']=='1' ? 1 : 0 );

			wangguard_update_option('wangguard-report-posts', $_POST['wangguardreportposts']=='1' ? 1 : 0 );

			wangguard_update_option('wangguard-delete-users-on-report', $_POST['wangguard-delete-users-on-report']=='1' ? 1 : -1 );
			
			wangguard_update_option('wangguard-enable-bp-report-btn', $_POST['wangguardenablebpreportbtn']=='1' ? 1 : -1 );
			
			wangguard_update_option('wangguard-enable-bp-report-blog', $_POST['wangguardenablebpreportblog']=='1' ? 1 : -1 );

			wangguard_update_option('wangguard-verify-gmail', $_POST['wangguard-verify-gmail']=='1' ? 1 : 0 );
			
			wangguard_update_option('wangguard-verify-dns-mx', $_POST['wangguard-verify-dns-mx']=='1' ? 1 : 0 );

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
		'new_key_empty' => array('color' => 'aa0', 'text' => __('Your key has been cleared.', 'wangguard')),
		'new_key_valid' => array('color' => '2d2', 'text' => __('Your key has been verified!', 'wangguard')),
		'new_key_invalid' => array('color' => 'd22', 'text' => __('The key you entered is invalid. Please double-check it.', 'wangguard')),
		'new_key_failed' => array('color' => 'd22', 'text' => __('The key you entered could not be verified because a connection to wangguard.com could not be established. Please check your server configuration.', 'wangguard')),
		'no_connection' => array('color' => 'd22', 'text' => __('There was a problem connecting to the WangGuard server. Please check your server configuration.', 'wangguard')),
		'key_empty' => array('color' => 'aa0', 'text' => sprintf(__('Please enter an API key. (<a href="%s" style="color:#fff">Get your key here.</a>)', 'wangguard'), 'http://wangguard.com/getapikey')),
		'key_valid' => array('color' => '2d2', 'text' => __('This key is valid.', 'wangguard')),
		'key_failed' => array('color' => 'aa0', 'text' => __('The key below was previously validated but a connection to wangguard.com can not be established at this time. Please check your server configuration.', 'wangguard')));

?>


<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'wangguard') ?></strong></p></div>
<?php endif; ?>


<div class="wrap">
<div class="icon32" id="icon-wangguard"><br></div>
<h2><?php _e('WangGuard Configuration', 'wangguard'); ?></h2>
<div class="narrow">
<form action="" method="post" id="wangguard-conf" style="margin: auto; width: 500px; ">
	<p><?php printf(__('For many people, <a href="%1$s">WangGuard</a> will greatly reduce or even completely eliminate the Sploggers you get on your site. If one does happen to get through, simply mark it as Splogger on the Users screen. If you don\'t have an API key yet, <a href="%2$s" target="_new">get one here</a>.', 'wangguard'), 'http://wangguard.com/', 'http://wangguard.com/getapikey'); ?></p>

	<h3><label for="key"><?php _e('WangGuard API Key', 'wangguard'); ?></label></h3>
	<?php foreach ( $ms as $m ) : ?>
		<p style="padding: .5em; background-color: #<?php echo $messages[$m]['color']; ?>; color: #fff; font-weight: bold;"><?php echo $messages[$m]['text']; ?></p>
	<?php endforeach; ?>
	<p><input id="key" name="key" type="text" size="35" maxlength="32" value="<?php echo wangguard_get_option('wangguard_api_key'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /> (<?php _e('<a href="http://wangguard.com/faq" target="_new">What is this?</a>', 'wangguard'); ?>)</p>

	<?php if ( $invalid_key ) { ?>
		<h3><?php _e('Why might my key be invalid?', 'wangguard'); ?></h3>
		<p><?php _e('This can mean one of two things, either you copied the key wrong or that the plugin is unable to reach the WangGuard servers, which is most often caused by an issue with your web host around firewalls or similar.', 'wangguard'); ?></p>
	<?php } ?>


	<?php wangguard_nonce_field($wangguard_nonce) ?>

	<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;', 'wangguard'); ?>" /></p>
</form>


<div id="wangguard-questions" style="margin: auto; width: 500px;">

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
	<?php _e("Question", 'wangguard')?><br/><input type="text" name="wangguardnewquestion" id="wangguardnewquestion" style="width: 500px; padding: 6px" maxlength="255" value="" />
	<br/><br style="line-height: 5px"/>
	<?php _e("Answer", 'wangguard')?><br/><input type="text" name="wangguardnewquestionanswer" id="wangguardnewquestionanswer" style="width: 500px; padding: 6px" maxlength="50" value="" />
	<div id="wangguardnewquestionerror">
		<?php _e('Fill in both the question and the answer fields to create a new security question', 'wangguard')?>
	</div>
	<p class="submit"><input type="button" id="wangguardnewquestionbutton" name="submit" value="<?php _e('Create question &raquo;', 'wangguard'); ?>" /></p>
</div>



<?php 
$wangguard_edit_prefix = "";
if (function_exists( 'is_network_admin' )) 
	if (is_network_admin())
		$wangguard_edit_prefix = "../";
?>
<form action="" method="post" id="wangguard-settings" class="wangguard-sep" style="margin:30px auto 0 auto; width: 500px; ">
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
		<label for="wangguardexpertmode"><?php _e("<strong>Ninja mode.</strong><br/>By checking this option no confirmation message will be asked for report operations on the Users manager. Just remember that users gets deleted when reported, and when reporting a domain, users whose e-mail matches the reported domain gets deleted as well.", 'wangguard') ?></label>
	</p>

	<p class="submit"><input type="submit" name="optssave" value="<?php _e('Save options &raquo;', 'wangguard'); ?>" /></p>

</form>


<form action="" method="post" id="wangguard-connectivity" style="margin:30px auto 0 auto; width: 500px; ">

<h3><?php _e('Server Connectivity', 'wangguard'); ?></h3>
<?php
	if ( !function_exists('fsockopen') || !function_exists('gethostbynamel') ) {
		?>
			<p style="padding: .5em; background-color: #d22; color: #fff; font-weight:bold;"><?php _e('Network functions are disabled.', 'wangguard'); ?></p>
			<p><?php echo sprintf( __('Your web host or server administrator has disabled PHP\'s <code>fsockopen</code> or <code>gethostbynamel</code> functions.  <strong>WangGuard cannot work correctly until this is fixed.</strong>  Please contact your web host or firewall administrator.', 'wangguard')); ?></p>
		<?php
	} else {
		$servers = wangguard_get_server_connectivity();
		$fail_count = count($servers) - count( array_filter($servers) );
		if ( is_array($servers) && count($servers) > 0 ) {
			// some connections work, some fail
			if ( $fail_count > 0 && $fail_count < count($servers) ) { ?>
				<p style="padding: .5em; background-color: #aa0; color: #fff; font-weight:bold;"><?php _e('Unable to reach some WangGuard servers.', 'wangguard'); ?></p>
				<p><?php echo sprintf( __('A network problem or firewall is blocking some connections from your web server to WangGuard.com.  WangGuard is working but this may cause problems during times of network congestion.', 'wangguard')); ?></p>
			<?php
			// all connections fail
			} elseif ( $fail_count > 0 ) { ?>
				<p style="padding: .5em; background-color: #d22; color: #fff; font-weight:bold;"><?php _e('Unable to reach any WangGuard servers.', 'wangguard'); ?></p>
				<p><?php echo sprintf( __('A network problem or firewall is blocking all connections from your web server to WangGuard.com.  <strong>WangGuard cannot work correctly until this is fixed.</strong>', 'wangguard')); ?></p>
			<?php
			// all connections work
			} else { ?>
				<p style="padding: .5em; background-color: #2d2; color: #fff; font-weight:bold;"><?php  _e('All WangGuard servers are available.', 'wangguard'); ?></p>
				<p><?php _e('WangGuard is working correctly.  All servers are accessible.', 'wangguard'); ?></p>
			<?php
			}
		} else {
			?>
				<p style="padding: .5em; background-color: #d22; color: #fff; font-weight:bold;"><?php _e('Unable to find WangGuard servers.', 'wangguard'); ?></p>
				<p><?php echo sprintf( __('A DNS problem or firewall is preventing all access from your web server to wangguard.com.  <strong>WangGuard cannot work correctly until this is fixed.</strong>', 'wangguard')); ?></p>
			<?php
		}
	}

	if ( !empty($servers) ) {
	?>
		<table style="width: 100%;">
		<thead><th><?php _e('WangGuard server', 'wangguard'); ?></th><th><?php _e('Network Status', 'wangguard'); ?></th></thead>
		<tbody>
			<?php
			asort($servers);
			foreach ( $servers as $ip => $status ) {
				$color = ( $status ? '#2d2' : '#d22');?>
			<tr>
			<td><?php echo htmlspecialchars($ip); ?></td>
			<td style="padding: 0 .5em; font-weight:bold; color: #fff; background-color: <?php echo $color; ?>"><?php echo ($status ? __('No problems', 'wangguard') : __('Obstructed', 'wangguard') ); ?></td>

			<?php
			}
	}
	?>
	</tbody>
	</table>
	<p><?php if ( wangguard_get_option('wangguard_connectivity_time') ) echo sprintf( __('Last checked %s ago.', 'wangguard'), human_time_diff( wangguard_get_option('wangguard_connectivity_time') ) ); ?></p>
	<p class="submit"><input type="submit" name="check" value="<?php _e('Check network status &raquo;', 'wangguard'); ?>" /></p>
</form>

</div>
</div>
<?php
}
?>