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
 
abstract class Buddyvents_Admin_Core
{
	/**
	 * Set up the page
	 * 
	 * @package Admin
	 * @since 	2.1
	 */
	public function __construct()
	{
		$this->head();
		$this->content();
		$this->footer();
	}
	
	/**
	 * Content of the events tab
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	protected function head()
	{
		if( isset( $_GET['action'] ) && $_GET['action'] == 'create' )
			$title = __( 'Create Event', 'events' );
		elseif( isset( $_GET['event'] ) && ! empty( $_GET['event'] ) )
			$title = __( 'Edit Event', 'events' );
		else
			$title = __( 'Events', 'events' );
			
		$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : false;
		?>
        <div class="wrap">
			<div class="icon32 icon32-posts-post" id="icon-shabushabu"></div>
            <h2 class="nav-tab-wrapper">
            	<?php
                if( current_user_can( 'bpe_manage_events' ) ) : 
	            	?>
	                <a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER ) echo ' nav-tab-active'; ?>"><?php echo $title ?></a>
					<?php
				endif;
				
                if( current_user_can( 'bpe_manage_event_approvals' ) ) : 
	                if( bpe_get_option( 'approve_events' ) == true || bpe_get_option( 'enable_api' ) == true ) :
	                	?>
	                	<a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-approve' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-approve' ) echo ' nav-tab-active'; ?>"><?php _e( 'Approve', 'events' ); ?></a>
	                	<?php
	                endif;
				endif;
                
                if( current_user_can( 'bpe_manage_event_categories' ) ):
                	?>
                	<a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-categories' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-categories' ) echo ' nav-tab-active'; ?>"><?php _e( 'Categories', 'events' ); ?></a>
                	<?php
                endif;

                if( current_user_can( 'bpe_manage_event_sales' ) ) :
	                if( bpe_get_option( 'enable_tickets' ) == true ) : 
	                	?>
	                	<a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-sales' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-sales' ) echo ' nav-tab-active'; ?>"><?php _e( 'Sales', 'events' ); ?></a>
	                	<?php
	                endif;
                endif;
				
                if( current_user_can( 'bpe_manage_event_invoices' ) ) :
	                if( bpe_get_option( 'enable_invoices' ) == true && bpe_get_option( 'enable_tickets' ) == true ) :
	                	?>
		                <a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-invoices' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-invoices' ) echo ' nav-tab-active'; ?>"><?php _e( 'Invoices', 'events' ); ?></a>
	                	<?php
	                endif;
				endif;

                if( current_user_can( 'bpe_manage_event_apikeys' ) ) :
	                if( bpe_get_option( 'enable_api' ) === true ) :
	                	?>
	                	<a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-apikeys' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-apikeys' ) echo ' nav-tab-active'; ?>"><?php _e( 'Api Keys', 'events' ); ?></a>
	                	<?php
	                endif;
				endif;
                
                if( current_user_can( 'bpe_manage_event_webhooks' ) ) :
	                if( bpe_get_option( 'enable_webhooks' ) === true && bpe_get_option( 'enable_api' ) === true ) :
	                	?>
	                	<a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-webhooks' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-webhooks' ) echo ' nav-tab-active'; ?>"><?php _e( 'Webhooks', 'events' ); ?></a>
	                	<?php
	                endif;
				endif;
                
                if( current_user_can( 'bpe_manage_event_settings' ) ) :
                	?>
                	<a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-settings' ) echo ' nav-tab-active'; ?>"><?php _e( 'Settings', 'events' ); ?></a>
                	<?php
                endif;
                ?>

                <a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-services' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-services' ) echo ' nav-tab-active'; ?>"><?php _e( 'Services', 'events' ); ?></a>

                <?php if( current_user_can( 'bpe_manage_events' ) ) : ?>
                	<a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-manual' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-manual' ) echo ' nav-tab-active'; ?>"><?php _e( 'Manual', 'events' ); ?></a>
                <?php endif; ?>

                <a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-readme' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-readme' ) echo ' nav-tab-active'; ?>"><?php _e( 'Readme', 'events' ); ?></a>
                <a href="<?php echo admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-changelog' ), 'admin.php' ) ); ?>" class="nav-tab<?php if( $page == EVENT_FOLDER .'-changelog' ) echo ' nav-tab-active'; ?>"><?php _e( 'Changelog', 'events' ); ?></a>

                <?php do_action( 'bpe_admin_tabs' ); ?>
            </h2>
            <div id="bpe-content">
        <?php
	}

	/**
	 * The actual content
	 * 
	 * Needs to be overridden in a subclass
	 * 
	 * @package Admin
	 * @since 	2.1
	 */
	abstract protected function content();

	/**
	 * Content of the events tab
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	protected function footer()
	{
			?>
        	</div>
        </div>
        <?php
	}
}
?>