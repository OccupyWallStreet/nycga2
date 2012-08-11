<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
do_action( 'bpe_events_before_list_tabs' ) ?>

<div id="object-nav" class="item-list-tabs no-ajax events-nav">
    <ul>
		<li class="feed"><a href="<?php bpe_event_feed_links() ?>" title="RSS Feed"><?php _e( 'RSS', 'events' ) ?></a></li>
 		<?php bpe_main_navigation() ?>
        <?php do_action( 'bpe_events_directory_event_types' ) ?>
        <?php do_action( 'bpe_display_archive_options' ) ?>
    </ul>
    <div class="clear"></div>
</div><!-- .item-list-tabs -->

<?php do_action( 'bpe_events_after_list_tabs' ) ?>