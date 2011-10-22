<?php
/*
Plugin Name: CD BuddyPress Avatar Bubble
Plugin URI: http://cosydale.com/plugin-cd-avatar-bubble.html
Description: After moving your mouse pointer on a BuddyPress user avatar you will see a bubble with the defined by admin information about this user.
Version: 2.1.1
Author: slaFFik
Author URI: http://cosydale.com/
Site Wide Only: true
*/
define ('CD_AB_VERSION', '2.1.1');
define ('CD_AB_IMAGE_URI', WP_PLUGIN_URL . '/cd-bp-avatar-bubble/_inc/images');

register_activation_hook( __FILE__, 'cd_ab_activation');
register_deactivation_hook( __FILE__, 'cd_ab_deactivation');
function cd_ab_activation() {
    $cd_ab['color'] = 'blue';
    $cd_ab['borders'] = 'images';
    
    $cd_ab['access'] = 'all';
    
    $cd_ab['messages'] = 'yes';
    $cd_ab['friend'] = 'no';
    
    $cd_ab['action'] = 'click';
    $cd_ab['delay'] = '0';
    
    $cd_ab['groups']['status'] = 'off';
    $cd_ab['groups']['join'] = 'off';
    $cd_ab['groups']['type'] = array('public');
    $cd_ab['groups']['data'] = array('name', 'short_desc', 'members', 'forum_stat');
    
    add_option('cd_ab', $cd_ab, '', 'yes');
}
function cd_ab_deactivation() { delete_option('cd_ab'); }

/* LOAD LANGUAGES */
function cd_ab_load_textdomain() {
    $locale = apply_filters('buddypress_locale', get_locale() );
    $mofile = dirname( __File__ )   . "/langs/$locale.mo";

    if ( file_exists( $mofile ) )
        load_textdomain('cd_ab', $mofile );
}
add_action ('plugins_loaded', 'cd_ab_load_textdomain', 7 );

require ( WP_PLUGIN_DIR . '/cd-bp-avatar-bubble/cd-ab-admin.php');
require ( WP_PLUGIN_DIR . '/cd-bp-avatar-bubble/cd-ab-cssjs.php');

/***
* BUBBLE ENGINE 
***/
add_filter('bp_core_fetch_avatar', 'cd_ab_rel_filter', 10, 2 );
function cd_ab_rel_filter( $text, $params ) {
    $cd_ab = get_option('cd_ab');
    
    if ( $params['object'] == 'user') {
        return preg_replace('~<img (.+?) />~i', "<img $1 rel='user_{$params['item_id']}' />", $text );
    }elseif( $params['object'] == 'group') {
        if($cd_ab['groups']['status'] == 'on'){
            return preg_replace('~<img (.+?) />~i', "<img $1 rel='group_{$params['item_id']}' />", $text );
        }else{
            return $text;
        }
    }else{
        return $text;
    }
}

add_filter( 'bp_get_activity_action', 'cd_ab_rel_activity_filter', 99, 2 );
function cd_ab_rel_activity_filter($action, $activity){
    switch ( $activity->component ) {
        case 'groups' :
            $cd_ab = get_option('cd_ab');
            if($cd_ab['groups']['status'] == 'on') {
                $reverse_content = strrev( $action );
                $position = strpos( $reverse_content, 'gmi<' );
                preg_match('~group-(\d++)-avatar~', $action, $match);
                $replace = "rel='group_{$match[1][0]}' ";
                $action = substr_replace( $action, $replace, -$position + 1, 0 );
            }
            break;
    }
    return $action;
}

