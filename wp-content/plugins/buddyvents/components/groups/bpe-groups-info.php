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
 * Setup more BPE globals
 * Needs to be done here. <code>bp_setup_globals</code> happens too early in the game.
 * 
 * @package	 Groups
 * @since 	 1.0
 * 
 * @uses	bp_get_current_group_id()
 * @uses 	groups_get_groupmeta()
 * @global 	object $bpe Global Buddyvents settings
 */
function bpe_setup_additional_globals()
{
	global $bpe;

	// set up the address and email messages of the current group
	$group_cookie = isset( $_COOKIE['bp_new_group_id'] ) ? $_COOKIE['bp_new_group_id'] : false;
	$group_id = ( ! bp_get_current_group_id() ) ? $group_cookie : bp_get_current_group_id();
	
	if( ! $group_id )
		return false;
	
	if( $address = groups_get_groupmeta( $group_id, 'group_address' ) )
	{
		foreach( $address as $key => $val )
			$bpe->displayed_group->{$key} = $val;
	}
}
add_action( 'wp', 'bpe_setup_additional_globals', 1 );

/**
 * Add group contact details to group creation and edit screen
 *
 * @package	 Groups
 * @since 	 1.0
 * 
 * @uses 	bpe_group_contact_required()
 * @uses 	bpe_get_displayed_group()
 */
function bpe_group_creation_details()
{
	?>
    <label for="group_street"><?php _e( 'Street', 'events' ) ?><?php if( bpe_group_contact_required( 'group_street' ) ) _e( ' (required)', 'events' ); ?></label>
    <input type="text" name="group_street" id="group_street" value="<?php echo bpe_get_displayed_group( 'street' ) ?>" />

    <label for="group_postcode"><?php _e( 'Postcode', 'events' ) ?><?php if( bpe_group_contact_required( 'group_postcode' ) ) _e( ' (required)', 'events' ); ?></label>
    <input type="text" name="group_postcode" id="group_postcode" value="<?php echo bpe_get_displayed_group( 'postcode' ) ?>" />

    <label for="group_city"><?php _e( 'City', 'events' ) ?><?php if( bpe_group_contact_required( 'group_city' ) ) _e( ' (required)', 'events' ); ?></label>
    <input type="text" name="group_city" id="group_city" value="<?php echo bpe_get_displayed_group( 'city' ) ?>" />

    <label for="group_country"><?php _e( 'Country', 'events' ) ?><?php if( bpe_group_contact_required( 'group_country' ) ) _e( ' (required)', 'events' ); ?></label>
    <select name="group_country" id="group_country"><?php bpe_country_select( bpe_get_displayed_group( 'country' ) ) ?></select>

    <label for="group_telephone"><?php _e( 'Telephone', 'events' ) ?><?php if( bpe_group_contact_required( 'group_telephone' ) ) _e( ' (required)', 'events' ); ?></label>
    <input type="text" name="group_telephone" id="group_telephone" value="<?php echo bpe_get_displayed_group( 'telephone' ) ?>" />

    <label for="group_mobile"><?php _e( 'Mobile', 'events' ) ?><?php if( bpe_group_contact_required( 'group_mobile' ) ) _e( ' (required)', 'events' ); ?></label>
    <input type="text" name="group_mobile" id="group_mobile" value="<?php echo bpe_get_displayed_group( 'mobile' ) ?>" />

    <label for="group_fax"><?php _e( 'Fax', 'events' ) ?><?php if( bpe_group_contact_required( 'group_fax' ) ) _e( ' (required)', 'events' ); ?></label>
    <input type="text" name="group_fax" id="group_fax" value="<?php echo bpe_get_displayed_group( 'fax' ) ?>" />

    <label for="group_website"><?php _e( 'Website', 'events' ) ?><?php if( bpe_group_contact_required( 'group_website' ) ) _e( ' (required)', 'events' ); ?></label>
    <input type="text" name="group_website" id="group_website" value="<?php echo bpe_get_displayed_group( 'website' ) ?>" />
    
	<?php		
    do_action( 'bpe_group_creation_details' );
}
add_action( 'groups_custom_group_fields_editable', 'bpe_group_creation_details' );

/**
 * Check if a group contact field is required
 * 
 * @package	 Groups
 * @since 	 2.0
 * 
 * @uses 	bpe_get_option()
 * @uses 	bpe_get_required_fields()
 * @param 	string
 * @return 	boolean
 */
function bpe_group_contact_required( $field )
{
	if( bpe_get_option( 'group_contact_required' ) === false )
		return false;
	
	$req = bpe_get_required_fields();
	
	if( in_array( $field, (array)$req ) )
		return true;
	
	return false;
}	

