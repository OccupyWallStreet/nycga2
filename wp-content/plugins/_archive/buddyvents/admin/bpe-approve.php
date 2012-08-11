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
 
class Buddyvents_Admin_Approve extends Buddyvents_Admin_Core
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
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-approve' ), 'admin.php' ) );
		
		parent::__construct();
    }

	/**
	 * Content of the approve tab
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	protected function content()
	{
		$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
		if( $paged < 1 ) $paged = 1;

		$result = bpe_get_events( array(
			'restrict' => false,
			'per_page' => 40,
			'page' 	   => 1,
			'future'   => false,
			'approved' => 0
		) );

		$page_links = paginate_links( array(
			'base' 		=> add_query_arg( 'paged', '%#%' ),
			'format' 	=> '',
			'prev_text' => __( '&laquo;' ),
			'next_text' => __( '&raquo;' ),
			'total' 	=> ceil( $result['total'] / 20 ),
			'current' 	=> $paged
		));

		$page_links_text = sprintf( '<div class="tablenav-pages"><span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s</div>',
				number_format_i18n( ( $paged - 1 ) * 20 + 1 ),
				number_format_i18n( min( $paged * 20, $result['total'] ) ),
				number_format_i18n( $result['total'] ),
				$page_links
		);
		?>
		<form method="post" action="" id="posts-filter">
		
			<?php wp_nonce_field( 'bpe_approve_event_actions' ) ?>

            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkapprove" name="bulkapprove">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="approve"><?php _e( 'Approve', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="bulkapprove-submit" id="bulkapprove-submit" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
		
			<table id="bpe-approve-events" cellspacing="0" class="widefat post fixed">
				<thead>
					<tr>
                       	<th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
						<th class="manage-column" scope="col"><?php _e( 'Title', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Creator', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Group', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Category', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Date created', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Actions', 'events' ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
                       	<th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
						<th class="manage-column" scope="col"><?php _e( 'Title', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Creator', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Group', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Category', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Date created', 'events' ); ?></th>
						<th class="manage-column" scope="col"><?php _e( 'Actions', 'events' ); ?></th>
					</tr>
				</tfoot>
				<tbody>
				<?php if( ! empty( $result['events'] ) ) : ?>            
				
					<?php
                    $counter = 1;
                    foreach( $result['events'] as $event ) :
		
						$class = ( $counter % 2 == 0 ) ? '' : 'alternate';
						?>
                        <tr class="<?php echo $class ?>">
                        	<td><input name="be[]" type="checkbox" value="<?php echo bpe_get_event_id( $event ) ?>"></td>
							<td><strong><a class="toggle-desc" href="#"><?php bpe_event_name( $event ) ?></a></strong></td>
							<td><?php echo bp_core_get_userlink( bpe_get_event_user_id( $event ) ) ?></td>
							<td><?php if( bpe_get_event_group_id( $event ) ) { printf( __( '<a href="%s">%s</a>', 'events' ), bpe_event_get_group_permalink( $event ), bpe_event_get_group_name( $event ) ); } else { _e( 'n/a', 'events' ); } ?></td>
							<td><?php bpe_event_category( $event ) ?></td>
							<td><?php bpe_event_date_created_be( $event ) ?></td>
							<td>
								<a class="appdec approve" href="<?php echo wp_nonce_url( $this->filepath .'&approved=true&eid='. bpe_get_event_id( $event ), 'bpe_approve_event' ) ?>"><?php _e( 'Approve', 'events' ); ?></a>
								<a class="appdec decline" href="<?php echo wp_nonce_url( $this->filepath .'&approved=false&eid='. bpe_get_event_id( $event ), 'bpe_delete_event' ) ?>"><?php _e( 'Delete', 'events' ); ?></a>
							</td>
						</tr>
						<tr class="bpe-desc <?php echo $class ?>">
							<td colspan="7"><?php bpe_event_description( $event ) ?></td>
						</tr>
		
					<?php
                    $counter++;
					endforeach; ?>
					
				<?php else: ?>
				
					<tr><td colspan="7" style="text-align:center"><?php _e( 'No events to be approved right now.', 'events' ); ?></td></tr>
					
				<?php endif; ?>
				</tbody>
			</table>

            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkapprove2" name="bulkapprove2">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="approve"><?php _e( 'Approve', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="bulkapprove-submit2" id="bulkapprove-submit2" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
		</form>
		<?php
	}
}
?>