function cd_ab_get_add_friend_button( $ID = false, $friend_status = false ) {
    global $bp, $friends_template;

    if ( !is_user_logged_in() )
        return false;

    if ( !$ID && $friends_template->friendship->friend )
        $ID = $friends_template->friendship->friend->id;
    else if ( !$ID && !$friends_template->friendship->friend )
        $ID = $bp->displayed_user->id;

    if ( $bp->loggedin_user->id == $ID )
        return false;

    if ( empty( $friend_status ) )
        $friend_status = friends_check_friendship_status( $bp->loggedin_user->id, $ID );

    if ('pending' == $friend_status ) {
        $button .= '<a class="requested" href="' . $bp->loggedin_user->domain . $bp->friends->slug . '/">' . __('Friendship Requested', 'buddypress') . '</a>';
    } else if ('is_friend' == $friend_status ) {
        $button .= '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->friends->slug . '/remove-friend/' . $ID . '/', 'friends_remove_friend') . '" title="' . __('Cancel Friendship', 'buddypress') . '" id="friend-' . $ID . '" rel="remove" class="remove">' . __('Cancel Friendship', 'buddypress') . '</a>';
    } else {
        $button .= '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->friends->slug . '/add-friend/' . $ID . '/', 'friends_add_friend') . '" title="' . __('Add Friend', 'buddypress') . '" id="friend-' . $ID . '" rel="add" class="add">' . __('Add Friend', 'buddypress') . '</a>';
    }

    return apply_filters('cd_ab_get_add_friend_button', $button );
}

/* DISPLAY EVERYTHING */
add_action('wp_ajax_cd_ab_the_avatardata', 'cd_ab_the_avatardata');
add_action('wp_ajax_nopriv_cd_ab_the_avatardata', 'cd_ab_the_avatardata');
function cd_ab_the_avatardata(){
    $cd_ab = get_option('cd_ab');
    $ID = $_GET['ID'];
    $type = $_GET['type'];
    
    if($type == 'user'){
        if ( $cd_ab['access'] == 'admin' && is_super_admin() ) {
            cd_ab_get_the_userdata( $ID, $cd_ab );
        }elseif ( $cd_ab['access'] == 'logged_in' && is_user_logged_in() ) {
            cd_ab_get_the_userdata( $ID, $cd_ab );
        }elseif ( $cd_ab['access'] == 'all') {
            cd_ab_get_the_userdata( $ID, $cd_ab );
        }else{
            echo $cd_ab['delay'].'|~|<div id="user_'.$ID.'">'.__('You don\'t have enough rights to view user data','cd_ab').'</div>';
        }
    }elseif($type == 'group'){
        if ( $cd_ab['access'] == 'admin' && is_super_admin() ) {
            cd_ab_get_the_group_data( $ID, $cd_ab );
        }elseif ( $cd_ab['access'] == 'logged_in' && is_user_logged_in() ) {
            cd_ab_get_the_group_data( $ID, $cd_ab );
        }elseif ( $cd_ab['access'] == 'all') {
            cd_ab_get_the_group_data( $ID, $cd_ab );
        }else{
            echo $cd_ab['delay'].'|~|<div id="group_'.$ID.'">'.__('You don\'t have enough rights to view group data','cd_ab').'</div>';
        }
    }
    die;
}