/**
 * Save extra group contact details
 * 
 * @package	 Groups
 * @since 	 1.0
 * 
 * @param 	int		$id		The current group_id
 * @uses 	groups_update_groupmeta()
 * @uses 	wp_filter_kses()
 * @uses 	apply_filters()
 * @global 	object $bp Global BuddyPress settings
 */
function bpe_save_group_creation_details( $id = false )
{
	global $bp;
	
	if( ! $id )
		$id = $bp->groups->new_group_id;
	
	$address = array(
		'street'	=> $_POST['group_street'],
		'postcode'	=> $_POST['group_postcode'],
		'city'		=> $_POST['group_city'],
		'country'	=> $_POST['group_country'],
		'telephone' => $_POST['group_telephone'],
		'mobile'	=> $_POST['group_mobile'],
		'fax'		=> $_POST['group_fax'],
		'website'	=> $_POST['group_website']
	);
	
	foreach( $address as $key => $val )
		$address_safe[$key] = wp_filter_kses( $val );

	$address_safe = apply_filters( 'bpe_save_group_creation_details', $address_safe );
	
	do_action( 'bpe_save_extra_group_details', $address_safe, $id );
	
	groups_update_groupmeta( $id, 'group_address', $address_safe );
}
add_action( 'groups_create_group_step_save_group-details', 'bpe_save_group_creation_details' );
add_action( 'groups_group_details_edited', 'bpe_save_group_creation_details' );

/**
 * Get required POST fields 
 * 
 * Returns an array that holds all required extra group creation fields
 * Can be adjusted with the bpe_required_group_fields filter
 * 
 * @package Groups
 * @since 	2.0
 * 
 * @uses 	apply_filters()
 * @return 	array
 */
function bpe_get_required_fields()
{
	return apply_filters( 'bpe_required_group_fields', array(
		'group_street',
		'group_postcode',
		'group_city',
		'group_country'	
	) );
}

/**
 * Validate the extra group data 
 * 
 * Attached to the action hook <code>groups_details_updated</code>
 * 
 * @package Groups
 * @since 	1.0
 * 
 * @param 	int 	$group_id	The current group id
 * @uses 	bpe_get_option()
 * @uses 	bpe_get_required_fields()
 * @uses 	bp_core_add_message()
 * @uses 	bp_core_redirect()
 * @uses 	bp_get_group_permalink()
 */
function bpe_check_group_save_details( $group_id )
{
	if( bpe_get_option( 'group_contact_required' ) === false )
		return false;
		
	$has_empty = false;
		
	$required_fields = bpe_get_required_fields();
	
	foreach( $required_fields as $field )
	{
		if( empty( $_POST[$field] ) )
		{
			$has_empty = true;
			break;
		}
	}
		
	if( $has_empty )
	{
		$group = new BP_Groups_Group( $group_id );
		
		bp_core_add_message( __( 'Please fill in all required fields.', 'events' ), 'error' );
		bp_core_redirect( bp_get_group_permalink( $group ) . 'admin/edit-details/' );
	}
}
add_action( 'groups_details_updated', 'bpe_check_group_save_details' );

/**
 * Validate the extra input
 * 
 * Attached to the <code>groups_created_group</code> action hook
 * 
 * @package Groups
 * @since 	1.0 
 * 
 * @param 	int 	$group_id	The current group id
 * @global 	object 	$bp			Global BuddyPress settings
 *
 * @uses 	bpe_get_option()
 * @uses 	bpe_get_required_fields()
 * @uses 	bp_core_add_message()
 * @uses 	bp_core_redirect()
 * @uses 	bp_get_root_domain()
 * @uses 	bp_get_groups_slug()
 */
