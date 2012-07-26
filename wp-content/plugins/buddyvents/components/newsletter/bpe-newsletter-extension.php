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

class Buddyvents_Newsletter_Extension extends Buddyvents_Extension
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Newsletter
	 * @since 	2.1
 	 */
	public function __construct()
	{
		if( count( Buddyvents_Newsletter::$services ) <= 0 )
			return false;
		
 		$this->name 	= __( 'Newsletter', 'events' );
		$this->slug 	= bpe_get_option( 'newsletter_slug' );
		$this->service  = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ),'newsletter_service' );

		$this->create_step_position = apply_filters( 'bpe_newsletter_create_step_position', 9 );
		$this->enable_create_step	= true;
		$this->enable_edit_item		= true;
		$this->enable_nav_item		= ( $this->service ) ? true : false;
	}

	/**
	 * Display create screen
	 * 
	 * @package	Newsletter
	 * @since 	2.1
	 */
	public function create_screen()
	{
		if( ! bpe_is_event_creation_step( $this->slug ) )
			return false;
		
		$class = ( count( Buddyvents_Newsletter::$services ) <= 1 ) ? '' : ' class="multiple-services"';
		?>
		<div id="newsletter-create-screen"<?php echo $class ?>>
			<?php if( count( Buddyvents_Newsletter::$services ) >= 2 ) : ?>
				<p><?php _e( 'You can only pick one Newsletter service.', 'events' ) ?></p>
			<?php endif; ?>
			
			<?php do_action( 'bpe_newsletter_service_create_screen' ); ?>
		</div>
		<input type="hidden" id="nl_event_id" name="nl_event_id" value="<?php echo esc_attr( bpe_get_displayed_event( 'id' ) ) ?>" />
		<?php
		wp_nonce_field( 'bpe_add_event_'. $this->slug );
	}

	/**
	 * Process create screen
	 * 
	 * @package	Newsletter
	 * @since 	2.1
	 */
	public function create_screen_save()
	{
		check_admin_referer( 'bpe_add_event_'. $this->slug );
		
		do_action( 'bpe_newsletter_service_create_screen_save' );
	}

	/**
	 * Display edit screen
	 * 
	 * @package	Newsletter
	 * @since 	2.1
	 */
	public function edit_screen()
	{
		if( ! bpe_is_event_edit_screen( $this->slug ) )
			return false;

		$class = ( count( Buddyvents_Newsletter::$services ) <= 1 ) ? '' : ' class="multiple-services"';
		?>
		<div id="newsletter-edit-screen"<?php echo $class ?>>
			<?php if( count( Buddyvents_Newsletter::$services ) >= 2 ) : ?>
				<p><?php _e( 'You can only pick one Newsletter service.', 'events' ) ?></p>
			<?php endif; ?>
			
			<?php do_action( 'bpe_newsletter_service_edit_screen' ); ?>
		</div>

        <div class="submit">
            <input type="submit" value="<?php _e( 'Save Changes', 'events' ) ?>" id="edit-event" name="edit-event" />
        </div>

		<input type="hidden" id="nl_event_id" name="nl_event_id" value="<?php echo esc_attr( bpe_get_displayed_event( 'id' ) ) ?>" />
		<?php

		wp_nonce_field( 'bpe_edit_event_'. $this->slug );
	}

	/**
	 * Process edit screen
	 * 
	 * @package	Newsletter
	 * @since 	2.1
	 */
 	public function edit_screen_save()
	{
		if( ! isset( $_POST['edit-event'] ) )
			return false;

		check_admin_referer( 'bpe_edit_event_'. $this->slug );

		do_action( 'bpe_newsletter_service_edit_screen_save' );

		if( is_admin() )
			bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged=1&event='. bpe_get_displayed_event( 'id' ) .'&step='. $this->slug ) );
		else
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. $this->slug .'/' );
	}
	
	/**
	 * Display any signup forms
	 * 
	 * @package	Newsletter
	 * @since 	2.1
	 */
	public function display()
	{
		if( $this->service )
			do_action( 'bpe_newsletter_service_signup_form' );
	}
}
bpe_register_event_extension( 'Buddyvents_Newsletter_Extension' );
?>