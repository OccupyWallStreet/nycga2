<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
?>
<div class="item-list-tabs no-ajax events-nav" id="subnav">
    <ul>
        <li class="feed"><a href="<?php bpe_event_group_category_feed_links() ?>" title="RSS Feed"><?php _e( 'RSS', 'events' ) ?></a></li>
		<?php bpe_group_navigation() ?>
		<?php do_action( 'bpe_display_archive_options' ) ?>
    </ul>
    <div class="clear"></div>
</div>