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
 * Display the proper form action
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_event_creation_form_action()
{
	echo bpe_get_event_creation_form_action();
}
	function bpe_get_event_creation_form_action()
	{
		global $bp;
		
		if( is_admin() )
		{
			$step = ( isset( $_GET['step'] ) ) ? $_GET['step'] : false;
			
			if( ! $step )
				$step = array_shift( array_keys( bpe_get_config( 'creation_steps' ) ) );
	
			return apply_filters( 'bpe_get_event_admin_creation_form_action', admin_url( 'admin.php?page='. EVENT_FOLDER .'&action=create&step='. $step ) );
		}
		else
		{
			if( empty( $bp->action_variables[1] ) )
				$bp->action_variables[1] = array_shift( array_keys( (array) bpe_get_config( 'creation_steps' ) ) );
	
			return apply_filters( 'bpe_get_event_creation_form_action', bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) .'/'. bpe_get_option( 'step_slug' ) .'/' . bp_action_variable( 1 ) );
		}
	}

/**
 * Display the creation tabs
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_event_creation_tabs()
{
	global $bp;

	if( ! is_array( bpe_get_config( 'creation_steps' ) ) )
		return false;

	if( ! bpe_get_config( 'current_create_step' ) )
		$bpe->config->current_create_step = array_shift( array_keys( bpe_get_config( 'creation_steps' ) ) );

	$counter = 1;
	
	foreach( (array)bpe_get_config( 'creation_steps' ) as $slug => $step )
	{
		$url = ( is_admin() ) ? admin_url( 'admin.php?page='. EVENT_FOLDER .'&action=create&step='. $slug ) : bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'create_slug' ) .'/'. bpe_get_option( 'step_slug' ) .'/'. $slug .'/';

		$is_enabled = bpe_are_previous_creation_steps_complete( $slug );

		echo '<li'. ( ( bpe_get_config( 'current_create_step' ) == $slug ) ? ' class="current"' : '' ) .'>'. ( ( $is_enabled ) ? '<a href="'. $url .'">' : '<span>' ) . $counter .'. '. $step['name'] . ( ( $is_enabled ) ? '</a>' : '</span>' ) .'</li>';
		$counter++;
	}

	unset( $is_enabled );

	do_action( 'bpe_event_creation_tabs' );
}

/**
 * Check for all completed creation steps
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_are_previous_creation_steps_complete( $step_slug )
{
	global $bpe;

	if ( array_shift( array_keys( bpe_get_config( 'creation_steps' ) ) ) == $step_slug )
		return true;

	reset( $bpe->config->creation_steps );
	unset( $previous_steps );

	foreach( (array) bpe_get_config( 'creation_steps' ) as $slug => $name ) {
		if( $slug == $step_slug )
			break;

		$previous_steps[] = $slug;
	}

	return bpe_is_creation_step_complete( $previous_steps );
}

/**
 * Check for a completed creation step
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_creation_step_complete( $step_slugs )
{
	if( ! bpe_get_config( 'completed_create_steps' ) )
		return false;

	if( is_array( $step_slugs ) )
	{
		$found = true;

		foreach( (array)$step_slugs as $step_slug )
		{
			if( ! in_array( $step_slug, bpe_get_config( 'completed_create_steps' ) ) )
				$found = false;
		}

		return $found;
	}
	else
		return in_array( $step_slugs, bpe_get_config( 'completed_create_steps' ) );

	return true;
}

/**
 * Get the correct order for the creation steps
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_sort_creation_steps()
{
	global $bpe;

	if( ! is_array( bpe_get_config( 'creation_steps' ) ) )
		return false;

	foreach( bpe_get_config( 'creation_steps' ) as $slug => $step )
	{
		while( ! empty( $temp[$step['position']] ) )
			$step['position']++;

		$temp[$step['position']] = array( 'name' => $step['name'], 'slug' => $slug );
	}

	ksort($temp);
	unset( $bpe->config->creation_steps );

	foreach( (array)$temp as $position => $step )
		$bpe->config->creation_steps[$step['slug']] = array( 'name' => $step['name'], 'position' => $position );
}

/**
 * Process the various steps
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_create_event_action()
{
	global $bpe, $bp;
	
	$page 	= ( isset( $_GET['page']   ) ) ?  $_GET['page']  : '';
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : '';
	$step 	= ( isset( $_GET['step']   ) ) ? $_GET['step']   : '';

	if( is_admin() )
	{
		if( $page != EVENT_FOLDER || $action != 'create' )
			return false;
	}
	else
	{
		if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) || ! bp_is_current_action( bpe_get_option( 'create_slug' ) ) )
			return false;
	}

	if( ! is_user_logged_in() )
		return false;

	bpe_sort_creation_steps();

	$step = ( is_admin() ) ? $step : bp_action_variable( 1 );

	$reset_steps = false;

	if( ! $bpe->config->current_create_step = $step )
	{
		unset( $bpe->config->current_create_step 	);
		unset( $bpe->config->completed_create_steps );

		setcookie( 'bpe_new_event_id', 			 false, time() - 1000, COOKIEPATH );
		setcookie( 'bpe_completed_create_steps', false, time() - 1000, COOKIEPATH );

		$reset_steps = true;
		
		if( is_admin() )
			bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&action=create&step='. array_shift( array_keys( bpe_get_config( 'creation_steps' ) ) ) ) );
		else
			bp_core_redirect( bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) .'/'. bpe_get_option( 'step_slug' ) .'/' . array_shift( array_keys( bpe_get_config( 'creation_steps' ) ) ) . '/' );
	}

	if( $step && ! bpe_get_config( 'creation_steps', $step ) )
	{
		bpe_add_message( __( 'There was an error saving this step. Please try again.', 'events' ), 'error' );
		
		if( is_admin() )
			bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&action=create' ) );
		else
			bp_core_redirect( bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) .'/' );
	}

	if( isset( $_COOKIE['bpe_completed_create_steps'] ) && ! $reset_steps )
		$bpe->config->completed_create_steps = unserialize( stripslashes( $_COOKIE['bpe_completed_create_steps'] ) );

	if( isset( $_COOKIE['bpe_new_event_id'] ) )
	{
		$bpe->config->new_event_id = $_COOKIE['bpe_new_event_id'];
		$result = bpe_get_events( array(
			'ids' 		=> bpe_get_config( 'new_event_id' ),
			'approved' 	=> ( ( bpe_get_option( 'approve_events' ) == true ) ? false : true )
		) );

		$bpe->displayed_event = $result['events'][0];
	}

	if( isset( $_POST['save-event'] ) )
	{
		check_admin_referer( 'bpe_add_event_'. bpe_get_config( 'current_create_step' ) );

		if( bpe_get_config( 'current_create_step' ) == bpe_get_option( 'general_slug' ) ) :
			$user = ( ! empty( $_POST['user_id'] ) && is_admin() ) ? absint( $_POST['user_id'] ) : false;
			
			$bpe->config->new_event_id = bpe_process_event_creation( $_POST, false, $user );
		endif;

		if( bpe_get_config( 'current_create_step' ) == bpe_get_option( 'invite_slug' ) )
			bpe_process_event_invitations( $_POST, bpe_get_displayed_event() );

		do_action( 'bpe_create_event_step_save_' . bpe_get_config( 'current_create_step' ) );
		do_action( 'bpe_create_event_step_complete' );

		if( ! in_array( bpe_get_config( 'current_create_step' ), (array) bpe_get_config( 'completed_create_steps' ) ) )
			$bpe->config->completed_create_steps[] = bpe_get_config( 'current_create_step' );

		@setcookie( 'bpe_new_event_id', bpe_get_config( 'new_event_id' ), time() + 60*60*24, COOKIEPATH );
		@setcookie( 'bpe_completed_create_steps', serialize( bpe_get_config( 'completed_create_steps' ) ), time() + 60*60*24, COOKIEPATH );

		if( count( bpe_get_config( 'completed_create_steps' ) ) == count( bpe_get_config( 'creation_steps' ) ) && bpe_get_config( 'current_create_step' ) == array_pop( array_keys( bpe_get_config( 'creation_steps' ) ) ) )
		{
			unset( $bpe->config->current_create_step );
			unset( $bpe->config->completed_create_steps );

			do_action( 'bpe_event_create_complete', bpe_get_config( 'new_event_id' ) );
			
			@setcookie( 'buddyvents_submission', false, time() - 1000, COOKIEPATH );
			@setcookie( 'buddyvents_schedules', false, time() - 1000, COOKIEPATH );
			
			$event = bpe_get_displayed_event();
			
			if( ! $event ) :
				$event = new Buddyvents_Events( bpe_get_config( 'new_event_id' ) );
			endif;
				

			if( bpe_get_option( 'approve_events' ) == true && ! is_admin() || $event->group_approved == false && ! is_admin() )
			{
				bpe_send_approve_mail( $event );

				do_action( 'bpe_saved_new_event_approve', $event );
				
				bpe_add_message( __( 'Your event has been saved successfully. It will show up in the events list as soon as it has been approved!', 'events' ) );
				bp_core_redirect( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/' );
			}
			else
			{
				// approve before anything else				
				bpe_approve_event( bpe_get_config( 'new_event_id' ) );

				// activity comments are attached here
				do_action( 'bpe_saved_new_event', $event );
				
				bpe_add_message( __( 'Your event was successfully published!', 'events' ) );
				bp_core_redirect( bpe_create_redirect_success_url( $event->slug ) );
			}
		}
		else
		{
			$next = false;
			
			foreach( (array) bpe_get_config( 'creation_steps' ) as $key => $value )
			{
				if( $key == bpe_get_config( 'current_create_step' ) )
				{
					$next = 1;
					continue;
				}

				if( $next )
				{
					$next_step = $key;
					break;
				}
			}

			if( is_admin() )
				bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&action=create&step='. $next_step ) );
			else
				bp_core_redirect( bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) .'/'. bpe_get_option( 'step_slug' ) .'/' . $next_step . '/' );
		}
	}

	if( bpe_get_config( 'current_create_step' ) == bpe_get_option( 'logo_slug' ) && isset( $_POST['upload'] ) )
	{
		if( ! empty( $_FILES ) )
		{
			if( bp_core_avatar_handle_upload( $_FILES, 'bpe_avatar_upload_dir' ) )
			{
				$bp->avatar_admin->step = 'crop-image';
				if( is_admin() )
					bpe_core_add_jquery_cropper();
				else
					add_action( 'wp_print_scripts', 'bp_core_add_jquery_cropper' );
			}
		}
		
		if( isset( $_POST['avatar-crop-submit'] ) )
		{
			if( ! bp_core_avatar_handle_crop( array( 'object' => 'event', 'avatar_dir' => 'event-avatars', 'item_id' => bpe_get_displayed_event( 'id' ), 'original_file' => $_POST['image_src'], 'crop_x' => $_POST['x'], 'crop_y' => $_POST['y'], 'crop_w' => $_POST['w'], 'crop_h' => $_POST['h'] ) ) )
				bpe_add_message( __( 'There was an error saving the event logo, please try uploading again.', 'events' ), 'error' );

			else
				bpe_add_message( __( 'The event logo was uploaded successfully!', 'events' ) );
		}
	}
}
add_action( 'wp', 'bpe_create_event_action', 1 );
add_action( 'admin_init', 'bpe_create_event_action', 0 );

/**
 * Add all necessary jcrop scripts/styles for admin page
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_core_add_jquery_cropper()
{
	wp_enqueue_script( 'jcrop', array( 'jquery' ) );
	add_action( 'admin_head', 'bp_core_add_cropper_inline_js' );
	add_action( 'admin_head', 'bp_core_add_cropper_inline_css' );
}

/**
 * Get the new event avatar
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_new_event_avatar( $args = '' )
{
	echo bpe_get_new_event_avatar( $args );
}
	function bpe_get_new_event_avatar( $args = '' )
	{
		$defaults = array(
			'type' 		=> 'full',
			'width' 	=> false,
			'height' 	=> false,
			'class' 	=> 'avatar',
			'id' 		=> 'avatar-crop-preview',
			'alt' 		=> __( 'Event logo', 'events' ),
			'no_grav' 	=> true
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$url = apply_filters( 'bpe_get_new_event_avatar', bp_core_fetch_avatar( array( 'item_id' => bpe_get_displayed_event( 'id' ), 'object' => 'event', 'type' => $type, 'avatar_dir' => 'event-avatars', 'alt' => $alt, 'width' => $width, 'height' => $height, 'class' => $class, 'no_grav' => $no_grav, 'html' => false ) ) );

		if( strpos( $url, 'bp-core' ) !== false )
			$url = false;
		
		if( ! $url )
		{
			if( bpe_get_option( 'default_avatar', 'mid' ) )
				$url = bp_get_root_domain() . bpe_get_option( 'default_avatar', 'mid' );
			
			else
				$url = bpe_get_config( 'default_logo' );
		}
		
		$avatar = '<img src="'. $url .'" alt="'. $alt .'" width="'. $width .'" height="'. $height .'" class="'. $class .'" id="'. $id .'" />';
		
		return $avatar;
	}

/**
 * Process avatar handling
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_screen_event_edit_avatar()
{
	global $bp;

	if ( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'logo_slug' ), 2 ) || isset( $_GET['page'] ) && $_GET['page'] == EVENT_FOLDER && isset( $_GET['step'] ) && $_GET['step'] == bpe_get_option( 'logo_slug' ) && isset( $_GET['event'] ) )
	{
		if( ! is_super_admin() )
		{
			if( ! $bp->is_item_admin )
				return false;
		}

		if( bp_is_action_variable( 'delete', 3 ) || isset( $_GET['delete'] ) && $_GET['delete'] == 'avatar' )
		{
			check_admin_referer( 'bpe_event_avatar_delete' );

			if( bp_core_delete_existing_avatar( array( 'item_id' => bpe_get_displayed_event( 'id' ), 'object' => 'event', 'avatar_dir' => 'event-avatars' ) ) )
				bp_core_add_message( __( 'Your logo was deleted successfully!', 'events' ) );
			else
				bp_core_add_message( __( 'There was a problem deleting the logo, please try again.', 'events' ), 'error' );
		}

		$bp->avatar_admin->step = 'upload-image';

		if( ! empty( $_FILES ) && isset( $_POST['upload'] ) )
		{
			check_admin_referer( 'bp_avatar_upload' );
			
			if( bp_core_avatar_handle_upload( $_FILES, 'bpe_avatar_upload_dir' ) )
			{
				$bp->avatar_admin->step = 'crop-image';
				
				if( is_admin() )
					bpe_core_add_jquery_cropper();
				else
					add_action( 'wp_print_scripts', 'bp_core_add_jquery_cropper' );
			}
		}

		if( isset( $_POST['avatar-crop-submit'] ) )
		{
			check_admin_referer( 'bp_avatar_cropstore' );
			
			 bp_core_delete_existing_avatar( array( 'item_id' => bpe_get_displayed_event( 'id' ), 'object' => 'event', 'avatar_dir' => 'event-avatars' ) );

			if( ! bp_core_avatar_handle_crop( array( 'object' => 'event', 'avatar_dir' => 'event-avatars', 'item_id' => bpe_get_displayed_event( 'id' ), 'original_file' => $_POST['image_src'], 'crop_x' => $_POST['x'], 'crop_y' => $_POST['y'], 'crop_w' => $_POST['w'], 'crop_h' => $_POST['h'] ) ) )
				bp_core_add_message( __( 'There was a problem cropping the logo, please try uploading it again', 'events' ) );
			else
			{
				do_action( 'bpe_new_event_logo', bpe_get_displayed_event() );
				bp_core_add_message( __( 'The new event logo was uploaded successfully!', 'events' ) );
			}

			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}

		do_action( 'bpe_screen_event_admin_avatar', bpe_get_displayed_event() );
	}
}
add_action( 'wp', 'bpe_screen_event_edit_avatar', 1 );
add_action( 'admin_init', 'bpe_screen_event_edit_avatar',  1 );

/**
 * Event avatar delete link
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_event_avatar_delete_link()
{
	echo bpe_get_event_avatar_delete_link();
}
	function bpe_get_event_avatar_delete_link()
	{
		if( is_admin() )
			return apply_filters( 'bpe_get_admin_event_avatar_delete_link', wp_nonce_url( admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged='. $_GET['paged'] .'&event='. $_GET['event'] .'&step='. bpe_get_option( 'logo_slug' ) .'&delete=avatar' ), 'bpe_event_avatar_delete' ) );
		else
			return apply_filters( 'bpe_get_event_avatar_delete_link', wp_nonce_url( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'logo_slug' ) . '/delete/', 'bpe_event_avatar_delete' ) );
	}

/**
 * Has an event an avatar
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_has_event_avatar()
{
	$avatar = bp_core_fetch_avatar( array( 
		'item_id' 	 => bpe_get_displayed_event( 'id' ), 
		'object' 	 => 'event', 
		'avatar_dir' => 'event-avatars', 
		'no_grav' 	 => true,
		'html'		 => false
	) );
	
	if( ! empty( $_FILES ) || ! $avatar || $avatar == bpe_get_config( 'default_logo' ) )
		return false;

	return true;
}

/**
 * Filter the upload directory
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_avatar_upload_dir( $event_id = false )
{
	if ( !$event_id )
		$event_id = bpe_get_displayed_event( 'id' );

	$path = bp_core_avatar_upload_path() . '/event-avatars/' . $event_id;
	$newbdir = $path;

	if ( !file_exists( $path ) )
		@wp_mkdir_p( $path );

	$newurl = bp_core_avatar_url() . '/event-avatars/' . $event_id;
	$newburl = $newurl;
	$newsubdir = '/event-avatars/' . $event_id;

	return apply_filters( 'bpe_avatar_upload_dir', array( 'path' => $path, 'url' => $newurl, 'subdir' => $newsubdir, 'basedir' => $newbdir, 'baseurl' => $newburl, 'error' => false ) );
}

/**
 * Verify a creation step
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_event_creation_step( $step_slug )
{
	if( ! is_admin() )
	{
		if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) || ! bp_is_current_action( bpe_get_option( 'create_slug' ) ) )
			return false;
	}
	
	$step = ( is_admin() ) ? $_GET['step'] : bp_action_variable( 1 );

	if( ! $step && array_shift( array_keys( bpe_get_config( 'creation_steps' ) ) ) == $step_slug )
		return true;

	if( ! bpe_is_first_event_creation_step() )
	{
		if( ! bpe_are_previous_creation_steps_complete( $step_slug ) )
			return false;
	}

	if( $step == $step_slug )
		return true;

	return false;
}

/**
 * Is this the last creation step
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_last_event_creation_step()
{
	$last_step = array_pop( array_keys( bpe_get_config( 'creation_steps' ) ) );

	if( $last_step == bpe_get_config( 'current_create_step' ) )
		return true;

	return false;
}

/**
 * Is this the first creation step
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_first_event_creation_step()
{
	$first_step = array_shift( array_keys( bpe_get_config( 'creation_steps' ) ) );

	if( $first_step == bpe_get_config( 'current_create_step' ) )
		return true;

	return false;
}

/**
 * Get the previous creation panel
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_event_creation_previous_link()
{
	echo bpe_get_event_creation_previous_link();
}
	function bpe_get_event_creation_previous_link()
	{
		$step = ( is_admin() ) ? $_GET['step'] : bp_action_variable( 1 );

		foreach( (array) bpe_get_config( 'creation_steps' ) as $slug => $name )
		{
			if( $slug == $step )
				break;

			$previous_steps[] = $slug;
		}

		if( is_admin() )
			return apply_filters( 'bpe_get_admin_event_creation_previous_link', admin_url( 'admin.php?page='. EVENT_FOLDER .'&action=create&step='. array_pop( $previous_steps ) ) );
		else
			return apply_filters( 'bpe_get_event_creation_previous_link', bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) .'/'. bpe_get_option( 'step_slug' ) .'/' . array_pop( $previous_steps ) );
	}
	
/**
 * Get the edit tabs
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_event_edit_tabs( $event = false )
{
	global $event_template;

	if( ! $event )
		$event = ( isset( $event_template->event ) ) ? $event_template->event : bpe_get_displayed_event();
	
	if( is_admin() )
	{
		$step = ( isset( $_GET['step'] ) ) ? $_GET['step'] : '';
		$event = ( isset( $_GET['event'] ) ) ? $_GET['event'] : '';
		$paged = ( isset( $_GET['paged'] ) ) ? $_GET['paged'] : '';
			
		$url = admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged='. $paged .'&event='. $event .'&step=' );
		$current_tab = $step;
	}	
	else
	{
		$url = bpe_get_event_link( $event ) . bpe_get_option( 'edit_slug' ) .'/';
		$current_tab = bp_action_variable( 2 );
	}
	
	echo '<li'. ( ( $current_tab == bpe_get_option( 'general_slug' )  ) ? ' class="current"' : '' ) .'><a href="'. $url . bpe_get_option( 'general_slug' ) .'">'. __( 'General', 'events' ) .'</a></li>';
	
	if( bpe_get_option( 'enable_attendees' ) === true )
		echo '<li'. ( ( $current_tab == bpe_get_option( 'manage_slug' )  ) ? ' class="current"' : '' ) .'><a href="'. $url . bpe_get_option( 'manage_slug' ) .'">'. __( 'Attendees', 'events' ) .'</a></li>';

	if( bpe_get_option( 'enable_logo' ) === true )
		echo '<li'. ( ( $current_tab == bpe_get_option( 'logo_slug' )  ) ? ' class="current"' : '' ) .'><a href="'. $url . bpe_get_option( 'logo_slug' ) .'">'. __( 'Logo', 'events' ) .'</a></li>';

	do_action( 'bpe_event_edit_tabs', $current_tab, $url );

	echo '<li><a class="confirm" href="'. wp_nonce_url( $url .'cancel', 'bpe_cancel_event_now' ) .'">'. __( 'Cancel', 'events' ) .'</a></li>';
	echo '<li><a class="confirm" href="'. wp_nonce_url( $url .'delete', 'bpe_delete_event_now' ) .'">'. __( 'Delete', 'events' ) .'</a></li>';
}

/**
 * Get the edit form action
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_event_edit_form_action( $page = false )
{
	echo bpe_get_event_edit_form_action( $page );
}
	function bpe_get_event_edit_form_action( $page = false, $event = false )
	{
		if( ! $event )
			$event = bpe_get_displayed_event();

		if( ! $page )
		{
			if( is_admin() )
				$page = ( isset( $_GET['step'] ) ) ? $_GET['step'] : '';
				
			else
				$page = bp_action_variable( 2 );
		}

		if( is_admin() )
		{
			$paged = ( isset( $_GET['paged'] ) ) ? $_GET['paged'] : '';
			$event_id = ( isset( $_GET['event'] ) ) ? $_GET['event'] : '';

			return apply_filters( 'bpe_get_admin_event_edit_form_action', admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged='. $paged .'&event='. $event_id .'&step='. $page ) );
		}
		else
			return apply_filters( 'bpe_get_event_edit_form_action', bpe_get_event_link( $event ) . bpe_get_option( 'edit_slug' ) .'/'. $page );
	}

/**
 * Get the current screen
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_event_edit_screen( $slug )
{
	if( is_admin() )
		$step = ( isset( $_GET['step'] ) ) ? $_GET['step'] : '';

	else
	{
		if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) || ! bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) )
			return false;
			
		$step = bp_action_variable( 2 );
	}

	if( $step == $slug )
		return true;

	return false;
}

/**
 * Redirect to main edit screen
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_redirect_to_edit_screen()
{
	if( is_admin() )
	{
		$page = ( isset( $_GET['page'] ) && $_GET['page'] == EVENT_FOLDER ) ? true : false;
		$paged = ( isset( $_GET['paged'] ) ) ? $_GET['paged'] : 1;
		$event = ( isset( $_GET['event'] ) ) ? $_GET['event'] : false;
		$step = ( isset( $_GET['step'] ) ) ? $_GET['step'] : false;
		
		if( $page && $event && ! $step )
			bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged='. $paged .'&event='. $event .'&step='. bpe_get_option( 'general_slug' ) ) );
	}
	else
	{
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && ! bp_action_variable( 2 ) )
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'general_slug' ) .'/' );
	}
}
add_action( 'wp', 		  'bpe_redirect_to_edit_screen', 0 );
add_action( 'admin_init', 'bpe_redirect_to_edit_screen', 0 );

/**
 * Process the general section of an event
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_edit_event_action_general()
{
	if ( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'general_slug' ), 2 ) || isset( $_GET['page'] ) && $_GET['page'] == EVENT_FOLDER && isset( $_GET['event'] ) && isset( $_GET['step'] ) && $_GET['step'] == bpe_get_option( 'general_slug' ) )
	{
		if( isset( $_POST['edit-event'] ) )
		{
			check_admin_referer( 'bpe_edit_event_'. bpe_get_option( 'general_slug' ) );
			bpe_process_event_edit( $_POST, bpe_get_displayed_event() );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}
	}
}
add_action( 'wp', 		  'bpe_edit_event_action_general', 0 );
add_action( 'admin_init', 'bpe_edit_event_action_general', 0 );

/**
 * Process the manage section of an event
 *
 * @package	 Core
 * @since 	 1.7.10
 */
