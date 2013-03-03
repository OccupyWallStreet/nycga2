<?php
if ( !current_user_can('manage_network_options') )
	wp_die('You dont have permissions for this page');

global $current_site;
$domain = $current_site->domain;
$register_url = "http://premium.wpmudev.org/wp-admin/profile.php?page=ustapi&amp;domain=$domain";

function ust_trim_array($input) {
	if (!is_array($input))
		return trim($input);
	return array_map('ust_trim_array', $input);
}

//process form
if (isset($_POST['ust_settings'])) {

	//check the api key and connection
	$request["API_KEY"] = $_POST['ust']['api_key'];
	$api_response = ust_http_post('api_check', $request);
	if ($api_response && $api_response != 'Valid') {
		$_POST['ust']['api_key'] = '';
		echo '<div id="message" class="error"><p>'.__(sprintf('There was a problem with the API key you entered: "%s" <a href="%s" target="_blank">Fix it here&raquo;</a>', $api_response, $register_url), 'ust').'</p></div>';
	} else if (!$api_response) {
		$_POST['ust']['api_key'] = '';
		echo '<div id="message" class="error"><p>'.__('There was a problem connecting to the API server. Please try again later.', 'ust').'</p></div>';
	}
	$_POST['ust']['hide_adminbar'] = isset($_POST['ust']['hide_adminbar']) ? 1 : 0; //handle checkbox
	if (isset($_POST['ust']['keywords']) && trim($_POST['ust']['keywords']))
		$_POST['ust']['keywords'] = explode("\n", trim($_POST['ust']['keywords']));
	else
		$_POST['ust']['keywords'] = '';
	update_site_option("ust_settings", $_POST['ust']);

	$ust_signup['active'] = isset($_POST['ust_signup']) ? 1 : 0;
	$ust_signup['expire'] = time() + 86400; //extend 24 hours
	$ust_signup['slug'] = 'signup-'.substr(md5(time()), rand(0,30), 3); //create new random signup url
	update_site_option('ust_signup', $ust_signup);

	update_site_option("ust_recaptcha", ust_trim_array($_POST['recaptcha']));
	update_site_option("ust_ayah", ust_trim_array($_POST['ayah']));

	//process user questions
	$qa['questions'] = explode("\n", trim($_POST['ust_qa']['questions']));
	$qa['answers'] = explode("\n", trim($_POST['ust_qa']['answers']));
	$i = 0;
	foreach ($qa['questions'] as $question) {
		if (trim($qa['answers'][$i]))
			$ust_qa[] = array(trim($question), trim($qa['answers'][$i]));
		$i++;
	}
	update_site_option("ust_qa", $ust_qa);

	do_action('ust_settings_process');

	echo '<div id="message" class="updated fade"><p>'.__('Settings Saved!', 'ust').'</p></div>';
}

$ust_settings = get_site_option("ust_settings");
$ust_signup = get_site_option('ust_signup');
$ust_recaptcha = get_site_option("ust_recaptcha");
$ust_ayah = get_site_option("ust_ayah");
$ust_qa = get_site_option("ust_qa");
if (!$ust_qa)
	$ust_qa = array(array('What is the answer to "Ten times Two" in word form?','Twenty'), array('What is the last name of the current US president?','Obama'));

if (is_array($ust_qa) && count($ust_qa)) {
	foreach ($ust_qa as $pair) {
		$questions[] = $pair[0];
		$answers[] = $pair[1];
	}
}

//create salt if not set
if (!get_site_option("ust_salt"))
	update_site_option("ust_salt", substr(md5(time()), rand(0,15), 10));

if (!$ust_settings['api_key'])
	$style = ' style="background-color:#FF7C7C;"';
else
	$style = ' style="background-color:#ADFFAA;"';

