<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

do_action( 'bpe_member_map_before_map' ) ?>

<div id="events-overview-map"></div>
<div id="eventsmap-controls">
    <?php bpe_events_map_controls( false, bp_displayed_user_id() ) ?>
</div>

<?php do_action( 'bpe_member_map_after_map' ) ?>