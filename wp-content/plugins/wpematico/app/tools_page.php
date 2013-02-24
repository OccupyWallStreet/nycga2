<?php
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');

$cfg = $this->options;  
if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
	if ( get_magic_quotes_gpc() ) {
		$_POST = array_map( 'stripslashes_deep', $_POST );
	}
	# evaluation goes here
	check_admin_referer('wpematico-tools');
	@$impold = $_POST['impold']==1 ? true : false;
	@$remold = $_POST['remold']==1 ? true : false;
	
	# saving
	if ( $impold ) {
		?><div class="updated"><?php 
			$jobs=get_option('wpematico_jobs'); //load jobdata
			foreach($jobs as $job) {
				
				$campaign = WPeMatico_Campaign_edit :: check_campaigndata($job);
				
				printf(__( 'Adding %1s', self :: TEXTDOMAIN ), $job['name']);
				echo "<br />";
				
				//$date = ($timestamp) ? gmdate('Y-m-d H:i:s', $timestamp + (get_option('gmt_offset') * 3600)) : null;
				$post_id = wp_insert_post(array(
					'post_title' 	          => $job['name'],
					'post_status' 	          => 'publish',
					'post_type' 	          => 'wpematico'
				));
				// Grabo la campaña
				add_post_meta( $post_id, 'campaign_data', $campaign, true )  or
				  update_post_meta( $post_id, 'campaign_data', $campaign );
			}
		
		?><p><?php		
			printf(__( 'All old WPeMatico campaigns was added to current %1s.  Now you can check these campaigns and then delete old campaigns for remove the header warning.', self :: TEXTDOMAIN ), '<a href="'. admin_url( 'edit.php?post_type=wpematico') .'">All Campaigns</a>' );
		?></p>
		</div>		
		<?php
	}elseif ($remold) {
		delete_option( 'wpematico_jobs' );
		?><div class="updated fade"><?php printf(__( 'Cleaned. Go to %1s.', self :: TEXTDOMAIN ), '<a href="'. admin_url( 'edit.php?post_type=wpematico') .'">All Campaigns</a>' ); ?></div><?php 
		//die();
	}
}


$jobs=get_option('wpematico_jobs'); //load old jobdatas

?>

<div class="wrap">
	<h2><?php _e( 'WPeMatico import old version campaigns', self :: TEXTDOMAIN );?></h2>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<form method="post" action="">
		<?php  wp_nonce_field('wpematico-tools'); ?>
		<div id="side-info-column" class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox inside">
					<h3 class="handle"><?php _e( 'About', self :: TEXTDOMAIN );?></h3>
					<div class="inside">
						<p>WPeMatico Free Version <?php echo self :: VERSION ; ?> R<?php echo self :: RELEASE ; ?></p>
						<p><?php _e( 'Thanks for test, use and enjoy this plugin.', self :: TEXTDOMAIN );?></p>
						<p><?php _e( 'If you like it, I really appreciate a donation.', self :: TEXTDOMAIN );?></p>
						<p>
						<input type="button" class="button-primary" name="donate" value="<?php _e( 'Click for Donate', self :: TEXTDOMAIN );?>" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B8V39NWK3NFQU');return false;"/>
						</p>
						<p><?php // _e('Help', self :: TEXTDOMAIN ); ?><a href="#" onclick="javascript:window.open('https://www.paypal.com/ar/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');"><img  src="https://www.paypal.com/es_XC/Marketing/i/logo/bnr_airlines1_205x67.gif" border="0" alt="Paypal Help"></a>
						</p>
						<p></p>
					</div>
				</div>
				<?php /*
				<div class="postbox inside">
					<div class="inside">
						<p>
						<input type="submit" class="button-primary" name="submit" value="<?php _e( 'Submit', self :: TEXTDOMAIN );?>" />
						</p>
					</div>
				</div>	*/ ?>

			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
		
			<?php if ( is_array($jobs) ) : ?>
			<div id="olds" class="postbox">
				<h3 class="hndle"><span><?php _e('Importing Old Campaigns', self :: TEXTDOMAIN ); ?></span></h3>
				<div class="inside">
					<input class="checkbox" value="1" type="checkbox" name="impold" id="impold" /><b>&nbsp;<?php _e('Check this option to restore and add all campaigns from old Version.', self :: TEXTDOMAIN ); ?></b><br />
					<div id="hlpimg" style="padding-left:20px;"><?php _e('After WPeMatico 1.0 the campaigns are saved on database in different way, and now the campaigns are not compatibles with old versions.', self :: TEXTDOMAIN ); ?><br />
					<?php _e('Note: If already have campaigns on current WPeMatico campaigns list, the old campaigns will be added to the current list.', self :: TEXTDOMAIN ); ?><br /><br />
					</div>
					<p></p>
					<p></p>
					<input class="checkbox" value="1" type="checkbox" name="remold" id="remold" /><b>&nbsp;<?php _e('Delete campaigns from prior versions of WPeMatico 1.0.', self :: TEXTDOMAIN ); ?></b><br />
					<div id="hlpatt" style="padding-left:20px;"><?php _e('This option delete all old campaigns from database and remove the ugly header warning.', self :: TEXTDOMAIN ); ?><br />
					<b><?php _e('Warning:', self :: TEXTDOMAIN ); ?></b> <?php _e("This can't be undone. You'll never see this list again.", self :: TEXTDOMAIN ); ?></div>
					
					<div style="margin: 15px 0 15px 30px;background-color: #FFEBE8;border: 1px solid #CC0000;padding: 0 0.6em;max-width: 400px;border-radius: 3px 3px 3px 3px;border-style: solid;border-width: 1px;overflow-y: scroll;max-height: 230px;">
				
					<div style="height: 19px;margin: 5px 0 15px;background-color: #FFFFE0;border: 1px solid #E6DB55;padding: 3px 0.6em;max-width: 400px;border-radius: 3px 3px 3px 3px;border-style: solid;border-width: 1px;font-weight: bold;"><?php _e("Campaigns found and ready to import and/or delete.", self :: TEXTDOMAIN ); ?></div>
					<p style="margin-left:20px;">
					<?php 
						foreach($jobs as $job) {
							$campaign = WPeMatico_Campaign_edit :: check_campaigndata($job);
							echo "{$job['name']}";
							echo "<br />";
						}
					?>
					</p>
					</div>
				</div>
			</div>
				
			<div class="postbox inside">
				<div class="inside">
					<p>
					<input type="submit" class="button-primary" name="submit" value="<?php _e( 'Submit', self :: TEXTDOMAIN );?>" />
					</p>
				</div>
			</div>
			<?php endif; ?>
			</div>
			</div>
		</div>
		</form>
	</div>
</div>
	