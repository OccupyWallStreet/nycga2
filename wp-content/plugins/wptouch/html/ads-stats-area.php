<?php global $wptouch_settings; ?>

<div class="metabox-holder">
	<div class="postbox">
		<h3><span class="adsense-options">&nbsp;</span><?php _e( "Adsense, Stats &amp; Custom Code", "wptouch" ); ?></h3>

			<div class="left-content">
				<h4><?php _e( "Adsense", "wptouch" ); ?></h4>
					<p><?php _e( "Enter your Google AdSense ID if you'd like to support mobile advertising in WPtouch posts.", "wptouch" ); ?></p>
					<p><?php _e( "Make sure to include the 'pub-' part of your ID string.", "wptouch" ); ?></p>
				<br />
			    <h4><?php _e( "Stats &amp; Custom Code", "wptouch" ); ?></h4>
			 		<p><?php _e( "If you'd like to capture traffic statistics ", "wptouch" ); ?><br /><?php _e( "(Google Analytics, MINT, etc.)", "wptouch" ); ?></p>
			 		<p><?php _e( "Enter the code snippet(s) for your statistics tracking.", "wptouch" ); ?> <?php _e( "You can also enter custom CSS &amp; other HTML code.", "wptouch" ); ?> <a href="#css-info" class="fancylink">?</a></p>
			 		<div id="css-info" style="display:none">
					<h2><?php _e( "More Info", "wptouch" ); ?></h2>
					<p><?php _e( "You may enter a custom css file link easily. Simply add the full link to the css file like this:", "wptouch" ); ?></p>
					<p><?php _e( "<code>&lt;style type=&quot;text/css&quot;&gt;#mydiv { color: red; }&lt;/style&gt;</code>", "wptouch" ); ?></p>
				</div>

			</div><!-- left content -->

			<div class="right-content">
				<ul class="wptouch-make-li-italic">
					<li><input name="adsense-id" type="text" value="<?php echo $wptouch_settings['adsense-id']; ?>" /><?php _e( "Google AdSense ID", "wptouch" ); ?></li>
					<li><input name="adsense-channel" type="text" value="<?php echo $wptouch_settings['adsense-channel']; ?>" /><?php _e( "Google AdSense Channel", "wptouch" ); ?></li>
				</ul>
			
				<textarea id="wptouch-stats" name="statistics"><?php echo stripslashes($wptouch_settings['statistics']); ?></textarea>

						</div><!-- right content -->
		<div class="bnc-clearer"></div>
	</div><!-- postbox -->
</div><!-- metabox -->