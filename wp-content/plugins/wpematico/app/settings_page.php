<?php
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');

$cfg = $this->check_options($this->options);  ?>
<div class="wrap">
	<h2><?php _e( 'WPeMatico settings', self :: TEXTDOMAIN );?></h2>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<form method="post" action="">
		<?php  wp_nonce_field('wpematico-settings'); ?>
		<div id="side-info-column" class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox inside">
					<h3 class="handle"><?php _e( 'About', self :: TEXTDOMAIN );?></h3>
					<div class="inside">
						<p id="left1" onmouseover="this.style.background =  '#111';" onmouseout="this.style.background =  '#FFF';" style="text-align:center; background-color: rgb(255, 255, 255); background-position: initial initial; background-repeat: initial initial; "><a href="http://www.wpematico.com" title="Go to new WPeMatico WebSite"><img style="background: transparent;border-radius: 15px;width: 258px;" src="http://www.netmdp.com/wpematicofiles/bannerWPematico.png" title=""></a><br />
						WPeMatico Free Version <?php echo self :: VERSION ; ?> R<?php echo self :: RELEASE ; ?></p>
						<p><?php _e( 'Thanks for test, use and enjoy this plugin.', self :: TEXTDOMAIN );?></p>
						<p><?php _e( 'If you like it, I really appreciate a donation.', self :: TEXTDOMAIN );?></p>
						<p>
						<input type="button" class="button-primary" name="donate" value="<?php _e( 'Click for Donate', self :: TEXTDOMAIN );?>" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B8V39NWK3NFQU');return false;"/>
						</p>
						<p><?php // _e('Help', self :: TEXTDOMAIN ); ?><a href="#" onclick="javascript:window.open('https://www.paypal.com/ar/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');"><img  src="https://www.paypal.com/es_XC/Marketing/i/logo/bnr_airlines1_205x67.gif" border="0" alt="Paypal Help"></a>
						</p>
						<p></p>
						<p>
						<input type="button" class="button-primary" name="buypro" value="<?php _e( 'Buy PRO version online', self :: TEXTDOMAIN );?>" onclick="javascript:window.open('http://www.wpematico.com/wpematico/');return false;"/>
						</p>
						<p></p>
					</div>
				</div>
				<div class="postbox">
					<h3 class="handle"><?php _e( 'Sending e-Mails', self :: TEXTDOMAIN );?></h3>
					<div class="inside">
						<p><b><?php _e('Sender Email:', self :: TEXTDOMAIN ); ?></b><br /><input name="mailsndemail" type="text" value="<?php echo $cfg['mailsndemail'];?>" class="large-text" /></p>
						<p><b><?php _e('Sender Name:', self :: TEXTDOMAIN ); ?></b><br /><input name="mailsndname" type="text" value="<?php echo $cfg['mailsndname'];?>" class="large-text" /></p>
						<p><b><?php _e('Send mail method:', self :: TEXTDOMAIN ); ?></b><br />
						<?php 
						echo '<select id="mailmethod" name="mailmethod">';
						echo '<option value="mail"'.selected('mail',$cfg['mailmethod'],false).'>'.__('PHP: mail()', self :: TEXTDOMAIN ).'</option>';
						echo '<option value="Sendmail"'.selected('Sendmail',$cfg['mailmethod'],false).'>'.__('Sendmail', self :: TEXTDOMAIN ).'</option>';
						echo '<option value="SMTP"'.selected('SMTP',$cfg['mailmethod'],false).'>'.__('SMTP', self :: TEXTDOMAIN ).'</option>';
						echo '</select>';
						?></p>
						<label id="mailsendmail" <?php if ($cfg['mailmethod']!='Sendmail') echo 'style="display:none;"';?>><b><?php _e('Sendmail Path:', self :: TEXTDOMAIN ); ?></b><br /><input name="mailsendmail" type="text" value="<?php echo $cfg['mailsendmail'];?>" class="large-text" /><br /></label>
						<label id="mailsmtp" <?php if ($cfg['mailmethod']!='SMTP') echo 'style="display:none;"';?>>
						<b><?php _e('SMTP Hostname:', self :: TEXTDOMAIN ); ?></b><br /><input name="mailhost" type="text" value="<?php echo $cfg['mailhost'];?>" class="large-text" /><br />
						<b><?php _e('SMTP Secure Connection:', self :: TEXTDOMAIN ); ?></b><br />
						<select name="mailsecure">
						<option value=""<?php selected('',$cfg['mailsecure'],true); ?>><?php _e('none', self :: TEXTDOMAIN ); ?></option>
						<option value="ssl"<?php selected('ssl',$cfg['mailsecure'],true); ?>>SSL</option>
						<option value="tls"<?php selected('tls',$cfg['mailsecure'],true); ?>>TLS</option>
						</select><br />
						<b><?php _e('SMTP Username:', self :: TEXTDOMAIN ); ?></b><br /><input name="mailuser" type="text" value="<?php echo $cfg['mailuser'];?>" class="user large-text" /><br />
						<b><?php _e('SMTP Password:', self :: TEXTDOMAIN ); ?></b><br /><input name="mailpass" type="password" value="<?php echo base64_decode($cfg['mailpass']);?>" class="password large-text" /><br />
						</label>

					</div>
				</div>
				
				<div class="postbox inside">
					<div class="inside">
						<p>
						<input type="submit" class="button-primary" name="submit" value="<?php _e( 'Save settings', self :: TEXTDOMAIN );?>" />
						</p>
					</div>
				</div>
				<div class="postbox inside">
					<h3 class="handle"><?php _e( 'Advanced', self :: TEXTDOMAIN );?></h3>
					<div class="inside">
						<p></p>
						<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['disablecheckfeeds'],true); ?> name="disablecheckfeeds" id="disablecheckfeeds" /> <?php _e('Disable <b><i>Check Feeds before Save</i></b>', self :: TEXTDOMAIN ); ?><br />
						<div id="hlpdfc" style="padding-left:20px;"><?php _e('Check this if you don\'t want automatic check feed URLs before save every campaign. ', self :: TEXTDOMAIN ); ?></div>
						<br /> 
						<p></p>
						<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enabledelhash'],true); ?> name="enabledelhash" id="enabledelhash" /><b>&nbsp;<?php _e('Enable <b><i>Del Hash</i></b>', self :: TEXTDOMAIN ); ?></b><br />
						<div id="hlpimg" style="padding-left:20px;"><?php _e('Show `Del Hash` link on campaigns list.  This link delete all hash codes for check duplicates on every feed per campaign.', self :: TEXTDOMAIN ); ?></div>
						<br />
						<p></p>
						<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enableseelog'],true); ?> name="enableseelog" id="enableseelog" /><b>&nbsp;<?php _e('Enable <b><i>See last log</i></b>', self :: TEXTDOMAIN ); ?></b><br />
						<div id="hlpimg" style="padding-left:20px;"><?php _e('Show `See Log` link on campaigns list.  This link show the last fetch log of campaign.', self :: TEXTDOMAIN ); ?></div>
						<br />
						<p></p>
						<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['disable_credits'],true); ?> name="disable_credits" id="disable_credits" /><b>&nbsp;<?php _e('Disable <b><i>WPeMatico Credits</i> on post.</b>', self :: TEXTDOMAIN ); ?></b><br />
						<div id="hlpimg" style="padding-left:20px;"><?php _e('I really appreciate if you can left this option blank to show the plugin\'s credits.', self :: TEXTDOMAIN ); ?></div>
						<br />
						<p></p>
					</div>
				</div>
				
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
		
			<div id="imgs" class="postbox">
				<h3 class="hndle"><span><?php _e('Images', self :: TEXTDOMAIN ); ?></span></h3>
				<div class="inside">
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['imgcache'],true); ?> name="imgcache" id="imgcache" /><b>&nbsp;<?php echo '<label for="imgcache">' . __('Cache Images.', self :: TEXTDOMAIN ) . '</label>'; ?></b><br />
					<div id="hlpimg" style="padding-left:20px;font-size:10px;"><b><?php _e('Image Caching', self :: TEXTDOMAIN ); ?>:</b> <?php _e('When image caching is on, a copy of every image found in content of every feed (only in &lt;img&gt; tags) is downloaded to the Wordpress UPLOADS Dir.', self :: TEXTDOMAIN ); ?><br />
					<?php _e('If not enabled all images will linked to the image owner\'s server, but also make your website faster for your visitors.', self :: TEXTDOMAIN ); ?><br />
					<b><?php _e('Caching all images', self :: TEXTDOMAIN ); ?>:</b> <?php _e('This featured in the general Settings section, will be overridden for the campaign-specific options.', self :: TEXTDOMAIN ); ?></div>
					<p></p>
					<div id="nolinkimg" style="padding-left:20px; <?php if (!$cfg['imgcache']) echo 'display:none;';?>">
						<input name="gralnolinkimg" id="gralnolinkimg" class="checkbox" value="1" type="checkbox" <?php checked($cfg['gralnolinkimg'],true); ?> />
						<b><?php echo '<label for="gralnolinkimg">' . __('No link to source images', self :: TEXTDOMAIN ) . '</label>'; ?></b><br />
						<b><?php _e("Note",  self :: TEXTDOMAIN ) ?>:</b> <?php _e('If selected and image upload get error, then delete the "src" attribute of the &lt;img&gt;. Check this for don\'t link images from external sites.', self :: TEXTDOMAIN ) ?>
					</div>
					<p></p>
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['imgattach'],true); ?> name="imgattach" id="imgattach" /><b>&nbsp;<?php echo '<label for="imgattach">' . __('Attach Images to posts.', self :: TEXTDOMAIN ) . '</label>'; ?></b><br />
					<div id="hlpatt" style="padding-left:20px;"><b><?php _e('Image Attaching', self :: TEXTDOMAIN ); ?>:</b> <?php _e('By default when image caching is on (and everything is working fine), a copy of every image found is added to Wordpress Media.', self :: TEXTDOMAIN ); ?><br />
					<?php _e('If enabled Image Attaching all images will be attached to the owner post in WP media library; but if you see that the job process is too slowly you can deactivate this here.', self :: TEXTDOMAIN ); ?></div>
					<p></p>
					<div id="featimg" style="padding-left:20px; <?php if (!$cfg['imgattach']) echo 'display:none;';?>">
						<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['featuredimg'],true); ?> name="featuredimg" id="featuredimg" /><b>&nbsp;<?php echo '<label for="featuredimg">' . __('Enable first image found on content as Featured Image.', self :: TEXTDOMAIN ) . '</label>'; ?></b> <small> Read about <a href="http://codex.wordpress.org/Post_Thumbnails" target="_Blank">Post_Thumbnails</a></small>
					</div>
				</div>
			</div>
		
			<div id="enablefeatures" class="postbox">
				<h3 class="hndle"><span><?php _e('Enable Features', self :: TEXTDOMAIN ); ?></span></h3>
				<div class="inside"> 
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enablerewrite'],true); ?> name="enablerewrite" id="enablerewrite" /> <?php _e('Enable <b><i>Rewrite</i></b> feature', self :: TEXTDOMAIN ); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you need use this feature, you can activate here.  Not recommended if you will not use this.', self :: TEXTDOMAIN ); ?></div>
					<br /> 
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enableword2cats'],true); ?> name="enableword2cats" id="enableword2cats" /> <?php _e('Enable <b><i>Words to Categories</i></b> feature', self :: TEXTDOMAIN ); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you need this feature in every campaign, you can activate here.  Not recommended if you will not use this.', self :: TEXTDOMAIN ); ?></div><br /> 
					<?php if( ! $this->options['nonstatic'] ) : ?>
						<input class="checkbox" value="1" type="checkbox" disabled /> <?php _e('Enable <b><i>Keyword Filtering</i></b> feature', self :: TEXTDOMAIN ); ?><br />
						<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you need this feature in every campaign, you can activate here.  ONLY AVAILABLE AT PRO VERSION.', self :: TEXTDOMAIN ); ?><br /> 
						<?php _e('This is for exclude or include posts according to the keywords <b>founded</b> at content or title.', self :: TEXTDOMAIN ); ?>
						</div><br /> 
						<input class="checkbox" value="1" type="checkbox" disabled /> <?php _e('Enable <b><i>Word count Filters</i></b> feature', self :: TEXTDOMAIN ); ?><br />
						<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you need this feature in every campaign, you can activate here.  ONLY AVAILABLE AT PRO VERSION.', self :: TEXTDOMAIN ); ?><br /> 
						<?php _e('This is for cut, exclude or include posts according to the letters o words <b>counted</b> at content.', self :: TEXTDOMAIN ); ?>
						</div><br /> 
						<input class="checkbox" value="1" type="checkbox" disabled /> <?php _e('Enable <b><i>Custom Title</i></b> feature', self :: TEXTDOMAIN ); ?><br />
						<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you want a custom title for posts of a campaign, you can activate here.  ONLY AVAILABLE AT PRO VERSION.', self :: TEXTDOMAIN ); ?><br /> 					
						</div><br /> 
						<input class="checkbox" value="1" type="checkbox" disabled /> <?php _e('Enable Special <b><i>1 Minute fetch</i></b> feature', self :: TEXTDOMAIN ); ?><br />
						<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you want to see the option for fetch RSS posts just every 1 minute at every campaign, you can activate here.    Only recommended for short posts and really fast servers.  ONLY AVAILABLE AT PRO VERSION.', self :: TEXTDOMAIN ); ?><br /> 					
						</div><br /> 
						<input class="checkbox" value="1" type="checkbox" disabled /> <?php _e('Enable attempt to <b><i>Get Full Content</i></b> feature', self :: TEXTDOMAIN ); ?><br />
						<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you want to attempt to obtain full items content of a campaign, you can activate here.  Not recommended if you will not use this.  ONLY AVAILABLE AT PRO VERSION.', self :: TEXTDOMAIN ); ?><br /></div>
					<?php endif; ?>
				</div>
			</div>
		
			<div id="advancedfetching" class="postbox">
				<h3 class="hndle"><span><?php _e('Advanced Fetching', self :: TEXTDOMAIN ); ?></span></h3>
				<div class="inside">
					<p></p>
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['force_mysimplepie'],true); ?> name="force_mysimplepie" id="force_mysimplepie" /> <?php _e('Force <b><i>Custom Simplepie Library</i></b>', self :: TEXTDOMAIN ); ?><br />
					<div id="hlpspl" style="padding-left:20px;"><?php _e('Check this if you want to ignore Wordpress Simplepie library. ', self :: TEXTDOMAIN ); ?></div>
					<br /> 

					<p></p>
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['woutfilter'],true); ?> name="woutfilter" id="woutfilter" /> <?php _e('<b><i>Allow option on campaign for skip the content filters</i></b>', self :: TEXTDOMAIN ); ?><br />
					<div id="hlpspl" style="padding-left:20px;"><?php _e('NOTE: It is extremely dangerous to allow unfiltered content because there may be some vulnerability in the source code.', self :: TEXTDOMAIN ); ?>
					<br /><?php _e('See How WordPress Processes Post Content: ', self :: TEXTDOMAIN ); ?><a href="http://codex.wordpress.org/How_WordPress_Processes_Post_Content" target="_blank">http://codex.wordpress.org/How_WordPress_Processes_Post_Content</a>
					<br />
					</div>
					<br /> 

				</div>
			</div>

			<div id="enabledashboard" class="postbox">
				<h3 class="hndle"><span><?php _e('Dashboard widget', self :: TEXTDOMAIN ); ?></span></h3>
				<div class="inside">
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['disabledashboard'],true); ?> name="disabledashboard" id="disabledashboard" /> <?php _e('Disable <b><i>Wordpress Dashboard Widget</i></b>', self :: TEXTDOMAIN ); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?php _e('Check this if you don\'t want display the widget dashboard.  Anyway, only admins will see it.', self :: TEXTDOMAIN ); ?></div>
					<div id="roles" style="margin:10px 20px;"><label><?php _e('Select user roles that can see dashboard widget:', self :: TEXTDOMAIN ); ?></label><br>
					<?php 
						global $wp_roles;
						if(!isset($cfg['roles_widget'])) $cfg['roles_widget'] = array( "administrator" => "administrator" );
						$role_select = '<input type="hidden" name="role_name[]" value="administrator" />';
						foreach( $wp_roles->role_names as $role => $name ) {			
							$name = _x($name, self :: TEXTDOMAIN );
							if ( $role != 'administrator' ) {
								if ( array_search($role, $cfg['roles_widget']) ) {
									$checked = 'checked="checked"';
								}else{
									$checked = '';
								}
							  $role_select .= '<label style="margin:5px;"><input style="margin:0 5px;" ' . $checked . ' type="checkbox" name="role_name[]" value="'.$role .'" />'. $name . '</label>';
							}	
						}
						echo $role_select;
					?>
					</div>
				</div>
			</div>
		
			<div id="disablewpcron" class="postbox">
				<h3 class="hndle"><span><?php _e('Disable WP-Cron', self :: TEXTDOMAIN ); ?></span></h3>
				<div class="inside">
					<input class="checkbox" id="disablewpcron" type="checkbox"<?php checked($cfg['disablewpcron'],true);?> name="disablewpcron" value="1"/> <?php _e('Use Cron job of Hoster and disable WP_Cron', self :: TEXTDOMAIN ); ?><br />
					<div id="hlpcron" style="padding-left:20px;">
					<strong><?php _e('NOTE:', self :: TEXTDOMAIN ); ?></strong> <?php _e('Checking this, deactivate all Wordpress cron schedules.', self :: TEXTDOMAIN ); ?><br /><br />
					<?php _e('You must set up a cron job that calls:', self :: TEXTDOMAIN ); ?><br />
					<span class="coderr b"><i> php -q <?php echo self :: $dir . "app/wpe-cron.php"; ?></i></span><br />
					<?php _e('or URL:', self :: TEXTDOMAIN ); ?> &nbsp;&nbsp;&nbsp;<span class="coderr b"><i><?php echo self :: $uri . "app/wpe-cron.php"; ?></i></span>
					<br /><br />
					<?php _e('If also want to run the wordpress cron with external cron you can set up a cron job that calls:', self :: TEXTDOMAIN ); ?><br />
					<span class="coderr b"><i> php -q <?php echo ABSPATH.'wp-cron.php'; ?></i></span><br /> 
					<?php _e('or URL:', self :: TEXTDOMAIN ); ?> &nbsp;&nbsp;&nbsp;<span class="coderr b"><i><?php echo trailingslashit(get_option('siteurl')).'wp-cron.php'; ?></i></span></div><br /> 
				</div>
			</div>				

				
			<div class="postbox inside">
				<div class="inside">
					<p>
					<input type="submit" class="button-primary" name="submit" value="<?php _e( 'Save settings', self :: TEXTDOMAIN );?>" />
					</p>
				</div>
			</div>
			</div>
			</div>
		</div>
		</form>
	</div>
</div>
<script type="text/javascript" language="javascript">
//jQuery(document).ready(function($){
	jQuery('#imgcache').click(function() {
		if ( true == jQuery('#imgcache').is(':checked')) {
			jQuery('#nolinkimg').fadeIn();
		} else {
			jQuery('#nolinkimg').fadeOut();
		}
	});
	jQuery('#imgattach').click(function() {
		if ( true == jQuery('#imgattach').is(':checked')) {
			jQuery('#featimg').fadeIn();
		} else {
			jQuery('#featimg').fadeOut();
		}
	});
//}
</script>