<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

class Buddyvents_Admin_Invoices extends Buddyvents_Admin_Core
{
	private $filepath;

	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
    public function __construct()
	{
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-invoices' ), 'admin.php' ) );
		
		parent::__construct();
    }

	/**
	 * Content of the content tab
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
    protected function content()
	{
		global $bpe, $wpdb;
		
		$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
		if( $paged < 1 ) $paged = 1;
		
		$user_id  = ( ! empty( $_GET['user']   ) ) ? $_GET['user'] 	 : false;
		$paid 	  = ( ! empty( $_GET['paid']   ) ) ? $_GET['paid'] 	 : false;
		$status   = ( ! empty( $_GET['status'] ) ) ? $_GET['status'] : false;
		$month 	  = ( ! empty( $_GET['month']  ) ) ? $_GET['month']  : false;
		$per_page = 20;

		$result = bpe_get_invoices( array(
			'month' 	=> $month,
			'user_id' 	=> $user_id,
			'settled' 	=> $paid,
			'sent' 		=> $status
		) );

		if( $per_page > 0 ):
			$page_links = paginate_links( array(
				'base' 		=> add_query_arg( 'paged', '%#%' ),
				'format' 	=> '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' 	=> ceil( $result['total'] / $per_page ),
				'current' 	=> $paged
			));
	
			$page_links_text = sprintf( '<div class="tablenav-pages"><span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s</div>',
					number_format_i18n( ( $paged - 1 ) * $per_page + 1 ),
					number_format_i18n( min( $paged * $per_page, $result['total'] ) ),
					number_format_i18n( $result['total'] ),
					$page_links
			);
		endif;

		$users = bpe_get_event_users();
		$months = $wpdb->get_col( $wpdb->prepare( "SELECT month FROM {$bpe->tables->invoices}" ) );
		?>

        <form method="get" action="" id="filter-invoices">
            <p class="filter-box">
                <input type="hidden" name="page" value="buddyvents-invoices" />
                <input type="hidden" name="paged" value="<?php echo $paged ?>" />
                
                <select name="user" id="user">
                    <option value=""><?php _e( 'User', 'events' ); ?></option>
                    <?php foreach( (array)$users as $val ) { ?>
                        <option<?php if( $user_id == $val->user_id ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $val->user_id ) ?>"><?php echo esc_html( bp_core_get_user_displayname( $val->user_id ) ) ?></option>
                    <?php } ?>
                </select>

                <select name="month" id="month">
                    <option value=""><?php _e( 'Month', 'events' ); ?></option>
                    <?php foreach( $months as $m ) : ?>
                   		<option<?php if( $month == $m ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $m ) ?>"><?php echo esc_html( $m ) ?></option>
					<?php endforeach; ?>
                </select>

                <select name="paid" id="paid">
                    <option value=""><?php _e( 'Paid', 'events' ); ?></option>
              		<option<?php if( $paid == 'yes' ) echo ' selected="selected"'; ?> value="yes"><?php _e( 'Settled', 'events' ); ?></option>
              		<option<?php if( $paid == 'no' ) echo ' selected="selected"'; ?> value="no"><?php _e( 'Not settled', 'events' ); ?></option>
                </select>

                <select name="status" id="status">
                    <option value=""><?php _e( 'Status', 'events' ); ?></option>
              		<option<?php if( $status == 'yes' ) echo ' selected="selected"'; ?> value="yes"><?php _e( 'Sent', 'events' ); ?></option>
              		<option<?php if( $status == 'no' ) echo ' selected="selected"'; ?> value="no"><?php _e( 'Not sent', 'events' ); ?></option>
                </select>

                <input type="submit" class="button" value="<?php _e( 'Filter invoices', 'events' ); ?>">
            </p>      
        </form>

        <form method="post" action="" id="posts-filter">
        
            <?php wp_nonce_field( 'bpe_invoice_table' ) ?>

            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="ibulkoption" name="ibulkoption">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="send"><?php _e( 'Send email', 'events' ); ?></option>
                        <option value="paid"><?php _e( 'Set Paid', 'events' ); ?></option>
                        <option value="not-paid"><?php _e( 'Set Not Paid', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="ibulkedit-submit" id="ibulkedit-submit" value="<?php _e( 'Apply', 'events' ); ?>" />

                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
            <table cellspacing="0" class="widefat post fixed">
                <thead>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Month', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Amount', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Paid', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Actions', 'events' ); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Month', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Amount', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Paid', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Actions', 'events' ); ?></th>
                    </tr>
                </tfoot>
                <tbody>
				<?php if( ! empty( $result['invoices'] ) ) : ?>            
                
					<?php
                    $counter = 1;
					foreach( $result['invoices'] as $invoice ) :

						$amount = 0;
						foreach( (array)$invoice->datasets as $sale_entry ) :
							$amount += bpe_sale_get_commission( $sale_entry );
						endforeach;

						$class = ( $counter % 2 == 0 ) ? '' : 'alternate';
						?>
                        <tr class="<?php echo $class ?>">
                            <td><input name="be[]" type="checkbox" value="<?php echo $invoice->id ?>"></td>
                            <td><?php echo bp_core_get_userlink( $invoice->user_id ) ?></td>
                            <td><?php echo $invoice->month ?></td>
                            <td><?php echo $invoice->datasets[0]->currency .' '. esc_html( number_format( $amount, 2 ) ) ?></td>
                            <td><?php echo ( $invoice->settled == true ) ? __( 'Yes', 'events' ) : __( 'Not yet', 'events' );  ?></td>
                            <td><?php echo ( $invoice->sent_date != '0000-00-00 00:00:00' ) ? __( 'Sent', 'events' ) : __( 'To be sent', 'events' ); ?></td>
                            <td>
                            	<a class="appdec sendmail" onclick="javascript:check=confirm('<?php echo esc_js( __( 'Do you really want to do this?', 'events' ) ); ?>');if(check==false) return false;" title="<?php _e( 'Send', 'events' ); ?>" href="<?php echo wp_nonce_url( $this->filepath .'&action=send&invoice='. $invoice->id, 'bpe_sendmail_invoices' ) ?>"><?php _e( 'Send', 'events' ); ?></a>
                                <a class="appdec changestat" onclick="javascript:check=confirm('<?php echo esc_js( __( 'Do you really want to do this?', 'events' ) ); ?>');if(check==false) return false;" title="<?php _e( 'Change Paid Status', 'events' ); ?>" href="<?php echo wp_nonce_url( $this->filepath .'&action=stat&invoice='. $invoice->id, 'bpe_changestat_invoices' ) ?>"><?php _e( 'Change Paid Status', 'events' ); ?></a>
                                <a class="appdec trash" onclick="javascript:check=confirm('<?php echo esc_js( __( 'Do you really want to do this?', 'events' ) ); ?>');if(check==false) return false;" title="<?php _e( 'Delete', 'events' ); ?>" href="<?php echo wp_nonce_url( $this->filepath .'&action=delete&invoice='. $invoice->id, 'bpe_trash_invoices' ) ?>"><?php _e( 'Delete', 'events' ); ?></a>
                                <a class="appdec pdfpreview" title="<?php _e( 'Preview', 'events' ); ?>" href="<?php echo wp_nonce_url( $this->filepath .'&action=preview&invoice='. $invoice->id, 'bpe_pdfpreview_invoices' ) ?>"><?php _e( 'Preview', 'events' ); ?></a>
                            </td>
                       </tr>
        
                    <?php
                    $counter++;
					endforeach; ?>
                    
				<?php else: ?>
                
                    <tr><td colspan="7" style="text-align:center"><?php _e( 'No invoices were found.', 'events' ); ?></td></tr>
                    
                <?php endif; ?>
                </tbody>
            </table>
            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="ibulkoption2" name="ibulkoption2">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="send"><?php _e( 'Send email', 'events' ); ?></option>
                        <option value="paid"><?php _e( 'Set Paid', 'events' ); ?></option>
                        <option value="not-paid"><?php _e( 'Set Not Paid', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="ibulkedit-submit2" id="ibulkedit-submit2" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
        </form>
		<?php
	}
}
?>