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