function bpe_edit_event_action_manage()
{
	if ( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) || isset( $_GET['page'] ) && $_GET['page'] == EVENT_FOLDER && isset( $_GET['event'] ) && isset( $_GET['step'] ) && $_GET['step'] == bpe_get_option( 'manage_slug' ) )
	{
		if( isset( $_POST['add-attendee'] ) )
		{
			check_admin_referer( 'bpe_edit_event_'. bpe_get_option( 'manage_slug' ) );
			bpe_process_manual_attendees( $_POST, bpe_get_displayed_event() );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}
	}
}
add_action( 'wp', 		  'bpe_edit_event_action_manage', 0 );
add_action( 'admin_init', 'bpe_edit_event_action_manage', 0 );

/**
 * Check for more than 1 creation steps
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_only_create_step()
{
	if( count( (array) bpe_get_config( 'creation_steps' ) ) <= 1 )
		return true;
		
	return false;
}

/**
 * Display event members
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_display_event_members( $displayed_event, $context )
{
	if( bp_has_members( 'include='. bpe_event_get_member_ids( $displayed_event, $context ) .'&type=alphabetical&per_page=9999' ) ) : ?>
	
		<ul id="<?php echo $context ?>-list" class="attendee-list item-list">
		<?php while ( bp_members() ) : bp_the_member(); ?>
	
			<li>
				<div class="item-avatar">
					<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar() ?></a>
				</div>
	
				<div class="item item-member">
					<div class="item-title">
						<a href="<?php bp_member_permalink() ?>"><span class="attendee"><?php bp_member_name() ?></span></a>
					</div>
                    <div class="item-desc">
						<?php do_action( 'bpe_directory_members_actions', bp_get_member_user_id(), $displayed_event, $context ) ?>
                    </div>
				</div>
			</li>
	
		<?php endwhile; ?>
		</ul>
	
		<?php do_action( 'bp_after_directory_members_list' ) ?>
	
	<?php else: ?>
	
		<div id="message" class="info">
			<p><?php _e( 'No members were found.', 'events' ) ?></p>
		</div>
	
	<?php endif;
}
?>