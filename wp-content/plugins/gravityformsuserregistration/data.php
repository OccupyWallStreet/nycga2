<?php
class GFUserData{

    public static function update_table(){
        global $wpdb;
        $table_name = self::get_user_registration_table_name();

        if ( ! empty($wpdb->charset) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if ( ! empty($wpdb->collate) )
            $charset_collate .= " COLLATE $wpdb->collate";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE $table_name (
              id mediumint(8) unsigned not null auto_increment,
              form_id mediumint(8) unsigned not null,
              is_active tinyint(1) not null default 1,
              meta longtext,
              PRIMARY KEY  (id),
              KEY form_id (form_id)
            )$charset_collate;";

        dbDelta($sql);

    }

    public static function get_user_registration_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_userregistration";
    }

    public static function get_feeds(){
        global $wpdb;
        $table_name = self::get_user_registration_table_name();
        $form_table_name = RGFormsModel::get_form_table_name();
        $sql = "SELECT s.id, s.is_active, s.form_id, s.meta, f.title as form_title
                FROM $table_name s
                INNER JOIN $form_table_name f ON s.form_id = f.id";

        $results = $wpdb->get_results($sql, ARRAY_A);

        $count = sizeof($results);
        for($i=0; $i<$count; $i++){
            $results[$i]["meta"] = maybe_unserialize($results[$i]["meta"]);
        }

        return $results;
    }

    public static function delete_feed($id){
        global $wpdb;
        $table_name = self::get_user_registration_table_name();
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id=%s", $id));
    }

    public static function get_feed_by_form($form_id, $only_active = false){
        global $wpdb;
        $table_name = self::get_user_registration_table_name();
        $active_clause = $only_active ? " AND is_active=1" : "";
        $sql = $wpdb->prepare("SELECT id, form_id, is_active, meta FROM $table_name WHERE form_id=%d $active_clause", $form_id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        if(empty($results))
            return array();

        //Deserializing meta
        $count = sizeof($results);
        for($i=0; $i<$count; $i++){
            $results[$i]["meta"] = maybe_unserialize($results[$i]["meta"]);
        }
        return $results;
    }

    public static function get_feed($id){
        global $wpdb;
        $table_name = self::get_user_registration_table_name();
        $sql = $wpdb->prepare("SELECT id, form_id, is_active, meta FROM $table_name WHERE id=%d", $id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        if(empty($results))
            return array();

        $result = $results[0];
        $result["meta"] = maybe_unserialize($result["meta"]);
        return $result;
    }

    public static function update_feed($id, $form_id, $is_active, $setting){
        global $wpdb;
        $table_name = self::get_user_registration_table_name();
        $setting = maybe_serialize($setting);
        if($id == 0){
            //insert
            $wpdb->insert($table_name, array("form_id" => $form_id, "is_active"=> $is_active, "meta" => $setting), array("%d", "%d", "%s"));
            $id = $wpdb->get_var("SELECT LAST_INSERT_ID()");
        }
        else{
            //update
            $wpdb->update($table_name, array("form_id" => $form_id, "is_active"=> $is_active, "meta" => $setting), array("id" => $id), array("%d", "%d", "%s"), array("%d"));
        }

        return $id;
    }

    public static function drop_tables(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_user_registration_table_name());
    }
       
    public static function get_available_forms($active_form = ''){
        
        $forms = RGFormsModel::get_forms();
        $feeds = self::get_feeds();
        $available_forms = array();
        
        foreach($forms as $form) {
            if($form->id == $active_form || !self::does_form_have_feed($form->id, $feeds))
                $available_forms[] = $form;
        }
        
        return $available_forms;
    }
    
    public static function does_form_have_feed($form_id, $feeds) {
        
        foreach($feeds as $feed) {
            if($feed['form_id'] == $form_id) {
                return true;
            }
        }
        
        return false;
    }
    
    public static function get_user_by_entry_id($entry_id) {
        global $wpdb;
        
        if (!$user_id = $wpdb->get_var($wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'entry_id' AND meta_value = %d LIMIT 1", $entry_id )))
            return false;
            
        $user = new WP_User($user_id);
        
        return $user;
        
    }
    
    public static function insert_buddypress_data($bp_rows) {
        global $wpdb, $bp;
        
        $table = $bp->profile->table_name_data;
        
        foreach($bp_rows as $bp_row) {
            $sql = $wpdb->prepare("INSERT INTO {$table} (user_id, field_id, value, last_updated) VALUES (%d, %d, %s, %s)", $bp_row['user_id'], $bp_row['field_id'], $bp_row['value'], $bp_row['last_update'] );
            $result = $wpdb->query( $sql );
        }
        
    }
    
    public static function remove_password($form_id, $entry_id, $field_id){
        global $wpdb;
        
        $table = $wpdb->prefix . 'rg_lead_detail';
        $removed = $wpdb->query("DELETE FROM $table WHERE lead_id = $entry_id AND form_id = $form_id AND CAST(field_number as DECIMAL(4,2)) = $field_id");
        
    }
    
    public static function update_site_meta($site_id, $key, $value){
        global $wpdb;
        
        $wpdb->show_errors();
        
        $meta_id = $wpdb->get_results("SELECT meta_id FROM $wpdb->sitemeta WHERE site_id = '$site_id' AND meta_key = '$key'");
        
        if(!empty($meta_id)) {
            $meta_id = $meta_id[0]->meta_id;
            $result = $wpdb->query("UPDATE $wpdb->sitemeta SET meta_value = '$value' WHERE meta_id = '{$meta_id}'");
        } else {
            $result = $wpdb->query("INSERT INTO $wpdb->sitemeta (site_id, meta_key, meta_value) VALUES ('$site_id', '$key', '$value')");
        }
        
        return $result;
    }
    
    public static function get_site_by_entry_id($entry_id) {
        global $wpdb;
        
        $site_id = $wpdb->get_var("SELECT site_id FROM $wpdb->sitemeta WHERE meta_key = 'entry_id' AND meta_value = '$entry_id'");
        
        return $site_id;
    }
    
}
?>
