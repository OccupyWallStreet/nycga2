<?php

$member_id = get_query_var( 'unsubscribe_member_id' );
$unsubscribe_code = get_query_var( 'unsubscribe_code' );

if ( 0 < get_query_var( 'unsubscribe_member_id' ) ) {
    global $wpdb;
    $member_id = $wpdb->get_var( $wpdb->prepare( "SELECT member_id FROM {$this->tb_prefix}enewsletter_members WHERE member_id = %d AND unsubscribe_code = '%s'", $member_id, $unsubscribe_code ) );

    if ( 0 < $member_id ) {
        if ( $this->unsubscribe( $unsubscribe_code, 'false' ) )
            echo "<center><br /><br /><br /><h2 style='color: #19700A;'>" . __( 'You are successfully unsubscribed!', 'email-newsletter' ) . "</h2></center>";

        exit;

//    $email_newsletter =& new Email_Newsletter();
//    $email_newsletter->unsubscribe( get_query_var( 'code'), 'http://localhost/wpblog32_1/e-newsletter/unsubscribe/78880970f028f5d/0/' );
    }
    echo "<center><br /><br /><br /><h2 style='color: #ff0000;'>" . __( 'You are already unsubscribed or are not subscribed yet!', 'email-newsletter' ) . "</h2></center>";
}

exit;

?>