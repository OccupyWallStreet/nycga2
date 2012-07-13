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

/**
 * Distance away dropdown
 *
 * @package Core
 * @since 	1.2.3
 */
function bpe_proximity_dropdown()
{
	global $bp;
	
	$show = false;
	
	$dist = ( bpe_get_option( 'system' ) == 'm' ) ? __( 'm', 'events' ) : __( 'km', 'events' );
	
	if( bpe_is_events_directory_loop() && ! bp_current_action() && ! bp_displayed_user_id() || bpe_is_events_active() && ! bpe_is_single_event() && ! bp_displayed_user_id() )
		$show = true;

	if( bpe_get_option( 'default_tab' ) != bpe_get_option( 'active_slug' ) )
	{
		$slug = bpe_get_option( 'active_slug' ) .'/';
		
		if( ! bpe_is_events_active() )
			$show = false;
	}

	if( ! bpe_loggedin_user_has_location() || bpe_is_single_event() )
		$show = false;

	if( $show === true ) :
		$prox = ( isset( $_GET['prox'] ) ) ? $_GET['prox'] : false;
	?>
    <li class="bpe-sorter">
        <span class="sort-title"><?php _e( 'Within:', 'events' ) ?></span>
        <select id="within-sorter" class="sorter">
            <option<?php if( ! $prox ) echo ' selected="selected"'; ?> value="<?php echo bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. $slug ?>">----</option>
            <option<?php if( $prox == 10 ) echo ' selected="selected"'; ?> value="?prox=10">10 <?php echo $dist ?></option>
            <option<?php if( $prox == 25 ) echo ' selected="selected"'; ?> value="?prox=25">25 <?php echo $dist ?></option>
            <option<?php if( $prox == 50 ) echo ' selected="selected"'; ?> value="?prox=50">50 <?php echo $dist ?></option>
            <option<?php if( $prox == 75 ) echo ' selected="selected"'; ?> value="?prox=75">75 <?php echo $dist ?></option>
            <option<?php if( $prox == 100 ) echo ' selected="selected"'; ?> value="?prox=100">100 <?php echo $dist ?></option>
            <option<?php if( $prox == 125 ) echo ' selected="selected"'; ?> value="?prox=125">125 <?php echo $dist ?></option>
            <option<?php if( $prox == 150 ) echo ' selected="selected"'; ?> value="?prox=150">150 <?php echo $dist ?></option>
            <option<?php if( $prox == 200 ) echo ' selected="selected"'; ?> value="?prox=200">200 <?php echo $dist ?></option>
            <option<?php if( $prox == 250 ) echo ' selected="selected"'; ?> value="?prox=250">250 <?php echo $dist ?></option>
            <option<?php if( $prox == 500 ) echo ' selected="selected"'; ?> value="?prox=500">500 <?php echo $dist ?></option>
            <option<?php if( $prox == 750 ) echo ' selected="selected"'; ?> value="?prox=750">750 <?php echo $dist ?></option>
            <?php do_action( 'bpe_proximity_drop_down', $dist ) ?>
        </select>
	</li>
	<?php endif;
}
add_action( 'bpe_user_options', 'bpe_proximity_dropdown', 10 );

/**
 * Timezone dropdown
 *
 * @package Core
 * @since 	1.7
 */
function bpe_timezone_dropdown()
{
	global $bpe;
	
	if( ! bpe_get_config( 'timezones' ) )
		return false;

	$has_data = false;

	if( bp_is_active( 'groups' ) ) :
		if( bp_get_current_group_id() )
		{
			$link = bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/';
			$tz = bp_action_variable( 1 );
			$action = bp_action_variable( 0 );
			
			$has_data = true;
		}
	endif;
	
	if( bp_displayed_user_id() )
	{
		$link = bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/';
		$tz = bp_action_variable( 0 );
		$action = bp_current_action();
		
		$has_data = true;
	}
	
	if( ! $has_data )
	{
		$link = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/';
		$tz = bp_action_variable( 0 );
		$action = bp_current_action();
	}

	?>
    <li class="bpe-sorter">
        <span class="sort-title"><?php _e( 'Timezone:', 'events' ) ?></span>
        <select id="timezone-sorter" class="sorter">
        	<option value="<?php echo bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'; ?>">----</option>
        	<?php foreach( (array) bpe_get_config( 'timezones' ) as $slug => $name ) : ?>
            <option<?php if( $tz == $slug && $action == bpe_get_option( 'timezone_slug' ) ) echo ' selected="selected"'; ?> value="<?php echo $link . bpe_get_option( 'timezone_slug' ) .'/'. $slug .'/'; ?>"><?php echo $name ?></option>
            <?php endforeach; ?>
        </select>
	</li>
	<?php
}
add_action( 'bpe_user_options', 'bpe_timezone_dropdown', 8 );

