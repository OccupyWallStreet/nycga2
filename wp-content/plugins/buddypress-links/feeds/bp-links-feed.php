<?php
/**
 * RSS2 Feed Template for displaying the most recent sitewide links.
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
	<?php do_action('bp_directory_links_feed'); ?>
>

<channel>
	<title><?php echo bp_site_name() ?> - <?php _e( 'Most Recent Links', 'buddypress-links' ) ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<atom:link href="<?php echo site_url() . '/' . $bp->links->slug ?>" rel="alternate" type="text/html" />
	<link><?php echo site_url() . '/' . $bp->links->slug ?></link>
	<description><?php _e( 'Most Recent Links Feed', 'buddypress-links' ) ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s O', bp_links_get_last_updated(), false); ?></pubDate>
	<generator>http://buddypress.org/?v=<?php echo BP_VERSION ?></generator>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('bp_directory_links_feed_head'); ?>
	
	<?php if ( bp_has_links( 'type=newest&max=50' ) ) : ?>
		<?php while ( bp_links() ) : bp_the_link(); ?>
			<item>
				<guid><?php bp_link_feed_item_guid() ?></guid>
				<title><?php bp_link_feed_item_title() ?></title>
				<link><?php bp_link_feed_item_link() ?></link>
				<pubDate><?php bp_link_feed_item_date() ?></pubDate>

				<description><?php bp_link_feed_item_description() ?></description>
			<?php do_action('bp_directory_links_feed_item'); ?>
			</item>
		<?php endwhile; ?>

	<?php endif; ?>
</channel>
</rss>