<?php
    $siteurl = get_option( 'siteurl' );

    $newsletter_data = $this->get_newsletter_data( $_REQUEST['newsletter_id'] );

    $groups = $this->get_groups();

    //send newsletter
    if ( ! isset( $_REQUEST['send_id'] ) ) {
        $check_key = substr( md5( uniqid( rand(), true ) ), 0, 7);
        $_SESSION['check_key'] = $check_key;
    }

    //Display status message
    if ( isset( $_GET['updated'] ) ) {
        ?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
    }

?>

     <script type="text/javascript">
        jQuery( document ).ready( function() {
            var cron = 0;

            jQuery( '#add_cron' ).click( function() {
                cron = 1;
                jQuery( '#send_form' ).submit();
            });


            jQuery( '#send_form' ).submit( function() {
                error = '1';

                if ( true == jQuery( "input[name='all_members']" ).prop( 'checked' ) )
                    error = '0'

                jQuery( "input[name='group_name[]']" ).each( function() {
                    if ( true == jQuery(this).prop( 'checked' ) )
                        error = '0'
                });

                jQuery( "input[name='group_id[]']" ).each( function() {
                    if ( true == jQuery(this).prop( 'checked' ) )
                        error = '0'
                });

                if ( '1' == error ) {
                    alert( "<?php _e( 'Please select members.', 'email-newsletter' ) ?>" );
                    cron = 0;
                    return false;
                } else {
                    if ( 1 == cron ) {
                        jQuery( '#cron' ).val( 'cron' );
                    }
                    return true;
                }
            });

        });

     </script>


    <div class="wrap">
        <h2><?php _e( 'Send Newsletter:', 'email-newsletter' ) ?> "<?php echo htmlspecialchars( $newsletter_data['subject'] );?>"</h2>
        <p><?php _e( 'At this page you can send newsletter to the selected groups.', 'email-newsletter' ) ?></p>

        <?php

        if ( isset( $_REQUEST['send_id'] ) && 0 < $_REQUEST['send_id'] ) {
            $send_id        = $_REQUEST['send_id'];
            $newsletter_id  = $_REQUEST['newsletter_id'];

            $count_send_members = $this->get_count_send_members( $send_id );
        ?>
            <center>
                <p>The Newsletter was sent to <span id="count_sent">0</span> out of <?php echo $count_send_members; ?> members</p>
                <div class="enewsletter_progressbar">
                    <div id="progressbar">
                        <span id="progressbar_text">
                            <?php echo _e( "Sending", 'email-newsletter' ) ?>
                        </span>
                    </div>
                </div>
                <form method="post" action="" id="sending_form" >
                    <input type="hidden" name="newsletter_id" value="<?php echo $newsletter_id; ?>">
                    <input type="hidden" name="send_id" value="<?php echo $send_id; ?>">
                    <input type="hidden" name="action" value="send_newsletter">
                    <input type="hidden" name="cron" value="add_to_cron" />
                    <input type="button" id="send_pause" value="<?php echo _e( 'Pause', 'email-newsletter' ) ?>" />
                    <input type="button" id="send_cron" value="<?php echo _e( 'Pause, and send by WP-CRON', 'email-newsletter' ) ?>" />
                    <input type="button" id="send_cancel" value="<?php echo _e( 'Cancel', 'email-newsletter' ) ?>" />
                </form>
            </center>

            <script type="text/javascript">
                jQuery( document ).ready( function() {
                    var pause = 0;

                    jQuery( function() {
                        jQuery( "#progressbar" ).progressbar({
                            value: 0
                        });
                    });

                    jQuery( '#send_cron' ).click( function () {
                        pause = 1;
                        jQuery( '#sending_form' ).submit();
                    });

                    jQuery( '#send_cancel' ).click( function () {
                        pause = 1;
                        window.location.href = "?page=<?php echo $_REQUEST['page']; ?>&newsletter_action=send_newsletter&newsletter_id=<?php echo $newsletter_id; ?>";
                    });

                    jQuery( '#send_pause' ).click( function () {
                        if ( 1 == pause ) {
                            pause = 0;
//                            jQuery( "#send_cancel" ).attr( 'disabled', true );
                            jQuery( "#progressbar_text" ).html( '<?php echo _e( 'Sending', 'email-newsletter' ) ?>' );
                            jQuery( this ).val( '<?php echo _e( "Pause", 'email-newsletter' ) ?>' );
                            jQuery( this ).send_email();
                        } else {
                            pause = 1;
//                            jQuery( "#send_cancel" ).attr( 'disabled', false );
                            jQuery( "#progressbar_text" ).html( '<?php echo _e( 'Pause', 'email-newsletter' ) ?>' );
                            jQuery( this ).val( '<?php echo _e( "Continue", 'email-newsletter' ) ?>' );
                        }

                    });

                    var count_email = <?php echo $count_send_members ; ?>;
//                    var step = Math.round( 100 / count_email + 0.4 )
                    var step = 100 / count_email
                    var send = 1;

                    jQuery.fn.send_email = function ( ) {
                        jQuery.ajax({
                            type: 'POST',
                            url: '<?php echo $siteurl;?>/wp-admin/admin-ajax.php',
                            data: 'action=send_email_to_member&send_id=<?php echo $send_id ; ?>&check_key=<?php echo $_REQUEST['check_key'] ; ?>',
                            success: function( html ){
                                if ( 'ok' == html ) {

                                    jQuery( '#count_sent' ).html( send );

                                    value = step * send;

                                    jQuery( "#progressbar" ).progressbar( "option", "value", value );

                                    send++;
                                    if ( 1 != pause )
                                        jQuery( this ).send_email();


                                } else if ( 'end' == html) {
                                     jQuery( "#send_pause" ).hide();
                                     jQuery( "#send_cron" ).hide();
                                     jQuery( "#progressbar_text" ).html( '<?php echo _e( 'Done', 'email-newsletter' ) ?>' );
                                } else {
                                    alert( html );
                                }
                            }
                        });
                    };

                    jQuery( this ).send_email();

                });

            </script>

        <?php
        } else {
        ?>

        <form action="" method="post" id="send_form">
            <input type="hidden" name="newsletter_id" value="<?php echo $newsletter_data["newsletter_id"];?>">
            <input type="hidden" name="cron" id="cron" value="">
            <input type="hidden" name="check_key" id="check_key" value="">
            <input type="hidden" name="action" value="send">
            <table cellpadding="10" cellspacing="10" class="widefat post">
                <thead><tr>

                        <th>
                            <?php _e( 'Select which groups you would like to send to:', 'email-newsletter' ) ?>
                        </th>

                </tr></thead>
                <tr>
                    <td>
                    <p>
                        <label><input type="checkbox" name="all_members" value="1" /> <strong><?php _e( 'All Members - except unsubscribed', 'email-newsletter' ) ?></strong> (<?php echo $this->get_count_members();?>)</label><br/>
                        &nbsp;&nbsp;-or-<br/>
                        <?php
                            foreach ( array('administrator', 'editor', 'author', 'contributor', 'subscriber') as $role ) {
                                $col = count ( get_users( array( 'role' => $role ) ) );
                                if ( 0 < $col )
                                    echo "<label><input type='checkbox' name='group_name[]' value='{$role}' /> All site {$role}s ({$col})</label><br>";
                            }

                            if ( $groups )
                                foreach ( $groups as $group ) {
                                    $col = count( $this->get_members_of_group( $group['group_id'] ) );
                                    if ( 0 < $col )
                                        echo "<label><input type='checkbox' name='group_id[]' value='{$group['group_id']}' /> {$group['group_name']} ({$col})</label><br>";
                                }
                        ?>
                        <br />
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                         <label>
                             <input type="checkbox" name="dont_send_duplicate" value="1" checked="checked" />
                             <?php echo _e( "Don't send to people who've already received this:", 'email-newsletter' ) ?>
                         </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="send" value="<?php echo _e( 'Send Newsletter', 'email-newsletter' ) ?>" />
                        <input type="button" name="send" id="add_cron" value="<?php echo _e( 'Add Newsletter to CRON list', 'email-newsletter' ) ?>" />
                    </td>
                </tr>
            </table>

        </form>

        <?php
            $sends = $this->get_sends( $_REQUEST['newsletter_id'] );
            $total = array ( 'send' => 0, 'cron' => 0, 'sent' => 0, 'bounced' => 0 );
        ?>

        <h2><?php _e( 'Previous sending:', 'email-newsletter' ) ?></h2>

        <table width="700px" class="widefat post" id="send_list" style="width:95%;">
            <thead>
                <tr>
                    <th>
                        <?php _e( 'Start Date', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Waiting send (manualy)', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Waiting send (cron)', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Bounced', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Sent', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Actions', 'email-newsletter' ) ?>
                    </th>
                </tr>
            </thead>
        <?php
        $i = 0;
        if ( $sends )
            foreach( $sends as $send ) {
                if ( $i % 2 == 0 )
                    echo "<tr class='alternate'>";
                else
                    echo "<tr class='' >";

                $i++;
        ?>
                <td style="vertical-align: middle;">
                   <?php echo date( $this->settings['date_format'] . " h:i:s", $send['start_time'] ); ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php
                        echo $send['count_send_members'];
                        $total['send'] += $send['count_send_members'];
                    ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php
                        echo $send['count_send_cron'];
                        $total['cron'] += $send['count_send_cron'];
                    ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php
                        echo $send['count_bounced'];
                        $total['bounced'] += $send['count_bounced'];
                    ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php
                        echo $send['count_sent'];
                        $total['sent'] += $send['count_sent'];
                    ?>
                </td>
                <td style="vertical-align: middle; width: 220px;">
                <?php
                    if ( 0 < $send['count_send_members'] ) :
                ?>
                        <a href="?page=<?php echo $_REQUEST['page']; ?>&newsletter_action=send_newsletter&cron=add_to_cron&newsletter_id=<?php echo $newsletter_data["newsletter_id"];?>&send_id=<?php echo $send['send_id'];?>">
                            <input type="button" value="<?php echo _e( "Add to CRON list", 'email-newsletter' ) ?>" />
                        </a>
                        <a href="?page=<?php echo $_REQUEST['page']; ?>&newsletter_action=send_newsletter&newsletter_id=<?php echo $newsletter_data["newsletter_id"];?>&send_id=<?php echo $send['send_id'];?>&check_key=<?php echo $check_key;?>">
                            <input type="button" value="<?php _e( 'Continue Send', 'email-newsletter' ) ?>" />
                        </a>
                <?php
                    endif;
                ?>
                </td>
            </tr>
        <?php
            }
        ?>
            <thead>
                <tr>
                    <th>
                        <?php _e( 'Total:', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php echo $total['send']; ?>
                    </th>
                    <th>
                       <?php echo $total['cron']; ?>
                    </th>
                    <th>
                        <?php echo $total['bounced']; ?>
                    </th>
                    <th>
                        <?php echo $total['sent']; ?>
                    </th>
                    <th>
                    </th>
                </tr>
            </thead>
        </table>

        <?php } ?>

    </div><!--/wrap-->