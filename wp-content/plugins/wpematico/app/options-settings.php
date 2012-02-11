<?PHP 
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');

$cfg=get_option('wpematico');
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
<h2><?php _e("WPeMatico Settings", "wpematico"); ?></h2>
<?php wpematico_option_submenues(); ?>

<div class="clear"></div>

<form method="post" action="">
<input type="hidden" name="subpage" value="settings" />
<?php  wp_nonce_field('wpematico-cfg'); ?>

<div id="poststuff" class="metabox-holder has-right-sidebar"> 
	<div class="inner-sidebar">
		<div id="side-sortables" class="meta-box-sortables">
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'wpematico'); ?>" /> 
		</div>
	</div>
	<div class="has-sidebar" >
		<div id="post-body-content" class="has-sidebar-content">
						
			<div id="mailtype" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Send Mail','wpematico'); ?></span></h3>
				<div class="inside">
					<p><b><?PHP _e('Sender Email:','wpematico'); ?></b><br /><input name="mailsndemail" type="text" value="<?PHP echo $cfg['mailsndemail'];?>" class="large-text" /></p>
					<p><b><?PHP _e('Sender Name:','wpematico'); ?></b><br /><input name="mailsndname" type="text" value="<?PHP echo $cfg['mailsndname'];?>" class="large-text" /></p>
					<p><b><?PHP _e('Send mail method:','wpematico'); ?></b><br />
					<?PHP 
					echo '<select id="mailmethod" name="mailmethod">';
					echo '<option value="mail"'.selected('mail',$cfg['mailmethod'],false).'>'.__('PHP: mail()','wpematico').'</option>';
					echo '<option value="Sendmail"'.selected('Sendmail',$cfg['mailmethod'],false).'>'.__('Sendmail','wpematico').'</option>';
					echo '<option value="SMTP"'.selected('SMTP',$cfg['mailmethod'],false).'>'.__('SMTP','wpematico').'</option>';
					echo '</select>';
					?></p>
					<label id="mailsendmail" <?PHP if ($cfg['mailmethod']!='Sendmail') echo 'style="display:none;"';?>><b><?PHP _e('Sendmail Path:','wpematico'); ?></b><br /><input name="mailsendmail" type="text" value="<?PHP echo $cfg['mailsendmail'];?>" class="large-text" /><br /></label>
					<label id="mailsmtp" <?PHP if ($cfg['mailmethod']!='SMTP') echo 'style="display:none;"';?>>
					<b><?PHP _e('SMTP Hostname:','wpematico'); ?></b><br /><input name="mailhost" type="text" value="<?PHP echo $cfg['mailhost'];?>" class="large-text" /><br />
					<b><?PHP _e('SMTP Secure Connection:','wpematico'); ?></b><br />
					<select name="mailsecure">
					<option value=""<?PHP selected('',$cfg['mailsecure'],true); ?>><?PHP _e('none','wpematico'); ?></option>
					<option value="ssl"<?PHP selected('ssl',$cfg['mailsecure'],true); ?>>SSL</option>
					<option value="tls"<?PHP selected('tls',$cfg['mailsecure'],true); ?>>TLS</option>
					</select><br />
					<b><?PHP _e('SMTP Username:','wpematico'); ?></b><br /><input name="mailuser" type="text" value="<?PHP echo $cfg['mailuser'];?>" class="user large-text" /><br />
					<b><?PHP _e('SMTP Password:','wpematico'); ?></b><br /><input name="mailpass" type="password" value="<?PHP echo base64_decode($cfg['mailpass']);?>" class="password large-text" /><br />
					</label>
				</div>
			</div>
		
			<div id="imgs" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Images','wpematico'); ?></span></h3>
				<div class="inside">
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['imgcache'],true); ?> name="imgcache" id="imgcache" /><b>&nbsp;<?PHP _e('Cache Images.','wpematico'); ?></b><br />
					<div id="hlpimg" style="padding-left:20px;"><b><?PHP _e('Image Caching','wpematico'); ?>:</b> <?PHP _e('When image caching is on, a copy of every image found in content of every feed (only in &lt;img&gt; tags) is downloaded to the Wordpress UPLOADS Dir.','wpematico'); ?><br />
					<?PHP _e('If not enabled all images will linked to the image owner\'s server, but also make your website faster for your visitors.','wpematico'); ?><br />
					<b><?PHP _e('Caching all images','wpematico'); ?>:</b> <?PHP _e('This featured in the general Settings section, will be overridden for the campaign-specific options.','wpematico'); ?></div>
					<p></p>
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['imgattach'],true); ?> name="imgattach" id="imgattach" /><b>&nbsp;<?PHP _e('Attach Images to posts.','wpematico'); ?></b><br />
					<div id="hlpatt" style="padding-left:20px;"><b><?PHP _e('Image Attaching','wpematico'); ?>:</b> <?PHP _e('By default when image caching is on (and everything is working fine), a copy of every image found is added to Wordpress Media.','wpematico'); ?><br />
					<?PHP _e('If enabled Image Attaching all images will be attached to the owner post in WP media library; but if you see that the job process is too slowly you can deactivate this here.','wpematico'); ?></div>
					
				</div>
			</div>
		
			<div id="enablefeatures" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Enable Features','wpematico'); ?></span></h3>
				<div class="inside">
					<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['disabledashboard'],true); ?> name="disabledashboard" id="disabledashboard" /> <?PHP _e('Disable <b><i>Wordpress Dashboard Widget</i></b>','wpematico'); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?PHP _e('Check this if you don\'t want display the widget dashboard.  Anyway, only admins will see it.','wpematico'); ?></div><br /> 	<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enableword2cats'],true); ?> name="enableword2cats" id="enableword2cats" /> <?PHP _e('Enable <b><i>Words to Categories</i></b> feature','wpematico'); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?PHP _e('If you need this feature in every campaign, you can activate here.  Not recommended if you will not use this.','wpematico'); ?></div><br /> 
					<input class="checkbox" value="1" type="checkbox" disabled /> <?PHP _e('Enable <b><i>Keyword Filtering</i></b> feature','wpematico'); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?PHP _e('If you need this feature in every campaign, you can activate here.  ONLY AVAILABLE AT PRO VERSION.','wpematico'); ?><br /> 
					<?PHP _e('This is for exclude or include posts according to the keywords <b>founded</b> at content or title.','wpematico'); ?>
					</div><br /> 
					<input class="checkbox" value="1" type="checkbox" disabled /> <?PHP _e('Enable <b><i>Word count Filters</i></b> feature','wpematico'); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?PHP _e('If you need this feature in every campaign, you can activate here.  ONLY AVAILABLE AT PRO VERSION.','wpematico'); ?><br /> 
					<?PHP _e('This is for cut, exclude or include posts according to the letters o words <b>counted</b> at content.','wpematico'); ?>
					<input class="checkbox" value="1" type="checkbox" disabled /> <?PHP _e('Enable <b><i>Custom Title</i></b> feature','wpematico'); ?><br />
					<div id="hlpw2c" style="padding-left:20px;"><?PHP _e('If you want a custom title for posts of a campaign, you can activate here.  ONLY AVAILABLE AT PRO VERSION.','wpematico'); ?><br /> 					
					</div><br /> 
				</div>
			</div>
		
			<div id="disablewpcron" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Disable WP-Cron','wpematico'); ?></span></h3>
				<div class="inside">
					<input class="checkbox" id="disablewpcron" type="checkbox"<?php checked($cfg['disablewpcron'],true,true);?> name="disablewpcron" value="1"/> <?PHP _e('Use Cron job of Hoster and disable WP_Cron','wpematico'); ?><br />
					<div id="hlpcron" style="padding-left:20px;"><?PHP _e('You must set up a cron job that calls:','wpematico'); ?>
					<i> php -q <?PHP echo ABSPATH.'wp-cron.php'; ?></i><br /> 
					<?PHP _e('or URL:','wpematico'); ?> &nbsp;&nbsp;&nbsp;<i><?PHP echo trailingslashit(get_option('siteurl')).'wp-cron.php'; ?></i></div><br /> 
				</div>
			</div>
		
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'wpematico'); ?>" /> 
		</div>
	</div>
</div>
<p class="submit"> 

</p> 
</form>
</div>