?>
<div class="wrap">
<div class="icon32"><img src="<?php echo plugins_url('/anti-splog/includes/icon-large.png'); ?>" /></div>
<h2><?php _e('Anti-Splog Settings', 'ust') ?></h2>
<div id="poststuff" class="metabox-holder">
	<form method="post" action="">
	<input type="hidden" name="ust_settings" value="1" />
	
	<div class="postbox">
	<h3 class='hndle'><span><?php _e('API Settings', 'ust') ?></span></h3>
	<div class="inside">
		<p><?php _e("You must enter an API key and register the WordPress Multisite Domain (<strong>$domain</strong>) of this server to enable live splog checking. <a href='$register_url' target='_blank'>Get your API key and register your server here.</a> You must be a current WPMU DEV Premium subscriber to use our API.", 'ust') ?></p>
		<p><?php _e("<strong>How It Works</strong> - When a user completes the signup for a blog (email activated) or publishes a blog post it will send all kinds of blog and signup info to our server here where we will rate it based on our secret ever-adjusting logic. Our API will then return a splog Certainty number (0%-100%) to your server. If that number is greater than the sensitivity preference you set in the plugin settings (80% default) then the blog gets auto-spammed. Since the blog was actually created, it will still show up in the site admin (as spammed) so you can unspam later if there was a mistake (and our service will learn from that). The API (especially the post checking part) has proven to be more than 98% effective at removing splogs. Enable it today to save countless hours managing your network!", 'ust') ?></p>
		<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php _e('API Key', 'ust') ?>*</th>
				<td><input type="text" name="ust[api_key]"<?php echo $style; ?> size="45" value="<?php echo stripslashes($ust_settings['api_key']); ?>" /><input type="submit" name="check_key" value="<?php _e('Check Key &raquo;', 'ust') ?>" /></td>
				</tr>
				<!--
				<tr valign="top">
				<th scope="row"><?php _e('Blog Signup Blocking Certainty', 'ust') ?></th>
				<td><select name="ust[block_certainty]">
				<?php
					for ( $counter = 50; $counter <= 100; $counter += 1 ) {
						echo '<option value="' . $counter . '"' . ($ust_settings['block_certainty']==$counter ? ' selected="selected"' : '') . '>' . $counter . '%</option>' . "\n";
					}
					echo '<option value=""' . (empty($ust_settings['block_certainty']) ? ' selected="selected"' : '') . '>' . __("Don't Block", 'ust') . '</option>' . "\n";
				?>
				</select>
				<br /><em><?php _e('Blog signups that return a certainty number greater than or equal to this will be blocked from being able to be created. Use carefully!', 'ust'); ?></em></td>
				</tr>
				-->
				<tr valign="top">
				<th scope="row"><?php _e('Blog Signup Splog Certainty', 'ust') ?></th>
				<td><select name="ust[certainty]">
				<?php
					for ( $counter = 50; $counter <= 100; $counter += 1 ) {
						echo '<option value="' . $counter . '"' . ($ust_settings['certainty']==$counter ? ' selected="selected"' : '') . '>' . $counter . '%</option>' . "\n";
					}
					echo '<option value="999"' . ($ust_settings['certainty']==999 ? ' selected="selected"' : '') . '>' . __("Don't Spam", 'ust') . '</option>' . "\n";
				?>
				</select>
				<br /><em><?php _e('Blog signups that return a certainty number greater than or equal to this will automatically be marked as spam.', 'ust'); ?></em></td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Posting Splog Certainty', 'ust') ?></th>
				<td><select name="ust[post_certainty]">
				<?php
					for ( $counter = 50; $counter <= 100; $counter += 1 ) {
						echo '<option value="' . $counter . '"' . ($ust_settings['post_certainty']==$counter ? ' selected="selected"' : '') . '>' . $counter . '%</option>' . "\n";
					}
					echo '<option value="999"' . ($ust_settings['post_certainty']==999 ? ' selected="selected"' : '') . '>' . __("Don't Spam", 'ust') . '</option>' . "\n";
				?>
				</select>
				<br /><em><?php _e('If a post from a new blog is checked by the API and returns a certainty number greater than or equal to this, it will automatically be marked as spam.', 'ust'); ?></em></td>
				</tr>
			</table>
		</div>
	</div>
	
	<div class="postbox">
		<h3 class='hndle'><span><?php _e('General Settings', 'ust') ?></span> - <span class="description"><?php _e('These protections will work even without an API key.', 'ust') ?></span></h3>
		<div class="inside">
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php _e('Limit Blog Signups Per Day', 'ust') ?></th>
				<td><select name="ust[num_signups]">
				<?php
					for ( $counter = 1; $counter <= 250; $counter += 1 ) {
						echo '<option value="' . $counter . '"' . ($ust_settings['num_signups']==$counter ? ' selected="selected"' : '') . '>' . $counter . '</option>' . "\n";
					}
					echo '<option value=""' . ($ust_settings['num_signups']=='' ? ' selected="selected"' : '') . '>' . __('Unlimited', 'ust') . '</option>' . "\n";
				?>
				</select>
				<br /><em><?php _e('Splog bots and users often register a large number of blogs in a short amount of time. This setting will limit the number of blog signups per 24 hours per IP, which can drastically reduce the splogs you have to deal with if they get past other filters (human sploggers). Remember that an IP is not necessarily tied to a single user. For example employees behind a company firewall may share a single IP.', 'ust'); ?></em></td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Blacklist Splogger IPs', 'ust') ?></th>
				<td><select name="ust[ip_blocking]">
				<?php
					echo '<option value="0"' . ($ust_settings['ip_blocking']=='' ? ' selected="selected"' : '') . '>' . __('Never Block', 'ust') . '</option>' . "\n";
					for ( $counter = 1; $counter <= 250; $counter += 1 ) {
						echo '<option value="' . $counter . '"' . ($ust_settings['ip_blocking']==$counter ? ' selected="selected"' : '') . '>' . $counter . '</option>' . "\n";
					}
				?>
				</select>
				<br /><em><?php _e('This setting will block signups from IPs that are associated with blog signups you have marked as spam. A strict setting of "1" is usually ok, unless you want to weaken the check in case of false spam marking. Remember that an IP is not necessarily tied to a single user. For example employees behind a company firewall may share a single IP.', 'ust'); ?></em></td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Rename wp-signup.php', 'ust') ?>
				<br /><em><small><?php _e('(Not Buddypress compatible)', 'ust') ?></small></em>
				</th>
				<td>
				<label for="ust_signup"><input type="checkbox" name="ust_signup" id="ust_signup"<?php echo ($ust_signup['active']) ? ' checked="checked"' : ''; ?> /> <?php _e('Move wp-signup.php', 'ust') ?></label>
				<br /><?php _e('Current Signup URL:', 'ust') ?> <strong><a target="_blank" href="<?php ust_wpsignup_url(); ?>"><?php ust_wpsignup_url(); ?></a></strong>
				<br /><em><?php _e("Checking this option will disable the wp-signup.php form and change the signup url automatically every 24 hours. It will look something like <strong>http://$domain/signup-XXX/</strong>. To use this you may need to make some slight edits to your main theme's template files. Replace any hardcoded links to wp-signup.php with this function: <strong>&lt;?php ust_wpsignup_url(); ?&gt;</strong> Within post or page content you can insert the <strong>[ust_wpsignup_url]</strong> shortcode, usually in the href of a link. See the install.txt file for more detailed documentation on this function.", 'ust'); ?></em></td>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Spam/Unspam Blog Users', 'ust') ?></th>
				<td>
				<select name="ust[spam_blog_users]">
				<?php
					echo '<option value="1"' . ($ust_settings['spam_blog_users'] == 1 ? ' selected="selected"' : '') . '>' . __('Yes', 'ust') . '</option>' . "\n";
					echo '<option value="0"' . ($ust_settings['spam_blog_users'] != 1 ? ' selected="selected"' : '') . '>' . __('No', 'ust') . '</option>' . "\n";
				?>
				</select><br /><em><?php _e("Enable this to spam/unspam all of a blog's users when the blog is spammed/unspammed. Does not spam Super Admins.", 'ust'); ?></em></td>
				</td>
				</tr>
				<tr valign="top">
				<th scope="row"><?php _e('Hide Admin Bar Button', 'ust'); ?></th>
				<td><label><input type="checkbox" name="ust[hide_adminbar]" value="1"<?php checked($ust_settings['hide_adminbar']); ?> />
				<?php _e('Remove the Anti-Splog actions menu button from the admin bar', 'ust'); ?></label>
				</td>
				</tr>
				<tr valign="top">
				<th scope="row"><?php _e('Queue Display Preferences', 'ust') ?></th>
				<td>
				<?php _e('Strip Images From Post Previews:', 'ust') ?>
				<select name="ust[strip]">
				<?php
					echo '<option value="1"' . ($ust_settings['strip']==1 ? ' selected="selected"' : '') . '>' . __('Yes', 'ust') . '</option>' . "\n";
					echo '<option value="0"' . ($ust_settings['strip']==0 ? ' selected="selected"' : '') . '>' . __('No', 'ust') . '</option>' . "\n";
				?>
				</select><br />
				<?php _e('Blogs Per Page:', 'ust') ?>
				<select name="ust[paged_blogs]">
				<?php
					for ( $counter = 5; $counter <= 100; $counter += 5 ) {
						echo '<option value="' . $counter . '"' . ($ust_settings['paged_blogs']==$counter ? ' selected="selected"' : '') . '>' . $counter . '</option>' . "\n";
					}
				?>
				</select><br />
				<?php _e('Post Previews Per Blog:', 'ust') ?>
				<select name="ust[paged_posts]">
				<?php
					for ( $counter = 1; $counter <= 20; $counter += 1 ) {
						echo '<option value="' . $counter . '"' . ($ust_settings['paged_posts']==$counter ? ' selected="selected"' : '') . '>' . $counter . '</option>' . "\n";
					}
				?>
				</select>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Spam Keyword Search', 'ust') ?></th>
				<td>
				<em><?php _e('Enter one word or phrase per line. Keywords are not case sensitive and may match any part of a word. Example: "Ugg" would match "s<strong>ugg</strong>estion".', 'ust'); ?></em><br />
				<?php if (!function_exists('post_indexer_post_insert_update')) { ?>
				<p class="error"><?php _e('You must install the <a target="_blank" href="http://premium.wpmudev.org/project/post-indexer">Post Indexer</a> plugin to enable keyword flagging.', 'ust'); ?></p>
				<textarea name="ust[keywords]" style="width:200px" rows="4" disabled="disabled"><?php echo stripslashes(implode("\n", (array)$ust_settings['keywords'])); ?></textarea>
				<?php } else { ?>
				<textarea name="ust[keywords]" style="width:200px" rows="4"><?php echo stripslashes(implode("\n", (array)$ust_settings['keywords'])); ?></textarea>
				<?php } ?>
				<br /><strong><em><?php _e('This feature is designed to work in conjunction with our Post Indexer plugin to help you find old and inactive splogs that the API service would no longer catch. Blogs that have these keywords in posts will be temporarily flagged and added to the potential splogs queue. Keywords should only be added here temporarily while searching for splogs. CAUTION: Do not enter more than a few (2-4) keywords at a time or it may slow down or timeout the Suspected Blogs page depending on the number of site-wide posts and server speed.', 'ust'); ?></em></strong></td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Additional Signup Protection', 'ust') ?></th>
				<td>
				<select name="ust[signup_protect]" id="ust_signup_protect">
					<option value="none" <?php if($ust_settings['signup_protect'] == 'none'){echo 'selected="selected"';} ?>><?php _e('None', 'ust') ?></option>
					<option value="questions" <?php if($ust_settings['signup_protect'] == 'questions'){echo 'selected="selected"';} ?>><?php _e('Admin Defined Questions', 'ust') ?></option>
					<option value="asirra" <?php if($ust_settings['signup_protect'] == 'asirra'){echo 'selected="selected"';} ?>><?php _e('ASIRRA - Pick the Cats', 'ust') ?></option>
					<option value="recaptcha" <?php if($ust_settings['signup_protect'] == 'recaptcha'){echo 'selected="selected"';} ?>><?php _e('reCAPTCHA - Advanced Captcha', 'ust') ?></option>
					<option value="ayah" <?php if($ust_settings['signup_protect'] == 'ayah'){echo 'selected="selected"';} ?>><?php _e('Are You a Human PlayThru', 'ust') ?></option>
				</select>
				<br /><em><?php _e('These options are designed to prevent automated spam bot signups, so will have limited effect in stopping human sploggers. Be cautious using these options as it is important to find a balance between stopping bots and not annoying your users.', 'ust'); ?></em></td>
				</td>
				</tr>

				<?php do_action('ust_settings'); ?>
		</table>
	</div>
