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

class Buddyvents_Admin_Webhooks extends Buddyvents_Admin_Core
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
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-webhooks' ), 'admin.php' ) );
		
		parent::__construct();
    }
	
	/**
	 * Webhook overview
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
    protected function content()
	{
		global $wpdb, $bpe;
		
		$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
		if( $paged < 1 ) $paged = 1;

		$result = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS * FROM {$bpe->tables->webhooks} LIMIT %d, 20", intval( ( $paged - 1 ) * 20) ) );
		$total = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
	
		$page_links = paginate_links( array(
			'base' 		=> add_query_arg( 'paged', '%#%' ),
			'format' 	=> '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' 	=> ceil( $total / 20 ),
			'current' 	=> $paged
		));

		$page_links_text = sprintf( '<div class="tablenav-pages"><span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s</div>',
				number_format_i18n( ( $paged - 1 ) * 20 + 1 ),
				number_format_i18n( min( $paged * 20, $total ) ),
				number_format_i18n( $total ),
				$page_links
		);
		
		?>
        <form method="post" action="" id="posts-filter">
        
            <?php wp_nonce_field( 'bpe_bulk_webhooks' ) ?>

            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkhook" name="bulkhook">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="unverify"><?php _e( 'Unverify', 'events' ); ?></option>
                        <option value="verify"><?php _e( 'Verify', 'events' ); ?></option>
                        <option value="delete"><?php _e( 'Delete', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="bulkhook-submit" id="bulkhook-submit" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
            
            <table cellspacing="0" class="widefat post fixed">
                <thead>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'URL', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Event', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'URL', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Event', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                    </tr>
                </tfoot>
                <tbody>
				<?php if( ! empty( $result ) ) : ?>            
                
					<?php foreach( $result as $webhook ) : ?>
        
                        <tr>
                            <td><input name="be[]" type="checkbox" value="<?php echo $webhook->id ?>"></td>
                            <td><?php echo bp_core_get_userlink( $webhook->user_id ) ?></td>
                            <td><?php echo $webhook->url ?></td>
                            <td><?php echo $webhook->event ?></td>
                            <td><?php echo ( $webhook->verified == 1 ) ? __( 'verified', 'events' ) : __( 'unverified', 'events' ); ?></td>
                        </tr>
        
                    <?php endforeach; ?>
                    
				<?php else: ?>
                
                    <tr><td colspan="5" style="text-align:center"><?php _e( 'No webhooks were found.', 'events' ); ?></td></tr>
                    
                <?php endif; ?>
                </tbody>
            </table>
            
            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkhook2" name="bulkhook2">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="unverify"><?php _e( 'Unverify', 'events' ); ?></option>
                        <option value="verify"><?php _e( 'Verify', 'events' ); ?></option>
                        <option value="delete"><?php _e( 'Delete', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="bulkhook-submit2" id="bulkhook-submit2" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
        </form>
		<?php
	}
}
?>