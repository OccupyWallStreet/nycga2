<?php
/*
Plugin Name: Gravity Forms
Plugin URI: http://nythemes.com/gravityforms
Description: Easily create web forms and manage form entries within the WordPress admin.
Version: 1.2.2
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

require_once(RGForms::get_base_path() . "/forms_model.php");

define("GRAVITYFORMS_PAGES", "gf_edit_forms,gf_new_form,gf_entries,gf_settings,gf_export,gf_help");
define("RG_CURRENT_PAGE", basename($_SERVER['PHP_SELF']));
define("IS_ADMIN",  is_admin());
define("RG_CURRENT_VIEW", $_GET["view"]);
define("UNSUPPORTED_WP_VERSION", version_compare(get_bloginfo("version"), '2.8.0', '<'));
if(!defined("GRAVITY_MANAGER_URL"))
    define("GRAVITY_MANAGER_URL", "http://www.gravityhelp.com/wp-content/plugins/gravitymanager");

add_action('init',  array('RGForms', 'init'));

class RGForms{
    public static $tab_index = 1;
    public static $version = "1.2.2";

    public static function current_user_can_any($caps){

        if(!is_array($caps))
            return current_user_can($caps) || current_user_can("gform_full_access");

        foreach($caps as $cap){
            if(current_user_can($cap))
                return true;
        }

        return current_user_can("gform_full_access");
    }

    public static function current_user_can_which($caps){

        foreach($caps as $cap){
            if(current_user_can($cap))
                return $cap;
        }

        return "";
    }

    public static function init(){
        load_plugin_textdomain( 'gravityforms', FALSE, '/gravityforms/languages' );

        if(IS_ADMIN){

            global $current_user;

            //Members plugin integration. Adding Gravity Forms roles to the checkbox list
            if ( function_exists( 'members_get_capabilities' ) ){
                add_filter('members_get_capabilities', array("RGForms", "members_get_capabilities"));

                //Removing default GF capability when integrating with Members
                if(current_user_can("gform_full_access"))
                    $current_user->remove_cap("gform_full_access");

                //If and administrator does not have any Gravity Form permission, add all of them. (will happen the first time Gravity Forms gets installed)
                self::initialize_permissions();
            }
            else
            {
                $gform_full_access = current_user_can("administrator") ? "gform_full_access" : "";
                $gform_full_access = apply_filters("gform_cap_full_access", $gform_full_access);

                if(!empty($gform_full_access))
                    $current_user->add_cap($gform_full_access);
            }

            //Loading Gravity Forms if user has access to any functionality
            if(self::current_user_can_any(self::all_caps()))
            {
                //runs the setup when version changes
                self::setup();

                //checks if an update is available. Runs periodically
                self::periodic_check_updates();

                add_action('admin_menu',  array('RGForms', 'create_menu'));

                if(!UNSUPPORTED_WP_VERSION){
                    require_once(self::get_base_path() . "/tooltips.php");

                    add_action('admin_head',  array('RGForms', 'admin_head'));
                    add_action('admin_footer',  array('RGForms', 'add_mce_popup'));
                    add_action('admin_footer',  array('RGForms', 'check_upload_folder'));
                    add_action("admin_print_scripts", array('RGForms', 'print_scripts'));
                    add_action('wp_dashboard_setup', array('RGForms', 'dashboard_setup'));

                    //Adding "embed form" button
                    add_action('media_buttons_context', array('RGForms', 'add_form_button'));

                    //Plugin update actions
                    add_action('update_option_update_plugins', array('RGForms', 'check_update')); //for WP 2.7
                    add_action('update_option__transient_update_plugins', array('RGForms', 'check_update')); //for WP 2.8

                    if(RG_CURRENT_PAGE == "plugins.php")
                        add_action("admin_init", array('RGForms', 'check_update'));

                    add_action('after_plugin_row_gravityforms/gravityforms.php', array('RGForms', 'plugin_row') );
                    add_action('install_plugins_pre_plugin-information', array('RGForms', 'display_changelog'));
                }
            }
        }
        else{
            add_shortcode('gravityform', array('RGForms', 'parse_shortcode'));
            add_action('wp_enqueue_scripts', array('RGForms', 'enqueue_scripts'));
        }

        if(in_array(RG_CURRENT_PAGE, array("admin.php", "admin-ajax.php")) && self::current_user_can_any(self::all_caps())){
            add_action('wp_ajax_rg_save_form', array('RGForms', 'save_form'));
            add_action('wp_ajax_rg_add_field', array('RGForms', 'add_field'));
            add_action('wp_ajax_rg_change_field_type', array('RGForms', 'change_field_type'));
            add_action('wp_ajax_rg_delete_field', array('RGForms', 'delete_field'));
            add_action('wp_ajax_rg_delete_file', array('RGForms', 'delete_file'));
            add_action('wp_ajax_rg_select_export_form', array('RGForms', 'select_export_form'));
            add_action('wp_ajax_rg_start_export', array('RGForms', 'start_export'));

            //entry list ajax operations
            add_action('wp_ajax_rg_update_lead_property', array('RGForms', 'update_lead_property'));

            //form list ajax operations
            add_action('wp_ajax_rg_update_form_active', array('RGForms', 'update_form_active'));

            add_action('media_upload_rg_gform', array('RGForms', 'insert_form'));
        }
    }
    public static function initialize_permissions(){
        global $current_user;

        $is_gravity_forms_installation = get_option("rg_form_version") != self::$version;
        $is_members_installation = get_option("rg_members_installed");
        $is_admin_with_no_permissions = current_user_can("administrator") && !self::current_user_can_any(self::all_caps());

        //if this is a new gf install or members install and the administrator doesn't have any Gravity Forms permission, add all of them.
        if( ($is_gravity_forms_installation || $is_members_installation) && $is_admin_with_no_permissions){
            $role = get_role("administrator");
            foreach(self::all_caps() as $cap){
                $role->add_cap($cap);
            }
            update_option("rg_members_installed", true);
        }
    }

    public static function setup(){
        global $wpdb;

        $version = self::$version;

        if(get_option("rg_form_version") != $version){

            //fix checkbox value. needed for version 1.0 and below but won't hurt for higher versions
            self::fix_checkbox_value();

            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

            //------ FORM -----------------------------------------------
            $form_table_name = RGFormsModel::get_form_table_name();
            $sql = "CREATE TABLE " . $form_table_name . " (
                  id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
                  title VARCHAR(150) NOT NULL,
                  date_created DATETIME NOT NULL,
                  is_active TINYINT(1) NOT NULL DEFAULT 1,
                  PRIMARY KEY (id)
                )CHARSET=utf8;";
            dbDelta($sql);

            //------ META -----------------------------------------------
            $meta_table_name = RGFormsModel::get_meta_table_name();
            $sql = "CREATE TABLE " . $meta_table_name . " (
                  form_id MEDIUMINT UNSIGNED NOT NULL,
                  display_meta LONGTEXT,
                  entries_grid_meta LONGTEXT,
                  INDEX form_key (form_id),
                  FOREIGN KEY (form_id)
                    REFERENCES $form_table_name(id) ON UPDATE CASCADE ON DELETE CASCADE
                )CHARSET=utf8;";
            dbDelta($sql);

            //------ FORM VIEW -----------------------------------------------
            $form_view_table_name = RGFormsModel::get_form_view_table_name();
            $sql = "CREATE TABLE " . $form_view_table_name . " (
                  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  form_id MEDIUMINT UNSIGNED NOT NULL,
                  date_created DATETIME NOT NULL,
                  ip char(15),
                  count MEDIUMINT UNSIGNED NOT NULL DEFAULT 1,
                  INDEX form_key (form_id),
                  FOREIGN KEY (form_id) REFERENCES $form_table_name(id)
                )CHARSET=utf8;";
            dbDelta($sql);

            //------ LEAD -----------------------------------------------
            $lead_table_name = RGFormsModel::get_lead_table_name();
            $sql = "CREATE TABLE " . $lead_table_name . " (
                  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  form_id MEDIUMINT UNSIGNED NOT NULL,
                  post_id BIGINT UNSIGNED,
                  date_created DATETIME NOT NULL,
                  is_starred TINYINT(1) NOT NULL DEFAULT 0,
                  is_read TINYINT(1) NOT NULL DEFAULT 0,
                  ip char(15) NOT NULL,
                  source_url varchar(200) NOT NULL DEFAULT '',
                  user_agent varchar(250) NOT NULL DEFAULT '',
                  INDEX form_key (form_id),
                  FOREIGN KEY (form_id) REFERENCES $form_table_name(id)
                )CHARSET=utf8;";
           dbDelta($sql);

           //------ LEAD NOTES ------------------------------------------
            $lead_notes_table_name = RGFormsModel::get_lead_notes_table_name();
            $sql = "CREATE TABLE " . $lead_notes_table_name . " (
                  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  lead_id INT UNSIGNED NOT NULL,
                  user_name varchar(250),
                  user_id BIGINT,
                  date_created DATETIME NOT NULL,
                  value LONGTEXT,
                  INDEX lead_key (lead_id),
                  INDEX lead_user_key(lead_id, user_id),
                  FOREIGN KEY (lead_id) REFERENCES $lead_table_name(id) ON DELETE CASCADE
                )CHARSET=utf8;";
           dbDelta($sql);

            //------ LEAD DETAIL -----------------------------------------
            $lead_detail_table_name = RGFormsModel::get_lead_details_table_name();
            $sql = "CREATE TABLE " . $lead_detail_table_name . " (
                  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                  lead_id INT UNSIGNED NOT NULL,
                  form_id MEDIUMINT UNSIGNED NOT NULL,
                  field_number FLOAT UNSIGNED NOT NULL,
                  value varchar(". GFORMS_MAX_FIELD_LENGTH ."),
                  PRIMARY KEY (id),
                  INDEX form_key (form_id),
                  INDEX lead_key (lead_id),
                  FOREIGN KEY (lead_id) REFERENCES $lead_table_name(id) ON DELETE CASCADE,
                  FOREIGN KEY (form_id) REFERENCES $form_table_name(id) ON DELETE CASCADE
                )CHARSET=utf8;";
            dbDelta($sql);

            //------ LEAD DETAIL LONG -----------------------------------
            $lead_detail_long_table_name = RGFormsModel::get_lead_details_long_table_name();
            $sql = "CREATE TABLE " . $lead_detail_long_table_name . " (
                  lead_detail_id BIGINT UNSIGNED NOT NULL,
                  value LONGTEXT,
                  INDEX lead_detail_key (lead_detail_id),
                  FOREIGN KEY (lead_detail_id) REFERENCES $lead_detail_table_name(id) ON DELETE CASCADE
                )CHARSET=utf8;";
            dbDelta($sql);

        }
        update_option("rg_form_version", $version);
    }

    public static function fix_checkbox_value(){
        global $wpdb;

        $table_name = RGFormsModel::get_lead_details_table_name();

        $sql = "select * from $table_name where value= '!'";
        $results = $wpdb->get_results($sql);
        foreach($results as $result){
            $form = RGFormsModel::get_form_meta($result->form_id);
            $field = RGFormsModel::get_field($form, $result->field_number);
            if($field["type"] == "checkbox"){
                $input = self::get_input($field, $result->field_number);
                $wpdb->update($table_name, array("value" => $input["label"]), array("id" => $result->id));
            }
        }
    }

    function all_caps(){
        return array(   'gravityforms_edit_forms',
                        'gravityforms_delete_forms',
                        'gravityforms_create_form',
                        'gravityforms_view_entries',
                        'gravityforms_edit_entries',
                        'gravityforms_delete_entries',
                        'gravityforms_view_settings',
                        'gravityforms_edit_settings',
                        'gravityforms_export_entries',
                        'gravityforms_uninstall',
                        'gravityforms_view_entry_notes',
                        'gravityforms_edit_entry_notes'
                        );
    }

    function members_get_capabilities( $caps ) {
        return array_merge($caps, self::all_caps());
    }


    public static function check_upload_folder(){
        //check if upload folder is writable
        $folder = RGFormsModel::get_upload_root();
        if(empty($folder))
            echo "<div class='error'>Upload folder is not writable. Export and file upload features will not be functional.</div>";
    }

    public static function insert_form(){
        echo "insert form ui goes here";
    }

    public static function json_encode($value){

        if (!extension_loaded('json')){
            if (!class_exists('Services_JSON'))
                include_once(self::get_base_path() . '/json.php');

            $json = new Services_JSON();
            return $json->encode($value);
        }
        else{
            return json_encode($value);
        }
    }

    public static function json_decode($str, $is_assoc){
        if (!extension_loaded('json')){
            if (!class_exists('Services_JSON'))
                include_once(self::get_base_path() . '/json.php');

            $json = $is_assoc ? new Services_JSON(SERVICES_JSON_LOOSE_TYPE) : new Services_JSON();
            return $json->decode($str);
        }
        else{
            return json_decode($str, $is_assoc);
        }
    }

    public static function get_key(){
        return get_option("rg_gforms_key");
    }


    //Enqueues scripts based on this page's post contents
    public static function enqueue_scripts(){
        global $wp_query;
        $posts = $wp_query->get_posts();
        foreach($posts as $post){
            $forms = self::get_embedded_forms($post->post_content);
            foreach($forms as $form){
                if(!get_option('rg_gforms_disable_css'))
                    wp_enqueue_style("gforms_css", self::get_base_url() . "/css/forms.css");

                if(self::has_date_field($form)){
                    wp_enqueue_script("gforms_ui_datepicker", self::get_base_url() . "/js/jquery-ui/ui.datepicker.js", array("jquery"), false, true);
                    wp_enqueue_script("gforms_datepicker", self::get_base_url() . "/js/datepicker.js", array("gforms_ui_datepicker"), false, true);
                    return;
                }
            }
        }

    }

    private static function get_embedded_forms($post_content){
        $forms = array();
        if(preg_match_all('/\[gravityform.*?id=(\d*).*?\]/is', $post_content, $matches, PREG_SET_ORDER)){
            foreach($matches as $match){
                $form_id = $match[1];
                $forms[] = RGFormsModel::get_form_meta($form_id);
            }
        }
        return $forms;
    }

    public static function plugin_row($plugin_name){

        $key = self::get_key();
        $version_info = self::get_version_info();

        if(!$version_info["is_valid_key"]){
            $plugin_name = "gravityforms/gravityforms.php";
            $new_version = version_compare(self::$version, $version_info["version"], '<') ? __('There is a new version of Gravity Forms available.', 'gravityforms') .' <a class="thickbox" title="Gravity Forms" href="plugin-install.php?tab=plugin-information&plugin=gravityforms&TB_iframe=true&width=640&height=808">'. sprintf(__('View version %s Details', 'gravityforms'), $version_info["version"]) . '</a>. ' : '';
            echo '</tr><tr class="plugin-update-tr"><td colspan="5" class="plugin-update"><div class="update-message">' . $new_version . sprintf(__('%sPurchase Gravity Forms%s to get access to automatic upgrades and support.  Already purchased? Enter your license key on the %sSettings page%s', 'gravityforms'), '<a href="http://nythemes.com/gravityforms">', '</a>', '<a href="admin.php?page=gf_settings">' , '</a>') . '</div></td>';
        }
    }

    public static function get_remote_request_params(){
        global $wpdb;

        return sprintf("of=GravityForms&key=%s&v=%s&wp=%s&php=%s&mysql=%s", urlencode(self::get_key()), urlencode(self::$version), urlencode(get_bloginfo("version")), urlencode(phpversion()), urlencode($wpdb->db_version()));
    }

    public static function display_changelog(){
        if($_REQUEST["plugin"] != "gravityforms")
            return;

        $key = self::get_key();
        $body = "key=$key";
        $options = array('method' => 'POST', 'timeout' => 3, 'body' => $body);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'Content-Length' => strlen($body),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );

        $raw_response = wp_remote_request(GRAVITY_MANAGER_URL . "/changelog.php?" . self::get_remote_request_params(), $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            $page_text = __("Oops!! Something went wrong.<br/>Please try again or <a href='http://nythemes.com/gravityforms'>contact us</a>.", 'gravityforms');
        }
        else{
            $page_text = $raw_response['body'];
            if(substr($page_text, 0, 10) != "<!--GFM-->")
                $page_text = "";
        }
        echo stripslashes($page_text);

        exit;
    }


    public static function get_version_info(){
        //Getting version number
        $key = self::get_key();
        $body = "key=$key";
        $options = array('method' => 'POST', 'timeout' => 3, 'body' => $body);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'Content-Length' => strlen($body),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );

        $raw_response = wp_remote_request(GRAVITY_MANAGER_URL . "/version.php?" . self::get_remote_request_params(), $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code'])
            return -1;
        else
        {
            $ary = explode("||", $raw_response['body']);
            return array("is_valid_key" => $ary[0], "version" => $ary[1], "url" => $ary[2]);
        }
    }
    private static function get_remote_message(){
        return stripslashes(get_option("rg_gforms_message"));
    }

    public static function cache_remote_message(){
        //Getting version number
        $key = self::get_key();
        $body = "key=$key";
        $options = array('method' => 'POST', 'timeout' => 3, 'body' => $body);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'Content-Length' => strlen($body),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );

        $request_url = GRAVITY_MANAGER_URL . "/message.php?" . self::get_remote_request_params();
        $raw_response = wp_remote_request($request_url, $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code'] )
            $message = "";
        else
            $message = $raw_response['body'];

        //validating that message is a valid Gravity Form message. If message is invalid, don't display anything
        if(substr($message, 0, 10) != "<!--GFM-->")
            $message = "";

        update_option("rg_gforms_message", $message);
    }

    public static $_has_set_transient;
    public static $_has_set_option;

    public static function periodic_check_updates(){
        $last_update_check = get_option("rg_gform_last_update");
        $twelve_hours = 43200;
        if(empty($last_update_check) || (time() - $last_update_check) > $twelve_hours){
            RGForms::check_update();
            RGForms::cache_remote_message();
            update_option("rg_gform_last_update", time());
        }
    }

    public static function check_update(){

        //only check updates on the admin side
        if(!IS_ADMIN)
            return;

        $plugin_name = "gravityforms/gravityforms.php";
        $option = function_exists('get_transient') ? get_transient("update_plugins") : get_option("update_plugins");
        $gravity_option = $option->response[$plugin_name];

        if(empty($gravity_option))
            $option->response[$plugin_name] = new stdClass();

        //Getting version number
        $version_info = self::get_version_info();

        if ($response == -1)
            return;

        //Empty response means that the key is invalid. Do not queue for upgrade
        if(!$version_info["is_valid_key"] || version_compare(self::$version, $version_info["version"], '>=')){
            unset($option->response[$plugin_name]);
        }
        else{
            $option->response[$plugin_name]->url = "http://nythemes.com/gravityforms";
            $option->response[$plugin_name]->slug = "gravityforms";
            $option->response[$plugin_name]->package = str_replace("{KEY}", self::get_key(), $version_info["url"]);
            $option->response[$plugin_name]->new_version = $version_info["version"];
            $option->response[$plugin_name]->id = "0";
        }

        //Setting transient data (WP 2.8)
        if ( function_exists('set_transient') && !self::$_has_set_transient){
            self::$_has_set_transient = true;
            set_transient("update_plugins", $option);
        }

        //Setting option (WP 2.7)
        if(!self::$_has_set_option){
            self::$_has_set_option = true;
            update_option("update_plugins", $option);
        }

    }

    public static function dashboard_setup(){
        wp_add_dashboard_widget('rg_forms_dashboard', 'Gravity Forms',  array('RGForms', 'dashboard'));
    }

    public static function format_date($datetime, $is_human = true){
        if(empty($datetime))
            return "";

        //adjusting date to local configured Time Zone
        $local_time = strtotime($datetime) + (get_option( 'gmt_offset' ) * 3600 );
        if($is_human){
            $lead_time = mysql2date("G", $datetime);
            $time_diff = time() - $lead_time;

            if ( $time_diff > 0 && $time_diff < 24*60*60 )
                $date_display = sprintf( __('%s ago', 'gravityforms'), human_time_diff( $lead_time) );
            else
                $date_display = sprintf(__('%1$s at %2$s', 'gravityforms'), date_i18n(get_option('date_format'), $local_time), date_i18n(get_option('time_format'), $local_time));
        }
        else{
            $date_display = sprintf(__('%1$s at %2$s', 'gravityforms'), date_i18n(get_option('date_format'), $local_time), date_i18n(get_option('time_format'), $local_time));
        }

        return $date_display;
    }

    public static function dashboard(){
        $forms = RGFormsModel::get_form_summary();

        if(sizeof($forms) > 0){
            ?>
            <table class="widefat fixed" cellspacing="0" style="border:0px;">
                <thead>
                    <td style="text-align:left; padding:8px 0!important; font-weight:bold;"><i>Form Name</i></th>
                    <td style="text-align:center; padding:8px 0!important; font-weight:bold;"><i>Unread Entries</i></th>
                    <td style="text-align:left; padding:8px 0!important; font-weight:bold;"><i>Last Entry</i></th>
                </thead>

                <tbody class="list:user user-list">
                    <?php
                    foreach($forms as $form){
                        $date_display = self::format_date($form["last_lead_date"]);

                        ?>
                        <tr class='author-self status-inherit' valign="top">
                            <td class="column-title" style="padding:8px 0;">
                                <a style="display:inline;white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis; <?php echo  $form["unread_count"] > 0 ? "font-weight:bold;" : "" ?>" href="admin.php?page=gf_entries&view=entries&id=<?php echo absint($form["id"]) ?>" title="<?php echo self::escape_text($form["title"]) ?> : <?php _e("View All Entries", "gravityforms") ?>"><?php echo self::escape_text($form["title"]) ?></a>
                            </td>
                            <td class="column-date" style="padding:8px 0; text-align:center;"><a style="<?php echo $form["unread_count"] > 0 ? "font-weight:bold;" : "" ?>" href="admin.php?page=gf_entries&view=entries&id=<?php echo absint($form["id"]) ?>" title="<?php _e("View Unread Entries", "gravityforms") ?>"><?php echo absint($form["unread_count"]) ?></a></td>
                            <td class="column-date" style="padding-top:7px;"><?php echo self::escape_text($date_display) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <p class="textright">
			<a class="button" href="admin.php?page=gf_edit_forms">View All Forms</a>
		  </p>
            <?php
        }
        else{
            ?>
            <div>
                <?php _e(sprintf("You don't have any forms. Let's go %s create one %s!", '<a href="admin.php?page=gf_new_form">', '</a>'), 'gravityforms'); ?>
            </div>
            <?php
        }
    }

    public static function add_form_button($context){
        $image_btn = self::get_base_url() . "/images/form-button.png";
        $out = '<a href="#TB_inline?width=450&inlineId=select_form" class="thickbox" title="' . __("Add Gravity Form", 'gravityforms') . '"><img src="'.$image_btn.'" alt="' . __("Add Gravity Form", 'gravityform') . '" /></a>';
        return $context . $out;
    }

    function add_mce_popup(){
        ?>
        <script>
            function InsertForm(){
                var form_id = jQuery("#add_form_id").val();
                if(form_id == ""){
                    alert("<?php _e("Please select a form", "gravityforms") ?>");
                    return;
                }

                var form_name = jQuery("#add_form_id option[value='" + form_id + "']").text().replace(" ", "");
                var display_title = jQuery("#display_title").is(":checked");
                var display_description = jQuery("#display_description").is(":checked");
                var title_qs = !display_title ? " title=false" : "";
                var description_qs = !display_description ? " description=false" : "";

                var win = window.dialogArguments || opener || parent || top;
                win.send_to_editor("[gravityform id=" + form_id + " name=" + form_name + title_qs + description_qs +"]");
            }
        </script>

        <div id="select_form" style="display:none;">
            <div class="wrap">
                <div>
                    <div style="padding:15px 15px 0 15px;">
                        <h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e("Insert A Form", "gravityforms"); ?></h3>
                        <span>
                            <?php _e("Select a form below to add it to your post or page.", "gravityforms"); ?>
                        </span>
                    </div>
                    <div style="padding:15px 15px 0 15px;">
                        <select id="add_form_id">
                            <option value="">  <?php _e("Select a Form", "gravityforms"); ?>  </option>
                            <?php
                                $forms = RGFormsModel::get_forms(1);
                                foreach($forms as $form){
                                    ?>
                                    <option value="<?php echo absint($form->id) ?>"><?php echo self::escape_text($form->title) ?></option>
                                    <?php
                                }
                            ?>
                        </select> <br/>
                        <div style="padding:8px 0 0 0; font-size:11px; font-style:italic; color:#5A5A5A"><?php _e("Can't find your form? Make sure it is active.", "gravityforms"); ?></div>
                    </div>
                    <div style="padding:15px 15px 0 15px;">
                        <input type="checkbox" id="display_title" checked='checked' /> <label for="display_title"><?php _e("display form title", "gravityforms"); ?></label> &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" id="display_description" checked='checked' /> <label for="display_description"><?php _e("display form description", "gravityforms"); ?></label>
                    </div>
                    <div style="padding:15px;">
                        <input type="button" class="button-primary" value="Insert Form" onclick="InsertForm();"/>&nbsp;&nbsp;&nbsp;
					<a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "gravityforms"); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }

    public static function print_scripts(){
        if(self::is_gravity_page()){
            wp_print_scripts(array( 'sack' ));
        }
    }

    public static function admin_head(){
        if(self::is_gravity_page()){
            ?>
            <script type="text/javascript" src="<?php echo self::get_base_url() ?>/js/jquery.qtip-1.0.0-rc2.min.js"></script>
            <script type="text/javascript" src="<?php echo self::get_base_url() ?>/js/qtip_init.js"></script>
            <?php
        }
    }

    private static function is_gravity_page(){
        $current_page = trim(strtolower($_GET["page"]));
        $gf_pages = split(",", trim(strtolower(GRAVITYFORMS_PAGES)));

        return in_array($current_page, $gf_pages);
    }

    public static function create_menu(){

        $has_full_access = current_user_can("gform_full_access");
        $min_cap = self::current_user_can_which(self::all_caps());
        if(empty($min_cap))
            $min_cap = "gform_full_access";

        $parent_menu = self::get_parent_menu();

        // Add a top-level left nav
        add_object_page(__('Forms', "gravityforms"), __("Forms", "gravityforms"), $has_full_access ? "gform_full_access" : $min_cap, $parent_menu["name"] , $parent_menu["callback"], self::get_base_url() . '/images/gravity-admin-icon.png');

        // Adding submenu pages
        add_submenu_page($parent_menu["name"], __("Edit Forms", "gravityforms"), __("Edit Forms", "gravityforms"), $has_full_access ? "gform_full_access" : "gravityforms_edit_forms", "gf_edit_forms", array("RGForms", "forms"));

        add_submenu_page($parent_menu["name"], __("New Form", "gravityforms"), __("New Form", "gravityforms"), $has_full_access ? "gform_full_access" : "gravityforms_create_form", "gf_new_form", array("RGForms", "new_form"));

        add_submenu_page($parent_menu["name"], __("Entries", "gravityforms"), __("Entries", "gravityforms"), $has_full_access ? "gform_full_access" : "gravityforms_view_entries", "gf_entries", array("RGForms", "all_leads_page"));

        add_submenu_page($parent_menu["name"], __("Settings", "gravityforms"), __("Settings", "gravityforms"), $has_full_access ? "gform_full_access" : "gravityforms_view_settings", "gf_settings", array("RGForms", "settings_page"));

        add_submenu_page($parent_menu["name"], __("Export", "gravityforms"), __("Export", "gravityforms"), $has_full_access ? "gform_full_access" : "gravityforms_export_entries", "gf_export", array("RGForms", "export_page"));

        add_submenu_page($parent_menu["name"], __("Help", "gravityforms"), __("Help", "gravityforms"), $has_full_access ? "gform_full_access" : $min_cap, "gf_help", array("RGForms", "help_page"));

    }

    private static function get_parent_menu(){

        if(self::current_user_can_any("gravityforms_edit_forms"))
            $parent = array("name" => "gf_edit_forms", "callback" => array("RGForms", "forms"));

        else if(self::current_user_can_any("gravityforms_create_form"))
            $parent = array("name" => "gf_new_form", "callback" => array("RGForms", "new_form"));

        else if(self::current_user_can_any("gravityforms_view_entries"))
            $parent = array("name" => "gf_entries", "callback" => array("RGForms", "all_leads_page"));

        else if(self::current_user_can_any("gravityforms_view_settings"))
            $parent = array("name" => "gf_settings", "callback" => array("RGForms", "settings_page"));

        else if(self::current_user_can_any("gravityforms_export_entries"))
            $parent = array("name" => "gf_export", "callback" => array("RGForms", "export_page"));

        else if(self::current_user_can_any(self::all_caps()))
            $parent = array("name" => "gf_help", "callback" => array("RGForms", "help_page"));

        return $parent;
    }

    public static function all_leads_page(){
        if(!self::ensure_wp_version())
            return;

        $forms = RGFormsModel::get_forms(true);
        $id = $_GET["id"];

        if(sizeof($forms) == 0)
        {
            ?>
            <div style="margin:50px 0 0 10px;">
                <?php _e(sprintf("You don't have any forms. Let's go %screate one%s", '<a href="?page=gravityforms.php&id=0">', '</a>'), "gravityforms"); ?>
            </div>
            <?php
        }
        else{
            if(empty($id))
                $id = $forms[0]->id;

            self::leads_page($id);
        }
    }

    public static function start_export(){
        check_ajax_referer("rg_start_export", "rg_start_export");
        $form_id=$_POST["form_id"];
        $fields = explode(",", $_POST["fields"]);
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];

        $form = RGFormsModel::get_form_meta($form_id);
        //adding default fields
        array_push($form["fields"],array("id" => "date_created" , "label" => __("Entry Date", "gravityforms")));
        array_push($form["fields"],array("id" => "ip" , "label" => __("User IP", "gravityforms")));
        array_push($form["fields"],array("id" => "source_url" , "label" => __("Source Url", "gravityforms")));

        $entry_count = RGFormsModel::get_lead_count($form_id, "", null, null, $start_date, $end_date);
        $page_size = 2;
        $offset = 0;

        $upload_dir = RGFormsModel::get_upload_path($form_id);

        if(!wp_mkdir_p($upload_dir))
            die("EndExport($form_id, false, " . __('Could not create export folder. Make sure your /wp-content/uploads folder is writable', "gravityforms") . ")");

        $file_name = "export_" . time() . ".csv";
        $file_path = $upload_dir . "/$file_name";
        $fp = fopen($file_path, "w");

        //writing header
        foreach($fields as $field_id){
            $field = RGFormsModel::get_field($form, $field_id);
            $value = '"' . str_replace('"', '""', self::get_label($field, $field_id)) . '"';
            $lines .= "$value,";
        }
        $lines = substr($lines, 0, strlen($lines)-1) . "\n";

        //paging through results for memory issues
        while($entry_count > 0){
            $leads = RGFormsModel::get_leads($form_id,"date_created", "DESC", "", $offset, $page_size, null, null, false, $start_date, $end_date);

            foreach($leads as $lead){
                foreach($fields as $field_id){
                    $value = $lead[$field_id];
                    $lines .= '"' . str_replace('"', '""', $value) . '",';
                }
                $lines = substr($lines, 0, strlen($lines)-1);
                $lines.= "\n";
            }

            $offset += $page_size;
            $entry_count -= $page_size;

            //writing to output
            fwrite($fp, $lines);
            $lines = "";
        }
        fclose($fp);

        die("EndExport($form_id, true, '$file_name');");
    }


    public static function delete_directory($dir)
    {

        if(!file_exists($dir))
            return;

        if ($handle = opendir($dir))
        {
            $array = array();

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {

                    if(is_dir($dir.$file))
                    {
                        if(!@rmdir($dir.$file)) // Empty directory? Remove it
                        {
                            self::delete_directory($dir.$file.'/'); // Not empty? Delete the files inside it
                        }
                    }
                    else
                    {
                       @unlink($dir.$file);
                    }
                }
            }
            closedir($handle);

            @rmdir($dir);
        }
    }


    public static function settings_page(){
        global $wpdb;

        if(!self::ensure_wp_version())
            return;

        if($_POST["submit"]){
            check_admin_referer('gforms_update_settings', 'gforms_update_settings');

            if(!self::current_user_can_any("gravityforms_edit_settings"))
                die(__("You don't have adequate permission to edit settings.", "gravityforms"));

            RGFormsModel::save_key($_POST["gforms_key"]);
            update_option("rg_gforms_disable_css", $_POST["gforms_disable_css"]);
            update_option("rg_gforms_captcha_public_key", $_POST["gforms_captcha_public_key"]);
            update_option("rg_gforms_captcha_private_key", $_POST["gforms_captcha_private_key"]);

            //updating message because key could have been changed
            RGForms::cache_remote_message();
        }
        else if($_POST["uninstall"]){

            if(!self::current_user_can_any("gravityforms_uninstall"))
                die(__("You don't have adequate permission to uninstall Gravity Forms.", "gravityforms"));

            //droping all tables
            RGFormsModel::drop_tables();

            //removing options
            delete_option("rg_form_version");
            delete_option("rg_gforms_key");
            delete_option("rg_gforms_disable_css");
            delete_option("rg_gforms_captcha_public_key");
            delete_option("rg_gforms_captcha_private_key");

            //removing gravity forms upload folder
            self::delete_directory(RGFormsModel::get_upload_root());

            //Deactivating plugin
            $plugin = "gravityforms/gravityforms.php";
            deactivate_plugins($plugin);
            update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));

            ?>
            <div class="updated fade" style="padding:20px;"><?php _e(sprintf("Gravity Forms have been successfully uninstalled. It can be re-activated from the %splugins page%s.", "<a href='plugins.php'>","</a>"), "gravityforms")?></div>
            <?php
            return;
        }

        echo self::get_remote_message();

        ?>
            <link rel="stylesheet" href="<?php echo self::get_base_url()?>/css/admin.css" />
            <div class="wrap">
                <form method="post">
                    <?php wp_nonce_field('gforms_update_settings', 'gforms_update_settings') ?>
                    <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo self::get_base_url()?>/images/gravity-title-icon-32.png" style="float:left; margin:15px 7px 0 0;"/>
                    <h2><?php _e("Gravity Forms Settings", "gravityforms")?></h2>

                    <table class="form-table">
                      <tr valign="top">
                           <th scope="row"><label for="gforms_key"><?php _e("Support License Key", "gravityforms"); ?></label>  <?php gform_tooltip("settings_license_key") ?></th>
                            <td>
                                <?php
                                $key_field = '<input type="password" name="gforms_key" id="gforms_key" style="width:350px;" value="' . self::get_key() . '" />';
                                echo apply_filters('gform_settings_key_field', $key_field);
                                ?>
                                <br />
                            	<?php _e("The license key is used for access to automatic upgrades and support.", "gravityforms"); ?>
                            </td>
                        </tr>
                       <tr valign="top">
                             <th scope="row"><label for="gforms_disable_css"><?php _e("Output CSS", "gravityforms"); ?></label>  <?php gform_tooltip("settings_output_css") ?></th>
                            <td>
                                <input type="radio" name="gforms_disable_css" value="0" id="gforms_css_output_enabled" <?php echo get_option('rg_gforms_disable_css') == 1 ? "" : "checked='checked'" ?> /> <?php _e("Yes", "gravityforms"); ?>&nbsp;&nbsp;
                                <input type="radio" name="gforms_disable_css" value="1" id="gforms_css_output_disabled" <?php echo get_option('rg_gforms_disable_css') == 1 ? "checked='checked'" : "" ?> /> <?php _e("No", "gravityforms"); ?><br />
                                <?php _e("Set this to No if you would like to disable the plugin from outputting the form CSS.", "gravityforms"); ?>
                            </td>
                        </tr>
                    </table>

                    <div class="hr-divider"></div>

                      <h3><?php _e("reCAPTCHA Settings", "gravityforms"); ?></h3>

                      <p style="text-align: left;"><?php _e("Gravity Forms integrates with reCAPTCHA, a free CAPTCHA service that helps to digitize books while protecting your forms from spam bots. ", "gravityforms"); ?><a href="http://recaptcha.net/" target="_blank"><?php _e("Read more about reCAPTCHA", "gravityforms"); ?></a>.</p>

                      <table class="form-table">


                        <tr valign="top">
                           <th scope="row"><label for="gforms_captcha_public_key"><?php _e("reCAPTCHA Public Key", "gravityforms"); ?></label>  <?php gform_tooltip("settings_recaptcha_public") ?></th>
                            <td>
                                <input type="text" name="gforms_captcha_public_key" style="width:350px;" value="<?php echo get_option("rg_gforms_captcha_public_key") ?>" /><br />
                                <?php _e("Required only if you decide to use the reCAPTCHA field.", "gravityforms"); ?> <?php _e(sprintf("%sSign up%s for a free account to get the key.", '<a target="_blank" href="https://admin.recaptcha.net/recaptcha/createsite/?app=php">', '</a>'), "gravityforms"); ?>
                            </td>
                        </tr>
                        <tr valign="top">
                           <th scope="row"><label for="gforms_captcha_private_key"><?php _e("reCAPTCHA Private Key", "gravityforms"); ?></label>  <?php gform_tooltip("settings_recaptcha_private") ?></th>
                            <td>
                                <input type="text" name="gforms_captcha_private_key" style="width:350px;" value="<?php echo self::escape_attribute(get_option("rg_gforms_captcha_private_key")) ?>" /><br />
                                <?php _e("Required only if you decide to use the reCAPTCHA field.", "gravityforms"); ?> <?php _e(sprintf("%sSign up%s for a free account to get the key.", '<a target="_blank" href="https://admin.recaptcha.net/recaptcha/createsite/?app=php">', '</a>'), "gravityforms"); ?>
                            </td>
                        </tr>

                      </table>

                      <div class="hr-divider"></div>

                      <h3><?php _e("Installation Status", "gravityforms"); ?></h3>
                      <table class="form-table">

                        <tr valign="top">
                           <th scope="row"><label><?php _e("PHP Version", "gravityforms"); ?></label></th>
                            <td class="installation_item_cell">
                                <strong><?php echo phpversion(); ?></strong>
                            </td>
                            <td>
                                <?php
                                    if(version_compare(phpversion(), '5.0.0', '>')){
                                        ?>
                                        <img src="<?php echo self::get_base_url() ?>/images/tick.png"/>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <img src="<?php echo self::get_base_url() ?>/images/stop.png"/>
                                        <span class="installation_item_message"><?php _e("Gravity Forms requires PHP 5 or above.", "gravityforms"); ?></span>
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr valign="top">
                           <th scope="row"><label><?php _e("MySQL Version", "gravityforms"); ?></label></th>
                            <td class="installation_item_cell">
                                <strong><?php echo $wpdb->db_version();?></strong>
                            </td>
                            <td>
                                <?php
                                    if(version_compare($wpdb->db_version(), '5.0.0', '>')){
                                        ?>
                                        <img src="<?php echo self::get_base_url() ?>/images/tick.png"/>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <img src="<?php echo self::get_base_url() ?>/images/stop.png"/>
                                        <span class="installation_item_message"><?php _e("Gravity Forms requires MySQL 5 or above.", "gravityforms"); ?></span>
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr valign="top">
                           <th scope="row"><label><?php _e("WordPress Version", "gravityforms"); ?></label></th>
                            <td class="installation_item_cell">
                                <strong><?php echo get_bloginfo("version"); ?></strong>
                            </td>
                            <td>
                                <?php
                                    if(version_compare(get_bloginfo("version"), '2.8.0', '>')){
                                        ?>
                                        <img src="<?php echo self::get_base_url() ?>/images/tick.png"/>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <img src="<?php echo self::get_base_url() ?>/images/stop.png"/>
                                        <span class="installation_item_message"><?php _e("Gravity Forms requires WordPress 2.8 or above.", "gravityforms"); ?></span>
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                         <tr valign="top">
                           <th scope="row"><label><?php _e("Gravity Forms Version", "gravityforms"); ?></label></th>
                            <td class="installation_item_cell">
                                <strong><?php echo self::$version ?></strong>
                            </td>
                            <td>
                                <?php
                                    $version_info = self::get_version_info();
                                    if(version_compare(self::$version, $version_info["version"], '>=')){
                                        ?>
                                        <img src="<?php echo self::get_base_url() ?>/images/tick.png"/>
                                        <?php
                                    }
                                    else{
                                        self::check_update();
                                        _e(sprintf("New version %s available. Automatic upgrade available on the %splugins page%s", $version_info["version"], '<a href="plugins.php">', '</a>'), "gravityforms");
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>

                    <?php if(self::current_user_can_any("gravityforms_edit_settings")){ ?>
                        <br/><br/>
                        <p class="submit" style="text-align: left;">
                        <?php
                        $save_button = '<input type="submit" name="submit" value="' . __("Save Settings", "gravityforms"). '" class="button-primary"/>';
                        echo apply_filters("gform_settings_save_button", $save_button);
                        ?>
                        </p>
                   <?php } ?>
                </form>

                <form action="" method="post">
                    <?php if(self::current_user_can_any("gravityforms_uninstall")){ ?>
                        <div class="hr-divider"></div>

                        <h3><?php _e("Uninstall Gravity Forms", "gravityforms") ?></h3>
                        <div class="delete-alert"><?php _e("Warning! This operation deletes ALL Gravity Forms data.", "gravityforms") ?>
                            <?php
                            $uninstall_button = '<input type="submit" name="uninstall" value="' . __("Uninstall Gravity Forms", "gravityforms") . '" class="button" onclick="return confirm(\'' . __("Warning! ALL Gravity Forms data will be deleted, including entries. This cannot be undone. \'OK\' to delete, \'Cancel\' to stop", "gravityforms") . '\');"/>';
                            echo apply_filters("gform_uninstall_button", $uninstall_button);
                            ?>

                        </div>
                    <?php } ?>
                </form>
            </div>
        <?php

         if($_POST["submit"]){
             ?>
             <div class="updated fade" style="padding:6px;">
                <?php _e("Settings Updated", "gravityforms"); ?>.
             </div>
             <?php
        }
    }

    public static function export_page(){
        if(!self::ensure_wp_version())
            return;

        echo self::get_remote_message();

        ?>
            <script type='text/javascript' src='<?php echo self::get_base_url()?>/js/jquery-ui/ui.datepicker.js'></script><link rel='stylesheet' href='<?php echo self::get_base_url() ?>/css/datepicker.css' type='text/css' />
            <script type="text/javascript">
                function SelectExportForm(formId){
                    if(!formId)
                        return;

                    var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                    mysack.execute = 1;
                    mysack.method = 'POST';
                    mysack.setVar( "action", "rg_select_export_form" );
                    mysack.setVar( "rg_select_export_form", "<?php echo wp_create_nonce("rg_select_export_form") ?>" );
                    mysack.setVar( "form_id", formId);
                    mysack.encVar( "cookie", document.cookie, false );
                    mysack.onError = function() { alert('<?php _e("Ajax error while selecting a form", "gravityforms") ?>' )};
                    mysack.runAJAX();

                    return true;
                }

                function EndSelectExportForm(aryFields){
                    if(aryFields.length == 0)
                    {
                        jQuery("#export_field_container, #export_date_container, #export_submit_container").hide()
                        return;
                    }

                    var fieldList = "<li><input type='checkbox' onclick=\"jQuery('.gform_export_field').attr('checked', this.checked); jQuery('#gform_export_check_all').html(this.checked ? '<strong><?php _e("Deselect All", "gravityforms") ?></strong>' : '<strong><?php _e("Select All", "gravityforms") ?></strong>'); \"> <label id='gform_export_check_all'><strong><?php _e("Select All", "gravityforms") ?></strong></label></li>";
                    for(var i=0; i<aryFields.length; i++){
                        fieldList += "<li><input type='checkbox' id='export_field_" + i + "' name='export_field[]' value='" + aryFields[i][0] + "' class='gform_export_field'> <label for='export_field_" + i + "'>" + aryFields[i][1] + "</label></li>";
                    }
                    jQuery("#export_field_list").html(fieldList);
                    jQuery("#export_date_start, #export_date_end").datepicker({dateFormat: 'yy-mm-dd'});

                    jQuery("#export_field_container, #export_date_container, #export_submit_container").hide().show();
                }
                function StartExport(){

                    jQuery("#please_wait_container").show();
                    jQuery("#export_button").attr("disabled", "disabled");

                    var formId = jQuery("#export_form").val();
                    var fields = ""
                    jQuery(".gform_export_field").each(
                        function (){
                            if(this.checked)
                                fields += this.value + ",";
                        }
                    );

                    if(fields.length > 0)
                        fields = fields.substr(0, fields.length -1);

                    var startDate = jQuery("#export_date_start").val();
                    var endDate = jQuery("#export_date_end").val();

                    var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                    mysack.execute = 1;
                    mysack.method = 'POST';
                    mysack.setVar( "action", "rg_start_export" );
                    mysack.setVar( "rg_start_export", "<?php echo wp_create_nonce("rg_start_export") ?>" );
                    mysack.setVar( "form_id", formId);
                    mysack.setVar( "fields", fields);
                    mysack.setVar( "start_date", startDate);
                    mysack.setVar( "end_date", endDate);

                    mysack.encVar( "cookie", document.cookie, false );
                    mysack.onError = function() { alert('<?php _e("Ajax error while exporting.", "gravityforms") ?>' )};
                    mysack.runAJAX();

                    return true;
                }

                function EndExport(formId, isSuccess, value){
                    if(!isSuccess)
                    {
                        alert(value);
                        return;
                    }

                    jQuery("#please_wait_container").hide();
                    jQuery("#export_button").removeAttr("disabled");
                    jQuery("#export_frame").attr("src", "<?php echo self::get_base_url() ?>/download.php?form_id=" + formId + "&f=" + value);

                }

            </script>
            <link rel="stylesheet" href="<?php echo self::get_base_url()?>/css/admin.css"/>
            <div class="wrap">
                <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" style="margin: 15px 7px 0pt 0pt; float: left;" src="<?php echo self::get_base_url() ?>/images/gravity-title-icon-32.png"/>
                <h2><?php _e("Export Form Entries", "gravityforms") ?></h2>

                <form method="post" style="margin-top:10px;">

                   <table class="form-table">
                      <tr valign="top">
                           <th scope="row"><label for="export_form"><?php _e("Select A Form", "gravityforms"); ?></label> <?php gform_tooltip("export_select_form") ?></th>
                            <td>

                              <select id="export_form" name="export_form" onchange="SelectExportForm(jQuery(this).val());">
                                <option value=""><?php _e("Select a form", "gravityforms"); ?></option>
                                <?php
                                $forms = RGFormsModel::get_forms();
                                foreach($forms as $form){
                                    ?>
                                    <option value="<?php echo absint($form->id) ?>"><?php echo self::escape_text($form->title) ?></option>
                                    <?php
                                }
                                ?>
                            </select>

                            </td>
                        </tr>
                      <tr id="export_field_container" valign="top" style="display: none;">
                           <th scope="row"><label for="export_fields"><?php _e("Select Fields", "gravityforms"); ?></label> <?php gform_tooltip("export_select_fields") ?></th>
                            <td>
                                <ul id="export_field_list">
                                <ul>
                            </td>
                       </tr>
                      <tr id="export_date_container" valign="top" style="display: none;">
                           <th scope="row"><label for="export_date"><?php _e("Select Date Range", "gravityforms"); ?></label> <?php gform_tooltip("export_date_range") ?></th>
                            <td>
                                <div>
                                    <span style="width:150px; float:left; ">
                                        <input type="text" id="export_date_start" name="export_date_start" style="width:90%"/>
                                        <strong><label for="export_date_start" style="display:block;"><?php _e("Start", "gravityforms"); ?></label></strong>
                                    </span>

                                    <span style="width:150px; float:left;">
                                        <input type="text" id="export_date_end" name="export_date_end" style="width:90%"/>
                                        <strong><label for="export_date_end" style="display:block;"><?php _e("End", "gravityforms"); ?></label></strong>
                                    </span>
                                    <div style="clear: both;"></div>
                                    <?php _e("Date Range is optional, if no date range is selected all entries will be exported.", "gravityforms"); ?>
                                </div>
                            </td>
                       </tr>
                    </table>
                    <ul>
                        <li id="export_submit_container" style="display:none; clear:both;">
                            <br/><br/>
                            <input type="button" id="export_button" name="export" value="<?php _e("Download Export File", "gravityforms"); ?>" class="button-primary" onclick="StartExport();"/>
                            <span id="please_wait_container" style="display:none; margin-left:15px;">
                                <img src="<?php echo self::get_base_url()?>/images/loading.gif"> <?php _e("Exporting entries. Please wait...", "gravityforms"); ?>
                            </span>

                            <iframe id="export_frame" width="1" height="1" src="about:blank"></iframe>
                        </li>
                    </ul>
                </form>
            </div>
        <?php


    }


    public static function help_page(){
        if(!self::ensure_wp_version())
            return;

        echo self::get_remote_message();

        ?>
        <link rel="stylesheet" href="<?php echo self::get_base_url()?>/css/admin.css" />
        <div class="wrap">
            <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" style="margin: 15px 7px 0pt 0pt; float: left;" src="<?php echo self::get_base_url() ?>/images/gravity-title-icon-32.png"/>
            <h2><?php _e("Gravity Forms Help", "gravityforms"); ?></h2>

            <div style="margin-top:10px;">
            <h3><?php _e("Embedding a form", "gravityforms"); ?></h3>
            <?php _e("There are two ways to embed a form to a post or page.", "gravityforms"); ?>
            <ul style="margin-top:15px;">
                <li>
                    <strong><?php _e("Shortcode:", "gravityforms"); ?></strong>
                    <div style="padding:6px;">
                        <?php _e("Add the following shortcode to your page or post", "gravityforms"); ?><br/><br/>

                        <div class="gforms_code"><pre>[gravityform id=2 title=false description=false]</pre></div>

                        <strong><?php _e("id", "gravityforms"); ?>:</strong> <?php _e("(Required) The id of the form to be embedded", "gravityforms"); ?><br/>
                        <strong><?php _e("title", "gravityforms"); ?>:</strong> <?php _e("(Optional) Whether or not do display the form title. Defaults to 'false'", "gravityforms"); ?><br/>
                        <strong><?php _e("description", "gravityforms"); ?>:</strong> <?php _e("(Optional) Whether or not do display the form description. Defaults to 'false'", "gravityforms"); ?><br/>
                    </div>
                </li>
                <li>
                    <strong>Function:</strong>
                    <div style="padding:6px;">
                        <?php _e("Add the following function call to your template", "gravityforms"); ?><br/><br/>
                        <div class="gforms_code"><pre>&lt;?php gravity_form(2, false, false); ?&gt;</pre></div>
                        <strong><?php _e("Parameter 1 (id)", "gravityforms"); ?>:</strong> <?php _e("(Required) The id of the form to be embedded", "gravityforms"); ?><br/>
                        <strong><?php _e("Parameter 2 (title)", "gravityforms"); ?>:</strong> <?php _e("(Optional) Whether or not do display the form title. Defaults to 'false'", "gravityforms"); ?><br/>
                        <strong><?php _e("Parameter 3(description)", "gravityforms"); ?>:</strong> <?php _e("(Optional) Whether or not do display the form description. Defaults to 'false'", "gravityforms"); ?><br/>
                    </div>
                </li>
            </ul>

            <div class="hr-divider"></div>

            <h3><?php _e("Action Hooks and Filters", "gravityforms"); ?></h3>
            <?php _e("The following hooks and filters allow developers to integrate Gravity Forms with other plugins.", "gravityforms"); ?>
            <ul style="margin-top:15px;">
                <li>
                    <strong>gform_pre_submission</strong>
                    <div style="padding:6px;">
                        <?php _e("This action hook runs during form submission after validation has taken place and before entry data has been saved", "gravityforms"); ?><br/><br/>

                        <strong><?php _e("Sample", "gravityforms"); ?></strong>:<br/>
                        <div class="gforms_code"><pre>
add_action("gform_pre_submission", "pre_submission_handler");
function pre_submission_handler($form_meta){

    //displaying form title
    echo "Form Title: " . $form_meta["title"] . "&lt;br/>";

    //displaying all submitted fields
    foreach($form_meta["fields"] as $field){

        if(is_array($field["inputs"])){

            //handling multi-input fields such as name and address
            foreach($field["inputs"] as $input){
                $value = stripslashes($_POST["input_" . str_replace('.', '_', $input["id"])]);
                echo $input["label"] . ": " . $value .  "&lt;br/>";
            }
        }
        else{
            //handling single-input fields such as text and paragraph (textarea)
            $value = stripslashes($_POST["input_" . $field["id"]]);
            echo $field["label"] . ": " . $value .  "&lt;br/>";
        }
    }
}
                        </pre></div>

                    </div>
                </li>
                <li>
                    <strong>gform_post_submission</strong>
                    <div style="padding:6px;">
                        <?php _e("This action hook runs after entry data has been saved", "gravityforms"); ?><br/><br/>

                        <strong><?php _e("Sample", "gravityforms"); ?></strong>:<br/>
                        <div class="gforms_code"><pre>
add_action("gform_post_submission", "post_submission_handler");
function post_submission_handler($entry){
    global $wpdb;

    $results = $wpdb->get_results($wpdb->prepare("  SELECT l.*, field_number, value
                            FROM wp_rg_lead l
                            INNER JOIN wp_rg_lead_detail ld ON l.id = ld.lead_id
                            WHERE l.id=%d
                            ORDER BY  l.id, field_number", $entry["id"]));

    foreach($results as $result){
        echo "&lt;hr/>Entry Id: " . $result->id . "&lt;br/>";
        echo "Field Number: " . $result->field_number . "&lt;br/>";
        echo "Field Value: " . $result->value . "&lt;br/>";
    }
}
                        </pre></div>
                    </div>
                </li>

                <li>
                    <strong>gform_submit_button</strong>
                    <div style="padding:6px;">
                        <?php _e("Filters the form submit buton", "gravityforms"); ?><br/><br/>

                        <strong><?php _e("Sample", "gravityforms"); ?></strong>:<br/>
                        <div class="gforms_code"><pre>

add_filter("gform_submit_button", "form_submit_button");
function form_submit_button($button){
    return "&lt;input type='submit' value='My new button' />";
}

                        </pre></div>
                    </div>
                </li>

                <li>
                    <strong>gform_submit_button_FORMID</strong>
                    <div style="padding:6px;">
                        <?php _e("Same as gform_submit_button, but it only applies to the specified form", "gravityforms"); ?>.<br/><br/>

                        <strong><?php _e("Sample", "gravityforms"); ?></strong>:<br/>
                        <div class="gforms_code"><pre>
add_filter("gform_submit_button_75", "form_75_submit_button");
function form_75_submit_button($button){
    return "&lt;input type='submit' value='Button for form 75' />";
}

                        </pre></div>
                    </div>
                </li>
            </ul>

            </div>
        </div>
        <?php
    }

    public static function parse_shortcode($attributes){

        extract(shortcode_atts(array(
             'title' => true,
             'description' => true,
             'id' => 0,
             'field_values' => ""
          ), $attributes));

        $title = strtolower($title) == "false" ? false : true;
        $description = strtolower($description) == "false" ? false : true;
        $field_values = htmlspecialchars_decode($field_values);

        parse_str($field_values, $field_value_array); //parsing query string like string for field values and placing them into an associative array
        $field_value_array = stripslashes_deep($field_value_array);
        return self::get_form($id, $title, $description, false, $field_value_array);
    }

    public static function update_form_active(){
        check_ajax_referer('rg_update_form_active','rg_update_form_active');
        RGFormsModel::update_form_active($_POST["form_id"], $_POST["is_active"]);
    }

    public static function update_lead_property(){
        check_ajax_referer('rg_update_lead_property','rg_update_lead_property');
        RGFormsModel::update_lead_property($_POST["lead_id"], $_POST["name"], $_POST["value"]);
    }

    public static function delete_lead(){
        RGFormsModel::delete_lead($_POST["lead_id"]);
    }

//------------------------ FIELD INPUT FUNCTIONS ----------------------------------------
    public static function get_label($field, $input_id = 0, $input_only = false){
        $field_label = (IS_ADMIN || RG_CURRENT_PAGE == "select_columns.php") && !empty($field["adminLabel"]) ? $field["adminLabel"] : $field["label"];
        $input = self::get_input($field, $input_id);
        if($field["type"] == "checkbox" && $input != null)
            return $input["label"];
        else if($input != null)
            return $input_only ? $input["label"] : $field_label . ' (' . $input["label"] . ')';
        else
            return $field_label;
    }

    public static function get_input($field, $id){
        if(is_array($field["inputs"])){
            foreach($field["inputs"] as $input)
            {
                if($input["id"] == $id)
                    return $input;
            }
        }
        return null;
    }

    public static function get_field_value($field, $field_values = array()){

        switch($field["type"]){
            case "post_image" :
                $value[$field["id"] . ".1"] = self::get_input_value($field, "input_" . $field["id"] . "_1");
                $value[$field["id"] . ".4"] = self::get_input_value($field, "input_" . $field["id"] . "_4");
                $value[$field["id"] . ".7"] = self::get_input_value($field, "input_" . $field["id"] . "_7");
            break;
            case "checkbox" :
                $parameter_values = explode(",", self::get_parameter_value($field["inputName"], $field_values));

                if(!is_array($field["inputs"]))
                    return "";

                foreach($field["inputs"] as $input){
                    if(!empty($_POST)){
                        $value[$input["id"]] = stripslashes($_POST["input_" . str_replace('.', '_', $input["id"])]);
                    }
                    else{

                        foreach($parameter_values as $item){
                            $item = trim($item);
                            if($input["label"] == $item)
                            {
                                $value[$input["id"]] = $item;
                                break;
                            }
                        }
                    }
                }

            break;

            default:

                if(is_array($field["inputs"])){
                    foreach($field["inputs"] as $input)
                        $value[$input["id"]] = self::get_input_value($field, "input_" . str_replace('.', '_', $input["id"]), $input["name"], $field_values);
                }
                else{
                    $value = self::get_input_value($field, "input_" . $field["id"], $field["inputName"], $field_values);
                }
            break;
        }

        return $value;
    }

    private static function get_input_value($field, $standard_name, $custom_name = "", $field_values=array()){
        if(!empty($_POST)){
            return stripslashes($_POST[$standard_name]);
        }
        else if($field["allowsPrepopulate"]){
            return self::get_parameter_value($custom_name, $field_values);
        }
    }

    private static function get_parameter_value($name, $field_values){
        $value = stripslashes($_GET[$name]);
        if(empty($value))
            $value = $field_values[$name];

        return apply_filters("gform_field_value_$name", $value);
    }

    private static function has_date_field($form){
        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){
                if($field["type"] == "date")
                    return true;
            }
        }
        return false;

    }

    public static function get_form($form_id, $display_title=true, $display_description=true, $force_display=false, $field_values=null){

        //reading form metadata
        $form = RGFormsModel::get_form_meta($form_id);

        //Fired right before the form rendering process. Allow users to manipulate the form object before it gets displayed in the front end
        $form = apply_filters("gform_pre_render_$form_id", apply_filters("gform_pre_render", $form));

        if($form == null)
            return "<p>" . __("Oops! We could not locate your form.", "gravityforms") . "</p>";

        //Don't display inactive forms
        if(!$force_display){

            $form_info = RGFormsModel::get_form($form_id);
            if(!$form_info->is_active)
                return "";

            //If form has a schedule, make sure it is within the configured start and end dates
            if($form["scheduleForm"]){
                $local_time_start = sprintf("%s %02d:%02d %s", $form["scheduleStart"], $form["scheduleStartHour"], $form["scheduleStartMinute"], $form["scheduleStartAmpm"]);
                $local_time_end = sprintf("%s %02d:%02d %s", $form["scheduleEnd"], $form["scheduleEndHour"], $form["scheduleEndMinute"], $form["scheduleEndAmpm"]);
                $timestamp_start = strtotime($local_time_start . ' +0000');
                $timestamp_end = strtotime($local_time_end . ' +0000');
                $now = current_time("timestamp");

                if( (!empty($form["scheduleStart"]) && $now < $timestamp_start) || (!empty($form["scheduleEnd"]) && $now > $timestamp_end))
                    return  empty($form["scheduleMessage"]) ? "<p>" . __("Sorry. This form is no longer available.", "gravityforms") . "</p>" : "<p>" . $form["scheduleMessage"] . "</p>";
            }

            //If form has a limit of entries, check current entry count
            if($form["limitEntries"]) {
                $entry_count = RGFormsModel::get_lead_count($form_id, "");
                if($entry_count >= $form["limitEntriesCount"])
                    return  empty($form["limitEntriesMessage"]) ? "<p>" . __("Sorry. This form is no longer accepting new submissions.", "gravityforms"). "</p>" : "<p>" . $form["limitEntriesMessage"] . "</p>";
            }
        }

        $form_string = "";

        //When called via a template, this will enqueue the proper scripts
        //When called via a shortcode, this will be ignored (too late to enqueue), but the scripts will be enqueued via the enqueue_scripts event
        if(!get_option('rg_gforms_disable_css'))
            wp_enqueue_style("gforms_css", self::get_base_url() . "/css/forms.css");

        if(self::has_date_field($form)){
            wp_enqueue_script("gforms_ui_datepicker", self::get_base_url() . "/js/jquery-ui/ui.datepicker.js", array("jquery"), false, true);
            wp_enqueue_script("gforms_datepicker", self::get_base_url() . "/js/datepicker.js", array("gforms_ui_datepicker"), false, true);
        }

        //handling postback if form was submitted
        $is_postback = $_POST["is_submit_" . $form_id];
        $is_valid = true;
        if($is_postback){
            $is_valid = RGForms::validate($form);
            if($is_valid){

                //pre submission action
                do_action("gform_pre_submission", $form);

                //pre submission filter
                $form = apply_filters("gform_pre_submission_filter", $form);

                //handle submission
                $lead = array();
                $confirmation_message = RGForms::handle_submission($form, $lead);

                //post submission hook
                do_action("gform_post_submission", $lead);
            }
        }
        else{
            //recording form view. Ignores views from administrators
            if(!current_user_can("administrator")){
                RGFormsModel::insert_form_view($form_id, $_SERVER['REMOTE_ADDR']);
            }
        }

        if(empty($confirmation_message)){
            $form_string .= "
                <div class='gform_wrapper' id='gform_wrapper_$form_id'>
                <form method='post' enctype='multipart/form-data' id='gform_$form_id' class='" . $form["cssClass"] . "' action=''>";

            if($display_title || $display_description){
                $form_string .= "
                        <div id='gform_heading'>";
                if($display_title){
                    $form_string .= "
                            <h3 id='gform_title'>" . $form['title'] . "</h3>";
                }
                if($display_description){
                    $form_string .= "
                            <span id='gform_description'>" . $form['description'] ."</span>";
                }
                $form_string .= "
                        </div>";
            }

            if($is_postback && !$is_valid){
                $form_string .= "<div class='validation_error'>" . __("There was a problem with your submission.", "gravityforms") . "<br /> " . __("Errors have been highlighted below ", "gravityforms") . "</div>";
            }
            $form_string .= "
                        <div class='gform_body'>
                            <input type='hidden' class='gform_hidden' name='is_submit_$form_id' value='1'/>
                            <ul id='gform_fields' class='" . $form['labelPlacement'] . "'>";

                                if(is_array($form['fields']))
                                {
                                    foreach($form['fields'] as $field){
                                        $form_string .= RGForms::get_field($field, self::get_field_value($field, $field_values));
                                    }
                                }
            $form_string .= "
                            </ul>
                        </div>
                        <div class='gform_footer " . $form['labelPlacement'] . "'>";
                        $tabindex = self::$tab_index++;
                        if($form["button"]["type"] == "text" || empty($form["button"]["imageUrl"])){
                            $button_text = empty($form["button"]["text"]) ? __("Submit", "gravityforms") : $form["button"]["text"];
                            $button_input = "<input type='submit' class='button' value='" . esc_attr($button_text) . "' tabindex='$tabindex'/>";
                        }
                        else{
                            $imageUrl = $form["button"]["imageUrl"];
                            $button_input= "<input type='image' src='$imageUrl' tabindex='$tabindex'/>";
                        }

                        $button_input = apply_filters("gform_submit_button", $button_input);
                        $button_input = apply_filters("gform_submit_button_$form_id", $button_input);
                        $form_string .= $button_input;
                        if(current_user_can("gform_full_access"))
                            $form_string .= "&nbsp;&nbsp;<a href='" . get_bloginfo("wpurl") . "/wp-admin/admin.php?page=gf_edit_forms&amp;id=" . $form_id . "'>" . __("Edit this form", "gravityforms") . "</a>";
            $form_string .="
                        </div>
                </form>
                </div>";

            return $form_string;
        }
        else{
            return $confirmation_message;
        }
    }

    public static function new_form(){
        self::forms_page(0);
    }

    public static function ensure_wp_version(){
        if(UNSUPPORTED_WP_VERSION){
            echo "<div class='error' style='padding:10px;'>Gravity Forms require WordPress 2.8 or greater. You must upgrade WordPress in order to use Gravity Forms</div>";
            return false;
        }
        return true;
    }

    public static function forms(){
        if(!self::ensure_wp_version())
            return;

        $id = $_GET["id"];
        $view = $_GET["view"];

        if($view == "entries")
            self::leads_page($id);
        else if($view == "entry")
            self::lead_detail_page();
        else if($view == "notification")
            self::notification_page($id);
        else if(is_numeric($id))
            self::forms_page($id);
        else
            self::form_list_page();

    }

    public static function notification_page($form_id){
        $form = RGFormsModel::get_form_meta($form_id);

        if($_POST["save"]){
            check_admin_referer('gforms_save_notification', 'gforms_save_notification');

            $form["notification"]["to"] = stripslashes($_POST["form_notification_to"]);
            $form["notification"]["bcc"] = stripslashes($_POST["form_notification_bcc"]);
            $form["notification"]["subject"] = stripslashes($_POST["form_notification_subject"]);
            $form["notification"]["message"] = stripslashes($_POST["form_notification_message"]);
            $form["notification"]["from"] = empty($_POST["form_notification_from_field"]) ? stripslashes($_POST["form_notification_from"]) : "";
            $form["notification"]["fromField"] = stripslashes($_POST["form_notification_from_field"]);
            $form["notification"]["replyTo"] = empty($_POST["form_notification_reply_to_field"]) ? stripslashes($_POST["form_notification_reply_to"]) : "";
            $form["notification"]["replyToField"] = stripslashes($_POST["form_notification_reply_to_field"]);

            $form["autoResponder"]["toField"] = stripslashes($_POST["form_autoresponder_to"]);
            $form["autoResponder"]["bcc"] = stripslashes($_POST["form_autoresponder_bcc"]);
            $form["autoResponder"]["from"] = stripslashes($_POST["form_autoresponder_from"]);
            $form["autoResponder"]["replyTo"] = stripslashes($_POST["form_autoresponder_reply_to"]);
            $form["autoResponder"]["subject"] = stripslashes($_POST["form_autoresponder_subject"]);
            $form["autoResponder"]["message"] = stripslashes($_POST["form_autoresponder_message"]);

            RGFormsModel::update_form_meta($form_id, $form);
        }

        $wp_email = get_bloginfo("admin_email");
        $email_fields = self::get_email_fields($form);

        ?>
        <link rel="stylesheet" href="<?php echo self::get_base_url()?>/css/admin.css" />

        <script>

        function InsertVariable(element_id){
            var variable = jQuery('#' + element_id + '_variable_select').val();
            var messageElement = jQuery("#" + element_id);

            if(document.selection) {
                // Go the IE way
                messageElement[0].focus();
                document.selection.createRange().text=variable;
            }
            else if(messageElement[0].selectionStart) {
                // Go the Gecko way
                obj = messageElement[0]
                obj.value = obj.value.substr(0, obj.selectionStart) + variable + obj.value.substr(obj.selectionEnd, obj.value.length);
            }
            else {
                messageElement.val(variable + messageElement.val());
            }

            jQuery('#' + element_id + '_variable_select')[0].selectedIndex = 0;
        }


        </script>

        <form method="post" id="entry_form">
            <?php wp_nonce_field('gforms_save_notification', 'gforms_save_notification') ?>
            <div class="wrap">
                <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo self::get_base_url()?>/images/gravity-title-icon-32.png" style="float:left; margin:15px 7px 0 0;"/>
                <h2><?php echo self::escape_text($form["title"])?> : <?php _e("Notifications", "gravityforms"); ?></h2>
                <div id="poststuff" class="metabox-holder">
                    <div id="submitdiv" class="stuffbox">
                        <h3><span class="hndle"><?php _e("Notification to Administrator", "gravityforms"); ?></span></h3>
                        <div class="inside">
                            <div id="submitcomment" class="submitbox">
                                <div id="minor-publishingx" style="padding:10px;">
                                    <?php _e("Enter a message below to receive a notification email when users submit this form.", "gravityforms"); ?><br/><br/><br/>
                                    <ul id="form_notification_container">
                                        <li>
                                            <label for="form_notification_to">
                                                <?php _e("Send To Email", "gravityforms"); ?>
                                                <?php gform_tooltip("notification_send_to_email") ?>
                                            </label>
                                            <input type="text" name="form_notification_to" id="form_notification_to" value="<?php echo self::escape_attribute($form["notification"]["to"]) ?>" class="fieldwidth-1" />
                                        </li>
                                        <li>
                                            <label for="form_notification_from">
                                                <?php _e("From Email", "gravityforms"); ?>
                                                <?php gform_tooltip("notification_from_email") ?>
                                            </label>
                                            <input type="text" class="fieldwidth-2" name="form_notification_from" id="form_notification_from" onkeydown="jQuery('#form_notification_from_field').val('');" value="<?php echo (empty($form["notification"]["from"]) && empty($form["notification"]["fromField"])) ? self::escape_attribute($wp_email) : self::escape_attribute($form["notification"]["from"]) ?>"/>
                                            <?php
                                            if(!empty($email_fields)){
                                            ?>
                                                <?php _e("OR", "gravityforms"); ?>
                                                <select name="form_notification_from_field" id="form_notification_from_field" onchange="if(jQuery(this).val().length > 0 ) jQuery('#form_notification_from').val('');">
                                                    <option value=""><?php _e("Select an email field", "gravityforms"); ?></option>
                                                    <?php
                                                    foreach($email_fields as $field){
                                                        $selected = $form["notification"]["fromField"] == $field["id"] ? "selected='selected'" : "";
                                                        ?>
                                                        <option value="<?php echo $field["id"]?>" <?php echo $selected ?>><?php echo self::get_label($field)?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </li>

                                        <li>
                                            <label for="form_notification_reply_to">
                                                <?php _e("Reply To", "gravityforms"); ?>
                                                <?php gform_tooltip("notification_reply_to") ?>
                                            </label>
                                            <input type="text" name="form_notification_reply_to" id="form_notification_reply_to" onkeydown="jQuery('#form_notification_reply_to_field').val('');" value="<?php echo self::escape_attribute($form["notification"]["replyTo"]) ?>" class="fieldwidth-2" />
                                            <?php
                                            if(!empty($email_fields)){
                                            ?>
                                                <?php _e("OR", "gravityforms"); ?>
                                                <select name="form_notification_reply_to_field" id="form_notification_reply_to_field" onchange="if(jQuery(this).val().length > 0 ) jQuery('#form_notification_reply_to').val('');">
                                                    <option value=""><?php _e("Select an email field", "gravityforms"); ?></option>
                                                    <?php
                                                    foreach($email_fields as $field){
                                                        $selected = $form["notification"]["replyToField"] == $field["id"] ? "selected='selected'" : "";
                                                        ?>
                                                        <option value="<?php echo $field["id"]?>" <?php echo $selected ?>><?php echo self::get_label($field)?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </li>
                                        <li>
                                            <label for="form_notification_bcc">
                                                <?php _e("BCC", "gravityforms"); ?>
                                                <?php gform_tooltip("notification_bcc") ?>
                                            </label>
                                            <input type="text" name="form_notification_bcc" id="form_notification_bcc" value="<?php echo self::escape_attribute($form["notification"]["bcc"]) ?>" class="fieldwidth-1" />
                                        </li>
                                        <li>

                                                <div>

                                                    <label for="form_notification_subject">
                                                        <?php _e("Subject", "gravityforms"); ?>
                                                    </label>
                                                    <div>
                                                        <?php self::insert_variables($form["fields"], "form_notification_subject", true); ?>
                                                    </div>
                                                    <input type="text" name="form_notification_subject" id="form_notification_subject" value="<?php echo self::escape_attribute($form["notification"]["subject"]) ?>" class="fieldwidth-1" />
                                                </div>
								</li>
                                        <li>
                                                <div>

                                                    <label for="form_notification_message">
                                                        <?php _e("Message", "gravityforms"); ?>
                                                    </label>
                                                    <div>
                                                        <?php self::insert_variables($form["fields"], "form_notification_message"); ?>
                                                    </div>
                                                    <textarea name="form_notification_message" id="form_notification_message" class="fieldwidth-1 fieldheight-1" ><?php echo self::escape_text($form["notification"]["message"]) ?></textarea>
                                                </div>


                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="submitdiv" class="stuffbox">
                        <h3><span class="hndle"><?php _e("Notification to User", "gravityforms"); ?></span></h3>
                        <div class="inside">
                            <div id="submitcomment" class="submitbox">
                                <div id="minor-publishingx" style="padding:10px;">
                                    <?php

                                    if(empty($email_fields)){
                                        ?>
                                        <div class="gold_notice">
                                        <p><?php _e(sprintf("Your form does not have any %semail%s field.", "<strong>", "</strong>"), "gravityforms"); ?></p>
                                        <p>
                                        <?php _e(sprintf("Sending notifications to users require that the form has at least one email field. %sEdit your form%s",'<a href="?page=gf_edit_forms&id=' . absint($form_id) . '">', '</a>'), "gravityforms"); ?>
                                        </p>
                                        </div>
                                        <?php
                                    }
                                    else {
                                        ?>
                                        <?php _e("Enter a message below to send users an automatic response when they submit this form.", "gravityforms"); ?><br/><br/><br/>
                                        <ul id="form_autoresponder_container">

                                            <li>
                                                <label for="form_autoresponder_to">
                                                    <?php _e("Send To Field", "gravityforms"); ?>
                                                    <?php gform_tooltip("autoresponder_send_to_email") ?>
                                                </label>
                                                <select name="form_autoresponder_to" id="form_autoresponder_to">
                                                    <?php
                                                    foreach($email_fields as $field){
                                                        $selected = $form["autoResponder"]["toField"] == $field["id"] ? "selected='selected'" : "";
                                                        ?>
                                                        <option value="<?php echo $field["id"]?>" <?php echo $selected ?>><?php echo self::escape_text(self::get_label($field)) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </li>
                                            <li>
                                                <label for="form_autoresponder_bcc">
                                                    <?php _e("BCC", "gravityforms"); ?>
                                                    <?php gform_tooltip("autoresponder_bcc") ?>
                                                </label>
                                                <input type="text" name="form_autoresponder_bcc" id="form_autoresponder_bcc" value="<?php echo self::escape_attribute($form["autoResponder"]["bcc"]) ?>" class="fieldwidth-1" />
                                            </li>
                                            <li>
                                                <label for="form_autoresponder_from">
                                                    <?php _e("From Email", "gravityforms"); ?>
                                                    <?php gform_tooltip("autoresponder_from") ?>
                                                </label>
                                                <input type="text" name="form_autoresponder_from" id="form_autoresponder_from" value="<?php echo empty($form["autoResponder"]["from"]) ? self::escape_attribute($wp_email) : self::escape_attribute($form["autoResponder"]["from"]) ?>" class="fieldwidth-2" />
                                            </li>
                                            <li>
                                                <label for="form_autoresponder_reply_to" style="display:block;">
                                                    <?php _e("Reply To (optional)", "gravityforms"); ?>
                                                    <?php gform_tooltip("autoresponder_reply_to") ?>
                                                </label>
                                                <input type="text" name="form_autoresponder_reply_to" id="form_autoresponder_reply_to" value="<?php echo self::escape_attribute($form["autoResponder"]["replyTo"]) ?>" class="fieldwidth-2" />
                                            </li>
                                            <li>


                                                        <label for="form_autoresponder_subject">
                                                            <?php _e("Subject", "gravityforms"); ?>
                                                        </label>
                                                        <div>
                                                            <?php self::insert_variables($form["fields"], "form_autoresponder_subject", true); ?>
                                                        </div>
                                                        <input type="text" name="form_autoresponder_subject" id="form_autoresponder_subject" value="<?php echo self::escape_attribute($form["autoResponder"]["subject"]) ?>" class="fieldwidth-1" />


                                             </li>
                                            <li>

                                                    <div>

                                                        <label for="form_autoresponder_message">
                                                            <?php _e("Message", "gravityforms"); ?>
                                                        </label>
                                                        <div>
                                                            <?php self::insert_variables($form["fields"], "form_autoresponder_message"); ?>
                                                        </div>
                                                        <textarea name="form_autoresponder_message" id="form_autoresponder_message" class="fieldwidth-1 fieldheight-1"><?php echo self::escape_text($form["autoResponder"]["message"]) ?></textarea>
                                                    </div>
                                            </li>
                                        </ul>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br class="clear" />
                    <div>
                        <?php
                            $notification_button = '<input class="button-primary" type="submit" tabindex="4" value="' . __("Save Settings", "gravityforms") . '" name="save"/>';
                            echo apply_filters("gform_save_notification_button", $notification_button);
                        ?>
                    </div>
                </div>
            </div>
        </form>
        <?php

        if($_POST["save"]){
            ?>
            <div class="updated fade" style="padding:6px;">
                <?php _e("Notification Updated.", "gravityforms"); ?>
            </div>
            <?php
        }
    }

    public static function escape_attribute($text){
        return esc_attr($text);
    }

    public static function escape_text($text){
        return esc_html($text);
    }

    private static function get_email_fields($form){
        $fields = array();
        foreach($form["fields"] as $field){
            if($field["type"] == "email")
                $fields[] = $field;
        }

        return $fields;
    }

    private static function insert_field_variable($field){
        if(is_array($field["inputs"]))
        {
            foreach($field["inputs"] as $input){
                ?>
                <option value='<?php echo "{" . self::escape_text(self::get_label($field, $input["id"])) . ":" . $input["id"] . "}" ?>'><?php echo self::escape_text(self::get_label($field, $input["id"])) ?></option>
                <?php
            }
        }
        else{
            ?>
            <option value='<?php echo "{" . self::escape_text(self::get_label($field)) . ":" . $field["id"] . "}" ?>'><?php echo self::escape_text(self::get_label($field)) ?></option>
            <?php
        }
    }

    private static function has_post_field($fields){
        foreach($fields as $field){
            if(in_array($field["type"], array("post_title", "post_content", "post_excerpt", "post_category", "post_image", "post_tags", "post_custom_field")))
                return true;
        }
        return false;
    }

    private static function insert_variables($fields, $element_id, $hide_all_fields=false){
        ?>
        <select id="<?php echo $element_id?>_variable_select" onchange="InsertVariable('<?php echo $element_id?>');">
            <option value=''><?php _e("Insert form field", "gravityforms"); ?></option>
            <option value='' class='option_header'>Standard form fields</option>
            <?php
            if(!$hide_all_fields){
                ?>
                <option value='{all_fields}'><?php _e("All Submitted Fields", "gravityforms"); ?></option>
                <?php
            }
            ?>
            <option value='{date_mdy}'><?php _e("Date", "gravityforms"); ?> (mm/dd/yyyy)</option>
            <option value='{date_dmy}'><?php _e("Date", "gravityforms"); ?> (dd/mm/yyyy)</option>
            <option value='{embed_url}'><?php _e("Embed Url", "gravityforms"); ?></option>
            <option value='{entry_id}'><?php _e("Entry Id", "gravityforms"); ?></option>
            <option value='{entry_url}'><?php _e("Entry Url", "gravityforms"); ?></option>
            <option value='{form_id}'><?php _e("Form Id", "gravityforms"); ?></option>
            <option value='{form_title}'><?php _e("Form Title", "gravityforms"); ?></option>

            <?php if(self::has_post_field($fields)){ ?>
                <option value='{post_id}'><?php _e("Post Id", "gravityforms"); ?></option>
                <option value='{post_edit_url}'><?php _e("Post Edit Url", "gravityforms"); ?></option>
            <?php } ?>

            <option value='{ip}'><?php _e("User IP Address", "gravityforms"); ?></option>

            <?php
            $required_fields = array();
            $optional_fields = array();

            foreach($fields as $field){

                if($field["isRequired"]){

                    switch($field["type"]){
                        case "name" :
                            if($field["nameFormat"] == "extended"){
                                $prefix = self::get_input($field, $field["id"] + 0.2);
                                $suffix = self::get_input($field, $field["id"] + 0.8);
                                $optional_field = $field;
                                $optional_field["inputs"] = array($prefix, $suffix);

                                //Add optional name fields to the optional list
                                $optional_fields[] = $optional_field;

                                //Remove optional name field from required list
                                unset($field["inputs"][0]);
                                unset($field["inputs"][3]);
                            }

                            $required_fields[] = $field;
                        break;


                        default:
                            $required_fields[] = $field;
                    }
                }
                else{
                   $optional_fields[] = $field;
                }

            }

            if(!empty($required_fields)){
                ?>
                <option value='' class='option_header'><?php _e("Required form fields", "gravityforms"); ?></option>
                <?php
                foreach($required_fields as $field){
                    self::insert_field_variable($field);
                }
            }

            if(!empty($optional_fields)){
                ?>
                <option value='' class='option_header'><?php _e("Optional form fields", "gravityforms"); ?></option>
                <?php
                foreach($optional_fields as $field){
                    self::insert_field_variable($field);
                }
            }
            ?>
        </select>
        <?php
    }

    private static function truncate_url($url){
        $truncated_url = basename($url);
        if(empty($truncated_url))
            $truncated_url = dirname($url);

        $ary = explode("?", $truncated_url);

        return $ary[0];
    }

    public static function lead_detail_page(){
        global $wpdb;
        global $current_user;

        if(!self::ensure_wp_version())
            return;

        echo self::get_remote_message();

        $form = RGFormsModel::get_form_meta($_GET["id"]);
        $lead = RGFormsModel::get_lead($_GET["lid"]);
        if(!$lead){
            _e("OOps! We couldn't find your lead. Please try again", "gravityforms");
            return;
        }

        RGFormsModel::update_lead_property($lead["id"], "is_read", 1);

        $search_qs = empty($_GET["s"]) ? "" : "&s=" . $_GET["s"];
        $sort_qs = empty($_GET["sort"]) ? "" : "&sort=" . $_GET["sort"];
        $dir_qs = empty($_GET["dir"]) ? "" : "&dir=" . $_GET["dir"];
        $page_qs = empty($_GET["paged"]) ? "" : "&paged=" . absint($_GET["paged"]);

        switch($_POST["action"]){
            case "update" :
                check_admin_referer('gforms_save_entry', 'gforms_save_entry');
                RGFormsModel::save_lead($form, $lead);
                $lead = RGFormsModel::get_lead($_GET["lid"]);

            break;

            case "add_note" :
                check_admin_referer('gforms_update_note', 'gforms_update_note');
                $user_data = get_userdata($current_user->ID);
                RGFormsModel::add_note($lead["id"], $current_user->ID, $user_data->display_name, stripslashes($_POST["new_note"]));

                //emailing notes if configured
                if($_POST["gentry_email_notes_to"])
                {
                    $email_to = $_POST["gentry_email_notes_to"];
                    $email_from = $current_user->user_email;
                    $email_subject = stripslashes($_POST["gentry_email_subject"]);

                    $headers = "From: \"$email_from\" <$email_from> \r\n";
                    $result = wp_mail($email_to, $email_subject, stripslashes($_POST["new_note"]), $headers);
                }
            break;

            case "add_quick_note" :
                check_admin_referer('gforms_save_entry', 'gforms_save_entry');
                $user_data = get_userdata($current_user->ID);
                RGFormsModel::add_note($lead["id"], $current_user->ID, $user_data->display_name, stripslashes($_POST["quick_note"]));
            break;

            case "bulk" :
                check_admin_referer('gforms_update_note', 'gforms_update_note');
                if($_POST["bulk_action"] == "delete")
                    RGFormsModel::delete_notes($_POST["note"]);
            break;

            case "delete" :
                check_admin_referer('gforms_save_entry', 'gforms_save_entry');
                RGFormsModel::delete_lead($lead["id"]);
                ?>
                <div id="message" class="updated fade" style="background-color: rgb(255, 251, 204); margin-top:50px; padding:50px;">
                    <?php _e("Entry has been deleted.", "gravityforms"); ?> <a href="<?php echo esc_url("admin.php?page=gf_entries&view=entries&id=" . absint($form["id"]) . $search_qs . $sort_qs . $dir_qs . $page_qs) ?>"><?php _e("Back to entries list", "gravityforms"); ?></a>
                </div>
                <?php
                exit;
            break;
        }


        $mode = empty($_POST["screen_mode"]) ? "view" : $_POST["screen_mode"];

        ?>
        <link rel="stylesheet" href="<?php echo self::get_base_url()?>/css/admin.css" />
        <script type="text/javascript">


            function DeleteFile(leadId, fieldId){
                if(confirm(<?php _e("'Would you like to delete this file? \'Cancel\' to stop. \'OK\' to delete'", "gravityforms"); ?>)){

                    var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                    mysack.execute = 1;
                    mysack.method = 'POST';
                    mysack.setVar( "action", "rg_delete_file" );
                    mysack.setVar( "rg_delete_file", "<?php echo wp_create_nonce("rg_delete_file") ?>" );
                    mysack.setVar( "lead_id", leadId );
                    mysack.setVar( "field_id", fieldId );
                    mysack.encVar( "cookie", document.cookie, false );
                    mysack.onError = function() { alert('<?php _e("Ajax error while deleting field.", "gravityforms") ?>' )};
                    mysack.runAJAX();

                    return true;
                }
            }

            function EndDeleteFile(fieldId){
                jQuery('#preview_' + fieldId).hide();
                jQuery('#upload_' + fieldId).show('slow');
            }

        </script>

        <form method="post" id="entry_form" enctype='multipart/form-data'>
            <?php wp_nonce_field('gforms_save_entry', 'gforms_save_entry') ?>
            <input type="hidden" name="action" id="action" value=""/>
            <input type="hidden" name="screen_mode" id="screen_mode" value="<?php echo $_POST["screen_mode"] ?>" />

            <div class="wrap">
            <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo self::get_base_url()?>/images/gravity-title-icon-32.png" style="float:left; margin:15px 7px 0 0;"/>
            <h2><?php _e("Entry #", "gravityforms"); ?><?php echo absint($lead["id"])?></h2>
            <a href="<?php echo esc_url("admin.php?page=gf_entries&view=entries&id=" . absint($form["id"]) . $search_qs . $sort_qs . $dir_qs . $page_qs) ?>"><?php _e("&laquo; back to entries list", "gravityforms"); ?></a>
            <div id="poststuff" class="metabox-holder has-right-sidebar">
                <div id="side-info-column" class="inner-sidebar">
                    <div id="submitdiv" class="stuffbox">
                        <h3>
                            <span class="hndle"><?php _e("Info", "gravityforms"); ?></span>
                        </h3>
                        <div class="inside">
                            <div id="submitcomment" class="submitbox">
                                <div id="minor-publishing" style="padding:10px;">
                                    <br/>
                                    <?php _e("Entry Id", "gravityforms"); ?>: <?php echo absint($lead["id"]) ?><br/><br/>
                                    <?php _e("Submitted on", "gravityforms"); ?>: <?php echo self::escape_text(self::format_date($lead["date_created"], false)) ?>
                                    <br/>
                                    <br/>
                                    <?php _e("User IP", "gravityforms"); ?>: <?php echo $lead["ip"] ?>
                                    <br/><br/>
                                    <?php _e("Embed Url", "gravityforms"); ?>: <a href="<?php echo esc_url($lead["source_url"]) ?>" target="_blank" alt="<?php echo esc_url($lead["source_url"]) ?>" title="<?php echo esc_url($lead["source_url"]) ?>">.../<?php echo self::escape_text(self::truncate_url($lead["source_url"]))?></a>
                                    <br/><br/>
                                    <?php
                                    if(!empty($lead["post_id"])){
                                        $post = get_post($lead["post_id"]);
                                        ?>
                                        <?php _e("Edit Post", "gravityforms"); ?>: <a href="post.php?action=edit&post=<?php echo absint($post->ID) ?>" alt="<?php _e("Click to edit post", "gravityforms"); ?>" title="<?php _e("Click to edit post", "gravityforms"); ?>"><?php echo self::escape_text($post->post_title) ?></a>
                                        <br/><br/>
                                        <?php
                                    }
                                    ?>

                                </div>
                                <div id="major-publishing-actions">
                                    <div id="delete-action">
                                        <?php
                                            if(self::current_user_can_any("gravityforms_delete_entries")){
                                                $delete_link = '<a class="submitdelete deletion" onclick="if ( confirm(\''. __("You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.", "gravityforms") .'\') ) { jQuery(\'#action\').val(\'delete\'); jQuery(\'#entry_form\')[0].submit();} return false;" href="#">' . __("Delete", "gravityforms") . '</a>';
                                                echo apply_filters("gform_entrydetail_delete_link", $delete_link);
                                            }
                                        ?>
                                    </div>
                                    <div id="publishing-action">
                                        <?php
                                            if(self::current_user_can_any("gravityforms_edit_entries")){
                                                $button_text = $mode == "view" ? __("Edit Entry", "gravityforms") : __("Update Entry", "gravityforms");
                                                $button_click = $mode == "view" ? "jQuery('#screen_mode').val('edit');" : "jQuery('#action').val('update'); jQuery('#screen_mode').val('view');";
                                                $update_button = '<input class="button-primary" type="submit" tabindex="4" value="' . $button_text . '" name="save" onclick="' . $button_click . '"/>';
                                                echo apply_filters("gform_entrydetail_update_button", $update_button);
                                                if($mode == "edit")
                                                    echo '&nbsp;&nbsp;<input class="button" style="color:#bbb;" type="submit" tabindex="5" value="' . __("Cancel", "gravityforms") . '" name="cancel" onclick="jQuery(\'#screen_mode\').val(\'view\');"/>';
                                            }
                                        ?>
                                    </div>
                                    <br/> <br/><br/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(self::current_user_can_any("gravityforms_edit_entry_notes")) { ?>
                        <!-- start side notes -->
                        <div class="postbox" id="tagsdiv-post_tag">
                            <h3 style="cursor:default;"><span>Quick Note</span></h3>
                            <div class="inside">
                                <div id="post_tag" class="tagsdiv">
                                    <div>
                                        <span>
                                            <textarea name="quick_note" style="width:99%; height:180px; margin-bottom:4px;"></textarea>
                                            <input type="submit" name="add_quick_note" value="<?php _e("Add Note", "gravityforms") ?>" class="button" style="width:60px;" onclick="jQuery('#action').val('add_quick_note');"/>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <!-- end side notes -->
                   <?php } ?>

                   <!-- begin print button -->
                   <div class="detail-view-print">
                       <a href="javascript:;" onclick="var notes_qs = jQuery('#gform_print_notes').is(':checked') ? '&notes=1' : ''; var url='<?php echo self::get_base_url() ?>/print-entry.php?fid=<?php echo $form['id'] ?>&lid=<?php echo $lead['id']?>' + notes_qs; window.open (url,'printwindow');" class="button">Print</a>
                       <?php if(self::current_user_can_any("gravityforms_view_entry_notes")) { ?>
                           <input type="checkbox" name="print_notes" value="print_notes" checked="checked" id="gform_print_notes"/>
                           <label for="print_notes">include notes</label>
                       <?php } ?>
                   </div>
                   <!-- end print button -->

                </div>

                <div id="post-body" class="has-sidebar">
                    <div id="post-body-content" class="has-sidebar-content">
                        <?php
                        if($mode == "view")
                            self::lead_detail_grid($form, $lead);
                        else
                            self::lead_detail_edit($form, $lead);
                        ?>

                        <?php if(self::current_user_can_any("gravityforms_view_entry_notes")) { ?>
                            <div id="namediv" class="stuffbox">
                                <h3>
                                    <label for="name"><?php _e("Notes", "gravityforms"); ?></label>
                                </h3>

                                <form method="post">
                                    <?php wp_nonce_field('gforms_update_note', 'gforms_update_note') ?>
                                    <div class="inside">
                                        <?php
                                        $notes = RGFormsModel::get_lead_notes($lead["id"]);

                                        //getting email values
                                        $email_fields = self::get_email_fields($form);
                                        $emails = array();

                                        foreach($email_fields as $email_field){
                                            if(!empty($lead[$email_field["id"]])){
                                                $emails[] = $lead[$email_field["id"]];
                                            }
                                        }
                                        //displaying notes grid
                                        $subject = !empty($form["autoResponder"]["subject"]) ? "RE: " . self::replace_variables($form["autoResponder"]["subject"], $form, $lead) : "";
                                        self::notes_grid($notes, true, $emails, $subject);
                                        ?>
                                    </div>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php

         if($_POST["action"] == "update"){
            ?>
            <div class="updated fade" style="padding:6px;">
                <?php _e("Entry Updated.", "gravityforms"); ?>
            </div>
            <?php
        }
    }

    public static function lead_detail_edit($form, $lead){
        ?>
        <div id="namediv" class="stuffbox">
            <h3>
                <label for="name"><?php _e("Details", "gravityforms"); ?></label>
            </h3>
            <div class="inside">
                <table class="form-table entry-details">
                    <tbody>
                    <?php
                    foreach($form["fields"] as $field){
                        switch($field["type"]){
                            case "section" :
                                ?>
                                <tr valign="top">
                                    <td class="detail-view">
                                        <div style="margin-bottom:10px; border-bottom:1px dotted #ccc;"><h2 class="detail_gsection_title"><?php echo self::escape_text(self::get_label($field))?></h2></div>
                                    </td>
                                </tr>
                                <?php
                            break;

                            case "captcha":
                                //ignore captcha field
                            break;

                            default :
                                $value = RGFormsModel::get_lead_field_value($lead, $field);
                                ?>
                                <tr valign="top">
                                    <td class="detail-view">
                                        <label class="detail-label"><?php echo self::escape_text(self::get_label($field))?></label>
                                        <?php echo self::get_field_input($field, $value, $lead["id"])?>
                                    </td>
                                </tr>
                                <?php
                            break;
                        }

                    }
                    ?>
                    </tbody>
                </table>
                <br/>
            </div>
        </div>
        <?php
    }

    public static function notes_grid($notes, $is_editable, $emails = null, $autoresponder_subject=""){
        if(sizeof($notes) > 0 && $is_editable && self::current_user_can_any("gravityforms_edit_entry_notes")){
            ?>
            <div class="alignleft actions" style="padding:3px 0;">
                <label class="hidden" for="bulk_action"><?php _e(" Bulk action", "gravityforms") ?></label>
                <select name="bulk_action" id="bulk_action">
                    <option value=''><?php _e(" Bulk action ", "gravityforms") ?></option>
                    <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                </select>
                <?php
                $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="jQuery(\'#action\').val(\'bulk\');" style="width: 50px;" />';
                echo apply_filters("gform_notes_apply_button", $apply_button);
                ?>
            </div>
            <?php
        }
        ?>
        <table class="widefat fixed entry-detail-notes" cellspacing="0">
            <?php
            if(!$is_editable){
            ?>
            <thead>
                <tr>
                    <th id="notes">Notes</th>
                </tr>
            </thead>
            <?php
            }
            ?>
            <tbody id="the-comment-list" class="list:comment">
            <?php
            $count = 0;
            $notes_count = sizeof($notes);
            foreach($notes as $note){
                $count++;
                $is_last = $count >= $notes_count ? true : false;
                ?>
                <tr valign="top">
                    <?php
                    if($is_editable && self::current_user_can_any("gravityforms_edit_entry_notes")){
                    ?>
                        <th class="check-column" scope="row" style="padding:9px 3px 0 0">
                            <input type="checkbox" value="<?php echo $note->id ?>" name="note[]"/>
                        </th>
                        <td colspan="2">
                    <?php
                    }
                    else{
                    ?>
                        <td class="entry-detail-note<?php echo $is_last ? " lastrow" : "" ?>">
                    <?php
                    }
                    ?>
                            <div style="margin-top:4px;">
                                <div class="note-avatar"><?php echo get_avatar($note->user_id, 48);?></div>
                                <h6 class="note-author"> <?php echo self::escape_text($note->user_name)?></h6>
                                <p style="line-height:130%; text-align:left; margin-top:3px;"><a href="mailto:<?php echo self::escape_attribute($note->user_email)?>"><?php echo self::escape_text($note->user_email) ?></a><br />
                                <?php _e("added on", "gravityforms"); ?> <?php echo self::escape_text(self::format_date($note->date_created, false)) ?></p>
                            </div>
                            <div class="detail-note-content"><?php echo self::escape_text($note->value) ?></div>
                        </td>

                </tr>
                <?php
            }
            if($is_editable && self::current_user_can_any("gravityforms_edit_entry_notes")){
                ?>
                <tr>
                    <td colspan="3" style="padding:10px;" class="lastrow">
                        <textarea name="new_note" style="width:100%; height:50px; margin-bottom:4px;"></textarea>
                        <?php
                        $note_button = '<input type="submit" name="add_note" value="' . __("Add Note", "gravityforms") . '" class="button" style="width:60px;" onclick="jQuery(\'#action\').val(\'add_note\');"/>';
                        echo apply_filters("gform_addnote_button", $note_button);

                        if(!empty($emails)){ ?>
                            &nbsp;&nbsp;
                            <span>
                                <select name="gentry_email_notes_to" onchange="if(jQuery(this).val() != '') {jQuery('#gentry_email_subject_container').css('display', 'inline');} else{jQuery('#gentry_email_subject_container').css('display', 'none');}">
                                    <option value=""><?php _e("Also email this note to", "gravityforms") ?></option>
                                    <?php foreach($emails as $email){ ?>
                                        <option value="<?php echo $email ?>"><?php echo $email ?></option>
                                    <?php } ?>
                                </select>
                                &nbsp;&nbsp;

                                <span id='gentry_email_subject_container' style="display:none;">
                                    <label for="gentry_email_subject"><?php _e("Subject:", "gravityforms") ?></label>
                                    <input type="text" name="gentry_email_subject" id="gentry_email_subject" value="<?php echo $autoresponder_subject ?>" style="width:35%"/>
                                </span>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }

    public static function lead_detail_grid($form, $lead){
        ?>
        <table cellspacing="0" class="widefat fixed entry-detail-view">
            <thead>
                <tr>
                    <th id="details"><?php echo $form["title"]?> : <?php _e("Entry # ", "gravityforms") ?> <?php echo $lead["id"] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                $field_count = sizeof($form["fields"]);
                foreach($form["fields"] as $field){
                    $count++;
                    $is_last = $count >= $field_count ? true : false;

                    switch($field["type"]){
                        case "section" :
                            ?>
                            <tr>
                                <td class="entry-view-section-break<?php echo $is_last ? " lastrow" : ""?>"><?php echo self::escape_text(self::get_label($field))?></td>
                            </tr>
                            <?php
                        break;

                        case "captcha":
                            //ignore captcha field
                        break;

                        default :

                            $value = RGFormsModel::get_lead_field_value($lead, $field);

                            $display_value = self::get_lead_field_display($field, $value);
                            ?>
                            <tr>
                                <td class="entry-view-field-name"><?php echo self::escape_text(self::get_label($field))?></td>
                            </tr>
                            <tr>
                                <td class="entry-view-field-value<?php echo $is_last ? " lastrow" : ""?>"><?php echo empty($display_value) ? "&nbsp;" : $display_value ?></td>
                            </tr>
                            <?php
                        break;
                    }
                }
                ?>
            </tbody>
        </table>
        <?php
    }

    public static function get_lead_field_display($field, $value){

        switch($field["type"]){
            case "name" :
                if(is_array($value)){
                    $prefix = trim($value[$field["id"] . ".2"]);
                    $first = trim($value[$field["id"] . ".3"]);
                    $last = trim($value[$field["id"] . ".6"]);
                    $suffix = trim($value[$field["id"] . ".8"]);

                    $name = $prefix;
                    $name .= !empty($name) && !empty($first) ? " $first" : $first;
                    $name .= !empty($name) && !empty($last) ? " $last" : $last;
                    $name .= !empty($name) && !empty($suffix) ? " $suffix" : $suffix;

                    return $name;
                }
                else{
                    return $value;
                }

            break;

            case "address" :
                if(is_array($value)){
                    $street_value = trim($value[$field["id"] . ".1"]);
                    $street2_value = trim($value[$field["id"] . ".2"]);
                    $city_value = trim($value[$field["id"] . ".3"]);
                    $state_value = trim($value[$field["id"] . ".4"]);
                    $zip_value = trim($value[$field["id"] . ".5"]);
                    $country_value = trim($value[$field["id"] . ".6"]);

                    $address = $street_value;
                    $address .= !empty($address) && !empty($street2_value) ? " $street2_value" : $street2_value;
                    $address .= !empty($address) && (!empty($city_value) || !empty($state_value)) ? "<br />$city_value" : $city_value;
                    $address .= !empty($address) && !empty($city_value) && !empty($state_value) ? ", $state_value" : $state_value;
                    $address .= !empty($address) && !empty($zip_value) ? " $zip_value" : $zip_value;
                    $address .= !empty($address) && !empty($country_value) ? "<br />$country_value" : $country_value;

                    //adding map link
                    if(!empty($address)){
                        $address_qs = str_replace("<br />", " ", $address); //replacing <br/> with spaces
                        $address_qs = urlencode($address_qs);
                        $address .= "<br/><a href='http://maps.google.com/maps?q=$address_qs' target='_blank' class='map-it-link'>Map It</a>";
                    }

                    return $address;
                }
                else{
                    return "";
                }
            break;

            case "email" :
                return !empty($value) ? "<a href='mailto:$value'>$value</a>" : "";
            break;

            case "website" :
                return !empty($value) ? "<a href='$value' target='_blank'>$value</a>" : "";
            break;

            case "checkbox" :
                if(is_array($value)){

                    foreach($value as $key => $item){
                        if(!empty($item)){
                            $items .= "<li>$item</li>";
                        }
                    }
                    return empty($items) ? "" : "<ul class='bulleted'>$items</ul>";
                }
                else{
                    return $value;
                }
            break;

            case "post_image" :
                list($url, $title, $caption, $description) = explode("|:|", $value);
                if(!empty($url)){
                    $value = "<a href='$url' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$url' width='100' /></a>";
                    $value .= !empty($title) ? "<div>Title: $title</div>" : "";
                    $value .= !empty($caption) ? "<div>Caption: $caption</div>" : "";
                    $value .= !empty($description) ? "<div>Description: $description</div>": "";
                }
                return $value;
            case "fileupload" :
                $file_path = $value;
                if(!empty($file_path)){
                    $info = pathinfo($file_path);
                    $value = "<a href='$file_path' target='_blank' title='" . __("Click to view", "gravityforms") . "'>" . $info["basename"] . "</a>";
                }
                return $value;
            break;

            case "date" :
                return self::date_display($value, $field["dateFormat"]);
            break;

            default :
                return nl2br($value);
            break;
        }
    }

    public static function leads_page($form_id){
        global $wpdb;

        //quit if version of wp is not supported
        if(!self::ensure_wp_version())
            return;

        //displaying lead detail page if lead id is in the query string
        $lead_id = $_GET["lid"];
        if(!empty($lead_id))
        {
            self::lead_detail_page();
            return;
        }

        echo self::get_remote_message();
        $action = $_POST["action"];

        switch($action){
            case "delete" :
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $lead_id = $_POST["action_argument"];
                RGFormsModel::delete_lead($lead_id);
            break;

            case "bulk":
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $bulk_action = !empty($_POST["bulk_action"]) ? $_POST["bulk_action"] : $_POST["bulk_action2"];
                $leads = $_POST["lead"];
                switch($bulk_action){
                    case "delete":
                        RGFormsModel::delete_leads($leads);
                    break;

                    case "mark_read":
                        RGFormsModel::update_leads_property($leads, "is_read", 1);
                    break;

                    case "mark_unread":
                        RGFormsModel::update_leads_property($leads, "is_read", 0);
                    break;

                    case "add_star":
                        RGFormsModel::update_leads_property($leads, "is_starred", 1);
                    break;

                    case "remove_star":
                        RGFormsModel::update_leads_property($leads, "is_starred", 0);
                    break;
                }
            break;

            case "change_columns":
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $columns = self::json_decode(stripslashes($_POST["grid_columns"]), true);
                RGFormsModel::update_grid_column_meta($form_id, $columns);
            break;
        }



        $sort_field = empty($_GET["sort"]) ? 0 : $_GET["sort"];
        $sort_direction = empty($_GET["dir"]) ? "DESC" : $_GET["dir"];
        $search = $_GET["s"];
        $page_index = empty($_GET["paged"]) ? 0 : intval($_GET["paged"]) - 1;
        $star = is_numeric($_GET["star"]) ? intval($_GET["star"]) : null;
        $read = is_numeric($_GET["read"]) ? intval($_GET["read"]) : null;
        $page_size = 20;
        $first_item_index = $page_index * $page_size;

        $form = RGFormsModel::get_form_meta($form_id);
        $sort_field_meta = RGFormsModel::get_field($form, $sort_field);
        $is_numeric = $sort_field_meta["type"] == "number";

        $leads = RGFormsModel::get_leads($form_id, $sort_field, $sort_direction, $search, $first_item_index, $page_size, $star, $read, $is_numeric);
        $lead_count = RGFormsModel::get_lead_count($form_id, $search, $star, $read);

        $summary = RGFormsModel::get_form_counts($form_id);
        $total_lead_count = $summary["total"];
        $unread_count = $summary["unread"];
        $starred_count = $summary["starred"];

        $columns = RGFormsModel::get_grid_columns($form_id, true);

        $search_qs = empty($search) ? "" : "&s=$search";
        $sort_qs = $sort_field == 0 ? "" : "&sort=$sort_field";
        $dir_qs = $sort_field == 0 ? "" : "&dir=$sort_direction";
        $star_qs = $star !== null ? "&star=$star" : "";
        $read_qs = $read !== null ? "&read=$read" : "";

        $page_links = paginate_links( array(
            'base' =>  admin_url("admin.php") . "?page=gf_entries&view=entries&id=$form_id&%_%" . $search_qs . $sort_qs . $dir_qs. $star_qs . $read_qs,
            'format' => 'paged=%#%',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($lead_count / $page_size),
            'current' => $page_index + 1,
            'show_all' => false
        ));


        wp_print_scripts(array("thickbox"));
        wp_print_styles(array("thickbox"));


        ?>

        <script src="<?php echo self::get_base_url() ?>/js/jquery.json-1.3.js"></script>

        <script>
            function ChangeColumns(columns){
                jQuery("#action").val("change_columns");
                jQuery("#grid_columns").val(jQuery.toJSON(columns));
                tb_remove();
                jQuery("#lead_form")[0].submit();
            }

            function Search(sort_field_id, sort_direction, form_id, search, star, read){
                var search_qs = search == "" ? "" : "&s=" + search;
                var star_qs = star == "" ? "" : "&star=" + star;
                var read_qs = read == "" ? "" : "&read=" + read;

                var location = "?page=gf_entries&view=entries&id=" + form_id + "&sort=" + sort_field_id + "&dir=" + sort_direction + search_qs + star_qs + read_qs;
                document.location = location;
            }

            function ToggleStar(img, lead_id){
                var is_starred = img.src.indexOf("star1.png") >=0
                if(is_starred)
                    img.src = img.src.replace("star1.png", "star0.png");
                else
                    img.src = img.src.replace("star0.png", "star1.png");

                UpdateCount("star_count", is_starred ? -1 : 1);

                UpdateLeadProperty(lead_id, "is_starred", is_starred ? 0 : 1);
            }

            function ToggleRead(lead_id){
                var title = jQuery("#lead_row_" + lead_id);

                marking_read = title.hasClass("lead_unread");

                jQuery("#mark_read_" + lead_id).css("display", marking_read ? "none" : "inline");
                jQuery("#mark_unread_" + lead_id).css("display", marking_read ? "inline" : "none");
                title.toggleClass("lead_unread");

                UpdateCount("unread_count", marking_read ? -1 : 1);

                UpdateLeadProperty(lead_id, "is_read", marking_read ? 1 : 0);
            }

            function UpdateLeadProperty(lead_id, name, value){
                var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "rg_update_lead_property" );
                mysack.setVar( "rg_update_lead_property", "<?php echo wp_create_nonce("rg_update_lead_property") ?>" );
                mysack.setVar( "lead_id", lead_id);
                mysack.setVar( "name", name);
                mysack.setVar( "value", value);
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() { alert('<?php _e("Ajax error while setting lead property", "gravityforms") ?>' )};
                mysack.runAJAX();

                return true;
            }

            function UpdateCount(element_id, change){
                var element = jQuery("#" + element_id);
                var count = parseInt(element.html()) + change
                element.html(count + "");
            }

            function DeleteLead(lead_id){
                jQuery("#action").val("delete");
                jQuery("#action_argument").val(lead_id);
                jQuery("#lead_form")[0].submit();
                return true;
            }

            function ChangeForm(){
                var form_id = jQuery("#form_id").val();
                document.location =  "?page=gf_entries&view=entries&id=" + form_id;
            }

        </script>
        <link rel="stylesheet" href="<?php echo self::get_base_url() ?>/css/admin.css" type="text/css" />
        <style>
            .lead_unread a, .lead_unread td{font-weight: bold;}
            .row-actions a{ font-weight:normal;}
            .entry_nowrap{
                overflow:hidden; white-space:nowrap;
            }
        </style>


        <div class="wrap">
            <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo self::get_base_url()?>/images/gravity-title-icon-32.png" style="float:left; margin:15px 7px 0 0;"/>
            <h2><?php _e("Entries", "gravityforms"); ?> : <?php echo $form["title"] ?> </h2>

            <form id="lead_form" method="post">
                <?php wp_nonce_field('gforms_entry_list', 'gforms_entry_list') ?>

                <input type="hidden" value="" name="grid_columns" id="grid_columns" />
                <input type="hidden" value="" name="action" id="action" />
                <input type="hidden" value="" name="action_argument" id="action_argument" />

                <ul class="subsubsub">
                    <li><a class="<?php echo ($star === null && $read === null) ? "current" : "" ?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>"><?php _e("All", "gravityforms"); ?> <span class="count">(<span id="all_count"><?php echo $total_lead_count ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $read !== null ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&read=0"><?php _e("Unread", "gravityforms"); ?> <span class="count">(<span id="unread_count"><?php echo $unread_count ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $star !== null ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&star=1"><?php _e("Starred", "gravityforms"); ?> <span class="count">(<span id="star_count"><?php echo $starred_count ?></span>)</span></a></li>
                </ul>
                <p class="search-box">
                    <label class="hidden" for="lead_search"><?php _e("Search Posts:", "gravityforms"); ?></label>
                    <input type="text" id="lead_search" value="<?php echo $search ?>"><a class="button" href="javascript:Search('<?php echo $sort_field ?>', '<?php echo $sort_direction ?>', <?php echo $form_id ?>, jQuery('#lead_search').val(), '<?php echo $star ?>', '<?php echo $read ?>');"><?php _e("Search", "gravityforms") ?></a>
                </p>
                <div class="tablenav">

                    <div class="alignleft actions" style="padding:8px 0 7px 0;">
                        <label class="hidden" for="bulk_action"> <?php _e("Bulk action", "gravityforms") ?></label>
                        <select name="bulk_action" id="bulk_action">
                            <option value=''><?php _e(" Bulk action ", "gravityforms") ?></option>

                            <?php if(self::current_user_can_any("gravityforms_delete_entries")){ ?>
                            <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                            <?php } ?>

                            <option value='mark_read'><?php _e("Mark as Read", "gravityforms") ?></option>
                            <option value='mark_unread'><?php _e("Mark as Unread", "gravityforms") ?></option>
                            <option value='add_star'><?php _e("Add Star", "gravityforms") ?></option>
                            <option value='remove_star'><?php _e("Remove Star", "gravityforms") ?></option>
                        </select>
                        <?php
                        $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="jQuery(\'#action\').val(\'bulk\');" />';
                        echo apply_filters("gform_entry_apply_button", $apply_button);
                        ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <label for="form_id"><?php _e("Select a Form","gravityforms") ?></label>
                        <select id="form_id" onchange="ChangeForm();">
                            <?php
                            $forms = RGFormsModel::get_forms();
                            foreach($forms as $current_form){
                                ?>
                                <option value="<?php echo $current_form->id ?>" <?php echo $current_form->id == $form_id ? "selected='selected'" : "" ?>>
                                    <?php echo $current_form->title ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <?php
                    //Displaying paging links if appropriate
                    if($page_links){
                        ?>
                        <div class="tablenav-pages">
                            <span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravityforms"), $first_item_index + 1, ($first_item_index + $page_size) > $lead_count ? $lead_count : $first_item_index + $page_size , $lead_count) ?></span>
                            <?php echo $page_links ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="clear"></div>
                </div>

                <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" style="vertical-align:middle;"><input type="checkbox" class="headercb" /></th>
                        <th scope="col" class="manage-column column-cb check-column" >&nbsp;</th>
                        <?php
                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            ?>
                            <th scope="col" class="manage-column" onclick="Search('<?php echo $field_id ?>', '<?php echo $dir ?>', <?php echo $form_id ?>, '<?php echo $search ?>', '<?php echo $star ?>', '<?php echo $read ?>');" style="cursor:pointer;"><?php echo self::escape_text($field_info["label"]) ?></th>
                            <?php
                        }
                        ?>
                        <th scope="col" align="right" width="50">
                            <a title="<?php _e("Select Columns" , "gravityforms") ?>" href="<?php echo self::get_base_url() ?>/select_columns.php?id=<?php echo $form_id ?>&TB_iframe=true&height=365&width=600" class="thickbox entries_edit_icon">Edit</a>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" >&nbsp;</th>
                        <?php
                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            ?>
                            <th scope="col" class="manage-column" onclick="Search('<?php echo $field_id ?>', '<?php echo $dir ?>', <?php echo $form_id ?>, '<?php echo $search ?>', '<?php echo $star ?>', '<?php echo $read ?>');" style="cursor:pointer;"><?php echo self::escape_text($field_info["label"]) ?></th>
                            <?php
                        }
                        ?>
                        <th scope="col" style="width:15px;">
                            <a href="<?php echo self::get_base_url() ?>/select_columns.php?id=<?php echo $form_id ?>&TB_iframe=true&height=350&width=500" class="thickbox entries_edit_icon">Edit</a>
                        </th>
                    </tr>
                </tfoot>

                <tbody class="list:user user-list">
                    <?php
                    if(sizeof($leads) > 0){
                        $field_ids = array_keys($columns);

                        foreach($leads as $lead){
                            ?>
                            <tr id="lead_row_<?php echo $lead["id"] ?>" class='author-self status-inherit <?php echo $lead["is_read"] ? "" : "lead_unread" ?>' valign="top">
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="lead[]" value="<?php echo $lead["id"] ?>" />
                                </th>
                                <td >
                                    <img src="<?php echo self::get_base_url() ?>/images/star<?php echo intval($lead["is_starred"]) ?>.png" onclick="ToggleStar(this, <?php echo $lead["id"] ?>);" />
                                </td>
                                <?php
                                $is_first_column = true;
                                $nowrap_class = "";
                                foreach($field_ids as $field_id){
                                    $value = $lead[$field_id];
                                    switch($columns[$field_id]["type"]){
                                        case "checkbox" :
                                            $value = "";

                                            //looping through lead detail values trying to find an item identical to the column label. Mark with a tick if found.
                                            $lead_field_keys = array_keys($lead);
                                            foreach($lead_field_keys as $input_id){
                                                //mark as a tick if input label (from form meta) is equal to submitted value (from lead)
                                                if(is_numeric($input_id) && absint($input_id) == absint($field_id) && $lead[$input_id] == $columns[$field_id]["label"]){
                                                    $value = "<img src='" . self::get_base_url() . "/images/tick.png'/>";
                                                }
                                            }
                                        break;

                                        case "post_image" :
                                            list($url, $title, $caption, $description) = explode("|:|", $value);
                                            if(!empty($url)){
                                                //displaying thumbnail (if file is an image) or an icon based on the extension
                                                $thumb = self::get_icon_url($url);
                                                $value = "<a href='$url' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
                                            }
                                        break;

                                        case "fileupload" :
                                            $file_path = $value;
                                            if(!empty($file_path)){
                                                //displaying thumbnail (if file is an image) or an icon based on the extension
                                                $thumb = self::get_icon_url($file_path);
                                                $value = "<a href='$file_path' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
                                            }
                                        break;

                                        case "source_url" :
                                            $value = "<a href='" . self::escape_attribute($lead["source_url"]) . "' target='_blank' alt='" . self::escape_attribute($lead["source_url"]) ."' title='" . self::escape_attribute($lead["source_url"]) . "'>.../" . self::escape_attribute(self::truncate_url($lead["source_url"])) . "</a>";
                                        break;

                                        case "textarea" :
                                        case "post_content" :
                                        case "post_excerpt" :
                                            $nowrap_class="entry_nowrap";
                                            $value = self::escape_text($value);
                                        break;

                                        case "date_created" :
                                            $value = self::format_date($value, false);// date('Y/m/d \a\t H:i', mysql2date('G', $value));
                                        break;

                                        case "date" :
                                            $field = RGFormsModel::get_field($form, $field_id);
                                            $value = self::date_display($value, $field["dateFormat"]);
                                        break;

                                        default:
                                            $value = self::escape_text($value);
                                    }

                                    if($is_first_column){
                                        ?>
                                        <td class="column-title" >
                                            <a href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs?>&paged=<?php echo ($page_index + 1)?>"><?php echo $value ?></a>
                                            <div class="row-actions">
                                                <span class="edit">
                                                    <a title="<?php _e("View this entry", "gravityforms"); ?>" href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs?>&paged=<?php echo ($page_index + 1)?>"><?php _e("View", "gravityforms"); ?></a>
                                                    |
                                                </span>
                                                <span class="edit">
                                                    <a id="mark_read_<?php echo $lead["id"] ?>" title="Mark this entry as read" href="javascript:ToggleRead(<?php echo $lead["id"] ?>);" style="display:<?php echo $lead["is_read"] ? "none" : "inline" ?>;"><?php _e("Mark read", "gravityforms"); ?></a><a id="mark_unread_<?php echo $lead["id"] ?>" title="<?php _e("Mark this entry as unread", "gravityforms"); ?>" href="javascript:ToggleRead(<?php echo $lead["id"] ?>);" style="display:<?php echo $lead["is_read"] ? "inline" : "none" ?>;"><?php _e("Mark unread", "gravityforms"); ?></a>
                                                    <?php echo self::current_user_can_any("gravityforms_delete_entries") ? "|" : "" ?>
                                                </span>

                                                <?php if(self::current_user_can_any("gravityforms_delete_entries")){ ?>
                                                <span class="edit">
                                                    <?php
                                                    $delete_link ='<a title="' . __("Delete this entry", "gravityforms"). '"  href="javascript:if ( confirm(' . __("'You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.'", "gravityforms"). ') ) { DeleteLead(' . $lead["id"] .')};">' . __("Delete", "gravityforms") .'</a>';
                                                    echo apply_filters("gform_delete_entry_link", $delete_link);
                                                    ?>
                                                </span>
                                                <?php } ?>

                                            </div>
                                        </td>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <td class="<?php echo $nowrap_class ?>">
                                            <?php echo $value ?>&nbsp;
                                        </td>
                                        <?php

                                    }
                                    $is_first_column = false;
                                }
                                ?>
                                <td>&nbsp;</td>
                            </tr>
                            <?php
                        }
                    }
                    else{
                        ?>
                        <tr>
                            <td colspan="<?php echo sizeof($columns) + 3 ?>" style="padding:20px;"><?php _e("This form does not have any entries yet.", "gravityforms"); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                </table>

                <div class="clear"></div>

                <div class="tablenav">

                    <div class="alignleft actions" style="padding:8px 0 7px 0;">
                        <label class="hidden" for="bulk_action2"> <?php _e("Bulk action", "gravityforms") ?></label>
                        <select name="bulk_action2" id="bulk_action2">
                        <option value=''><?php _e("Bulk action ", "gravityforms") ?></option>
                            <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                            <option value='mark_read'><?php _e("Mark as Read", "gravityforms") ?></option>
                            <option value='mark_unread'><?php _e("Mark as Unread", "gravityforms") ?></option>
                            <option value='add_star'><?php _e("Add Star", "gravityforms") ?></option>
                            <option value='remove_star'><?php _e("Remove Star", "gravityforms") ?></option>
                        </select>
                        <?php
                        $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="jQuery(\'#action\').val(\'bulk\');" />';
                        echo apply_filters("gform_entry_apply_button", $apply_button);
                        ?>
                    </div>

                    <?php
                    //Displaying paging links if appropriate
                    if($page_links){
                        ?>
                        <div class="tablenav-pages">
                            <span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravityforms"), $first_item_index + 1, ($first_item_index + $page_size) > $lead_count ? $lead_count : $first_item_index + $page_size , $lead_count) ?></span>
                            <?php echo $page_links ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="clear"></div>
                </div>

            </form>
        </div>
        <?php


    }

    public static function form_list_page(){
        global $wpdb;

        if(!self::ensure_wp_version())
            return;

        echo self::get_remote_message();

        $action = $_POST["action"];
        $bulk_action = !empty($_POST["bulk_action"]) ? $_POST["bulk_action"] : $_POST["bulk_action2"];

        if($action == "delete")
        {
            check_admin_referer('gforms_update_forms', 'gforms_update_forms');
            $form_id = $_POST["action_argument"];
            RGFormsModel::delete_form($form_id);
        }
        else if($action == "duplicate"){
            check_admin_referer('gforms_update_forms', 'gforms_update_forms');
            $form_id = $_POST["action_argument"];
            RGFormsModel::duplicate_form($form_id);
        }
        else if($bulk_action == "delete"){
            check_admin_referer('gforms_update_forms', 'gforms_update_forms');
            $form_ids = $_POST["form"];
            RGFormsModel::delete_forms($form_ids);
        }

        $active = $_GET["active"];
        $forms = RGFormsModel::get_forms($active);
        $form_count = RGFormsModel::get_form_count();

        ?>
        <script>
            function DeleteForm(form_id){
                jQuery("#action_argument").val(form_id);
                jQuery("#action").val("delete");
                jQuery("#forms_form")[0].submit();
            }

            function DuplicateForm(form_id){
                jQuery("#action_argument").val(form_id);
                jQuery("#action").val("duplicate");
                jQuery("#forms_form")[0].submit();
            }

            function ToggleActive(img, form_id){
                var is_active = img.src.indexOf("active1.png") >=0
                if(is_active){
                    img.src = img.src.replace("active1.png", "active0.png");
                    jQuery(img).attr('title','<?php _e("Inactive", "gravityforms") ?>').attr('alt', '<?php _e("Inactive", "gravityforms") ?>');
                }
                else{
                    img.src = img.src.replace("active0.png", "active1.png");
                    jQuery(img).attr('title','<?php _e("Active", "gravityforms") ?>').attr('alt', '<?php _e("Active", "gravityforms") ?>');
                }

                UpdateCount("active_count", is_active ? -1 : 1);
                UpdateCount("inactive_count", is_active ? 1 : -1);

                var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "rg_update_form_active" );
                mysack.setVar( "rg_update_form_active", "<?php echo wp_create_nonce("rg_update_form_active") ?>" );
                mysack.setVar( "form_id", form_id);
                mysack.setVar( "is_active", is_active ? 0 : 1);
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() { alert('<?php _e("Ajax error while update form", "gravityforms") ?>' )};
                mysack.runAJAX();

                return true;
            }
            function UpdateCount(element_id, change){
                var element = jQuery("#" + element_id);
                var count = parseInt(element.html()) + change
                element.html(count + "");
            }
        </script>

        <link rel="stylesheet" href="<?php echo self::get_base_url()?>/css/admin.css" />
        <div class="wrap">
            <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo self::get_base_url()?>/images/gravity-title-icon-32.png" style="float:left; margin:15px 7px 0 0;"/>
            <h2><?php _e("Edit Forms", "gravityforms"); ?></h2>
            <form id="forms_form" method="post">
                <?php wp_nonce_field('gforms_update_forms', 'gforms_update_forms') ?>
                <input type="hidden" id="action" name="action"/>
                <input type="hidden" id="action_argument" name="action_argument"/>

                <ul class="subsubsub">
                    <li><a class="<?php echo ($active === null) ? "current" : "" ?>" href="?page=gf_edit_forms"><?php _e("All", "gravityforms"); ?> <span class="count">(<span id="all_count"><?php echo $form_count["total"] ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $active == "1" ? "current" : ""?>" href="?page=gf_edit_forms&active=1"><?php _e("Active", "gravityforms"); ?> <span class="count">(<span id="active_count"><?php echo $form_count["active"] ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $active == "0" ? "current" : ""?>" href="?page=gf_edit_forms&active=0"><?php _e("Inactive", "gravityforms"); ?> <span class="count">(<span id="inactive_count"><?php echo $form_count["inactive"] ?></span>)</span></a></li>
                </ul>

                <?php
                if(self::current_user_can_any("gravityforms_delete_forms")){
                ?>
                    <div class="tablenav">
                        <div class="alignleft actions" style="padding:8px 0 7px; 0">

                            <label class="hidden" for="bulk_action"><?php _e("Bulk action", "gravityforms") ?></label>
                            <select name="bulk_action" id="bulk_action">
                                <option value=''> <?php _e("Bulk action", "gravityforms") ?> </option>
                                <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                            </select>
                            <?php
                            $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="if( jQuery(\'#bulk_action\').val() == \'delete\' && !confirm(\'' . __("WARNING: You are about to delete this form and ALL entries associated with it. ", "gravityforms") . __("\'Cancel\' to stop, \'OK\' to delete.", "gravityforms") .'\')) { return false; } return true;"/>';
                            echo apply_filters("gform_form_apply_button", $apply_button);
                            ?>

                            <br class="clear"></div>
                        </div>
                    </div>
                <?php
                }
                ?>

                <table class="widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <?php
                            if(self::current_user_can_any("gravityforms_delete_forms")){
                            ?>
                                <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" name="form_bulk_check_all" onclick="jQuery('.gform_list_checkbox').attr('checked', this.checked);" /></th>
                            <?php
                            }
                            ?>
                            <th scope="col" id="active" class="manage-column column-cb check-column"></th>
                            <th scope="col" id="id" class="manage-column" style="width:50px;"><?php _e("Id", "gravityforms") ?></th>
                            <th width="360" scope="col" id="title" class="manage-column column-title"><?php _e("Title", "gravityforms") ?></th>
                            <th scope="col" id="author" class="manage-column column-author" style=""><?php _e("Views", "gravityforms") ?></th>
                            <th scope="col" id="template" class="manage-column" style=""><?php _e("Entries", "gravityforms") ?></th>
                            <th scope="col" id="template" class="manage-column" style=""><?php _e("Conversion", "gravityforms") ?> <?php gform_tooltip("entries_conversion") ?> </th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <?php
                            if(self::current_user_can_any("gravityforms_delete_forms")){
                            ?>
                                <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" name="form_bulk_check_all" onclick="jQuery('.gform_list_checkbox').attr('checked', this.checked);" /></th>
                            <?php
                            }
                            ?>
                            <th scope="col" id="active" class="manage-column column-cb check-column"></th>
                            <th scope="col" id="id" class="manage-column"><?php _e("Id", "gravityforms") ?></th>
                            <th width="350" scope="col" id="title" class="manage-column column-title"><?php _e("Title", "gravityforms") ?></th>
                            <th scope="col" id="author" class="manage-column column-author" style=""><?php _e("Views", "gravityforms") ?></th>
                            <th scope="col" id="template" class="manage-column" style=""><?php _e("Entries", "gravityforms") ?></th>
                            <th scope="col" id="template" class="manage-column" style=""><?php _e("Conversion", "gravityforms") ?></th>
                        </tr>
                    </tfoot>

                    <tbody class="list:user user-list">
                        <?php
                        if(sizeof($forms) > 0){
                            foreach($forms as $form){
                                $conversion = "0%";
                                if($form->view_count > 0){
                                    $conversion = (number_format($form->lead_count / $form->view_count, 3) * 100) . "%";
                                }
                                ?>
                                <tr class='author-self status-inherit' valign="top">
                                    <?php
                                    if(self::current_user_can_any("gravityforms_delete_forms")){
                                    ?>
                                        <th scope="row" class="check-column"><input type="checkbox" name="form[]" value="<?php echo $form->id ?>" class="gform_list_checkbox"/></th>
                                    <?php
                                    }
                                    ?>

                                    <td><img src="<?php echo self::get_base_url() ?>/images/active<?php echo intval($form->is_active) ?>.png" alt="<?php echo $form->is_active ? __("Active", "gravityforms") : __("Inactive", "gravityforms");?>" title="<?php echo $form->is_active ? __("Active", "gravityforms") : __("Inactive", "gravityforms");?>" onclick="ToggleActive(this, <?php echo $form->id ?>); " /></td>
                                    <td class="column-id"><?php echo $form->id ?></td>
                                    <td class="column-title">
                                        <strong><a class="row-title" href="admin.php?page=gf_edit_forms&id=<?php echo $form->id ?>" title="<?php _e("Edit", "gravityforms") ?>"><?php echo $form->title ?></a></strong>
                                        <div class="row-actions">
                                            <span class="edit">
                                            <a title="Edit this form" href="admin.php?page=gf_edit_forms&id=<?php echo $form->id ?>"><?php _e("Edit", "gravityforms"); ?></a>
                                            |
                                            </span>
                                            <span class="edit">
                                            <a title="<?php _e("Preview this form", "gravityforms"); ?>" href="<?php echo self::get_base_url() ?>/preview.php?id=<?php echo $form->id ?>" target="_blank"><?php _e("Preview", "gravityforms"); ?></a>
                                            |
                                            </span>

                                            <?php
                                            if(self::current_user_can_any("gravityforms_view_entries")){
                                            ?>
                                                <span class="edit">
                                                <a title="<?php _e("View entries generated by this form", "gravityforms"); ?>" href="admin.php?page=gf_entries&view=entries&id=<?php echo $form->id ?>"><?php _e("Entries", "gravityforms"); ?></a>
                                                |
                                                </span>
                                            <?php
                                            }
                                            ?>

                                            <span class="edit">
                                            <a title="<?php _e("Edit notifications sent by this form", "gravityforms"); ?>" href="admin.php?page=gf_edit_forms&view=notification&id=<?php echo $form->id ?>"><?php _e("Notifications", "gravityforms"); ?></a>
                                            <?php echo self::current_user_can_any("gravityforms_create_form") || self::current_user_can_any("gravityforms_delete_forms") ? "|" : "" ?>
                                            </span>

                                            <?php
                                            if(self::current_user_can_any("gravityforms_create_form")){
                                            ?>
                                                <span class="edit">
                                                <a title="<?php _e("Duplicate this form", "gravityforms"); ?>" href="javascript:DuplicateForm(<?php echo $form->id ?>);"><?php _e("Duplicate", "gravityforms"); ?></a>
                                                <?php echo self::current_user_can_any("gravityforms_delete_forms") ? "|" : "" ?>
                                                </span>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if(self::current_user_can_any("gravityforms_delete_forms")){
                                            ?>
                                                <span class="edit">
                                                <?php
                                                $delete_link = '<a title="Delete" href="javascript: if(confirm(\'' . __("WARNING: You are about to delete this form and ALL entries associated with it. ", "gravityforms") . __("\'Cancel\' to stop, \'OK\' to delete.", "gravityforms") . '\')){ DeleteForm(' . $form->id . ');}">' . __("Delete", "gravityforms"). '</a>';
                                                echo apply_filters("gform_form_delete_link", $delete_link);
                                                ?>
                                                </span>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="column-date"><strong><?php echo $form->view_count ?></strong></td>
                                    <td class="column-date"><strong><?php echo $form->lead_count ?></strong></td>
                                    <td class="column-date"><?php echo $conversion?></td>
                                </tr>
                                <?php
                            }
                        }
                        else{
                            ?>
                            <tr>
                                <td colspan="6" style="padding:20px;">
                                    <?php _e(sprintf("You don't have any forms. Let's go %screate one%s!", '<a href="admin.php?page=gf_new_form">', "</a>"), "gravityforms"); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="tablenav">
                    <div class="alignleft actions" style="padding:8px 0 7px; 0">
                        <?php
                        if(self::current_user_can_any("gravityforms_delete_forms")){
                            ?>
                            <label class="hidden" for="bulk_action2"><?php _e("Bulk action", "gravityforms") ?></label>
                            <select name="bulk_action2" id="bulk_action2">
                                <option value=''> <?php _e("Bulk action", "gravityforms") ?> </option>
                                <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                            </select>
                            <?php
                            $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="if( jQuery(\'#bulk_action2\').val() == \'delete\' && !confirm(\'' . __("WARNING: You are about to delete this form and ALL entries associated with it. ", "gravityforms") . __("\'Cancel\' to stop, \'OK\' to delete.", "gravityforms") .'\')) { return false; } return true;"/>';
                            echo apply_filters("gform_form_apply_button", $apply_button);
                        }
                        ?>
                        <br class="clear" />
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public static function save_form(){
        global $wpdb;
        check_ajax_referer('rg_save_form', 'rg_save_form');
        $id = $_POST["id"];

        $form_json = $_POST["form"];
        $form_json = stripslashes($form_json);

        $form_meta = self::json_decode($form_json, true);
        $form_table_name =  $wpdb->prefix . "rg_form";
        $meta_table_name =  $wpdb->prefix . "rg_form_meta";

        //Making sure title is not duplicate
        $forms = RGFormsModel::get_forms();
        foreach($forms as $form)
            if(strtolower($form->title) == strtolower($form_meta["title"]) && $form_meta["id"] != $form->id)
                die('DuplicateTitleMessage();');

        if($id > 0){
            RGFormsModel::update_form_meta($id, $form_meta);

            //updating form title
            $wpdb->query($wpdb->prepare("UPDATE $form_table_name SET title=%s WHERE id=%d", $form_meta["title"], $form_meta["id"]));

            die("EndUpdateForm($id);");
        }
        else{
            //inserting form
            $id = RGFormsModel::insert_form($form_meta["title"]);

            //updating object's id property
            $form_meta["id"] = $id;

            //updating form meta
            RGFormsModel::update_form_meta($id, $form_meta);

            die("EndInsertForm($id);");
        }
    }

    public static function add_field(){
        check_ajax_referer("rg_add_field", "rg_add_field");
        $field_json = stripslashes_deep($_POST["field"]);
        $field = self::json_decode($field_json, true);
        $field_html = self::get_field($field, "", true);

        die("EndAddField($field_json, \"$field_html\");");
    }

    public static function delete_field(){
        check_ajax_referer("rg_delete_field", "rg_delete_field");
        $form_id =  intval($_POST["form_id"]);
        $field_id =  intval($_POST["field_id"]);

        RGFormsModel::delete_field($form_id, $field_id);
        die("EndDeleteField($field_id);");
    }

    public static function delete_file(){
        check_ajax_referer("rg_delete_file", "rg_delete_file");
        $lead_id =  intval($_POST["lead_id"]);
        $field_id =  intval($_POST["field_id"]);

        RGFormsModel::delete_file($lead_id, $field_id);
        die("EndDeleteFile($field_id);");
    }

    public static function select_export_form(){
        check_ajax_referer("rg_select_export_form", "rg_select_export_form");
        $form_id =  intval($_POST["form_id"]);
        $form = RGFormsModel::get_form_meta($form_id);
        $fields = array();

        //Adding default fields
        array_push($form["fields"],array("id" => "date_created" , "label" => __("Entry Date", "gravityforms")));
        array_push($form["fields"],array("id" => "ip" , "label" => __("User IP", "gravityforms")));
        array_push($form["fields"],array("id" => "source_url" , "label" => __("Source Url", "gravityforms")));

        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){
                if(is_array($field["inputs"])){
                    foreach($field["inputs"] as $input)
                        $fields[] =  array($input["id"], RGForms::get_label($field, $input["id"]));
                }
                else if(!$field["displayOnly"]){
                    $fields[] =  array($field["id"], self::get_label($field));
                }
            }
        }
        $field_json = self::json_encode($fields);

        die("EndSelectExportForm($field_json);");
    }

    public static function change_field_type(){
        check_ajax_referer('rg_change_field_type','rg_change_field_type');
        $field_json = stripslashes_deep($_POST["field"]);
        $field = self::json_decode($field_json, true);
        $id = $field["id"];
        $type = $field["type"];

        $field_content = self::get_field_content($field);

        die("EndChangeFieldType('$id', '$type', \"$field_content\");");
    }

    public static function get_field($field, $value="", $force_frontend_label = false){
        if(!IS_ADMIN && $field["adminOnly"])
        {
            if($field["allowsPrepopulate"])
                $field["type"] = "adminonly_hidden";
            else
                return;
        }

        $id = $field["id"];
        $type = $field["type"];
        $error_class = $field["failed_validation"] ? "gfield_error" : "";
        $custom_class = $field["cssClass"];
        $admin_only_class =  $field["adminOnly"] ? "field_admin_only" : "";
        $selectable_class = IS_ADMIN ? "selectable" : "";

        $section_class = $field["type"] == "section" ? "gsection" : "";
        $css_class = "$selectable_class gfield $error_class $section_class $admin_only_class $custom_class";
        $css_class = trim($css_class);

        return "<li id='field_$id' class='$css_class'>" . self::get_field_content($field, $value, $force_frontend_label) . "</li>";
    }

    public static function get_field_content($field, $value="", $force_frontend_label = false){
        $id = $field["id"];
        $size = $field["size"];
        $validation_message = ($field["failed_validation"] && !empty($field["validation_message"])) ? sprintf("<div class='gfield_description validation_message'>%s</div>", $field["validation_message"]) : "";

        $delete_field_link = "<a class='field_delete_icon' id='gfield_delete_$id' title='" . __("click to delete this field", "gravityforms") . "' href='javascript:void(0);' onclick='StartDeleteField(this);'>" . __("Delete", "gravityforms") . "</a>";
        $delete_field_link = apply_filters("gform_delete_field_link", $delete_field_link);

        $admin_buttons = IS_ADMIN ? $delete_field_link . " <a class='field_edit_icon edit_icon_collapsed' href='javascript:void(0);' title='" . __("click to edit this field", "gravityforms") . "'>" . __("Edit", "gravityforms") . "</a>" : "";

        $field_label = $force_frontend_label ? $field["label"] : self::get_label($field);

        switch($field["type"]){
            case "section" :
                $description = self::get_description($field["description"], "gsection_description");
                $field_content = sprintf("%s<h2 class='gsection_title'>%s</h2>$description", $admin_buttons,  self::escape_text($field_label));
            break;

            case "adminonly_hidden":
            case "hidden" :
                $field_content = !IS_ADMIN ? "{FIELD}" : $field_content = sprintf("%s<label class='gfield_label' for='input_%d'>%s</label>{FIELD}", $admin_buttons, $id, self::escape_text($field_label));
            break;
            default :
                $description = self::get_description($field["description"],"gfield_description");
                $field_content = sprintf("%s<label class='gfield_label' for='input_%d'>%s<span class='gfield_required'>%s</span></label>{FIELD}$description%s", $admin_buttons, $id, self::escape_text($field_label), $field["isRequired"] ? "*" : "", $validation_message);
            break;
        }

        if(empty($value))
            $value = $field["defaultValue"];

        $field_content = str_replace("{FIELD}", self::get_field_input($field, $value), $field_content);

        return $field_content;
    }

    private static function get_description($description, $css_class){
        return IS_ADMIN || !empty($description) ? "<div class='$css_class'>" . $description . "</div>" : "";
    }

    public static function get_field_input($field, $value="", $lead_id=0){

        $id = $field["id"];
        $size = $field["size"];
        $disabled_text = (IS_ADMIN && RG_CURRENT_VIEW != "entry") ? "disabled='disabled'" : "";
        $class_suffix = RG_CURRENT_VIEW == "entry" ? "_admin" : "";
        $class = $size . $class_suffix;

        if(RG_CURRENT_VIEW == "entry"){
            $lead = RGFormsModel::get_lead($lead_id);
            $post_id = $lead["post_id"];
            if(is_numeric($post_id)){
                $post_link = "You can <a href='post.php?action=edit&post=$post_id'>edit this post</a> from the post page.";
            }
        }
        switch($field["type"]){

            case "website":
                $value = empty($value) ? "http://" : $value;
            case "text":
            case "email":
                return sprintf("<div class='ginput_container'><input name='input_%d' id='input_%d' type='text' value='%s' class='%s' tabindex='%d' %s/></div>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), self::$tab_index++, $disabled_text);
            break;

            case "hidden" :
                $field_type = IS_ADMIN ? "text" : "hidden";
                $class_attribute = IS_ADMIN ? "" : "class='gform_hidden'";

                return sprintf("<input name='input_%d' id='input_%d' type='$field_type' $class_attribute value='%s' %s/>", $id, $id, self::escape_attribute($value), $disabled_text);
            break;

            case "adminonly_hidden" :
                if(!is_array($field["inputs"]))
                    return sprintf("<input name='input_%d' id='input_%d' class='gform_hidden' type='hidden' value='%s'/>", $id, $id, self::escape_attribute($value));

                $fields = "";
                foreach($field["inputs"] as $input){
                    $fields .= sprintf("<input name='input_%s' id='input_%s' class='gform_hidden' type='hidden' value='%s'/>", $input["id"], $input["id"], self::escape_attribute($value[$input["id"]]));
                }
                return $fields;
            break;

            case "number" :
                if(!IS_ADMIN){
                    $min = $field["rangeMin"];
                    $max = $field["rangeMax"];
                    $validation_class = $field["failed_validation"] ? "validation_message" : "";

                    if(is_numeric($min) && is_numeric($max))
                        $instruction = "<div class='instruction $validation_class'>" . __(sprintf("Please enter a value between %s and %s.", "<strong>$min</strong>", "<strong>$max</strong>"), "gravityforms")."</div>";
                    else if(is_numeric($min))
                        $instruction = "<div class='instruction $validation_class'>" . __(sprintf("Please enter a value greater than or equal to %s.", "<strong>$min</strong>"), "gravityforms")."</div>";
                    else if(is_numeric($max))
                        $instruction = "<div class='instruction $validation_class'>" . __(sprintf("Please enter a value less than or equal to %s.", "<strong>$max</strong>"), "gravityforms")."</div>";
                    else
                        $instruction = "";
                }
                return sprintf("<div class='ginput_container'><input name='input_%d' id='input_%d' type='text' value='%s' class='%s' tabindex='%d' %s/>%s</div>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), self::$tab_index++, $disabled_text, $instruction);

            case "phone" :
                $validation_class = $field["failed_validation"] ? "validation_message" : "";
                $instruction = $field["phoneFormat"] == "standard" ? __("Phone format:", "gravityforms") . " (###)###-####" : "";

                if(IS_ADMIN)
                    $instruction_div = empty($instruction) ? "<div class='instruction' style='display:none'>$instruction</div>" : "<div class='instruction'>$instruction</div>";
                else
                    $instruction_div = empty($instruction) ? "" : "<div class='instruction $validation_class'>$instruction</div>";

                return sprintf("<div class='ginput_container'><input name='input_%d' id='input_%d' type='text' value='%s' class='%s' tabindex='%d' %s/>$instruction_div</div>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), self::$tab_index++, $disabled_text);

            case "textarea":
                return sprintf("<div class='ginput_container'><textarea name='input_%d' id='input_%d' class='textarea %s' tabindex='%d' %s rows='10' cols='50'>%s</textarea></div>", $id, $id, self::escape_attribute($class), self::$tab_index++, $disabled_text, self::escape_text($value));

            case "post_title":
            case "post_tags":
            case "post_custom_field":
                return RG_CURRENT_VIEW == "entry" ? $post_link : sprintf("<div><input name='input_%d' id='input_%d' type='text' value='%s' class='%s' tabindex='%d' %s/></div>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), self::$tab_index++, $disabled_text);
            break;

            case "post_content":
            case "post_excerpt":
                return RG_CURRENT_VIEW == "entry" ? $post_link : sprintf("<div><textarea name='input_%d' id='input_%d' class='textarea %s' tabindex='%d' %s rows='10' cols='50'>%s</textarea></div>", $id, $id, self::escape_attribute($class), self::$tab_index++, $disabled_text, self::escape_text($value));
            break;

            case "post_category" :
                if(RG_CURRENT_VIEW == "entry")
                    return $post_link;

                if($field["displayAllCategories"] && !IS_ADMIN){
                    $selected = empty($value) ? get_option('default_category') : $value;
                    return "<div class='ginput_container'>" . wp_dropdown_categories(array('echo' => 0, 'selected' => $selected, "class" => self::escape_attribute($class) . " gfield_select", "tab_index" =>  self::$tab_index++,  'hide_empty' => 0, 'name' => "input_$id", 'orderby' => 'name', 'hierarchical' => true )) . "</div>";
                }
                else
                    return sprintf("<div class='ginput_container'><select name='input_%d' id='input_%d' class='%s gfield_select' tabindex='%d' %s>%s</select></div>", $id, $id, self::escape_attribute($class), self::$tab_index++, $disabled_text, self::get_select_choices($field, $value));
            break;

            case "post_image" :
                if(RG_CURRENT_VIEW == "entry")
                    return $post_link;

                $title = self::escape_attribute($value[$field["id"] . ".1"]);
                $caption = self::escape_attribute($value[$field["id"] . ".4"]);
                $description = self::escape_attribute($value[$field["id"] . ".7"]);

                //hidding meta fields for admin
                $hidden_style = "style='display:none;'";
                $title_style = !$field["displayTitle"] && IS_ADMIN ? $hidden_style : "";
                $caption_style = !$field["displayCaption"] && IS_ADMIN ? $hidden_style : "";
                $description_style = !$field["displayDescription"] && IS_ADMIN ? $hidden_style : "";
                $file_label_style = IS_ADMIN && !($field["displayTitle"] || $field["displayCaption"] || $field["displayDescription"]) ? $hidden_style : "";

                //in admin, render all meta fields to allow for immediate feedback, but hide the ones not selected
                $file_label = (IS_ADMIN || $field["displayTitle"] || $field["displayCaption"] || $field["displayDescription"]) ? "<label for='input_$id' class='ginput_post_image_file' $file_label_style>" . apply_filters("gform_postimage_file",__("File", "gravityforms")) . "</label>" : "";
                $upload = sprintf("<span class='ginput_full$class_suffix'><input name='input_%d' id='input_%d' type='file' value='%s' class='%s' tabindex='%d' %s/>$file_label</span>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), self::$tab_index++, $disabled_text);
                $title_field = $field["displayTitle"] || IS_ADMIN ? sprintf("<span class='ginput_full$class_suffix ginput_post_image_title' $title_style><input type='text' name='input_%d.1' id='input_%d.1' value='%s' tabindex='%d' %s/><label for='input_%d.1'>" . apply_filters("gform_postimage_title",__("Title", "gravityforms")) . "</label></span>", $id, $id, $title, self::$tab_index++, $disabled_text, $id) : "";
                $caption_field = $field["displayCaption"] || IS_ADMIN ? sprintf("<span class='ginput_full$class_suffix ginput_post_image_caption' $caption_style><input type='text' name='input_%d.4' id='input_%d.4' value='%s' tabindex='%d' %s/><label for='input_%d.4'>" . apply_filters("gform_postimage_title",__("Caption", "gravityforms")) . "</label></span>", $id, $id, $caption, self::$tab_index++, $disabled_text, $id) : "";
                $description_field = $field["displayDescription"] || IS_ADMIN? sprintf("<span class='ginput_full$class_suffix ginput_post_image_description' $description_style><input type='text' name='input_%d.7' id='input_%d.7' value='%s' tabindex='%d' %s/><label for='input_%d.7'>" . apply_filters("gform_postimage_title",__("Description", "gravityforms")) . "</label></span>", $id, $id, $description, self::$tab_index++, $disabled_text, $id) : "";

                return "<div class='ginput_complex$class_suffix ginput_container'>" . $upload . $title_field . $caption_field . $description_field . "</div>";

            break;
            case "select" :
                return sprintf("<div class='ginput_container'><select name='input_%d' id='input_%d' class='%s gfield_select' tabindex='%d' %s>%s</select></div>", $id, $id, self::escape_attribute($class), self::$tab_index++, $disabled_text, self::get_select_choices($field, $value));

            case "checkbox" :
                return sprintf("<div class='ginput_container'><ul class='gfield_checkbox' id='input_%d'>%s</ul></div>", $id, self::get_checkbox_choices($field, $value, $disabled_text));

            case "radio" :
                return sprintf("<div class='ginput_container'><ul class='gfield_radio' id='input_%d'>%s</ul></div>", $id, self::get_radio_choices($field, $value, $disabled_text));

            case "name" :
                if(is_array($value)){
                    $prefix = self::escape_attribute($value[$field["id"] . ".2"]);
                    $first = self::escape_attribute($value[$field["id"] . ".3"]);
                    $last = self::escape_attribute($value[$field["id"] . ".6"]);
                    $suffix = self::escape_attribute($value[$field["id"] . ".8"]);
                }
                switch($field["nameFormat"]){

                    case "extended" :
                        return sprintf("<div class='ginput_complex$class_suffix ginput_container' id='input_$id'><span class='name_prefix'><input type='text' name='input_%d.2' id='input_%d.2' value='%s' tabindex='%d' %s/><label for='input_%d.2'>" . apply_filters("gform_name_prefix",__("Prefix", "gravityforms")) . "</label></span><span class='name_first'><input type='text' name='input_%d.3' id='input_%d.3' value='%s' tabindex='%d' %s/><label for='input_%d.3'>" . apply_filters("gform_name_first",__("First", "gravityforms")) . "</label></span><span class='name_last'><input type='text' name='input_%d.6' id='input_%d.6' value='%s' tabindex='%d' %s/><label for='input_%d.6'>" . apply_filters("gform_name_last", __("Last", "gravityforms")) . "</label></span><span class='name_suffix'><input type='text' name='input_%d.8' id='input_%d.8' value='%s' tabindex='%d' %s/><label for='input_%d.8'>" . apply_filters("gform_name_suffix", __("Suffix", "gravityforms")) . "</label></span></div>", $id, $id, $prefix, self::$tab_index++, $disabled_text, $id, $id, $id, $first, self::$tab_index++, $disabled_text, $id, $id, $id, $last, self::$tab_index++, $disabled_text, $id, $id, $id, $suffix, self::$tab_index++, $disabled_text, $id);

                    case "simple" :
                        return sprintf("<div class='ginput_container'><input name='input_%d' id='input_%d' type='text' value='%s' class='%s' tabindex='%d' %s/></div>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), self::$tab_index++, $disabled_text);

                    default :
                        return sprintf("<div class='ginput_complex$class_suffix ginput_container' id='input_$id'><span class='ginput_left'><input type='text' name='input_%d.3' id='input_%d.3' value='%s' tabindex='%d' %s/><label for='input_%d.3'>" . apply_filters("gform_name_first",__("First", "gravityforms")) . "</label></span><span class='ginput_right'><input type='text' name='input_%d.6' id='input_%d.6' value='%s' tabindex='%d' %s/><label for='input_%d.6'>" . apply_filters("gform_name_last",__("Last", "gravityforms")) . "</label></span></div>", $id, $id, $first, self::$tab_index++, $disabled_text, $id, $id, $id, $last, self::$tab_index++, $disabled_text, $id);
                }

            case "address" :
                if(is_array($value)){
                    $street_value = self::escape_attribute($value[$field["id"] . ".1"]);
                    $street2_value = self::escape_attribute($value[$field["id"] . ".2"]);
                    $city_value = self::escape_attribute($value[$field["id"] . ".3"]);
                    $state_value = self::escape_attribute($value[$field["id"] . ".4"]);
                    $zip_value = self::escape_attribute($value[$field["id"] . ".5"]);
                    $country_value = self::escape_attribute($value[$field["id"] . ".6"]);
                }

                $country_list = self::get_country_dropdown($country_value);

                $street_address = sprintf("<span class='ginput_full$class_suffix'><input type='text' name='input_%d.1' id='input_%d.1' value='%s' tabindex='%d' %s/><label for='input_%d.1'>" . apply_filters("gform_address_street",__("Street Address", "gravityforms")) . "</label></span>", $id, $id, $street_value, self::$tab_index++, $disabled_text, $id);
                $street_address2 = sprintf("<span class='ginput_full$class_suffix'><input type='text' name='input_%d.2' id='input_%d.2' value='%s' tabindex='%d' %s/><label for='input_%d.2'>" . apply_filters("gform_address_street2",__("Address Line 2", "gravityforms")) . "</label></span>", $id, $id, $street2_value, self::$tab_index++, $disabled_text, $id);
                $city = sprintf("<span class='ginput_left$class_suffix'><input type='text' name='input_%d.3' id='input_%d.3' value='%s' tabindex='%d' %s/><label for='input_%d.3'>" . apply_filters("gform_address_city",__("City", "gravityforms")) . "</label></span>", $id, $id, $city_value, self::$tab_index++, $disabled_text, $id);
                $state = sprintf("<span class='ginput_right$class_suffix'><input type='text' name='input_%d.4' id='input_%d.4' value='%s' tabindex='%d' %s/><label for='input_%d.4'>" . apply_filters("gform_address_state",__("State / Province", "gravityforms")) . "</label></span>", $id, $id, $state_value, self::$tab_index++, $disabled_text, $id);
                $zip = sprintf("<span class='ginput_left$class_suffix'><input type='text' name='input_%d.5' id='input_%d.5' value='%s' tabindex='%d' %s/><label for='input_%d.5'>" . apply_filters("gform_address_zip",__("Zip / Postal Code", "gravityforms")) . "</label></span>", $id, $id, $zip_value, self::$tab_index++, $disabled_text, $id);
                $country = sprintf("<span class='ginput_right$class_suffix'><select name='input_%d.6' id='input_%d.6' tabindex='%d' %s>%s</select><label for='input_%d.6'>" . apply_filters("gform_address_country",__("Country", "gravityforms")) . "</label></span>", $id, $id, self::$tab_index++, $disabled_text, $country_list, $id);

                return "<div class='ginput_complex$class_suffix ginput_container' id='input_$id'>" . $street_address . $street_address2 . $city . $state . $zip . $country . "</div>";

            case "date" :
                $format = empty($field["dateFormat"]) ? "mdy" : self::escape_attribute($field["dateFormat"]);

                if(IS_ADMIN && RG_CURRENT_VIEW != "entry"){
                    $datepicker_display = $field["dateType"] == "datefield" ? "none" : "inline";
                    $dropdown_display = $field["dateType"] == "datefield" ? "inline" : "none";
                    $icon_display = $field["calendarIconType"] == "calendar" ? "inline" : "none";

                    $month_field = "<div class='gfield_date_month ginput_date' id='gfield_input_date_month' style='display:$dropdown_display'><input name='ginput_month' type='text' disabled='disabled'/><label>" . __("MM", "gravityforms") . "</label></div>";
                    $day_field = "<div class='gfield_date_day ginput_date' id='gfield_input_date_day' style='display:$dropdown_display'><input name='ginput_day' type='text' disabled='disabled'/><label>" . __("DD", "gravityforms") . "</label></div>";
                    $year_field = "<div class='gfield_date_year ginput_date' id='gfield_input_date_year' style='display:$dropdown_display'><input type='text' name='ginput_year' disabled='disabled'/><label>" . __("YYYY", "gravityforms") . "</label></div>";

                    $field_string ="<div class='ginput_container' id='gfield_input_datepicker' style='display:$datepicker_display'><input name='ginput_datepicker' type='text' /><img src='" . self::get_base_url() . "/images/calendar.png' id='gfield_input_datepicker_icon' style='display:$icon_display'/></div>";
                    $field_string .= $field["dateFormat"] == "dmy" ? $day_field . $month_field . $year_field : $month_field . $day_field . $year_field;

                    return $field_string;
                }
                else{
                    $date_info = self::parse_date($value, $format);

                    if($field["dateType"] == "datefield")
                    {
                        if($format == "mdy"){
                            $field_str = sprintf("<div class='clear-multi'><div class='gfield_date_month ginput_container' id='input_%d'><input type='text' maxlength='2' name='input_%d[]' id='input_%d.1' value='%s' tabindex='%d' %s/><label for='input_%d.1'>" . __("MM", "gravityforms") . "</label></div>", $id, $id, $id, $date_info["month"], self::$tab_index++, $disabled_text, $id);
                            $field_str .= sprintf("<div class='gfield_date_day ginput_container' id='input_%d'><input type='text' maxlength='2' name='input_%d[]' id='input_%d.2' value='%s' tabindex='%d' %s/><label for='input_%d.2'>" . __("DD", "gravityforms") . "</label></div>", $id, $id, $id, $date_info["day"], self::$tab_index++, $disabled_text, $id);
                        }
                        else{
                            $field_str = sprintf("<div class='clear-multi'><div class='gfield_date_day ginput_container' id='input_%d'><input type='text' maxlength='2' name='input_%d[]' id='input_%d.2' value='%s' tabindex='%d' %s/><label for='input_%d.2'>" . __("DD", "gravityforms") . "</label></div>", $id, $id, $id, $date_info["day"], self::$tab_index++, $disabled_text, $id);
                            $field_str .= sprintf("<div class='gfield_date_month ginput_container' id='input_%d'><input type='text' maxlength='2' name='input_%d[]' id='input_%d.1' value='%s' tabindex='%d' %s/><label for='input_%d.1'>" . __("MM", "gravityforms") . "</label></div>", $id, $id, $id, $date_info["month"], self::$tab_index++, $disabled_text, $id);
                        }
                        $field_str .= sprintf("<div class='gfield_date_year ginput_container' id='input_%d'><input type='text' maxlength='4' name='input_%d[]' id='input_%d.3' value='%s' tabindex='%d' %s/><label for='input_%d.3'>" . __("YYYY", "gravityforms") . "</label></div></div>", $id, $id, $id, $date_info["year"], self::$tab_index++, $disabled_text, $id);

                        return $field_str;
                    }
                    else
                    {
                        $value = self::date_display($value, $format);
                        $icon_class = $field["calendarIconType"] == "none" ? "datepicker_no_icon" : "datepicker_with_icon";
                        $icon_url = empty($field["calendarIconUrl"]) ? self::get_base_url() . "/images/calendar.png" : $field["calendarIconUrl"];
                        return sprintf("<div class='ginput_container'><input name='input_%d' id='input_%d' type='text' value='%s' class='datepicker %s %s %s' tabindex='%d' %s/>%s</div><input type='hidden' id='gforms_calendar_icon_input_$id' class='gform_hidden' value='$icon_url'/>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), $format, $icon_class, self::$tab_index++, $disabled_text, $datepicker);
                    }
                }

            case "time" :
                if(!is_array($value) && !empty($value)){
                    preg_match('/^(\d*):(\d*) (.*)$/', $value, $matches);
                    $hour = self::escape_attribute($matches[1]);
                    $minute = self::escape_attribute($matches[2]);
                    $am_selected = $matches[3] == "am" ? "selected='selected'" : "";
                    $pm_selected = $matches[3] == "pm" ? "selected='selected'" : "";
                }
                else{
                    $hour = self::escape_attribute($value[0]);
                    $minute = self::escape_attribute($value[1]);
                    $am_selected = $value[2] == "am" ? "selected='selected'" : "";
                    $pm_selected = $value[2] == "pm" ? "selected='selected'" : "";
                }

                return sprintf("<div class='clear-multi'><div class='gfield_time_hour ginput_container' id='input_%d'><input type='text' name='input_%d[]' id='input_%d.1' value='%s' tabindex='%d' %s/> : <label for='input_%d.1'>" . __("HH", "gravityforms") . "</label></div><div class='gfield_time_minute'><input type='text' name='input_%d[]' id='input_%d.2' value='%s' tabindex='%d' %s/><label for='input_%d.2'>" . __("MM", "gravityforms") . "</label></div><div class='gfield_time_ampm'><select name='input_%d[]' id='input_%d.3' tabindex='%d' %s><option value='am' %s>" . __("AM", "gravityforms") . "</option><option value='pm' %s>" . __("PM", "gravityforms") . "</option></select></div></div>", $id, $id, $id, $hour, self::$tab_index++, $disabled_text, $id, $id, $id, $minute, self::$tab_index++, $disabled_text, $id, $id, $id, self::$tab_index++, $disabled_text, $am_selected, $pm_selected);

            case "fileupload" :
                $upload = sprintf("<input name='input_%d' id='input_%d' type='file' value='%s' size='20' class='%s' tabindex='%d' %s/>", $id, $id, self::escape_attribute($value), self::escape_attribute($class), self::$tab_index++, $disabled_text);

                if(IS_ADMIN && !empty($value)){
                    $value = self::escape_attribute($value);
                    $preview = sprintf("<div id='preview_%d'><a href='%s' target='_blank' alt='%s' title='%s'>%s</a><a href='%s' target='_blank' alt='" . __("Download file", "gravityforms") . "' title='" . __("Download file", "gravityforms") . "'><img src='%s' style='margin-left:10px;'/></a><a href='javascript:void(0);' alt='" . __("Delete file", "gravityforms") . "' title='" . __("Delete file", "gravityforms") . "' onclick='DeleteFile(%d,%d);' ><img src='%s' style='margin-left:10px;'/></a></div>", $id, $value, $value, $value, self::truncate_url($value), $value, self::get_base_url() . "/images/download.png", $lead_id, $id, self::get_base_url() . "/images/delete.png");
                    return $preview . "<div id='upload_$id' style='display:none;'>$upload</div>";
                }
                else{
                    return "<div class='ginput_container'>$upload</div>";
                }


            case "captcha" :
                if(!function_exists("recaptcha_get_html")){
                    require_once(self::get_base_path() . '/recaptchalib.php');
                }

                $theme = empty($field["captchaTheme"]) ? "red" : self::escape_attribute($field["captchaTheme"]);
                $publickey = get_option("rg_gforms_captcha_public_key");
                $privatekey = get_option("rg_gforms_captcha_private_key");
                if(IS_ADMIN){
                    if(empty($publickey) || empty($privatekey)){
                        return "<div class='captcha_message'>" . __("To use the reCaptcha field you must first do the following:", "gravityforms") . "</div><div class='captcha_message'>1 - <a href='https://admin.recaptcha.net/recaptcha/createsite/?app=php' target='_blank'>" . __(sprintf("Sign up%s for a free reCAPTCHA account", "</a>"), "gravityforms") . "</div><div class='captcha_message'>2 - " . __(sprintf("Enter your reCAPTCHA keys in the %ssettings page%s", "<a href='?page=gf_settings'>", "</a>"), "gravityforms") . "</div>";
                    }
                    else{
                        return "<div><img class='gfield_captcha' src='" . self::get_base_url() . "/images/captcha_$theme.jpg' alt='reCAPTCHA' title='reCAPTCHA'/></div>";
                    }
                }
                else{
                    $language = empty($field["captchaLanguage"]) ? "en" : self::escape_attribute($field["captchaLanguage"]);

                    $options = "<script>var RecaptchaOptions = {theme : '$theme',tabindex : " . self::$tab_index++ . ", lang : '$language'};</script>";

                    $is_ssl = !empty($_SERVER['HTTPS']);
                    return $options . "<div class='ginput_container' id='input_$id'>" . recaptcha_get_html($publickey, null, $is_ssl) . "</div>";
                }
            break;
        }
    }

    private static function date_display($value, $format = "mdy"){
        $date = self::parse_date($value, $format);
        if(empty($date))
            return "";

        return $format == "dmy" ? $date["day"] . "/" . $date["month"] . "/" . $date["year"] : $date["month"] . "/" . $date["day"] . "/" . $date["year"];
    }

    public static function parse_date($date, $format="mdy"){
        $date_info = array();

        if(is_array($date)){
            if(empty($date[0]))
                return array();

            //format mm-dd-yyyy or dd-mm-yyyy
            $date_info["year"] = $date[2];
            $date_info["month"] = $format == "mdy" ? $date[0] : $date[1];
            $date_info["day"] = $format == "mdy" ? $date[1] : $date[0];
            return $date_info;
        }

        $date = str_replace("/", "-", $date);
        if(preg_match('/^(\d{1,4})-(\d{1,2})-(\d{1,4})$/', $date, $matches)){

            if(strlen($matches[1]) == 4){
                //format yyyy-mm-dd
                $date_info["year"] = $matches[1];
                $date_info["month"] = $matches[2];
                $date_info["day"] = $matches[3];
            }
            else{
                //format mm-dd-yyyy or dd-mm-yyyy
                $date_info["year"] = $matches[3];
                $date_info["month"] = $format == "mdy" ? $matches[1] : $matches[2];
                $date_info["day"] = $format == "mdy" ? $matches[2] : $matches[1];
            }
        }

        return $date_info;
    }

    private static function get_icon_url($path){
        $info = pathinfo($path);

        switch(strtolower($info["extension"])){

            case "css" :
                $file_name = "icon_css.gif";
            break;

            case "doc" :
                $file_name = "icon_doc.gif";
            break;

            case "fla" :
                $file_name = "icon_fla.gif";
            break;

            case "html" :
            case "htm" :
            case "shtml" :
                $file_name = "icon_html.gif";
            break;

            case "js" :
                $file_name = "icon_js.gif";
            break;

            case "log" :
                $file_name = "icon_log.gif";
            break;

            case "mov" :
                $file_name = "icon_mov.gif";
            break;

            case "pdf" :
                $file_name = "icon_pdf.gif";
            break;

            case "php" :
                $file_name = "icon_php.gif";
            break;

            case "ppt" :
                $file_name = "icon_ppt.gif";
            break;

            case "psd" :
                $file_name = "icon_psd.gif";
            break;

            case "sql" :
                $file_name = "icon_sql.gif";
            break;

            case "swf" :
                $file_name = "icon_swf.gif";
            break;

            case "txt" :
                $file_name = "icon_txt.gif";
            break;

            case "xls" :
                $file_name = "icon_xls.gif";
            break;

            case "xml" :
                $file_name = "icon_xml.gif";
            break;

            case "zip" :
                $file_name = "icon_zip.gif";
            break;

            case "gif" :
            case "jpg" :
            case "jpeg":
            case "png" :
            case "bmp" :
            case "tif" :
            case "eps" :
                $file_name = "icon_image.gif";
            break;

            case "mp3" :
            case "wav" :
            case "wma" :
                $file_name = "icon_audio.gif";
            break;

            case "mp4" :
            case "avi" :
            case "wmv" :
            case "flv" :
                $file_name = "icon_video.gif";
            break;

            default:
                $file_name = "icon_generic.gif";
            break;
        }

        return self::get_base_url() . "/images/doctypes/$file_name";
    }

    public static function get_countries(){
        return array(__('Afghanistan', 'gravityforms'),__('Albania', 'gravityforms'),__('Algeria', 'gravityforms'),__('Andorra', 'gravityforms'),__('Angola', 'gravityforms'),__('Antigua and Barbuda', 'gravityforms'),__('Argentina', 'gravityforms'),__('Armenia', 'gravityforms'),__('Australia', 'gravityforms'),__('Austria', 'gravityforms'),__('Azerbaijan', 'gravityforms'),__('Bahamas', 'gravityforms'),__('Bahrain', 'gravityforms'),__('Bangladesh', 'gravityforms'),__('Barbados', 'gravityforms'),__('Belarus', 'gravityforms'),__('Belgium', 'gravityforms'),__('Belize', 'gravityforms'),__('Benin', 'gravityforms'),__('Bermuda', 'gravityforms'),__('Bhutan', 'gravityforms'),__('Bolivia', 'gravityforms'),__('Bosnia and Herzegovina', 'gravityforms'),__('Botswana', 'gravityforms'),__('Brazil', 'gravityforms'),__('Brunei', 'gravityforms'),__('Bulgaria', 'gravityforms'),__('Burkina Faso', 'gravityforms'),__('Burundi', 'gravityforms'),__('Cambodia', 'gravityforms'),__('Cameroon', 'gravityforms'),__('Canada', 'gravityforms'),__('Cape Verde', 'gravityforms'),__('Central African Republic', 'gravityforms'),__('Chad', 'gravityforms'),__('Chile', 'gravityforms'),__('China', 'gravityforms'),__('Colombia', 'gravityforms'),__('Comoros', 'gravityforms'),__('Congo', 'gravityforms'),__('Costa Rica', 'gravityforms'),__('C&ocirc;te d\'Ivoire', 'gravityforms'),__('Croatia', 'gravityforms'),__('Cuba', 'gravityforms'),__('Cyprus', 'gravityforms'),__('Czech Republic', 'gravityforms'),__('Denmark', 'gravityforms'),__('Djibouti', 'gravityforms'),__('Dominica', 'gravityforms'),__('Dominican Republic', 'gravityforms'),__('East Timor', 'gravityforms'),__('Ecuador', 'gravityforms'),__('Egypt', 'gravityforms'),__('El Salvador', 'gravityforms'),__('Equatorial Guinea', 'gravityforms'),__('Eritrea', 'gravityforms'),__('Estonia', 'gravityforms'),__('Ethiopia', 'gravityforms'),__('Fiji', 'gravityforms'),__('Finland', 'gravityforms'),__('France', 'gravityforms'),__('Gabon', 'gravityforms'),__('Gambia', 'gravityforms'),__('Georgia', 'gravityforms'),__('Germany', 'gravityforms'),__('Ghana', 'gravityforms'),__('Greece', 'gravityforms'),__('Grenada', 'gravityforms'),__('Guatemala', 'gravityforms'),__('Guinea', 'gravityforms'),__('Guinea-Bissau', 'gravityforms'),__('Guyana', 'gravityforms'),__('Haiti', 'gravityforms'),__('Honduras', 'gravityforms'),__('Hong Kong', 'gravityforms'),__('Hungary', 'gravityforms'),__('Iceland', 'gravityforms'),__('India', 'gravityforms'),__('Indonesia', 'gravityforms'),__('Iran', 'gravityforms'),__('Iraq', 'gravityforms'),__('Ireland', 'gravityforms'),__('Israel', 'gravityforms'),__('Italy', 'gravityforms'),__('Jamaica', 'gravityforms'),__('Japan', 'gravityforms'),__('Jordan', 'gravityforms'),__('Kazakhstan', 'gravityforms'),__('Kenya', 'gravityforms'),__('Kiribati', 'gravityforms'),__('North Korea', 'gravityforms'),__('South Korea', 'gravityforms'),__('Kuwait', 'gravityforms'),__('Kyrgyzstan', 'gravityforms'),__('Laos', 'gravityforms'),__('Latvia', 'gravityforms'),__('Lebanon', 'gravityforms'),__('Lesotho', 'gravityforms'),__('Liberia', 'gravityforms'),__('Libya', 'gravityforms'),__('Liechtenstein', 'gravityforms'),__('Lithuania', 'gravityforms'),__('Luxembourg', 'gravityforms'),__('Macedonia', 'gravityforms'),__('Madagascar', 'gravityforms'),__('Malawi', 'gravityforms'),__('Malaysia', 'gravityforms'),__('Maldives', 'gravityforms'),__('Mali', 'gravityforms'),__('Malta', 'gravityforms'),__('Marshall Islands', 'gravityforms'),__('Mauritania', 'gravityforms'),__('Mauritius', 'gravityforms'),__('Mexico', 'gravityforms'),__('Micronesia', 'gravityforms'),__('Moldova', 'gravityforms'),__('Monaco', 'gravityforms'),__('Mongolia', 'gravityforms'),__('Montenegro', 'gravityforms'),__('Morocco', 'gravityforms'),__('Mozambique', 'gravityforms'),__('Myanmar', 'gravityforms'),__('Namibia', 'gravityforms'),__('Nauru', 'gravityforms'),__('Nepal', 'gravityforms'),__('Netherlands', 'gravityforms'),__('New Zealand', 'gravityforms'),__('Nicaragua', 'gravityforms'),__('Niger', 'gravityforms'),__('Nigeria', 'gravityforms'),__('Norway', 'gravityforms'),__('Oman', 'gravityforms'),__('Pakistan', 'gravityforms'),__('Palau', 'gravityforms'),__('Palestine', 'gravityforms'),__('Panama', 'gravityforms'),__('Papua New Guinea', 'gravityforms'),__('Paraguay', 'gravityforms'),__('Peru', 'gravityforms'),__('Philippines', 'gravityforms'),__('Poland', 'gravityforms'),__('Portugal', 'gravityforms'),__('Puerto Rico', 'gravityforms'),__('Qatar', 'gravityforms'),__('Romania', 'gravityforms'),__('Russia', 'gravityforms'),__('Rwanda', 'gravityforms'),__('Saint Kitts and Nevis', 'gravityforms'),__('Saint Lucia', 'gravityforms'),__('Saint Vincent and the Grenadines', 'gravityforms'),__('Samoa', 'gravityforms'),__('San Marino', 'gravityforms'),__('Sao Tome and Principe', 'gravityforms'),__('Saudi Arabia', 'gravityforms'),__('Senegal', 'gravityforms'),__('Serbia and Montenegro', 'gravityforms'),__('Seychelles', 'gravityforms'),__('Sierra Leone', 'gravityforms'),__('Singapore', 'gravityforms'),__('Slovakia', 'gravityforms'),__('Slovenia', 'gravityforms'),__('Solomon Islands', 'gravityforms'),__('Somalia', 'gravityforms'),__('South Africa', 'gravityforms'),__('Spain', 'gravityforms'),__('Sri Lanka', 'gravityforms'),__('Sudan', 'gravityforms'),__('Suriname', 'gravityforms'),__('Swaziland', 'gravityforms'),__('Sweden', 'gravityforms'),__('Switzerland', 'gravityforms'),__('Syria', 'gravityforms'),__('Taiwan', 'gravityforms'),__('Tajikistan', 'gravityforms'),__('Tanzania', 'gravityforms'),__('Thailand', 'gravityforms'),__('Togo', 'gravityforms'),__('Tonga', 'gravityforms'),__('Trinidad and Tobago', 'gravityforms'),__('Tunisia', 'gravityforms'),__('Turkey', 'gravityforms'),__('Turkmenistan', 'gravityforms'),__('Tuvalu', 'gravityforms'),__('Uganda', 'gravityforms'),__('Ukraine', 'gravityforms'),__('United Arab Emirates', 'gravityforms'),__('United Kingdom', 'gravityforms'),__('United States', 'gravityforms'),__('Uruguay', 'gravityforms'),__('Uzbekistan', 'gravityforms'),__('Vanuatu', 'gravityforms'),__('Vatican City', 'gravityforms'),__('Venezuela', 'gravityforms'),__('Vietnam', 'gravityforms'),__('Yemen', 'gravityforms'),__('Zambia', 'gravityforms'),__('Zimbabwe', 'gravityforms'));
    }

    public static function get_country_dropdown($selected_country){
        $countries = array_merge(array(''), self::get_countries());
        $country_list = "";
        foreach($countries as $country){
            $selected = ($country == $selected_country) ? "selected = 'selected'" : "";
            $country_list .= "<option value='" . esc_attr($country) . "' $selected>$country</option>";
        }
        return $country_list;
    }

    public static function get_checkbox_choices($field, $value, $disabled_text){
        $choices = "";

        if(is_array($field["choices"])){
            $choice_number = 1;

            foreach($field["choices"] as $choice){

                $input_id = $field["id"] . '.' . $choice_number;
                $id = $field["id"] . '_' . $choice_number++;

                if(empty($value) && $choice["isSelected"])
                    $checked = "checked='checked'";
                else if($value == $choice["value"] || (is_array($value) && $value[$input_id] == $choice["value"]))
                    $checked = "checked='checked'";
                else
                    $checked = "";

                $choices.= sprintf("<li><input name='input_%s' type='checkbox' value='%s' %s id='choice_%s' tabindex='%d'  %s /><label for='choice_%s'>%s</label></li>", $input_id, esc_attr($choice["value"]), $checked, $id, self::$tab_index++, $disabled_text, $id, self::escape_text($choice["text"]));
            }
        }
        return $choices;
    }

    public static function get_radio_choices($field, $value="", $disabled_text){
        $choices = "";

        if(is_array($field["choices"])){
            $choice_id = 0;

            foreach($field["choices"] as $choice){
                $id = $field["id"] . '_' . $choice_id++;
                if(empty($value))
                    $checked = $choice["isSelected"] ? "checked='checked'" : "";
                else
                    $checked = ($value == $choice["text"] || (is_array($value) && in_array($choice["text"], $value))) ? "checked='checked'" : "";

                $choices.= sprintf("<li><input name='input_%d' type='radio' value='%s' %s id='choice_%s' tabindex='%d' %s /><label for='choice_%s'>%s</label></li>", $field["id"], self::escape_attribute($choice["text"]), $checked, $id, self::$tab_index++, $disabled_text, $id, self::escape_text($choice["text"]));
            }
        }
        return $choices;
    }

    public static function get_select_choices($field, $value=""){
        $choices = "";
        if(is_array($field["choices"])){
            foreach($field["choices"] as $choice){
                if(empty($value))
                    $selected = $choice["isSelected"] ? "selected='selected'" : "";
                else
                    $selected = ($value == $choice["text"]) ? "selected='selected'" : "";

                //needed for users upgrading from 1.0
                $val = !empty($choice["value"]) ? self::escape_attribute($choice["value"]) : self::escape_text($choice["text"]);

                $choices.= sprintf("<option value='%s' %s>%s</option>", $val, $selected,  self::escape_text($choice["text"]));
            }
        }
        return $choices;
    }

    public static function forms_page($form_id){
        global $wpdb;

        if(!self::ensure_wp_version())
            return;



        if($_POST["operation"] == "delete"){
            check_admin_referer('gforms_delete_form', 'gforms_delete_form');
            RGFormsModel::delete_form($form_id);
            ?>
                <script>
                jQuery(document).ready(
                    function(){document.location.href="?page=gf_edit_forms";}
                );
                </script>
            <?php
            exit;
        }

        wp_print_scripts(array("jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","sack","thickbox"));
        wp_print_styles(array("thickbox"));
        ?>
        <script src="<?php echo self::get_base_url() ?>/js/jquery.dimensions.js"></script>
        <script src="<?php echo self::get_base_url() ?>/js/floatmenu_init.js"></script>
        <script src="<?php echo self::get_base_url() ?>/js/menu.js"></script>
        <script src="<?php echo self::get_base_url() ?>/js/jquery.json-1.3.js"></script>
        <script src="<?php echo self::get_base_url() ?>/js/jquery.simplemodal-1.3.min.js"></script>
        <script src="<?php echo self::get_base_url() ?>/js/forms.js"></script>
        <script src="<?php echo self::get_base_url() ?>/js/jquery-ui/ui.datepicker.js"></script>

        <link rel="stylesheet" href="<?php echo self::get_base_url() ?>/css/jquery-ui-1.7.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo self::get_base_url() ?>/css/datepicker.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo self::get_base_url() ?>/css/admin.css" type="text/css" />
        <script>
            jQuery(document).ready(
                function() {
                    jQuery('.datepicker').datepicker({showOn: "both", buttonImage: "<?php echo self::get_base_url() ?>/images/calendar.png", buttonImageOnly: true});
                }
            );

            function has_entry(fieldNumber){
                var submitted_fields = new Array(<?php echo RGFormsModel::get_submitted_fields($form_id) ?>);
                for(var i=0; i<submitted_fields.length; i++){
                    if(submitted_fields[i] == fieldNumber)
                        return true;
                }
                return false;
            }

            function InsertVariable(element_id){
                var variable = jQuery('#' + element_id + '_variable_select').val();
                var messageElement = jQuery("#" + element_id);

                if(document.selection) {
                    // Go the IE way
                    messageElement[0].focus();
                    document.selection.createRange().text=variable;
                }
                else if(messageElement[0].selectionStart) {
                    // Go the Gecko way
                    obj = messageElement[0]
                    obj.value = obj.value.substr(0, obj.selectionStart) + variable + obj.value.substr(obj.selectionEnd, obj.value.length);
                }
                else {
                    messageElement.val(variable + messageElement.val());
                }

                jQuery('#' + element_id + '_variable_select')[0].selectedIndex = 0;
            }

        </script>

        <style>
            .field_type li {
                float:left;
                width:50%;
            }
            .field_type input{
                width:100px;
            }
        </style>

        <?php

        $form = RGFormsModel::get_form_meta($form_id);

        if(is_object($form) || is_array($form))
            echo "<script>var form = " . self::json_encode($form) . ";</script>";
        else
            echo "<script>var form = new Form();</script>";

        ?>

        <div class="wrap gforms_edit_form">
            <a class="gforms_settings_button" href="javascript:FieldClick(jQuery('#gform_heading')[0]);"><?php _e("Form Settings", "gravityforms"); ?></a>
            <?php echo self::get_remote_message(); ?>
            <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo self::get_base_url()?>/images/gravity-title-icon-32.png" class="gtitle_icon"/>
            <h2><?php _e("Form Editor", "gravityforms"); ?></h2>

            <table width="100%">
            <tr>
                <td class="pad_top" valign="top">

                    <div id="gform_heading" class="selectable">
                        <form method="post" id="form_delete">
                            <?php wp_nonce_field('gforms_delete_form', 'gforms_delete_form') ?>

                            <?php if(self::current_user_can_any("gravityforms_delete_forms")){
                                $delete_link = '<a href="javascript:void(0);" class="form_delete_icon" title="' . __("Delete this Form", "gravityforms") . '" onclick="if(confirm(\'' . __("Would you like to delete this form and ALL entries associated with it? \'Cancel\' to stop. \'OK\' to delete", "gravityforms") . '\')){jQuery(\'#form_delete\')[0].submit();} else{return false;}">' . __("Delete Form", "gravityforms") . '</a>';
                                echo apply_filters("gform_form_delete_link", $delete_link);
                                ?>
                            <?php } ?>

                            <a href="javascript:void(0);" class="form_edit_icon edit_icon_collapsed" title="<?php _e("Edit Form's properties", "gravityforms"); ?>"><?php _e("Edit", "gravityforms"); ?></a>
                            <input type="hidden" value="delete" name="operation"/>
                        </form>
                        <h3 id="gform_title"></h3>
                        <span id="gform_description">&nbsp;</span>

                        <div id="form_settings" style="display:none;">
                            <ul>
                                <li style="width:100px; padding:0px;"><a href="#gform_settings_tab_1"><?php _e("Properties", "gravityforms"); ?></a></li>
                                <li style="width:100px; padding:0px; "><a href="#gform_settings_tab_2"><?php _e("Advanced", "gravityforms"); ?></a></li>
                                <li style="width:120px; padding:0px; "><a href="#gform_settings_tab_3"><?php _e("Confirmation", "gravityforms"); ?></a></li>
                            </ul>
                            <div id="gform_settings_tab_1">
                                <ul class="gforms_form_settings">
                                    <li>
                                        <label for="form_title_input" style="display:block;">
                                            <?php _e("Title", "gravityforms"); ?>
                                            <?php gform_tooltip("form_tile") ?>
                                        </label>
                                        <input type="text" id="form_title_input" class="fieldwidth-3" onkeyup="UpdateFormProperty('title', this.value);" />
                                    </li>
                                    <li>
                                        <label for="form_description_input" style="display:block;">
                                            <?php _e("Description", "gravityforms"); ?>
                                            <?php gform_tooltip("form_description") ?>
                                        </label>
                                        <textarea id="form_description_input" class="fieldwidth-3 fieldheight-2" onkeyup="UpdateFormProperty('description', this.value);"/></textarea>
                                    </li>
                                    <li>
                                        <label for="form_label_placement" style="display:block;">
                                            <?php _e("Label Placement", "gravityforms"); ?>
                                            <?php gform_tooltip("form_label_placement") ?>
                                        </label>
                                        <select id="form_label_placement" onchange="UpdateLabelPlacement();">
                                            <option value="top_label"><?php _e("Top aligned", "gravityforms"); ?></option>
                                            <option value="left_label"><?php _e("Left aligned", "gravityforms"); ?></option>
                                            <option value="right_label"><?php _e("Right aligned", "gravityforms"); ?></option>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                            <div id="gform_settings_tab_2">
                                <ul class="gforms_form_settings">
                                    <li>
                                        <label><?php _e("Form Button", "gravityforms"); ?></label>
                                        <div class="form_button_options">
                                            <input type="radio" id="form_button_text" name="form_button" value="text" onclick="ToggleButton();"/>
                                            <label for="form_button_text" class="inline">
                                                <?php _e("Default", "gravityforms"); ?>
                                                <?php gform_tooltip("form_button_text") ?>
                                            </label>
                                            &nbsp;&nbsp;
                                            <input type="radio" id="form_button_image" name="form_button" value="image" onclick="ToggleButton();"/>
                                            <label for="form_button_image" class="inline">
                                                <?php _e("Image", "gravityforms"); ?>
                                                <?php gform_tooltip("form_button_image") ?>
                                            </label>

                                            <div id="form_button_text_container" style="margin-top:5px;">
                                            <label for="form_button_text_input" class="float_label">
                                                    <?php _e("Text:", "gravityforms"); ?>
                                                </label>
                                                <input type="text" id="form_button_text_input" class="input_size_b" size="40" />
                                            </div>

                                            <div id="form_button_image_container" style="margin-top:5px;">
                                                <label for="form_button_image_url" class="inline">
                                                    <?php _e("Image Path:", "gravityforms"); ?>
                                                </label>
                                                <input type="text" id="form_button_image_url" size="45"/>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <label for="form_css_class" style="display:block;">
                                            <?php _e("CSS Class Name", "gravityforms"); ?>
                                            <?php gform_tooltip("form_css_class") ?>
                                        </label>
                                        <input type="text" id="form_css_class" class="fieldwidth-3"/>
                                    </li>
                                   <li>
                                        <input type="checkbox" id="gform_limit_entries" onclick="ToggleLimitEntry();"/> <label for="gform_limit_entries"><?php _e("Limit number of entries", "gravityforms") ?> <?php gform_tooltip("form_limit_entries") ?></label>
                                        <br/>
                                        <div id="gform_limit_entries_container" style="display:none;">
                                            <br/>
                                            <label for="gform_limit_entries_count" style="display:block;">
                                                <?php _e("Number of Entries", "gravityforms"); ?>
                                            </label>
                                            <input type="text" id="gform_limit_entries_count"/>
                                            <br/><br/>
                                            <label for="form_limit_entries_message" style="display:block;">
                                                <?php _e("Entry Limit Reached Message", "gravityforms"); ?>
                                            </label>
                                            <textarea id="form_limit_entries_message" class="fieldwidth-3"></textarea>
                                        </div>
                                   </li>
                                   <li>
                                        <input type="checkbox" id="gform_schedule_form" onclick="ToggleSchedule();"/> <label for="gform_schedule_form"><?php _e("Schedule form", "gravityforms") ?> <?php gform_tooltip("form_schedule_form") ?></label>
                                        <br/>
                                        <div id="gform_schedule_form_container" style="display:none;">
                                            <br/>
                                            <label for="gform_schedule_start" style="display:block;">
                                                <?php _e("Start Date/Time", "gravityforms"); ?>
                                            </label>
                                            <input type="text" id="gform_schedule_start" name="gform_schedule_start" class="datepicker"/>
                                            &nbsp;&nbsp;
                                            <select id="gform_schedule_start_hour">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                            :
                                            <select id="gform_schedule_start_minute">
                                                <option value="00">00</option>
                                                <option value="15">15</option>
                                                <option value="30">30</option>
                                                <option value="45">45</option>
                                            </select>
                                            <select id="gform_schedule_start_ampm">
                                                <option value="am">AM</option>
                                                <option value="pm">PM</option>
                                            </select>
                                            <br/><br/>
                                            <label for="gform_schedule_end" style="display:block;">
                                                <?php _e("End Date/Time", "gravityforms"); ?>
                                            </label>
                                            <input type="text" id="gform_schedule_end" class="datepicker"/>
                                            &nbsp;&nbsp;
                                            <select id="gform_schedule_end_hour">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                            :
                                            <select id="gform_schedule_end_minute">
                                                <option value="00">00</option>
                                                <option value="15">15</option>
                                                <option value="30">30</option>
                                                <option value="45">45</option>
                                            </select>
                                            <select id="gform_schedule_end_ampm">
                                                <option value="am">AM</option>
                                                <option value="pm">PM</option>
                                            </select>

                                            <br/><br/>
                                            <label for="gform_schedule_message" style="display:block;">
                                                <?php _e("Form Expired Message", "gravityforms"); ?>
                                            </label>
                                            <textarea id="gform_schedule_message" class="fieldwidth-3"></textarea>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div id="gform_settings_tab_3">
                                <ul class="gforms_form_settings">
                                    <li>
                                        <label><?php _e("Confirmation Message", "gravityforms"); ?></label>
                                        <div style="margin:4px 0;">
                                            <input type="radio" id="form_confirmation_show_message" name="form_confirmation" value="message" onclick="ToggleConfirmation();" />
                                            <label for="form_confirmation_show_messagex" class="inline">
                                                <?php _e("Text", "gravityforms"); ?>
                                                <?php gform_tooltip("form_confirmation_message") ?>
                                            </label>
                                            &nbsp;&nbsp;
                                            <input type="radio" id="form_confirmation_show_page" name="form_confirmation" value="page" onclick="ToggleConfirmation();" />
                                            <label for="form_confirmation_show_page" class="inline">
                                                <?php _e("Page", "gravityforms"); ?>
                                                <?php gform_tooltip("form_redirect_to_webpage") ?>
                                            </label>
                                            &nbsp;&nbsp;
                                            <input type="radio" id="form_confirmation_redirect" name="form_confirmation" value="redirect" onclick="ToggleConfirmation();" />
                                            <label for="form_confirmation_redirect" class="inline">
                                                <?php _e("Redirect", "gravityforms"); ?>
                                                <?php gform_tooltip("form_redirect_to_url") ?>
                                            </label>

                                            <div id="form_confirmation_message_container" style="padding-top:10px;">
                                                <div>
                                                    <?php self::insert_variables($form["fields"], "form_confirmation_message"); ?>
                                                </div>
                                                <textarea id="form_confirmation_message" style="width:400px; height:300px;" /></textarea>
                                            </div>

                                            <div id="form_confirmation_page_container" style="margin-top:5px;">
                                                <div>
                                                    <?php wp_dropdown_pages(array("name" => "form_confirmation_page", "show_option_none" => "Select a page")); ?>
                                                </div>
                                            </div>

                                            <div id="form_confirmation_redirect_container" style="margin-top:5px;">
                                                <div>
                                                    <input type="text" id="form_confirmation_url" style="width:98%;"/>
                                                </div>
                                                <div style="margin-top:15px;">
                                                    <input type="checkbox" id="form_redirect_use_querystring" onclick="ToggleQueryString()"/> <label for="form_redirect_use_querystring"><?php _e("Pass Field Data Via Query String", "gravityforms") ?> <?php gform_tooltip("form_redirect_querystring") ?></label>
                                                    <br/>
                                                    <div id="form_redirect_querystring_container" style="display:none;">
                                                        <div style="margin-top:6px;">
                                                            <?php self::insert_variables($form["fields"], "form_redirect_querystring", true); ?>
                                                        </div>
                                                        <textarea name="form_redirect_querystring" id="form_redirect_querystring" style="width:98%; height:100px;"></textarea><br/>
                                                        <div class="instruction"><?php _e("Sample: phone={Phone:1}&email{Email:2}", "gravityforms"); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <ul id="gform_fields" class="<?php echo $form["labelPlacement"] ?>" style="position: relative;">
                        <?php
                        if(is_array($form["fields"]))
                        {
                            foreach($form["fields"] as $field){
                                echo self::get_field($field, "", true);
                            }
                        }
                        ?>
                    </ul>

                    <div>
                        <?php
                            $button_text = $form["id"] > 0 ? __("Update Form", "gravityforms") : __("Save Form", "gravityforms");
                            $save_button = '<input type="button" class="button-primary" value="' . $button_text . '" onclick="SaveForm();" />';
                            $save_button = apply_filters("gform_save_form_button", $save_button);
                            echo $save_button;
                        ?>
                        <span id="please_wait_container" style="display:none; margin-left:15px;">
                            <img src="<?php echo self::get_base_url()?>/images/loading.gif"> <?php _e("Saving form. Please wait...", "gravityforms"); ?>
                        </span>
                        <div id="after_insert_dialog" style="display:none;">
                            <h3><?php _e("You have successfully saved your form!", "gravityforms"); ?></h3>
                            <p><?php _e("What do you want to do next?", "gravityforms"); ?></p>
                            <div class="new-form-option"><a title="<?php _e("Preview this form", "gravityforms"); ?>" id="preview_form_link" href="<?php echo self::get_base_url() ?>/preview.php?id={formid}" target="_blank"><?php _e("Preview this Form", "gravityforms"); ?></a></div>

                            <?php if(self::current_user_can_any("gravityforms_edit_forms")){ ?>
                                <div class="new-form-option"><a title="<?php _e("Setup email notifications for this form", "gravityforms"); ?>" id="notification_form_link" href="#"><?php _e("Setup Email Notifications for this Form", "gravityforms"); ?></a></div>
                            <?php } ?>

                            <div class="new-form-option"><a title="<?php _e("Continue editing this form", "gravityforms"); ?>" id="edit_form_link" href="#"><?php _e("Continue Editing this Form", "gravityforms"); ?></a></div>

                            <div class="new-form-option"><a title="<?php _e("I am done. Take me back to form list", "gravityforms"); ?>" href="?page=gf_edit_forms"><?php _e("Return to Form List", "gravityforms"); ?></a></div>

                        </div>
                        <h2></h2>
                        <div class="updated fade" id="after_update_dialog" style="padding:10px; display:none;">
                            <strong><?php _e("Form updated successfully.", "gravityforms"); ?></strong><br />
                            <a title="<?php _e("Continue editing this form", "gravityforms"); ?>" id="continue_form_link" href="javascript:void(0);" onclick="jQuery('#after_update_dialog').slideUp();"><?php _e("Continue Editing", "gravityforms"); ?></a> |
                            <a title="<?php _e("Setup email notifications for this form", "gravityforms"); ?>" href="?page=gf_edit_forms&view=notification&id=<?php echo absint($form["id"]) ?>"><?php _e("Setup Email Notifications", "gravityforms"); ?></a> |

                            <?php if(self::current_user_can_any("gravityforms_view_entries")){ ?>
                                <a title="<?php _e("View this form's entries", "gravityforms"); ?>" href="?page=gf_entries&view=entries&id=<?php echo absint($form["id"]) ?>"><?php _e("View Entries", "gravityforms"); ?></a> |
                            <?php } ?>

                            <a title="<?php _e("Preview this form", "gravityforms"); ?>" href="<?php echo self::get_base_url() ?>/preview.php?id=<?php echo absint($form["id"]) ?>" target="_blank"><?php _e("Preview Form", "gravityforms"); ?></a>
                        </div>
                    </div>
                    <div id="field_settings" style="display: none;">
                        <ul>
                            <li style="width:100px; padding:0px;"><a href="#gform_tab_1"><?php _e("Properties", "gravityforms"); ?></a></li>
                            <li style="width:100px; padding:0px; "><a href="#gform_tab_2"><?php _e("Advanced", "gravityforms"); ?></a></li>
                        </ul>
                        <div id="gform_tab_1">
                            <ul>
                            <li class="label_setting field_setting">
                                <label for="field_label">
                                    <?php _e("Field Label", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_label") ?>
                                </label>
                                <input type="text" id="field_label" class="fieldwidth-3" onkeyup="SetFieldLabel(this.value)" size="35"/>
                            </li>
                            <li class="captcha_theme_setting field_setting">
                                <label for="field_captcha_theme">
                                    <?php _e("Theme", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_recaptcha_theme") ?>
                                </label>
                                <select id="field_captcha_theme" onchange="SetCaptchaTheme(this.value, '<?php echo self::get_base_url() ?>/images/captcha_' + this.value + '.jpg')">
                                    <option value="red"><?php _e("Red", "gravityforms"); ?></option>
                                    <option value="white"><?php _e("White", "gravityforms"); ?></option>
                                    <option value="blackglass"><?php _e("Black Glass", "gravityforms"); ?></option>
                                    <option value="clean"><?php _e("Clean", "gravityforms"); ?></option>
                                </select>
                            </li>
                            <li class="post_custom_field_setting field_setting">
                                <label for="field_custom_field_name">
                                    <?php _e("Custom Field Name", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_custom_field_name") ?>
                                </label>
                                <div style="width:100px; float:left;">
                                    <input type="radio" name="field_custom" id="field_custom_existing" size="10" onclick="ToggleCustomField();" />
                                    <label for="field_custom_existing" class="inline">
                                        <?php _e("Existing", "gravityforms"); ?>
                                    </label>
                                </div>
                                <div style="width:100px; float:left;">
                                    <input type="radio" name="field_custom" id="field_custom_new" size="10" onclick="ToggleCustomField();" />
                                    <label for="field_custom_new" class="inline">
                                        <?php _e("New", "gravityforms"); ?>
                                    </label>
                                </div>
                                <div class="clear">
                                   <input type="text" id="field_custom_field_name_text" size="35" onkeyup="SetFieldProperty('postCustomFieldName', this.value);"/>
                                   <select id="field_custom_field_name_select" onchange="SetFieldProperty('postCustomFieldName', jQuery(this).val());">
                                        <option value=""><?php _e("Select an existing custom field", "gravityforms"); ?></option>
                                        <?php
                                            $custom_field_names = RGFormsModel::get_custom_field_names();
                                            foreach($custom_field_names as $name){
                                                ?>
                                                <option value="<?php echo $name?>"><?php echo $name?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            </li>
                            <li class="type_setting field_setting">
                                <label for="field_type">
                                    <?php _e("Field Type", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_type") ?>
                                </label>
                                <select id="field_type" onchange="StartChangeFieldType(jQuery(this).val());">
                                    <option value="" class="option_header"><?php _e("", "gravityforms"); ?>Standard Fields</option>
                                    <option value="text"><?php _e("Single line text", "gravityforms"); ?></option>
                                    <option value="textarea"><?php _e("Paragraph Text", "gravityforms"); ?></option>
                                    <option value="select"><?php _e("Drop Down", "gravityforms"); ?></option>
                                    <option value="number"><?php _e("Number", "gravityforms"); ?></option>
                                    <option value="checkbox"><?php _e("Checkboxes", "gravityforms"); ?></option>
                                    <option value="radio"><?php _e("Multiple Choice", "gravityforms"); ?></option>
                                    <option value="section"><?php _e("Section Break", "gravityforms"); ?></option>

                                    <option value="" class="option_header"><?php _e("Advanced Fields", "gravityforms"); ?></option>
                                    <option value="name"><?php _e("Name", "gravityforms"); ?></option>
                                    <option value="date"><?php _e("Date", "gravityforms"); ?></option>
                                    <option value="time"><?php _e("Time", "gravityforms"); ?></option>
                                    <option value="phone"><?php _e("Phone", "gravityforms"); ?></option>
                                    <option value="address"><?php _e("Address", "gravityforms"); ?></option>
                                    <option value="website"><?php _e("Website", "gravityforms"); ?></option>
                                    <option value="email"><?php _e("Email", "gravityforms"); ?></option>
                                    <option value="fileupload"><?php _e("File Upload", "gravityforms"); ?></option>
                                    <option value="captcha">reCAPTCHA</option>
                                    <option value="" class="option_header"><?php _e("Post Fields", "gravityforms"); ?></option>
                                    <option value="post_title"><?php _e("Title", "gravityforms"); ?></option>
                                    <option value="post_content"><?php _e("Body", "gravityforms"); ?></option>
                                    <option value="post_excerpt"><?php _e("Excerpt", "gravityforms"); ?></option>
                                    <option value="post_category"><?php _e("Category", "gravityforms"); ?></option>
                                    <option value="post_category"><?php _e("Image", "gravityforms"); ?></option>
                                    <option value="post_tags"><?php _e("Tags", "gravityforms"); ?></option>
                                    <option value="post_custom_field"><?php _e("Custom Field", "gravityforms"); ?></option>
                                </select>
                            </li>

                            <li class="post_status_setting field_setting">
                                <label for="field_post_status">
                                    <?php _e("Post Status", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_post_status") ?>
                                </label>
                                <select id="field_post_status" name="field_post_status">
                                    <option value="draft">Draft</option>
                                    <option value="pending">Pending Review</option>
                                    <option value="publish">Published</option>
                                </select>
                            </li>

                            <li class="post_author_setting field_setting">
                                <label for="field_post_author">
                                    <?php _e("Default Post Author", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_post_author") ?>
                                </label>
                                <?php wp_dropdown_users(array('name' => 'field_post_author')) ?>
                                <div>
                                    <input type="checkbox" id="gfield_current_user_as_author"/>
                                    <label for="gfield_current_user_as_author" class="inline"><?php _e("Use logged in user as author", "gravityforms"); ?> <?php gform_tooltip("form_field_current_user_as_author") ?></label>
                                </div>
                            </li>
                            <li class="post_category_setting field_setting">
                                <label for="field_post_category">
                                    <?php _e("Post Category", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_post_category") ?>
                                </label>
                                <?php wp_dropdown_categories(array('selected' => get_option('default_category'), 'hide_empty' => 0, 'id' => 'field_post_category', 'name' => 'field_post_category', 'orderby' => 'name', 'selected' => 'field_post_category', 'hierarchical' => true )); ?>
                            </li>

                            <li class="post_category_checkbox_setting field_setting">
                                <label for="field_post_category">
                                    <?php _e("Category", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_post_category_selection") ?>
                                </label>

                                <input type="radio" id="gfield_category_all" name="gfield_category" value="all" onclick="ToggleCategory();"/>
                                <label for="gfield_category_all" class="inline">
                                    <?php _e("All Categories", "gravityforms"); ?>

                                </label>
                                &nbsp;&nbsp;
                                <input type="radio" id="gfield_category_select" name="gfield_category" value="select" onclick="ToggleCategory();"/>
                                <label for="form_button_image" class="inline">
                                    <?php _e("Select Categories", "gravityforms"); ?>
                                </label>

                                <div id="gfield_settings_category_container">
                                    <table cellpadding="0" cellspacing="5">
                                    <?php
                                        $categories = get_categories( array('hide_empty' => 0) );
                                        $count = 0;
                                        $category_rows = '';
                                        self::_cat_rows($categories, $count, $category_rows);
                                        echo $category_rows;
                                    ?>
                                    </table>
                                </div>
                            </li>

                            <li class="post_image_setting field_setting">
                                <label><?php _e("Image Metadata", "gravityforms") ?> <?php gform_tooltip("form_field_image_meta") ?></label>
                                <input type="checkbox" id="gfield_display_title" onclick="SetPostImageMeta();" />
                                <label for="gfield_display_title" class="inline">
                                    <?php _e("Title", "gravityforms"); ?>
                                </label>
                                <br/>
                                <input type="checkbox" id="gfield_display_caption"  onclick="SetPostImageMeta();" />
                                <label for="gfield_display_caption" class="inline">
                                    <?php _e("Caption", "gravityforms"); ?>
                                </label>
                                <br/>
                                <input type="checkbox" id="gfield_display_description"  onclick="SetPostImageMeta();"/>
                                <label for="gfield_display_description" class="inline">
                                    <?php _e("Description", "gravityforms"); ?>
                                </label>
                            </li>

                            <li class="name_format_setting field_setting">
                                <label for="field_name_format">
                                    <?php _e("Name Format", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_name_format") ?>
                                </label>
                                <select id="field_name_format" onchange="StartChangeNameFormat(jQuery(this).val());">
                                    <option value="normal"><?php _e("Normal", "gravityforms"); ?></option>
                                    <option value="extended"><?php _e("Extended", "gravityforms"); ?></option>
                                    <option value="simple"><?php _e("Simple", "gravityforms"); ?></option>
                                </select>
                            </li>

                            <li class="date_input_type_setting field_setting">
                                <label for="field_date_input_type">
                                    <?php _e("Type", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_date_input_type") ?>
                                </label>
                                <select id="field_date_input_type" onchange="SetDateInputType(jQuery(this).val());">
                                    <option value="datefield"><?php _e("Date Field", "gravityforms") ?></option>
                                    <option value="datepicker"><?php _e("Date Picker", "gravityforms") ?></option>
                                </select>
                                <div id="date_picker_container">

                                    <input type="radio" id="gsetting_icon_none" name="gsetting_icon" value="none" onclick="SetCalendarIconType(this.value);"/>
                                    <label for="gsetting_icon_none" class="inline">
                                        <?php _e("No Icon", "gravityforms"); ?>
                                    </label>
                                    &nbsp;&nbsp;
                                    <input type="radio" id="gsetting_icon_calendar" name="gsetting_icon" value="calendar" onclick="SetCalendarIconType(this.value);"/>
                                    <label for="gsetting_icon_calendar" class="inline">
                                        <?php _e("Calendar Icon", "gravityforms"); ?>
                                    </label>
                                    &nbsp;&nbsp;
                                    <input type="radio" id="gsetting_icon_custom" name="gsetting_icon" value="custom" onclick="SetCalendarIconType(this.value);"/>
                                    <label for="gsetting_icon_custom" class="inline">
                                        <?php _e("Custom Icon", "gravityforms"); ?>
                                    </label>

                                    <div id="gfield_icon_url_container">
                                        <label for="gfield_calendar_icon_url" class="inline">
                                            <?php _e("Image Path: ", "gravityforms"); ?>
                                        </label>
                                        <input type="text" id="gfield_calendar_icon_url" size="45" onkeyup="SetFieldProperty('calendarIconUrl', this.value);"/>
                                        <div class="instruction"><?php _e("Preview this form to see your custom icon.", "gravityforms") ?></div>
                                    </div>
                                </div>
                            </li>

                            <li class="date_format_setting field_setting">
                                <label for="field_date_format">
                                    <?php _e("Date Format", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_date_format") ?>
                                </label>
                                <select id="field_date_format" onchange="SetDateFormat(jQuery(this).val());">
                                    <option value="mdy">mm/dd/yyyy</option>
                                    <option value="dmy">dd/mm/yyyy</option>
                                </select>
                            </li>

                            <li class="file_extensions_setting field_setting">
                                <label for="field_file_extension">
                                    <?php _e("Allowed file extensions", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_fileupload_allowed_extensions") ?>
                                </label>
                               <input type="text" id="field_file_extension" size="40" onkeyup="SetFieldProperty('allowedExtensions', this.value);"/>
                               <div><small><?php _e("Separated with commas (i.e. jpg, gif, png, pdf)", "gravityforms"); ?></small></div>
                            </li>


                            <li class="phone_format_setting field_setting">
                                <label for="field_phone_format">
                                    <?php _e("Phone Format", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_phone_format") ?>
                                </label>
                                <select id="field_phone_format" onchange="SetFieldPhoneFormat(jQuery(this).val());">
                                    <option value="standard">(###)### - ####</option>
                                    <option value="international"><?php _e("International", "gravityforms"); ?></option>
                                </select>
                            </li>

                            <li class="choices_setting field_setting">
                                <?php _e("Choices", "gravityforms"); ?> <?php gform_tooltip("form_field_choices") ?><br />
                                <div id="gfield_settings_choices_container">
                                    <ul id="field_choices"></ul>
                                </div>

                                <a title="<?php _e("Bulk Add / Predefined Choices" , "gravityforms") ?>" href="#TB_inline?height=500&width=600&inlineId=gfield_bulk_add" class="thickbox button"><?php _e("Bulk Add / Predefined Choices" , "gravityforms") ?></a>

                                <div id="gfield_bulk_add" style="display:none;">
                                    <div>
                                        <?php

                                        $predefined_choices = array(
                                            __("Countries", "gravityforms") => self::get_countries(),
                                            __("U.S. States", "gravityforms") => array(__("Alabama","gravityforms"),__("Alaska","gravityforms"),__("Arizona","gravityforms"),__("Arkansas","gravityforms"),__("California","gravityforms"),__("Colorado","gravityforms"),__("Connecticut","gravityforms"),__("Delaware","gravityforms"),__("Florida","gravityforms"),__("Georgia","gravityforms"),__("Hawaii","gravityforms"),__("Idaho","gravityforms"),__("Illinois","gravityforms"),__("Indiana","gravityforms"),__("Iowa","gravityforms"),__("Kansas","gravityforms"),__("Kentucky","gravityforms"),__("Louisiana","gravityforms"),__("Maine","gravityforms"),__("Maryland","gravityforms"),__("Massachusetts","gravityforms"),__("Michigan","gravityforms"),__("Minnesota","gravityforms"),__("Mississippi","gravityforms"),__("Missouri","gravityforms"),__("Montana","gravityforms"),__("Nebraska","gravityforms"),__("Nevada","gravityforms"),__("New Hampshire","gravityforms"),__("New Jersey","gravityforms"),__("New Mexico","gravityforms"),__("New York","gravityforms"),__("North Carolina","gravityforms"),__("North Dakota","gravityforms"),__("Ohio","gravityforms"),__("Oklahoma","gravityforms"),__("Oregon","gravityforms"),__("Pennsylvania","gravityforms"),__("Rhode Island","gravityforms"),__("South Carolina","gravityforms"),__("South Dakota","gravityforms"),__("Tennessee","gravityforms"),__("Texas","gravityforms"),__("Utah","gravityforms"),__("Vermont","gravityforms"),__("Virginia","gravityforms"),__("Washington","gravityforms"),__("West Virginia","gravityforms"),__("Wisconsin","gravityforms"),__("Wyoming","gravityforms")),
                                            __("Canadian Province/Territory", "gravityforms") => array(__("Alberta","gravityforms"),__("British Columbia","gravityforms"),__("Manitoba","gravityforms"),__("New Brunswick","gravityforms"),__("Newfoundland & Labrador","gravityforms"),__("Northwest Territories","gravityforms"),__("Nova Scotia","gravityforms"),__("Nunavut","gravityforms"),__("Ontario","gravityforms"),__("Prince Edward Island","gravityforms"),__("Quebec","gravityforms"),__("Saskatchewan","gravityforms"),__("Yukon","gravityforms")),
                                            __("Continents", "gravityforms") => array(__("Africa","gravityforms"),__("Antarctica","gravityforms"),__("Asia","gravityforms"),__("Australia","gravityforms"),__("Europe","gravityforms"),__("North America","gravityforms"),__("South America","gravityforms")),
                                            __("Gender", "gravityforms") => array(__("Male","gravityforms"),__("Female","gravityforms"),__("Prefer Not to Answer","gravityforms")),
                                            __("Age", "gravityforms") => array(__("Under 18","gravityforms"),__("18-24","gravityforms"),__("25-34","gravityforms"),__("35-44","gravityforms"),__("45-54","gravityforms"),__("55-64","gravityforms"),__("65 or Above","gravityforms"),__("Prefer Not to Answer","gravityforms")),
                                            __("Marital Status", "gravityforms") => array(__("Single","gravityforms"),__("Married","gravityforms"),__("Divorced","gravityforms"),__("Widowed","gravityforms")),
                                            __("Employment", "gravityforms") => array(__("Employed Full-Time","gravityforms"),__("Employed Part-Time","gravityforms"),__("Self-employed","gravityforms"),__("Not employed","gravityforms"),__(" but looking for work","gravityforms"),__("Not employed and not looking for work","gravityforms"),__("Homemaker","gravityforms"),__("Retired","gravityforms"),__("Student","gravityforms"),__("Prefer Not to Answer","gravityforms")),
                                            __("Job Type", "gravityforms") => array(__("Full-Time","gravityforms"),__("Part-Time","gravityforms"),__("Per Diem","gravityforms"),__("Employee","gravityforms"),__("Temporary","gravityforms"),__("Contract","gravityforms"),__("Intern","gravityforms"),__("Seasonal","gravityforms")),
                                            __("Industry", "gravityforms") => array(__("Accounting/Finance","gravityforms"),__("Advertising/Public Relations","gravityforms"),__("Aerospace/Aviation","gravityforms"),__("Arts/Entertainment/Publishing","gravityforms"),__("Automotive","gravityforms"),__("Banking/Mortgage","gravityforms"),__("Business Development","gravityforms"),__("Business Opportunity","gravityforms"),__("Clerical/Administrative","gravityforms"),__("Construction/Facilities","gravityforms"),__("Consumer Goods","gravityforms"),__("Customer Service","gravityforms"),__("Education/Training","gravityforms"),__("Energy/Utilities","gravityforms"),__("Engineering","gravityforms"),__("Government/Military","gravityforms"),__("Green","gravityforms"),__("Healthcare","gravityforms"),__("Hospitality/Travel","gravityforms"),__("Human Resources","gravityforms"),__("Installation/Maintenance","gravityforms"),__("Insurance","gravityforms"),__("Internet","gravityforms"),__("Job Search Aids","gravityforms"),__("Law Enforcement/Security","gravityforms"),__("Legal","gravityforms"),__("Management/Executive","gravityforms"),__("Manufacturing/Operations","gravityforms"),__("Marketing","gravityforms"),__("Non-Profit/Volunteer","gravityforms"),__("Pharmaceutical/Biotech","gravityforms"),__("Professional Services","gravityforms"),__("QA/Quality Control","gravityforms"),__("Real Estate","gravityforms"),__("Restaurant/Food Service","gravityforms"),__("Retail","gravityforms"),__("Sales","gravityforms"),__("Science/Research","gravityforms"),__("Skilled Labor","gravityforms"),__("Technology","gravityforms"),__("Telecommunications","gravityforms"),__("Transportation/Logistics","gravityforms"),__("Other","gravityforms")),
                                            __("Income", "gravityforms") => array(__("Under $20,000","gravityforms"),__("$20,000 - $30,000","gravityforms"),__("$30,000 - $40,000","gravityforms"),__("$40,000 - $50,000","gravityforms"),__("$50,000 - $75,000","gravityforms"),__("$75,000 - $100,000","gravityforms"),__("$100,000 - $150,000","gravityforms"),__("$150,000 or more","gravityforms"),__("Prefer Not to Answer","gravityforms")),
                                            __("Education", "gravityforms") => array(__("High School","gravityforms"),__("Associate Degree","gravityforms"),__("Bachelor's Degree","gravityforms"),__("Graduate of Professional Degree","gravityforms"),__("Some College","gravityforms"),__("Other","gravityforms"),__("Prefer Not to Answer","gravityforms")),
                                            __("Days of the Week", "gravityforms") => array(__("Sunday","gravityforms"),__("Monday","gravityforms"),__("Tuesday","gravityforms"),__("Wednesday","gravityforms"),__("Thursday","gravityforms"),__("Friday","gravityforms"),__("Saturday","gravityforms")),
                                            __("Months of the Year", "gravityforms") => array(__("January","gravityforms"),__("February","gravityforms"),__("March","gravityforms"),__("April","gravityforms"),__("May","gravityforms"),__("June","gravityforms"),__("July","gravityforms"),__("August","gravityforms"),__("September","gravityforms"),__("October","gravityforms"),__("November","gravityforms"),__("December","gravityforms")),
                                            __("How Often", "gravityforms") => array(__("Everyday","gravityforms"),__("Once a week","gravityforms"),__("2 to 3 times a week","gravityforms"),__("Once a month","gravityforms"),__(" 2 to 3 times a month","gravityforms"),__("Less than once a month","gravityforms")),
                                            __("How Long", "gravityforms") => array(__("Less than a month","gravityforms"),__("1-6 months","gravityforms"),__("1-3 years","gravityforms"),__("Over 3 Years","gravityforms"),__("Never used","gravityforms")),
                                            __("Satisfaction", "gravityforms") => array(__("Very Satisfied","gravityforms"),__("Satisfied","gravityforms"),__("Neutral","gravityforms"),__("Unsatisfied","gravityforms"),__("Very Unsatisfied","gravityforms")),
                                            __("Importance", "gravityforms") => array(__("Very Important","gravityforms"),__("Important","gravityforms"),__("Somewhat Important","gravityforms"),__("Not Important","gravityforms")),
                                            __("Agreement", "gravityforms") => array(__("Strongly Agree","gravityforms"),__("Agree","gravityforms"),__("Disagree","gravityforms"),__("Strongly Disagree","gravityforms")),
                                            __("Comparison", "gravityforms") => array(__("Much Better","gravityforms"),__("Somewhat Better","gravityforms"),__("About the Same","gravityforms"),__("Somewhat Worse","gravityforms"),__("Much Worse","gravityforms")),
                                            __("Would You", "gravityforms") => array(__("Definitely","gravityforms"),__("Probably","gravityforms"),__("Not Sure","gravityforms"),__("Probably Not","gravityforms"),__("Definitely Not","gravityforms")),
                                            __("Size", "gravityforms") => array(__("Extra Small","gravityforms"),__("Small","gravityforms"),__("Medium","gravityforms"),__("Large","gravityforms"),__("Extra Large","gravityforms")),

                                        );
                                        $predefined_choices = apply_filters("gform_predefined_choices", $predefined_choices);
                                        ?>
                                        <script type="text/javascript">
                                            var gform_predefined_choices = <?php echo self::json_encode($predefined_choices) ?>;
                                        </script>

								<div class="panel-instructions">Select a category and customize the predefined choices or paste your own list to bulk add choices.</div>
                                        <div class="bulk-left-panel">
                                            <ul>
                                            <?php
                                            foreach(array_keys($predefined_choices) as $name){
                                                $key = str_replace("'", "\'", $name);
                                            ?>
                                                <li><a href="javascript:void(0);" onclick="jQuery('#gfield_bulk_add_input').val(gform_predefined_choices['<?php echo $key ?>'].join('\n'));" class="bulk-choice"><?php echo $name ?></a>
                                            <?php
                                            }
                                            ?>
                                            </ul>
                                        </div>
                                        <div class="bulk-arrow-mid"></div>
                                        <textarea id="gfield_bulk_add_input"></textarea>
                                        <br style="clear:both;"/>

                                        <div class="panel-buttons">
                                            <input type="button" onclick="InsertBulkChoices(jQuery('#gfield_bulk_add_input').val().split('\n')); tb_remove();" class="button-primary" value="Add Choices">&nbsp;
                                            <input type="button" onclick="tb_remove();" class="button" value="Cancel">
                                        </div>
                                    </div>
                                </div>

                            </li>
                             <li class="description_setting field_setting">
                                <label for="field_description">
                                    <?php _e("Description", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_description") ?>
                                </label>
                                <textarea id="field_description" class="fieldwidth-3  fieldheight-2" onkeyup="SetFieldDescription(this.value);"/></textarea>
                            </li>
                            <li class="rules_setting field_setting">
                                <?php _e("Rules", "gravityforms"); ?><br/>
                                <input type="checkbox" id="field_required" onclick="SetFieldRequired(this.checked);"/>
                                <label for="field_required" class="inline">
                                    <?php _e("Required", "gravityforms"); ?>
                                    <?php gform_tooltip("form_field_required") ?>
                                </label><br/>
                                <div class="duplicate_setting field_setting">
                                    <input type="checkbox" id="field_no_duplicates" onclick="SetFieldProperty('noDuplicates', this.checked);"/>
                                    <label for="field_no_duplicates" class="inline">
                                        <?php _e("No Duplicates", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_no_duplicate") ?>
                                    </label>
                                </div>
                            </li>

                            <li class="range_setting field_setting">
                                <div style="clear:both;"><?php _e("Range", "gravityforms"); ?>
                                <?php gform_tooltip("form_field_number_range") ?>
                                </div>
                                <div style="width:90px; float:left;">
                                <input type="text" id="field_range_min" size="10" onkeyup="SetFieldProperty('rangeMin', this.value);" />
                                    <label for="field_range_min" >
                                        <?php _e("Min", "gravityforms"); ?>
                                    </label>

                                </div>
                                <div style="width:90px; float:left;">
                                <input type="text" id="field_range_max" size="10" onkeyup="SetFieldProperty('rangeMax', this.value);" />
                                    <label for="field_range_max">
                                        <?php _e("Max", "gravityforms"); ?>
                                    </label>

                                </div>
                                <br class="clear" />
                            </li>


                        </ul>
                        </div>
                        <div id="gform_tab_2">
                            <ul>
                                <li class="admin_label_setting field_setting">
                                    <label for="field_admin_label">
                                        <?php _e("Admin Label", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_admin_label") ?>
                                    </label>
                                    <input type="text" id="field_admin_label" size="35" onkeyup="SetFieldProperty('adminLabel', this.value);"/>
                                </li>
                                <li class="size_setting field_setting">
                                    <label for="field_size">
                                        <?php _e("Field Size", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_size") ?>
                                    </label>
                                    <select id="field_size" onchange="SetFieldSize(jQuery(this).val());">
                                        <option value="small"><?php _e("Small", "gravityforms"); ?></option>
                                        <option value="medium"><?php _e("Medium", "gravityforms"); ?></option>
                                        <option value="large"><?php _e("Large", "gravityforms"); ?></option>
                                    </select>
                                </li>
                                <li class="default_value_setting field_setting">
                                    <label for="field_default_value">
                                        <?php _e("Default Value", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_default_value") ?>
                                    </label>
                                    <input type="text" id="field_default_value" size="20" onkeyup="SetFieldDefaultValue(this.value);"/>
                                </li>
                                <li class="default_value_textarea_setting field_setting">
                                    <label for="field_default_value_textarea">
                                        <?php _e("Default Value", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_default_value") ?>
                                    </label>
                                    <textarea id="field_default_value_textarea" onkeyup="SetFieldDefaultValue(this.value);" class="fieldwidth-3"></textarea>
                                </li>
                                <li class="error_message_setting field_setting">
                                    <label for="field_error_message">
                                        <?php _e("Validation Message", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_validation_message") ?>
                                    </label>
                                    <input type="text" id="field_error_message" size="40" onkeyup="SetFieldProperty('errorMessage', this.value);"/>
                                </li>
                                <li class="captcha_language_setting field_setting">
                                    <label for="field_captcha_language">
                                        <?php _e("Language", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_recaptcha_language") ?>
                                    </label>
                                    <select id="field_captcha_language" onchange="SetFieldProperty('captchaLanguage', this.value);">
                                        <option value="en"><?php _e("English", "gravityforms"); ?></option>
                                        <option value="nl"><?php _e("Dutch", "gravityforms"); ?></option>
                                        <option value="fr"><?php _e("French", "gravityforms"); ?></option>
                                        <option value="de"><?php _e("German", "gravityforms"); ?></option>
                                        <option value="pt"><?php _e("Portuguese", "gravityforms"); ?></option>
                                        <option value="ru"><?php _e("Russian", "gravityforms"); ?></option>
                                        <option value="es"><?php _e("Spanish", "gravityforms"); ?></option>
                                        <option value="tr"><?php _e("Turkish", "gravityforms"); ?></option>
                                    </select>
                                </li>
                                <li class="css_class_setting field_setting">
                                    <label for="field_css_class">
                                        <?php _e("CSS Class Name", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_css_class") ?>
                                    </label>
                                    <input type="text" id="field_css_class" size="30" onkeyup="SetFieldProperty('cssClass', this.value);"/>
                                </li>
                                <li class="visibility_setting field_setting">
                                    <label><?php _e("Visibility", "gravityforms"); ?> <?php gform_tooltip("form_field_visibility") ?></label>
                                    <div>
                                        <input type="radio" name="field_visibility" id="field_visibility_everyone" size="10" onclick="SetFieldAdminOnly(!this.checked);" />
                                        <label for="field_visibility_everyone" class="inline">
                                            <?php _e("Everyone", "gravityforms"); ?>
                                        </label>
                                        &nbsp;&nbsp;
                                        <input type="radio" name="field_visibility" id="field_visibility_admin" size="10" onclick="SetFieldAdminOnly(this.checked);" />
                                        <label for="field_visibility_admin" class="inline">
                                            <?php _e("Admin Only", "gravityforms"); ?>
                                        </label>
                                    </div>
                                    <br class="clear" />
                                </li>
                                <li class="prepopulate_field_setting field_setting">
                                    <label for="field_prepopulate">
                                        <?php _e("Incoming Field Data", "gravityforms"); ?>
                                        <?php gform_tooltip("form_field_prepopulate") ?>
                                    </label>

                                    <input type="checkbox" id="field_prepopulate" onclick="SetFieldProperty('allowsPrepopulate', this.checked); ToggleInputName()"/> <label for="field_prepopulate" class="inline"><?php _e("Allow field to be populated dynamically", "gravityforms") ?></label>
                                    <br/>
                                    <div id="field_input_name_container" style="display:none; padding-top:10px;">
                                        <!-- content dynamically created from js.php -->
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td valign="top" align="right">
                    <div id="add_fields" style="text-align:left; width:285px; padding:0 20px 0 15px;">
                        <div id="floatMenu">
                        <h3><?php _e("Add Fields", "gravityforms"); ?></h3>

                        <!-- begin add button boxes -->


                        <ul id="sidebarmenu1" class="menu collapsible expandfirst">
					<li id="add-standard-buttons">

						<div class="button-title-link"><div class="add-buttons-title"><?php _e("Standard Fields", "gravityforms"); ?> <?php gform_tooltip("form_standard_fields", "tooltip_left") ?></div></div>

						<ul>
							<li class="add-buttons">
								<ol class="field_type">
			                        <li><input type="button" class="button" value="<?php _e("Single Line Text", "gravityforms"); ?>" onclick="StartAddField('text');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Paragraph Text", "gravityforms"); ?>" onclick="StartAddField('textarea');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Drop Down", "gravityforms"); ?>" onclick="StartAddField('select');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Number", "gravityforms"); ?>" onclick="StartAddField('number');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Checkboxes", "gravityforms"); ?>" onclick="StartAddField('checkbox');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Multiple Choice", "gravityforms"); ?>" onclick="StartAddField('radio');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Section Break", "gravityforms"); ?>" onclick="StartAddField('section');"/></li>
                                    <li><input type="button" class="button" value="<?php _e("Hidden", "gravityforms"); ?>" onclick="StartAddField('hidden');"/></li>
			                    </ol>
							</li>
						</ul>
					</li>
					<li id="add-advanced-buttons">

						<div class="button-title-link"><div class="add-buttons-title"><?php _e("Advanced Fields", "gravityforms"); ?> <?php gform_tooltip("form_advanced_fields", "tooltip_left") ?></div></div>

						<ul>
							<li class="add-buttons">

								<ol class="field_type">
			                        <li><input type="button" class="button" value="<?php _e("Name", "gravityforms"); ?>" onclick="StartAddField('name');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Date", "gravityforms"); ?>" onclick="StartAddField('date');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Time", "gravityforms"); ?>" onclick="StartAddField('time');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Phone", "gravityforms"); ?>" onclick="StartAddField('phone');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Address", "gravityforms"); ?>" onclick="StartAddField('address');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Website", "gravityforms"); ?>" onclick="StartAddField('website');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Email", "gravityforms"); ?>" onclick="StartAddField('email');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("File Upload", "gravityforms"); ?>" onclick="StartAddField('fileupload');"/></li>
			                        <li><input type="button" class="button" value="reCAPTCHA" onclick="AddCaptchaField();"/></li>
			                    </ol>
							</li>
						</ul>
					</li>
					<li id="add-post-buttons">

						<div class="button-title-link"><div class="add-buttons-title"><?php _e("Post Fields", "gravityforms"); ?> <?php gform_tooltip("form_post_fields", "tooltip_left") ?></div></div>

						<ul>
							<li class="add-buttons">

								<ol class="field_type">
			                        <li><input type="button" class="button" value="<?php _e("Title", "gravityforms"); ?>" onclick="StartAddField('post_title');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Body", "gravityforms"); ?>" onclick="StartAddField('post_content');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Excerpt", "gravityforms"); ?>" onclick="StartAddField('post_excerpt');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Tags", "gravityforms"); ?>" onclick="StartAddField('post_tags');"/></li>
                                    <li><input type="button" class="button" value="<?php _e("Category", "gravityforms"); ?>" onclick="StartAddField('post_category');"/></li>
                                    <li><input type="button" class="button" value="<?php _e("Image", "gravityforms"); ?>" onclick="StartAddField('post_image');"/></li>
			                        <li><input type="button" class="button" value="<?php _e("Custom Field", "gravityforms"); ?>" onclick="StartAddField('post_custom_field');"/></li>
			                    </ol>
							</li>
						</ul>
					</li>
					</ul>
					<br style="clear:both;"/>
					<!--end add button boxes -->
				</div>
                    </div>
                </td>
            </tr>
        </table>
        </div>

        <?php
        require_once(self::get_base_path() . "/js.php");
    }

    public static function is_empty($field){
        switch($field["type"]){
            case "post_image" :
            case "fileupload" :
                $input_name = "input_" . $field["id"];
                return empty($_FILES[$input_name]['name']);
            break;
        }

        if(is_array($field["inputs"]))
        {
            foreach($field["inputs"] as $input){
                $value = $_POST["input_" . str_replace('.', '_', $input["id"])];
                if(!empty($value))
                    return false;
            }
            return true;
        }
        else{
            $value = $_POST["input_" . $field["id"]];
            if(is_array($value)){
                //empty if any of the inputs are empty (for inputs with the same name)
                foreach($value as $input){
                    if(empty($input))
                        return true;
                }
                return false;
            }
            else{
                return empty($value);
            }
        }
    }

    public static function validate(&$form){
        $is_valid = true;
        foreach($form["fields"] as &$field){
             $value = self::get_field_value($field);

            //display error message if field is marked as required and the submitted value is empty
            if($field["isRequired"] && self::is_empty($field)){
                $field["failed_validation"] = true;
                $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required. Please enter a value.", "gravityforms") : $field["errorMessage"];
                $is_valid = false;
            }
            //display error if field does not allow duplicates and the submitted value already exists
            else if($field["noDuplicates"] && RGFormsModel::is_duplicate($form["id"], $field, $value)){
                $field["failed_validation"] = true;
                $field["validation_message"] = is_array($value) ? __("This field requires an unique entry and the values you entered have been already been used", "gravityforms") :  __(sprintf("This field requires an unique entry and '%s' has already been used", $value), "gravityforms");
                $is_valid = false;
            }
            else{
                switch($field["type"]){
                    case "name" :
                        if($field["isRequired"] && $field["nameFormat"] != "simple")
                        {
                            $first = $_POST["input_" . $field["id"] . "_3"];
                            $last = $_POST["input_" . $field["id"] . "_6"];
                            if(empty($first) || empty($last)){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required. Please enter the first and last name.", "gravityforms") : $field["errorMessage"];
                                $is_valid = false;
                            }
                        }

                    break;

                    case "address" :
                        if($field["isRequired"])
                        {
                            $street = $_POST["input_" . $field["id"] . "_1"];
                            $city = $_POST["input_" . $field["id"] . "_3"];
                            $state = $_POST["input_" . $field["id"] . "_4"];
                            $zip = $_POST["input_" . $field["id"] . "_5"];
                            $country = $_POST["input_" . $field["id"] . "_6"];
                            if(empty($street) || empty($city)|| empty($state) || empty($zip) || empty($country)){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required. Please enter a complete address.", "gravityforms") : $field["errorMessage"];
                                $is_valid = false;
                            }
                        }

                    break;

                    case "email" :
                        if(!empty($value) && !self::is_valid_email($value)){
                            $field["failed_validation"] = true;
                            $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid email address.", "gravityforms"): $field["errorMessage"];
                            $is_valid = false;
                        }
                    break;

                    case "number" :
                        if(trim($value) != '' && !self::validate_range($field, $value)){
                            $field["failed_validation"] = true;
                            $is_valid = false;
                        }
                    break;

                    case "phone" :

                        $regex = '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/';
                        if($field["phoneFormat"] == "standard" && !empty($value) && !preg_match($regex, $value)){
                            $field["failed_validation"] = true;
                            if(!empty($field["errorMessage"]))
                                $field["validation_message"] = $field["errorMessage"];
                            $is_valid = false;
                        }
                    break;

                    case "date" :
                        if(is_array($value) && empty($value[0]))
                            $value = null;

                        if(!empty($value)){
                            $format = empty($field["dateFormat"]) ? "mdy" : $field["dateFormat"];
                            $date = self::parse_date($value, $format);

                            if(empty($date) || !checkdate($date["month"], $date["day"], $date["year"])){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __(sprintf("Please enter a valid date in the format (%s).", $format == "mdy" ? "mm/dd/yyyy" : "dd/mm/yyyy"), "gravityforms") : $field["errorMessage"];
                                $is_valid = false;
                            }
                        }
                    break;

                    case "time" :

                        //create variable values if time came in one field
                        if(!is_array($value) && !empty($value)){
                            preg_match('/^(\d*):(\d*) (.*)$/', $value, $matches);
                            $value = array();
                            $value[0] = $matches[1];
                            $value[1] = $matches[2];
                        }

                        $hour = $value[0];
                        $minute = $value[1];

                        if(empty($hour) && empty($minute))
                            break;

                        $is_valid_format = is_numeric($hour) && is_numeric($minute);

                        if(!$is_valid_format || $hour <= 0 || $hour > 12 || $minute < 0 || $minute >= 60)
                        {
                            $field["failed_validation"] = true;
                            $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid time." , "gravityforms"): $field["errorMessage"];
                            $is_valid = false;
                        }
                    break;

                    case "website" :
                        if($value == "http://"){
                            $value = "";
                            if($field["isRequired"]){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required. Please enter a value.", "gravityforms") : $field["errorMessage"];
                                $is_valid = false;
                            }
                        }

                        if(!empty($value) && !preg_match('!^(http|https)://([\w-]+\.)+[\w-]+(/[\w- ./?%&=]*)?$!', $value)){
                            $field["failed_validation"] = true;
                            $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid Website URL (i.e. http://nythemes.com/gravityforms).", "gravityforms") : $field["errorMessage"];
                            $is_valid = false;
                        }
                    break;

                    case "captcha" :
                        if(!function_exists("recaptcha_get_html")){
                            require_once(self::get_base_path() . '/recaptchalib.php');
                        }

                        $privatekey = get_option("rg_gforms_captcha_private_key");
                        $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

                        if (!$resp->is_valid) {
                            $field["failed_validation"] = true;
                            $field["validation_message"] = empty($field["errorMessage"]) ? __("The reCAPTCHA wasn't entered correctly. Go back and try it again.", "gravityforms") : $field["errorMessage"];
                            $is_valid = false;
                        }

                    break;

                    case "fileupload" :
                    case "post_image" :
                        $info = pathinfo($_FILES["input_" . $field["id"]]["name"]);
                        $allowedExtensions = self::clean_extensions(explode(",", strtolower($field["allowedExtensions"])));
                        $extension = strtolower($info["extension"]);
                        if(!empty($field["allowedExtensions"]) && !empty($info["basename"]) && !in_array($extension, $allowedExtensions)){
                            $field["failed_validation"] = true;
                            $field["validation_message"] = empty($field["errorMessage"]) ? sprintf(__("The uploaded file type is not allowed. Must be one of the following: %s", "gravityforms"), strtolower($field["allowedExtensions"]) )  : $field["errorMessage"];
                            $is_valid = false;
                        }
                    break;
                }
            }
        }
        return $is_valid;
    }

    private static function clean_extensions($extensions){
        $count = sizeof($extensions);
        for($i=0; $i<$count; $i++){
            $extensions[$i] = str_replace(".", "",str_replace(" ", "", $extensions[$i]));
        }
        return $extensions;
    }

    private static function validate_range($field, $value){
        if( !is_numeric($value) ||
            (is_numeric($field["rangeMin"]) && $value < $field["rangeMin"]) ||
            (is_numeric($field["rangeMax"]) && $value > $field["rangeMax"])
        )
            return false;
        else
            return true;
    }

    public static function handle_submission($form, &$lead){

        //insert submissing in DB
        RGFormsModel::save_lead($form, $lead);

        //reading lead that was just saved
        $lead = RGFormsModel::get_lead($lead["id"]);

        //send auto-responder and notification emails
        self::send_emails($form, $lead);

        //display confirmation message or redirect to confirmation page
        return self::handle_confirmation($form, $lead);

    }

    public static function handle_confirmation($form, $lead){
        if($form["confirmation"]["type"] == "message"){
           return "<div id='gforms_confirmation_message'>" . self::replace_variables($form["confirmation"]["message"], $form, $lead) . "</div>";
        }
        else{
            if(!empty($form["confirmation"]["pageId"])){
                $url = get_permalink($form["confirmation"]["pageId"]);
            }
            else{
                $url_info = parse_url($form["confirmation"]["url"]);
                $query_string = $url_info["query"];
                $dynamic_query = self::replace_variables($form["confirmation"]["queryString"], $form, $lead, true);
                $query_string .= empty($url_info["query"]) || empty($dynamic_query) ? $dynamic_query : "&" . $dynamic_query;

                if(!empty($url_info["fragment"]))
                    $query_string .= "#" . $url_info["fragment"];

                $url = $url_info["scheme"] . "://" . $url_info["host"] . $url_info["path"] . "?" . $query_string;
            }
            return "<script>document.location.href='$url';</script>";
        }
    }

    private static function is_valid_email($email){
        return preg_match('/^(([a-zA-Z0-9_\.\-])+\@((([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+|localhost) *,? *)+$/', $email);
    }

    private static function send_email($from, $to, $bcc, $reply_to, $subject, $message){

        //invalid to email address or no content. can't send email
        if(!self::is_valid_email($to) || (empty($subject) && empty($message)))
            return;

        if(!self::is_valid_email($from))
            $from = get_bloginfo("admin_email");

        //invalid from address. can't send email
        if(!self::is_valid_email($from))
            return;

        $headers = "From: \"$from\" <$from> \r\n";
        $headers .= self::is_valid_email($reply_to) ? "Reply-To: $reply_to\r\n" :"";
        $headers .= self::is_valid_email($bcc) ? "Bcc: $bcc\r\n" :"";
        $headers .= 'Content-type: text/html; charset=' . get_option('blog_charset') . "\r\n";

        $result = wp_mail($to, $subject, $message, $headers);

    }

    public static function send_emails($form, $lead){

        //handling autoresponder email
        $to = stripslashes($_POST["input_" . $form["autoResponder"]["toField"]]);
        $subject = self::replace_variables($form["autoResponder"]["subject"], $form, $lead );
        $message = self::replace_variables($form["autoResponder"]["message"], $form, $lead);
        self::send_email($form["autoResponder"]["from"], $to, $form["autoResponder"]["bcc"], $form["autoResponder"]["replyTo"], $subject, $message);

        //handling admin notification email
        $subject = self::replace_variables($form["notification"]["subject"], $form, $lead);
        $message = self::replace_variables($form["notification"]["message"], $form, $lead);
        $from = empty($form["notification"]["fromField"]) ? $form["notification"]["from"] : stripslashes($_POST["input_" . $form["notification"]["fromField"]]);
        $replyTo = empty($form["notification"]["replyToField"]) ? $form["notification"]["replyTo"] : stripslashes($_POST["input_" . $form["notification"]["replyToField"]]);


        $form_id = $form["id"];
        //Filters the admin notification email to address. Allows users to change email address before notification is sent
        $to = apply_filters("gform_notification_email_$form_id" , apply_filters("gform_notification_email", $form["notification"]["to"], $lead), $lead);

        self::send_email($from, $to, $form["notification"]["bcc"], $replyTo, $subject, $message);
    }


    public static function replace_variables($text, $form, $lead, $url_encode = false){
        $text = nl2br($text);

        //Replacing field variables
        preg_match_all('/{.*?:(\d+(\.\d+)?)}/mi', $text, $matches, PREG_SET_ORDER);
        if(is_array($matches))
        {
            foreach($matches as $match){
                $input_id = $match[1];

                $field = RGFormsModel::get_field($form,$input_id);
                $value = RGFormsModel::get_lead_field_value($lead, $field);
                if(is_array($value))
                    $value = $value[$input_id];

                $value = nl2br(self::escape_text($value));

                if($url_encode)
                    $value = urlencode($value);

                $text = str_replace($match[0], $value , $text);
            }
        }

        //replacing global variables
        //form title
        $text = str_replace("{form_title}", $url_encode ? urlencode($form["title"]) : $form["title"], $text);

        //date (mm/dd/yyyy)
        $text = str_replace("{date_mdy}", $url_encode ? urlencode(date("m/d/Y")) : date("m/d/Y"), $text);

        //date (dd/mm/yyyy)
        $text = str_replace("{date_dmy}", $url_encode ? urlencode(date("d/m/Y")) : date("d/m/Y"), $text);

        //ip
        $text = str_replace("{ip}", $url_encode ? urlencode($_SERVER['REMOTE_ADDR']) : $_SERVER['REMOTE_ADDR'], $text);

        //all submitted fields
        $text = str_replace("{all_fields}", self::get_submitted_fields($form, $lead), $text);

         //embed url
        $text = str_replace("{embed_url}", $url_encode ? urlencode(RGFormsModel::get_current_page_url()) : RGFormsModel::get_current_page_url(), $text);

        //form id
        $text = str_replace("{form_id}", $url_encode ? urlencode($form["id"]) : $form["id"], $text);

        //entry id
        $text = str_replace("{entry_id}", $url_encode ? urlencode($lead["id"]) : $lead["id"], $text);

        //entry url
        $entry_url = get_bloginfo("wpurl") . "/wp-admin/admin.php?page=gf_entries&view=entry&id=" . $form["id"] . "&lid=" . $lead["id"];
        $text = str_replace("{entry_url}", $url_encode ? urlencode($entry_url) : $entry_url, $text);

        //post id
        $text = str_replace("{post_id}", $url_encode ? urlencode($lead["post_id"]) : $lead["post_id"], $text);

        //post edit url
        $post_url = get_bloginfo("wpurl") . "/wp-admin/post.php?action=edit&post=" . $lead["post_id"];
        $text = str_replace("{post_edit_url}", $url_encode ? urlencode($post_url) : $post_url, $text);

        return $text;
    }

    public static function get_submitted_fields($form, $lead){
        $field_data = '<table width="99%" border="0" cellpadding="1" cellpsacing="0" bgcolor="#EAEAEA"><tr><td><table width="100%" border="0" cellpadding="5" cellpsacing="0" bgcolor="#FFFFFF">';
        foreach($form["fields"] as $field){
            $field_label = self::escape_text(self::get_label($field));

            switch($field["type"]){
                case "captcha" :
                    break;

                case "section" :
                    $field_data .= sprintf('<tr><td colspan="2" style="font-size:14px; font-weight:bold; background-color:#EEE; border-bottom:1px solid #DFDFDF; padding:7px 7px">%s</td></tr>', $field_label);
                    break;

                default :
                    $value = RGFormsModel::get_lead_field_value($lead, $field);
                    $field_value = self::get_lead_field_display($field, $value);
                    $field_data .= sprintf('<tr bgcolor="#EAF2FA"><td colspan="2"><font style="font-family:verdana; font-size:12px;"><strong>%s</strong></font></td></tr><tr bgcolor="#FFFFFF"><td width="20">&nbsp;</td><td><font style="font-family:verdana; font-size:12px;">%s</font></td></tr>', $field_label, empty($field_value) ? "&nbsp;" : $field_value);
            }
        }
        $field_data .= "</table></td></tr></table>";
        return $field_data;
    }

    //Hierarchical category functions copied from WordPress core and modified.
    private static function _cat_rows( $categories, &$count, &$output, $parent = 0, $level = 0, $page = 1, $per_page = 9999999 ) {
        if ( empty($categories) ) {
            $args = array('hide_empty' => 0);
            if ( !empty($_POST['search']) )
                $args['search'] = $_POST['search'];
            $categories = get_categories( $args );
        }

        if ( !$categories )
            return false;

        $children = self::_get_term_hierarchy('category');

        $start = ($page - 1) * $per_page;
        $end = $start + $per_page;
        $i = -1;
        foreach ( $categories as $category ) {
            if ( $count >= $end )
                break;

            $i++;

            if ( $category->parent != $parent )
                continue;

            // If the page starts in a subtree, print the parents.
            if ( $count == $start && $category->parent > 0 ) {
                $my_parents = array();
                while ( $my_parent) {
                    $my_parent = get_category($my_parent);
                    $my_parents[] = $my_parent;
                    if ( !$my_parent->parent )
                        break;
                    $my_parent = $my_parent->parent;
                }
                $num_parents = count($my_parents);
                while( $my_parent = array_pop($my_parents) ) {
                    self::_cat_row( $my_parent, $level - $num_parents, $output );
                    $num_parents--;
                }
            }

            if ( $count >= $start )
                self::_cat_row( $category, $level, $output );

            unset($categories[$i]); // Prune the working set
            $count++;

            if ( isset($children[$category->term_id]) )
                self::_cat_rows( $categories, $count, $output, $category->term_id, $level + 1, $page, $per_page );

    }
}
    private static function _cat_row( $category, $level, &$output, $name_override = false ) {
        static $row_class = '';

        $cat = get_category( $category, OBJECT, 'display' );

        $default_cat_id = (int) get_option( 'default_category' );
        $pad = str_repeat( '&#8212; ', $level );
        $name = ( $name_override ? $name_override : $pad . ' ' . $cat->name );

        $cat->count = number_format_i18n( $cat->count );

        $output .="
        <tr class='author-self status-inherit' valign='top'>
            <th scope='row' class='check-column'><input type='checkbox' class='gfield_category_checkbox' value='$cat->term_id' name='" . esc_attr($cat->name) . "' onclick='SetSelectedCategories();' /></th>
            <td class='gfield_category_cell'>$name</td>
        </tr>";
    }
    private static function _get_term_hierarchy($taxonomy) {
        if ( !is_taxonomy_hierarchical($taxonomy) )
            return array();
        $children = get_option("{$taxonomy}_children");
        if ( is_array($children) )
            return $children;

        $children = array();
        $terms = get_terms($taxonomy, 'get=all');
        foreach ( $terms as $term ) {
            if ( $term->parent > 0 )
                $children[$term->parent][] = $term->term_id;
        }
        update_option("{$taxonomy}_children", $children);

        return $children;
    }

    //Returns the url of the plugin's root folder
    public function get_base_url(){
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_URL . "/" . $folder;
    }

    //Returns the physical path of the plugin's root folder
    public function get_base_path(){
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . "/" . $folder;
    }
}

function gravity_form($id, $display_title=true, $display_description=true){
    echo RGForms::get_form($id, $display_title, $display_description);
}
?>