</div>
	
<div class="postbox">
	<h3 class='hndle'><span><?php _e('Assira', 'ust') ?></span></h3>
	<div class="inside">
		<p><?php _e('Asirra works by asking users to identify photographs of cats and dogs. This task is difficult for computers, but user studies have shown that people can accomplish it quickly and accurately. Many even think it\'s fun!. <a href="http://research.microsoft.com/en-us/um/redmond/projects/asirra/default.aspx" target="_blank">Read more and try a demo here.</a> You must have the cURL extension enabled in PHP to use this. There are no configuration options for Assira.', 'ust') ?></p>
	</div>
</div>

<div class="postbox">
	<h3 class='hndle'><span><?php _e('reCAPTCHA Options', 'ust') ?></span></h3>
	<div class="inside">
		<p><?php _e('reCAPTCHA asks someone to retype two words scanned from a book to prove that they are a human. This verifies that they are not a spambot while also correcting the automatic scans of old books. So you get less spam, and the world gets accurately digitized books. Everybody wins! For details, visit the <a href="http://recaptcha.net/">reCAPTCHA website</a>.', 'ust') ?></p>
		<p><?php _e('<strong>NOTE</strong>: Even if you don\'t use reCAPTCHA on the signup form, you should setup an API key anyway to prevent spamming from the splog review forms.', 'ust') ?></p>
		<table class="form-table">
			<tr valign="top">
			<th scope="row"><?php _e('Keys', 'ust') ?>*</th>
			<td>
				<?php _e('reCAPTCHA requires an API key for each domain, consisting of a "public" and a "private" key. You can sign up for a <a href="http://recaptcha.net/whyrecaptcha.html" target="_blank">free reCAPTCHA key</a>.', 'ust') ?>
				<br />
				<p class="re-keys">
					<!-- reCAPTCHA public key -->
					<label class="which-key" for="recaptcha_pubkey"><?php _e('Public Key:&nbsp;&nbsp;', 'ust') ?></label>
					<input name="recaptcha[pubkey]" id="recaptcha_pubkey" size="40" value="<?php echo stripslashes($ust_recaptcha['pubkey']); ?>" />
					<br />
					<!-- reCAPTCHA private key -->
					<label class="which-key" for="recaptcha_privkey"><?php _e('Private Key:', 'ust') ?></label>
					<input name="recaptcha[privkey]" id="recaptcha_privkey" size="40" value="<?php echo stripslashes($ust_recaptcha['privkey']); ?>" />
				</p>
				</td>
			</tr>
			<tr valign="top">
			<th scope="row"><?php _e('Theme:', 'ust') ?></th>
				<td>
					<!-- The theme selection -->
					<div class="theme-select">
					<select name="recaptcha[theme]" id="recaptcha_theme">
					<option value="red" <?php if($ust_recaptcha['theme'] == 'red'){echo 'selected="selected"';} ?>>Red</option>
					<option value="white" <?php if($ust_recaptcha['theme'] == 'white'){echo 'selected="selected"';} ?>>White</option>
					<option value="blackglass" <?php if($ust_recaptcha['theme'] == 'blackglass'){echo 'selected="selected"';} ?>>Black Glass</option>
					<option value="clean" <?php if($ust_recaptcha['theme'] == 'clean'){echo 'selected="selected"';} ?>>Clean</option>
					</select>
					</div>
				</td>
			</tr>
			<tr valign="top">
			<th scope="row"><?php _e('Language:', 'ust') ?></th>
				<td>
					<select name="recaptcha[lang]" id="recaptcha_lang">
					<option value="en" <?php if($ust_recaptcha['lang'] == 'en'){echo 'selected="selected"';} ?>>English</option>
					<option value="nl" <?php if($ust_recaptcha['lang'] == 'nl'){echo 'selected="selected"';} ?>>Dutch</option>
					<option value="fr" <?php if($ust_recaptcha['lang'] == 'fr'){echo 'selected="selected"';} ?>>French</option>
					<option value="de" <?php if($ust_recaptcha['lang'] == 'de'){echo 'selected="selected"';} ?>>German</option>
					<option value="pt" <?php if($ust_recaptcha['lang'] == 'pt'){echo 'selected="selected"';} ?>>Portuguese</option>
					<option value="ru" <?php if($ust_recaptcha['lang'] == 'ru'){echo 'selected="selected"';} ?>>Russian</option>
					<option value="es" <?php if($ust_recaptcha['lang'] == 'es'){echo 'selected="selected"';} ?>>Spanish</option>
					<option value="tr" <?php if($ust_recaptcha['lang'] == 'tr'){echo 'selected="selected"';} ?>>Turkish</option>
					</select>
				</td>
			</tr>
		</table>
		</div>
	</div>
	
	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Are You a Human PlayThru', 'ust') ?></span></h3>
		<div class="inside">
		<p><?php _e('PlayThru is the next evolution of online human verification and the best CAPTCHA replacement around. It stops bots with fun, by focusing on being simple and intuitive. Stop bots without harassing your users. For details, visit the <a href="http://areyouahuman.com/features" target="_blank">Are You a Human website</a>.', 'ust') ?></p>
		<table class="form-table">
			<tr valign="top">
			<th scope="row"><?php _e('Site Keys', 'ust') ?>*</th>
			<td>
				<?php _e('These keys are required to get PlayThru up and running. Signup <a href="http://portal.areyouahuman.com/signup" target="_blank">here</a> to get your free keys.', 'ust') ?>
				<br />
				<p class="re-keys">
					<label class="which-key" for="ayah_pubkey"><?php _e('Publisher Key:', 'ust') ?></label>
					<input name="ayah[pubkey]" id="ayah_pubkey" size="60" value="<?php echo stripslashes($ust_ayah['pubkey']); ?>" />
					<br />
					<label class="which-key" for="ayah_privkey"><?php _e('Scoring Key:', 'ust') ?>&nbsp;&nbsp;</label>
					<input name="ayah[privkey]" id="ayah_privkey" size="60" value="<?php echo stripslashes($ust_ayah['privkey']); ?>" />
				</p>
				</td>
			</tr>
		</table>
		</div>
	</div>

	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Defined Questions Options', 'ust') ?></span></h3>
		<div class="inside">
		<p><?php _e('Displays a random question from the list, and the user must enter the correct answer. It is best to create a large pool of questions that have one-word answers. Answers are not case-sensitive.', 'ust') ?></p>
		<table class="form-table">
		<tr valign="top">
		<th scope="row"><?php _e('Questions and Answers', 'ust') ?></th>
		<td>
			<table>
				<tr>
					<td style="width:75%">
						<?php _e('Questions (one per row)', 'ust') ?>
						<textarea name="ust_qa[questions]" style="width:100%" rows="10"><?php echo stripslashes(implode("\n", $questions)); ?></textarea>
					</td>
					<td style="width:25%">
						<?php _e('Answers (one per row)', 'ust') ?>
						<textarea name="ust_qa[answers]" style="width:100%" rows="10"><?php echo stripslashes(implode("\n", $answers)); ?></textarea>
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</table>
		</div>
	</div>
	
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes', 'ust') ?>" class="button-primary" />
	</p>
	</form>
</div>