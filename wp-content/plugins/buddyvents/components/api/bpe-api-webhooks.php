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
 * Add the webhooks settings page
 * 
 * @package API
 * @since 	1.7
 */
function bpe_setup_webhooks()
{
	bp_core_new_subnav_item( array(
		'name' 				=> __( 'Webhooks', 'events' ),
		'slug' 				=> 'webhooks',
		'parent_url' 		=> bp_loggedin_user_domain() . bp_get_settings_slug() .'/',
		'parent_slug' 		=> bp_get_settings_slug(),
		'screen_function' 	=> 'bpe_events_settings_webhooks',
		'position' 			=> 40,
		'item_css_id' 		=> 'settings-webhooks',
		'user_has_access' 	=> bp_is_my_profile()
		)
	);
}
add_action( 'bp_setup_nav', 'bpe_setup_webhooks' );

/**
 * Generate a unique verifier
 * 
 * @package API
 * @since 	1.7
 */
function bpe_api_generate_verifier()
{
	$key = wp_generate_password( 40, true, true );
	
	if( bpe_check_verifier( $key ) )
	{
		do {
			$key = wp_generate_password( 40, true, true );
		}
		while( bpe_check_verifier( $key ) );
	}
	
	return $key;
}

/**
 * URL endpoint to verify a webhook
 * 
 * @package API
 * @since 	1.2.5
 */
function bpe_api_webhook_endpoint()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'api_slug' ) ) && bp_is_action_variable( 'webhooks', 0 ) && bp_is_action_variable( 'pingback', 1 ) )
	{
		if( isset( $_POST['verifier'] ) )
			bpe_verify_webhooks( $_POST['verifier'] );
	}
}
add_action( 'wp', 'bpe_api_webhook_endpoint', 1 );

/**
 * Screen function for event settings
 * 
 * @package API
 * @since 	1.2.5
 */
