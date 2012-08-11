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

class Buddyvents_Admin_Apikeys extends Buddyvents_Admin_Core
{
	private $filepath;

	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
    public function __construct()
	{
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-apikeys' ), 'admin.php' ) );
		
		parent::__construct();
    }
	
	/**
	 * Events overview
	 * 
	 * @package Admin
	 * @since 	1.5.1
	 */
    protected function content()
	{
		global $wpdb, $bpe;
		
		$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
		if( $paged < 1 ) $paged = 1;

		$result = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS * FROM {$bpe->tables->api} LIMIT %d, 20", intval( ( $paged - 1 ) * 20) ) );
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
        
            <?php wp_nonce_field( 'bpe_bulk_api_keys' ) ?>

            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkoption" name="bulkoption">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
						<option value="revoke"><?php _e( 'Revoke Access', 'events' ); ?></option>
                        <option value="grant"><?php _e( 'Grant Access', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="bulkapi-submit" id="bulkapi-submit" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
            
            <table cellspacing="0" class="widefat post fixed">
                <thead>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column apikey" scope="col"><?php _e( 'API Key', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'API Access', 'events' ); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column apikey" scope="col"><?php _e( 'API Key', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'API Access', 'events' ); ?></th>
                    </tr>
                </tfoot>
                <tbody>
				<?php if( ! empty( $result ) ) : ?>            
                
					<?php foreach( $result as $api ) : ?>
        
                        <tr>
                            <td><input name="be[]" type="checkbox" value="<?php echo $api->id ?>"></td>
                            <td><?php echo bp_core_get_userlink( $api->user_id ) ?></td>
                            <td class="apikey"><?php echo $api->api_key ?></td>
                            <td><?php echo ( $api->active == 1 ) ? __( 'active', 'events' ) : __( 'inactive', 'events' ); ?></td>
                            <td><?php echo ( $api->active == 1 ) ? '<a href="'. wp_nonce_url( $this->filepath .'&amp;action=revoke&amp;id='. $api->id, 'bpe_revoke_api' ) .'">'. __( 'Revoke', 'events' ) .'</a>' : '<a href="'. wp_nonce_url( $this->filepath .'&amp;action=grant&amp;id='. $api->id, 'bpe_grant_api' ) .'">'. __( 'Grant', 'events' ) .'</a>'; ?></td>
                        </tr>
        
                    <?php endforeach; ?>
                    
				<?php else: ?>
                
                    <tr><td colspan="5" style="text-align:center"><?php _e( 'No api keys were found.', 'events' ); ?></td></tr>
                    
                <?php endif; ?>
                </tbody>
            </table>
            
            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkoption2" name="bulkoption2">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
                        <option value="revoke"><?php _e( 'Revoke Access', 'events' ); ?></option>
                        <option value="grant"><?php _e( 'Grant Access', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="bulkapi-submit2" id="bulkapi-submit2" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
        </form>
		<?php
	}
}
?>