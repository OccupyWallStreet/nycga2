<?php
/**
 * RSS2 Feed Template for displaying a single link's activity stream
 *
 * @package BuddyPress-Links
 */
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
header('Status: 200 OK');
?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	<?php do_action('bp_links_activity_link_feed'); ?>
>

<channel>
	<title><?php echo $bp->links->current_link->name ?> - <?php _e( 'Link Activity', 'buddypress-links' ) ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo bp_get_link_permalink( $bp->links->current_link ) . $bp->activity->slug . '/feed' ?></link>
	<description><?php printf( __( '%s - Link Activity Feed', 'buddypress-links' ), $bp->links->current_link->name  ) ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s O', bp_activity_get_last_updated(), false); ?></pubDate>
	<generator>http://buddypress.org/?v=<?php echo BP_VERSION ?></generator>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('bp_links_activity_link_feed_head'); ?>

	<?php if ( bp_has_activities( 'object=' . $bp->links->id . '&primary_id=' . $bp->links->current_link->cloud_id . '&max=50&display_comments=threaded' ) ) : ?>
		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<item>
				<guid><?php bp_activity_thread_permalink() ?></guid>
				<title><![CDATA[<?php bp_activity_feed_item_title() ?>]]></title>
				<link><?php echo bp_activity_thread_permalink() ?></link>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s O', bp_get_activity_feed_item_date(), false); ?></pubDate>

				<description>
					<![CDATA[
						<?php bp_activity_feed_item_description() ?>

						<?php if ( bp_activity_can_comment() ) : ?>
							<p><?php printf( __( 'Comments: %s', 'buddypress' ), bp_activity_get_comment_count() ); ?></p>
						<?php endif; ?>
					]]>
				</description>
				<?php do_action('bp_links_activity_link_feed_item'); ?>
			</item>
		<?php endwhile; ?>

	<?php endif; ?>
</channel>
</rss>
