<?php
/**
* Plugin functions class
**/
class Email_Newsletter_functions {


    /**
     * load template for page
     **/
    function load_template( $name ) {
        $path = locate_template( $name );

        if ( !$path ) {
            $path = $this->plugin_dir . "email-newsletter-files/$name";
        }

        load_template( $path );
        die;
    }

    function add_rewrite_rule( $regex, $args, $position = 'top' ) {
        global $wp, $wp_rewrite;

        $result = add_query_arg( $args, 'index.php' );
        add_rewrite_rule( $regex, $result, $position );
    }



    /**
     * Show not menu page
     **/
    function template_redirect() {
//        global $wp_query;
        if ( $this->is_enewsletter_page( 'unsubscribe_page' ) ) {
//            $this->load_template( 'page-unsubscribe.php' );
            require_once( $this->plugin_dir . "email-newsletter-files/page-unsubscribe.php" );
            exit;
        }
    }



    /**
     * Generate Unsubscribe code
     **/
    function gen_unsubscribe_code() {
        $now = time();
        $unsubscribe_code = substr( $now, strlen( $now ) - 3, 3 ) . substr( md5( uniqid( rand(), true ) ), 0, 8 ) . substr( md5( $now . rand() ), 0, 4);
        return $unsubscribe_code;
    }

    /**
     * function for sorting an array of arrays by volue of field
     **/
    function sort_array_by_field ( $array, $field, $sort = "asc" ) {
        $fn = create_function( '$a, $b', '
            if( $a["' . $field . '"] == $b["' . $field . '"] ) return 0;
            if ( "asc" == "' . $sort . '")
                return ( $a["' . $field . '"] < $b["' . $field . '"] ) ? -1 : 1;
            else
                return ( $a["' . $field . '"] > $b["' . $field . '"] ) ? -1 : 1;
        ');

        usort($array, $fn);
        return $array;
    }

    /**
     * Checking of duplicate send
     **/
    function check_duplicate_send( $newsletter_id, $member_id ) {
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare( "SELECT b.member_id FROM {$this->tb_prefix}enewsletter_send a, {$this->tb_prefix}enewsletter_send_members b WHERE a.newsletter_id = %d AND a.send_id = b.send_id AND b.member_id = %d ", $newsletter_id, $member_id ), "ARRAY_A");
        if ( 0 < $result )
            return true;
        else
            return false;
    }

    /**
     * Get count of sent email by newsletter_id or for all newsletters
     **/
     function get_count_sent( $newsletter_id = '' ) {
        global $wpdb;
        if ( '' === $newsletter_id )
            $count = $wpdb->get_row( "SELECT Count(b.member_id) FROM {$this->tb_prefix}enewsletter_send_members b  WHERE b.status = 'sent'", "ARRAY_A");
        else
            $count = $wpdb->get_row( $wpdb->prepare( "SELECT Count(b.member_id) FROM {$this->tb_prefix}enewsletter_send a, {$this->tb_prefix}enewsletter_send_members b  WHERE a.newsletter_id = %d AND a.send_id = b.send_id AND b.status = 'sent'", $newsletter_id ), "ARRAY_A");
        return $count['Count(b.member_id)'];
    }

    /**
     * Get count of bounced email by newsletter_id or for all newsletters
     **/
     function get_count_bounced( $newsletter_id = '' ) {
        global $wpdb;
        if ( '' === $newsletter_id )
            $count = $wpdb->get_row( "SELECT Count(b.member_id) FROM {$this->tb_prefix}enewsletter_send_members b  WHERE b.status = 'bounced'", "ARRAY_A");
        else
            $count = $wpdb->get_row( $wpdb->prepare( "SELECT Count(b.member_id) FROM {$this->tb_prefix}enewsletter_send a, {$this->tb_prefix}enewsletter_send_members b  WHERE a.newsletter_id = %d AND a.send_id = b.send_id AND b.status = 'bounced'", $newsletter_id ), "ARRAY_A");
        return $count['Count(b.member_id)'];
    }

    /**
     * Get count of all newsletters
     **/
     function get_count_newsletters() {
        global $wpdb;
        $count = $wpdb->get_row( "SELECT Count(newsletter_id) FROM {$this->tb_prefix}enewsletter_newsletters", "ARRAY_A");
        return $count['Count(newsletter_id)'];
    }
    /**
     * Get count of all groups
     **/
     function get_count_groups() {
        global $wpdb;
        $count = $wpdb->get_row( "SELECT Count(group_id) FROM {$this->tb_prefix}enewsletter_groups", "ARRAY_A");
        return $count['Count(group_id)'];
    }

    /**
     * Get count of all members
     **/
     function get_count_members() {
        global $wpdb;
        $count = $wpdb->get_row( "SELECT Count(member_id) FROM {$this->tb_prefix}enewsletter_members WHERE unsubscribe_code != ''", "ARRAY_A");
        return $count['Count(member_id)'];
    }

    /**
     * Get count of opened email
     **/
     function get_count_opened( $newsletter_id ) {
        global $wpdb;
        $count = $wpdb->get_row( $wpdb->prepare( "SELECT Count(b.member_id) FROM {$this->tb_prefix}enewsletter_send a, {$this->tb_prefix}enewsletter_send_members b  WHERE a.newsletter_id = %d AND a.send_id = b.send_id AND b.opened_time > 0", $newsletter_id ), "ARRAY_A");
        return $count['Count(b.member_id)'];
    }

