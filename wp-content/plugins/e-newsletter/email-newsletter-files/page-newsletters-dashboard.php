<?php

    $newsletters_sent = $this->get_sent_newsletters();
    if ( 5 < count( $newsletters_sent ) )
        $newsletters_sent = array_slice ( $newsletters_sent, 0, 5 );

    $members = $this->get_members( array( 'limit' => 'LIMIT 0,5' ) );
    if ( 5 < count( $members ) )
        $members = array_slice ( array_reverse( $members ), 0, 5 );


    //Display status message
    if ( isset( $_GET['updated'] ) ) {
        ?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
    }

?>

    <div class="wrap">
        <h2><?php _e( 'Newsletters Dashboard', 'email-newsletter' ) ?></h2>
        <table width="700px" class="widefat post newsletter_table_center" style="width:95%;">
            <thead>
                <tr>
                    <th>
                        <?php _e( 'Newsletters', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Members', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Groups', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Sent', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Waiting', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'CRON', 'email-newsletter' ) ?>
                        (<?php echo wp_next_scheduled( $this->cron_send_name ) ? __( 'enabled', 'email-newsletter') : __( 'disabled', 'email-newsletter'); ?>)

                    </th>
                </tr>
            </thead>

            <tr class='alternate'>
                <td>
                    <?php echo $this->get_count_newsletters(); ?>
                </td>
                <td>
                    <?php echo $this->get_count_members(); ?>
                </td>
                <td>
                    <?php echo $this->get_count_groups(); ?>
                </td>
                <td>
                    <?php echo $this->get_count_sent(); ?>
                </td>
                <td>
                    <?php echo $this->get_count_send_members( '', 'waiting_send' ); ?>
                </td>
                <td>
                    <?php echo $this->get_count_send_members( '', 'by_cron' ); ?>
                </td>
            </tr>
        </table>
        <br clear="all"/>

        <h3><?php _e( '5 Latest Sent Newsletters:', 'email-newsletter' ) ?></h3>
        <table width="700px" class="widefat post newsletter_table_center" style="width:95%;">
            <thead>
                <tr>
                    <th>
                        <?php _e( 'Date of Last Sent', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Email Subject', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Sent From', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Sent To', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Bounced', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Opened', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Actions', 'email-newsletter' ) ?>
                    </th>
                </tr>
            </thead>
        <?php
        $i = 0;
        if ( $newsletters_sent )
            foreach( $newsletters_sent as $one_sent ) {
                if ( $i % 2 == 0 )
                    echo "<tr class='alternate'>";
                else
                    echo "<tr class='' >";

                $i++;
        ?>
                <td>
                   <?php echo date( $this->settings['date_format'] . " h:i:s", $one_sent['last_sent_time'] ); ?>
                </td>
                <td style="text-align: left;">
                    <?php echo $one_sent['subject']; ?>
                </td>
                <td style="text-align: left;">
                    &lt;<?php echo $one_sent['from_name']; ?>&gt;
                    <?php echo $one_sent['from_email']; ?>
                </td>
                <td>
                     <?php echo $one_sent['count_sent']; ?> <?php _e( 'members', 'email-newsletter' ) ?>
                </td>
                <td>
                     <?php echo $one_sent['count_bounced']; ?> <?php _e( 'members', 'email-newsletter' ) ?>
                </td>
                <td>
                     <?php echo $one_sent['count_opened']; ?> <?php _e( 'members', 'email-newsletter' ) ?>
                </td>
                <td style="width: 280px;">
                    <a href="?page=newsletters-create&newsletter_id=<?php echo $one_sent['newsletter_id'];?>">
                        <input type="button" value="<?php _e( 'Edit', 'email-newsletter' ) ?>" />
                    </a>
                    <a href="?page=newsletters-dashboard&newsletter_action=delete_newsletter&newsletter_id=<?php echo $one_sent['newsletter_id'];?>">
                        <input type="button" value="<?php _e( 'Delete', 'email-newsletter' ) ?>" />
                    </a>
                    <a href="?page=newsletters-dashboard&newsletter_action=send_newsletter&newsletter_id=<?php echo $one_sent['newsletter_id'];?>">
                        <input type="button" value="<?php _e( 'Send Page', 'email-newsletter' ) ?>" />
                    </a>
                </td>
            </tr>
        <?php
            }
        ?>

        </table>
        <br clear="all"/>
        <center>
            <a href="?page=newsletters-create" style="text-decoration: none;" >
                <input type="button" value="<?php _e( 'Create New Newsletter', 'email-newsletter' ) ?>" />
            </a>
        </center>
        <br clear="all"/>

        <h3><?php _e( '5 Latest Members:', 'email-newsletter' ) ?></h3>
        <table width="700px" class="widefat post newsletter_table_center" style="width:95%; table-layout: inherit;">
            <thead>
                <tr>
                    <th>
                        <?php _e( 'Email Address', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Name', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Join Date', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Number Sent', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Number Opened', 'email-newsletter' ) ?>
                    </th>
                    <th>
                        <?php _e( 'Groups', 'email-newsletter' ) ?>
                    </th>
                </tr>
            </thead>
        <?php
        $i = 0;
        if ( $members )
            foreach( $members as $member ) {
                if ( $i % 2 == 0 )
                    echo "<tr class='alternate'>";
                else
                    echo "<tr class='' >";

                $i++;

                $member['member_nicename'] = $member['member_fname'];
                $member['member_nicename'] .= $member['member_lname'] ? ' ' . $member['member_lname'] : '';
        ?>
                <td style="text-align: left;">
                   <?php echo $member['member_email']; ?>
                </td>
                <td style="text-align: left;">
                    <?php echo $member['member_nicename']; ?>
                </td>
                <td>
                    <?php echo date( $this->settings['date_format'] . " h:i:s", $member['join_date'] ); ?>
                </td>
                <td>
                    <?php echo $this->get_count_sent_to_user( $member['member_id'] ); ?> <?php _e( 'newsletters', 'email-newsletter' ) ?>
                </td>
                <td>
                    <?php echo $this->get_count_opened_by_user( $member['member_id'] ); ?> <?php _e( 'newsletters', 'email-newsletter' ) ?>
                </td>
                <td>
                <?php
                    if ( "" != $member['unsubscribe_code'] ) {
                        $groups_id = $this->get_memeber_groups( $member['member_id'] );
                        if ( $groups_id ) {
                            $groups = "";
                            foreach ( $groups_id as $group_id) {
                                $group = $this->get_group_by_id( $group_id );
                                $groups .= $group['group_name'] . ", ";
                            }
                            echo substr( $groups, 0, strlen( $groups )-2 );
                        }
                    } else {
                        echo '<span class="red" >' . __( 'Unsubscribed', 'email-newsletter' ) . '</span>';
                    }
                ?>
                </td>
            </tr>
        <?php
            }
        ?>
        </table>

    </div><!--/wrap-->