<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_invoices() ) : ?>

	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="events-count">
			<?php bpe_invoices_pagination_count() ?>
		</div>

		<div class="pagination-links" id="events-pag">
			<?php bpe_invoices_pagination_links() ?>
		</div>

	</div>

    <?php do_action( 'bpe_member_invoices_before_loop' ) ?>

	<ul id="invoice-list" class="item-list">
	
	<?php while ( bpe_invoices() ) : bpe_the_invoice();	?>
        <li id="invoice-<?php bpe_invoice_id() ?>" class="invoice">
            <div class="item">
                <div class="item-title"><a href="<?php bpe_invoice_view_link() ?>"><?php bpe_invoice_month() ?></a></div>
            </div>
            
            <?php if( ! bpe_invoice_is_unsettled() ) : ?>
            <div class="action">
                <a class="button" href="<?php bpe_invoice_settle_link() ?>"><?php _e( 'Settle invoice', 'events' ) ?></a>
            </div>
            <?php endif; ?>
        </li>
	<?php endwhile; ?>
    
    <?php do_action( 'bpe_member_invoices_inside_loop' ) ?>
	
	</ul>

    <?php do_action( 'bpe_member_invoices_after_loop' ) ?>

	<div id="pag-bottom" class="pagination no-ajax">

		<div class="pag-count" id="events-count">
			<?php bpe_invoices_pagination_count() ?>
		</div>

		<div class="pagination-links" id="events-pag">
			<?php bpe_invoices_pagination_links() ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No invoices were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>