    /**
     * Get count of sent email to user
     **/
     function get_count_sent_to_user( $member_id ) {
        global $wpdb;
        $count = $wpdb->get_row( $wpdb->prepare( "SELECT Count(member_id) FROM {$this->tb_prefix}enewsletter_send_members WHERE member_id = %d  AND status = 'sent'", $member_id ), "ARRAY_A");
        return $count['Count(member_id)'];
    }

    /**
     * Get count of opened email by user
     **/
     function get_count_opened_by_user( $member_id ) {
        global $wpdb;
        $count = $wpdb->get_row( $wpdb->prepare( "SELECT Count(member_id) FROM {$this->tb_prefix}enewsletter_send_members WHERE member_id = %d AND opened_time > 0", $member_id ), "ARRAY_A");
        return $count['Count(member_id)'];
    }

    /**
     * Get all templates
     **/
    function get_templates(){
        $template_dirs = glob( $this->plugin_dir . "email-newsletter-files/templates/*" );
        $templates = array();
        foreach( $template_dirs as $template_dir ){
            $templates[] = array(
                "dir" => $template_dir,
                "name" => basename( $template_dir ),
            );
        }
        return $templates;
    }

    /**
     * Get all uploads images
     **/
    function get_uploaded_images(){
        $upload_files = glob( $this->plugin_dir . "email-newsletter-files/uploads/*" );

        if ( !is_array( $upload_files ) )
            return false;

        $uploads = '<option value="">' . __( 'Select an image', 'email-newsletter' ) . '</option>';
        foreach( $upload_files as $upload_file ) {
            $uploads .='<option value="' . $this->plugin_url . 'email-newsletter-files/uploads/' . basename( $upload_file ) . '"> ' . basename( $upload_file ) . ' </option>';
        }
        return $uploads;
    }

    /**
     * checks that current page is e-newsletter's page
     **/
    function is_enewsletter_page ( $page = '' ) {
        switch ( $page ) {
            case 'newsletters':
            case 'newsletters-dashboard':
            case 'newsletters-create':
            case 'newsletters-groups':
            case 'newsletters-members':
            case 'newsletters-subscribes':
            case 'newsletters-settings':
                return 1;
                break;
            case 'unsubscribe_page':
                if ( 1 == get_query_var( 'unsubscribe_page' ) )
                    return 1;
                else
                    return 0;
                break;
            default:
                return 0;
        }
    }

    /**
     * Add some periods for CRON
     **/
    function add_new_cron_time( $schedules ) {

        $schedules['enewsletter_min_2'] = array(
            'interval' => 1*60,
            'display' => __('every 2 min')
        );

        return $schedules;
    }

    /**
     * Send email
     **/
    function send_email( $email_from_name, $email_from, $email_to, $email_subject, $email_contents,  $options=array() ) {

        $options['to']          = $email_to;
        $options['subject']     = $email_subject;
        $options['from']        = $email_from;
        $options['from_name']   = $email_from_name;

        foreach( array( "to", "cc", "bcc", "reply_to" ) as $type ) {
            if( ! $options[$type] ) {
                $options[$type]=array();
            } else if( ! is_array( $options[$type] ) ) {
                $emails = explode( ",", $options[$type] );
                $options[$type] = array();
                foreach( $emails as $e ) {
                    if ( $e=trim( $e ) ) {
                        $options[$type][]=$e;
                    }
                }
            }
        }

        require_once( $this->plugin_dir . "email-newsletter-files/phpmailer/class.phpmailer.php" );

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';

        //Set Sending Method
        switch( $this->settings['outbound_type'] ) {
            case 'smtp':
                $mail->IsSMTP();
                $mail->Host     = $this->settings['smtp_host'];
                $mail->SMTPAuth = ( strlen( $this->settings['smtp_user'] ) > 0 );
                if( $mail->SMTPAuth ){
                    $mail->Username = $this->settings['smtp_user'];
                    $mail->Password = $this->_decrypt( $this->settings['smtp_pass'] );
                }
                break;

            case 'mail':
                $mail->IsMail();
                break;

            case 'sendmail':
                $mail->IsSendmail();
                break;
        }


        $mail->From = $options['from'];
        if( $options['from_name'] ) {
            $mail->FromName = $options['from_name'];
        }
        $mail->Subject = $options['subject'];

        $mail->isHTML( true );


        $mail->MsgHTML( $email_contents );


        foreach( $options['to'] as $email ) {
            $mail->AddAddress( $email );
        }
        foreach( $options['cc'] as $email ) {
            $mail->AddCC( $email );
        }
        foreach( $options['bcc'] as $email ) {
            $mail->AddBCC( $email );
        }
        foreach( $options['reply_to'] as $email ) {
            $mail->AddReplyTo( $email );
        }

        if( $options['bounce_email']  ){
            $mail->Sender = $options['bounce_email'];
        }
        if( $options['message_id'] ) {
            $mail->MessageID = $options['message_id'];
        }

        if( ! $mail->Send() ) {
            echo $mail->ErrorInfo;
            return false;
        }
        return true;
    }

