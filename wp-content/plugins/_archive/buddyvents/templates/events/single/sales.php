<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_sales() ) :	?>
    
    <?php do_action( 'bpe_before_event_ticket_sales' ); ?>
    
	<table id="ticket-sales" class="messages-notices">
        <thead>
            <tr>
                <th width="1%" scope="col">&nbsp;</th>
                <th width="24%" scope="col"><?php _e( 'Buyer', 'events' ) ?></th>
                <th width="15%" scope="col"><?php _e( 'Ticket', 'events' ) ?></th>
                <th width="12%" scope="col"><?php _e( 'Currency', 'events' ) ?></th>
                <th width="11%" scope="col"><?php _e( 'Price', 'events' ) ?></th>
                <th width="10%" scope="col"><?php _e( 'Qty', 'events' ) ?></th>
                <th width="15%" scope="col"><?php _e( 'Commission', 'events' ) ?></th>
                <th width="12%" scope="col" class="sub"><?php _e( 'Sub Total', 'events' ) ?></th>
            </tr>
        </thead>
        <tfoot>
        	<?php foreach( bpe_sale_get_currencies() as $currency ) : ?>
            <tr>
                <th colspan="4" scope="col">&nbsp;</th>
                <th colspan="2" scope="col"><?php printf( __( 'Totals (%s)', 'events' ), $currency ) ?></th>
                <th scope="col"><?php bpe_sale_event_commission( false, $currency ) ?></th>
                <th scope="col" class="sub"><?php bpe_sale_total( false, $currency ) ?></th>
            </tr>
            <?php endforeach; ?>
        </tfoot>
    	<tbody>
			<?php while ( bpe_sales() ) : bpe_the_sale(); ?>
            <tr class="<?php bpe_sale_css_class() ?>">
                <td><?php bpe_sale_buyer_avatar() ?></td>
                <td><?php bpe_sale_buyer_link() ?></td>
                <td><?php bpe_sale_ticket_name() ?></td>
                <td><?php bpe_sale_currency() ?></td>
                <td><?php bpe_sale_single_price() ?></td>
                <td><?php bpe_sale_quantity() ?></td>
                <td><?php bpe_sale_commission( false, true ) ?></td>
                <td class="sub"><?php bpe_sale_subtotal() ?></td>
            </tr>
        	<?php endwhile; ?>
   		</tbody>
	</table>

    <?php do_action( 'bpe_after_event_ticket_sales' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No sales were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>