function bpe_check_group_creation_details( $group_id )
{
	global $bp;
	
	if( $bp->groups->current_create_step == 'group-details' )
	{
		if( bpe_get_option( 'group_contact_required' ) === false )
			return false;

		$has_empty = false;
			
		$required_fields = bpe_get_required_fields();
		
		foreach( $required_fields as $field )
		{
			if( empty( $_POST[$field] ) )
			{
				$has_empty = true;
				break;
			}
		}
			
		if( $has_empty )		
		{
			bp_core_add_message( __( 'Please fill in all required fields.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . '/' . bp_get_groups_slug() . '/create/step/' . $bp->groups->current_create_step . '/' );
		}
	}
}
add_action( 'groups_created_group', 'bpe_check_group_creation_details' );

/**
 * Display group contact details
 * 
 * Output can be adjusted via the bpe_display_group_contact_details filter
 * Filter takes the displayed group as a second argument
 * 
 * @package Groups
 * @since 	1.0
 * 
 * @uses 	bpe_check_empty()
 * @uses 	bpe_get_displayed_group()
 * @uses 	bpe_check_empty_object()
 * @uses 	apply_filters()
 * @return 	string 	$out 	Group contact details
 */
function bpe_display_group_address()
{
	$out = '';

	if( bpe_check_empty_object( bpe_get_displayed_group() ) )
	{
		$out .= '<h3>'. __( 'Contact Details', 'events' ) .'</h3>';
		$out .= '<p class="address">';
		$out .= bpe_check_empty( bpe_get_displayed_group( 'street' ), '<br />' );
		$out .= bpe_check_empty( bpe_get_displayed_group( 'postcode' ), '<br />' );
		$out .= bpe_check_empty( bpe_get_displayed_group( 'city' ), '<br />' );
		$out .= bpe_check_empty( bpe_get_displayed_group( 'country' ) );
		$out .= '</p>';
	
		$out .= '<p class="contact">';
		$out .= bpe_check_empty( bpe_get_displayed_group( 'telephone' ), '<br />', __( 'Telephone: ', 'events' ) );
		$out .= bpe_check_empty( bpe_get_displayed_group( 'mobile' ), '<br />', __( 'Mobile: ', 'events' ) );
		$out .= bpe_check_empty( bpe_get_displayed_group( 'fax' ), '<br />', __( 'Fax: ', 'events' ) );
		$out .= bpe_check_empty( bpe_get_displayed_group( 'website' ), '', __( 'Website: ', 'events' ), true );
		$out .= '</p>';
		$out .= '<div class="clear"></div>';
	}
	
	echo apply_filters( 'bpe_display_group_contact_details', $out, bpe_get_displayed_group() );
}
add_action( 'bp_before_group_header_meta', 'bpe_display_group_address', 20 );

/**
 * Get the displayed group
 * 
 * @package Groups
 * @since 	2.0
 * 
 * @param 	mixed 	$value 	Either boolean or string
 * @global 	object 	$bpe 	The Buddyvents settings
 * @return 	mixed
 */
function bpe_get_displayed_group( $value = false )
{
	global $bpe;
	
	if( ! isset( $bpe->displayed_group ) )
		return false;
	
	return ( empty( $value ) ) ? $bpe->displayed_group : $bpe->displayed_group->{$value};
}

/**
 * Helper function to check for empty values
 * 
 * @package Groups
 * @since 	1.0
 * 
 * @param 	string		$val
 * @param 	string		$suffix
 * @param 	string		$prefix
 * @param 	boolean		$clickable
 * @return 	mixed 		Either string or boolean
 * @uses 	make_clickable()
 */
function bpe_check_empty( $val, $suffix = '', $prefix = '', $clickable = false )
{
	if( ! empty( $val ) )
	{
		if( $clickable )
			$val = make_clickable( $val );
			
		return $prefix . $val . $suffix;
	}
	
	return false;
}

/**
 * Prepopulate the event location with a group address
 * 
 * Attached to wp_ajax_bpe_get_group_address
 * 
 * @package Groups
 * @since 	1.0
 * 
 * @uses 	check_ajax_referer()
 * @uses 	groups_get_groupmeta()
 * @return	mixed 	Either string or boolean
 */
function bpe_ajax_get_group_address()
{
	check_ajax_referer( 'bpe_add_event_'. bpe_get_option( 'general_slug' ) );
	
	if( empty( $_POST['group_id'] ) )
		exit;
		
	$addr = groups_get_groupmeta( (int)$_POST['group_id'], 'group_address' );
	
	$address_parts = array();
	
	if( ! empty( $addr['street'] ) )
		$address_parts[] = $addr['street'];

	if( ! empty( $addr['city'] ) )
		$address_parts[] = $addr['city'];

	if( ! empty( $addr['postcode'] ) )
		$address_parts[] = $addr['postcode'];

	if( ! empty( $addr['country'] ) )
		$address_parts[] = $addr['country'];

	die( implode( ', ', $address_parts ) );
}
add_action( 'wp_ajax_bpe_get_group_address', 'bpe_ajax_get_group_address' );

/**
 * Prepopulate the event location with a group address
 * 
 * Attached to bpe_group_dropdown_action action hook
 * 
 * @package Groups
 * @since 	1.0
 * @return 	string
 */
function bpe_ajax_get_group_address_js()
{
	?>
	<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#group_id').live( 'change', function() {
            var nonce = jQuery( "form#create-event-form #_wpnonce" ).val();
            var group_id = jQuery('#group_id').val();

            jQuery( 'form#create-event-form span.ajax-loader' ).show();
            
            jQuery.post( ajaxurl, {
                action: 'bpe_get_group_address',
                'cookie': encodeURIComponent(document.cookie),
                'group_id': group_id,
                '_wpnonce': nonce
            },
            function(response) {
                jQuery( 'form#create-event-form input#location' ).empty().val( response );
                jQuery( 'form#create-event-form span.ajax-loader' ).hide();
            });
            
            return false;
        });
    });
    </script>
    <?php
}
add_action( 'bpe_group_dropdown_action', 'bpe_ajax_get_group_address_js' );
?>