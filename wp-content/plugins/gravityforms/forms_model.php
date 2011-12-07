<?php

require_once(ABSPATH . "/wp-includes/post.php");

define("GFORMS_MAX_FIELD_LENGTH", 200);

class RGFormsModel{

    public static function get_form_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_form";
    }

    public static function get_meta_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_form_meta";
    }

    public static function get_form_view_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_form_view";
    }

    public static function get_lead_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_lead";
    }
    public static function get_lead_notes_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_lead_notes";
    }

    public static function get_lead_details_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_lead_detail";
    }

    public static function get_lead_details_long_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_lead_detail_long";
    }

     public static function get_lead_view_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_lead_view";
    }

    public static function get_forms($is_active = null){
        global $wpdb;
        $form_table_name =  self::get_form_table_name();
        $lead_table_name = self::get_lead_table_name();
        $view_table_name = self::get_form_view_table_name();

        $active_clause = $is_active !== null ? $wpdb->prepare("WHERE is_active=%d", $is_active) : "";
        $sql = "SELECT f.id, f.title, f.date_created, f.is_active, l.lead_count, v.view_count
                FROM $form_table_name f
                LEFT OUTER JOIN
                    (SELECT form_id, count(id) as lead_count FROM $lead_table_name l GROUP BY form_id) l ON f.id = l.form_id
                LEFT OUTER JOIN
                    (SELECT form_id, sum(count) as view_count FROM $view_table_name GROUP BY form_id) v ON f.id = v.form_id
                $active_clause";

        return $wpdb->get_results($sql);
    }

    public static function get_form_counts($form_id){
        global $wpdb;
        $lead_table_name = self::get_lead_table_name();

         $sql = $wpdb->prepare(" SELECT count(l.id)
                                 FROM $lead_table_name l
                                 WHERE is_read=0
                                 AND form_id=%d", $form_id);

         $unread_count = $wpdb->get_var($sql);

         $sql = $wpdb->prepare(" SELECT count(l.id)
                                 FROM $lead_table_name l
                                 WHERE is_starred=1
                                 AND form_id=%d", $form_id);

         $starred_count = $wpdb->get_var($sql);

         $sql = $wpdb->prepare(" SELECT count(l.id)
                                 FROM $lead_table_name l
                                 WHERE form_id=%d", $form_id);

         $total_count = $wpdb->get_var($sql);

         return array("total" => $total_count, "unread" => $unread_count, "starred" => $starred_count);

    }

    public static function get_form_summary(){
        global $wpdb;
        $form_table_name =  self::get_form_table_name();
        $lead_table_name = self::get_lead_table_name();

        $sql = "SELECT f.id, f.title, count(l.id) as unread_count
                FROM $lead_table_name l
                INNER JOIN $form_table_name f ON f.id = l.form_id
                WHERE is_read=0
                GROUP BY form_id
                ORDER BY unread_count DESC, title";

        //getting number of unread leads for all forms
        $unread_results = $wpdb->get_results($sql, ARRAY_A);

        $sql = "SELECT f.id, f.title, max(l.date_created) as last_lead_date, 0 as unread_count
                FROM $lead_table_name l
                INNER JOIN $form_table_name f ON f.id = l.form_id
                GROUP BY form_id
                ORDER BY title";

        $lead_date_results = $wpdb->get_results($sql, ARRAY_A);

        $sql = "SELECT id, title, '' as last_lead_date, 0 as unread_count
                FROM $form_table_name
                ORDER BY title";

        $forms = $wpdb->get_results($sql, ARRAY_A);


        for($i=0; $count = sizeof($forms), $i<$count; $i++){
            if(is_array($unread_results)){
                foreach($unread_results as $unread_result){
                    if($unread_result["id"] == $forms[$i]["id"]){
                        $forms[$i]["unread_count"] = $unread_result["unread_count"];
                        break;
                    }
                }
            }

            if(is_array($unread_results)){
                foreach($lead_date_results as $lead_date_result){
                    if($lead_date_result["id"] == $forms[$i]["id"]){
                        $forms[$i]["last_lead_date"] = $lead_date_result["last_lead_date"];
                        break;
                    }
                }
            }

        }

        return $forms;
    }


    public static function get_form_count(){
        global $wpdb;
        $form_table_name =  self::get_form_table_name();
        $results = $wpdb->get_results("SELECT count(0) as count FROM $form_table_name UNION ALL SELECT count(0) as count FROM $form_table_name WHERE is_active=1 ");
        return array(   "total" => intval($results[0]->count),
                        "active" => intval($results[1]->count),
                        "inactive" => intval($results[0]->count) - intval($results[1]->count)
                        );
    }

     public static function get_form($form_id){
        global $wpdb;
        $table_name =  self::get_form_table_name();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id=%d", $form_id));
        return $results[0];

    }

    public static function get_form_meta($form_id){
        global $wpdb;

        $table_name =  self::get_meta_table_name();
        return maybe_unserialize($wpdb->get_var($wpdb->prepare("SELECT display_meta FROM $table_name WHERE form_id=%d", $form_id)));
    }

     public static function get_grid_column_meta($form_id){
        global $wpdb;

        $table_name =  self::get_meta_table_name();
        return maybe_unserialize($wpdb->get_var($wpdb->prepare("SELECT entries_grid_meta FROM $table_name WHERE form_id=%d", $form_id)));
    }

    public static function update_grid_column_meta($form_id, $columns){
        global $wpdb;

        $table_name = self::get_meta_table_name();
        $meta = maybe_serialize(stripslashes_deep($columns) );
        $wpdb->query( $wpdb->prepare("UPDATE $table_name SET entries_grid_meta=%s WHERE form_id=%d", $meta, $form_id) );
    }


    public static function get_lead_detail_id($current_fields, $field_number){
        foreach($current_fields as $field)
            if($field->field_number == $field_number)
                return $field->id;

        return 0;
    }

    public static function update_form_active($form_id, $is_active){
        global $wpdb;
        $form_table = self::get_form_table_name();
        $sql = $wpdb->prepare("UPDATE $form_table SET is_active=%d WHERE id=%d", $is_active, $form_id);
        $wpdb->query($sql);
    }

    public static function update_forms_active($forms, $is_active){
        foreach($forms as $form_id)
            self::update_form_active($form_id, $is_active);
    }

    public static function update_leads_property($leads, $property_name, $property_value){
        foreach($leads as $lead)
            self::update_lead_property($lead, $property_name, $property_value);
    }

    public static function update_lead_property($lead_id, $property_name, $property_value){
        global $wpdb;
        $lead_table = self::get_lead_table_name();
        $wpdb->update($lead_table, array($property_name => $property_value ), array("id" => $lead_id));
    }

    public static function delete_leads($leads){
        foreach($leads as $lead_id)
            self::delete_lead($lead_id);
    }

    public static function delete_forms($forms){
        foreach($forms as $form_id)
            self::delete_form($form_id);
    }

    public static function delete_form($form_id){
        global $wpdb;

        if(!RGForms::current_user_can_any("gravityforms_delete_forms"))
            die(__("You don't have adequate permission to delete forms.", "gravityforms"));

        $lead_table = self::get_lead_table_name();
        $lead_notes_table = self::get_lead_notes_table_name();
        $lead_detail_table = self::get_lead_details_table_name();
        $lead_detail_long_table = self::get_lead_details_long_table_name();
        $form_meta_table = self::get_meta_table_name();
        $form_view_table = self::get_form_view_table_name();
        $form_table = self::get_form_table_name();

        //Delete from detail long
        $sql = $wpdb->prepare(" DELETE FROM $lead_detail_long_table
                                WHERE lead_detail_id IN(
                                    SELECT ld.id FROM $lead_detail_table ld
                                    INNER JOIN $lead_table l ON l.id = ld.lead_id
                                    WHERE l.form_id=%d AND ld.form_id=%d
                                )", $form_id, $form_id);
        $wpdb->query($sql);

        //Delete from lead details
        $sql = $wpdb->prepare(" DELETE FROM $lead_detail_table
                                WHERE lead_id IN (
                                    SELECT id FROM $lead_table WHERE form_id=%d
                                )", $form_id);
        $wpdb->query($sql);

        //Delete from lead notes
        $sql = $wpdb->prepare(" DELETE FROM $lead_notes_table
                                WHERE lead_id IN (
                                    SELECT id FROM $lead_table WHERE form_id=%d
                                )", $form_id);
        $wpdb->query($sql);

        //Delete from lead
        $sql = $wpdb->prepare("DELETE FROM $lead_table WHERE form_id=%d", $form_id);
        $wpdb->query($sql);

        //Delete form meta
        $sql = $wpdb->prepare("DELETE FROM $form_meta_table WHERE form_id=%d", $form_id);
        $wpdb->query($sql);

        //Delete form view
        $sql = $wpdb->prepare("DELETE FROM $form_view_table WHERE form_id=%d", $form_id);
        $wpdb->query($sql);

        //Delete form
        $sql = $wpdb->prepare("DELETE FROM $form_table WHERE id=%d", $form_id);
        $wpdb->query($sql);
    }

    public static function duplicate_form($form_id){
        global $wpdb;

        if(!RGForms::current_user_can_any("gravityforms_create_form"))
            die(__("You don't have adequate permission to create forms.", "gravityforms"));

        //finding unique title
        $form = self::get_form($form_id);
        $count = 2;
        $title = $form->title . " - Copy 1";
        while(!self::is_unique_title($title)){
            $title = $form->title . " - Copy $count";
            $count++;
        }

        //creating new form
        $new_id = self::insert_form($title);

        //copying form meta
        $meta = self::get_form_meta($form_id);
        $meta["title"] = $title;
        $meta["id"] = $new_id;
        self::update_form_meta($new_id, $meta);

        return $new_id;
    }

    private static function is_unique_title($title){
        $forms = self::get_forms();
        foreach($forms as $form){
            if(strtolower($form->title) == strtolower($title))
                return false;
        }

        return true;
    }

    public static function insert_form($form_title){
        global $wpdb;
        $form_table_name =  $wpdb->prefix . "rg_form";

        //creating new form
        $wpdb->query($wpdb->prepare("INSERT INTO $form_table_name(title, date_created) VALUES(%s, utc_timestamp())", $form_title));

        //returning newly created form id
        return $wpdb->get_var("SELECT LAST_INSERT_ID();");

    }

    public static function update_form_meta($form_id, $form_meta){
        global $wpdb;
        $meta_table_name = self::get_meta_table_name();
        $form_meta = maybe_serialize($form_meta);

        if(intval($wpdb->get_var($wpdb->prepare("SELECT count(0) FROM $meta_table_name WHERE form_id=%d", $form_id))) > 0)
            $wpdb->query( $wpdb->prepare("UPDATE $meta_table_name SET display_meta=%s WHERE form_id=%d", $form_meta, $form_id) );
        else
            $wpdb->query( $wpdb->prepare("INSERT INTO $meta_table_name(form_id, display_meta) VALUES(%d, %s)", $form_id, $form_meta ) );
    }

    public static function delete_file($lead_id, $field_id){
        global $wpdb;

        if($lead_id == 0 || $field_id == 0)
            return;

        $lead_detail_table = self::get_lead_details_table_name();

        //Deleting file
        $sql = $wpdb->prepare("SELECT value FROM $lead_detail_table WHERE lead_id=%d AND field_number BETWEEN %f AND %f", $lead_id, $field_id - 0.001, $field_id + 0.001);
        $file_path = $wpdb->get_var($sql);

        //Convert from url to physical path
        $file_path = str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $file_path);
        unlink($file_path);

        //Delete from lead details
        $sql = $wpdb->prepare("DELETE FROM $lead_detail_table WHERE lead_id=%d AND field_number BETWEEN %f AND %f", $lead_id, $field_id - 0.001, $field_id + 0.001);
        $wpdb->query($sql);
    }

    public static function delete_field($form_id, $field_id){
        global $wpdb;

        if($form_id == 0)
            return;

        $lead_table = self::get_lead_table_name();
        $lead_detail_table = self::get_lead_details_table_name();
        $lead_detail_long_table = self::get_lead_details_long_table_name();

        //Deleting field from form meta
        $form = self::get_form_meta($form_id);
        $count = sizeof($form["fields"]);
        for($i = $count-1; $i >= 0; $i--){
            if($form["fields"][$i]["id"] == $field_id){
                unset($form["fields"][$i]);
            }
        }
        $form["fields"] = array_values($form["fields"]);
        self::update_form_meta($form_id, $form);

        //Delete from grid column meta
        $columns = self::get_grid_column_meta($form_id);
        $count = sizeof($columns);
        for($i = $count -1; $i >=0; $i--)
        {
            if(intval($columns[$i]) == intval($field_id)){
                unset($columns[$i]);
            }
        }
        self::update_grid_column_meta($form_id, $columns);

        //Delete from detail long
        $sql = $wpdb->prepare(" DELETE FROM $lead_detail_long_table
                                WHERE lead_detail_id IN(
                                    SELECT id FROM $lead_detail_table WHERE form_id=%d AND field_number >= %d AND field_number < %d
                                )", $form_id, $field_id, $field_id + 1);
        $wpdb->query($sql);

        //Delete from lead details
        $sql = $wpdb->prepare("DELETE FROM $lead_detail_table WHERE form_id=%d AND field_number >= %d AND field_number < %d", $form_id, $field_id, $field_id + 1);
        $wpdb->query($sql);

        //Delete leads with no details
        $sql = $wpdb->prepare(" DELETE FROM $lead_table
                                WHERE form_id=%d
                                AND id NOT IN(
                                    SELECT DISTINCT(lead_id) FROM $lead_detail_table WHERE form_id=%d
                                )", $form_id, $form_id);
        $wpdb->query($sql);
    }

    public static function delete_lead($lead_id){
        global $wpdb;

        if(!RGForms::current_user_can_any("gravityforms_delete_entries"))
            die(__("You don't have adequate permission to delete entries.", "gravityforms"));

        $lead_table = self::get_lead_table_name();
        $lead_notes_table = self::get_lead_notes_table_name();
        $lead_detail_table = self::get_lead_details_table_name();
        $lead_detail_long_table = self::get_lead_details_long_table_name();

        //Delete from detail long
        $sql = $wpdb->prepare(" DELETE FROM $lead_detail_long_table
                                WHERE lead_detail_id IN(
                                    SELECT id FROM $lead_detail_table WHERE lead_id=%d
                                )", $lead_id);
        $wpdb->query($sql);

        //Delete from lead details
        $sql = $wpdb->prepare("DELETE FROM $lead_detail_table WHERE lead_id=%d", $lead_id);
        $wpdb->query($sql);

        //Delete from lead notes
        $sql = $wpdb->prepare("DELETE FROM $lead_notes_table WHERE lead_id=%d", $lead_id);
        $wpdb->query($sql);

        //Delete from lead
        $sql = $wpdb->prepare("DELETE FROM $lead_table WHERE id=%d", $lead_id);
        $wpdb->query($sql);
    }

    public static function add_note($lead_id, $user_id, $user_name, $note){
        global $wpdb;

        if(!RGForms::current_user_can_any("gravityforms_edit_entry_notes"))
            die(__("You don't have adequate permission to add notes.", "gravityforms"));

        $table_name = self::get_lead_notes_table_name();
        $sql = $wpdb->prepare("INSERT INTO $table_name(lead_id, user_id, user_name, value, date_created) values(%d, %d, %s, %s, utc_timestamp())", $lead_id, $user_id, $user_name, $note);
        $wpdb->query($sql);
    }

    public static function delete_note($note_id){
        global $wpdb;

        if(!RGForms::current_user_can_any("gravityforms_edit_entry_notes"))
            die(__("You don't have adequate permission to delete notes.", "gravityforms"));

        $table_name = self::get_lead_notes_table_name();
        $sql = $wpdb->prepare("DELETE FROM $table_name WHERE id=%d", $note_id);
        $wpdb->query($sql);
    }

    public static function delete_notes($notes){
        if(!is_array($notes))
            return;

        foreach($notes as $note_id){
            self::delete_note($note_id);
        }
    }

    public static function get_ip(){
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        if (!$ip)
            $ip = $_SERVER["REMOTE_ADDR"];

        $ip_array = explode(",", $ip); //HTTP_X_FORWARDED_FOR can return a comma separated list of IPs. Using the first one.
        return $ip_array[0];
    }

    public static function save_lead($form, &$lead){
        global $wpdb;

        if(IS_ADMIN && !RGForms::current_user_can_any("gravityforms_edit_entries"))
            die(__("You don't have adequate permission to edit entries.", "gravityforms"));

        $lead_detail_table = self::get_lead_details_table_name();

        //Inserting lead if null
        if($lead == null){
            $lead_table = RGFormsModel::get_lead_table_name();
            $user_agent = strlen($_SERVER["HTTP_USER_AGENT"]) > 250 ? substr($_SERVER["HTTP_USER_AGENT"], 0, 250) : $_SERVER["HTTP_USER_AGENT"];

            $wpdb->query($wpdb->prepare("INSERT INTO $lead_table(form_id, ip, source_url, date_created, user_agent) VALUES(%d, %s, %s, utc_timestamp(), %s)", $form["id"], self::get_ip(), self::get_current_page_url(), $user_agent));

            //reading newly created lead id
            $lead_id = $wpdb->get_var("SELECT LAST_INSERT_ID();");
            $lead = array("id" => $lead_id);
        }

        $current_fields = $wpdb->get_results($wpdb->prepare("SELECT id, field_number FROM $lead_detail_table WHERE lead_id=%d", $lead["id"]));
        $original_post_id = $lead["post_id"];

        foreach($form["fields"] as $field){
            if(is_array($field["inputs"])){
                foreach($field["inputs"] as $input)
                    self::save_input($form, $field, $lead, $current_fields, $input["id"]);
            }
            else{
                self::save_input($form, $field, $lead, $current_fields, $field["id"]);
            }
        }

        //update post_id field if a post was created
        if($lead["post_id"] != $original_post_id)
            $wpdb->update($lead_table, array("post_id" => $lead["post_id"]),  array("id" => $lead["id"]), array("%d"), array("%d"));

    }

    private static function get_default_value($field, $input_id){
        if(!is_array($field["choices"]))
            return $field["defaultValue"];
        else if($field["type"] == "checkbox"){
            for($i=0, $count=sizeof($field["inputs"]); $i<$count; $i++){
                $input = $field["inputs"][$i];
                $choice = $field["choices"][$i];
                if($input["id"] == $input_id && $choice["isSelected"]){
                    return $choice["value"];
                }
            }
            return "";
        }
        else{
            foreach($field["choices"] as $choice){
                if($choice["isSelected"])
                    return $choice["text"];
            }
            return "";
        }

    }

    private static function get_post_fields($form){
        global $current_user;
        $post_data = array();
        $post_data["post_custom_fields"] = array();
        $categories = array();
        $images = array();

        foreach($form["fields"] as $field){
            $value = $_POST["input_" . $field["id"]];

            switch($field["type"]){
                case "post_title" :
                case "post_excerpt" :
                case "post_content" :
                    $post_data[$field["type"]] = $value;
                break;

                case "post_tags" :
                    $post_data["tags_input"] = explode(",", $value);
                break;

                case "post_custom_field" :
                    $post_data["post_custom_fields"][$field["postCustomFieldName"]] = $value;
                break;

                case "post_category" :
                    array_push($categories, $value);
                break;

                case "post_image" :
                    $title = strip_tags($_POST["input_" . $field["id"] . "_1"]);
                    $caption = strip_tags($_POST["input_" . $field["id"] . "_4"]);
                    $description = strip_tags($_POST["input_" . $field["id"] . "_7"]);
                    array_push($images, array("field_name" => "input_" . $field['id'], "title" => $title, "description" => $description, "caption" => $caption));
                break;
            }
        }

        $post_data["post_status"] = $form["postStatus"];
        $post_data["post_category"] = !empty($categories) ? $categories : array($form["postCategory"]);
        $post_data["images"] = $images;

        //setting current user as author depending on settings
        $post_data["post_author"] = $form["useCurrentUserAsAuthor"] && !empty($current_user->ID) ? $current_user->ID : $form["postAuthor"];

        return $post_data;
    }

    public static function get_custom_field_names(){
        global $wpdb;
        $keys = $wpdb->get_col( "
        SELECT meta_key
        FROM $wpdb->postmeta
        WHERE meta_key NOT LIKE '\_%'
        GROUP BY meta_key
        ORDER BY meta_id DESC");

        if ( $keys )
            natcasesort($keys);

        return $keys;
    }

    private static function get_default_post_title(){
        global $wpdb;
        $title = "Untitled";
        $count = 1;

        while($wpdb->get_var($wpdb->prepare("SELECT count(0) FROM $wpdb->posts WHERE post_title=%s", $title)) > 0){
            $title = "Untitled_$count";
            $count++;
        }
        return $title;
    }

    private static $post_images = array();
    private static function save_input($form, $field, &$lead, $current_fields, $input_id){
        global $wpdb;

        $lead_detail_table = self::get_lead_details_table_name();
        $lead_detail_long_table = self::get_lead_details_long_table_name();

        $input_name = "input_" . str_replace('.', '_', $input_id);
        $value = $_POST[$input_name];

        if(empty($value) && $field["adminOnly"] && !IS_ADMIN){
            $value = self::get_default_value($field, $input_id);
        }

        switch($field["type"])
        {
            case "post_category" :
                $cat = get_category($value);
                $value = $cat->name;
            case "post_title" :
            case "post_content" :
            case "post_excerpt" :
            case "post_tags" :
            case "post_custom_fields" :
            case "post_image":

                $value = stripslashes($value);

                //post fields don't not get updated from the admin
                if(RG_CURRENT_VIEW == "entry")
                    return;

                if(empty($lead["post_id"])){
                    $post_data = self::get_post_fields($form);
                    if(empty($post_data["post_title"]) && empty($post_data["post_content"]) && empty($post_data["post_excerpt"])){
                        $post_data["post_title"] = self::get_default_post_title();
                    }

                    $post_id = wp_insert_post($post_data);

                    //adding custom fields
                    foreach($post_data["post_custom_fields"] as $meta_name => $meta_value){
                        update_post_meta($post_id, $meta_name, $meta_value);
                    }

                    //adding post images
                    foreach($post_data["images"] as $image){
                        $image_meta= array( "post_excerpt" => $image["caption"],
                                            "post_content" => $image["description"]);

                        //adding title only if it is not empty. It will default to the file name if it is not in the array
                        if(!empty($image["title"]))
                            $image_meta["post_title"] = $image["title"];


                        //WordPress Administration API required for the media_handle_upload() function
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        require_once(ABSPATH . 'wp-admin/includes/media.php');

                        $media_id = media_handle_upload($image["field_name"], $post_id, $image_meta);

                        //saving media id so that it's url can be saved later as part of the entry
                        self::$post_images[$image["field_name"]] = $media_id;
                    }

                    if(is_numeric($post_id))
                        $lead["post_id"] = $post_id;
                }

                //saving image information as part of the entry
                if($field["type"] == "post_image"){
                    //copying file from media to entry location
                    $destination = self::get_file_upload_path($form["id"], $_FILES[$input_name]["name"]);
                    $media_id  = self::$post_images[$input_name];
                    if($destination !== false && is_numeric($media_id))
                    {
                        $file_path = get_attached_file($media_id, true);
                        if(copy($file_path, $destination["path"])){
                            $post = get_post($media_id);
                            $value = $destination["url"] . "|:|" . $post->post_title . "|:|" . $post->post_excerpt . "|:|" . $post->post_content;
                        }
                    }
                    else{
                        return;
                    }
                }

            break;

            case "phone" :
                if($field["phoneFormat"] == "standard" && preg_match('/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $value, $matches))
                    $value = sprintf("(%d)%d-%d", $matches[1], $matches[2], $matches[3]);
            break;

            case "time":

                if(!is_array($value) && !empty($value)){
                    preg_match('/^(\d*):(\d*) ?(.*)$/', $value, $matches);
                    $value = array();
                    $value[0] = $matches[1];
                    $value[1] = $matches[2];
                    $value[2] = $matches[3];
                }

                $hour = empty($value[0]) ? "0" : strip_tags($value[0]);
                $minute = empty($value[1]) ? "0" : strip_tags($value[1]);
                $ampm = strip_tags($value[2]);

                /*if($ampm == "am" && $hour == 12)
                    $hour = 0;
                else if($ampm == "pm" && $hour != 12)
                    $hour +=12;
                */

                if(!(empty($hour) && empty($minute)))
                    $value = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
                else
                    $value = "";

            break;

            case "date" :
                $format = empty($field["dateFormat"]) ? "mdy" : $field["dateFormat"];
                $date_info = RGForms::parse_date($value, $format);
                if(!empty($date_info))
                    $value = sprintf("%d-%02d-%02d", $date_info["year"], $date_info["month"], $date_info["day"]);

            break;

            case "fileupload" :
                //in the admin, only update file upload field if a new file was posted
                if(empty($_FILES[$input_name]["name"]))
                    return;

                $value = self::upload_file($form["id"], $_FILES[$input_name]);
            break;

            default:
                $value = strip_tags(stripslashes($value));
            break;
        }


        //ignore fields that have not changed
        if($lead != null && $value == $lead[$input_id])
            return;

        if(!empty($value)){

            $truncated_value = substr($value, 0, GFORMS_MAX_FIELD_LENGTH);
            $lead_detail_id = self::get_lead_detail_id($current_fields, $input_id);
            if($lead_detail_id > 0){
                $wpdb->update($lead_detail_table, array("value" => $truncated_value), array("id" => $lead_detail_id), array("%s"), array("%d"));

                //insert, update or delete long value
                $sql = $wpdb->prepare("SELECT count(0) FROM $lead_detail_long_table WHERE lead_detail_id=%d", $lead_detail_id);
                $has_long_field = intval($wpdb->get_var($sql)) > 0;

                //delete long field if value has been shortened
                if($has_long_field && strlen($value) <= GFORMS_MAX_FIELD_LENGTH){
                    $sql = $wpdb->prepare("DELETE FROM $lead_detail_long_table WHERE lead_detail_id=%d", $lead_detail_id);
                    $wpdb->query($sql);
                }
                //update long field
                else if($has_long_field){
                    $wpdb->update($lead_detail_long_table, array("value" => $value), array("lead_detail_id" => $lead_detail_id), array("%s"), array("%d"));
                }
                //insert long field (value has been increased)
                else if(strlen($value) > GFORMS_MAX_FIELD_LENGTH){
                    $wpdb->insert($lead_detail_long_table, array("lead_detail_id" => $lead_detail_id, "value" => $value), array("%d", "%s"));
                }

            }
            else{
                $wpdb->insert($lead_detail_table, array("lead_id" => $lead["id"], "form_id" => $form["id"], "field_number" => $input_id, "value" => $truncated_value), array("%d", "%d", "%f", "%s"));

                if(strlen($value) > GFORMS_MAX_FIELD_LENGTH){

                    //read newly created lead detal id
                    $lead_detail_id = $wpdb->get_var("SELECT LAST_INSERT_ID()");

                    //insert long value
                    $wpdb->insert($lead_detail_long_table, array("lead_detail_id" => $lead_detail_id, "value" => $value), array("%d", "%s"));
                }
            }
        }
        else{
            //Deleting details for this field
            $sql = $wpdb->prepare("DELETE FROM $lead_detail_table WHERE lead_id=%d AND field_number BETWEEN %f AND %f ", $lead["id"], $input_id - 0.001, $input_id + 0.001);
            $wpdb->query($sql);

            //Deleting long field if there is one
            $sql = $wpdb->prepare("DELETE FROM $lead_detail_long_table
                                    WHERE lead_detail_id IN(
                                        SELECT id FROM $lead_detail_table WHERE lead_id=%d AND field_number BETWEEN %f AND %f
                                    )",
                                    $lead["id"], $input_id - 0,001, $input_id + 0.001);
            $wpdb->query($sql);
        }
    }

    public static function upload_file($form_id, $file){

        $target = self::get_file_upload_path($form_id, $file["name"]);
        if(!$target)
            return "FAILED (Upload folder could not be created.)";

        if(move_uploaded_file($file['tmp_name'], $target["path"])){
            return $target["url"];
        }
        else{
            return "FAILED (Temporary file could not be copied.)";
        }
    }


    public static function get_upload_root(){
        $dir = wp_upload_dir();

        if($dir["error"])
            return null;

        return $dir["basedir"] . "/gravity_forms/";
    }

    public static function get_upload_path($form_id){
        return self::get_upload_root() . $form_id;
    }

    public static function get_upload_url($form_id){
        $dir = wp_upload_dir();
        return $dir["baseurl"] . "/gravity_forms/$form_id";
    }

    public static function get_file_upload_path($form_id, $file_name)
    {
        // Where the file is going to be placed
        // Generate the yearly and monthly dirs
        $time = current_time( 'mysql' );
        $y = substr( $time, 0, 4 );
        $m = substr( $time, 5, 2 );
        $target_root = self::get_upload_path($form_id) . "/$y/$m/";
        $target_root_url = self::get_upload_url($form_id) . "/$y/$m/";

        if(!wp_mkdir_p($target_root))
            return false;

        //Add the original filename to our target path.
        //Result is "uploads/filename.extension"
        $file_info = pathinfo($file_name);
        $extension = $file_info["extension"];
        $file_name = basename($file_info["basename"], "." . $extension);

        $counter = 1;
        $target_path = $target_root . $file_name . "." . $extension;
        while(file_exists($target_path)){
            $target_path = $target_root . $file_name . "$counter" . "." . $extension;
            $counter++;
        }

        //creating url
        $target_url = str_replace($target_root, $target_root_url, $target_path);

        return array("path" => $target_path, "url" => $target_url);
    }


    public static function drop_tables(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_lead_details_long_table_name());
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_lead_notes_table_name());
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_lead_details_table_name());
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_lead_table_name());
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_form_view_table_name());
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_meta_table_name());
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_form_table_name());
    }

    public static function insert_form_view($form_id, $ip){
        global $wpdb;
        $table_name = self::get_form_view_table_name();

        $sql = $wpdb->prepare(" SELECT id FROM $table_name
                                WHERE form_id=%d
                                AND year(date_created) = year(utc_timestamp())
                                AND month(date_created) = month(utc_timestamp())
                                AND day(date_created) = day(utc_timestamp())
                                AND hour(date_created) = hour(utc_timestamp())", $form_id);

        $id = $wpdb->get_var($sql, 0, 0);

        if(empty($id))
            $wpdb->query($wpdb->prepare("INSERT INTO $table_name(form_id, date_created, ip) values(%d, utc_timestamp(), %s)", $form_id, $ip));
        else
            $wpdb->query($wpdb->prepare("UPDATE $table_name SET count = count+1 WHERE id=%d", $id));
    }

    public static function is_duplicate($form_id, $field, $value){
        global $wpdb;
        $lead_detail_table_name = self::get_lead_details_table_name();
        $lead_table_name = self::get_lead_table_name();

        switch($field["type"]){
            case "time" :
                $value = sprintf("%d:%02d %s", $value[0], $value[1], $value[2]);
            break;
         }


        $inner_sql_template = " SELECT %s as input, ld.lead_id
                                FROM $lead_detail_table_name ld
                                INNER JOIN $lead_table_name l ON l.id = ld.lead_id
                                WHERE l.form_id=%d AND ld.form_id=%d
                                AND ld.field_number between %s AND %s
                                AND ld.value=%s";

        $sql = "SELECT count(distinct input) as match_count FROM ( ";

        $input_count = 1;
        if(is_array($field["inputs"])){
            $input_count = sizeof($field["inputs"]);
            foreach($field["inputs"] as $input){
                $union = empty($inner_sql) ? "" : " UNION ALL ";
                $inner_sql .= $union . $wpdb->prepare($inner_sql_template, $input["id"], $form_id, $form_id, $input["id"] - 0.001, $input["id"] + 0.001, $value[$input["id"]]);
            }
        }
        else{
            $inner_sql = $wpdb->prepare($inner_sql_template, $field["id"], $form_id, $form_id, $field["id"] - 0.001, $field["id"] + 0.001, $value);
        }

        $sql .= $inner_sql . "
                ) as count
                GROUP BY lead_id
                ORDER BY match_count DESC";

        $count = $wpdb->get_var($sql);
        return $count != null && $count >= $input_count;
    }

    public static function get_lead($lead_id){
        global $wpdb;
        $lead_detail_table_name = self::get_lead_details_table_name();
        $lead_table_name = self::get_lead_table_name();

        $results = $wpdb->get_results($wpdb->prepare("  SELECT l.*, field_number, value
                                                        FROM $lead_table_name l
                                                        INNER JOIN $lead_detail_table_name ld ON l.id = ld.lead_id
                                                        WHERE l.id=%d", $lead_id));

        $leads = self::build_lead_array($results);
        return sizeof($leads) == 1 ? $leads[0] : false;
    }

    public static function get_lead_notes($lead_id){
        global $wpdb;
        $notes_table = self::get_lead_notes_table_name();

        return $wpdb->get_results($wpdb->prepare("  SELECT n.id, n.user_id, n.date_created, n.value, ifnull(u.display_name,n.user_name) as user_name, u.user_email
                                                    FROM $notes_table n
                                                    LEFT OUTER JOIN $wpdb->users u ON n.user_id = u.id
                                                    WHERE lead_id=%d", $lead_id));
    }

    public static function get_lead_field_value($lead, $field){
        if(empty($lead))
            return;

        $max_length = GFORMS_MAX_FIELD_LENGTH;

        if(is_array($field["inputs"])){
            //making sure values submitted are sent in the value even if
            //there isn't an input associated with it
            $lead_field_keys = array_keys($lead);
            foreach($lead_field_keys as $input_id){
                if(is_numeric($input_id) && absint($input_id) == absint($field["id"])){
                    $val = $lead[$input_id];
                    $value[$input_id] = strlen($val) >= $max_length ? self::get_field_value_long($lead["id"], $input_id) : $val;
                }
            }
        }
        else{
            $val = $lead[$field["id"]];

            //To save a database call to get long text, only getting long text if regular field is "somewhat" large (i.e. max - 50)
            if(strlen($val) >= ($max_length - 50))
                $long_text = self::get_field_value_long($lead["id"], $field["id"]);

            $value = !empty($long_text) ? $long_text : $val;
        }

        return $value;
    }


    public static function get_field_value_long($lead_id, $field_number){
        global $wpdb;
        $detail_table_name = self::get_lead_details_table_name();
        $long_table_name = self::get_lead_details_long_table_name();

        $sql = $wpdb->prepare(" SELECT l.value FROM $detail_table_name d
                                INNER JOIN $long_table_name l ON l.lead_detail_id = d.id
                                WHERE lead_id=%d AND field_number BETWEEN %f AND %f", $lead_id, $field_number - 0.001, $field_number + 0.001);
         return $wpdb->get_var($sql);
    }


public static function get_leads($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=30, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null){
        global $wpdb;

        if($sort_field_number == 0)
            $sort_field_number = "date_created";

        if(is_numeric($sort_field_number))
            //TODO: implement date search filter
            $sql = self::sort_by_custom_field_query($form_id, $sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort);
        else
            $sql = self::sort_by_default_field_query($form_id, $sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $start_date, $end_date);

        //initializing rownum
        $wpdb->query("select @rownum:=0");

        //getting results
        $results = $wpdb->get_results($sql);

        return self::build_lead_array($results);
    }

    private static function sort_by_custom_field_query($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=30, $star=null, $read=null, $is_numeric_sort = false){
        global $wpdb;
        if(!is_numeric($form_id) || !is_numeric($sort_field_number)|| !is_numeric($offset)|| !is_numeric($page_size))
            return "";

        $lead_detail_table_name = self::get_lead_details_table_name();
        $lead_table_name = self::get_lead_table_name();

        $orderby = $is_numeric_sort ? "ORDER BY query, (value+0) $sort_direction" : "ORDER BY query, value $sort_direction";

        //$search = empty($search) ? "" : "WHERE d.value LIKE '%$search%' ";
        $search_term = "%$search%";
        $search_filter = empty($search) ? "" : $wpdb->prepare("WHERE d.value LIKE %s", $search_term);

        $where = empty($search) ? "WHERE" : "AND";
        $search_filter .= $star !== null ? $wpdb->prepare("$where is_starred=%d ", $star) : "";

        $where = empty($search) ? "WHERE" : "AND";
        $search_filter .= $read !== null ? $wpdb->prepare("$where is_read=%d ", $read) : "";

        $field_number_min = $sort_field_number - 0.001;
        $field_number_max = $sort_field_number + 0.001;

        $sql = "
            SELECT filtered.sort, l.*, d.field_number, d.value
            FROM $lead_table_name l
            INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
            INNER JOIN (
                SELECT distinct sorted.sort, l.id
                FROM $lead_table_name l
                INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
                INNER JOIN (
                    SELECT @rownum:=@rownum+1 as sort, id FROM (
                        SELECT 0 as query, lead_id as id, value
                        FROM $lead_detail_table_name
                        WHERE form_id=$form_id
                        AND field_number between $field_number_min AND $field_number_max

                        UNION ALL

                        SELECT 1 as query, l.id, d.value
                        FROM $lead_table_name l
                        LEFT OUTER JOIN $lead_detail_table_name d ON d.lead_id = l.id AND field_number between $field_number_min AND $field_number_max
                        WHERE l.form_id=$form_id
                        AND d.lead_id IS NULL

                    ) sorted1
                   $orderby
                ) sorted ON d.lead_id = sorted.id
                $search_filter
                LIMIT $offset,$page_size
            ) filtered ON filtered.id = l.id
            ORDER BY filtered.sort";

        return $sql;
    }


    private static function sort_by_default_field_query($form_id, $sort_field, $sort_direction='DESC', $search='', $offset=0, $page_size=30, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null){
        global $wpdb;

        if(!is_numeric($form_id) || !is_numeric($offset)|| !is_numeric($page_size)){
            return "";
        }

        $lead_detail_table_name = self::get_lead_details_table_name();
        $lead_table_name = self::get_lead_table_name();

        //$search = empty($search) ? "" : " AND value LIKE '%$search%'";
        $search_term = "%$search%";
        $search_filter = empty($search) ? "" : $wpdb->prepare(" AND value LIKE %s", $search_term);

        $star_filter = $star !== null ? $wpdb->prepare(" AND is_starred=%d ", $star) : "";
        $read_filter = $read !== null ? $wpdb->prepare(" AND is_read=%d ", $read) :  "";
        $start_date_filter = empty($start_date) ? "" : " AND datediff(date_created, '$start_date') >=0";
        $end_date_filter = empty($end_date) ? "" : " AND datediff(date_created, '$end_date') <=0";

        $sql = "
            SELECT filtered.sort, l.*, d.field_number, d.value
            FROM $lead_table_name l
            INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
            INNER JOIN
            (
                SELECT distinct @rownum:=@rownum+1 as sort, l.id
                FROM $lead_table_name l
                INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
                WHERE l.form_id=$form_id
                $search_filter
                $star_filter
                $read_filter
                $start_date_filter
                $end_date_filter
                ORDER BY $sort_field $sort_direction
                LIMIT $offset,$page_size
            ) filtered ON filtered.id = l.id
            ORDER BY filtered.sort";

        return $sql;
    }

    private static function build_lead_array($results){

        $leads = array();
        $lead = array();
        if(is_array($results) && sizeof($results) > 0){
            $lead = array("id" => $results[0]->id, "date_created" => $results[0]->date_created, "is_starred" => intval($results[0]->is_starred), "is_read" => intval($results[0]->is_read), "ip" => $results[0]->ip, "source_url" => $results[0]->source_url, "post_id" => $results[0]->post_id);
        }

        $prev_lead_id=0;
        foreach($results as $result){
            if($prev_lead_id <> $result->id && $prev_lead_id > 0){
                array_push($leads, $lead);
                $lead = array("id" => $result->id, "date_created" => $result->date_created, "is_starred" => intval($result->is_starred), "is_read" => intval($result->is_read), "ip" => $result->ip, "post_id" => $results[0]->post_id);
            }
            $lead[$result->field_number] = $result->value;
            $prev_lead_id = $result->id;
        }
        //adding last lead.
        if(sizeof($lead) > 0)
            array_push($leads, $lead);

        return $leads;

    }

    public static function save_key($key){
        $current_key = get_option("rg_gforms_key");
        if(empty($key)){
            delete_option("rg_gforms_key");
        }
        else if($current_key != $key){
            update_option("rg_gforms_key", md5($key));
        }
    }

    public static function get_lead_count($form_id, $search, $star=null, $read=null, $start_date=null, $end_date=null){
        global $wpdb;

        if(!is_numeric($form_id))
            return "";

        $detail_table_name = self::get_lead_details_table_name();
        $lead_table_name = self::get_lead_table_name();

        $star_filter = $star !== null ? $wpdb->prepare("AND is_starred=%d ", $star) : "";
        $read_filter = $read !== null ? $wpdb->prepare("AND is_read=%d ", $read) : "";
        $start_date_filter = empty($start_date) ? "" : " AND datediff(date_created, '$start_date') >=0";
        $end_date_filter = empty($end_date) ? "" : " AND datediff(date_created, '$end_date') <=0";

        $search_term = "%$search%";
        $search_filter = empty($search) ? "" : $wpdb->prepare("AND value LIKE %s", $search_term);

        $sql = "SELECT count(distinct l.id)
                FROM $lead_table_name l
                INNER JOIN $detail_table_name ld ON l.id = ld.lead_id
                WHERE l.form_id=$form_id
                AND ld.form_id=$form_id
                $star_filter
                $read_filter
                $start_date_filter
                $end_date_filter
                $search_filter";

        return $wpdb->get_var($sql);
    }

    public static function get_grid_columns($form_id, $input_label_only=false){
        $form = self::get_form_meta($form_id);
        $field_ids = self::get_grid_column_meta($form_id);

        if(!is_array($field_ids)){
            $field_ids = array();
            for($i=0, $count=sizeof($form["fields"]); $i<$count && $i<5; $i++){
                $field = $form["fields"][$i];

                if($field["displayOnly"])
                    continue;

                if(is_array($field["inputs"])){
                    if($field["type"] == "name"){
                        $field_ids[] = $field["id"] . '.3'; //adding first name
                        $field_ids[] = $field["id"] . '.6'; //adding last name
                    }
                    else{
                        $field_ids[] = $field["inputs"][0]["id"]; //getting first input
                    }
                }
                else{
                    $field_ids[] = $field["id"];
                }
            }
        }

        $columns = array();
        foreach($field_ids as $field_id){
            $field = self::get_field($form, $field_id);
            switch($field_id){
                case "ip" :
                    $columns[$field_id] = array("label" => "User IP", "type" => "ip");
                break;
                case "date_created" :
                    $columns[$field_id] = array("label" => "Entry Date", "type" => "date_created");
                break;
                case "source_url" :
                    $columns[$field_id] = array("label" => "Source Url", "type" => "source_url");
                break;
                default :
                    if(!is_array($field["inputs"]) || self::has_input($field, $field_id))
                        $columns[$field_id] = array("label" => RGForms::get_label($field, $field_id, $input_label_only), "type" => $field["type"]);
            }
        }
        return $columns;
    }

    function has_input($field, $input_id){
        if(!is_array($field["inputs"]))
            return false;
        else{
            foreach($field["inputs"] as $input)
            {
                if($input["id"] == $input_id)
                    return true;
            }
            return false;
        }
    }

    function get_current_page_url() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on")
            $pageURL .= "s";
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80")
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        else
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        return $pageURL;
    }

    public static function get_submitted_fields($form_id){
        global $wpdb;
        $lead_detail_table_name = self::get_lead_details_table_name();
        $field_list = "";
        $fields = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT field_number FROM $lead_detail_table_name WHERE form_id=%d", $form_id));
        foreach($fields as $field)
            $field_list .= intval($field->field_number) . ',';

        if(!empty($field_list))
            $field_list = substr($field_list, 0, strlen($field_list) -1);

        return $field_list;
    }

    public static function get_field($form, $field_id){
        if(is_numeric($field_id))
            $field_id = intval($field_id); //removing floating part of field (i.e 1.3 -> 1) to return field by input id

        foreach($form["fields"] as $field){
            if($field["id"] == $field_id)
                return $field;
        }
        return null;
    }

}
?>
