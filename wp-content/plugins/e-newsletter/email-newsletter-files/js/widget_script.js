jQuery( document ).ready( function() {

    //New Subscibes
    jQuery( "#new_subscribe" ).click( function() {
        if ( "" == jQuery( "#e_newsletter_email" ).val() ) {
            // append a error message
            jQuery( '#message' ).text( 'Please write your Email!' ).slideDown();
            return false;
        }

        jQuery( "#newsletter_action" ).val( 'new_subscribe' );
        jQuery( "#subscribes_form" ).submit();
        return false;
    });

    //Save Subscibes
    jQuery( "#save_subscribes" ).click( function() {
        jQuery( "#newsletter_action" ).val( 'save_subscribes' );
        jQuery( "#subscribes_form" ).submit();
        return false;
    });

    //Unsubscribes
    jQuery( "#unsubscribe" ).click( function() {
        jQuery( "#newsletter_action" ).val( 'unsubscribe' );
        jQuery( "#subscribes_form" ).submit();
        return false;
    });
});