    /**
     * Check bounces email
     **/
    function check_bounces() {
        global $wpdb;

        @set_time_limit( 0 );
        $email_address  = $this->settings['bounce_email'];
        $email_username = $this->settings['bounce_username'];
        $email_password = $this->settings['bounce_password'];
        $email_host     = trim( $this->settings['bounce_host'] );
        $email_port     = ( $this->settings['bounce_port'] ) ? $this->settings['bounce_port'] : 110;

        if( ! $email_host )
            return true;

        $mbox = imap_open ( '{'.$email_host.':'.$email_port.'/pop3/notls}INBOX', $email_username, $email_password ) or die( imap_last_error() );

        if( ! $mbox ) {
            return 'Error: Failed to connect when checking bounces!';
        } else {
            $MC     = imap_check( $mbox );
            $mails  = imap_fetch_overview( $mbox, "1:{$MC->Nmsgs}", 0 );

            foreach ( $mails as $mail ) {
                $body = imap_body ( $mbox, $mail->msgno );

                if( preg_match( '/Message-ID:\s*<?Newsletters-(\d+)-(\d+)-([A-Fa-f0-9]{32})/i', $body, $matches) ) {

                    $member_id      = ( int ) $matches[1];
                    $send_id        = ( int ) $matches[2];
                    $email_hash     = trim( $matches[3] );
                    $hash           = md5( 'Hash of bounce member_id='. $member_id . ', send_id='. $send_id );

                    if( $email_hash == $hash ){
                        $result = $wpdb->query( $wpdb->prepare( "UPDATE {$this->tb_prefix}enewsletter_send_members SET status = 'bounced' WHERE send_id = %d AND member_id = %d AND status = 'sent'", $send_id, $member_id ) );
                        imap_delete( $mbox, $mail->msgno );
                        echo 'ok';
                    } else {
                        echo 'Error: hash';
                    }
                }
            }
            imap_expunge( $mbox );
            imap_close( $mbox );
        }
    }

    /**
     * Save Settings
     **/
     function save_settings( $settings ) {
        global $wpdb;

        if( ! is_array( $settings ) )
            $settings = array();

        //change time for CRON
        if ( 1 == $settings['cron_enable'] ) {
            wp_schedule_event( time(), 'enewsletter_min_2', $this->cron_send_name );
        } else {
            if ( wp_next_scheduled( $this->cron_send_name ) )
                wp_clear_scheduled_hook( $this->cron_send_name );
        }

        if ( $settings['send_limit'] )
            $settings['send_limit'] = (int) trim( $settings['send_limit'] );

        //Encrypt password
        if ( isset( $settings['smtp_pass'] ) && '********' == $settings['smtp_pass'] )
            unset( $settings['smtp_pass'] );
        elseif( isset( $settings['smtp_pass'] ) && '' != $settings['smtp_pass'] )
            $settings['smtp_pass'] = $this->_encrypt( $settings['smtp_pass'] );
        else
            $settings['smtp_pass'] = '';
		

        foreach( $settings as $key=>$item )
             $result = $wpdb->query( $wpdb->prepare( "REPLACE INTO {$this->tb_prefix}enewsletter_settings SET `key` = '%s', `value` = '%s'", $key, stripslashes( $item ) ) );
		
		
        $this->get_settings();

        if ( "install" == $_REQUEST['mode']) {
            // first setup of plugin
            wp_redirect( add_query_arg( array( 'page' => 'newsletters-dashboard', 'updated' => 'true', 'dmsg' => urlencode( __( 'The Plugin is installed!', 'email-newsletter' ) ) ), 'admin.php' ) );
            exit;
        } else {
            wp_redirect( add_query_arg( array( 'page' => 'newsletters-settings', 'updated' => 'true', 'dmsg' => urlencode( __( 'The Settings are saved!', 'email-newsletter' ) ) ), 'admin.php' ) );
            exit;
        }
    }

    /**
     * Get Settings
     **/
    function get_settings() {
		
		//Coming Soon
		/*if(is_multisite() && is_network_admin()) {
			$this->settings = get_site_option('enewsletter_settings');
			return $this->settings;
		}*/
		
		
        global $wpdb;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->tb_prefix}enewsletter_settings'" ) == "{$this->tb_prefix}enewsletter_settings" ) {
            $results = $wpdb->get_results( "SELECT * FROM {$this->tb_prefix}enewsletter_settings ORDER BY `key`", "ARRAY_A" );