// For groups
function cd_ab_get_the_group_data($ID, $cd_ab){
    global $bp;
    echo $cd_ab['delay'].'|~|<div id="group_'.$ID.'">';
        $group = groups_get_group( array( 'group_id' => $ID ) );
        if ( !in_array( $group->status, $cd_ab['groups']['type'] ) ) {
            echo __('You don\'t have enough rights to view data of this group','cd_ab').'</div>';
            die;
        }
            
        $group_link = $bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $group->slug;
        //print_var( $group );
        //print_var($cd_ab['groups']);
        
        // Group Name
        if( in_array('name', $cd_ab['groups']['data']) ){
            echo '<p class="popupLine" style="padding-top:0"><a href="'. $group_link .'">'.$group->name.'</a>';
            // Group Description (shortened)
            if( in_array('short_desc', $cd_ab['groups']['data']) )
                echo ' &rarr; '.bp_create_excerpt( $group->description, 10 );
            echo '</p>';
        }else{ // and description only
            if( in_array('short_desc', $cd_ab['groups']['data']) )
                echo '<p class="popupLine" style="padding-top:0"><a href="'. $group_link .'">#</a> ' . bp_create_excerpt( $group->description, 10 ) . '</p>';
        }
        
        echo '<p class="popupLine">';
            // Group Status display
            if( in_array('status', $cd_ab['groups']['data']) ) {
                if ( 'public' == $group->status ) {
                    $type = __( "Public Group", "buddypress" );
                } else if ( 'hidden' == $group->status ) {
                    $type = __( "Hidden Group", "buddypress" );
                } else if ( 'private' == $group->status ) {
                    $type = __( "Private Group", "buddypress" );
                } else {
                    $type = ucwords( $group->status ) . ' ' . __( 'Group', 'buddypress' );
                }
                echo $type;
                $type_used = true;
            }
            
            // Formatted number of group members
            if( in_array('members', $cd_ab['groups']['data']) ) {
                if ( 1 == $group->total_member_count )
                    $members_data = apply_filters( 'bp_get_group_member_count', sprintf( __( '%s member', 'buddypress' ), bp_core_number_format( $group->total_member_count ) ) );
                else
                    $members_data = apply_filters( 'bp_get_group_member_count', sprintf( __( '%s members', 'buddypress' ), bp_core_number_format( $group->total_member_count ) ) );
                    if ($type_used) 
                        echo '<span style="float:right">' . $members_data . '</span>';
                    else 
                        echo $members_data;
            }
         echo '</p>';
        
        if( in_array('join', $cd_ab['groups']['data']) ) {
            $button = bp_get_group_join_button($group);
            if(!empty($button)){
                echo $button;
            }
        }
        
        // Display activity date
        if( in_array('activity_date', $cd_ab['groups']['data']) ) {
            $activity_data = sprintf( __('Active %s ago', 'cd_ab'), bp_core_time_since( $group->last_activity ) );
            echo '<p class="popupLine">' . $activity_data; 
            if( in_array('feed_link', $cd_ab['groups']['data']) )
                echo ' (<a href="'. $group_link .'/feed" target="_blank">'.__( 'RSS', 'buddypress' ).'</a>)';
            echo '</p>';
        }

        // display the forum stat text
        if ( function_exists('bp_forums_is_installed_correctly') ) {
            if ( bp_forums_is_installed_correctly() ) {
                if( in_array('forum_stat', $cd_ab['groups']['data']) ){
                    // get all required data for count
                    $forum_id = groups_get_groupmeta( $ID, 'forum_id' );
                    $forum_counts = bp_forums_get_forum_topicpost_count( (int)$forum_id );
                    if ( 1 == (int) $forum_counts[0]->topics ) {
                        $total_topics = sprintf( __( '%d topic', 'buddypress' ), (int) $forum_counts[0]->topics );
                    }else{
                        $total_topics = sprintf( __( '%d topics', 'buddypress' ), (int) $forum_counts[0]->topics );
                    }
                    if ( 1 == (int) $forum_counts[0]->posts ) {
                        $total_posts = sprintf( __( '%d post', 'buddypress' ), (int) $forum_counts[0]->posts );
                    }else{
                        $total_posts = sprintf( __( '%d posts', 'buddypress' ), (int) $forum_counts[0]->posts );
                    }
                    // echo the text
                    echo '<p class="popupLine">'.sprintf(__('<strong>Forum</strong>: %s and %s', 'cd_ab'), $total_topics, $total_posts).'</p>';
                }
            }
        }
        
    echo '<div style="clear:both"></div></div>';
}

