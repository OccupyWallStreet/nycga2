<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

bpe_load_template( 'events/includes/event-header' );
 
if( bpe_has_documents() ) : ?>                

	<ul class="item-list">
		<?php while ( bpe_documents() ) : bpe_the_document(); ?>
			<li class="event-document">
				<div class="doc-title file-icon icon-<?php bpe_document_type() ?>"><a href="<?php bpe_document_url() ?>"><?php bpe_document_name() ?></a></div>
				<div class="doc-desc"><?php bpe_document_description() ?></div>
			</li>
		<?php endwhile; ?>
	</ul>
                
<?php endif; ?>