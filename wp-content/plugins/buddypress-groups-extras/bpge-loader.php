<?php

class BPGE extends BP_Group_Extension {
    var $bpge = false;
    
    var $slug = 'extras';
    var $page_slug = 'gpages';
    var $name = false;
    var $nav_item_name = false;
    var $gpages_item_name = false;
    
    var $home_name = false;

    var $gpage_id = false;
    
    /* By default - Is it visible to non-members of a group? Options: public/private */
    var $visibility = false;

    var $create_step_position = 5;
    var $nav_gpages_position = 12;
    var $nav_item_position = 13;

    var $enable_create_step = false; // will set to true in future version
    var $enable_nav_item = false;
    var $enable_gpages_item = false;
    var $enable_edit_item = true;

    var $display_hook = 'groups_extras_group_boxes';
    var $template_file = 'groups/single/plugins';
    
    function BPGE(){
        global $bp;

        if (!empty($bp->groups->current_group)){
            // populate extras data in global var
            $bp->groups->current_group->extras = groups_get_groupmeta($bp->groups->current_group->id, 'bpge');
            add_action('bp_groups_adminbar_admin_menu', array($this, 'buddybar_admin_links'));
        }
        
        $this->gpage_id = !empty($bp->groups->current_group->extras['gpage_id']) ? $bp->groups->current_group->extras['gpage_id'] : $this->get_gpage_by('group_id');
        
        // Display or Hide top menu from group non-members
        $this->visibility = $bp->groups->current_group->extras['display_page'] ? $bp->groups->current_group->extras['display_page'] : 'public';
        $this->enable_nav_item = $bp->groups->current_group->extras['display_page'] == 'public' ? true : false;
        
        if ($bp->is_single_item && !empty($bp->groups->current_group) && empty($bp->groups->current_group->extras['display_page_layout'])){
            $bp->groups->current_group->extras['display_page_layout'] = 'profile';
        }
    
        // In Admin
        $this->name = bpge_names('nav');
        // Public page
        $this->nav_item_name = $bp->groups->current_group->extras['display_page_name'];
        // Home page
        if( !empty($bp->groups->current_group->extras['home_name']) ){
            $this->home_name = $bp->groups->current_group->extras['home_name'];
            $bp->bp_options_nav[$bp->groups->current_group->slug]['home']['name'] = $this->home_name;
        }
        
        // gPages Page
        $this->gpages_item_name   = $bp->groups->current_group->extras['gpage_name'];
        $this->enable_gpages_item = $bp->groups->current_group->extras['display_gpages'] == 'public' ? true : false;        
        
        if ( $this->enable_gpages_item ) {
            if ( bp_is_groups_component() && $bp->is_single_item ) {
                $order = groups_get_groupmeta($bp->groups->current_group->id, 'bpge_nav_order');
                if(empty($order[$this->page_slug])){
                    $order[$this->page_slug] = 99;
                }
                bp_core_new_subnav_item( array(
                        'name' => $this->gpages_item_name,
                        'slug' => $this->page_slug,
                        'parent_slug' => $bp->groups->current_group->slug,
                        'parent_url' => bp_get_group_permalink( $bp->groups->current_group ),
                        'position' => $order[$this->page_slug],
                        'item_css_id' => $this->page_slug,
                        'screen_function' => array(&$this, 'gpages'),
                        'user_has_access' => $this->enable_gpages_item
                ) );
                if( bp_is_current_action( $this->page_slug ) ){
                    $this->gpages();
                }
            }
        }

        add_action('groups_custom_group_fields_editable', array($this, 'edit_group_fields'));
        add_action('groups_group_details_edited', array($this, 'edit_group_fields_save'));
    }

    // Public page with already saved content
    function display() {
        global $bp;
        $fields = $this->get_all_items('fields', $bp->groups->current_group->id);
        if (empty($fields))
            return false;
        
        if ( $bp->groups->current_group->extras['display_page_layout'] == 'plain' ){
            echo '<div class="extra-data">';
                foreach($fields as $field){
                    if ( $field->display != 1)
                        continue;
                        
                    echo '<h4 title="' . ( ! empty($field->desc)  ? esc_attr($field->desc) : '')  .'">' . $field->title .'</h4>';
                    $data = groups_get_groupmeta($bp->groups->current_group->id, $field->slug);
                    if ( is_array($data))
                        $data = implode(', ', $data);
                    echo '<p>' . $data . '</p>';
                }
            echo '</div>';
        }elseif ( !$bp->groups->current_group->extras['display_page_layout'] || $bp->groups->current_group->extras['display_page_layout'] == 'profile'  ){
            echo '<table class="profile-fields zebra">';
                foreach($fields as $field){
                    if ( $field->display != 1)
                        continue;
                     
                    echo '<tr><td class="label" title="' . ( ! empty($field->desc)  ? esc_attr($field->desc) : '')  .'">' . $field->title .'</td>';
                    $data = groups_get_groupmeta($bp->groups->current_group->id, $field->slug);
                    if ( is_array($data))
                        $data = implode(', ', $data);
                    echo '<td class="data">' . $data . '</td></tr>';
                }
            echo '</table>';
        }
    }

