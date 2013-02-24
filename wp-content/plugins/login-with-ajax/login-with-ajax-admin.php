<?php
/*
Copyr</td>ight (C) 2009 NetWebLogic LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Class initialization
class LoginWithAjaxAdmin{
	// action function for above hook
	function LoginWithAjaxAdmin() {
		global $user_level;
		add_action ( 'admin_menu', array (&$this, 'menus') );
		if( !empty($_GET['lwa_dismiss_notice']) && $_GET['lwa_dismiss_notice'] == '1' ){
			update_option('lwa_notice', 1);
		}elseif( get_option('lwa_notice') != 1 && $user_level == 10 ){
			add_action('admin_notices', array(&$this, 'admin_notices') );			
		}
	}
	
	function menus(){
		$page = add_options_page('Login With Ajax', 'Login With Ajax', 'manage_options', 'login-with-ajax', array(&$this,'options'));
		add_action('admin_head-'.$page, array(&$this,'options_head'));
	}

	function admin_notices() {
		$dismiss = $_SERVER['REQUEST_URI'];
		$dismiss .= (strpos($dismiss, '?')) ? "&amp;":"?";
		$dismiss .= "lwa_dismiss_notice=1";
		?>
		<div id='lwa-warning' class='updated fade'>
			<p>
				<?php _e('New features in Login With AJAX (including registration!), check out the settings and widget pages!', 'login-with-ajax') ?> - 
				<a href="<?php echo bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=login-with-ajax">Settings</a> | 
				<a href="<?php echo $dismiss ?>"><?php _e('Dismiss','login-with-ajax') ?></a> 
			</p>
		</div>
		<?php
	}
	
	
	function options_head(){
		?>
		<style type="text/css">
			.nwl-plugin table { width:100%; }
			.nwl-plugin table .col { width:100px; }
			.nwl-plugin table input.wide { width:100%; padding:2px; }
		</style>
		<?php
	}
	
	function options() {
		global $LoginWithAjax;
		add_option('lwa_data');
		$lwa_data = array();	
		
		if( is_admin() && !empty($_POST['lwasubmitted']) ){
			//Build the array of options here
			foreach ($_POST as $postKey => $postValue){
				if( $postValue != '' && preg_match('/lwa_role_log(in|out)_/', $postKey) ){
					//Custom role-based redirects
					if( preg_match('/lwa_role_login/', $postKey) ){
						//Login
						$lwa_data['role_login'][str_replace('lwa_role_login_', '', $postKey)] = $postValue;
					}else{
						//Logout
						$lwa_data['role_logout'][str_replace('lwa_role_logout_', '', $postKey)] = $postValue;
					}
				}elseif( substr($postKey, 0, 4) == 'lwa_' ){
					//For now, no validation, since this is in admin area.
					if($postValue != ''){
						$lwa_data[substr($postKey, 4)] = $postValue;
					}
				}
			}
			update_option('lwa_data', $lwa_data);
			if( !empty($_POST['lwa_notification_override']) ){
				update_option('lwa_notification_override',$_POST['lwa_notification_override']);
			}
			?>
			<div class="updated"><p><strong><?php _e('Changes saved.'); ?></strong></p></div>
			<?php
		}else{
			$lwa_data = get_option('lwa_data');	
		}
		?>
		<div class="wrap nwl-plugin">
			<h2>Login With Ajax</h2>
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div id="side-info-column" class="inner-sidebar">
				<div id="categorydiv" class="postbox ">
						<div class="handlediv" title="Click to toggle"></div>
						<h3 class="hndle">Donations</h3>
						<div class="inside">
							<em>Plugins don't grow on trees.</em> Please remember that this plugin is provided to you free of charge, yet it takes many hours of work to maintain and improve!
							<div style="text-align:center;">
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
									<input type="hidden" name="cmd" value="_s-xclick">
									<input type="hidden" name="hosted_button_id" value="8H9R5FVER3SWW">
									<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
									<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
								</form>
							</div>
						</div>
					</div>
					<div id="categorydiv" class="postbox ">
						<div class="handlediv" title="Click to toggle"></div>
						<h3 class="hndle">Plugin Information</h3>
						<div class="inside">
							<p>This plugin was developed by <a href="http://twitter.com/marcussykes">Marcus Sykes</a> @ <a href="http://netweblogic.com">NetWebLogic</a></p>
							<p>Please visit <a href="http://netweblogic.com/forums/">our forum</a> for plugin support.</p>
							<p>If you'd like to translate this plugin, the language files are in the langs folder. Please email any translations to wp.plugins@netweblogic.com and we'll incorporate it into the plugin.</p>
						</div>
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content">
						<p>If you have any suggestions, come over to our plugin page and leave a comment. It may just happen!</p>
						<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
						<table class="form-table">
							<tbody id="lwa-body">
								<tr valign="top">
									<td colspan="2">
										<h3><?php _e("General Settings", 'login-with-ajax'); ?></h3>
									</td>
								</tr>
								<?php if( count($LoginWithAjax->templates) > 1 ) : ?>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Default Template", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<select name="lwa_template"  style="margin:0px; padding:0px; width:auto;">
						            		<?php foreach( array_keys($LoginWithAjax->templates) as $template ): ?>
						            		<option <?php echo (!empty($lwa_data['template']) && $lwa_data['template'] == $template) ? 'selected="selected"':""; ?>><?php echo $template ?></option>
						            		<?php endforeach; ?>
						            	</select>
										<br />
										<i><?php _e("Choose the default theme you'd like to use. This can be overriden in the widget, shortcode and template tags.", 'login-with-ajax'); ?></i>
										<i><?php _e("Further documentation for this feature coming soon...", 'login-with-ajax'); ?></i>
									</td>
								</tr>
								<?php endif; ?>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Disable refresh upon login?", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<input style="margin:0px; padding:0px; width:auto;" type="checkbox" name="lwa_no_login_refresh" value='1' class='wide' <?php echo ( !empty($lwa_data['no_login_refresh']) && $lwa_data['no_login_refresh'] == '1' ) ? 'checked="checked"':''; ?> />
										<br />
										<i><?php _e("If the user logs in and you check the button above, only the login widget will update itself without refreshing the page. Not a good idea if your site shows different content to users once logged in, as a refresh would be needed.", 'login-with-ajax'); ?></i> 
										<i><?php _e("<strong>Experimental and not fully tested!</strong> please test and report any bugs on our forum.", 'login-with-ajax'); ?></i>
									</td>
								</tr>
								<tr valign="top">
									<td colspan="2">
										<h3><?php _e("Redirection Settings", 'login-with-ajax'); ?></h3>
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Global Login Redirect", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<input type="text" name="lwa_login_redirect" value='<?php echo (!empty($lwa_data['login_redirect'])) ? $lwa_data['login_redirect']:''; ?>' class='wide' />
										<i><?php _e("If you'd like to send the user to a specific URL after login, enter it here (e.g. http://wordpress.org/)", 'login-with-ajax'); ?></i>
										<br/><i><strong><?php _e("New!", 'login-with-ajax'); ?></strong> <?php _e("Use %USERNAME% and it will be replaced with the username of person logging in.", 'login-with-ajax'); ?></i> 
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Global Logout Redirect", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<input type="text" name="lwa_logout_redirect" value='<?php echo (!empty($lwa_data['logout_redirect'])) ? $lwa_data['logout_redirect']:''; ?>' class='wide' />
										<i><?php _e("If you'd like to send the user to a specific URL after logout, enter it here (e.g. http://wordpress.org/)", 'login-with-ajax'); ?></i>
										<br /><i><strong><?php _e("New!", 'login-with-ajax'); ?></strong> <?php _e("Enter %LASTURL% to send the user back to the page they were previously on.", 'login-with-ajax'); ?></i> 
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Role-Based Custom Login Redirects", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<i><?php _e("If you would like a specific user role to be redirected to a custom URL upon login, place it here (blank value will default to the global redirect)", 'login-with-ajax'); ?></i>
										<table>
										<?php 
										//Taken from /wp-admin/includes/template.php Line 2715  
										$editable_roles = get_editable_roles();		
										foreach( $editable_roles as $role => $details ) {
											$role_login = ( !empty($lwa_data['role_login']) && is_array($lwa_data['role_login']) && array_key_exists($role, $lwa_data['role_login']) ) ? $lwa_data['role_login'][$role]:''
											?>
											<tr>
												<td class="col"><?php echo translate_user_role($details['name']) ?></td>
												<td><input type='text' class='wide' name='lwa_role_login_<?php echo esc_attr($role) ?>' value="<?php echo $role_login ?>" /></td>
											</tr>
											<?php
										}
										?>
										</table>
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Role-Based Custom Logout Redirects", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<i><?php _e("If you would like a specific user role to be redirected to a custom URL upon logout, place it here (blank value will default to the global redirect)", 'login-with-ajax'); ?></i>
										<table>
										<?php 
										//Taken from /wp-admin/includes/template.php Line 2715  
										$editable_roles = get_editable_roles();		
										foreach( $editable_roles as $role => $details ) {
											$role_logout = ( !empty($lwa_data['role_logout']) && is_array($lwa_data['role_logout']) && array_key_exists($role, $lwa_data['role_logout']) ) ? $lwa_data['role_logout'][$role]:''
											?>
											<tr>
												<td class='col'><?php echo translate_user_role($details['name']) ?></td>
												<td><input type='text' class='wide' name='lwa_role_logout_<?php echo esc_attr($role) ?>' value="<?php echo $role_logout ?>" /></td>
											</tr>
											<?php
										}
										?>
										</table>
									</td>
								</tr>
								<tr valign="top">
									<td colspan="2">
										<h3><?php _e("Notification Settings", 'login-with-ajax'); ?></h3>
										<p>
											<i><?php _e("If you'd like to override the default Wordpress email users receive once registered, make sure you check the box below and enter a new email subject and message", 'login-with-ajax'); ?></i><br />
											<i><?php _e("If this feature doesn't work, please make sure that you don't have another plugin installed which also manages user registrations (e.g. BuddyPress and MU).", 'login-with-ajax'); ?></i>										
										</p>
									</td>
								</tr>
								<tr valign="top">
									<td>
										<label><?php _e("Override Default Email?", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<input style="margin:0px; padding:0px; width:auto;" type="checkbox" name="lwa_notification_override" value='1' class='wide' <?php echo ( !empty($lwa_data['notification_override']) && $lwa_data['notification_override'] == '1' ) ? 'checked="checked"':''; ?> />
									</td>
								</tr>
								<tr valign="top">
									<td>
										<label><?php _e("Subject", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<?php 
										if(empty($lwa_data['notification_subject'])){
											$lwa_data['notification_subject'] = __('Your registration at %BLOGNAME%', 'login-with-ajax');
										}
										?>
										<input type="text" name="lwa_notification_subject" value='<?php echo (!empty($lwa_data['notification_subject'])) ? $lwa_data['notification_subject'] : ''; ?>' class='wide' />
										<i><?php _e("<code>%USERNAME%</code> will be replaced with a username.", 'login-with-ajax'); ?></i><br />
										<i><?php _e("<code>%PASSWORD%</code> will be replaced with the user's password.", 'login-with-ajax'); ?></i><br />
										<i><?php _e("<code>%BLOGNAME%</code> will be replaced with the name of your blog.", 'login-with-ajax'); ?></i>
										<i><?php _e("<code>%BLOGURL%</code> will be replaced with the url of your blog.", 'login-with-ajax'); ?></i>
									</td>
								</tr>
								<tr valign="top">
									<td>
										<label><?php _e("Message", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<?php 
										if( empty($lwa_data['notification_message']) ){
											$lwa_data['notification_message'] = __('Thanks for signing up to our blog. 

You can login with the following credentials by visiting %BLOGURL%

Username : %USERNAME%
Password : %PASSWORD%

We look forward to your next visit!

The team at %BLOGNAME%', 'login-with-ajax');
										}
										?>
										<textarea name="lwa_notification_message" class='wide' style="width:100%; height:250px;"><?php echo $lwa_data['notification_message'] ?></textarea>
										<i><?php _e("<code>%BLOGNAME%</code> will be replaced with the name of your blog.", 'login-with-ajax'); ?></i>
										<i><?php _e("<code>%BLOGURL%</code> will be replaced with the url of your blog.", 'login-with-ajax'); ?></i>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr valign="top">
									<td colspan="2">	
										<input type="hidden" name="lwasubmitted" value="1" />
										<p class="submit">
											<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
										</p>							
									</td>
								</tr>
							</tfoot>
						</table>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

function LoginWithAjaxAdminInit(){
	global $LoginWithAjaxAdmin; 
	$LoginWithAjaxAdmin = new LoginWithAjaxAdmin();
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'LoginWithAjaxAdminInit' );
?>