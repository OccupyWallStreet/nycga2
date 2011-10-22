<?php
/**
 * RSS2 Feed Template for displaying a member's posts' activity
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
header('Status: 200 OK');

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; 
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
>
 */

?>
<channel>
	<title><?php echo bp_site_name() ?> | <?php echo $bp->displayed_user->fullname; ?> | <?php 
		global $bp;
		$type = $bp->active_components[ $bp->current_component ];
		$type_object = get_post_type_object( $type );
		echo $type_object->labels->my_posts; ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo site_url( BP_ACTIVITY_SLUG . '/#' . $bp->{$type}->slugs->my_posts . '/' ) ?></link>
	<description><?php echo $bp->displayed_user->fullname; ?> - <?php printf( '%s' . __('- Public Activity', 'bpcp' ), $type_object->labels->my_posts ); ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s O', bp_activity_get_last_updated(), false); ?></pubDate>
	<generator>http://buddypress.org/?v=<?php echo BP_VERSION ?></generator>
	<language><?php echo get_option('rss_language'); ?></language>

	<?php
		$primary_ids = bpcp_get_user_post_ids();
	?>

	<?php if ( bp_has_activities( 'primary_id=' . $primary_ids . '&max=50&display_comments=threaded' ) ) : ?>
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
			</item>
		<?php endwhile; ?>

	<?php endif; ?>
</channel>
</rss>
