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
 
class Buddyvents_Admin_Services extends Buddyvents_Admin_Core
{
	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
    public function __construct()
	{
		parent::__construct();
    }

	/**
	 * Content of the readme tab
	 * 
	 * @package Admin
	 * @since 	2,0
	 */
	protected function content()
	{
		?>
		<h3>1. <?php _e( 'General Modifications', 'events' ) ?></h3>
		<p><?php _e( 'Buddyvents is a versatile plugin and can be modified to do many more things than `just` events. It has been used, for example, to create a locations directory, a reservation engine and as a tool to create trips.', 'events' ) ?></p>
		<p><?php _e( 'We have made it as easy as possible to extend Buddyvents, but should you have no experience coding, then we are available for custom work.', 'events' ) ?></p>

		<h3>2. <?php _e( 'Ticket Design', 'events' ) ?></h3>
		<p><?php _e( 'Version 2.0 includes PayPal integration and PDF tickets. We have kept the current ticket design intentionally bland. Should you require a custom ticket design or have a finished design already, then we can transform your design into a PDF template for use on your site.', 'events' ) ?></p>

		<h3><?php _e( 'Contact Us For a Quote', 'events' ) ?></h3>
		<small><?php printf( __( 'Please do not use the form below for support requests. Use our <a href="%s">forums</a> instead.', 'events' ), Buddyvents::HOME_URL .'forums/' ) ?></small>

	    <div id="contact-wrapper">
	    
	    	<div id="ajax-response"></div>
	        
	        <form action="" method="post" id="shabu-contact-form" name="shabu-contact-form" class="standard-form">
	
	            <?php wp_nonce_field( 'shabu_contact-form' ) ?>
	            
	            <label for="contact-subject"><?php _e( 'Subject', 'events' ) ?></label>
	            <input type="text" id="contact-subject" name="contact-subject" value="" />
	            
	            <label for="contact-message"><?php _e( '<span class="required">*</span> Message', 'events' ) ?></label>
	            <textarea id="contact-message" name="contact-message"></textarea>
	     
	            <div class="submit">
	                <input type="submit" id="send-contact-message" name="send-contact-message" value="<?php _e( 'Request Quote', 'events' ) ?>" />
	                <img alt="" id="ajax-loading" class="ajax-loading" src="<?php echo admin_url( '/images/wpspin_light.gif' ) ?>">
	            </div>   
	        </form>
	    </div>
		<?php
	}
}
?>