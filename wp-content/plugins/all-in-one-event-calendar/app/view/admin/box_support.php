<div class="ai1ec-plugin-branding timely">

	<h2 class="timely-logo"><a href="http://time.ly/" title="<?php esc_attr_e( 'Timely', AI1EC_PLUGIN_NAME ); ?>" target="_blank"></a></h2>

	<div class="timely-intro">
		<h2>
			<?php _e( 'Timely’s All-in-One Event Calendar is a revolutionary new way to find and share events.', AI1EC_PLUGIN_NAME ); ?>
		</h2>
	</div>

	<div class="ai1ec-follow-fan">
		<div class="ai1ec-facebook-like-top">
			<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
			<fb:like href="http://www.facebook.com/timelycal" layout="button_count" show_faces="true" width="110" font="lucida grande"></fb:like>
		</div>
		<a href="http://twitter.com/_Timely" class="twitter-follow-button"><?php _e( 'Follow @_Timely', AI1EC_PLUGIN_NAME ) ?></a>
		<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
	</div>

	<p></p>
	<h2 class="ai1ec-premium-features">
		<?php _e( 'Upgrade to Premium for free and get exclusive features:', AI1EC_PLUGIN_NAME ) ?>
	</h2>
	<p></p>
	<h4 class="row-fluid ai1ec-premium-features">
		<div class="span1">&nbsp;</div>
		<ul class="span4">
			<li class="icon-leaf span6"><strong><?php _e( 'Calendar Themes', AI1EC_PLUGIN_NAME ) ?></strong></li>
			<li class="icon-th span6"><strong><?php _e( 'Posterboard View', AI1EC_PLUGIN_NAME ) ?></strong></li>
			<li class="icon-calendar span6"><strong><?php _e( 'Duplicate Events', AI1EC_PLUGIN_NAME ) ?></strong></li>
		</ul>
		<ul class="span5">
			<li class="icon-facebook-sign span6"><strong><?php _e( 'Facebook Event Import/Export', AI1EC_PLUGIN_NAME ) ?></strong></li>
			<li class="icon-map-marker span6"><strong><?php _e( 'Location by Latitude/Longitude', AI1EC_PLUGIN_NAME ) ?></strong></li>
			<li class="icon-star span6"><strong><?php _e( '... and much more!', AI1EC_PLUGIN_NAME ) ?></strong></li>
		</ul>
	</h4>
	<p></p>
	<p class="ai1ec-get-support">
		<a class="btn btn-large btn-primary" href="<?php echo admin_url( 'edit.php?post_type=' . AI1EC_POST_TYPE . '&amp;page=' . AI1EC_PLUGIN_NAME . '-upgrade' ) ?>">
			<i class="icon-download-alt"></i> <?php _e( 'Upgrade to Premium for Free', AI1EC_PLUGIN_NAME ) ?>
		</a>
	</p>
	<hr />
	<h4>
		<?php _e( 'Timely is dedicated to creating the best calendar software in the world.', AI1EC_PLUGIN_NAME ) ?>
	</h4>
	<p></p>
	<p>
		<?php _e( 'Please let us know if anything is not working the way you expect. While many problems are caused by conflicts with other plugins, most problems can be solved quickly.  Visit our <a href="http://help.time.ly/" target="_blank">Help Desk</a> to report bugs, request features, or learn how to get the most out of this plugin.', AI1EC_PLUGIN_NAME ) ?>
	</p>
	<div class="ai1ec-get-support">
		<a class="btn btn-large btn-primary" href="http://help.time.ly/" target="_blank">
			<i class="icon-info-sign"></i> <?php _e( 'Get Support', AI1EC_PLUGIN_NAME ) ?>
		</a>
	</div>
	<div id="ai1ec-blog-feed-container">
		<hr />
		<h4><?php _e( 'Timely News', AI1EC_PLUGIN_NAME ); ?> <small><a href="http://time.ly/blog" target="_blank"><?php _e( 'view all news »', AI1EC_PLUGIN_NAME ); ?></a></small></h4>
		<?php if( count( $news ) > 0 ) : ?>
			<?php foreach( $news as $n ) : ?>
				<article>
					<header>
						<strong><a href="<?php echo $n->get_permalink() ?>" target="_blank"><?php echo $n->get_title() ?></a></strong>
					</header>
					<?php echo preg_replace( '/\s+?(\S+)?$/', '', substr( $n->get_description(), 0, 100 ) ); ?> …
				</article>
			<?php endforeach ?>
		<?php else : ?>
			<p><em>No news available.</em></p>
		<?php endif ?>
	</div>
</div>
<br class="clear" />
