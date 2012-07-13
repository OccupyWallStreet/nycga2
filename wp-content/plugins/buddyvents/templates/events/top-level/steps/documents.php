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
<label for="docs"><?php _e( 'Documents', 'events' ) ?></label>
<input type="file" name="docs[]" id="docs" accept="pdf|doc|txt|docx|xls|pps|ppt|zip">
<small><?php _e( 'You can upload documents (pdf,doc,txt,docx,xls,pps,ppt or zip).<br />Max upload size for each file is 3MB.', 'events' ) ?></small>

<div id="doc-wrapper"></div>

<?php bpe_edit_documents() ?>