/**
 * Venue dropdown
 *
 * @package Core
 * @since 	1.7
 */
function bpe_venue_dropdown()
{
	global $bpe;
	
	if( ! bpe_get_config( 'venues' ) )
		return false;
		
	$has_data = false;

	if( bp_is_active( 'groups' ) ) :
		if( bp_get_current_group_id() )
		{
			$link = bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/';
			$tz = bp_action_variable( 1 );
			$action = bp_action_variable( 0 );
			
			$has_data = true;
		}
	endif;
	
	if( bp_displayed_user_id() )
	{
		$link = bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/';
		$tz = bp_action_variable( 0 );
		$action = bp_current_action();

		$has_data = true;
	}
	
	if( ! $has_data )
	{
		$link = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/';
		$tz = bp_action_variable( 0 );
		$action = bp_current_action();
	}

	?>
    <li class="bpe-sorter">
        <span class="sort-title"><?php _e( 'Venue:', 'events' ) ?></span>
        <select id="venue-sorter" class="sorter">
        	<option value="<?php echo bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'; ?>">----</option>
        	<?php foreach( (array) bpe_get_config( 'venues' ) as $slug => $name ) : ?>
            <option<?php if( $tz == $slug && $action == bpe_get_option( 'venue_slug' ) ) echo ' selected="selected"'; ?> value="<?php echo $link . bpe_get_option( 'venue_slug' ) .'/'. $slug .'/'; ?>"><?php echo $name ?></option>
            <?php endforeach; ?>
        </select>
	</li>
	<?php
}
add_action( 'bpe_user_options', 'bpe_venue_dropdown', 9 );

/**
 * Sort events
 *
 * @package Core
 * @since 	1.4
 */
function bpe_events_sorter()
{
	$show = false;
	
	if( ! bpe_is_event_search_results() && ! bpe_is_event_search() && ! bpe_is_events_calendar() && ! bpe_is_events_map() && ! bpe_is_events_create() )
		$show = true;

	if( bpe_get_option( 'default_tab' ) != bpe_get_option( 'active_slug' ) )
	{
		$slug = bpe_get_option( 'active_slug' ) .'/';
		
		if( ! bpe_is_events_active() && ! bpe_is_events_archive() && ! bpe_is_event_category() && ! bpe_is_event_day_archive() && ! bpe_is_event_month_archive() )
			$show = false;
	}
	
	if( bpe_is_single_event() )
		$show = false;
		
	$sort = ( isset( $_GET['sort'] ) ) ? $_GET['sort'] : false;
	
	if( $show === true ) : ?>
    <li class="bpe-sorter">
        <span class="sort-title"><?php _e( 'Order By:', 'events' ) ?></span>
        <select id="order-sorter" class="sorter">
            <option<?php if( $sort == 'end_date_asc' || ! $_GET['sort'] ) echo ' selected="selected"' ?> value="?sort=end_date_asc"><?php _e( 'End Date ASC', 'events' ) ?></option>
            <option<?php if( $sort == 'end_date_desc' ) echo ' selected="selected"' ?> value="?sort=end_date_desc"><?php _e( 'End Date DESC', 'events' ) ?></option>
            <option<?php if( $sort == 'start_date_asc' ) echo ' selected="selected"' ?> value="?sort=start_date_asc"><?php _e( 'Start Date ASC', 'events' ) ?></option>
            <option<?php if( $sort == 'start_date_desc' ) echo ' selected="selected"' ?> value="?sort=start_date_desc"><?php _e( 'Start Date DESC', 'events' ) ?></option>
            <option<?php if( $sort == 'date_created_asc' ) echo ' selected="selected"' ?> value="?sort=date_created_asc"><?php _e( 'Creation Date ASC', 'events' ) ?></option>
            <option<?php if( $sort == 'date_created_desc' ) echo ' selected="selected"' ?> value="?sort=date_created_desc"><?php _e( 'Creation Date DESC', 'events' ) ?></option>
            <?php do_action( 'bpe_events_directory_order_options' ) ?>
        </select>
	</li>
	<?php endif;
}
add_action( 'bpe_user_options', 'bpe_events_sorter', 11 );