// For users
function cd_ab_get_the_userdata($ID, $cd_ab) {
    global $bp;
    
    if ( !$cd_ab['delay'] ) {
        echo '0|~|';
    }else{
        echo $cd_ab['delay'] . '|~|';
    }
    $i = 1;
    $action = 'false';
    do_action('cd_ab_before_default');
    if ( $cd_ab['messages'] == 'yes') {
        $i++;

        if ( $cd_ab['action'] == 'click') {
            $action = 'true';
            $mention = '<strong><a href="'. bp_core_get_user_domain( $ID, false, false ) .'" title="'. __('Go to profile page',  'cd_ab') .'">#</a> | </strong>';
        }
    
        if ( is_user_logged_in() ) {
            $mention .= '<strong><a href="'. bp_core_get_user_domain( $bp->loggedin_user->id, false, false ) . BP_ACTIVITY_SLUG .'/?r='.bp_core_get_username( $ID, false, false ).'" title="'. __('Mention this user', 'cd_ab') .'">@'. bp_core_get_username( $ID, false, false ) .'</a></strong>';
            $message = '<a href="'. bp_core_get_user_domain( $bp->loggedin_user->id, false, false ) . BP_MESSAGES_SLUG . '/compose/?r=' . bp_core_get_username( $ID, false, false ) .'" title="'. __('Send a private message to this user', 'cd_ab') .'">'. __('Private Message', 'cd_ab') .'</a>';
        }else{
            $mention .= '<strong><a href="' . $bp->root_domain . '/wp-login.php?redirect_to=' . urlencode( $bp->root_domain ) . '" title="'.__('You should be logged in to mention this user', 'cd_ab') .'">@'. bp_core_get_username( $ID, false, false ) .'</a></strong>';
            $message = '<strong><a href="' . $bp->root_domain . '/wp-login.php?redirect_to=' . urlencode( $bp->root_domain ) . '" title="'. __('You should be logged in to send a private message', 'cd_ab') .'">'. __('Private Message', 'cd_ab') .'</a></strong>';
        }
        $output .= '<p class="popupLine" style="padding-top:0px">'. $mention .' | '. $message .'</p>';
    }

    if ( $cd_ab['friend'] == 'yes' && $ID != $bp->loggedin_user->id && is_user_logged_in() ) {
        $i++;
        if ( $i != 1 ) $class = ' style="padding-top:6px;"';
        if ( $cd_ab['action'] == 'click' && $action == 'false')
            $link = '<strong><a href="'. bp_core_get_user_domain( $ID, false, false ) .'" title="'. __('Go to profile page',  'cd_ab') .'">#</a> | </strong>';
        $output .= '<p class="popupLine"'. $class .'>'. $link . cd_ab_get_add_friend_button( $ID, false) .'</p>';
    }
    do_action('cd_ab_before_fields');
    foreach ( $cd_ab as $field_id => $field_data ) {
        if ( $field_data['name'] && is_numeric( $field_id ) ) {
            $field_value = xprofile_get_field_data( $field_id, $ID );
            if ( $field_value != null ) {
                if ( $field_data['type'] == 'multiselectbox' || $field_data['type'] == 'checkbox') $field_value = bp_unserialize_profile_field ( $field_value );
                if ( $field_data['type'] == 'datebox' && $field_value != null ) $field_value = bp_format_time( bp_unserialize_profile_field ( $field_value), true );
                if ( $i != 1 ) $class = ' style="padding-top:6px;"';
                
                if ( $field_data['link'] == 'yes') {
                    //print_var($field_value);
                    if (is_array($field_value)) $field_value = implode(',', $field_value);
                    $field_link = xprofile_filter_link_profile_data( $field_value, $field_data['type'] );
                    $field_link = apply_filters('cd_ab_field_link', $field_link, $ID, $field_id, $field_data['type'], $field_value );
                }else{
                    $field_link = $field_value;
                    $field_link = apply_filters('cd_ab_field_text', $field_link, $ID, $field_id, $field_data['type'], $field_value );
                }
                $output .= '<p class="popupLine"'. $class .'><strong>' . $field_data['name'] . '</strong>: ' . $field_link . '</p>';
            }
            $i++;
        }
    }
    $output = apply_filters('cd_ab_output', $output );
    do_action('cd_ab_after_default');
    if ( $output == '')
        $output = __('Nothing to display. Check a bit later please.', 'cd_ab');
    
    echo "<div id='user_$ID'>$output<div style='clear:both'></div></div>";
}

// for debug
if(!function_exists('print_var')) {
    function print_var($var){
        echo '<pre>';
        if ( !empty($var))
            print_r($var);
        else
            var_dump($var);
        echo '</pre>';
    }
}
