<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

do_action( 'bpe_before_grid_view' ) ?>

<li id="event-<?php bpe_event_id() ?>" class="grid-view <?php bpe_event_status_class() ?>">

    <div class="item-avatar">
        <a href="<?php bpe_event_link() ?>"><?php bpe_event_image() ?></a>
    </div>
    
    <div class="grid-item">
    	<a id="colorbox-<?php bpe_event_id() ?>" rel="event" class="zoomin" href="#"></a>
        <a href="<?php bpe_event_link() ?>"><?php bpe_event_name() ?></a>
        <span class="grid-date"><?php bpe_event_start_date() ?> @ <?php bpe_event_start_time() ?><br /><?php bpe_event_location() ?></span>
    </div>
    
</li>

<?php do_action( 'bpe_after_grid_view' ) ?>