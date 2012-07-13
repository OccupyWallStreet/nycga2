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
<div class="item-edit-tabs">
	<ul>
		<?php bpe_bp_gallery_admin_tabs(); ?>
	</ul>
</div>

<?php locate_template( array( 'gallery/single/media/upload-form.php' ), true ); ?>