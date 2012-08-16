<?php
/*
Plugin Name: Gravity Forms User Registration Add-On
Plugin URI: http://www.gravityforms.com
Description: Allows WordPress users to be automatically created upon submitting a Gravity Form
Version: 1.4
Author: rocketgenius
Author URI: http://www.rocketgenius.com

------------------------------------------------------------------------
Copyright 2009 rocketgenius

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

add_action('init',  array('GFUser', 'init'), 9);
register_activation_hook( __FILE__, array("GFUser", "add_permissions"));

class GFUser {

    private static $path = "gravityformsuserregistration/userregistration.php";
    private static $url = "http://www.gravityforms.com";
    private static $slug = "gravityformsuserregistration";
    private static $version = "1.4";
    private static $min_gravityforms_version = "1.5";

    //Plugin starting point. Will load appropriate files
    public static function init(){

        //loading translations
        load_plugin_textdomain('gravityformsuserregistration', FALSE, '/gravityformsuserregistration/languages' );

        if(basename($_SERVER['PHP_SELF']) == "plugins.php") {

            add_action('after_plugin_row_' . self::$path, array('GFUser', 'plugin_row') );

            //force new remote request for version info on the plugin page
            self::flush_version_info();
        }

        if(!self::is_gravityforms_supported())
            return;

        if(is_admin()){

            // automatic upgrade hooks
            add_filter("transient_update_plugins", array('GFUser', 'check_update'));
            add_filter("site_transient_update_plugins", array('GFUser', 'check_update'));
            add_action('install_plugins_pre_plugin-information', array('GFUser', 'display_changelog'));

            // paypal plugin integration hooks
            add_action("gform_paypal_add_option_group", array("GFUser", "add_paypal_user_registration_options"), 10, 2);
            add_filter("gform_paypal_save_config", array("GFUser", "save_paypal_user_config"));

            // integrating with Members plugin
            if(function_exists('members_get_capabilities'))
                add_filter('members_get_capabilities', array("GFUser", "members_get_capabilities"));

            // creates the subnav left menu
            add_filter("gform_addon_navigation", array('GFUser', 'create_menu'));

            // activate password field
            add_filter("gform_enable_password_field", create_function("", "return true;"));

            // process users from unspammed entries
            add_action("gform_update_status", array("GFUser", "gf_process_user"), 10, 3);

            if(self::is_user_registration_page()){

                //enqueueing sack for AJAX requests
                wp_enqueue_script(array("sack"));
                wp_enqueue_script("jquery_json", self::get_base_url() . "/js/jquery.json-1.3.js", array("jquery"), self::$version);

                //loading data lib
                require_once(self::get_base_path() . "/data.php");

                //loading upgrade lib
                if(!class_exists("RGUserUpgrade"))
                    require_once("plugin-upgrade.php");

                //loading Gravity Forms tooltips
                require_once(GFCommon::get_base_path() . "/tooltips.php");
                add_filter('gform_tooltips', array('GFUser', 'tooltips'));

                //runs the setup when version changes
                self::setup();

            }
            else if(in_array(RG_CURRENT_PAGE, array("admin-ajax.php"))){

                //loading data class
                require_once(self::get_base_path() . "/data.php");

                add_action('wp_ajax_rg_user_update_feed_active', array('GFUser', 'update_feed_active'));
                add_action('wp_ajax_gf_select_user_registration_form', array('GFUser', 'select_user_registration_form'));

            }
            else if(RGForms::get("page") == "gf_settings") {
                // add settings page
                RGForms::add_settings_page("User Registration", array("GFUser", "settings_page"), self::get_base_url() . "/images/user-registration-icon-32.png");
            }
        }
        else{
            //loading data class
            require_once(self::get_base_path() . "/data.php");

            // handling post submission
            add_action("gform_post_submission", array("GFUser", "gf_create_user"), 10, 2);
            add_filter("gform_validation", array("GFUser", "user_registration_validation"));

            // add paypal ipn hooks
            add_action("gform_paypal_fulfillment", array("GFUser", "add_paypal_user"), 10, 8);
            add_action("gform_subscription_canceled", array("GFUser", "downgrade_paypal_user"), 10, 8);

            // custom registration form page
            add_action('wp_loaded', array('GFUser', 'custom_registration_page'));

        }

        // buddypress hooks
        if(self::is_bp_active()) {
            self::add_buddypress_hooks();
        }

        // multisite hooks
        if(is_multisite()) {
            self::add_multisite_hooks();
        }
    }

    public static function update_feed_active(){
        check_ajax_referer('rg_user_update_feed_active','rg_user_update_feed_active');
        $id = RGForms::post("feed_id");
        $feed = GFUserData::get_feed($id);
        GFUserData::update_feed($id, $feed["form_id"], RGForms::post("is_active"), $feed["meta"]);
    }

    //-------------- Automatic upgrade ---------------------------------------

    public static function flush_version_info(){
        if(!class_exists("RGUserUpgrade"))
            require_once("plugin-upgrade.php");

        RGUserUpgrade::set_version_info(false);
    }

    public static function plugin_row(){
        if(!self::is_gravityforms_supported()){
            $message = sprintf(__("Gravity Forms " . self::$min_gravityforms_version . " is required. Activate it now or %spurchase it today!%s", "gravityformsuserregistration"), "<a href='http://www.gravityforms.com'>", "</a>");
            RGUserUpgrade::display_plugin_message($message, true);
        }
        else{
            $version_info = RGUserUpgrade::get_version_info(self::$slug, self::get_key(), self::$version);

            if(!$version_info["is_valid_key"]){
                $new_version = version_compare(self::$version, $version_info["version"], '<') ? __('There is a new version of Gravity Forms User Registration Add-On available.', 'gravityformsuserregistration') .' <a class="thickbox" title="Gravity Forms User Registration Add-On" href="plugin-install.php?tab=plugin-information&plugin=' . self::$slug . '&TB_iframe=true&width=640&height=808">'. sprintf(__('View version %s Details', 'gravityformsuserregistration'), $version_info["version"]) . '</a>. ' : '';
                $message = $new_version . sprintf(__('%sRegister%s your copy of Gravity Forms to receive access to automatic upgrades and support. Need a license key? %sPurchase one now%s.', 'gravityformsuserregistration'), '<a href="admin.php?page=gf_settings">', '</a>', '<a href="http://www.gravityforms.com">', '</a>') . '</div></td>';
                RGUserUpgrade::display_plugin_message($message);
            }
        }
    }

    //Displays current version details on Plugin's page
    public static function display_changelog(){
        if($_REQUEST["plugin"] != self::$slug)
            return;

        //loading upgrade lib
        if(!class_exists("RGUserUpgrade"))
            require_once("plugin-upgrade.php");

        RGUserUpgrade::display_changelog(self::$slug, self::get_key(), self::$version);
    }

    public static function check_update($update_plugins_option){
        if(!class_exists("RGUserUpgrade"))
            require_once("plugin-upgrade.php");

        return RGUserUpgrade::check_update(self::$path, self::$slug, self::$url, self::$slug, self::get_key(), self::$version, $update_plugins_option);
    }

    private static function get_key(){
        if(self::is_gravityforms_supported())
            return GFCommon::get_key();
        else
            return "";
    }

    //------------------------------------------------------------------------

    // Creates User Registration left nav menu under Forms
    public static function create_menu($menus){

        // Adding submenu if user has access
        $permission = self::has_access("gravityforms_user_registration");
        if(!empty($permission))
            $menus[] = array("name" => "gf_user_registration", "label" => __("User Registration", "gravityformsuserregistration"), "callback" =>  array("GFUser", "user_registration_page"), "permission" => $permission);

        return $menus;
    }

    // Creates or updates database tables. Will only run when version changes
    private static function setup(){
        if(get_option("gf_user_registration_version") != self::$version)
            GFUserData::update_table();

        update_option("gf_user_registration_version", self::$version);
    }

    // Adds feed tooltips to the list of tooltips
    public static function tooltips($tooltips){
        $userregistration_tooltips = array(
            "user_registration_gravity_form" => "<h6>" . __("Gravity Form", "gravityformsuserregistration") . "</h6>" . __("Select the Gravity Form you would like to use to register users for your WordPress website.", "gravityformsuserregistration"),
            "user_registration_username" => "<h6>" . __("Username", "gravityformsuserregistration") . "</h6>" . __("Select the form field that should be used for the user's username.", "gravityformsuserregistration"),
            "user_registration_firstname" => "<h6>" . __("First Name", "gravityformsuserregistration") . "</h6>" . __("Select the form field that should be used for the user's first name.", "gravityformsuserregistration"),
            "user_registration_lastname" => "<h6>" . __("Last Name", "gravityformsuserregistration") . "</h6>" . __("Select the form field that should be used for the user's last name.", "gravityformsuserregistration"),
            "user_registration_displayname" => "<h6>" . __("Display Name", "gravityformsuserregistration") . "</h6>" . __("Select how the user's name should be displayed publicly.", "gravityformsuserregistration"),
            "user_registration_email" => "<h6>" . __("Email Address", "gravityformsuserregistration") . "</h6>" . __("Select the form field that should be used for the user's email address.", "gravityformsuserregistration"),
            "user_registration_password" => "<h6>" . __("Password", "gravityformsuserregistration") . "</h6>" . __("Select the form field that should be used for the user's password.", "gravityformsuserregistration"),
            "user_registration_role" => "<h6>" . __("Role", "gravityformsuserregistration") . "</h6>" . __("Select the role the user should be assigned.", "gravityformsuserregistration"),
            "user_registration_notification" => "<h6>" . __("Send Email?", "gravityformsuserregistration") . "</h6>" . __("Specify whether to send the password to the new user by email. <em class=\"enabled-by-default\">Enabled by default.</em>", "gravityformsuserregistration"),
            "user_registration_set_post_author" => "<h6>" . __("Set As Post Author", "gravityformsuserregistration") . "</h6>" . __("When a form submission creates a post and registers a user, set the new user as the post author. <em class=\"enabled-by-default\">Enabled by default.</em>", "gravityformsuserregistration"),
            "user_registration_condition" => "<h6>" . __("Registration Condition", "gravityformsuserregistration") . "</h6>" . __("When the registration condition is enabled, form submissions will only register the user when the condition is met. When disabled the user will not be registered.", "gravityformsuserregistration"),
            "user_registration_paypal_user_options" => "<h6>" . __("User Registration", "gravityformsuserregistration") . "</h6>" . __("The selected form also has a User Registration feed. These options allow you to specify how you would like the PayPal and User Registration Add-ons to work together.", "gravityformsuserregistration"),
            "user_registration_multisite_create_site" => "<h6>" . __("Create Site", "gravityformsuserregistration") . "</h6>" . __("When WordPress Multisite is enabled, checking this option will enable the creation of a new site on the network when a new user registers.", "gravityformsuserregistration"),
            "user_registration_multisite_site_address" => "<h6>" . __("Site Address", "gravityformsuserregistration") . "</h6>" . __("Select the form field that should be used for the site address.", "gravityformsuserregistration"),
            "user_registration_multisite_site_title" => "<h6>" . __("Site Title", "gravityformsuserregistration") . "</h6>" . __("Select the form field that should be used for the site title.", "gravityformsuserregistration"),
            "user_registration_multisite_site_role" => "<h6>" . __("Site Role", "gravityformsuserregistration") . "</h6>" . __("Select role the user should be assigned on the newly created site.", "gravityformsuserregistration"),
            "user_registration_multisite_root_role" => "<h6>" . __("Current Site Role", "gravityformsuserregistration") . "</h6>" . __("Select role the user should be assigned on the site they registered from. This option overrides the \"Role\" option under User Settings.", "gravityformsuserregistration")
        );
        return array_merge($tooltips, $userregistration_tooltips);
    }

    public static function user_registration_page(){
        $view = RGForms::get("view");
        $id = RGForms::get("id");
        if($view == "edit")
            self::edit_page($id);
        else if($view == "stats")
            self::stats_page($id);
        else
            self::list_page();
    }

    // List Page

    private static function list_page(){
        if(!self::is_gravityforms_supported()){
            die(sprintf(__("User Registration Add-On requires Gravity Forms %s. Upgrade automatically on the %sPlugin page%s.", "gravityformsuserregistration"), self::$min_gravityforms_version, "<a href='plugins.php'>", "</a>"));
        }

        $action = RGForms::post("action");
        $bulk_action = RGForms::post("bulk_action");

        if($action == "delete"){
            check_admin_referer("list_action", "gf_user_registration_list");

            $id = absint($_POST["action_argument"]);
            GFUserData::delete_feed($id);
            ?>
            <div class="updated fade" style="padding:6px"><?php _e("Feed deleted.", "gravityformsuserregistration") ?></div>
            <?php
        }
        else if (!empty($bulk_action)){
            check_admin_referer("list_action", "gf_user_registration_list");
            $selected_feeds = RGForms::post("feed");
            if(is_array($selected_feeds)){
                foreach($selected_feeds as $feed_id)
                    GFUserData::delete_feed($feed_id);
            }
            ?>
            <div class="updated fade" style="padding:6px"><?php _e("Feeds deleted.", "gravityformsuserregistration") ?></div>
            <?php
        }

        ?>
        <div class="wrap">
            <img alt="<?php _e("User Registration", "gravityformsuserregistration") ?>" src="<?php echo self::get_base_url()?>/images/user-registration-icon-32.png" style="float:left; margin:15px 7px 0 0;"/>
            <h2><?php _e("User Registration Forms", "gravityformsuserregistration"); ?>
            <a class="button add-new-h2" href="admin.php?page=gf_user_registration&view=edit&id=0"><?php _e("Add New", "gravityformsuserregistration") ?></a>
            </h2>

            <form id="feed_form" method="post">
                <?php wp_nonce_field('list_action', 'gf_user_registration_list') ?>
                <input type="hidden" id="action" name="action"/>
                <input type="hidden" id="action_argument" name="action_argument"/>

                <div class="tablenav">
                    <div class="alignleft actions" style="padding:8px 0 7px;">
                        <label class="hidden" for="bulk_action"><?php _e("Bulk action", "gravityformsuserregistration") ?></label>
                        <select name="bulk_action" id="bulk_action">
                            <option value=''> <?php _e("Bulk action", "gravityformsuserregistration") ?> </option>
                            <option value='delete'><?php _e("Delete", "gravityformsuserregistration") ?></option>
                        </select>
                        <?php
                        echo '<input type="submit" class="button" value="' . __("Apply", "gravityformsuserregistration") . '" onclick="if( jQuery(\'#bulk_action\').val() == \'delete\' && !confirm(\'' . __("Delete selected feeds? ", "gravityformsuserregistration") . __("\'Cancel\' to stop, \'OK\' to delete.", "gravityformsuserregistration") .'\')) { return false; } return true;"/>';
                        ?>
                    </div>
                </div>
                <table class="widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                            <th scope="col" id="active" class="manage-column check-column"></th>
                            <th scope="col" class="manage-column"><?php _e("Form", "gravityformsuserregistration") ?></th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                            <th scope="col" id="active" class="manage-column check-column"></th>
                            <th scope="col" class="manage-column"><?php _e("Form", "gravityformsuserregistration") ?></th>
                        </tr>
                    </tfoot>

                    <tbody class="list:user user-list">
                        <?php

                        $settings = GFUserData::get_feeds();
                        if(is_array($settings) && sizeof($settings) > 0){
                            foreach($settings as $setting){
                                ?>
                                <tr class='author-self status-inherit' valign="top">
                                    <th scope="row" class="check-column"><input type="checkbox" name="feed[]" value="<?php echo $setting["id"] ?>"/></th>
                                    <td><img src="<?php echo self::get_base_url() ?>/images/active<?php echo intval($setting["is_active"]) ?>.png" alt="<?php echo $setting["is_active"] ? __("Active", "gravityformsuserregistration") : __("Inactive", "gravityformsuserregistration");?>" title="<?php echo $setting["is_active"] ? __("Active", "gravityformsuserregistration") : __("Inactive", "gravityformsuserregistration");?>" onclick="ToggleActive(this, <?php echo $setting['id'] ?>); " /></td>
                                    <td class="column-title">
                                        <a href="admin.php?page=gf_user_registration&view=edit&id=<?php echo $setting["id"] ?>" title="<?php _e("Edit", "gravityformsuserregistration") ?>"><?php echo $setting["form_title"] ?></a>
                                        <div class="row-actions">
                                            <span class="edit">
                                            <a title="<?php _e("Edit", "gravityformsuserregistration")?>" href="admin.php?page=gf_user_registration&view=edit&id=<?php echo $setting["id"] ?>" title="<?php _e("Edit", "gravityformsuserregistration") ?>"><?php _e("Edit", "gravityformsuserregistration") ?></a>
                                            |
                                            </span>
                                            <span class="edit">
                                            <a title="<?php _e("Delete", "gravityformsuserregistration") ?>" href="javascript: if(confirm('<?php _e("Delete this feed? ", "gravityformsuserregistration") ?> <?php _e("\'Cancel\' to stop, \'OK\' to delete.", "gravityformsuserregistration") ?>')){ DeleteSetting(<?php echo $setting["id"] ?>);}"><?php _e("Delete", "gravityformsuserregistration")?></a>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        else{
                            ?>
                            <tr>
                                <td colspan="3" style="padding:20px;">
                                    <?php echo sprintf(__("You don't have any User Registration feeds configured. Let's go %screate one%s!", "gravityformsuserregistration"), '<a href="admin.php?page=gf_user_registration&view=edit&id=0">', "</a>"); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
        <script type="text/javascript">

            function DeleteSetting(id){
                jQuery("#action_argument").val(id);
                jQuery("#action").val("delete");
                jQuery("#feed_form")[0].submit();
            }

            function ToggleActive(img, feed_id){
                var is_active = img.src.indexOf("active1.png") >=0
                if(is_active){
                    img.src = img.src.replace("active1.png", "active0.png");
                    jQuery(img).attr('title','<?php _e("Inactive", "gravityformsuserregistration") ?>').attr('alt', '<?php _e("Inactive", "gravityformsuserregistration") ?>');
                }
                else{
                    img.src = img.src.replace("active0.png", "active1.png");
                    jQuery(img).attr('title','<?php _e("Active", "gravityformsuserregistration") ?>').attr('alt', '<?php _e("Active", "gravityformsuserregistration") ?>');
                }

                var mysack = new sack("<?php echo admin_url("admin-ajax.php")?>" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "rg_user_update_feed_active" );
                mysack.setVar( "rg_user_update_feed_active", "<?php echo wp_create_nonce("rg_user_update_feed_active") ?>" );
                mysack.setVar( "feed_id", feed_id );
                mysack.setVar( "is_active", is_active ? 0 : 1 );
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() { alert('<?php echo esc_js(__("Ajax error while updating feed", "gravityformsuserregistration" )) ?>' )};
                mysack.runAJAX();

                return true;
            }

        </script>
        <?php
    }

    public static function settings_page(){

        $ur_settings = get_option('gf_userregistration_settings') ? get_option('gf_userregistration_settings') : array();

        $uninstall = RGForms::post("uninstall");
        if($uninstall){
            check_admin_referer("uninstall", "gf_user_registration_uninstall");
            self::uninstall();

            ?>
            <div class="updated fade" style="padding:20px;">
                <?php echo sprintf(__("Gravity Forms User Registration Add-On has been successfully uninstalled. It can be re-activated from the %splugins page%s.", "gravityformsuserregistration"), "<a href='plugins.php'>","</a>"); ?>
            </div>
            <?php
            return;
        }

        $is_submit = rgpost('gf_userregistration_settings_submit');
        if($is_submit){

            $ur_settings['enable_custom_reg_page'] = rgpost('gf_userregistration_enable_custom_reg_page');
            $ur_settings['custom_reg_page'] = rgpost('gf_userregistration_custom_reg_page');

            update_option('gf_userregistration_settings', $ur_settings);
        }

        ?>

        <form method="post">
            <?php wp_nonce_field("update", "gf_userregistration_update") ?>
            <input type="hidden" value="1" name="gf_userregistration_settings_submit" />

            <h3><?php _e("User Registration Settings", "gravityformsuserregistration") ?></h3>

            <table width="100%">
                <tr>
                    <td valign="top" width="260"><?php _e("Custom Registration Page", "gravityformsuserregistration") ?></td>
                    <td valign="top">
                        <input type="checkbox" name="gf_userregistration_enable_custom_reg_page" id="gf_userregistration_enable_custom_reg_page" <?php echo rgar($ur_settings, 'enable_custom_reg_page') ? "checked='checked'" : "" ?> onclick="if(jQuery(this).is(':checked')) { jQuery('#gf_userregistration_custom_reg_page_options').slideDown(); } else { jQuery('#gf_userregistration_custom_reg_page_options').slideUp(); }" />
                        <label for="gf_userregistration_enable_custom_reg_page" class="inline"><?php _e("Enable Custom Registration Page", "gravityformsuserregistration") ?> <?php gform_tooltip("user_registration_username") ?></label>

                        <div id="gf_userregistration_custom_reg_page_options" style="<?php echo !rgar($ur_settings, 'enable_custom_reg_page') ? 'display:none;' : ''; ?> margin: 10px 0;">

                            <?php $pages = get_posts('post_type=page&numberposts=-1&order=asc&orderby=title'); ?>
                            <select id="gf_userregistration_custom_reg_page" name="gf_userregistration_custom_reg_page">
                                <?php foreach($pages as $page){
                                    $selected = $page->ID == rgar($ur_settings, 'custom_reg_page') ? "selected='selected'" : "";
                                    ?>
                                    <option value="<?php echo $page->ID; ?>" <?php echo $selected ?>><?php echo $page->post_title; ?></option>
                                <?php } ?>
                            </select>

                        </div>

                    </td>
                </tr>
            </table>

            <input type="submit" name="save" value="<?php _e("Save Settings", "gravityformsuserregistration") ?>" class="button-primary" style="margin-top:40px;" />
        </form>

        <form action="" method="post">
            <?php wp_nonce_field("uninstall", "gf_user_registration_uninstall") ?>
            <?php if(GFCommon::current_user_can_any("gravityforms_user_registration_uninstall")){ ?>
                <div class="hr-divider"></div>

                <h3><?php _e("Uninstall User Registration Add-On", "gravityformsuserregistration") ?></h3>
                <div class="delete-alert"><?php _e("Warning! This operation deletes ALL User Registration Feeds.", "gravityformsuserregistration") ?>
                    <?php
                    $uninstall_button = '<input type="submit" name="uninstall" value="' . __("Uninstall User Registration Add-On", "gravityformsuserregistration") . '" class="button" onclick="return confirm(\'' . __("Warning! ALL User Registration Feeds will be deleted. This cannot be undone. \'OK\' to delete, \'Cancel\' to stop", "gravityformsuserregistration") . '\');"/>';
                    echo apply_filters("gform_user_registration_uninstall_button", $uninstall_button);
                    ?>
                </div>

            <?php } ?>
        </form>
        <?php
    }

    // Edit Page

    private static function edit_page(){
        ?>

        <style type="text/css">
            #user_registration_submit_container{clear:both;}
            .user_registration_col_heading{padding-bottom:2px; border-bottom: 1px solid #ccc; font-weight:bold; width:120px;}
            .user_registration_field_cell {padding: 6px 17px 0 0; margin-right:15px;}

            .user_registration_validation_error{ background-color:#FFDFDF; margin-top:4px; margin-bottom:6px; padding-top:6px; padding-bottom:6px; border:1px dotted #C89797;}
            .user_registration_validation_error span {color: red;}
            .left_header { float:left; width: 260px;}
            .column_right { margin-left: 260px; }
            .margin_vertical_10{margin: 10px 0; padding-left:5px;}
            .margin_vertical_30{margin: 30px 0; padding-left:5px;}
            .width-1{width:300px;}
            .gf_user_registration_invalid_form{margin-top:30px; background-color:#FFEBE8;border:1px solid #CC0000; padding:10px; width:600px;}

            .hide-label { display: none; }
            .enabled-by-default { color: #999; }

            div.custom_metaname { position: relative; }
            span.reset { background:url("images/xit.gif") no-repeat scroll 0 -10px transparent; cursor:pointer; display:block;
                position:absolute; text-indent:-9999px; width:10px; height: 10px; right: 16px; top: 7px; }
            .hover span.reset { background-position: 0 0; }

            option.label { font-style: italic; color: #999; }
            .metaname, .metavalue { float: left; }
            .metavalue { margin-left: 30px; }
            .metaname .width-1 { margin-right: 10px; width: 220px; }
            .add_field_choice, .delete_field_choice { margin: 3px 0 4px 3px; }
            .custom_usermeta .margin_vertical_10 { overflow: hidden; margin-bottom: 0; }
            .custom_usermeta option.custom { font-weight: bold; }
            .custom_metaname { float: left; }
            .notice { font-size: 12px; font-style: italic; color: #aaa; }
            .system-option, .password-field option:last-child { font-style: italic; }
            .checkbox-label { font-size: 12px; }

            .disabled label { color: #999; }

        </style>

        <div class="wrap">
            <img alt="<?php _e("User Registration", "gravityformsuserregistration") ?>" style="margin: 15px 7px 0pt 0pt; float: left;" src="<?php echo self::get_base_url() ?>/images/user-registration-icon-32.png"/>
            <h2><?php _e("User Registration Settings", "gravityformsuserregistration") ?></h2>

        <?php

        //getting setting id (0 when creating a new one)
        $id = RGForms::post("user_registration_setting_id");
        $id = !empty($id) ? $id : absint(RGForms::get("id"));
        $config = empty($id) ? array("meta" => array(), "is_active" => true) : GFUserData::get_feed($id);
        $is_validation_error = false;

        // handle submission
        $is_submit = RGForms::post("gf_user_registration_submit");
        if(!empty($is_submit) && check_admin_referer('user_registration_edit_submit','user_registration_edit_submit')){

            $form_id = RGForms::post("gf_user_registration_form");
            $config["form_id"] = absint($form_id);

            // standard meta
            $config["meta"]["username"] = RGForms::post("gf_user_registration_username");
            $config["meta"]["firstname"] = RGForms::post("gf_user_registration_firstname");
            $config["meta"]["lastname"] = RGForms::post("gf_user_registration_lastname");
            $config["meta"]["displayname"] = RGForms::post("gf_user_registration_displayname");
            $config["meta"]["email"] = RGForms::post("gf_user_registration_email");
            $config["meta"]["password"] = RGForms::post("gf_user_registration_password");
            $config["meta"]["role"] = RGForms::post("gf_user_registration_role");

            // user meta
            $json = stripslashes(RGForms::post("gf_user_meta"));
            $config["meta"]["user_meta"] = GFCommon::json_decode($json);

            //clean user meta (workaround to avoid values being marked as array)
            if(is_array($config["meta"]["user_meta"])){
                foreach($config["meta"]["user_meta"] as &$meta){
                    if(is_array($meta["meta_value"])){
                        $meta["meta_value"] = $meta["meta_value"][0];
                    }
                }
            }

            // registration condition
            $config['meta']['reg_condition_enabled'] = RGForms::post('gf_user_registration_enabled');
            $config['meta']['reg_condition_field_id'] = RGForms::post('gf_user_registration_field_id');
            $config['meta']['reg_condition_operator'] = RGForms::post('gf_user_registration_operator');
            $config['meta']['reg_condition_value'] = RGForms::post('gf_user_registration_value');

            // additional meta options
            $config['meta']['notification'] = RGForms::post('gf_user_registration_notification');
            $config['meta']['set_post_author'] = RGForms::post('gf_user_registration_set_post_author');

            // use to save custom config options (used by BuddyPress)
            $config = apply_filters("gform_user_registration_save_config", $config);

            $is_validation_error = apply_filters("gform_user_registration_config_validation", false, $config);

            // validate and create/update feed
            if($config["meta"]["email"] && $config["meta"]["username"] && !$is_validation_error) {
                $id = GFUserData::update_feed($id, $config["form_id"], $config["is_active"], $config["meta"]);
                ?>

                <div class="updated fade" style="padding:6px"><?php echo sprintf(__("Feed Updated. %sback to list%s", "gravityformsuserregistration"), "<a href='?page=gf_user_registration'>", "</a>") ?></div>

                <?php
            }
            else {
                $is_validation_error = true;
            }

        }

        $form = (isset($config["form_id"]) && $config["form_id"]) ? RGFormsModel::get_form_meta($config["form_id"]) : array();
        $form_fields = $email_fields = $selection_fields = $password_fields = array();

        if(!empty($form)) {
            $set_author_style = (GFCommon::has_post_field($form['fields'])) ? 'display:block' : 'display:none';
            $form_fields = self::get_form_fields($form);
            $email_fields = self::get_fields_by_type($form, 'email');
            $selection_fields = GFCommon::get_selection_fields($form, $config['meta']['reg_condition_field_id']);

            // add custom option to password fields
            $password_default = array(array('', __('Auto Generate Password', 'gravityformsuserregistration') ));
            $password_fields = self::get_fields_by_type($form, 'password');
            $password_fields = array_merge($password_fields, $password_default);
        }

        ?>

        <form method="post" action="" id="gf_user_form">

            <input type="hidden" name="user_registration_setting_id" value="<?php echo $id ?>" />
            <input type="hidden" name="gf_user_meta" id="gf_user_meta" value="" />
            <?php wp_nonce_field('user_registration_edit_submit','user_registration_edit_submit'); ?>

            <div class="margin_vertical_10<?php echo $is_validation_error ? " user_registration_validation_error" : "" ?>">
                <?php
                if($is_validation_error){
                    ?>
                    <span><?php _e('Your Registration Feed could not be saved. Please update the errors below and re-save your feed.', 'gravityformsuserregistration'); ?></span>
                    <?php
                }
                ?>
            </div> <!-- / validation message -->

            <div id="user_registration_form_container" valign="top" class="margin_vertical_10">
                <label for="gf_user_registration_form" class="left_header"><?php _e("Gravity Form", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_gravity_form") ?></label>

                <select id="gf_user_registration_form" name="gf_user_registration_form" onchange="SelectForm(jQuery(this).val(), '<?php echo rgar($config, 'id') ?>');">
                    <option value=""><?php _e("Select a form", "gravityformsuserregistration"); ?> </option>
                    <?php

                    $active_form = $config["form_id"];
                    $available_forms = GFUserData::get_available_forms($active_form);

                    foreach($available_forms as $current_form) {
                        $selected = (absint($current_form->id) == $config["form_id"]) ? 'selected="selected"' : '';
                        ?>

                            <option value="<?php echo absint($current_form->id) ?>" <?php echo $selected; ?>><?php echo esc_html($current_form->title) ?></option>

                        <?php
                    }
                    ?>
                </select>
                &nbsp;&nbsp;
                <img src="<?php echo self::get_base_url() ?>/images/loading.gif" id="user_registration_wait" style="display: none;"/>

                <div id="gf_user_registration_invalid_product_form" class="gf_user_registration_invalid_form"  style="display:none;">
                    <?php _e("The form selected does not have any Email fields. Please add an Email field to the form and try again.", "gravityformsuserregistration") ?>
                </div>

            </div> <!-- / select form -->

            <div id="user_registration_field_group" valign="top" <?php echo empty($config["form_id"]) ? "style='display:none;'" : "" ?>>

                <div id="standard_fields">

                    <h3>User Settings</h3>

                    <div class="margin_vertical_10 <?php echo ($is_validation_error && empty($config['meta']['username'])) ? 'user_registration_validation_error' : ''; ?>">
                        <label class="left_header" for="gf_user_registration_username"><?php _e("Username", "gravityformsuserregistration"); ?> <span class="description">(<?php _e("required", "gravityformsuserregistration"); ?>)</span> <?php gform_tooltip("user_registration_username") ?></label>
                        <?php echo self::get_field_drop_down("gf_user_registration_username", $form_fields, rgar($config['meta'], 'username'), "width-1 standard-meta"); ?>
                    </div>

                    <div class="margin_vertical_10">
                        <label class="left_header" for="gf_user_registration_firstname"><?php _e("First Name", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_firstname") ?></label>
                        <?php echo self::get_field_drop_down("gf_user_registration_firstname", $form_fields, rgar($config['meta'], 'firstname'), "width-1 standard-meta"); ?>
                    </div>

                    <div class="margin_vertical_10">
                        <label class="left_header" for="gf_user_registration_lastname"><?php _e("Last Name", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_lastname") ?></label>
                        <?php echo self::get_field_drop_down("gf_user_registration_lastname", $form_fields, rgar($config['meta'], 'lastname'), "width-1 standard-meta"); ?>
                    </div>

                    <div class="margin_vertical_10">
                        <label class="left_header" for="gf_user_registration_displayname">
                            <?php _e("Display Name", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_displayname") ?>
                        </label>
                        <?php $display_names = array(
                            'username' => '{username}',
                            'firstname' => '{first name}',
                            'lastname' => '{last name}',
                            'firstlast' => '{first name} {last name}',
                            'lastfirst' => '{last name} {first name}'
                            );
                        ?>
                        <select id="gf_user_registration_displayname" name="gf_user_registration_displayname" class="width-1 standard-meta">
                            <?php foreach($display_names as $key => $value) {
                                $selected = rgar($config['meta'], 'displayname') == $key ? 'selected="selected"' : '';
                                ?>

                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>

                            <?php } ?>
                        </select>
                    </div>

                    <div class="margin_vertical_10 <?php echo ($is_validation_error && empty($config['meta']['email'])) ? 'user_registration_validation_error' : ''; ?>">
                        <label class="left_header" for="gf_user_registration_email"><?php _e("Email Address", "gravityformsuserregistration"); ?> <span class="description">(<?php _e("required", "gravityformsuserregistration"); ?>)</span> <?php gform_tooltip("user_registration_email") ?></label>
                        <?php echo self::get_field_drop_down("gf_user_registration_email", $email_fields, rgar($config['meta'], 'email'), "width-1 email-field"); ?>
                    </div>

                    <div class="margin_vertical_10">
                        <label class="left_header" for="gf_user_registration_password"><?php _e("Password", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_password") ?></label>
                        <?php echo self::get_field_drop_down("gf_user_registration_password", $password_fields, rgar($config['meta'], 'password'), "width-1 password-field", false); ?>
                    </div>

                    <?php $disabled = is_multisite() && rgars($config['meta'], 'multisite_options/create_site') ? 'disabled="disabled"' : ''; ?>
                    <div class="margin_vertical_10 <?php echo $disabled ? 'disabled' : '' ?>">
                        <label class="left_header" for="gf_user_registration_role"><?php _e("Role", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_role") ?></label>
                        <select id="gf_user_registration_role" name="gf_user_registration_role" class="width-1" <?php echo $disabled; ?>>
                            <?php if(is_multisite() && rgars($config['meta'], 'multisite_options/create_site')): ?>
                                <option value="" selected="selected" class="empty-option"></option>
                            <?php endif; ?>
                            <?php self::display_role_dropdown_options(rgars($config, 'meta/role')); ?>
                        </select>
                    </div>

                </div> <!-- / standard fields -->

                <div id="user_meta_fields">

                    <h3>User Meta</h3>

                    <div id="custom_usermeta" class="custom_usermeta"></div>

                </div> <!-- / user meta fields -->

                <?php do_action("gform_user_registration_add_option_section", $config, $form, $is_validation_error); // buddypress, networkmode ?>

                <div id="additional_options">

                    <h3>Additional Options</h3>

                    <div class="margin_vertical_10">
                        <label class="left_header"><?php _e("Send Email?", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_notification") ?></label>

                        <input type="checkbox" id="gf_user_registration_notification" name="gf_user_registration_notification" value="1" <?php echo (rgar($config['meta'], 'notification') == 1 || !isset($config["meta"]["notification"])) ? "checked='checked'" : ""?>/>
                        <label for="gf_user_registration_notification" class="checkbox-label"><?php _e("Send this password to the new user by email.", "gravityformsuserregistration"); ?></label>
                    </div> <!-- / send email? -->

                    <div id="gf_user_registration_set_post_author_container" class="margin_vertical_10" style="<?php echo $set_author_style; ?>">
                        <label class="left_header"><?php _e("Set As Post Author", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_set_post_author") ?></label>

                        <input type="checkbox" id="gf_user_registration_set_post_author" name="gf_user_registration_set_post_author" value="1" <?php echo (rgar($config['meta'], 'set_post_author') == 1 || !isset($config["meta"]["set_post_author"])) ? "checked='checked'" : ""?>/>
                        <label for="gf_user_registration_set_post_author" class="checkbox-label"><?php _e("Enable", "gravityformsuserregistration"); ?></label>
                    </div> <!-- / set post author -->

                    <div id="gf_user_registration_section" valign="top" class="margin_vertical_10">
                        <label for="gf_user_registration_optin" class="left_header"><?php _e("Registration Condition", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_condition") ?></label>

                        <div id="gf_user_registration_option" class="column_right">
                            <input type="checkbox" id="gf_user_registration_enabled" name="gf_user_registration_enabled" value="1" onclick="if(this.checked){jQuery('#gf_user_registration_container').fadeIn('fast');} else{jQuery('#gf_user_registration_container').fadeOut('fast');}" <?php echo rgar($config['meta'], 'reg_condition_enabled') ? "checked='checked'" : ""?>/>
                            <label for="gf_user_registration_enabled" class="checkbox-label"><?php _e("Enable", "gravityformsuserregistration"); ?></label>

                            <div id="gf_user_registration_container" style="padding-top:5px; <?php echo !$config["meta"]["reg_condition_enabled"] ? "display:none" : ""?>">

                                <div id="gf_user_registration_condition_fields" <?php echo empty($selection_fields) ? "style='display:none'" : ""?>>
                                    <?php _e("Register the user if ", "gravityformsuserregistration") ?>

                                    <select id="gf_user_registration_field_id" name="gf_user_registration_field_id" class='optin_select' onchange='jQuery("#gf_user_registration_value").html(GetFieldValues(jQuery(this).val(), "", 20));'>
                                        <?php echo $selection_fields ?>
                                    </select>
                                    <select id="gf_user_registration_operator" name="gf_user_registration_operator">
                                        <option value="is" <?php echo rgar($config['meta'], 'reg_condition_operator') == 'is' ? 'selected="selected"' : '' ?>><?php _e("is", "gravityformsuserregistration") ?></option>
                                        <option value="isnot" <?php echo rgar($config['meta'], 'reg_condition_operator') == "isnot" ? 'selected="selected"' : '' ?>><?php _e("is not", "gravityformsuserregistration") ?></option>
                                    </select>
                                    <select id="gf_user_registration_value" name="gf_user_registration_value" class='optin_select'></select>

                                </div>

                                <div id="gf_user_registration_condition_message" <?php echo !empty($selection_fields) ? "style='display:none'" : ""?>>
                                    <?php _e("To create a registration condition, your form must have a drop down, checkbox or multiple choice field", "gravityformsuserregistration") ?>
                                </div>

                            </div>
                        </div>

                    </div> <!-- / registration conditional -->

                    <?php do_action("gform_user_registration_add_option_group", $config, $form, $is_validation_error); ?>

                    <div id="user_registration_submit_container" class="margin_vertical_30">
                        <input type="submit" name="gf_user_registration_submit" value="<?php echo empty($id) ? __("Save", "gravityformsuserregistration") : __("Update", "gravityformsuserregistration"); ?>" class="button-primary"/>
                        <input type="button" value="<?php _e("Cancel", "gravityformsuserregistration"); ?>" class="button" onclick="javascript:document.location='admin.php?page=gf_user_registration'" />
                    </div> <!-- / form actions -->

                </div> <!-- / additional options -->

            </div> <!-- / field group -->

        </form>

        </div> <!-- / wrap -->

        <script type="text/javascript">

            <?php $user_meta = rgar($config['meta'], 'user_meta'); ?>

            var GFUser = {
                form: <?php echo !empty($form) ? GFCommon::json_encode($form) : 'new Array()'; ?>,
                form_fields: <?php echo (!empty($form_fields)) ? GFCommon::json_encode($form_fields) : "new Array()"; ?>,
                form_id: <?php echo rgar($config, 'form_id') ? rgar($config, 'form_id') : "''" ?>,
                user_meta: <?php echo !empty($user_meta) ? GFCommon::json_encode($user_meta) : "[new MetaOption()]"; ?>,
                bp_gform_options: <?php echo (!empty($form)) ? GFCommon::json_encode(self::get_bp_gform_fields($form)) : "''"; ?>,
                meta_names: [
                    { 'name': 'Website', 'value': 'user_url' },
                    { 'name': 'AIM', 'value': 'aim' },
                    { 'name': 'Yahoo', 'value': 'yim' },
                    { 'name': 'Jabber / Google Talk', 'value': 'jabber' },
                    { 'name': 'Biographical Information', 'value': 'description' }
                ]
            }

            function SelectForm(formId, configId){

                if(!formId){
                    jQuery("#user_registration_field_group").slideUp();
                    return;
                }

                jQuery("#user_registration_wait").show();
                jQuery("#user_registration_field_group").slideUp();

                var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "gf_select_user_registration_form" );
                mysack.setVar( "gf_select_user_registration_form", "<?php echo wp_create_nonce("gf_select_user_registration_form") ?>" );
                mysack.setVar( "form_id", formId);
                mysack.setVar( "config_id", configId);
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() {jQuery("#user_registration_wait").hide(); alert('<?php echo esc_js(__("Ajax error while selecting a form", "gravityformsuserregistration")); ?>' )};
                mysack.runAJAX();

                return true;
            }

            function EndSelectForm(form_meta, form_fields, field_options, password_options, email_options, options_meta, bp_gform_options){

                if(email_options == '<option value=""></option>') {
                    jQuery(".gf_user_registration_invalid_form").show();
                    jQuery("#user_registration_wait").hide();
                    return;
                } else {
                    jQuery(".gf_user_registration_invalid_form").hide();
                }

                // set global form object & reset user meta
                GFUser.form = form_meta;
                GFUser.form_fields = form_fields;
                GFUser.bp_gform_options = bp_gform_options;
                GFUser.user_meta = [new MetaOption()];

                // update dropdowns with selected form's fields
                jQuery.each(jQuery("select.standard-meta"), function(){
                    jQuery(this).html(field_options);
                });

                jQuery.each(jQuery("select.password-field"), function(){
                    jQuery(this).html(password_options + '<option class="system-option">Auto Generate Password</option>');
                });

                jQuery.each(jQuery("select.email-field"), function(){
                    jQuery(this).html(email_options);
                });

                CreateCustomMetaOptions();

                // handle set post author option
                if(options_meta['set_post_author']) {
                    jQuery("#gf_user_registration_set_post_author_container").show();
                } else {
                    jQuery("#gf_user_registration_set_post_author_container").hide();
                }

                // handle registration option
                jQuery("#gf_user_registration_enabled").attr('checked', false);
                SetRegistrationCondition("","");

                // Form Selected Event
                jQuery(document).trigger('gform_user_registration_form_selected', [GFUser.form]);

                jQuery("#user_registration_field_group").slideDown();
                jQuery("#user_registration_wait").hide();

            }

            function CreateCustomMetaOptions(){

                var form = GFUser.form;
                var user_meta = GFUser.user_meta;
                var meta_names = GFUser.meta_names;
                var images_url = '<?php echo GFCommon::get_base_url() . "/images"?>';
                var str = '';

                for(var i=0; i<user_meta.length; i++){

                    var show_select = (user_meta[i].custom == true) ? "display:none;" : "";
                    var show_input = (user_meta[i].custom == false) ? "display:none;" : "";

                    str += '<div class="margin_vertical_10">';
                    str += '<div class="metaname"><select type="text" name="gf_user_meta_name_' + i + '" id="gf_user_meta_name_' + i + '" class="meta-name-select width-1" style="' + show_select + '">';
                    str += getMetaNameOptions(meta_names, user_meta[i].meta_name) + '</select>'
                    str += '<div class="custom_metaname" style="' + show_input + '">' + getMetaNameInput(i, user_meta[i]) + '</div></div>';
                    str += '<div class="metavalue"><select type="text" name="gf_user_meta_value_' + i + '" id="gf_user_meta_value_' + i + '" class="meta-value-select width-1">';
                    str += getMetaValueOptions(form, user_meta[i].meta_value) + '</select></div>';

                    str += "<img src='" + images_url + "/add.png' class='add_field_choice' title='<?php _e("add another rule", "gravityformsuserregistration"); ?>' alt='<?php _e("add another rule", "gravityformsuserregistration"); ?>' style='cursor:pointer;' onclick=\"InsertRule(" + (i+1) + ", 'user_meta');\" />";
                    if(user_meta.length > 1 )
                        str += "<img src='" + images_url + "/remove.png' title='<?php _e("remove this rule", "gravityformsuserregistration"); ?>' alt='<?php _e("remove this rule", "gravityformsuserregistration"); ?>' class='delete_field_choice' style='cursor:pointer;' onclick=\"DeleteRule(" + (i) + ", 'user_meta');\" /></li>";

                    str += '</div>';
                }

                jQuery("#custom_usermeta").html(str);
            }

            function getMetaNameOptions(meta_names, selected, custom_option) {

                var str = '<option value="" class="label">Select Meta Option</option>';
                for(i = 0; i < meta_names.length; i++) {
                    if(meta_names[i].value == selected) {
                        str += '<option value="' + meta_names[i].value + '" selected="selected">' + meta_names[i].name + '</option>';
                    } else {
                        str += '<option value="' + meta_names[i].value + '">' + meta_names[i].name + '</option>';
                    }
                }

                if(custom_option != false) {
                    str += '<option value="gf_custom" class="custom">Add Custom</option>';
                }

                return str;
            }

            function getMetaValueOptions(form, meta_value) {

                var form_fields = GFUser.form_fields;

                var str = '<option value=""></option>';
                for(i = 0; i < form_fields.length; i++) {
                    if(form_fields[i][0] == meta_value) {
                        str += '<option value="' + form_fields[i][0] + '" selected="selected">' + form_fields[i][1] + '</option>';
                    } else {
                        str += '<option value="' + form_fields[i][0] + '">' + form_fields[i][1] + '</option>';
                    }
                }

                return str;
            }

            function getMetaNameInput(index, user_meta) {

                var meta_name = (user_meta.meta_name != "") ? user_meta.meta_name : "<?php _e("Enter Meta Name", "gravityformsuserregistration"); ?>";

                str = '<input type="text" name="gf_user_custom_meta_name_' + index + '" id="gf_user_custom_meta_name_' + index + '" class="width-1" value="' + meta_name + '" />';
                str += '<span class="reset">Reset</span>';

                return str;
            }

            function InsertRule(ruleIndex, meta_group){

                if(meta_group == 'user_meta') {
                    GFUser[meta_group].splice(ruleIndex, 0, new MetaOption());
                    CreateCustomMetaOptions();
                } else {
                    // Insert Rule Action (used by BuddyPress)
                    jQuery(document).trigger('gform_user_registration_insert_rule', [ruleIndex, meta_group]);
                }

            }

            function DeleteRule(ruleIndex, meta_group){

                if(meta_group == 'user_meta') {
                    GFUser[meta_group].splice(ruleIndex, 1);
                    CreateCustomMetaOptions();
                } else {
                    // Delete Rule Action (used by BuddyPress)
                    jQuery(document).trigger('gform_user_registration_delete_rule', [ruleIndex, meta_group]);
                }

            }

            function MetaOption() {
                this.meta_name = "";
                this.meta_value = "";
                this.custom = false;
            }

            function IndexOf(ary, item){
                for(var i=0; i<ary.length; i++)
                    if(ary[i] == item)
                        return i;

                return -1;
            }

            function saveUserMeta() {

                var user_meta = GFUser.user_meta;

                for(var i=0; i<user_meta.length; i++){
                    user_meta[i].custom = (jQuery("#gf_user_meta_name_" + i).css('display') == 'none') ? true : false;
                    user_meta[i].meta_name = (user_meta[i].custom == true) ? jQuery("#gf_user_custom_meta_name_" + i).val() : jQuery("#gf_user_meta_name_" + i).val();
                    user_meta[i].meta_value = jQuery("#gf_user_meta_value_" + i).val();
                }

                GFUser.user_meta = user_meta;

                // save meta to hidden field for php
                var json = jQuery.toJSON(user_meta);
                jQuery("#gf_user_meta").val(json);

            }

            // initialize
            jQuery(document).ready(function(){

                <?php if(!empty($form)) { ?>
                    CreateCustomMetaOptions();
                <?php } ?>

                // after GFUser is init, trigger actions
                jQuery(document).trigger('gform_gfuser_object_init');

                // disable 'Select a form' option
                jQuery(this).find('option:first').attr('disabled', 'disabled');

                // custom meta input
                jQuery("select.meta-name-select").live('change', function(){
                     if(jQuery(this).val() == 'gf_custom') {
                         jQuery(this).fadeOut(function(){
                             jQuery(this).val("");
                             jQuery(this).next('div.custom_metaname').fadeIn();
                             jQuery(this).next('div.custom_metaname').find('input').cleardefault().focus();
                         });
                     }
                });

                // custom meta input reset button
                jQuery("span.reset").live('click', function(){
                    jQuery(this).parent('div.custom_metaname').fadeOut(function(){
                        jQuery(this).siblings('select').fadeIn();
                    })
                });

                // add hover class for custom meta input reset
                jQuery("div.custom_metaname").live('mouseover mouseout', function(event) {
                    if (event.type == 'mouseover') {
                        jQuery(this).addClass('hover');
                    } else {
                        jQuery(this).removeClass('hover');
                    }
                });

                // save entered information
                jQuery(".metaname input, .metaname select, .metavalue select").live('change', function(){ saveUserMeta(); });
                jQuery("#gf_user_form").submit(function(){ saveUserMeta(); })

                // clear default values
                jQuery.fn.cleardefault = function() {
                    return this.focus(function() {
                        if( this.value == this.defaultValue ) {
                            this.value = "";
                        }
                    }).blur(function() {
                    if( !this.value.length ) {
                        this.value = this.defaultValue;
                    }
                });

            }

            });

        </script>

        <script type="text/javascript">

            // Registration Conditional Functions

            <?php
            if(!empty($config["form_id"])){
                ?>

                // initializing registration condition drop downs
                jQuery(document).ready(function(){

                    //form = [] <?php //echo GFCommon::json_encode($form_meta) ?>;

                    var selectedField = "<?php echo str_replace('"', '\"', $config["meta"]["reg_condition_field_id"])?>";
                    var selectedValue = "<?php echo str_replace('"', '\"', $config["meta"]["reg_condition_value"])?>";
                    SetRegistrationCondition(selectedField, selectedValue);
                });

                <?php
            }
            ?>

            function SetRegistrationCondition(selectedField, selectedValue){

                // load form fields
                jQuery("#gf_user_registration_field_id").html(GetSelectableFields(selectedField, 20));
                var optinConditionField = jQuery("#gf_user_registration_field_id").val();
                var checked = jQuery("#gf_user_registration_enabled").attr('checked');

                if(optinConditionField){
                    jQuery("#gf_user_registration_condition_message").hide();
                    jQuery("#gf_user_registration_condition_fields").show();
                    jQuery("#gf_user_registration_value").html(GetFieldValues(optinConditionField, selectedValue, 20));
                }
                else{
                    jQuery("#gf_user_registration_condition_message").show();
                    jQuery("#gf_user_registration_condition_fields").hide();
                }

                if(!checked) jQuery("#gf_user_registration_container").hide();

            }

            function GetFieldValues(fieldId, selectedValue, labelMaxCharacters){
                if(!fieldId)
                    return "";

                var str = "";
                var field = GetFieldById(fieldId);
                if(!field || !field.choices)
                    return "";

                var isAnySelected = false;

                for(var i=0; i<field.choices.length; i++){
                    var fieldValue = field.choices[i].value ? field.choices[i].value : field.choices[i].text;
                    var isSelected = fieldValue == selectedValue;
                    var selected = isSelected ? "selected='selected'" : "";
                    if(isSelected)
                        isAnySelected = true;

                    str += "<option value='" + fieldValue.replace("'", "&#039;") + "' " + selected + ">" + TruncateMiddle(field.choices[i].text, labelMaxCharacters) + "</option>";
                }

                if(!isAnySelected && selectedValue){
                    str += "<option value='" + selectedValue.replace("'", "&#039;") + "' selected='selected'>" + TruncateMiddle(selectedValue, labelMaxCharacters) + "</option>";
                }

                return str;
            }

            function GetFieldById(fieldId){
                var form = GFUser.form;
                for(var i=0; i<form.fields.length; i++){
                    if(form.fields[i].id == fieldId)
                        return form.fields[i];
                }
                return null;
            }

            function TruncateMiddle(text, maxCharacters){
                if(text.length <= maxCharacters)
                    return text;
                var middle = parseInt(maxCharacters / 2);
                return text.substr(0, middle) + "..." + text.substr(text.length - middle, middle);
            }

            function GetSelectableFields(selectedFieldId, labelMaxCharacters){
                var form = GFUser.form;
                var str = "";
                var inputType;
                for(var i=0; i<form.fields.length; i++){
                    fieldLabel = form.fields[i].adminLabel ? form.fields[i].adminLabel : form.fields[i].label;
                    inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
                    if(inputType == "checkbox" || inputType == "radio" || inputType == "select"){
                        var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : "";
                        str += "<option value='" + form.fields[i].id + "' " + selected + ">" + TruncateMiddle(fieldLabel, labelMaxCharacters) + "</option>";
                    }
                }
                return str;
            }

        </script>

        <?php

    }

    public static function select_user_registration_form() {

        check_ajax_referer("gf_select_user_registration_form", "gf_select_user_registration_form");

        $form_id =  intval(RGForms::post("form_id"));
        $config_id = intval(RGForms::post("config_id"));
        $options_meta = array();

        // fields meta
        $form = RGFormsModel::get_form_meta($form_id);
        $options_meta['set_post_author'] = (GFCommon::has_post_field($form['fields'])) ? true : false;

        $form_fields = self::get_form_fields($form);
        $password_fields = self::get_fields_by_type($form, 'password');
        $email_fields = self::get_fields_by_type($form, 'email');
        $bp_gform_fields = self::get_bp_gform_fields($form);

        $form_dropdown_items = self::get_field_drop_down_items($form_fields, "");
        $password_dropdown_items = self::get_field_drop_down_items($password_fields, "", false);
        $email_dropdown_items = self::get_field_drop_down_items($email_fields, "");

        die("EndSelectForm(" . GFCommon::json_encode($form) . ", " .
            GFCommon::json_encode($form_fields) . ", '" .
            str_replace("'", "\'", $form_dropdown_items) . "', '" .
            str_replace("'", "\'", $password_dropdown_items) . "', '" .
            str_replace("'", "\'", $email_dropdown_items) . "', " .
            GFCommon::json_encode($options_meta) . ", " .
            GFCommon::json_encode($bp_gform_fields) . " );");
    }

    private static function get_field_drop_down($field_name, $fields, $selected_field, $class, $empty_option = true){
        $str = "<select name='$field_name' id='$field_name' class='$class'>";
        $str .= self::get_field_drop_down_items($fields, $selected_field, $empty_option);
        $str .= "</select>";
        return $str;
    }

    private static function get_field_drop_down_items($fields, $selected_field, $empty_option = true){

        $str = '';

        if($empty_option == true)
            $str = '<option value=""></option>';

        if(is_array($fields)){
            foreach($fields as $field){
                $field_id = $field[0];
                $field_label = $field[1];
                $selected = $field_id == $selected_field ? "selected='selected'" : "";
                $str .= "<option value='" . $field_id . "' ". $selected . ">" . GFCommon::truncate_middle($field_label, 25) . "</option>";
            }
        }

        return $str;
    }

    // Buddy Press

    public static function add_buddypress_options($config, $form) {
        ?>

        <h3><?php _e('BuddyPress Profile', 'gravityformsuserregistration'); ?></h3>

        <div id="gf_buddypress_meta" class="gf_buddypress_meta"></div>
        <input id="gf_buddypress_config" name="gf_buddypress_config" type="hidden" value="" />

        <script type="text/javascript">

            jQuery(document).ready(function($){

                // when GFUser object is init, append BuddyPress properties
                $(document).bind('gform_gfuser_object_init', function(){

                    // add buddypress properties to GFUser object
                    GFUser.buddypress_meta = <?php echo (!empty($config['meta']['buddypress_meta'])) ? GFCommon::json_encode($config['meta']['buddypress_meta']) : "[new BuddyPressMetaOption()]"; ?>;
                    GFUser.buddypress_options = <?php echo GFCommon::json_encode(self::get_buddypress_fields()); ?>;

                    CreateBuddyPressMetaOptions();

                });

                // setup on form select
                $(document).bind('gform_user_registration_form_selected', function(event, form){
                    CreateBuddyPressMetaOptions();
                });

                // insert rule action event
                jQuery(document).bind('gform_user_registration_insert_rule', function(event, ruleIndex, meta_group){
                    if(meta_group == 'buddypress_meta') {
                        GFUser[meta_group].splice(ruleIndex, 0, new BuddyPressMetaOption());
                        CreateBuddyPressMetaOptions();
                    }
                });

                // remove rule action event
                jQuery(document).bind('gform_user_registration_delete_rule', function(event, ruleIndex, meta_group){
                    if(meta_group == 'buddypress_meta') {
                        GFUser[meta_group].splice(ruleIndex, 1);
                        CreateBuddyPressMetaOptions();
                    }
                });

                // save entered information
                jQuery(".metaname input, .metaname select, .metavalue select").live('change', function(){ saveBuddyPressMeta(); });
                jQuery("#gf_user_form").submit(function(){ saveBuddyPressMeta(); })

            });

            function BuddyPressMetaOption(){
                this.meta_name = "";
                this.meta_value = "";
            }

            function CreateBuddyPressMetaOptions(){

                var form = GFUser.form;
                var buddypress_meta = (GFUser.buddypress_meta != '') ? GFUser.buddypress_meta : [new BuddyPressMetaOption()];
                var buddypress_options = GFUser.buddypress_options;
                var images_url = '<?php echo GFCommon::get_base_url() . "/images"?>';
                var str = '';

                for(var i=0; i<buddypress_meta.length; i++){

                    str += '<div class="margin_vertical_10">';
                    str += '<div class="metaname"><select type="text" name="gf_buddypress_meta_name_' + i + '" id="gf_buddypress_meta_name_' + i + '" class="width-1">';
                    str += getMetaNameOptions(buddypress_options, buddypress_meta[i].meta_name, false) + '</select></div>';
                    str += '<div class="metavalue"><select type="text" name="gf_buddypress_meta_value_' + i + '" id="gf_buddypress_meta_value_' + i + '" class="meta-value-select width-1">';
                    str += getBuddyPressMetaValueOptions(form, buddypress_meta[i].meta_value) + '</select></div>';

                    str += "<img src='" + images_url + "/add.png' class='add_field_choice' title='<?php _e("add another rule", "gravityformsuserregistration") ?>' alt='<?php _e("add another rule", "gravityformsuserregistration") ?>' style='cursor:pointer;' onclick=\"InsertRule(" + (i+1) + ", 'buddypress_meta');\" />";
                    if(buddypress_meta.length > 1 )
                        str += "<img src='" + images_url + "/remove.png' title='<?php _e("remove this rule", "gravityformsuserregistration") ?>' alt='<?php _e("remove this rule", "gravityformsuserregistration") ?>' class='delete_field_choice' style='cursor:pointer;' onclick=\"DeleteRule(" + (i) + ", 'buddypress_meta');\" /></li>";

                    str += '</div>';
                }

                jQuery("#gf_buddypress_meta").html(str);
            }

            function getBuddyPressMetaValueOptions(form, meta_value) {

                var form_fields = GFUser.bp_gform_options;

                var str = '<option value=""></option>';
                for(i = 0; i < form_fields.length; i++) {

                    if(form_fields[i][0] == meta_value) {
                        str += '<option value="' + form_fields[i][0] + '" selected="selected">' + form_fields[i][1] + '</option>';
                    } else {
                        str += '<option value="' + form_fields[i][0] + '">' + form_fields[i][1] + '</option>';
                    }
                }

                return str;
            }

            function saveBuddyPressMeta() {

                var buddypress_meta = GFUser.buddypress_meta;

                for(var i=0; i<buddypress_meta.length; i++){
                    buddypress_meta[i].meta_name = jQuery("#gf_buddypress_meta_name_" + i).val();
                    buddypress_meta[i].meta_value = jQuery("#gf_buddypress_meta_value_" + i).val();
                }

                GFUser.buddypress_meta = buddypress_meta;

                // save meta to hidden field for php
                var json = jQuery.toJSON(buddypress_meta);
                jQuery("#gf_buddypress_config").val(json);

            }

        </script>

        <?php
    }

    public static function get_buddypress_fields() {
        require_once(WP_PLUGIN_DIR . '/buddypress/bp-xprofile/bp-xprofile-classes.php');

        // get BP field groups
        $groups = BP_XProfile_Group::get(array('fetch_fields' => true ));

        $buddypress_fields = array();
        $i = 0;
        foreach($groups as $group) {

            if(!is_array($group->fields))
                continue;

            foreach($group->fields as $field) {
                $buddypress_fields[$i]['name'] = $field->name;
                $buddypress_fields[$i]['value'] = $field->id;
                $i++;
            }
        }

        return $buddypress_fields;
    }

    public static function save_buddypress_meta($config) {
        
        $json = stripslashes(RGForms::post("gf_buddypress_config"));
        $config["meta"]["buddypress_meta"] = GFCommon::json_decode($json);
        
        return $config;
    }

    public static function prepare_buddypress_data($user_id, $config, $entry) {
        
        // required for user to display in the directory
        bp_update_user_meta($user_id, 'last_activity', true);
        
        $buddypress_meta = rgars($config, 'meta/buddypress_meta');

        if(empty($buddypress_meta))
            return;

        $form = RGFormsModel::get_form_meta($entry['form_id']);
        $buddypress_row = array();

        $i = 0;
        foreach($buddypress_meta as $meta_item) {

            $buddypress_row[$i]['field_id'] = $meta_item['meta_name'];
            $buddypress_row[$i]['user_id'] = $user_id;

            // get GF and BP fields
            $gform_field = RGFormsModel::get_field($form, $meta_item['meta_value']);
            $bp_field = new BP_XProfile_Field();
            $bp_field->bp_xprofile_field($meta_item['meta_name']);

            // if bp field is a checkbox AND gf field is a checkbox, get array of input values
            if($bp_field->type == 'checkbox' && $gform_field['type'] == 'checkbox') {
                $meta_value = RGFormsModel::get_lead_field_value($entry, $gform_field);
                $meta_value = array_filter($meta_value, 'GFUser::not_empty');
            }
            // same as above, but for multiselectbox
            else if($bp_field->type == 'multiselectbox' && $gform_field['type'] == 'checkbox') {
                $meta_value = RGFormsModel::get_lead_field_value($entry, $gform_field);
                $meta_value = array_filter($meta_value, 'GFUser::not_empty');
            }
            else if($bp_field->type == 'datebox' && $gform_field['type'] == 'date'){
                $meta_value = strtotime(self::get_prepared_value($gform_field, $meta_item['meta_value'], $entry));
            }
            else {
                $meta_value = self::get_prepared_value($gform_field, $meta_item['meta_value'], $entry);
            }

            $buddypress_row[$i]['value'] = xprofile_sanitize_data_value_before_save($meta_value, $meta_item['meta_name']);
            $buddypress_row[$i]['last_update'] = date( 'Y-m-d H:i:s' );
            $i++;
        }

        GFUserData::insert_buddypress_data($buddypress_row);
    }
    
    public static function bp_user_signup($user_id) {
        global $bp;
        do_action( 'bp_core_activated_user', $user_id, null, new WP_User($user_id) );
    }

    // Multisite

    public static function add_multisite_section($config, $form, $is_validation_error) {

        get_current_site();

        $form_fields = self::get_form_fields($form);
        $multisite_options = rgar($config['meta'], 'multisite_options');

        if(!self::is_root_site())
            return;

        ?>

        <style type="text/css">
            #multisite_option_items { overflow: hidden; }
        </style>

        <div id="multsite_options" class="multsite_options">

            <h3><?php _e("Network Options", "userregistrationuserregistration"); ?></h3>

            <div class="margin_vertical_10">
                <label class="left_header"><?php _e("Create Site", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_multisite_create_site") ?></label>
                <input type="checkbox" id="gf_user_registration_multisite_create_site" name="gf_user_registration_multisite_create_site" value="1" <?php echo rgar($multisite_options, 'create_site') ? "checked='checked'" : "" ?> onclick="toggleNetworkOptions(this);" />
                <label for="gf_user_registration_multisite_create_site" class="checkbox-label"><?php _e("Create new site when a user registers.", "gravityformsuserregistration"); ?></label>
            </div> <!-- / send email? -->

            <div id="multisite_option_items" style="<?php echo rgar($multisite_options, 'create_site') ? 'display:block;' : 'display:none;'; ?>">

                <div class="margin_vertical_10 <?php echo ($is_validation_error && empty($multisite_options['site_address']) ) ? 'user_registration_validation_error' : ''; ?>">
                    <label class="left_header" for="gf_user_registration_multisite_site_address"><?php _e("Site Address", "gravityformsuserregistration"); ?> <span class="description">(<?php _e("required", "gravityformsuserregistration"); ?>)</span> <?php gform_tooltip("user_registration_multisite_site_address") ?></label>
                    <?php echo self::get_field_drop_down("gf_user_registration_multisite_site_address", $form_fields, rgar($multisite_options, 'site_address'), "width-1 standard-meta"); ?>
                </div>

                <div class="margin_vertical_10 <?php echo ($is_validation_error && empty($multisite_options['site_title']) ) ? 'user_registration_validation_error' : ''; ?>">
                    <label class="left_header" for="gf_user_registration_multisite_site_title"><?php _e("Site Title", "gravityformsuserregistration"); ?>  <span class="description">(<?php _e("required", "gravityformsuserregistration"); ?>)</span> <?php gform_tooltip("user_registration_multisite_site_title") ?></label>
                    <?php echo self::get_field_drop_down("gf_user_registration_multisite_site_title", $form_fields, rgar($multisite_options, 'site_title'), "width-1 standard-meta"); ?>
                </div>

                <div class="margin_vertical_10">
                    <label class="left_header" for="gf_user_registration_multisite_site_role">
                        <?php _e("Site Role", "gravityformsuserregistration"); ?>
                        <span class="description">(<?php _e("required", "gravityformsuserregistration"); ?>)</span>
                        <?php gform_tooltip("user_registration_multisite_site_role") ?>
                    </label>
                    <select id="gf_user_registration_multisite_site_role" name="gf_user_registration_multisite_site_role" class="width-1">
                        <?php self::display_role_dropdown_options(rgar($multisite_options, 'site_role')); ?>
                    </select>
                </div>

                <div class="margin_vertical_10">
                    <label class="left_header" for="gf_user_registration_multisite_root_role">
                        <?php _e("Current Site Role", "gravityformsuserregistration"); ?>
                        <span class="description">(<?php _e("required", "gravityformsuserregistration"); ?>)</span>
                        <?php gform_tooltip("user_registration_multisite_root_role") ?>
                    </label>
                    <select id="gf_user_registration_multisite_root_role" name="gf_user_registration_multisite_root_role" class="width-1">
                        <?php self::display_role_dropdown_options(rgar($multisite_options, 'root_role'), true); ?>
                    </select>
                </div>

            </div>

        </div> <!-- / multisite options -->

        <script type="text/javascript">

            function toggleNetworkOptions(elem) {

                if(elem.checked) {
                    jQuery('#multisite_option_items').slideDown('fast');
                    jQuery('#gf_user_registration_role')
                        .attr('disabled', 'disabled')
                        .append('<option value="" selected="selected" class="empty-option"></option>')
                        .parent('div').addClass('disabled');
                }
                else {
                    jQuery('#multisite_option_items').slideUp('fast');
                    jQuery('#gf_user_registration_role')
                        .removeAttr('disabled')
                        .parent('div').removeClass('disabled')
                        .find('option.empty-option').remove()

                }

            }
        </script>

        <?php
    }

    public static function save_multisite_config($config) {

        $config['meta']['multisite_options']['create_site'] = RGForms::post("gf_user_registration_multisite_create_site");
        $config['meta']['multisite_options']['site_address'] = RGForms::post("gf_user_registration_multisite_site_address");
        $config['meta']['multisite_options']['site_title'] = RGForms::post("gf_user_registration_multisite_site_title");
        $config['meta']['multisite_options']['site_role'] = RGForms::post("gf_user_registration_multisite_site_role");
        $config['meta']['multisite_options']['root_role'] = RGForms::post("gf_user_registration_multisite_root_role");

        return $config;
    }

    public static function validate_multisite_config($is_validation_error, $config) {

        $multisite_options = $config['meta']['multisite_options'];

        if(empty($multisite_options['create_site']))
            return $is_validation_error = false;

        if(empty($multisite_options['site_address']) || empty($multisite_options['site_title']))
            return $is_validation_error = true;

    }

    public static function validate_multisite_submission($form, $config, $pagenum){
        global $path;

        $multisite_options = $config['meta']['multisite_options'];

        // make sure multisite create site option is set
        if(empty($multisite_options['create_site']))
            return $form;

        // $_POST to Entry
        $entry = self::convert_post_to_entry();
        $domain = '';

        $site_address_field = RGFormsModel::get_field($form, $multisite_options['site_address']);
        $site_address = self::get_prepared_value($site_address_field, $multisite_options['site_address'], $entry);

        $site_title_field = RGFormsModel::get_field($form, $multisite_options['site_title']);
        $site_title = self::get_prepared_value($site_title_field, $multisite_options['site_title'], $entry);

        // get validation result for multi-site fields
        $validation_result = wpmu_validate_blog_signup($site_address, $site_title);
        $error_msg = false;

        // site address validation, only if on correct page
        if($site_address_field['pageNumber'] == $pagenum){

            $error_msg = (isset($validation_result['errors']->errors['blogname'][0])) ? $validation_result['errors']->errors['blogname'][0] : false;

            if($error_msg != false)
                $form = self::add_validation_failure($multisite_options['site_address'], $form, $error_msg);

        }

        // site title validation, only if on correct page
        if($site_title_field['pageNumber'] == $pagenum){

            $error_msg = (isset($validation_result['errors']->errors['blog_title'][0])) ? $validation_result['errors']->errors['blog_title'][0] : false;

            if($error_msg != false)
                $form = self::add_validation_failure($multisite_options['site_title'], $form, $error_msg);

        }

        return $form;
    }

    public static function create_new_multisite($user_id, $config, $entry, $password) {
        global $wpdb, $current_site, $current_user, $base;

        $multisite_options = $config['meta']['multisite_options'];
        $user = get_userdata($user_id);
        $user_set_pass = rgar($config['meta'], 'password') && rgar($entry, rgar($config['meta'], 'password'));
        $pass = rgar($entry, rgar($config['meta'], 'password'));

        if(!$user_set_pass)
            remove_filter('update_welcome_email', 'bp_core_filter_blog_welcome_email');

        // make sure multisite 'create site' option is set (user registration plugin)
        if(empty($multisite_options['create_site']))
            return;

        // get the domain name
        $domain = '';
        $site_address = $entry[$multisite_options['site_address']];
        if(!preg_match( '/(--)/', $site_address) && preg_match('|^([a-zA-Z0-9-])+$|', $site_address) )
            $domain = strtolower($site_address);

        // get the site title and user email
        $title = $entry[$multisite_options['site_title']];
        $email = $user->user_email;

        // final check to make sure our essentials are good to go
        if(empty($domain) || empty($email) || !is_email($email))
            return;

        if(is_subdomain_install()){
            $newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $current_site->domain );
            $path = $base;
        } else {
            $newdomain = $current_site->domain;
            $path = $base . $domain . '/';
        }

        // create the new site!
        $id = wpmu_create_blog($newdomain, $path, $title, $user_id , array( 'public' => 1 ), $current_site->id);

        if(is_wp_error($id))
            return;

        // add entry ID to site meta for new site
        GFUserData::update_site_meta($id, 'entry_id', $entry['id']);

        $dashboard_blog = get_dashboard_blog();
        if(!is_super_admin($user_id) && get_user_option('primary_blog', $user_id) == $dashboard_blog->blog_id) {
            update_user_option($user_id, 'primary_blog', $id, true);
        }

        if(rgar($multisite_options, 'site_role')) {
            $user = new WP_User($user_id, null, $id);
            $user->set_role(rgar($multisite_options, 'site_role'));
        }

        $root_role = rgar($multisite_options, 'root_role');

        // if no root role, remove user from current site
        if(!$root_role) {
            remove_user_from_blog($user_id);
        }
        // otherwise, update their role on current site
        else {
            $user = new WP_User($user_id);
            $user->set_role($root_role);
        }

        $content_mail = sprintf(__( "New site created by %1s\n\nAddress: http://%2s\nName: %3s", "gravityformsuserregistration"), $current_user->user_login , $newdomain . $path, stripslashes($title));
        wp_mail(get_site_option('admin_email'), sprintf(__('[%s] New Site Created', "gravityformsuserregistration"), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
        wpmu_welcome_notification($id, $user_id, $password, $title, array('public' => 1));

        do_action('gform_site_created', $id, $user_id, $entry, $config, $password);
    }

    public static function is_root_site() {

        global $current_blog, $current_site;

        if($current_site->blog_id != $current_blog->blog_id)
            return false;

        return true;
    }

    private static function get_paypal_config($form_id, $entry){
        if(!class_exists('GFPayPal'))
            return false;

        if(method_exists("GFPayPal", "get_config_by_entry")){
            return GFPayPal::get_config_by_entry($entry);
        }
        else{
            return GFPayPal::get_config($form_id);
        }
    }

    // Hook into Gravity Forms
    public static function gf_create_user($entry, $form, $fulfilled = false) {

        // if the entry is marked as spam
        if(rgar($entry, 'status') == 'spam')
            return;

        $config = self::get_config($form['id']);
        $meta = rgar($config, 'meta');

        // if there is no registration feed or the registration condition is not met or the feed is not active, abandon ship
        if(!$config || !self::registration_condition_met($form, $config, $entry) || !$config['is_active'])
            return;

        // if PayPal Add-on was used for this entry, integrate
        $paypal_config = self::get_paypal_config($form["id"], $entry);

        if($paypal_config) {
            //$paypal_config = self::get_paypal_config($form["id"], $entry);
            $order_total = GFCommon::get_order_total($form, $entry);

            // delay the registration IF:
            // - the delay registration option is checked
            // - the order total does NOT equal zero (no delay since there will never be a payment)
            // - the payment has not already been fulfilled
            if($paypal_config && $paypal_config['meta']['delay_registration'] && $order_total != 0 && $fulfilled != true)
                return;
        }

        $user_name = apply_filters("gform_username_{$form['id']}",
            apply_filters('gform_username', self::get_meta_value('username', $config, $form, $entry), $config, $form, $entry),
            $config, $form, $entry );
        $user_email = self::get_meta_value('email', $config, $form, $entry);
        $user_pass = self::get_meta_value('password', $config, $form, $entry);

        if(empty($user_name) || empty($user_email))
            return;

        if(!function_exists('username_exists'))
            require_once(ABSPATH . WPINC . "/registration.php");

        $user_id = username_exists($user_name);

        // create the user and password, then add user meta
        if(!$user_id && empty($user_pass)) {

            $user_pass = wp_generate_password();
            $user_id = wp_create_user($user_name, $user_pass, $user_email);
            if(is_wp_error($user_id))
                return;

            update_user_option($user_id, 'default_password_nag', true, false);
            self::add_user_meta($user_id, $config, $form, $entry, array());

        }
        else if(!$user_id) {

            $user_id = wp_create_user($user_name, $user_pass, $user_email);
            if(is_wp_error($user_id))
                return;

            GFUserData::remove_password($form['id'], $entry['id'], rgar($meta, 'password'));
            self::add_user_meta($user_id, $config, $form, $entry, array());

        }
        else {

            // if user with this username already exists, abort user registration
            return;

        }

        if(rgar($meta, 'role')) {
            $user = new WP_User($user_id);
            $user->set_role(rgar($meta, 'role'));
        }

        // set post author
        if(!empty($entry['post_id']) && rgar($meta, 'set_post_author'))
            self::attribute_post_author($user_id, $entry['post_id']);

        // send user/admin notifications
        if(rgar($meta, 'notification')) {
            wp_new_user_notification($user_id, $user_pass);
        } else {
            wp_new_user_notification($user_id, ""); // sending a blank password only sends notification to admin
        }

        do_action('gform_user_registered', $user_id, $config, $entry, $user_pass);
    }

    public static function gf_process_user($lead_id, $status, $prev_status) {

        // check if user has already been created for this lead
        if(self::get_user_id_by_meta('entry_id', $lead_id) || !($prev_status == 'spam' && $status == 'active'))
            return;

        $entry = RGFormsModel::get_lead($lead_id);
        $form = RGFormsModel::get_form_meta($entry['form_id']);

        self::gf_create_user($entry, $form);

    }

    private static function add_user_meta($user_id, $config, $form, $entry, $name_fields) {

        $form = RGFormsModel::get_form_meta($entry['form_id']);

        $standard_meta = array(
            'firstname' => 'first_name',
            'lastname' => 'last_name'
            );

        foreach($standard_meta as $meta_key => $wp_meta_key) {
            update_user_meta($user_id, $wp_meta_key, self::get_meta_value($meta_key, $config, $form, $entry));
        }

        // update display name format
        self::update_display_name($user_id, $config);

        // to track which entry the user was registered from
        update_user_meta($user_id, 'entry_id', $entry['id']);

        // add custom user meta
        $custom_meta = rgars($config, 'meta/user_meta');

        if(is_array($custom_meta) && !empty($custom_meta)) {
            $value = '';
            foreach($custom_meta as $custom_meta_item) {

                // skip empty meta items
                if(!$custom_meta_item['meta_name'] || !$custom_meta_item['meta_value'])
                    continue;

                $field = RGFormsModel::get_field($form, $custom_meta_item['meta_value']);
                $value = self::get_prepared_value($field, $custom_meta_item['meta_value'], $entry);

                if($custom_meta_item['meta_name'] == 'user_url' && $value) {
                    self::update_user_property($user_id, 'user_url', $value);
                } else
                if(!empty($value)) {
                    update_user_meta($user_id, $custom_meta_item['meta_name'], $value);
                }

            }
        }

    }

    public static function update_display_name($user_id, $config) {

        $meta = rgar($config, 'meta');
        $display_format = rgar($meta, 'displayname');
        $user = new WP_User($user_id);

        switch($display_format) {
        case 'firstname':
            $display_name = $user->first_name;
            break;
        case 'lastname':
            $display_name = $user->last_name;
            break;
        case 'firstlast':
            $display_name = $user->first_name . ' ' . $user->last_name;
            break;
        case 'lastfirst':
            $display_name = $user->last_name . ' ' . $user->first_name;
            break;
        default:
            $display_name = $user->user_login;
            break;
        }

        self::update_user_property($user_id, 'display_name', $display_name);

    }

    public static function update_user_property($user_id, $prop_key, $prop_value) {

        if(!$user_id)
            return;

        $user = new WP_User($user_id);
        $userdata = $user->data;

        $new_userdata = new stdClass();
        $new_userdata->ID = $userdata->ID;
        $new_userdata->$prop_key = $prop_value;

        wp_update_user(get_object_vars($new_userdata));

    }

    public static function get_meta_value($meta_key, $config, $form, $entry) {

        $is_username = $meta_key == 'username';
        $meta = rgar($config, 'meta');
        $field_id = rgar($meta, $meta_key);

        $field = RGFormsModel::get_field($form, rgar($meta, $meta_key));
        $value = !empty($field) ? self::get_prepared_value($field, rgar($meta, $meta_key), $entry, $is_username) : '';

        return $value;
    }

    public static function registration_condition_met($form, $config, $entry) {

        $config = $config["meta"];

        $operator = $config["reg_condition_operator"];
        $field = RGFormsModel::get_field($form, $config["reg_condition_field_id"]);

        if(empty($field) || !$config["reg_condition_enabled"])
            return true;

        $is_visible = !RGFormsModel::is_field_hidden($form, $field, array(), $entry);
        $field_value = RGFormsModel::get_lead_field_value($entry, $field);

        $is_value_match = RGFormsModel::is_value_match($field_value, $config["reg_condition_value"]);
        $is_match = $is_value_match && $is_visible;

        $create_user = ($operator == "is" && $is_match) || ($operator == "isnot" && !$is_match);

        return $create_user;
    }

    public static function user_registration_validation($validation_result){

        $form = $validation_result['form'];
        $config = self::get_config($form['id']);
        $pagenum = RGForms::post('gform_source_page_number_' . $form['id']);
        $entry = self::convert_post_to_entry();

        // if there is no registration feed or the registration condition is not met or feed is inactive, abandon ship
        if(!$config || !self::registration_condition_met($form, $config, $entry) || !$config['is_active'])
            return $validation_result;

        $entry = self::convert_post_to_entry();

        $username_field = RGFormsModel::get_field($form, $config['meta']['username']);
        $useremail_field = RGFormsModel::get_field($form, $config['meta']['email']);

        $username_hidden = RGFormsModel::is_field_hidden($form, $username_field, array());
        $useremail_hidden = RGFormsModel::is_field_hidden($form, $useremail_field, array());

        $user_name = apply_filters("gform_username_{$form['id']}",
            apply_filters('gform_username', self::get_meta_value('username', $config, $form, $entry), $config, $form, $entry),
            $config, $form, $entry );
        $user_email = self::get_prepared_value($useremail_field, $config['meta']['email'], $entry);
        $user_pass = RGForms::post('input_' . $config['meta']['password']);

        if(!function_exists('username_exists'))
            require_once(ABSPATH . WPINC . "/registration.php");

        $username_exists = username_exists($user_name); // check sanitized username
        $email_exists = email_exists($user_email);

        // if multisite is defined and true, lowercase name for validation
        if(is_multisite()) {
            $user_name = strtolower($user_name);
            $_POST['input_' . str_replace('.', '_', $config['meta']['username'])] = $user_name;
        }

        // if user name is not hidden and is on the current page we are validating, validate it
        if(!$username_hidden && $username_field['pageNumber'] == $pagenum) {
            if($username_exists)
                $form = self::add_validation_failure($config['meta']['username'], $form, __('This username is already registered', 'gravityformsuserregistration') );

            if(!validate_username($user_name))
                $form = self::add_validation_failure($config['meta']['username'], $form, __('The username can only contain alphanumeric characters (A-Z, 0-9), underscores, dashes and spaces', 'gravityformsuserregistration') );

            if(self::is_bp_active() && strpos($user_name, " ") !== false)
                $form = self::add_validation_failure($config['meta']['username'], $form, __('The username can only contain alphanumeric characters (A-Z, 0-9), underscores and dashes', 'gravityformsuserregistration') );

            if(!$user_name)
                $form = self::add_validation_failure($config['meta']['username'], $form, __('The username can not be empty', 'gravityformsuserregistration') );
        }

        // if user email is not hidden and is on the current page we are validating, validate it
        if(!$useremail_hidden && $useremail_field['pageNumber'] == $pagenum) {
            if($email_exists)
                $form = self::add_validation_failure($config['meta']['email'], $form, __('This email address is already registered', 'gravityformsuserregistration') );

            if(!$user_email)
                $form = self::add_validation_failure($config['meta']['email'], $form, __('The email address can not be empty', 'gravityformsuserregistration') );
        }

        if(strpos($user_pass, "\\") !== false)
            $form = self::add_validation_failure($config['meta']['password'], $form, __('Passwords may not contain the character "\"', 'gravityformsuserregistration') );

        $form = apply_filters('gform_user_registration_validation', $form, $config, $pagenum);

        $validation_result["is_valid"] = self::is_form_valid($form);
        $validation_result["form"] = $form;

        return $validation_result;
    }

    private static function get_prepared_value($field, $input_id, $entry, $is_username = false){

        $space = (self::is_bp_active() && $is_username) ? '' : ' ';

        switch(RGFormsModel::get_input_type($field)){
            case "name":
                if(strpos($input_id, '.') === false){
                    $prefix = trim(rgar($entry, "{$input_id}.2"));
                    $first = trim(rgar($entry, "{$input_id}.3"));
                    $last = trim(rgar($entry, "{$input_id}.6"));
                    $suffix = trim(rgar($entry, "{$input_id}.8"));

                    $name = $prefix;
                    $name .= !empty($name) && !empty($first) ? $space . $first : $first;
                    $name .= !empty($name) && !empty($last) ? $space . $last : $last;
                    $name .= !empty($name) && !empty($suffix) ? $space . $suffix : $suffix;

                    return $name;
                }
                else{
                    return rgar($entry, $input_id);
                }
            break;

            case "address" :
                if(strpos($input_id, '.') === false){
                    $street_value = trim(rgar($entry, "{$input_id}.1"));
                    $street2_value = trim(rgar($entry, "{$input_id}.2"));
                    $city_value = trim(rgar($entry, "{$input_id}.3"));
                    $state_value = trim(rgar($entry, "{$input_id}.4"));
                    $zip_value = trim(rgar($entry, "{$input_id}.5"));
                    $country_value = trim(rgar($entry, "{$input_id}.6"));

                    $address = $street_value;
                    $address .= !empty($address) && !empty($street2_value) ? ", $street2_value" : $street2_value;
                    $address .= !empty($address) && (!empty($city_value) || !empty($state_value)) ? ", $city_value" : $city_value;
                    $address .= !empty($address) && !empty($city_value) && !empty($state_value) ? ", $state_value" : $state_value;
                    $address .= !empty($address) && !empty($zip_value) ? " $zip_value" : $zip_value;
                    $address .= !empty($address) && !empty($country_value) ? " $country_value" : $country_value;

                    return $address;
                }
                else{
                    return rgar($entry, $input_id);
                }
            break;
            default:
                return rgar($entry, $input_id);
            break;
        }

    }

    // simulates entry format for inputs
    private static function convert_post_to_entry(){

        $entry = array();

        foreach($_POST as $key => $value){

            $id = str_replace('_', '.', str_replace('input_', '', $key));
            $entry[$id] = $value;

        }

        return $entry;
    }

    public static function add_validation_failure($field_id, $form, $message = "This field does not validate.") {

        if(is_numeric($field_id))
            $field_id = intval($field_id);

        for($i = 0; $i < count($form['fields']); $i++ ) {
            if($form['fields'][$i]['id'] == $field_id) {
                $form["fields"][$i]["failed_validation"] = true;
                $form["fields"][$i]["validation_message"] = apply_filters('gform_user_registration_validation_message', $message, $form);
            }
        }

        return $form;
    }

    private static function is_form_valid($form) {

        foreach($form['fields'] as $field) {
            if($field['failed_validation'] == true) {
                return false;
            }
        }

        return true;
    }

    private static function get_entry_value($field_id, $entry, $name_fields){
        foreach($name_fields as $name_field){
            if($field_id == $name_field["id"]){
                $value = RGFormsModel::get_lead_field_value($entry, $name_field);
                return GFCommon::get_lead_field_display($name_field, $value);
            }
        }

        return $entry[$field_id];
    }

    public static function attribute_post_author($user_id = 71, $post_id = 42) {

        $post = get_post($post_id);

        if(empty($post))
            return;

        $post->post_author = $user_id;

        wp_update_post($post);

    }

    // Hook into GF PayPal Plugin

    public static function add_paypal_user_registration_options($config, $form) {
        global $wp_roles;

        // activate user registration tooltips for integration with PayPal plugin
        add_filter('gform_tooltips', array('GFUser', 'tooltips'));

        $id = rgget('id');

        $registration_config = self::get_config(rgar($config, 'form_id'));
        $registration_feeds = GFUserData::get_feeds();
        $registration_forms = array();

        foreach($registration_feeds as $feed) {
            $registration_forms[] = $feed['form_id'];
        }

        $json_registration_forms = GFCommon::json_encode($registration_forms);

        if(empty($json_registration_forms))
            $json_registration_forms = '[]';

        $roles = array_keys($wp_roles->roles);
        $display_registration_options = !empty($registration_config) ? '' : 'display:none;';
        $display_multisite_options = (is_multisite() && self::is_root_site() && $config['meta']['type'] == 'subscription') ? '' : 'display:none;';

        ?>

        <script type="text/javascript">
            jQuery(document).ready(function($){
                $(document).bind('paypalFormSelected', function(event, form) {

                    var registration_form_ids = <?php echo $json_registration_forms; ?>;
                    var transaction_type = $("#gf_paypal_type").val();
                    var form = form;
                    var has_registration = false;
                    var display_multisite_options = <?php echo (is_multisite() && self::is_root_site()) ? 'true' : 'false' ?>;

                    if($.inArray(String(form['id']), registration_form_ids) != -1)
                        has_registration = true;

                    if(has_registration == true) {
                        $("#gf_paypal_user_registration_options").show();
                    } else {
                        $("#gf_paypal_user_registration_options").hide();
                    }

                    $("#gf_paypal_update_user_option, #gf_paypal_update_site_option").hide();

                    if(transaction_type == "subscription")
                        $("#gf_paypal_update_user_option").show();

                    if(transaction_type == "subscription" && display_multisite_options)
                        $("#gf_paypal_update_site_option").show();

                });
            });
        </script>

        <div id="gf_paypal_user_registration_options" class="margin_vertical_10" style="<?php echo $display_registration_options; ?>">
            <label class="left_header"><?php _e("User Registration", "gravityformsuserregistration"); ?> <?php gform_tooltip("user_registration_paypal_user_options") ?></label>

            <ul style="overflow:hidden;">

                <!-- Standard Options -->

                <li>
                    <input type="checkbox" name="gf_paypal_delay_registration" id="gf_paypal_delay_registration" value="1" <?php echo rgar($config['meta'], 'delay_registration') ? "checked='checked'" : ""?> />
                    <label class="inline" for="gf_paypal_delay_registration">
                        <?php
                        if(!is_multisite()){
                            _e("Register user only when a payment is received.", "gravityformsuserregistration");
                        } else {
                            _e("Register user and create site only when a payment is received.", "gravityformsuserregistration");
                        }
                        ?>
                    </label>
                </li>

                <li id="gf_paypal_update_user_option" <?php echo rgars($config,"meta/type") == "subscription" ? "" : "style='display:none;'" ?>>
                    <input type="checkbox" name="gf_paypal_update_user" id="gf_paypal_update_user" value="1" <?php echo rgars($config, "meta/update_user_action") ? "checked='checked'" : ""?> onclick="var action = this.checked ? '<?php echo $roles[0]; ?>' : ''; jQuery('#gf_paypal_update_user_action').val(action);" />
                    <label class="inline" for="gf_paypal_update_user"><?php _e("Update <strong>user</strong> when subscription is cancelled.", "gravityformsuserregistration"); ?></label>
                    <select id="gf_paypal_update_user_action" name="gf_paypal_update_user_action" onchange="var checked = jQuery(this).val() ? 'checked' : false; jQuery('#gf_paypal_update_user').attr('checked', checked);">
                        <option value=""></option>
                        <?php foreach($roles as $role) {
                            $role_name = ucfirst($role);
                            ?>
                            <option value="<?php echo $role ?>" <?php echo rgars($config, "meta/update_user_action") == $role ? "selected='selected'" : ""?>><?php echo sprintf(__("Set User as %s", "gravityformsuserregistration"), $role_name); ?></option>
                        <?php } ?>
                    </select>
                </li>

                <!-- Multisite Options -->

                <li id="gf_paypal_update_site_option" style="<?php echo $display_multisite_options; ?>">
                    <input type="checkbox" name="gf_paypal_update_site" id="gf_paypal_update_site" value="1" <?php echo $config["meta"]["update_site_action"] ? "checked='checked'" : ""?> onclick="var action = this.checked ? 'deactivate' : ''; jQuery('#gf_paypal_update_site_action').val(action);" />
                    <label class="inline" for="gf_paypal_update_site"><?php _e("Update <strong>site</strong> when subscription is cancelled.", "gravityformsuserregistration"); ?></label>
                    <select id="gf_paypal_update_site_action" name="gf_paypal_update_site_action" onchange="var checked = jQuery(this).val() ? 'checked' : false; jQuery('#gf_paypal_update_site').attr('checked', checked);">
                        <option value=""></option>
                        <?php $site_options = array('deactivate' => __('Deactivate', 'gravityformsuserregistration'), 'delete' => __('Delete', 'gravityformsuserregistration') ); ?>
                        <?php foreach($site_options as $option_key => $option_label) { ?>
                            <option value="<?php echo $option_key; ?>" <?php echo $config["meta"]["update_site_action"] == $option_key ? "selected='selected'" : ""?>><?php echo sprintf(__("%s site", "gravityformsuserregistration"), $option_key); ?></option>
                        <?php } ?>
                    </select>
                </li>

            </ul>
        </div>

        <?php

    }

    public static function save_paypal_user_config($config) {

        $config["meta"]["update_user_action"] = RGForms::post("gf_paypal_update_user_action");
        $config["meta"]["delay_registration"] = RGForms::post("gf_paypal_delay_registration");
        $config["meta"]["update_site_action"] = RGForms::post("gf_paypal_update_site_action"); // multisite option

        return $config;
    }

    public static function add_paypal_user($entry, $config, $transaction_id, $amount) {

        $form = RGFormsModel::get_form_meta($entry['form_id']);
        self::gf_create_user($entry, $form, true);

    }

    public static function downgrade_paypal_user($entry, $config, $transaction_id) {

        $paypal_config = self::get_paypal_config($entry['form_id'], $entry);

        if(!$paypal_config || !$paypal_config['meta']['update_user_action'])
            return;

        $user = GFUserData::get_user_by_entry_id($entry['id']);
        $user->set_role($paypal_config['meta']['update_user_action']);

    }

    public static function downgrade_paypal_site($entry, $config) {
        global $current_site;

        $action = $config['meta']['update_site_action'];

        if(!$action)
            return;

        $site_id = GFUserData::get_site_by_entry_id($entry['id']);

        if(!$site_id)
            return;

        switch($action){
        case 'deactivate':
            do_action('deactivate_blog', $site_id);
            update_blog_status($site_id, 'deleted', '1');
            break;
        case 'delete':
            require_once(ABSPATH . 'wp-admin/includes/ms.php');
            if($site_id != '0' && $site_id != $current_site->blog_id)
                wpmu_delete_blog($site_id, true);
            break;
        }

    }

    // More Functions...

    public static function add_permissions(){
        global $wp_roles;
        $wp_roles->add_cap("administrator", "gravityforms_user_registration");
        $wp_roles->add_cap("administrator", "gravityforms_user_registration_uninstall");
    }

    // Target of Member plugin filter. Provides the plugin with Gravity Forms lists of capabilities
    public static function members_get_capabilities( $caps ) {
        return array_merge($caps, array("gravityforms_user_registration", "gravityforms_user_registration_uninstall"));
    }

    public static function get_config($form_id){
        if(!class_exists("GFUserData"))
            require_once(self::get_base_path() . "/data.php");

        //Getting user_registration settings associated with this transaction
        $config = GFUserData::get_feed_by_form($form_id);

        //Ignore IPN messages from forms that are no longer configured with the PayPal add-on
        if(!$config)
            return false;

        return $config[0]; //only one feed per form is supported
    }

    public static function uninstall(){

        //loading data lib
        require_once(self::get_base_path() . "/data.php");

        if(!self::has_access("gravityforms_user_registration_uninstall"))
            die(__("You don't have adequate permission to uninstall the User Registration Add-On.", "gravityformsuserregistration"));

        //droping all tables
        GFUserData::drop_tables();

        //removing options
        delete_option("gf_user_registration_version");

        //Deactivating plugin
        $plugin = "gravityformsuserregistration/userregistration.php";
        deactivate_plugins($plugin);
        update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
    }

    private static function is_gravityforms_installed(){
        return class_exists("RGForms");
    }

    private static function is_gravityforms_supported(){
        if(class_exists("GFCommon")){
            $is_correct_version = version_compare(GFCommon::$version, self::$min_gravityforms_version, ">=");
            return $is_correct_version;
        }
        else{
            return false;
        }
    }

    protected static function has_access($required_permission){
        $has_members_plugin = function_exists('members_get_capabilities');
        $has_access = $has_members_plugin ? current_user_can($required_permission) : current_user_can("level_7");
        if($has_access)
            return $has_members_plugin ? $required_permission : "level_7";
        else
            return false;
    }

    private static function get_form_fields($form){
        $fields = array();

        if(is_array(rgar($form, 'fields'))){
            foreach($form["fields"] as $field){

                if(is_array($field["inputs"]) && !empty($field["inputs"])){

                    if(RGFormsModel::get_input_type($field) == "address" || RGFormsModel::get_input_type($field) == "name")
                        $fields[] =  array($field["id"], GFCommon::get_label($field) . " (" . __("Full" , "gravityformsuserregistration") . ")");

                    foreach($field["inputs"] as $input)
                        $fields[] =  array($input["id"], GFCommon::get_label($field, $input["id"]));

                }
                //else if(!$field["displayOnly"]){
                else if(!rgar($field, 'displayOnly')){
                    $fields[] =  array($field["id"], GFCommon::get_label($field));
                }

            }
        }
        return $fields;
    }

    private static function get_fields_by_type($form, $types){
        $fields = array();

        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){

                // if not a field type we want, skip it
                if(is_array($types)) {
                    if(!in_array($field["type"], $types) && !in_array($field['inputType'], $types))
                        continue;
                } else {
                    if($field["type"] != $types && rgar($field,'inputType') != $types)
                        continue;
                }

                if(is_array($field["inputs"])){

                    foreach($field["inputs"] as $input)
                        $fields[] =  array($input["id"], GFCommon::get_label($field, $input["id"]));
                }
                else {
                    $fields[] =  array($field["id"], GFCommon::get_label($field));
                }

            }
        }
        return $fields;
    }

    private static function get_bp_gform_fields($form){
        $fields = array();

        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){

                if(is_array($field["inputs"]) && !empty($field["inputs"])){

                    if(RGFormsModel::get_input_type($field) == "checkbox") {
                        $fields[] =  array($field["id"], GFCommon::get_label($field));
                        continue;
                    }

                    if(RGFormsModel::get_input_type($field) == "address" || RGFormsModel::get_input_type($field) == "name")
                        $fields[] =  array($field["id"], GFCommon::get_label($field) . " (" . __("Full" , "gravityformsuserregistration") . ")");

                    foreach($field["inputs"] as $input)
                        $fields[] =  array($input["id"], GFCommon::get_label($field, $input["id"]));

                }
                //else if(!$field["displayOnly"]){
                else if(!rgar($field, 'displayOnly')){
                    $fields[] =  array($field["id"], GFCommon::get_label($field));
                }

            }
        }

        return $fields;
    }

    private static function is_user_registration_page(){
        $current_page = trim(strtolower(RGForms::get("page")));
        return in_array($current_page, array("gf_user_registration"));
    }

    //Returns the url of the plugin's root folder
    protected function get_base_url(){
        return plugins_url(null, __FILE__);
    }

    //Returns the physical path of the plugin's root folder
    protected function get_base_path(){
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . "/" . $folder;
    }

    public static function print_rr($array) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    public static function add_buddypress_hooks() {

        if(is_admin()){

            // buddypress admin hooks
            add_action("gform_user_registration_add_option_section", array("GFUser", "add_buddypress_options"), 10, 2);
            add_filter("gform_user_registration_save_config", array("GFUser", "save_buddypress_meta"));

        } else {

            // buddypress non-admin hooks
            add_action("gform_user_registered", array("GFUser", "prepare_buddypress_data"), 10, 3);
            add_action("gform_user_registered", array("GFUser", "bp_user_signup"));

        }

    }

    public static function add_multisite_hooks() {

        if(is_admin()){

            // multisite admin hooks
            add_action("gform_user_registration_add_option_section", array("GFUser", "add_multisite_section"), 10, 3);
            add_filter("gform_user_registration_save_config", array("GFUser", "save_multisite_config"));
            add_filter("gform_user_registration_config_validation", array("GFUser", "validate_multisite_config"), 10, 2);

        } else {

            // multisite non-admin hooks
            add_action("gform_user_registration_validation", array("GFUser", "validate_multisite_submission"), 10, 3);
            add_action("gform_user_registered", array("GFUser", "create_new_multisite"), 10, 4);

            // add paypal ipn hooks for multisite
            add_action("gform_subscription_canceled", array("GFUser", "downgrade_paypal_site"), 10, 2);

        }

    }

    private static function is_bp_active() {
        return defined('BP_VERSION') ? true : false;
    }

    /**
     * Redirect to the custom registration page as specified in the User Registration settings.
     *
     * By default, this function checks if the user is accessing the default WP registration page
     * "/wp-login.php?action=register" and if so, processes the redirect.
     *
     * If BuddyPress is active, it checks if the current page is the the BP registration page
     * (as specified in the BP Page settings) and if so, processes the redirect. We also check
     * to ensure that the User Registration Custom Registration Page ID is not the same as the
     * BP Register Page ID.
     *
     */
    public static function custom_registration_page() {
        global $bp;

        $script_name = substr($_SERVER['SCRIPT_NAME'], -12, 12); // get last 12 characters of script name (we want wp-login.php);
        $action = rgget('action');
        $redirect = false;

        // if BP is active and this is the registration page, redirect
        if(self::is_bp_active() && bp_is_register_page())
            $redirect = true;

        // if "wp-login.php?action=register", aka default WP registration page
        if($script_name == 'wp-login.php' && $action == 'register')
            $redirect = true;

        if(!$redirect)
            return;

        $ur_settings = get_option('gf_userregistration_settings');
        $reg_page_id = rgar($ur_settings, 'custom_reg_page');

        if(empty($ur_settings) || !rgar($ur_settings, 'enable_custom_reg_page'))
            return;

        // if BP is active, BP Register Page is set and BP Register Page ID is the same as the UR Register Page ID, cancel redirect
        if(self::is_bp_active() && isset($bp->pages->register->id) && $bp->pages->register->id == $reg_page_id)
            return;

        wp_redirect(get_permalink($reg_page_id));
        exit;
    }

    public static function get_user_id_by_meta($key, $value) {
        global $wpdb;

        $table_name = $wpdb->prefix . "usermeta";
        $user = $wpdb->get_var($wpdb->prepare("select user_id from $table_name where meta_key = %s && meta_value = %s", $key, $value));

        return !empty($user) ? $user : false;
    }

    private static function not_empty($value) {
        return $value;
    }

    public static function display_role_dropdown_options($selected_role, $no_role_option = false) {

        wp_dropdown_roles($selected_role);

        if(!is_multisite() || !$no_role_option)
            return;

        $selected = !$selected_role ? 'selected="selected"' : '';

        echo '<option value="" ' . $selected . '>' . __('&mdash; No role for this site &mdash;') . '</option>';

    }

    /*
     * This function will check to ensure that the current install is a Network before
     * returning a UR Network meta value
     *
     */
    public static function get_network_meta_value($meta_key, $config) {

        if(!is_multisite());
            return false;

        $network_meta = rgars($config, 'meta/multisite_options');

        return rgar($network_meta, $meta_key);
    }

}



if(!function_exists("rgget")){
function rgget($name, $array=null){
    if(!isset($array))
        $array = $_GET;

    if(isset($array[$name]))
        return $array[$name];

    return "";
}
}

if(!function_exists("rgpost")){
function rgpost($name, $do_stripslashes=true){
    if(isset($_POST[$name]))
        return $do_stripslashes ? stripslashes_deep($_POST[$name]) : $_POST[$name];

    return "";
}
}

if(!function_exists("rgar")){
function rgar($array, $name){
    if(isset($array[$name]))
        return $array[$name];

    return '';
}
}

if(!function_exists("rgars")){
function rgars($array, $name){
    $names = explode("/", $name);
    $val = $array;
    foreach($names as $current_name){
        $val = rgar($val, $current_name);
    }
    return $val;
}
}

if(!function_exists("rgempty")){
function rgempty($name, $array = null){
    if(!$array)
        $array = $_POST;

    $val = rgget($name, $array);
    return empty($val);
}
}


if(!function_exists("rgblank")){
function rgblank($text){
    return empty($text) && strval($text) != "0";
}
}


if(!function_exists("rgobj")){
function rgobj($obj, $name){
    if(isset($obj->$name))
        return $obj->$name;

    return '';
}
}
?>