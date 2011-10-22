<?php
//avoid direct calls to this file where wp core files not present
if ( !function_exists('add_action') ) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$new_cd_ab_admin = new CD_AB_ADMIN_PAGE();

class CD_AB_ADMIN_PAGE {

    //constructor of class, PHP4 compatible construction for backward compatibility (until WP 3.1)
    function cd_ab_admin_page() {
        add_filter('screen_layout_columns', array( &$this, 'on_screen_layout_columns'), 10, 2 );
        if (is_multisite()){
            add_action('network_admin_menu', array( &$this, 'on_admin_menu') );
        }else{
            add_action('admin_menu', array( &$this, 'on_admin_menu') );
        }
    }
    
    function on_screen_layout_columns( $columns, $screen ) {
        if ( $screen == $this->pagehook ) {
            if (is_multisite()){
                $columns[ $this->pagehook ] = 1;
            }else{
                $columns[ $this->pagehook ] = 2;
            }
        }
        return $columns;
    }
	
    function on_admin_menu() {
        $this->pagehook = add_submenu_page('bp-general-settings', __('CD Avatar Bubble', 'cd_ab'), __('CD Avatar Bubble', 'cd_ab'), 'manage_options', 'cd-ab-admin', array( &$this, 'on_show_page') );
        add_action('load-'.$this->pagehook, array( &$this, 'on_load_page') );
    }
	
    //will be executed if wordpress core detects this page has to be rendered
    function on_load_page() {
        wp_enqueue_script('common');
        wp_enqueue_script('wp-lists');
        wp_enqueue_script('postbox');

        if (is_multisite()){
            $position = 'normal';
            $priority = 'low';
        }else{
            $position = 'side';
            $priority = 'core';
        }

        // sidebar
        add_meta_box('cd-ab-admin-privacy', __('Privacy Options', 'cd_ab'), array(&$this, 'on_cd_ab_admin_privacy'), $this->pagehook, $position, $priority );
        add_meta_box('cd-ab-admin-b-color', __('Border Color', 'cd_ab'), array(&$this, 'on_cd_ab_admin_b_color'), $this->pagehook, $position, $priority);
        // main content - normal
        add_meta_box('cd-ab-admin-users', __('Users Avatars Options', 'cd_ab'), array( &$this, 'on_cd_ab_admin_users'), $this->pagehook, 'normal', 'core');
        add_meta_box('cd-ab-admin-groups', __('Groups Avatars Options', 'cd_ab'), array( &$this, 'on_cd_ab_admin_groups'), $this->pagehook, 'normal', 'core');
        add_meta_box('cd-ab-admin-extra', __('Extra Options', 'cd_ab'), array(&$this, 'on_cd_ab_admin_extra'), $this->pagehook, 'normal', 'core');
    }
    
    //executed to show the plugins complete admin page
    function on_show_page() {
        global $bp, $wpdb;
        global $screen_layout_columns;
        
        //define some data can be given to each metabox during rendering
        $cd_ab = get_option('cd_ab'); 
        if(!is_array($cd_ab['groups']['data']))
            $cd_ab['groups']['data'] = array();
        if(!is_array($cd_ab['groups']['type']))
            $cd_ab['groups']['type'] = array();
        ?>
        <div id="cd-ab-admin-general" class="wrap">
            <?php screen_icon('options-general'); ?>
            <style>table.link-group li{margin:0 0 0 25px}</style>
            <h2><?php _e('CD Avatar Bubble','cd_ab') ?> <sup><?php echo 'v' . CD_AB_VERSION; ?></sup> &rarr; <?php _e('Interactive Avatars', 'cd_ab') ?></h2>
        
            <?php 
            if ( isset($_POST['saveData']) ) {
                $cd_ab = $_POST['cd_ab_display'];
                $cd_ab['color'] = $_POST['cd_ab_color'];
                $cd_ab['borders'] = $_POST['cd_ab_borders'];
                
                $cd_ab['access'] = $_POST['cd_ab_access'];
                
                $cd_ab['messages'] = $_POST['cd_ab_messages'];
                $cd_ab['friend'] = $_POST['cd_ab_friend'];
                
                $cd_ab['action'] = $_POST['cd_ab_action'];
                
                $cd_ab['groups']['status'] = $_POST['cd_ab_groups'];
                $cd_ab['groups']['join'] = $_POST['cd_ab_groups_join'];
                $cd_ab['groups']['type'] = $_POST['cd_ab_groups_type'];
                $cd_ab['groups']['data'] = $_POST['cd_ab_groups_data'];
                
                if ( is_numeric( $_POST['cd_ab_delay'] ) ) {
                    $cd_ab['delay'] = $_POST['cd_ab_delay'];
                }else{
                    $cd_ab['delay'] = 0;
                }
                update_option('cd_ab', $cd_ab);

                echo "<div id='message' class='updated fade'><p>" . __('All changes were saved. Go and check results!', 'cd_ab') . "</p></div>";
            }
        
            if (is_multisite()){ ?>
                <form action="<?php echo site_url() . '/wp-admin/network/admin.php?page=cd-ab-admin' ?>" id="cd-ab-form" method="post">
            <?php }else{ ?>
                <form action="<?php echo site_url() . '/wp-admin/admin.php?page=cd-ab-admin' ?>" id="cd-ab-form" method="post">
            <?php }
                    wp_nonce_field('cd-ab-admin-general');
                    wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
                    wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
                
                    <div id="poststuff" class="metabox-holder<?php echo (2 == $screen_layout_columns) ? ' has-right-sidebar' : ''; ?>">
                        <div id="side-info-column" class="inner-sidebar">
                            <?php do_meta_boxes($this->pagehook, 'side', $cd_ab); ?>
                        </div>
                        <div id="post-body" class="has-sidebar">
                            <div id="post-body-content" class="has-sidebar-content">
                                <?php do_meta_boxes($this->pagehook, 'normal', $cd_ab); ?>
                                <p>
                                    <input type="submit" value="<?php _e('Save Selected Fields', 'cd_ab') ?>" class="button-primary" name="saveData"/>    
                                </p>
                            </div>
                        </div>
                    </div>  
                </form>
            </div>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function() {
                // close postboxes that should be closed
                jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                // postboxes setup
                postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
            });
            //]]>
        </script>
        
