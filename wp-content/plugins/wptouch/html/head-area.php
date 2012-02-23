<?php global $wptouch_settings; ?>
<?php global $bnc_wptouch_version; ?>

<div class="metabox-holder" id="wptouch-head">
	<div class="postbox">
		<div id="wptouch-head-colour">
			<div id="wptouch-head-title">
				<?php WPtouch(); ?>
				<img class="ajax-load" src="<?php echo compat_get_plugin_url('wptouch'); ?>/images/admin-ajax-loader.gif" alt="ajax"/>
			</div>
				<div id="wptouch-head-links">
					<ul>
						<li><?php echo sprintf(__( "%sGet WPtouch Pro%s", "wptouch" ), '<a href="http://www.bravenewcode.com/store/plugins/wptouch-pro/?utm_source=wptouch&amp;utm_medium=web&amp;utm_campaign=top-' . str_replace( '.', '', $bnc_wptouch_version ) . '" target="_blank">','</a>'); ?> | </li>
						<li><?php echo sprintf(__( "%sJoin our FREE Affiliate Program%s", "wptouch" ), '<a href="http://www.bravenewcode.com/affiliate-program/" target="_blank">','</a>'); ?></li> |
						<li><?php echo sprintf(__( "%sFollow Us on Twitter%s", "wordtwit" ), '<a href="http://www.twitter.com/bravenewcode" target="_blank">','</a>'); ?></li> |
						<li><?php echo sprintf(__( "%sFind Us on Facebook%s", "wordtwit" ), '<a href="http://www.facebook.com/bravenewcode" target="_blank">','</a>'); ?></li>
					</ul>
				</div>
	<div class="bnc-clearer"></div>
			</div>	
	
		<div id="wptouch-news-support">

			<div id="wptouch-news-wrap">
			<h3><span class="rss-head">&nbsp;</span><?php _e( "WPtouch Wire", "wptouch" ); ?></h3>
				<div id="wptouch-news-content">
					
				</div>
			</div>

			<div id="wptouch-support-wrap">			
			<h3>&nbsp;</h3>
				<div id="wptouch-support-content">
				<a id="find-out-more" href="http://www.bravenewcode.com/products/wptouch-pro/?utm_source=wptouch&amp;utm_medium=web&amp;utm_campaign=find-out-more-<?php echo str_replace( '.', '', $bnc_wptouch_version ); ?>" target="_blank">&nbsp;</a>
				</div>
			</div>
			
		</div><!-- wptouch-news-support -->

	<div class="bnc-clearer"></div>
	</div><!-- postbox -->
</div><!-- wptouch-head -->
