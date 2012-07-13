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
 * Generate a unique API key
 * 
 * @package API
 * @since 	1.5
 */
function bpe_api_generate_key()
{
	$key = wp_generate_password( 40, false, false );
	
	if( bpe_check_apikey( $key ) )
	{
		do {
			$key = wp_generate_password( 40, false, false );
		}
		while( bpe_check_apikey( $key ) );
	}
	
	return $key;
}

/**
 * Screen function for event settings
 * 
 * @package API
 * @since 	1.2.5
 */
function bpe_events_settings_api_keys()
{
	if( isset( $_POST['get-key'] ) )
	{
		check_admin_referer( 'bpe_get_api_key' );
		
		$key = bpe_api_generate_key();

		bpe_add_apikey( null, bp_loggedin_user_id(), $key, 1, bp_core_current_time(), 0, bp_core_current_time(), 0 );

		bp_core_add_message( sprintf( __( 'Your API Key: %s', 'events' ), $key ) );
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/events-api/' );
	}

	if( bp_is_current_action( 'events-api' ) && bp_action_variable( 0 ) )
	{
		check_admin_referer( 'bpe_remove_user_api_key' );
		
		$api = new Buddyvents_API( bp_action_variable( 0 ) );
		
		if( $api->user_id != bp_loggedin_user_id() )
		{
			bp_core_add_message( __( 'You are not allowed to delete this API key.', 'events' ), 'error' );
			bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/events-api/' );
		}
		
		$api->delete();

		bp_core_add_message( __( 'API key successfully deleted.', 'events' ) );
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/events-api/' );
	}

	add_action( 'bp_template_title', 'bpe_events_api_title' );
	add_action( 'bp_template_content', 'bpe_events_api_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * Screen function for api keys title
 * 
 * @package API
 * @since 	2.0
 */
function bpe_events_api_title() 
{
	echo '<h3>'. __( 'Events API', 'events' ) .'</h3>';
}

/**
 * Screen function for api keys content
 * 
 * @package API
 * @since 	2.0
 */
function bpe_events_api_content()
{
	?>
	<h4><?php _e( 'What is the API?', 'events' ) ?></h4>
	<p><?php printf( __( 'The API (or <strong>A</strong>pplication <strong>P</strong>rogramming <strong>I</strong>nterface) lets external web sites use the events data here on %s. In order to use and access the API, a key is needed, which you can generate by clicking the button below. Please keep this API key safe and do not share it with others. Access to the API can be revoked without prior notice.', 'events' ), get_bloginfo( 'name' ) ) ?></p>
	<p><?php _e( 'Please note that only the data has been made available. Any styling, manipulation of the data and presentation needs to be handled externally.', 'events' ) ?></p>
	<p><?php printf( __( 'You can download an example <a href="%s">client library</a> written in PHP to put events data onto an external web page. Documentation is also included in the download.', 'events' ), EVENT_URLPATH .'components/api/client/client.zip' ) ?></p>

	<form action="" method="post" id="api-event-form" class="standard-form">

	    <?php wp_nonce_field( 'bpe_get_api_key' ) ?>
    
	    <div class="submit">
    	    <input type="submit" value="<?php _e( 'Get API Key', 'events' ) ?>" id="get-key" name="get-key" />
   		</div>

	</form>
	<?php
	$keys = bpe_get_all_for_user( bp_loggedin_user_id() );

	if( count( $keys ) > 0 ):
	?>
	<hr />
	<p>
    	<h4><?php _e( 'API Keys', 'events' ) ?></h4>
        <table cellspacing="0" id="webhooks-table" class="zebra">
            <thead>
                <tr>
                    <th scope="col"><?php _e( 'Key', 'events' ); ?></th>
                    <th scope="col"><?php _e( 'Status', 'events' ); ?></th>
                    <th scope="col"><?php _e( 'Hits', 'events' ); ?></th>
                    <th scope="col"><?php _e( 'Action', 'events' ); ?></th>
                </tr>
            </thead>
            <tbody>
	        <?php
    	    foreach( $keys as $key ) :
	    		echo '<tr>';
					echo '<td>'. $key->api_key .'</td>';
	    			echo '<td>'. ( ( $key->active == 1 ) ? __( 'active', 'events' ) : __( 'inactive', 'events' ) ) .'</td>';
					echo '<td>'. $key->hits .'</td>';
					echo '<td><a class="confirm" href="'. wp_nonce_url( bp_loggedin_user_domain() . bp_get_settings_slug() . '/events-api/'. $key->id .'/', 'bpe_remove_user_api_key' ) .'">'. __( 'Delete', 'events' ) .'</a></td>';
	    		echo '</tr>';
			endforeach;
			?>
			</tbody>
		</table>
	</p>
    <?php
	endif;
}

/**
 * Delete all user api keys upon user deletion
 * 
 * @package API
 * @since 	2.0
 */
function bpe_delete_api_keys( $user_id )
{
	Buddyvents_API::delete_by_user( $user_id );
}
add_action( 'wpmu_delete_user', 'bpe_delete_api_keys', 1 );
add_action( 'delete_user', 'bpe_delete_api_keys', 1 );
?>