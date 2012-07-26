<?php require_once( compat_get_plugin_dir( 'wptouch' ) . '/include/plugin.php' ); ?>
<?php global $wptouch_settings; global $bnc_wptouch_version; ?>
<?php $version = bnc_get_wp_version(); ?>

<div class="metabox-holder">
	<div class="postbox">
		<h3><span class="plugin-options">&nbsp;</span><?php _e( "Plugin Support &amp; Compatibility", "wptouch" ); ?></h3>

			<div class="left-content">
				<div class="wptouch-version-support">
					<?php
						echo '<p class="wpv">';
						_e( 'WordPress version: ', 'wptouch' );
						echo '' . get_bloginfo('version') . '';
						echo '</p><p class="wptv">';
						echo sprintf( __( 'WPtouch %s support: ', 'wptouch' ), $bnc_wptouch_version );
						if ($version > 3.4) {
							echo sprintf(__( "%sUnverified%s", "wptouch" ), '<span class="caution">','</span>');
						} else if ($version >= 2.9) {
							echo sprintf(__( "%sSupported.%s", "wptouch" ), '<span class="go">','</span>');
						} else {
							echo sprintf(__( "%sUnsupported. Upgrade Required.%s", "wptouch" ), '<span class="red">','</span>');
						}
						echo '</p>';
					?>	
				</div>
				<p><?php _e( "Here you'll find information on plugin compatibility.", "wptouch" ); ?></p>
		</div>
		
		<div class="right-content">
				
			<h4><?php _e( 'Known Plugin Support &amp; Conflicts', 'wptouch' ); ?></h4>
				<div id="wptouch-plugin-content">
					<!-- custom anti spam -->
						<div class="all-good">
							<?php echo sprintf(__('%sPeter\'s Custom Anti-Spam%s is fully supported.', 'wptouch'), '<a href="http://wordpress.org/extend/plugins/peters-custom-anti-spam-image/" target="_blank">','</a>'); ?>
						</div>
		
					<!-- wp spam free -->
						<div class="all-good">
							<?php echo sprintf(__('%sWP Spam Free%s is fully supported.', 'wptouch'), '<a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" target="_blank">','</a>'); ?>
						</div>
					
					<!-- flickr rss -->	  
						<div class="all-good">
							<?php echo sprintf(__('%sFlickrRSS%s: Your photos will automatically show on a page with the slug "photos" if you have it. Fully supported.', 'wptouch'), '<a href="http://eightface.com/wordpress/flickrrss/" target="_blank">','</a>'); ?>
						</div>
					
					<!-- wp cache -->		  
						<div class="sort-of">
							<?php echo sprintf(__('WP Cache is supported, but requires configuration. %sFollow this video tutorial%s for more information.', 'wptouch'), '<a href="http://www.bravenewcode.com/2009/12/video-tutorial-configuring-wptouch-with-wp-super-cache/" target="_blank">','</a>'); ?>
						</div>
								
					<!-- wp super cache -->		  
						<div class="sort-of">
							<?php echo sprintf(__('WP Super Cache is supported, but requires configuration. %sFollow this video tutorial%s for more information.', 'wptouch'), '<a href="http://www.bravenewcode.com/2009/12/video-tutorial-configuring-wptouch-with-wp-super-cache/" target="_blank">','</a>'); ?>
						</div>
						
					<!-- w3 cache -->		  
						<div class="sort-of">
							<?php echo sprintf(__('W3 Total Cache is supported, but requires configuration. %sFollow this video tutorial%s for more information.', 'wptouch'), '<a href="http://nimopress.com/pressed/blog-building-how-to-configure-w3-total-cache-to-work-with-wptouch-for-wordpress/" target="_blank">','</a>'); ?>
						</div>
		
					<!-- wp css -->		  
						<div class="sort-of">
						<?php echo sprintf(__('%sWP CSS%s is supported, but does	not compress WPtouch\'s CSS. WPtouch files are pre-optimized for mobile devices already.', 'wptouch'), '<a href="http://wordpress.org/extend/plugins/wp-css/" target="_blank">','</a>'); ?>
						</div>
		
					<!-- share this -->		  
						<div class="sort-of">
							<?php echo sprintf(__('%sShare This%s is supported, but requires the WPtouch setting "Enable Restrictive Mode" turned on to work properly.', 'wptouch'), '<a href="http://wordpress.org/extend/plugins/share-this/" target="_blank">','</a>'); ?>
						</div>
		
					<!-- wordpress admin bar -->		  
						<div class="sort-of">
						<?php echo sprintf(__('WordPress Admin Bar requires additional configuration to work with WPtouch. %sFollow this comment%s on the developer\'s official site.', 'wptouch'), '<a href="http://www.viper007bond.com/wordpress-plugins/wordpress-admin-bar/#comment-227660" target="_blank">','</a>'); ?>
						</div>
		
					<!-- simple captcha -->		  
						<div class="too-bad">
							<?php echo sprintf(__('%sWP Simple Captcha%s is not currently supported.', 'wptouch'), '<a href="http://wordpress.org/extend/plugins/simple-captcha/" target="_blank">','</a>'); ?>
						</div>
		
					<!-- next gen gallery -->		  
						<div class="too-bad">
							<?php echo sprintf(__('%sNextGEN Gallery%s is not currently supported.', 'wptouch'), '<a href="http://wordpress.org/extend/plugins/nextgen-gallery/" target="_blank">','</a>'); ?>
						</div>
		
					<!-- ajaxed pages comments-->		  
						<div class="too-bad">
							<?php echo sprintf(__('%sYet another ajax paged comments%s (YAAPC) is not currently supported. WPtouch uses its own ajaxed comments. WPtouch Pro supports WP 2.7+ comments out-of-the-box.', 'wptouch'), '<a href="http://wordpress.org/extend/plugins/yaapc/" target="_blank">','</a>'); ?>
						</div>
						
						<!-- Lightview Plus -->
						<div class="too-bad">
							<?php echo sprintf(__('%sLightview Plus%s is not currently supported. Images may not open in a  viewer or separate page.', 'wptouch'), '<a href="http://wordpress.org/extend/plugins/lightview-plus/" target="_blank">','</a>'); ?>
						</div>
				
				</div>				
		</div><!-- right content -->
	<div class="bnc-clearer"></div>
	</div><!-- postbox -->
</div><!-- metabox -->