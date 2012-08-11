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

class Buddyvents_Admin_Events extends Buddyvents_Admin_Core
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
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER ), 'admin.php' ) );
		
		parent::__construct();
    }

	/**
	 * Content of the events tab
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
    protected function content()
	{
   		if( isset( $_GET['event'] ) )
			$this->edit_event();

   		elseif( isset( $_GET['action'] ) && $_GET['action'] == 'create' )
			$this->create_event();
		
		else
			$this->events_overview();
	}
	
	/**
	 * Create an event
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	private function create_event()
	{
		?>
        <form action="<?php bpe_event_creation_form_action() ?>" method="post" enctype="multipart/form-data" id="create-event-form" class="standard-form">

            <div class="item-list-tabs no-ajax" id="group-create-tabs">
                <ul>
                    <?php bpe_event_creation_tabs(); ?>
                </ul>
            </div>
        
			<?php if( bpe_is_event_creation_step( bpe_get_option( 'general_slug' ) ) ) : ?>
            
                <?php bpe_load_template( 'events/top-level/steps/general' ) ?>
            
            <?php endif; ?>
            
            <?php if( bpe_is_event_creation_step( bpe_get_option( 'logo_slug' ) ) ) : ?>
        
                <?php bpe_load_template( 'events/top-level/steps/logo' ) ?>
                            
            <?php endif; ?>
        
            <?php if( bpe_is_event_creation_step( bpe_get_option( 'invite_slug' ) ) && bpe_get_option( 'enable_attendees' ) === true ) : ?>
            
                <?php bpe_load_template( 'events/top-level/steps/invite' ) ?>
        
            <?php endif; ?>

            <input type="hidden" name="new_event_id" id="new_event_id" value="<?php echo bpe_get_displayed_event( 'id' ) ?>" />
        
            <?php do_action( 'bpe_custom_create_steps' ) ?>
        
            <?php if( bp_get_avatar_admin_step() != 'crop-image' ) : ?>
                <div class="submit" id="previous-next">
                    <?php if( ! bpe_is_first_event_creation_step() ) : ?>
                        <input type="button" value="&larr; <?php _e( 'Previous Step', 'events' ) ?>" id="event-creation-previous" name="previous" onclick="location.href='<?php bpe_event_creation_previous_link() ?>'" />
                    <?php endif; ?>
            
                    <?php if( ! bpe_is_last_event_creation_step() && ! bpe_is_first_event_creation_step() ) : ?>
                        <input type="submit" value="<?php _e( 'Next Step', 'events' ) ?> &rarr;" id="event-creation-next" name="save-event" />
                    <?php endif;?>
            
                    <?php if( bpe_is_first_event_creation_step() && ! bpe_is_only_create_step() ) : ?>
                        <input type="submit" value="<?php _e( 'Create Event and Continue', 'events' ) ?> &rarr;" id="event-creation-create" name="save-event" />
                    <?php endif; ?>
            
                    <?php if( bpe_is_last_event_creation_step() ) : ?>
                        <input type="submit" value="<?php _e( 'Finish', 'events' ) ?> &rarr;" id="event-creation-finish" name="save-event" />
                    <?php endif; ?>
                </div>
            <?php endif; ?>
         </form>
         <?php
	}

	/**
	 * Edit single event
	 * 
	 * @package Admin
	 * @since 	1.2.4
	 */
    private function edit_event()
	{
		?>
        <div class="item-edit-tabs">
            <ul>
                <?php bpe_event_edit_tabs(); ?>
            </ul>
        </div><!-- .item-list-tabs -->
        
        <form action="<?php bpe_event_edit_form_action() ?>" method="post" enctype="multipart/form-data" id="edit-event-form" class="standard-form">

            <?php if( bpe_is_event_edit_screen( bpe_get_option( 'general_slug' ) ) ) : ?>
           
				<label for="user_id"><?php _e( 'Event Admin User ID', 'events' ) ?></label>
				<input type="text" name="user_id" id="user_id" value="<?php bpe_display_cookie( 'user_id' ) ?>" /><br />
				<small><?php _e( 'Leave empty to use the current user.', 'events' ) ?></small>
				
				<hr />

                <?php bpe_load_template( 'events/single/steps/general' ); ?>                 

            <?php endif; ?>
            
            <?php if( bpe_is_event_edit_screen( bpe_get_option( 'logo_slug' )  ) ) : ?>
            
                <?php bpe_load_template( 'events/single/steps/logo' ); ?>

            <?php endif; ?>

            <?php if( bpe_is_event_edit_screen( bpe_get_option( 'manage_slug' ) ) && bpe_get_option( 'enable_attendees' ) === true ) : ?>
           
                <?php bpe_load_template( 'events/single/steps/attendees' ); ?>

            <?php endif; ?>

            <?php do_action( 'bpe_custom_edit_steps' ) ?>
        
        </form><!-- #edit-event-form -->
		<?php
	}
	
	/**
	 * Events overview
	 * 
	 * @package Admin
	 * @since 	1.2.4
	 */
    private function events_overview()
	{
		$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
		if( $paged < 1 ) $paged = 1;
		
		$search 	= ( isset( $_GET['s'] 	  ) ) ? $_GET['s'] 		: false;
		$user_id 	= ( isset( $_GET['user']  ) ) ? $_GET['user'] 	: false;
		$group_id 	= ( isset( $_GET['group'] ) ) ? $_GET['group'] 	: false;
		$cat 		= ( isset( $_GET['cat']   ) ) ? $_GET['cat'] 	: false;

		$result = bpe_get_events( array(
			'restrict' 		=> false,
			'page' 			=> $paged,
			'per_page' 		=> 20,
			'spam' 			=> 2,
			'search_terms' 	=> $search,
			'user_id' 		=> $user_id,
			'category' 		=> $cat,
			'group_id' 		=> $group_id,
			'future' 		=> false,
			'sort' 			=> 'start_date_desc'
		) );
	
		$page_links = paginate_links( array(
			'base' 		=> add_query_arg( 'paged', '%#%' ),
			'format' 	=> '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' 	=> ceil( $result['total'] / 20 ),
			'current' 	=> $paged
		));

		$page_links_text = sprintf( '<div class="tablenav-pages"><span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s</div>',
				number_format_i18n( ( $paged - 1 ) * 20 + 1 ),
				number_format_i18n( min( $paged * 20, $result['total'] ) ),
				number_format_i18n( $result['total'] ),
				$page_links
		);
		
		$categories = bpe_get_event_categories();
		$users = bpe_get_event_users();
		$groups = bpe_get_event_groups();
		?>
        <form method="get" action="">
            <p class="search-box">
                <label for="event-search-input" class="screen-reader-text"><?php _e( 'Search Events', 'events' ); ?>:</label>
                <input type="hidden" name="page" value="buddyvents" />
                <input type="hidden" name="paged" value="<?php echo $paged ?>" />
                <input type="text" value="<?php echo ( $search ) ? $search : ''; ?>" name="s" id="event-search-input">
                <input type="submit" class="button" value="<?php _e( 'Search Events', 'events' ); ?>">
                <?php if( $search ) : ?>
                <a class="button" href="<?php echo $this->filepath . '&paged='. $paged ?>"><?php _e( 'Clear search', 'events' ) ?></a>
                <?php endif; ?>
            </p>      
        </form>

        <form method="get" action="" id="filter-events">
            <p class="filter-box">
                <input type="hidden" name="page" value="buddyvents" />
                <input type="hidden" name="paged" value="<?php echo $paged ?>" />
                <select name="cat" id="cat">
                    <option value=""><?php _e( 'Category', 'events' ); ?></option>
                    <?php foreach( (array)$categories as $val ) { ?>
                        <option<?php if( $cat == $val->id ) echo ' selected="selected"'; ?> value="<?php echo $val->id ?>"><?php echo $val->name ?></option>
                    <?php } ?>
                </select>
                
                <select name="user" id="user">
                    <option value=""><?php _e( 'User', 'events' ); ?></option>
                    <?php foreach( (array)$users as $val ) { ?>
                        <option<?php if( $user_id == $val->user_id ) echo ' selected="selected"'; ?> value="<?php echo $val->user_id ?>"><?php echo bp_core_get_user_displayname( $val->user_id ) ?></option>
                    <?php } ?>
                </select>

                <select name="group" id="group">
                    <option value=""><?php _e( 'Group', 'events' ); ?></option>
                    <?php foreach( (array)$groups as $val ) { ?>
                        <option<?php if( $group_id == $val->group_id ) echo ' selected="selected"'; ?> value="<?php echo $val->group_id ?>"><?php echo $val->name ?></option>
                    <?php } ?>
                </select>
                
                <input type="submit" class="button" value="<?php _e( 'Filter events', 'events' ); ?>">
                
                <?php if( $cat || $user_id || $group_id ) : ?>
                <a class="button" href="<?php echo $this->filepath . '&paged='. $paged ?>"><?php _e( 'Clear filter', 'events' ) ?></a>
                <?php endif; ?>
				<a class="button" href="<?php echo esc_url( $this->filepath ) ?>&action=create"><?php _e( 'Create event', 'events' ) ?></a>
            </p>      
        </form>

        <form method="post" action="" id="posts-filter">
        
            <?php wp_nonce_field( 'bpe_bulkedit' ) ?>

            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkoption" name="bulkoption">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="spam"><?php _e( 'Set as spam', 'events' ); ?></option>
                        <option value="nospam"><?php _e( 'Not spam', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
                        <?php do_action( 'bpe_add_bulkedit_options' ) ?>
                    </select>
                    <?php do_action( 'bpe_add_bulkedit_fields' ) ?>
                    <input type="submit" class="button-secondary action" name="bulkedit-submit" id="bulkedit-submit" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
            
            <table cellspacing="0" class="widefat post fixed">
                <thead>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'Title', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Creator', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Group', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Category', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Attendees', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Start Date', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Spam', 'events' ); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));"></th>
                        <th class="manage-column" scope="col"><?php _e( 'Title', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Creator', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Group', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Category', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Attendees', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Start Date', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Spam', 'events' ); ?></th>
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
                            <td><strong><a href="<?php echo $this->filepath . '&paged='. $paged .'&event='. bpe_get_event_id( $event ); ?>"><?php echo apply_filters( 'bpe_event_overview_name', bpe_get_event_name( $event ), $event ) ?></a></strong></td>
                            <td><?php echo bp_core_get_userlink( bpe_get_event_user_id( $event ) ) ?></td>
                            <td><?php if( bpe_get_event_group_id( $event ) ) { printf( __( '<a href="%s">%s</a>', 'events' ), bpe_event_get_group_permalink( $event ), bpe_event_get_group_name( $event ) ); } else { _e( 'n/a', 'events' ); } ?></td>
                            <td><?php bpe_event_category( $event ) ?></td>
                            <td><?php bpe_event_attendees( $event ) ?></td>
                            <td><?php bpe_event_start_date( $event ) ?></td>
                            <td><?php bpe_event_is_spam( $event ) ?></td>
                        </tr>

                    <?php
                    $counter++;
					endforeach; ?>
                    
				<?php else: ?>
                
                    <tr><td colspan="8" style="text-align:center"><?php _e( 'No events were found.', 'events' ); ?></td></tr>
                    
                <?php endif; ?>
                </tbody>
            </table>
            
            <div class="tablenav">
                <div class="alignleft actions">
                    <select id="bulkoption2" name="bulkoption2">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ); ?></option>
                        <option value="spam"><?php _e( 'Set as spam', 'events' ); ?></option>
                        <option value="nospam"><?php _e( 'Not spam', 'events' ); ?></option>
                        <option value="del"><?php _e( 'Delete', 'events' ); ?></option>
                    </select>
                    <input type="submit" class="button-secondary action" name="bulkedit-submit2" id="bulkedit-submit2" value="<?php _e( 'Apply', 'events' ); ?>" />
                </div>
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
        </form>
		<?php
	}
}
?>