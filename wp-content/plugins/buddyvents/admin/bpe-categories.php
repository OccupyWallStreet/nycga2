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
 
class Buddyvents_Admin_Categories extends Buddyvents_Admin_Core
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
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-categories' ), 'admin.php' ) );
		
		parent::__construct();
    }

	/**
	 * Content of the categories tab
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	protected function content()
	{
		$cats = bpe_get_event_categories( true );
		?>
		<div id="ajax-response"></div>
		
		<div id="bpe-categories">
			<form name="bpe-categories-form" id="bpe-categories-form" method="post" action="<?php echo $this->filepath ?>" >
			
				<?php wp_nonce_field( 'bpe_categories', '_wpnonce_save_category' ) ?>
				
				<input type="hidden" name="cat_id" id="cat_id" value="" />
				
				<table id="bpe-cat-settings" class="form-table">
				<tr>
					<th><label for="cat_name"><?php _e( 'Name', 'events' ); ?></label></th>
					<td><input type="text" id="cat_name" name="cat_name" value="" /><br /><small><?php _e( '(required)', 'events' ); ?></small></td>
				</tr>
				<tr>
					<th><label for="cat_slug"><?php _e( 'Slug', 'events' ); ?></label></th>
					<td><input type="text" id="cat_slug" name="cat_slug" value="" /><br /><small><?php _e( '(Can be left empty)', 'events' ); ?></small></td>
				</tr>
				</table>
				<div class="submit">
					<input type="submit" name="addcat" id="addcat" value="<?php _e( 'Add Category', 'events' ) ?> &raquo;"/> <span class="ajax-loader"></span>
					<a style="display:none" class="clear-form button-secondary" href=""><?php _e( 'Clear Form', 'events' ) ?></a>
				</div>
		
			</form>
		</div>
		
		<div id="bpe-categories-list">
			<p><?php _e( 'Deleting a category moves all associated events to the default category.', 'events' ) ?></p>
			<table cellspacing="0" class="widefat tag fixed">
				<thead>
					<tr>
						<th class="manage-column check-column" id="del" scope="col">&nbsp;</th>
						<th class="manage-column column-name" id="name" scope="col"><?php _e( 'Name', 'events' ) ?></th>
						<th class="manage-column column-slug" id="slug" scope="col"><?php _e( 'Slug', 'events' ) ?></th>
						<th class="manage-column column-posts num" id="events" scope="col"><?php _e( 'Events', 'events' ) ?></th>
					</tr>
				</thead>
			
				<tfoot>
					<tr>
						<th class="manage-column check-column" id="del" scope="col">&nbsp;</th>
						<th class="manage-column column-name" id="name" scope="col"><?php _e( 'Name', 'events' ) ?></th>
						<th class="manage-column column-slug" id="slug" scope="col"><?php _e( 'Slug', 'events' ) ?></th>
						<th class="manage-column column-posts num" id="events" scope="col"><?php _e( 'Events', 'events' ) ?></th>
					</tr>
				</tfoot>
			
				<tbody id="the-list">
				<?php if( $cats ) : foreach( $cats as $k => $val ) : ?>
					<tr>
						<td class="check-column"><?php if( $val->id != 1 ) : ?><a id="cat-<?php echo $val->id ?>" class="bpe-delete-category" href="<?php echo wp_nonce_url( $this->filepath, 'bpe_delete_category' ) ?>"></a><?php endif; ?></td>
						<td><a id="editcat-<?php echo $val->id ?>" class="bpe-edit-category" href="<?php echo $this->filepath ?>" title="<?php _e( 'Edit this category', 'events' ) ?>"><?php echo $val->name ?></a> <?php if( $val->id == 1 ) : _e( '(default)', 'events' ); endif; ?></td>
						<td id="catslug-<?php echo $val->id ?>"><?php echo $val->slug ?></td>
						<td id="num-<?php echo $val->id ?>" class="num"><?php echo $val->amount ?></td>
					</tr>
				<?php endforeach; endif; ?>                
				</tbody>
			</table>
		</div>
		<?php
	}
}
?>