function bpe_events_settings_webhooks()
{
	if( bp_is_settings_component() && bp_is_current_action( 'webhooks' ) && bp_is_action_variable( 'remove', 0 ) && bp_action_variable( 1 ) )
	{
		check_admin_referer( 'bpe_remove_webhook' );
		
		$webhook = new Buddyvents_Webhooks( (int)bp_action_variable( 1 ) );

		if( bp_loggedin_user_id() == $webhook->user_id )
		{	
			$webhook->delete();
			bp_core_add_message( __( 'The webhook has been removed successfully.', 'events' ) );
		}
		else
			bp_core_add_message( __( 'The webhook could not be removed.', 'events' ), 'error' );

		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/webhooks/' );
	}

	if( bp_is_settings_component() && bp_is_current_action( 'webhooks' ) && bp_is_action_variable( 'ping', 0 ) && bp_action_variable( 1 ) )
	{
		check_admin_referer( 'bpe_ping_webhook' );
		
		$webhook = new Buddyvents_Webhooks( (int)bp_action_variable( 1 ) );
		
		bpe_unverify_webhook( $webhook->id );
		
		$result = wp_remote_post( $webhook->url, array( 'body' => http_build_query( array( 'verifier' => $webhook->verifier, 'pingback_url' => bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' )  .'/'. bpe_get_option( 'api_slug' ) .'/webhooks/pingback/' ), '', '&' ) ) );

		if( is_wp_error( $result ) )
			bp_core_add_message( __( 'The webhook could not be repinged.', 'events' ), 'error' );
		else		
			bp_core_add_message( __( 'The webhook has been repinged.', 'events' ) );
			
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/webhooks/' );
	}
	
	if( isset( $_POST['verify'] ) )
	{
		check_admin_referer( 'bpe_verify_events_webhooks' );

		if( bpe_verify_webhooks( $_POST['manual_verify'] ) > 0 )
			bp_core_add_message( __( 'Your webhooks have been verified.', 'events' ) );
			
		else
			bp_core_add_message( __( 'Your webhooks could not be verified.', 'events' ), 'error' );

		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/webhooks/' );
	}
	
	if( isset( $_POST['submit'] ) )
	{
		check_admin_referer( 'bpe_settings_events_webhooks' );

		if( empty( $_POST['url'] ) || empty( $_POST['event'] ) )
		{
			bp_core_add_message( __( 'Please fill in all fields marked by *.', 'events' ), 'error' );
			bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/webhooks/' );
		}
		
		if( ! bpe_is_url( $_POST['url'] ) )
		{
			bp_core_add_message( __( 'Please enter a valid url.', 'events' ), 'error' );
			bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/webhooks/' );
		}

		$verifier = bpe_api_generate_verifier();

		if( $_POST['event'] == 'all' )
		{
			foreach( Buddyvents_Push::unique_hooks() as $event )
			{
				if( ! bpe_api_check_existing_hook( $event, $_POST['url'] ) )
					bpe_add_webhook( null, bp_loggedin_user_id(), $event, $_POST['url'], $verifier, 0 );
			}
		}
		else
		{
			if( ! bpe_api_check_existing_hook( $_POST['event'], $_POST['url'] ) )
				bpe_add_webhook( null, bp_loggedin_user_id(), $_POST['event'], $_POST['url'], $verifier, 0 );
		}

		$result = wp_remote_post( $_POST['url'], array( 'body' => http_build_query( array( 'verifier' => $verifier, 'pingback_url' => bp_get_root_domain() . bpe_get_base( 'root_slug' )  .'/'. bpe_get_option( 'api_slug' ) .'/webhooks/pingback/' ), '', '&' ) ) );

		if( is_wp_error( $result ) )
			bp_core_add_message( __( 'Your webhook could not be saved.', 'events' ), 'error' );
		else
			bp_core_add_message( __( 'Your webhook has been saved.', 'events' ) );
			
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/webhooks/' );
	}

	add_action( 'bp_template_title', 'bpe_events_webhooks_title' );
	add_action( 'bp_template_content', 'bpe_events_webhooks_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * Screen function for webhooks title
 * 
 * @package API
 * @since 	1.7
 */
function bpe_events_webhooks_title() 
{
	echo '<h3>'. __( 'Webhooks', 'events' ) .'</h3>';
}

/**
 * Screen function for webhooks content
 * 
 * @package API
 * @since 	1.7
 */
function bpe_events_webhooks_content()
{
	$webhooks = bpe_get_all_webhooks_for_user();
	
	?>
	<form action="<?php echo bp_loggedin_user_domain() . bp_get_settings_slug() . '/webhooks/' ?>" method="post" id="settings-form" class="standard-form">

		<?php wp_nonce_field( 'bpe_settings_events_webhooks' ) ?>
        
        <p><?php _e( 'Webhooks let you ping an URL on an external site with information about an event. You can either specify one URL per hook or one URL for all hooks.', 'events' ) ?></p>
        <p><?php _e( 'Please note that some programming knowledge is necessary to use this feature.', 'events' ) ?></p>
        
        <label for="event"><?php _e( '* Event', 'events' ) ?></label>
        <select id="event" name="event">
        	<option value="all"><?php _e( 'All', 'events' ) ?></option>
            <?php foreach( Buddyvents_Push::unique_hooks() as $key => $value ) : ?>
        	<option value="<?php echo $value ?>"><?php echo $value ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="url"><?php _e( '* URL', 'events' ) ?></label>
        <input type="text" name="url" id="url" value="" />

		<div class="submit">
			<input type="submit" name="submit" value="<?php _e( 'Add new webhook', 'events' ) ?>" id="submit" class="auto"/></p>
		</div>
	</form>
    
    <?php if( $webhooks ) : ?>
    
        <hr />
        
        <h4><?php _e( 'Manage Your Webhooks', 'events' ) ?></h4>
    
        <table cellspacing="0" id="webhooks-table" class="zebra">
            <thead>
                <tr>
                    <th scope="col"><?php _e( 'Event', 'events' ); ?></th>
                    <th scope="col"><?php _e( 'URL', 'events' ); ?></th>
                    <th scope="col" colspan="3"><?php _e( 'Actions', 'events' ); ?></th>
                </tr>
            </thead>
            <tbody>
			<?php foreach( $webhooks as $hook ) : ?>
            
                <tr>
                    <td><?php echo $hook->event ?></td>
                    <td><?php echo $hook->url ?></td>
                    <td>
                    	<a href="<?php echo wp_nonce_url( bp_displayed_user_domain() . bp_get_settings_slug() . '/webhooks/remove/'. $hook->id .'/', 'bpe_remove_webhook' ); ?>"><?php _e( 'Remove', 'events' ); ?></a>
                    </td>
                    <td>
                    	<a href="<?php echo wp_nonce_url( bp_displayed_user_domain() . bp_get_settings_slug() . '/webhooks/ping/'. $hook->id .'/', 'bpe_ping_webhook' ); ?>"><?php _e( 'Re-verify', 'events' ); ?></a>
                    </td>
                    <td>
                    	<?php if( $hook->verified == 0 ) : ?><a href="#" class="hook-toggle"><?php _e( 'Verify manually', 'events' ); ?></a><?php else : ?><?php _e( 'Verified', 'events' ); ?><?php endif; ?>
                    </td>
                </tr>
                <tr class="hook-manual">
                	<td colspan="5">
                        <form action="" method="post" id="webhooks-form-<?php echo $hook->id ?>" class="standard-form">
                        	<?php wp_nonce_field( 'bpe_verify_events_webhooks' ) ?>
                            <input type="text" id="manual-verify-<?php echo $hook->id ?>" name="manual_verify" value="<?php _e( 'Enter your verification code...', 'events' ) ?>"  onfocus="if (this.value == '<?php _e( 'Enter your verification code...', 'events' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Enter your verification code...', 'events' ) ?>';}" />
                            <input type="submit" name="verify" value="<?php _e( 'Verify', 'events' ) ?>" id="verify-<?php echo $hook->id ?>" />
                        </form>
                    </td>
                </tr>
            
            <?php endforeach; ?>
			</tbody>
		</table>
        
        <script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.hook-manual').hide();
			jQuery('.hook-toggle').click(function(){
				jQuery(this).parent().parent().next('.hook-manual').toggle();
				return false;
			});
		});
		</script>  
	<?php endif;
}

/**
 * Delete all user api keys upon user deletion
 * 
 * @package API
 * @since 	2.0
 */
function bpe_delete_webhooks( $user_id )
{
	Buddyvents_Webhooks::delete_by_user( $user_id );
}
add_action( 'wpmu_delete_user', 'bpe_delete_webhooks', 1 );
add_action( 'delete_user', 'bpe_delete_webhooks', 1 );
?>