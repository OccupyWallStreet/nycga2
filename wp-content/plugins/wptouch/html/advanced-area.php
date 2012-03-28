<?php global $wptouch_settings; ?>

<div class="metabox-holder">
	<div class="postbox">
		<h3><span class="advanced-options">&nbsp;</span><?php _e( "Advanced Options", "wptouch" ); ?></h3>

		<div class="left-content">
			<p><?php _e( "Choose to enable/disable advanced features &amp; options available for WPtouch.", "wptouch"); ?></p>	
			<p><?php _e( "* WPtouch Restricted Mode attempts to fix issues where other plugins load scripts which interfere with WPtouch CSS and JavaScript.", "wptouch" ); ?></p>
		 	<br />
		 	<h4><?php _e( "Custom User-Agents", "wptouch" ); ?></h4>
		 	<p><?php _e( "Enter a comma-separated list of user-agents to enable WPtouch for a device that isn't currently officially supported.", "wptouch" ); ?></p>
		 	<p><?php echo sprintf( __( "The currently enabled user-agents are: <em class='supported'>%s</em>", "wptouch" ), implode( ", ", bnc_wptouch_get_user_agents() ) ); ?></p>
		</div><!-- left-content -->
	
	<div class="right-content">
		<ul>
			<li>
				<input class="checkbox" type="checkbox" name="enable-zoom" <?php if ( isset( $wptouch_settings['enable-zoom']) && $wptouch_settings['enable-zoom'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-zoom"><?php _e( "Allow zooming on content", "wptouch" ); ?> <a href="#zoom-info" class="fancylink">?</a></label>
				<div id="zoom-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will allow users to zoom in and out on content.", "wptouch" ); ?></p>
				</div>
			</li>
<!-- 			<li>
				<input class="checkbox" type="checkbox" name="enable-fixed-header" <?php if ( isset( $wptouch_settings['enable-fixed-header']) && $wptouch_settings['enable-fixed-header'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-zoom"><?php _e( "Enable fixed header for iOS 5 devices", "wptouch" ); ?> <a href="#fixed-info" class="fancylink">?</a></label>
				<div id="fixed-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will emulate native applications on iOS devices where the header menu bar will stay fixed at all times when scroling.", "wptouch" ); ?></p>
				</div>
			</li>  -->
			<li>
				<input class="checkbox" type="checkbox" name="enable-cats-button" <?php if ( isset( $wptouch_settings['enable-cats-button']) && $wptouch_settings['enable-cats-button'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-cats-button"><?php _e( "Enable Categories tab in the header", "wptouch" ); ?> <a href="#cats-info" class="fancylink">?</a></label>
				<div id="cats-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will add a 'Categories' tab item in the WPtouch drop-down.", "wptouch" ); ?></p>
					<p><?php _e( "It will display a list of your popular categories.", "wptouch" ); ?></p>
				</div>
			</li>
			<li>
				<input class="checkbox" type="checkbox" name="enable-tags-button" <?php if ( isset( $wptouch_settings['enable-tags-button']) && $wptouch_settings['enable-tags-button'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-tags-button"><?php _e( "Enable Tags tab in the header", "wptouch" ); ?> <a href="#tags-info" class="fancylink">?</a></label>
				<div id="tags-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will add a 'Tags' tab item in the WPtouch drop-down.", "wptouch" ); ?></p>
					<p><?php _e( "It will display a list of your popular tags.", "wptouch" ); ?></p>
				</div>
			</li>
			<li>
				<input class="checkbox" type="checkbox" name="enable-search-button" <?php if (isset($wptouch_settings['enable-search-button']) && $wptouch_settings['enable-search-button'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-search-button"><?php _e( "Enable Search link in the header", "wptouch" ); ?> <a href="#search-info" class="fancylink">?</a></label>
				<div id="search-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will add a 'Search' item in the WPtouch sub header.", "wptouch" ); ?></p>
					<p><?php _e( "It will display an overlay on the title area allowing users to search your website.", "wptouch" ); ?></p>
				</div>
			</li>
			<li>
				<input class="checkbox" type="checkbox" name="enable-login-button" <?php if (isset($wptouch_settings['enable-login-button']) && $wptouch_settings['enable-login-button'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-login-button"><?php _e( "Enable Login/My Account tab in the header", "wptouch" ); ?> <a href="#login-info" class="fancylink">?</a></label>
				<div id="login-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will add a 'Login' tab in the WPtouch sub header beside the Tags and Categories tabs if they are also enabled.", "wptouch" ); ?></p>
					<p><?php _e( "It will display a username/password drop-down, allowing users to login plus be automatically re-directed back to the page they logged in from without seeing the WP admin.", "wptouch" ); ?></p>
					<p><?php _e( "Once logged in, a new 'My Account' button will appear. The 'My Account' button shows useful links depending on the type of account (subscriber, admin, etc.).", "wptouch" ); ?></p>
					<p><?php _e( "NOTE: The Account tab/links will always appear if you have enabled registration on your site or require users to login for comments.", "wptouch" ); ?></p>
				</div>
			</li>
			<li>
				<input class="checkbox" type="checkbox" <?php if (!function_exists( 'gigpress_shows' )) : ?>disabled="true"<?php endif; ?> name="enable-gigpress-button" <?php if (isset($wptouch_settings['enable-gigpress-button']) && $wptouch_settings['enable-gigpress-button'] == 1 && function_exists( 'gigpress_shows' )) echo('checked'); ?> />
				<label class="label" for="enable-show-tweets"> <?php _e( "Display Upcoming Dates link in the header (requires <a href='http://gigpress.com/' target='_blank'>GigPress 2.0.3</a> or higher)", "wptouch" ); ?> <a href="#gigpress-tweet-info" class="fancylink">?</a></label>
					<div id="gigpress-tweet-info" style="display:none">
						<h2><?php _e( "More Info", "wptouch" ); ?></h2>
						<p><?php _e( "When this option is checked and the GigPress plugin is installed, a list of your Upcoming Shows will be viewable from a drop-down in the WPtouch header.", "wptouch" ); ?></p>
					</div>
			</li>	
			<li>
				<input class="checkbox" type="checkbox" <?php if (!function_exists( 'wordtwit_get_recent_tweets' )) : ?>disabled="true"<?php endif; ?> name="enable-show-tweets" <?php if (isset($wptouch_settings['enable-show-tweets']) && $wptouch_settings['enable-show-tweets'] == 1 && function_exists( 'wordtwit_get_recent_tweets' )) echo('checked'); ?> />
				<label class="label" for="enable-show-tweets"> <?php _e( "Display Twitter link in the header (requires <a href='http://www.bravenewcode.com/wordtwit/' target='_blank'>WordTwit 2.3.3</a> or higher)", "wptouch" ); ?> <a href="#ajax-tweet-info" class="fancylink">?</a></label>
					<div id="ajax-tweet-info" style="display:none">
						<h2><?php _e( "More Info", "wptouch" ); ?></h2>
						<p><?php _e( "When this option is checked and the WordTwit plugin is installed, a list of your Tweets will be viewable from a drop-down in the WPtouch header.", "wptouch" ); ?></p>
					</div><br /><br />
			</li>	
			<li>
			<input class="checkbox" type="checkbox" name="enable-show-comments" <?php if (isset($wptouch_settings['enable-show-comments']) && $wptouch_settings['enable-show-comments'] == 1) echo('checked'); ?> />
			<label class="label" for="enable-show-comments"> <?php _e( "Enable comments on posts", "wptouch" ); ?> <a href="#page-coms-info" class="fancylink">?</a></label>
				<div id="page-coms-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "If unchecked, this will hide all commenting features on posts and blog listings.", "wptouch" ); ?></p>
				</div>
			</li>			
			<?php //If we actually have pages, show this option 
			if ( count( $pages ) ) { ?>
			<li>
			<input class="checkbox" type="checkbox" name="enable-page-coms" <?php if ( isset($wptouch_settings['enable-page-coms']) && $wptouch_settings['enable-page-coms'] == 1 ) echo('checked'); ?> />
			<label class="label" for="enable-page-coms"> <?php _e( "Enable comments on pages", "wptouch" ); ?> <a href="#page-coms-info" class="fancylink">?</a></label>
				<div id="page-coms-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "This will add the comment form to all pages with 'Allow Comments' checked in your WordPress admin.", "wptouch" ); ?></p>
				</div>
			</li>
			<?php } ?>
			<li>
				<input class="checkbox" type="checkbox" <?php if ( isset($wptouch_settings['enable-show-comments']) && $wptouch_settings['enable-show-comments'] == 0 ) echo ('disabled="true"');?> name="enable-gravatars" <?php if (isset($wptouch_settings['enable-gravatars']) && $wptouch_settings['enable-gravatars'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-gravatars"> <?php _e( "Enable gravatars in comments", "wptouch" ); ?></label>
			</li>
			<li>			
			<br />
				<input class="checkbox" type="checkbox" name="enable-regular-default" <?php if (isset($wptouch_settings['enable-regular-default']) && $wptouch_settings['enable-regular-default'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-regular-default"><?php echo sprintf(__( "1%sst%s visit mobile users will see desktop theme", "wptouch" ), '<sup>','</sup>'); ?> <a href="#reg-info" class="fancylink">?</a></label>
				<div id="reg-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "When this option is checked, users will see your regular site theme first, and have the option in your footer to switch to the WPtouch mobile view.", "wptouch" ); ?></p>
					<p><?php _e( "They'll be able to change back and forth either way. Make sure you have the wp_footer(); function call in your regular theme's footer.php file for the switch link to work properly.", "wptouch" ); ?></p>
				</div>
			</li>
			<li>
				<input class="checkbox" type="checkbox" name="enable-exclusive" <?php if (isset($wptouch_settings['enable-exclusive']) && $wptouch_settings['enable-exclusive'] == 1) echo('checked'); ?> />
				<label class="label" for="enable-exclusive"> <?php _e( "Enable WPtouch Restricted Mode", "wptouch" ); ?> <a href="#restricted-info" class="fancylink">?</a></label>
					<div id="restricted-info" style="display:none">
						<h2><?php _e( "More Info", "wptouch" ); ?></h2>
						<p><?php _e( "Disallow other plugins from loading scripts into WPtouch's header and footer.", "wptouch" ); ?></p> 
						<p><?php _e( "Sometimes fixes incompatibilities and speeds up WPtouch.", "wptouch" ); ?></p>
						<p><?php _e( "Some plugins load conflicting javascript, extra CSS style sheets, and other functional code into your theme to accomplish what they add to your site. As WPtouch works complete on its own without any other plugin installed, in some cases (where you have several plugins or find something doesn't work right with WPtouch) you may want to enable Restricted Mode to ensure that WPtouch works properly, and loads quickly for mobile users.", "wptouch" ); ?></p>
					</div>

	<ul class="wptouch-make-li-italic">
					<li>
						<input type="text" name="custom-user-agents" value="<?php if ( isset( $wptouch_settings['custom-user-agents'] ) ) echo implode( ', ', $wptouch_settings['custom-user-agents'] ); ?>" /><?php _e( "Custom user-agents", "wptouch" ); ?>
						<?php if ( function_exists( 'wpsc_update_htaccess' ) ) { ?>
							<br /><br /><?php _e( "After changing the user-agents, please visit the WP Super Cache admin page and update your rewrite rules.", "wptouch" ); ?>
						<?php } ?>
					</li>
				</ul>
				</li>
			</ul>
		</div><!-- right content -->
	<div class="bnc-clearer"></div>
	</div><!-- postbox -->
</div><!-- metabox -->