    /************************************************************************/

    // Publis gPages screen function
    function gpages(){
        add_action( 'bp_before_group_body', array( &$this, 'gpages_screen_nav' ) );
        add_action( 'bp_template_content', array( &$this, 'gpages_screen_content' ) );
        do_action('bpge_gpages', $this);
        bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'groups/single/plugins' ) );
    }
    
    function gpages_screen_nav(){
        global $bp;
        $pages = $this->get_all_gpages('publish');
        if (empty($bp->action_variables)){
            global $wpdb;
            $sql = "SELECT `post_name` FROM {$wpdb->prefix}posts 
                        WHERE `post_parent` = {$bp->groups->current_group->extras['gpage_id']} AND `post_type` = '{$this->page_slug}'
                        ORDER BY `menu_order` ASC
                        LIMIT 1";
            $bp->action_variables[0] = $wpdb->get_var($wpdb->prepare($sql));
        }
        
        if ( count($pages) > 1 ) {
            echo '<div role="navigation" id="subnav" class="item-list-tabs no-ajax">
                <ul>';
                        foreach($pages as $page){
                            echo '<li '. (($bp->action_variables[0] == $page->post_name) ? 'class="current"' : '') .'>
                                <a href="'.bp_get_group_permalink( $bp->groups->current_group ) . $this->page_slug .'/'. $page->post_name.'">'.$page->post_title.'</a>
                            </li>';
                        }
                    do_action('bpge_gpages_nav_in', $this, $pages);
                echo '</ul>
            </div>';
        }
        do_action('bpge_gpages_nav_after', $this, $pages);
    }
    
    function gpages_screen_content(){
        global $bp, $wpdb;
        
        $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE `post_name` = '{$bp->action_variables[0]}' and `post_type` = '{$this->page_slug}'";
        $page = $wpdb->get_row($wpdb->prepare($sql));
        do_action('bpge_gpages_content_display_before', $this, $page);
        
        setup_postdata($page);
        echo '<div class="gpage">';
            apply_filters('bpge_gpages_content', the_content());
        
            if (bp_group_is_admin()){
                echo '<div class="edit_link"><a target="_blank" href="'.bp_get_group_permalink( $bp->groups->current_group ).'admin/extras/pages-manage/?edit='.$page->ID.'">[Edit this page]</a></div>';
            }
        echo '</div>';
        
        do_action('bpge_gpages_content_display_after', $this, $page);
    }
    
    /************************************************************************/
    
    // Display exra fields on edit group details page
    function edit_group_fields(){
        global $bp;
        $fields = $this->get_all_items('fields', $bp->groups->current_group->id);
        if (empty($fields))
            return false;
        
        foreach( $fields as $field ){
            $field->value = groups_get_groupmeta($bp->groups->current_group->id, $field->slug);
            $req = false;
            if ( $field->required == 1 ) $req = '* ';
            echo '<label for="' . $field->slug . '">' . $req . $field->title . '</label>';
            switch($field->type){
                case 'text':
                    echo '<input id="' . $field->slug . '" name="bpge-' . $field->slug . '" type="text" value="' . $field->value . '" />';
                    break;
                case 'textarea':
                    echo '<textarea id="' . $field->slug . '" name="bpge-' . $field->slug . '">' . $field->value . '</textarea>';
                    break;
                case 'select':
                    echo '<select id="' . $field->slug . '" name="bpge-' . $field->slug . '">';
                        echo '<option ' . ($field->value == $option ? 'selected="selected"' : '') .' value="">-------</option>';
                        foreach($field->options as $option){
                            echo '<option ' . ($field->value == $option ? 'selected="selected"' : '') .' value="' . $option . '">' . $option . '</option>';
                        }
                    echo '</select>';
                    break;
                case 'checkbox':
                    foreach($field->options as $option){
                        echo '<input ' . ( in_array($option, (array)$field->value) ? 'checked="checked"' : '') .' type="' . $field->type . '" name="bpge-' . $field->slug . '[]" value="' . $option . '"> ' . $option . '<br />';
                    }
                    break;
                case 'radio':
                    echo '<span id="bpge-' . $field->slug . '">';
                        foreach($field->options as $option){
                            echo '<input ' . ($field->value == $option ? 'checked="checked"' : '') .' type="' . $field->type . '" name="bpge-' . $field->slug . '" value="' . $option . '"> ' . $option . '<br />';
                        }
                    echo '</span>';
                    if ($req) 
                        echo '<a class="clear-value" href="javascript:clear( \'bpge-' . $field->slug . '\' );">'. __( 'Clear', 'bpge' ) .'</a>';
                    break;
                case 'datebox':
                    echo '<input id="' . $field->slug . '" class="datebox" name="bpge-' . $field->slug . '" type="text" value="' . $field->value . '" />';
                    break;                  
            }
            if ( ! empty($field->desc) ) echo '<p class="description">' . $field->desc . '</p>';
            $req = false;
        }
        do_action('bpge_group_fields_edit', $this, $fields);
    }
    
    // Save extra fields in groupmeta
    function edit_group_fields_save($group_id){
        global $bp;
        
        if ( $bp->current_component == bp_get_groups_root_slug() && 'edit-details' == $bp->action_variables[0] ) {
            if ( $bp->is_item_admin || $bp->is_item_mod  ) {
                // If the edit form has been submitted, save the edited details
                if ( isset( $_POST['save'] ) ) {
                    /* Check the nonce first. */
                    if ( !check_admin_referer( 'groups_edit_group_details' ) )
                        return false;
                    foreach($_POST as $data => $value){
                        if ( substr($data, 0, 5) === 'bpge-' )
                            $to_save[$data] =  $value;
                    }

                    foreach($to_save as $key => $value){
                        $key = substr($key, 5);
                        if ( ! is_array($value) ) {
                            $value = wp_kses_data($value);
                            $value = force_balance_tags($value);
                        }
                        groups_update_groupmeta($group_id, $key, $value);
                    }
                    do_action('bpge_group_fields_save', $this, $to_save);
                }
            }
        }
    }
    
    function widget_display() {
        do_action('bpge_widget_display',$this);
        //echo 'BP_Group_Extension::widget_display()';
    }

    /************************************************************************/

    // Admin area - Main
    function edit_screen() {
        global $bp;
        //print_var($bp->groups->current_group);

        if ( 'admin' == $bp->current_action && $bp->action_variables[1] == 'fields' ) {
            $this->edit_screen_fields($bp);
        }elseif ( 'admin' == $bp->current_action && $bp->action_variables[1] == 'pages' ) {
            $this->edit_screen_pages($bp);
        }elseif ( 'admin' == $bp->current_action && $bp->action_variables[1] == 'fields-manage' ) {
            $this->edit_screen_fields_manage($bp);
        }elseif ( 'admin' == $bp->current_action && $bp->action_variables[1] == 'pages-manage' ) {
            $this->edit_screen_pages_manage($bp);
        }else{
            $this->edit_screen_general($bp);
        }

    }
    
    // Admin area - General Settings
    function edit_screen_general($bp){
        $public_checked = $bp->groups->current_group->extras['display_page'] == 'public' ? 'checked="checked"' : '';
        $private_checked = $bp->groups->current_group->extras['display_page'] == 'private' ? 'checked="checked"' : '';
        $public_gpages_checked = $bp->groups->current_group->extras['display_gpages'] == 'public' ? 'checked="checked"' : '';
        $private_gpages_checked = $bp->groups->current_group->extras['display_gpages'] == 'private' ? 'checked="checked"' : '';
        $layout_plain = $bp->groups->current_group->extras['display_page_layout'] == 'plain' ? 'checked="checked"' : '';
        $private_profile = $bp->groups->current_group->extras['display_page_layout'] == 'profile' ? 'checked="checked"' : '';
        
        $this->edit_screen_head('general');
        
        echo '<p>';
            echo '<label for="group_extras_display_name">'.__('Please specify the page name, where all fields will be displayed','bpge').'</label>';
            echo '<input type="text" value="'.$this->nav_item_name.'" name="group-extras-display-name">';
        echo '</p>';
        
        echo '<p>';
            echo '<label for="group_extras_display">'.sprintf(__('Do you want to make <strong>"%s"</strong> page public? Everyone will see this page.','bpge'), $this->nav_item_name).'</label>';
            echo '<input type="radio" value="public" '.$public_checked.' name="group-extras-display"> '.__('Show it', 'bpge').'<br />';
            echo '<input type="radio" value="private" '.$private_checked.' name="group-extras-display"> '. __('Hide it', 'bpge');
        echo '</p>';
        
        echo '<p>';
            echo '<label for="group_extras_display_layout">'.sprintf(__('Please choose the layout for <strong>"%s"</strong> page','bpge'), $this->nav_item_name).'</label>';
            echo '<input type="radio" value="plain" '.$layout_plain.' name="group-extras-display-layout"> '.__('Plain (field title and its data below)', 'bpge').'<br />';
            echo '<input type="radio" value="profile" '.$private_profile.' name="group-extras-display-layout"> '. __('Profile style (in a table)', 'bpge');
        echo '</p>';
        
        echo '<hr />';
        
        echo '<p>';
            echo '<label for="group_extras_display_name">'.__('Please specify the page name, where all custom pages will be displayed','bpge').'</label>';
            echo '<input type="text" value="'.$this->gpages_item_name.'" name="group-gpages-display-name">';
        echo '</p>';
        
        echo '<p>';
            echo '<label for="group_extras_display">'.sprintf(__('Do you want to make <strong>"%s"</strong> page public (extra group pages will be displayed there)?','bpge'), $this->gpages_item_name).'</label>';
            echo '<input type="radio" value="public" '.$public_gpages_checked.' name="group-gpages-display"> '.__('Show it', 'bpge').'<br />';
            echo '<input type="radio" value="private" '.$private_gpages_checked.' name="group-gpages-display"> '. __('Hide it', 'bpge');
        echo '</p>';
        
        echo '<hr />';
                
        echo '<label>'.__('You can reorder here all navigation links in this group. The first item will become a landing page for this group. Save changes after reordering.<br />Please do NOT make Admin pages on first place - that will cause display problems.', 'bpge') .'</label>';
        $group_nav = $bp->bp_options_nav[$bp->groups->current_group->slug];
        echo '<ul id="nav-sortable">';
            foreach($group_nav as $nav){
                if($nav['slug'] == 'home'){
                    $home_name = $nav['name'];
                }
                if(empty($nav['position'])){
                    $nav['position'] = 99;
                }
                echo '<li id="position_'.$nav['position'].'" class="default">
                        <strong>' . $nav['name'] .'</strong>
                    </li>';
            }
            echo '<input type="hidden" name="bpge_group_nav_position" value="" />';
        echo '</ul>';
        
        echo '<p>';
            echo '<label for="group_extras_home_name">'.__('Rename the Home group page - Activity (for example) is far better.','bpge').'</label>';
            echo '<input type="text" value="'.($this->home_name?$this->home_name:$home_name).'" name="group-extras-home-name">';
        echo '</p>';
        
        echo '<div class="clear">&nbsp;</div>';
        
        echo '<p><input type="submit" name="save_general" id="save" value="'.__('Save Changes &rarr;','bpge').'"></p>';
        wp_nonce_field('groups_edit_group_extras');
    }
    
    // Admin area - All Fields
    function edit_screen_fields($bp){
        $this->edit_screen_head('fields');

        $fields = $this->get_all_items('fields', $bp->groups->current_group->id);

        if(empty($fields)){
            $this->notices('no_fields');
            return false;
        }

        echo '<ul id="fields-sortable">';
            foreach($fields as $field){
                echo '<li id="position_'.str_replace('_', '', $field->slug).'" class="default">
                                <strong title="' . $field->desc . '">' . $field->title .'</strong> &rarr; ' . $field->type . ' &rarr; ' . (($field->display == 1)?__('displayed','bpge'):__('<u>not</u> displayed','bpge')) . '
                                <span class="items-link">
                                    <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug . '/fields-manage/?edit=' . $field->slug . '" class="button" title="'.__('Change its title, description etc','bpge').'">'.__('Edit field', 'bpge').'</a>&nbsp;
                                    <a href="#" class="button delete_field" title="'.__('Delete this item and all its content', 'bpge').'">'.__('Delete', 'bpge').'</a>
                                </span>
                            </li>';
            }
        echo '</ul>';
    }
    
    function get_all_gpages($post_status = 'any'){
        global $bp;
        $args = array( 
                            'post_parent' => $bp->groups->current_group->extras['gpage_id'], 
                            'post_type' => $this->page_slug,
                            'orderby' => 'menu_order',
                            'order' => 'ASC',
                            'post_status' => $post_status
                        );
        return get_posts( $args );
    }
    
    // Admin area - All Pages
    function edit_screen_pages($bp){
        $this->edit_screen_head('pages');

        $pages = $this->get_all_gpages();
        
        if(empty($pages)){
            $this->notices('no_pages');
            return false;
        }
        
        echo '<ul id="pages-sortable">';
            foreach($pages as $page){
                echo '<li id="position_'.$page->ID.'" class="default">
                        <strong>' . $page->post_title .'</strong> &rarr; ' . (($page->post_status == 'publish')?__('displayed','bpge'):__('<u>not</u> displayed','bpge')) . '
                        <span class="items-link">
                            <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . $this->page_slug . '/' . $page->post_name . '" class="button" target="_blank" title="'.__('View this page live','bpge').'">'.__('View', 'bpge').'</a>&nbsp;
                            <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug . '/pages-manage/?edit=' . $page->ID . '" class="button" title="'.__('Change its title, content etc','bpge').'">'.__('Edit', 'bpge').'</a>&nbsp;
                            <a href="#" class="button delete_page" title="'.__('Delete this item and all its content', 'bpge').'">'.__('Delete', 'bpge').'</a>
                        </span>
                    </li>';
            }
        echo '</ul>';
        
    }
    
    // Add / Edit fields form
    function edit_screen_fields_manage($bp){

        if (isset($_GET['edit']) && !empty($_GET['edit'])){
            $field = $this->get_item_by_slug('field', $_GET['edit']);
        }
        
        $this->edit_screen_head('fields-manage');
        
        echo '<p>';
            echo '<label>' . __('Field Title', 'bpge') . '</label>';
            echo '<input type="text" value="'.$field->title.'" name="extra-field-title">';
            
            if (empty($field)){
                echo '<label>' . __('Field Type', 'bpge') . '</label>';
                echo '<select name="extra-field-type" id="extra-field-type">';
                    echo '<option value="text">' . __('Text Box', 'bpge') . '</option>';
                    echo '<option value="textarea">' . __('Multi-line Text Box', 'bpge') . '</option>';
                    echo '<option value="checkbox">' . __('Checkboxes', 'bpge') . '</option>';
                    echo '<option value="radio">' . __('Radio Buttons', 'bpge') . '</option>';
                    //echo '<option value="datebox">' . __('Date Selector', 'bpge') . '</option>';
                    echo '<option value="select">' . __('Drop Down Select Box', 'bpge') . '</option>';
                echo '</select>';
                
                echo '<div id="extra-field-vars">';
                    echo '<div class="content"></div>';
                    echo '<div class="links">
                                    <a class="button" href="#" id="add_new">' . __('Add New', 'bpge') . '</a>
                            </div>';
                echo '</div>';
            }
            echo '<label>' . __('Field Description', 'bpge') . '</label>';
                echo '<textarea name="extra-field-desc">'.$field->title.'</textarea>';
            
            echo '<label for="extra-field-required">' . __('Is this field required (will be marked as required on group Edit Details page)?','bpge') . '</label>';
                $req = '';
                $not_req = 'checked="checked"';
                if ( $field->required == 1 ) {
                    $req = 'checked="checked"';
                    $not_req = '';
                }
                echo '<input type="radio" value="1" '.$req.' name="extra-field-required"> '.__('Required', 'bpge').'<br />';
                echo '<input type="radio" value="0" '.$not_req.' name="extra-field-required"> '. __('Not Required', 'bpge');
                
            echo '<label for="extra-field-display">' . sprintf(__('Should this field be displayed for public on "<u>%s</u>" page?','bpge'), $this->nav_item_name) . '</label>';
                $disp = 'checked="checked"';
                $not_disp = '';
                if ( $field->display != 1 ) {
                    $not_disp = 'checked="checked"';
                    $disp = '';
                }
                echo '<input type="radio" value="1" '.$disp.' name="extra-field-display"> '.__('Display it', 'bpge').'<br />';
                echo '<input type="radio" value="0" '.$not_disp.' name="extra-field-display"> '. __('Do NOT display it', 'bpge');
        echo '</p>';
        
        do_action('bpge_field_manage', $this, $field);
        
        if (empty($field)){
            echo '<p><input type="submit" name="save_fields_add" id="save" value="'.__('Create New &rarr;','bpge').'"></p>';
        }else{
            echo '<input type="hidden" name="extra-field-slug" value="' . $field->slug . '">';
            echo '<p><input type="submit" name="save_fields_edit" id="save" value="'.__('Save Changes &rarr;','bpge').'"></p>';
        }
        wp_nonce_field('groups_edit_group_extras');
    }
    
    // Add / Edit pages form
    function edit_screen_pages_manage($bp){
        if (isset($_GET['edit']) && !empty($_GET['edit']) && is_numeric($_GET['edit'])){
            $page = $this->get_gpage_by('id', $_GET['edit']);
        }
        
        $this->edit_screen_head('pages-manage');
        
        echo '<p>';
            echo '<label>' . __('Page Title', 'bpge') . '</label>';
            echo '<input type="text" value="'.$page->post_title.'" name="extra-page-title">';
        echo '</p>';
        
        echo '<p>';
            echo '<label>' . __('Page Content', 'bpge') . '</label>';
            echo '<textarea name="extra-page-content" id="post_content">'.$page->post_content.'</textarea>';
        echo '</p>';

        echo '<p>';
            echo '<label for="extra-page-display">' . __('Should this page be displayed for public in group navigation?','bpge') . '</label>';
                $checked = 'checked="checked"';
                echo '<input type="radio" value="publish" '.($page->post_status == 'publish'?$checked:'').' name="extra-page-status"> '.__('Display it', 'bpge').'<br />';
                echo '<input type="radio" value="draft" '.($page->post_status == 'publish'?'':$checked).' name="extra-page-status"> '. __('Do NOT display it', 'bpge');
        echo '</p>';
        
        do_action('bpge_page_manage', $this, $page);
        
        if (empty($page)){
            echo '<p><input type="submit" name="save_pages_add" id="save" value="'.__('Create New &rarr;','bpge').'"></p>';
        }else{
            echo '<input type="hidden" name="extra-page-id" value="' . $page->ID . '">';
            echo '<p><input type="submit" name="save_pages_edit" id="save" value="'.__('Save Changes &rarr;','bpge').'"></p>';
        }
        wp_nonce_field('groups_edit_group_extras');
    }
    
    // Save all changes into DB
    function edit_screen_save() {
        global $bp;
        if ( $bp->current_component == bp_get_groups_root_slug() && 'extras' == $bp->action_variables[0] ) {
            if ( !$bp->is_item_admin )
                return false;
            // Save general settings
            if ( isset($_POST['save_general'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;
                
                $meta = $bp->groups->current_group->extras;
                
                $meta['display_page'] = $_POST['group-extras-display'];
                $meta['display_page_name'] = stripslashes(strip_tags($_POST['group-extras-display-name']));
                $meta['display_page_layout'] = $_POST['group-extras-display-layout'];
                
                $meta['gpage_name'] = stripslashes(strip_tags($_POST['group-gpages-display-name']));
                $meta['display_gpages'] = $_POST['group-gpages-display'];
                
                $meta['home_name'] = stripslashes(strip_tags($_POST['group-extras-home-name']));
                
                // now save nav order
                if(!empty($_POST['bpge_group_nav_position'])){
                    // preparing vars
                    parse_str($_POST['bpge_group_nav_position'], $tab_order );
                    $nav_old = $bp->bp_options_nav[$bp->groups->current_group->slug];
                    $order = array();
                    $pos = 1;
                    
                    // update menu_order for each nav item
                    foreach($tab_order['position'] as $index => $old_position){
                        foreach($nav_old as $nav){
                            if ($nav['position'] == $old_position){
                                $order[$nav['slug']] = $pos;
                            }
                            $pos++;
                        }
                    }

                    // save to DB
                    groups_update_groupmeta($bp->groups->current_group->id, 'bpge_nav_order', $order);
                }
                
                do_action('bpge_save_general', $this, $meta);
                
                // Save into groupmeta table some general settings
                groups_update_groupmeta( $bp->groups->current_group->id, 'bpge', $meta );
                
                $this->notices('settings_updated');
                
                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/' );
            }
            
            // Save new field
            if ( isset($_POST['save_fields_add'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;

                // get current fields if any
                $fields = $this->get_all_items('fields', $bp->groups->current_group->id);
                if (!$fields)   
                    $fields = array();
                
                $new = new Stdclass;
                $new->title = apply_filters('bpge_new_field_title', htmlspecialchars(strip_tags($_POST['extra-field-title'])));
                $new->slug = apply_filters('bpge_new_field_slug', str_replace('-', '_', sanitize_title($new->title))); // will be used as unique identifier
                $new->desc = apply_filters('bpge_new_field_desc', htmlspecialchars(strip_tags($_POST['extra-field-desc'])));
                $new->type = apply_filters('bpge_new_field_type', $_POST['extra-field-type']);
                $new->required = apply_filters('bpge_new_field_required', $_POST['extra-field-required']);
                $new->display = apply_filters('bpge_new_field_display', $_POST['extra-field-display']);
                if(!empty($_POST['options'])){
                    foreach($_POST['options'] as $option){
                        $new->options[] = htmlspecialchars(strip_tags($option));
                    }
                }
                
                do_action('bpge_save_new_field', $this, $new);
                
                // To the end of an array of current fields
                array_push($fields, $new);

                // Save into groupmeta table
                $fields = json_encode($fields);
                groups_update_groupmeta( $bp->groups->current_group->id, 'bpge_fields', $fields );

                $this->notices('added_field');
                
                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/' );
            }
            
            // Save new page
            if ( isset($_POST['save_pages_add'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;
                    
                global $current_blog;
                $thisblog = $current_blog->blog_id;
                $admin = get_user_by( 'email', get_blog_option($thisblog, 'admin_email'));

                // Save as a post_type
                $page = array(
                    'comment_status' => 'open',
                    'ping_status' => 'open',
                    'post_author' => $admin->ID,
                    'post_title' => apply_filters('bpge_new_page_title', $_POST['extra-page-title']),
                    'post_content' => apply_filters('bpge_new_page_content', $_POST['extra-page-content']),
                    'post_parent' => apply_filters('bpge_new_page_parent', $bp->groups->current_group->extras['gpage_id']),
                    'post_status' => apply_filters('bpge_new_page_status', $_POST['extra-page-status']),
                    'menu_order' => count($this->get_all_gpages()) + 1,
                    'post_type' => $this->page_slug
                );
                
                do_action('bpge_save_new_page_before', $this, $page);
                $page_id = wp_insert_post($page);
                do_action('bpge_save_new_page_after', $this, $page_id);
                
                $this->notices('added_page');
                
                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/pages/' );
            }
            
            // Edit existing field
            if ( isset($_POST['save_fields_edit'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;
                    
                // get current fields
                $fields = $this->get_all_items('fields', $bp->groups->current_group->id);
                foreach( $fields as $field ){
                    if ( $_POST['extra-field-slug'] == $field->slug ){
                        $field->title = apply_filters('bpge_updated_field_title', htmlspecialchars(strip_tags($_POST['extra-field-title'])));
                        $field->desc = apply_filters('bpge_updated_field_desc', htmlspecialchars(strip_tags($_POST['extra-field-desc'])));
                        $field->required = apply_filters('bpge_updated_field_required', $_POST['extra-field-required']);
                        $field->display = apply_filters('bpge_updated_field_display', $_POST['extra-field-display']);
                    }
                    $updated[] = $field;
                }
                
                do_action('bpge_save_updated_field', $this, $updated);
                
                // Save into groupmeta table
                $updated = json_encode($updated);
                groups_update_groupmeta( $bp->groups->current_group->id, 'bpge_fields', $updated );
                
                $this->notices('edited_field');
                
                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/' );
                
            }
            
            // Edit existing page
            if ( isset($_POST['save_pages_edit'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;
                    
                $page['ID'] = $_POST['extra-page-id'];
                $page['post_title'] = apply_filters('bpge_updated_page_title', $_POST['extra-page-title']);
                $page['post_content'] = apply_filters('bpge_updated_page_content', $_POST['extra-page-content']);
                $page['post_status'] = apply_filters('bpge_updated_page_status', $_POST['extra-page-status']);
                
                do_action('bpge_save_updated_page_before', $this, $page);
                $updated = wp_update_post($page);
                do_action('bpge_save_updated_page_after', $this, $updated);
                
                $this->notices('edited_page');
                
                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/pages/' );
                
            }
        }
    }
    
    // Display Header and Extra-Nav
    function edit_screen_head($cur = 'general'){
        $group_link = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug;
        switch($cur){
            case 'general':
                echo '<span class="extra-title">'.bpge_names('title_general').'</span>';
                echo '<span class="extra-subnav">
                            <a href="'. $group_link .'/" class="button active">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields-manage/'.'" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/'.'" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;
            
            case 'fields':
                echo '<span class="extra-title">'.bpge_names('title_fields').'</span>';
                echo '<span class="extra-subnav">
                            <a href="'. $group_link .'/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields/" class="button active">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields-manage/'.'" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/'.'" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;
            
            case 'fields-manage':
                if ( isset($_GET['edit']) && !empty($_GET['edit']) ){
                    echo '<span class="extra-title">'.bpge_names('title_fields_edit').'</span>';
                    $active = '';
                }else{
                    echo '<span class="extra-title">'.bpge_names('title_fields_add').'</span>';
                    $active = 'active';
                }
                echo '<span class="extra-subnav">
                            <a href="'. $group_link . '/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link . '/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields-manage/" class="button ' . $active . '">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/'.'" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;
                
            case 'pages':
                echo '<span class="extra-title">'.bpge_names('title_pages').'</span>';
                echo '<span class="extra-subnav">
                            <a href="'. $group_link .'/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages/" class="button active">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields-manage/'.'" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/'.'" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;
            
            case 'pages-manage':
                if ( isset($_GET['edit']) && !empty($_GET['edit']) ){
                    echo '<span class="extra-title">'.bpge_names('title_pages_edit').'</span>';
                    $active = '';
                }else{
                    echo '<span class="extra-title">'.bpge_names('title_pages_add').'</span>';
                    $active = 'active';
                }
                echo '<span class="extra-subnav">
                            <a href="'. $group_link . '/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link . '/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields-manage/" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/'.'" class="button ' . $active . '">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;
        }
        
        echo '<div class="clear">&nbsp;</div>';
        
        do_action('bpge_extra_menus', $cur);
    }
    
    /************************************************************************/
    
    // Getting all extra items (fields or pages) for defined group
    function get_all_items($type, $id){
        // get all fields
        $items = array();
        
        if ( $type == 'fields' ){
            $items = groups_get_groupmeta($id, 'bpge_fields');
        }elseif ( $type == 'pages' ){
            $items = groups_get_groupmeta($id, 'bpge_pages');
        }

        if (empty($items)) {
            $items = false;
        }else{
            $items = json_decode($items);
        }
        
        return apply_filters('bpge_get_all_items', $items);
    }
    
    // Get item (field or page) by slug - reusable
    function get_item_by_slug($type, $slug){
        global $bp;
        // just in case...
        if (!is_string($type) || !is_string($slug))
            return false;
            
        $items = array();
        $searched = array();
        
        $type = apply_filters('bpge_items_by_slug_type', $type);
        $slug = apply_filters('bpge_items_by_slug_slug', $slug);
        
        if ( $type == 'field'){
            $items = $this->get_all_items('fields', $bp->groups->current_group->id);
        }elseif ( $type == 'page'){
            $items = $this->get_all_items('pages', $bp->groups->current_group->id);
        }
        
        foreach( $items as $item ){
            if ( $slug == $item->slug )
                $searched = $item;
        }
        
        return apply_filters('bpge_items_by_slug', $searched);
    }
    
    // Notices about user actions
    function notices($type){
        switch($type){
            case 'settings_updated';
                bp_core_add_message(__('Group Extras settings were succefully updated.','bpge'));
                break;
            case 'added_field';
                bp_core_add_message(__('New field was successfully added.','bpge'));
                break;
            case 'edited_field';
                bp_core_add_message(__('The field was successfully updated.','bpge'));
                break;
            case 'added_page';
                bp_core_add_message(__('New page was successfully added.','bpge'));
                break;
            case 'edited_page';
                bp_core_add_message(__('The page was successfully updated.','bpge'));
                break;
            case 'no_fields':
                echo '<div class="" id="message"><p>' . __('Please create at least 1 extra field to show it in a list.', 'bpge') . '</p></div>';
                break;
            case 'no_pages':
                echo '<div class="" id="message"><p>' . __('Please create at least 1 extra page to show it in a list.', 'bpge') . '</p></div>';
                break;
        }
        do_action('bpge_notices', $type);
    }
    
    // create a storage fro groups pages
    function get_gpage_by($what, $input = false){
        global $bp;
        
        switch($what){
            case 'group_id':
                global $current_blog;
                $thisblog = $current_blog->blog_id;
                $admin = get_user_by( 'email', get_blog_option($thisblog, 'admin_email'));
                $old_data = groups_get_groupmeta($bp->groups->current_group->id, 'bpge');
                // create a gpage...
                $old_data['gpage_id'] = wp_insert_post(array(
                                'comment_status' => 'closed',
                                'ping_status' => 'closed',
                                'post_author' => $admin->ID,
                                'post_content' => $bp->groups->current_group->description,
                                'post_name' => $bp->groups->current_group->slug,
                                'post_status' => 'publish',
                                'post_title' => $bp->groups->current_group->name,
                                'post_type' => $this->page_slug
                            ));
                // ...and save it to reuse later
                groups_update_groupmeta($bp->groups->current_group->id, 'bpge', $old_data);
                return $old_data['gpage_id'];
                break;
                
            case 'id':
                return get_post($input);
                break;
        }
        
    }
    
    // Handle all ajax requests
    function ajax(){
        global $bp;
        $method = isset($_POST['method']) ? $_POST['method'] : '';
        
        do_action('bpge_ajax', $this, $method);
        
        switch($method){
            case 'reorder_fields':
                parse_str($_POST['field_order'], $field_order );
                $fields = $this->get_all_items('fields', $bp->groups->current_group->id);

                // reorder all fields accordig to new positions
                foreach($field_order['position'] as $u_slug){
                    foreach($fields as $field){
                        if ( $u_slug == str_replace('_', '', $field->slug) ){
                            $new_order[] = $field;
                            //break;
                        }
                    }
                }

                // Save new order into groupmeta table
                $new_order = json_encode($new_order);
                groups_update_groupmeta( $bp->groups->current_group->id, 'bpge_fields', $new_order );
                die('saved');
                break;
                
            case 'delete_field':
                $fields = $this->get_all_items('fields', $bp->groups->current_group->id);
                $left = array();
                // Delete all corresponding data
                foreach( $fields as $field ) {
                    if ( str_replace('_', '', $field->slug) == $_POST['field'] ){
                        groups_delete_groupmeta($bp->groups->current_group->id, $field->slug);
                        continue;
                    }
                    array_push($left, $field);
                }
                // Save fields that are left
                $left = json_encode($left);
                groups_update_groupmeta($bp->groups->current_group->id, 'bpge_fields', $left);
                die('deleted');
                break;
                
            case 'reorder_pages':
                parse_str($_POST['page_order'], $page_order );
                // update menu_order for each gpage
                foreach($page_order['position'] as $index => $page_id){
                    wp_update_post(array(
                        'ID' => $page_id,
                        'menu_order' => $index
                    ));
                }
                die('saved');
                break;
                
            case 'delete_page':
                if($deleted = wp_delete_post($_POST['page'], true) ){
                    die('deleted');
                }else{
                    die('error');
                }
                break;
                
            default:
                die;
        }   
    }
    
    /************************************************************************/
    
    // Creation step - enter the data
    function create_screen() {
        do_action('bpge_create_screen', $this);
        //echo 'BP_Group_Extension::create_screen()';
    }

    // Creation step - save the data
    function create_screen_save() {
        do_action('bpge_create_save', $this);
        //echo 'BP_Group_Extension::create_screen_save()';
    }

    // Display a link for group/site admins in BuddyBar when on group page
    function buddybar_admin_links(){
        global $bp;
        echo '<li><a href="'. bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/">'. __( 'Manage Extras', 'bpge' ) .'</a>
                <ul>
                    <li><a href="'. bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/">'.__('Settings', 'bpge' ) .'</a></li>
                    <li><a href="'. bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields/">'.__('All Fields', 'bpge' ) .'</a></li>
                    <li><a href="'. bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/pages/">'.__('All Pages', 'bpge' ) .'</a></li>
                    <li><a href="'. bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields-manage/">'.__('Add Field', 'bpge' ) .'</a></li>
                    <li><a href="'. bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/pages-manage/">'.__('Add Page', 'bpge' ) .'</a></li>';
                    do_action('bpge_buddybar_admin_links', $this);
                    echo '
                </ul>
            </li>';
    }
    
    // Load if was not already loaded
    private static $instance = false;
    static function getInstance(){
        if(!self::$instance)
            self::$instance = new BPGE;
        
        return self::$instance;
    }
}

bp_register_group_extension('BPGE');

add_action('wp_ajax_bpge', 'bpge_ajax');
function bpge_ajax(){
    $load = BPGE::getInstance();
    $load->ajax();
}
