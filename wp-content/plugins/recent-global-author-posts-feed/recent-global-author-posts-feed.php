<?php
/*
Plugin Name: Recent Global Author Posts Feed
Plugin URI:
Description: RSS2 feeds
Author: Andrew Billits (Incsub) / Barry (Incsub)
Version: 2.3
Author URI:
WDP ID: 87
*/

/*
Copyright 2007-2009 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (empty($wp)) {
	require_once('../wp-load.php');
	wp('feed=rss2');
}

/* -------------------- Update Notifications Notice -------------------- */
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
      echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
  }
}
/* --------------------------------------------------------------------- */

//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//
$number = $_GET['number'];
if ( empty( $number ) ) {
	$number = '25';
}

$author = $_GET['author'];
if ( empty( $author ) ) {
	$author = '0';
}

$posttype = $_GET['posttype'];
if(empty($posttype)) {
	$posttype = 'post';
}

//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//
global $wpdb;

$query = $wpdb->prepare("SELECT * FROM " . $wpdb->base_prefix . "site_posts WHERE site_id = %d AND post_author = %s AND blog_public = '1' AND post_type = %s ORDER BY post_published_gmt DESC LIMIT %d", $current_site->id, $author, $posttype, $number);
$posts = $wpdb->get_results( $query, ARRAY_A );

if ( count( $posts ) > 0 ) {
	$last_published_post_date_time = $wpdb->get_var( $wpdb->prepare("SELECT post_published_gmt FROM " . $wpdb->base_prefix . "site_posts WHERE site_id = %d AND post_author = %s AND blog_public = '1' AND post_type = %s ORDER BY post_published_gmt DESC LIMIT 1", $current_site->id, $author, $posttype ) );
} else {
	$last_published_post_date_time = time();
}

if ( $author > 0 ) {
	$author_user_login = $wpdb->get_var( $wpdb->prepare("SELECT user_login FROM " . $wpdb->base_prefix . "users WHERE ID = %s", $author ) );
}

header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
>

<channel>
	<title><?php bloginfo_rss('name'); ?> <?php echo $author_user_login . ' '; ?><?php _e('Posts'); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $last_published_post_date_time, false); ?></pubDate>
	<?php the_generator( 'rss2' ); ?>
	<language><?php echo get_option('rss_language'); ?></language>
    <?php
	//--------------------------------------------------------------------//
	if ( count( $posts ) > 0 ) {
		foreach ($posts as $post) {
			$author_display_name = $wpdb->get_var( $wpdb->prepare( "SELECT display_name FROM " . $wpdb->base_prefix . "users WHERE ID = %d", $post['post_author']) );
			?>
			<item>
				<title><?php echo apply_filters( 'the_title_rss', $post['post_title']); ?></title>
				<link><?php echo $post['post_permalink']; ?></link>
				<comments><?php echo $post['post_permalink'] . '#comments'; ?></comments>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $post['post_published_gmt'], false); ?></pubDate>
				<dc:creator><?php echo $author_display_name; ?></dc:creator>
				<guid isPermaLink="false"><?php echo $post['post_permalink']; ?></guid>
                <description><![CDATA[<?php echo apply_filters('the_excerpt_rss', wp_trim_excerpt($post['post_content'])); ?>]]></description>
                <content:encoded><![CDATA[<?php echo apply_filters('the_content_feed', $post['post_content'], 'rss2'); ?>]]></content:encoded>
				<wfw:commentRss><?php echo $post['post_permalink'] . 'feed/'; ?></wfw:commentRss>
			</item>
			<?php
		}
	}
	//--------------------------------------------------------------------//
	?>
</channel>
</rss>
<?php
//------------------------------------------------------------------------//
?>