            if ( $results ) {
                foreach( $results as $setting )
                    $this->settings[$setting['key']] = $setting['value'];

                //Set date format
                $date_format = get_option( 'date_format' );
                if ( $date_format )
                    $this->settings['date_format'] = $date_format;
                else
                    $this->settings['date_format'] = "Y-m-d";

                return $this->settings;
            }
        }
        return false;
    }


    /**
     * Get All Sends
     **/
    function get_sends( $newsletter_id ) {
		
        global $wpdb;
        $sends = NULL;
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->tb_prefix}enewsletter_send WHERE newsletter_id = %d ORDER BY start_time DESC", $newsletter_id ), "ARRAY_A");

        foreach( $results as $result ){
            $result['count_send_members']   = $this->get_count_send_members( $result['send_id'], 'waiting_send' );
            $result['count_send_cron']      = $this->get_count_send_members( $result['send_id'], 'by_cron' );
            $result['count_sent']           = $this->get_count_send_members( $result['send_id'], 'sent' );
            $result['count_bounced']        = $this->get_count_send_members( $result['send_id'], 'bounced' );

            $sends[] = $result;
        }
        return $sends;
    }

    /**
     * Get count send member
     **/
    function get_count_send_members( $send_id = '', $status = 'waiting_send' ) {
        global $wpdb;
        if ( '' === $send_id )
            $count = $wpdb->get_row( $wpdb->prepare( "SELECT Count(member_id) FROM {$this->tb_prefix}enewsletter_send_members WHERE status = '%s'", $status ), "ARRAY_A");
        else
            $count = $wpdb->get_row( $wpdb->prepare( "SELECT Count(member_id) FROM {$this->tb_prefix}enewsletter_send_members WHERE send_id = %d AND status = '%s'", $send_id, $status ), "ARRAY_A");

        return $count['Count(member_id)'];
    }

    /**
     * Get Sent Newsletters
     **/
    function get_sent_newsletters() {
        global $wpdb;
        $newsletters = NULL;
        $results = $wpdb->get_results( "SELECT * FROM {$this->tb_prefix}enewsletter_newsletters WHERE newsletter_id IN (SELECT newsletter_id FROM {$this->tb_prefix}enewsletter_send GROUP BY newsletter_id)", "ARRAY_A");

        foreach( $results as $result ){

            //count of sent email
            $result["count_sent"] = $this->get_count_sent( $result['newsletter_id'] );

            //count of bounced email
            $result["count_bounced"] = $this->get_count_bounced( $result['newsletter_id'] );

            //count of opened email
            $result["count_opened"] = $this->get_count_opened( $result['newsletter_id'] );

            $last_sent_time = $wpdb->get_row( $wpdb->prepare( "SELECT start_time FROM {$this->tb_prefix}enewsletter_send WHERE newsletter_id = %d ORDER BY start_time DESC", $result['newsletter_id'] ), "ARRAY_A");
            $result["last_sent_time"] = $last_sent_time['start_time'];

            $newsletters[] = $result;
        }

        return $newsletters;
    }

    /**
     * Get all data of newsletter
     **/
     function get_newsletter_data( $newsletter_id ) {
        global $wpdb;
        $newsletter = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tb_prefix}enewsletter_newsletters WHERE newsletter_id = %d", $newsletter_id ), "ARRAY_A");
        return $newsletter;
    }

    /**
     * Get all data of all newsletters
     **/
     function get_newsletters() {
        global $wpdb;
        $newsletters = $wpdb->get_results( "SELECT * FROM {$this->tb_prefix}enewsletter_newsletters", "ARRAY_A");
        return $newsletters;
    }

    /**
     * Get member by ID
     **/
    function get_member( $member_id ) {
        global $wpdb;
        $member =  $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tb_prefix}enewsletter_members WHERE member_id = %d", $member_id ), "ARRAY_A" );

        $member['member_nicename'] = $member['member_fname'];
        $member['member_nicename'] .= $member['member_lname'] ? ' ' . $member['member_lname'] : '';

        if ( $member['member_info'] ) {
            $member =  array_merge ( $member, unserialize( $member['member_info'] ) );
            unset( $member['member_info'] );
        }

        $member['count_sent']   = $this->get_count_sent_to_user( $member_id );
        $member['count_opened'] = $this->get_count_opened_by_user( $member_id );

        return $member;
    }

    /**
     * Get member id of wp user
     **/
    function get_members_by_wp_user_id( $wp_user_id, $blog_id = '' ) {
        global $wpdb;

        //Checking DB prefix
        if ( 0 !== $blog_id && 1 < $blog_id )
            $tb_prefix = $wpdb->base_prefix . $blog_id . '_';
        else
            if ( 1 < $wpdb->blogid )
                $tb_prefix = $wpdb->base_prefix . $wpdb->blogid . '_';
            else
                $tb_prefix = $wpdb->base_prefix;

        $member = $wpdb->get_row( $wpdb->prepare( "SELECT member_id FROM {$tb_prefix}enewsletter_members WHERE wp_user_id = %d", $wp_user_id ), "ARRAY_A" );
        return $member['member_id'];
    }

    /**
     * Get all members
     **/
    function get_members( $arg = "") {
        global $wpdb;

        $where      = "";
        $orderby    = "";
        $limit      = "";

        if ( isset( $arg['where'] ) ) {
            $where = "WHERE ". $arg['where'];
        }

        if ( isset( $arg['limit'] ) ) {
            $limit = $arg['limit'];
        }

        if ( isset( $arg['orderby'] ) ) {
            $orderby = "ORDER BY ". $arg['orderby'];
            if ( $arg['order'] )
                $orderby .= " ". $arg['order'];
        }

        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->tb_prefix}enewsletter_members ". $where . " ".  $orderby . " " . $limit ), "ARRAY_A" );

        if ( $results )
                foreach( $results as $member ) {
                    $member['count_sent']   = $this->get_count_sent_to_user( $member['member_id'] );
                    $member['count_opened'] = $this->get_count_opened_by_user( $member['member_id'] );
                    $members[] = $member;
                }

        if ( isset( $arg['sortby'] ) )
            $members = $this->sort_array_by_field( $members, $arg['sortby'], $arg['order'] );

        return $members;
    }


    /**
     * Get all members of Group
     **/
    function get_members_of_group( $group_id, $limit = '' ) {
        global $wpdb;
        $members = NULL;
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT member_id FROM {$this->tb_prefix}enewsletter_member_group WHERE group_id = %d" . $limit , $group_id ), "ARRAY_A" );
        foreach( $results as $member ){
            $members[] = $member['member_id'];
        }
        return $members;
    }

    /**
     * Get count members of Group
     **/
    function get_count_members_of_group( $group_id ) {
        global $wpdb;
        $results = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(member_id) FROM {$this->tb_prefix}enewsletter_member_group WHERE group_id = %d", $group_id ) );
        return $results;
    }

    /**
     * Get unsubscribe members
     **/
    function get_unsubscribe_member( $limit = '' ) {
        global $wpdb;
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->tb_prefix}enewsletter_members WHERE unsubscribe_code = '' OR unsubscribe_code IS NULL" . $limit ), "ARRAY_A" );
        return $results;
    }

    /**
     * Get count unsubscribe members
     **/
    function get_count_unsubscribe_members() {
        global $wpdb;
        $results = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(member_id) FROM {$this->tb_prefix}enewsletter_members WHERE unsubscribe_code = '' OR unsubscribe_code IS NULL" ) );
        return $results;
    }

    /**
     * Create/Edit new Group
     **/
    function create_group( $group_name, $public, $group_id = "0" ) {
        global $wpdb;

        //checking that group not exist other ID
        $result = $wpdb->get_row( $wpdb->prepare( "SELECT group_id FROM {$this->tb_prefix}enewsletter_groups WHERE LOWER(group_name) = '%s'",  strtolower( $group_name ) ), "ARRAY_A");
        if ( $result ) {
            if ( "0" != $group_id && $result['group_id'] == $group_id ) {

            } else {
                //if group exist with other ID
                wp_redirect( add_query_arg( array( 'page' => 'newsletters-groups', 'updated' => 'true', 'dmsg' => urlencode( __( 'The Group already exists!!!', 'email-newsletter' ) ) ), 'admin.php' ) );
                exit;
            }
        }


        if ( "0" != $group_id ) {
            //update when edit group
            $result = $wpdb->query( $wpdb->prepare( "UPDATE {$this->tb_prefix}enewsletter_groups SET group_name = '%s', public = '%s' WHERE group_id = %d", trim( $group_name ), $public, $group_id ) );
            wp_redirect( add_query_arg( array( 'page' => 'newsletters-groups', 'updated' => 'true', 'dmsg' => urlencode( __( 'The changes of the group are saved!', 'email-newsletter' ) ) ), 'admin.php' ) );
            exit;
        } else {
            //create new group
            $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$this->tb_prefix}enewsletter_groups SET group_name = '%s', public = '%s'", trim( $group_name), $public ) );
            wp_redirect( add_query_arg( array( 'page' => 'newsletters-groups', 'updated' => 'true', 'dmsg' => urlencode( __( 'Group is created!', 'email-newsletter' ) ) ), 'admin.php' ) );
            exit;
        }
    }

    /**
     * Get all data of all groups
     **/
     function get_groups() {
        global $wpdb;
        $groups = $wpdb->get_results( "SELECT * FROM {$this->tb_prefix}enewsletter_groups", "ARRAY_A");
        return $groups;
    }

    /**
     * Get all data of one group
     **/
     function get_group_by_id( $group_id ) {
        global $wpdb;
        $result =  $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tb_prefix}enewsletter_groups WHERE group_id = %d", $group_id ), "ARRAY_A" );
        return $result;
    }

    /**
     * Delete Group
     **/
    function delete_group( $group_id ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->tb_prefix}enewsletter_groups WHERE group_id = %d", $group_id ) );
        wp_redirect( add_query_arg( array( 'page' => 'newsletters-groups', 'updated' => 'true', 'dmsg' => urlencode( __( 'Group is deleted!', 'email-newsletter' ) ) ), 'admin.php' ) );
        exit;
    }

    /**
     * Change Group
     **/
    function change_group( $member_id, $groups_id ) {
        global $wpdb;

        //deleting old list of groups for user
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->tb_prefix}enewsletter_member_group WHERE member_id = %d", $member_id ) );

        $member_data = $this->get_member( $member_id );
        if ( "" == $member_data['unsubscribe_code'] ) {
            $this->subscribe( $member_id, "false" );
        }

        //creating new list of groups for user
        if ( $groups_id )
            foreach( ( array ) $groups_id as $group_id )
                $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$this->tb_prefix}enewsletter_member_group SET member_id = %d, group_id =  %d", $member_id, $group_id ) );

        wp_redirect( add_query_arg( array( 'page' => 'newsletters-members', 'updated' => 'true', 'dmsg' => urlencode( __( 'Groups are changed!', 'email-newsletter' ) ) ), 'admin.php' ) );
        exit;
    }

    /**
     * Bulk option -  add member to group
     **/
    function add_members_group( $members_id, $group_id ) {
        global $wpdb;

        if ( 0 < $group_id ) {
            foreach( $members_id as $member_id ) {
                $result = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$this->tb_prefix}enewsletter_member_group WHERE member_id = %d AND group_id = %d", $member_id, $group_id ) );

                if ( ! $result )
                    $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$this->tb_prefix}enewsletter_member_group SET member_id = %d, group_id =  %d", $member_id, $group_id ) );
            }
            wp_redirect( add_query_arg( array( 'page' => 'newsletters-members', 'updated' => 'true', 'dmsg' => urlencode( __( 'Members are added to the group!', 'email-newsletter' ) ) ), 'admin.php' ) );
            exit;
        }
    }

    /**
     * Bulk option -  delete member from group
     **/
    function delete_members_group( $members_id, $group_id ) {
        global $wpdb;

        if ( 0 < $group_id ) {
            foreach( $members_id as $member_id )
                $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->tb_prefix}enewsletter_member_group WHERE member_id = %d AND group_id = %d", $member_id, $group_id ) );

            wp_redirect( add_query_arg( array( 'page' => 'newsletters-members', 'updated' => 'true', 'dmsg' => urlencode( __( 'Members are deleted from the group!', 'email-newsletter' ) ) ), 'admin.php' ) );
            exit;
        }
    }

    /**
     * Get all groups for memeber
     **/
     function get_memeber_groups( $member_id ) {
        global $wpdb;
        $groups = NULL;
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT group_id FROM {$this->tb_prefix}enewsletter_member_group WHERE member_id = %d", $member_id ), "ARRAY_A");
        foreach( $results as $group ){
            $groups[] = $group['group_id'];
        }
        return $groups;
    }

    /**
     * change plugin's icon
     **/
    function change_icon( ) {

       ?>
        <style type="text/css">
            #toplevel_page_newsletters-dashboard .wp-menu-image a img,
            #toplevel_page_newsletters-settings .wp-menu-image a img {
                display: none;
            }

            #toplevel_page_newsletters-dashboard div.wp-menu-image,
            #toplevel_page_newsletters-settings div.wp-menu-image {
                background: url("<?php echo $this->plugin_url; ?>email-newsletter-files/images/icon.png") no-repeat scroll 0px 0px transparent;
            }

            #toplevel_page_newsletters-dashboard:hover div.wp-menu-image,
            #toplevel_page_newsletters-settings:hover div.wp-menu-image {
                background: url("<?php echo $this->plugin_url; ?>email-newsletter-files/images/icon.png") no-repeat scroll 0px -32px transparent;
            }

            #toplevel_page_newsletters-dashboard.wp-has-current-submenu div.wp-menu-image,
            #toplevel_page_newsletters-settings.wp-has-current-submenu div.wp-menu-image {
                background: url("<?php echo $this->plugin_url; ?>email-newsletter-files/images/icon.png") no-repeat scroll 0px -32px transparent;
            }
        </style>
    <?php
    }


    /**
     * tinymce includes
     **/
    function tinymce_includes( ) {

        // Including files
        if ( isset( $_REQUEST['page'] ) && 'newsletters-create' == $_REQUEST['page'] ) {
            wp_admin_css( 'thickbox' );
            wp_print_scripts( 'jquery-ui-core' );
            wp_print_scripts( 'jquery-ui-tabs' );
            wp_print_scripts( 'post' );
            wp_print_scripts( 'editor' );
            add_thickbox();
            wp_print_scripts( 'media-upload' );
            //if ( function_exists( 'wp_editor' ) ) wp_editor();
        }
    }

    /**
     * import members
     **/
    function import_members() {
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'] . '/';

        // .csv full file name
        $file = $upload_dir . $_FILES['import_members_file']['name'];

        if ( is_writable( $upload_dir ) ) {
            if ( move_uploaded_file( $_FILES['import_members_file']['tmp_name'], $file ) ) {
                $f = fopen( $file, 'rt' ) or wp_die( 'error!' );

                //Set Separation sign
                if ( isset( $_REQUEST['separ_sign'] ) &&  1 == $_REQUEST['separ_sign'] )
                    $separ_sign = ';';
                elseif ( isset( $_REQUEST['separ_sign'] ) &&  2 == $_REQUEST['separ_sign'] )
                    $separ_sign = ',';
                else
                    $separ_sign = ';';

                // read file and write all to array
                for ( $i = 0; $data = fgetcsv( $f, 1000, $separ_sign ); $i++ ) {
                    $num = count( $data );

                    for ( $c = 0; $c < $num; $c++ )
                        $a[$c] = $data[$c];

                    $import_data[] = $a;
                }
                fclose( $f );
                unlink( $file );

                //write data to member table
                if ( is_array( $import_data ) ) {
                    global $wpdb;
                    $i = 0;
                    foreach( $import_data as $data ) {
                        $unsubscribe_code = $this->gen_unsubscribe_code();
                        $email = $data[0];
                        $fname = ( isset( $data[1] ) ) ? $data[1] : '';
                        $lname = ( isset( $data[2] ) ) ? $data[2] : '';

                        if ( isset( $_REQUEST['import_groups_id'] ) && is_array( $_REQUEST['import_groups_id'] ) )
                            $import_groups_id = $_REQUEST['import_groups_id'];

                        $result = $wpdb->get_var( $wpdb->prepare( "SELECT member_id FROM {$this->tb_prefix}enewsletter_members WHERE member_email = %s", $email ) );

                        if ( 0 < $result ) {
                            //email of member already exist
                            $exist_members[] = $email;
                        } else {
                            //create new member
                            $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$this->tb_prefix}enewsletter_members SET
                                    wp_user_id = 0,
                                    member_email = %s,
                                    member_fname = %s,
                                    member_lname = %s,
                                    join_date = %d,
                                    unsubscribe_code = '%s'
                                 ", $email, $fname, $lname, time(), $unsubscribe_code ) );

                            $member_id = $wpdb->insert_id;

                            //creating new list of groups for user
                            if ( isset( $import_groups_id ) )
                                foreach( $import_groups_id as $group_id )
                                    $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$this->tb_prefix}enewsletter_member_group SET member_id = %d, group_id =  %d", $member_id, $group_id ) );

                            $i++;
                        }
                    }
                }

                $dmsg = '';

                if ( 0 < $i )
                    $dmsg .=  __( 'Import is finished successfully,', 'email-newsletter' ) . ' ' . $i . ' ' . __( 'members are added.', 'email-newsletter' );

                if ( isset( $exist_members ) && is_array( $exist_members ) ) {
                    $dmsg .= '<br />' . __( 'These emails already exist in member list:', 'email-newsletter' ) . '<br />';
                    foreach($exist_members as $exist_member )
                        $dmsg .= $exist_member . '<br />';
                }

                wp_redirect( add_query_arg( array( 'page' => 'newsletters-members', 'updated' => 'true', 'dmsg' => urlencode( $dmsg ) ), 'admin.php' ) );
                exit;

            } else {
                wp_redirect( add_query_arg( array( 'page' => 'newsletters-members', 'updated' => 'true', 'dmsg' => urlencode( __( 'Import ERROR: Problem with uploading of the file!', 'email-newsletter' ) ) ), 'admin.php' ) );
                exit;
            }
        } else {
            wp_redirect( add_query_arg( array( 'page' => 'newsletters-members', 'updated' => 'true', 'dmsg' => urlencode( __( 'Import ERROR: Please change permission for the folder /wp-contant/uploads/', 'email-newsletter' ) ) ), 'admin.php' ) );
            exit;
        }
    }


    /**
     * Get pagination data
     **/
    function get_pagination_data( $count, $per_page ) {
            if ( 'all' == $per_page )
                $per_page = 1000000;

            if ( $count > $per_page ) {
                $pagination_data['count'] = $count;

                if ( isset( $_REQUEST['cpage'] ) && 0 < $_REQUEST['cpage'] )
                    $pagination_data['cpage'] = $_REQUEST['cpage'];
                else
                    $pagination_data['cpage'] = 1;

                $pagination_data['cpage_str'] = '&cpage=' . $pagination_data['cpage'];
                $start = ( $pagination_data['cpage'] - 1 ) * $per_page;
                $pagination_data['limit'] = ' LIMIT ' . $start . ',' . $per_page;

                return $pagination_data;
            }

        return NULL;
    }



    /**
     * Install of plugin - creating tables in DB
     **/
    function install( $blog_id = '' ) {
        global $wpdb;

        if ( function_exists( 'is_multisite' ) && is_multisite() && 0 !== $blog_id && isset( $_GET['networkwide'] ) && $_GET['networkwide'] == 1 ) {
                $blogids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs" ) );
        } else {
            if ( 0 !== $blog_id )
                $blogids[] = $wpdb->blogid;
            else
                $blogids[] = $blog_id;
        }

        foreach ( $blogids as $blog_id ) {
            //Checking DB prefix
            if ( 1 < $blog_id )
                $tb_prefix = $wpdb->base_prefix . $blog_id . '_';
            else
                $tb_prefix = $wpdb->base_prefix;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_newsletters'" ) != "{$tb_prefix}enewsletter_newsletters" ) {

                $enewsletter_table = "CREATE TABLE `{$tb_prefix}enewsletter_newsletters` (
                    `newsletter_id` int(11) NOT NULL auto_increment,
                    `create_date` int(11) NOT NULL,
                    `template` varchar(100) NOT NULL,
                    `subject` varchar(255) NOT NULL,
                    `from_name` varchar(255) NOT NULL,
                    `from_email` varchar(255) NOT NULL,
                    `content` text NOT NULL,
                    `contact_info` varchar(255) NOT NULL,
                    `bounce_email` varchar(255) NOT NULL,
                    PRIMARY KEY (`newsletter_id`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

                $result = $wpdb->query( $enewsletter_table );
            }

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_send'" ) != "{$tb_prefix}enewsletter_send" ) {

                $enewsletter_table = "CREATE TABLE `{$tb_prefix}enewsletter_send` (
                    `send_id` int(11) NOT NULL auto_increment,
                    `newsletter_id` int(11) NOT NULL,
                    `start_time` int(11) DEFAULT '0',
                    `end_time` int(11) DEFAULT '0',
                    `email_body` text,
                    PRIMARY KEY (`send_id`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

                $result = $wpdb->query( $enewsletter_table );
            }

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_send_members'" ) != "{$tb_prefix}enewsletter_send_members" ) {

                $enewsletter_table = "CREATE TABLE `{$tb_prefix}enewsletter_send_members` (
                    `send_id` int(11) NOT NULL,
                    `member_id` int(11) NOT NULL,
                    `status` varchar(15),
                    `opened_time` int(11) DEFAULT '0',
                    `bounce_time` int(11) DEFAULT '0',
                    `sent_time` int(11)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

                $result = $wpdb->query( $enewsletter_table );
            }

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_groups'" ) != "{$tb_prefix}enewsletter_groups" ) {

                $enewsletter_table = "CREATE TABLE `{$tb_prefix}enewsletter_groups` (
                    `group_id` int(11) NOT NULL auto_increment,
                    `group_name` varchar(255) NOT NULL,
                    `public` varchar(1) NOT NULL,
                    PRIMARY KEY (`group_id`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

                $result = $wpdb->query( $enewsletter_table );
            }

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_member_group'" ) != "{$tb_prefix}enewsletter_member_group" ) {

                $enewsletter_table = "CREATE TABLE `{$tb_prefix}enewsletter_member_group` (
                    `member_id` int(11) NOT NULL,
                    `group_id` int(11) NOT NULL
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

                $result = $wpdb->query( $enewsletter_table );
            }

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_members'" ) != "{$tb_prefix}enewsletter_members" ) {

                $enewsletter_table = "CREATE TABLE `{$tb_prefix}enewsletter_members` (
                    `member_id` int(11) NOT NULL auto_increment,
                    `wp_user_id` int(11) DEFAULT '0',
                    `member_fname` varchar(255),
                    `member_lname` varchar(255),
                    `member_email` varchar(255) NOT NULL,
                    `join_date` int(11) NOT NULL,
                    `member_info` text,
                    `unsubscribe_code` varchar(20),
                    PRIMARY KEY (`member_id`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
                $result = $wpdb->query( $enewsletter_table );

                //Sync exist wp users
                $arg = array (
                    'blog_id' => $blog_id
                );
                $users = get_users( $arg );
                if ( $users )
                    foreach( $users as $user ) {
                        $unsubscribe_code = $this->gen_unsubscribe_code();
                        $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$tb_prefix}enewsletter_members SET
                            wp_user_id = %d,
                            member_fname = %s,
                            member_email = %s,
                            join_date = %d,
                            unsubscribe_code = '%s'
                         ", $user->ID, $user->user_nicename, $user->user_email, time(), $unsubscribe_code ) );
                    }

            }

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_settings'" ) != "{$tb_prefix}enewsletter_settings" ) {

                $enewsletter_table = "CREATE TABLE `{$tb_prefix}enewsletter_settings` (
                    `key` varchar(255) NOT NULL,
                    `value` varchar(255) NOT NULL,
                    PRIMARY KEY (`key`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

                $result = $wpdb->query( $enewsletter_table );
            }

        }
    }


    /**
     * Deleting tables from DB
     **/
    function uninstall( $blog_id = '' ) {
        global $wpdb;

        if ( function_exists('is_multisite' ) && is_multisite() && 0 !== $blog_id  && $_GET['networkwide'] == 1 ) {
                $blogids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs" ) );
        } else {
            if ( 0 !== $blog_id )
                $blogids[] = $wpdb->blogid;
            else
                $blogids[] = $blog_id;
        }

        foreach ( $blogids as $blog_id ) {
            //Checking DB prefix
            if ( 1 < $blog_id )
                $tb_prefix = $wpdb->base_prefix . $blog_id . '_';
            else
                $tb_prefix = $wpdb->base_prefix;

            //Delete all CRON actions
            if ( wp_next_scheduled( $this->cron_send_name ) )
                wp_clear_scheduled_hook( $this->cron_send_name );

            if ( wp_next_scheduled( 'e_newsletter_cron_check_bounces_1' . $wpdb->blogid ) )
                wp_clear_scheduled_hook( 'e_newsletter_cron_check_bounces_1' . $wpdb->blogid );

            if ( wp_next_scheduled( 'e_newsletter_cron_check_bounces_2' . $wpdb->blogid ) )
                wp_clear_scheduled_hook( 'e_newsletter_cron_check_bounces_2' . $wpdb->blogid );

            delete_option( 'enewsletter_cron_send_run' );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_newsletters'" ) == "{$tb_prefix}enewsletter_newsletters" )
                $wpdb->query("DROP TABLE IF EXISTS {$tb_prefix}enewsletter_newsletters");

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_send'" ) == "{$tb_prefix}enewsletter_send" )
                $wpdb->query( "DROP TABLE IF EXISTS {$tb_prefix}enewsletter_send" );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_send_members'" ) == "{$tb_prefix}enewsletter_send_members" )
                $wpdb->query( "DROP TABLE IF EXISTS {$tb_prefix}enewsletter_send_members" );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_groups'" ) == "{$tb_prefix}enewsletter_groups" )
                $wpdb->query( "DROP TABLE IF EXISTS {$tb_prefix}enewsletter_groups" );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_member_group'" ) == "{$tb_prefix}enewsletter_member_group" )
                $wpdb->query( "DROP TABLE IF EXISTS {$tb_prefix}enewsletter_member_group" );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_members'" ) == "{$tb_prefix}enewsletter_members" )
                $wpdb->query( "DROP TABLE IF EXISTS {$tb_prefix}enewsletter_members" );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$tb_prefix}enewsletter_settings'" ) == "{$tb_prefix}enewsletter_settings" )
                $wpdb->query( "DROP TABLE IF EXISTS {$tb_prefix}enewsletter_settings" );

        }

    }

    /**
     * Write log for CRON
     **/
    function write_log( $message ) {
        $file = $this->plugin_dir . "email-newsletter-files/cron_log.log";

        $handle = fopen( $file, 'ab' );
        $data = date( "[Y-m-d H:i:s]" ) . $message . "\r\n";
        fwrite($handle, $data);
        fclose($handle);
    }

    /**
     * Encrypt text (SMTP password)
     **/
    protected function _encrypt( $text ) {
        if  ( function_exists( 'mcrypt_encrypt' ) ) {
            return base64_encode( @mcrypt_encrypt( MCRYPT_RIJNDAEL_256, DB_PASSWORD, $text, MCRYPT_MODE_ECB ) );
        } else {
            return $text;
        }
    }

    /**
     * Decrypt password (SMTP password)
     **/
    protected function _decrypt( $text ) {
        if ( function_exists( 'mcrypt_decrypt' ) ) {
            return trim( @mcrypt_decrypt( MCRYPT_RIJNDAEL_256, DB_PASSWORD, base64_decode( $text ), MCRYPT_MODE_ECB ) );
        } else {
            return $text;
        }
    }


}
?>