    <?php
    }

    function on_cd_ab_admin_groups($cd_ab) { 
        do_action( 'cd_ab_admin_groups_before', $cd_ab ); ?>
        <table class="widefat link-group">
            <tr>
                <td width="82%" style="vertical-align:middle;"><?php _e('Do you want to enable avatar bubble for groups too?', 'cd_ab') ?></td>
                <td>
                    <input name="cd_ab_groups" type="radio" value="on"<?php echo ('on' == $cd_ab['groups']['status'] ) ? ' checked="checked"' : ''; ?> /> <?php _e('Enable', 'cd_ab'); ?><br />
                    <input name="cd_ab_groups" type="radio" value="off"<?php echo ('off' == $cd_ab['groups']['status'] ) ? ' checked="checked"' : ''; ?> /> <?php _e('Disable', 'cd_ab'); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p><?php _e('For what types of groups this bubble should appear?','cd_ab') ?></p>
                    <ul>
                        <li><input type="checkbox" name="cd_ab_groups_type[]" <?php echo (in_array('public', $cd_ab['groups']['type'])?'checked="checked"':''); ?> value="public" /> <?php _e('For public groups', 'cd_ab') ?></span></li>
                        <li><input type="checkbox" name="cd_ab_groups_type[]" <?php echo (in_array('private', $cd_ab['groups']['type'])?'checked="checked"':''); ?> value="private" /> <?php _e('For private groups', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_type[]" <?php echo (in_array('hidden', $cd_ab['groups']['type'])?'checked="checked"':''); ?> value="hidden" /> <?php _e('For hidden groups', 'cd_ab') ?></li>
                    </ul>
                </td>
            <tr>
                <td colspan="2">
                    <p><?php _e('Please select below fields you want to show in a bubble:','cd_ab') ?></p>
                    <ul>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('name', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="name" /> <?php _e('Group name', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('short_desc', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="short_desc" /> <?php _e('Short description', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('status', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="status" /> <?php _e('Group status', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('members', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="members" /> <?php _e('Number of members', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('join', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="join" /> <?php _e('Join/Leave group links', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('activity_date', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="activity_date" /> <?php _e('Latest activity date', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('feed_link', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="feed_link" /> <?php _e('Feed link (displayed if latest activity is checked)', 'cd_ab') ?></li>
                        <li><input type="checkbox" name="cd_ab_groups_data[]" <?php echo (in_array('forum_stat', $cd_ab['groups']['data'])?'checked="checked"':''); ?> value="forum_stat" /> <?php _e('Forum statistics (number of topics and posts)', 'cd_ab') ?></li>
                    </ul>
                </td>
            </tr>
            <?php do_action( 'cd_ab_admin_groups_fields', $cd_ab ); ?>
        </table>
        <?php
        do_action( 'cd_ab_admin_groups_after', $cd_ab );
    }

    function on_cd_ab_admin_privacy($cd_ab) { ?>
        <p><?php _e('Whom would you like allow to see this avatar bubble?', 'cd_ab') ?></p>
        <p>
            <input name="cd_ab_access" type="radio" value="admin"<?php echo('admin' == $cd_ab['access'] ? ' checked="checked"' : ''); ?> /> <?php _e('Admin users only', 'cd_ab') ?><br />
            <input name="cd_ab_access" type="radio" value="logged_in"<?php echo('logged_in' == $cd_ab['access'] ? ' checked="checked"' : ''); ?> /> <?php _e('Logged in users only (including admins)', 'cd_ab') ?><br />
            <input name="cd_ab_access" type="radio" value="all"<?php echo('all' == $cd_ab['access'] ? ' checked="checked"' : ''); ?> /> <?php _e('All visitors (even not logged in)', 'cd_ab') ?>
        </p>
        <?php
        do_action( 'cd_ab_admin_privacy', $cd_ab );
    }
    
    function on_cd_ab_admin_users($cd_ab) {
        global $bp, $wpdb;
        $all_fields_ids = $wpdb->get_results( $wpdb->prepare( "
            SELECT id, group_id, name, type 
            FROM {$bp->profile->table_name_fields} 
            WHERE parent_id = 0 
            ORDER BY group_id ASC, field_order ASC" ) );
        do_action( 'cd_ab_admin_users_before', $cd_ab ); ?>
        <p>
        <script type="text/javascript">
        // Select all checkboxes after clicking the link All 
        jQuery(document).ready(function(){
            jQuery("a.select_all").click( function(e) {
                e.preventDefault();
                jQuery("input:checkbox.link").attr('checked', 'checked');
                return false;
            });
            jQuery("a.select_none").click( function(e) {
                e.preventDefault();
                jQuery("input:checkbox.link").removeAttr('checked');
                return false;
            });
        });
        </script>
        <table id="cd-ab-form" class="widefat link-group">
            <thead>
                <tr class="header">
                    <td colspan="2"><p><?php _e('Check here all fields from users profiles you want to show in a bubble', 'cd_ab') ?></p></td>
                    <td><p><center><?php _e('As a link?', 'cd_ab') ?></center></p></td>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php do_action( 'cd_ab_admin_users_before', $cd_ab ); ?>
            <?php 
            $i = 0;
            foreach ( $all_fields_ids as $field_obj => $field_data ) {
                $group_name = '';
                
                $field_data = (array) $field_data;
                
                $current[$i] = $field_data['group_id'];
                $prev = ($i - 1);
                if ( $current[ $i ] != $current[ $prev ] ) {
                    $group_name = $wpdb->get_results( 
                        $wpdb->prepare( "SELECT name FROM {$bp->profile->table_name_groups} WHERE id = ".$current[$i] ) 
                    );
                }
                if ( $group_name ) { ?>
                    <tr><td colspan="3"><p><strong><?php echo $group_name['0']->name; ?>:</strong></p></td></tr>
                <?php } ?>
                <tr>
                    <td scope="col" width="10px">
                        <input name="cd_ab_display[<?php echo $field_data['id']?>][name]" type="checkbox" <?php if ( $cd_ab[ $field_data['id'] ]['name'] ) { ?>checked="checked" <?php } ?>value="<?php echo $field_data['name']?>" />
                    </td>
                    <td><?php echo $field_data['name'] . ' <span style="color:grey">&rarr; ' . $field_data['type'] .'</span>' ?></td>
                    <td scope="col" style="text-align:center">
                        <input class="link" name="cd_ab_display[<?php echo $field_data['id']?>][link]" type="checkbox" <?php if ( $cd_ab[ $field_data['id'] ]['link'] && $field_data['type'] != 'datebox') { ?>checked="checked" <?php } ?>value="yes" <?php if ( $field_data['type'] == 'datebox') { ?>disabled=true<?php } ?> />
                    </td>
                    <input name="cd_ab_display[<?php echo $field_data['id']?>][type]" type="hidden" value="<?php echo $field_data['type']?>" />
                </tr>
            <?php 
            $i++;
            } ?>
            <?php do_action( 'cd_ab_admin_users_fields', $cd_ab ); ?>
            <tr style="border-bottom:2px">
                <td colspan="2" style="border-bottom:2px solid grey"><p><?php _e('<strong>Remember</strong>: the more you choose, the more time needed to process the request.', 'cd_ab') ?></p></td>
                <td style="text-align:center;border-bottom:2px solid grey">
                    <p>
                        <a href="#" class="select_all"><?php _e('All', 'cd_ab') ?></a> / <a href="#" class="select_none"><?php _e('None', 'cd_ab') ?></a>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle" colspan="2"><?php _e('Do you want to display Mention and Private message links?', 'cd_ab') ?></td>
                <td style="text-align:center">
                    <input name="cd_ab_messages" type="radio" value="yes"<?php echo( ('yes' == $cd_ab['messages'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Yes', 'cd_ab'); ?><br />
                    <input name="cd_ab_messages" type="radio" value="no"<?php echo( ('no' == $cd_ab['messages'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('No', 'cd_ab'); ?>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle" colspan="2"><?php _e('Do you want to display Add Friend link?', 'cd_ab') ?></td>
                <td style="text-align:center">
                    <input name="cd_ab_friend" type="radio" id="cd_ab_friend_yes" value="yes"<?php echo( ('yes' == $cd_ab['friend'] ) ? ' checked="checked"' : ''); ?> /> <label for="cd_ab_friend_yes"><?php _e('Yes', 'cd_ab'); ?></label><br />
                    <input name="cd_ab_friend" type="radio" id="cd_ab_friend_no" value="no"<?php echo( ('no' == $cd_ab['friend'] ) ? ' checked="checked"' : ''); ?> /> <label for="cd_ab_friend_no"><?php _e('No', 'cd_ab'); ?></label>
                </td>
            </tr>
            
            </tbody>
        </table>
        </p>
    <?php
        do_action( 'cd_ab_admin_users_after', $cd_ab );
    }
    
    function on_cd_ab_admin_extra($cd_ab) { ?>
        <table class="widefat link-group">
            <tr>
                <td width="82%" style="vertical-align:middle;"><?php _e('What do you want to do with an avatar to show a bubble?', 'cd_ab') ?></td>
                <td>
                    <input name="cd_ab_action" type="radio" value="click"<?php echo( ('click' == $cd_ab['action'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Click it', 'cd_ab'); ?><br />
                    <input name="cd_ab_action" type="radio" value="hover"<?php echo( ('hover' == $cd_ab['action'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Hover it', 'cd_ab'); ?><br />
                </td>
            <tr>
                <td style="vertical-align:middle;"><?php _e('How many <strong>seconds</strong> do you want bubble to wait before showing itself (Delay Time)?<br /><br /><strong>Remember:</strong> This number will be added to time needed to process the request (abt. 1 second).', 'cd_ab') ?></td>
                <td style="vertical-align:middle;">
                    <input name="cd_ab_delay" type="text" value="<?php echo $cd_ab['delay'] ?>" style="text-align:center;width:25px" />
                </td>
            </tr>
            <?php do_action( 'cd_ab_admin_extra', $cd_ab ); ?>
        </table>
    <?php
    }
    
    function on_cd_ab_admin_b_color($cd_ab) { ?>
        <p><?php _e('Which color of bubble border do you prefer to use?', 'cd_ab') ?></p>
        <p>
            <input name="cd_ab_color" type="radio" value="blue"<?php echo( ('blue' == $cd_ab['color'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Light Blue', 'cd_ab'); ?><br />
            <input name="cd_ab_color" type="radio" value="green"<?php echo( ('green' == $cd_ab['color'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Dark Green', 'cd_ab'); ?><br />
            <input name="cd_ab_color" type="radio" value="red"<?php echo( ('red' == $cd_ab['color'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Light Red', 'cd_ab'); ?><br />
            <input name="cd_ab_color" type="radio" value="black"<?php echo( ('black' == $cd_ab['color'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Just Black', 'cd_ab'); ?><br />
            <input name="cd_ab_color" type="radio" value="grey"<?php echo( ('grey' == $cd_ab['color'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Simply Grey', 'cd_ab'); ?>
        </p>
        <hr style="width:82%">
        <p><?php _e('Do you want to use images or CSS3 borders+shadows for corners?','cd_ab') ?></p>
        <p>
            <input name="cd_ab_borders" type="radio" value="images"<?php echo( ('images' == $cd_ab['borders'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('Images', 'cd_ab'); ?><br />
            <input name="cd_ab_borders" type="radio" value="css3"<?php echo( ('css3' == $cd_ab['borders'] ) ? ' checked="checked"' : ''); ?> /> <?php _e('CSS 3 borders+shadows', 'cd_ab'); ?>
        </p>
        <?php do_action( 'cd_ab_admin_b_color', $cd_ab );
    }
}
?>