/**
 * Display all categories in a dropdown (used in templates)
 *
 * @package Core
 * @since 	1.1
 */
function bpe_template_category_dropdown()
{
	$categories = bpe_get_event_categories();
	
	$has_data = false;
	
	if( bp_is_active( 'groups' ) ) :
		if( bp_get_current_group_id() )
		{
			$link = bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/';
			$slug = bp_action_variable( 1 );
			$action = bp_action_variable( 0 );
			
			$has_data = true;
		}
	endif;
	
	if( bp_displayed_user_id() )
	{
		$link = bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/';
		$slug = bp_action_variable( 0 );
		$action = bp_current_action();

		$has_data = true;
	}

	if( ! $has_data )
	{
		$link = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/';
		$slug = bp_action_variable( 0 );
		$action = bp_current_action();
	}
	?>
    <li class="bpe-sorter">
        <span class="sort-title"><?php _e( 'Category:', 'events' ) ?></span>
        <select id="cat-sorter" class="sorter">
            <option value="<?php echo $link ?>">----</option>
            <?php foreach( $categories as $key => $val ) { ?>
                <option<?php if( $slug == $val->slug && $action == bpe_get_option( 'category_slug' ) ) echo ' selected="selected"'; ?> value="<?php echo $link . bpe_get_option( 'category_slug' ) .'/'. $val->slug .'/' ?>"><?php echo $val->name ?></option>
            <?php } ?>
        </select>
    </li>
	<?php
}
add_action( 'bpe_user_options', 'bpe_template_category_dropdown', 5 );

/**
 * Switch view (list/grid)
 *
 * @package Core
 * @since 	1.7
 */
function bpe_list_choices()
{
	if( count( bpe_get_config( 'view_styles' ) ) <= 1 )
		return false;

	$link = '';

	if( bp_is_active( 'group' ) )
		if( bp_get_current_group_id() )
			$link = 'group/'. bp_get_current_group_id() .'/';
		
	if( bp_displayed_user_id() )
		$link = 'user/'. bp_displayed_user_id() .'/';

	?>
    <li class="bpe-sorter">
        <span class="sort-title"><?php _e( 'View:', 'events' ) ?></span>
        <?php if( in_array( bpe_get_option( 'list_slug' ), bpe_get_config( 'view_styles' ) ) ) : ?>
	        <a class="grid-style<?php bpe_view_class( bpe_get_option( 'grid_slug' ) ) ?>" title="<?php printf( __( 'Change to %s style', 'events' ), bpe_get_option( 'grid_slug' ) ) ?>" href="<?php bpe_view_link( bpe_get_option( 'grid_slug' ) ) ?><?php echo $link ?>"></a>
		<?php endif; ?>
        <?php if( in_array( bpe_get_option( 'grid_slug' ), bpe_get_config( 'view_styles' ) ) ) : ?>
	        <a class="list-style<?php bpe_view_class( bpe_get_option( 'list_slug' ) ) ?>" title="<?php printf( __( 'Change to %s style', 'events' ), bpe_get_option( 'list_slug' ) ) ?>" href="<?php bpe_view_link( bpe_get_option( 'list_slug' ) ) ?><?php echo $link ?>"></a>
		<?php endif; ?>
		<?php do_action( 'bpe_list_choices' ) ?>
    </li>
    <?php
}
add_action( 'bpe_user_options', 'bpe_list_choices', 1 );

/**
 * Build the user options
 *
 * @package Core
 * @since 	1.7
 */
function bpe_build_user_options()
{
	$show = apply_filters( 'bpe_build_user_options', true );
	
	if( $show ) :
	?>
    <li class="options-dd last">
    	<a href="#" class="drop" onclick="return false;"><?php _e( 'Options', 'events' ) ?></a>
        <div class="user-dropdowns">
        	<ul>
        		<?php do_action( 'bpe_user_options' ) ?>
            </ul>
        </div>
    </li>
    <?php
	endif;
}
add_action( 'bpe_display_archive_options', 'bpe_build_user_options' );
?>