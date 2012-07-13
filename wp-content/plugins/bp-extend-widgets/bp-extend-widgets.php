<?php
/*
Plugin Name: BuddyPress Extend Widgets
Plugin URI: http://ovirium.com/
Description: Provide all widgets with BuddyPress specific fields
Version: 1.0
Requires at least: WP 3.2, BuddyPress 1.5
Tested up to: WP 3.3.1, BuddyPress 1.5.4
Author: slaFFik
Author URI: http://ovirium.com/
*/
if(!defined('ABSPATH')) exit;

/*
 * The main loader
 */
add_action('bp_init', 'bpew_load');
function bpew_load(){
    // Load languages if any
    if ( file_exists( dirname(__File__) . '/langs/' . get_locale() . '.mo' ) )
        load_textdomain( 'bpew', dirname(__File__) . '/langs/' . get_locale() . '.mo' );

    // display our own fields
    add_action('in_widget_form', 'bpew_extend_form', 10, 3);
    
    // save our new things
    add_filter('widget_update_callback', 'bpew_extend_update', 10, 4);
    
    // display content if needed
    add_filter('widget_display_callback', 'bpew_extend_display', 10, 3);
}


/*
 * Handlers
 */
function bpew_extend_form($class, $return, $instance){
    echo '<hr /><p>'.__('Display the widget if it satisfies BuddyPress-specific options below:','bpew').'</p>';

    if(!isset($instance['bp_component_type']))
        $instance['bp_component_type'] = '';
    if(!isset($instance['bp_component_ids']))
        $instance['bp_component_ids'] = '';
    
    echo '<p>
            <label id="'.$class->get_field_id('bp_component_type').'">'.__('Please select for what to apply','bpew').':</label><br />
            <input '.checked($instance['bp_component_type'], '', false).' type="radio" name="'.$class->get_field_name('bp_component_type').'" value=""/> '.__('Do not apply', 'bpew').'<br />
            <input '.checked($instance['bp_component_type'], 'members', false).' type="radio" name="'.$class->get_field_name('bp_component_type').'" value="members"/> '.__('Members profiles pages', 'bpew').'<br />
            <input '.checked($instance['bp_component_type'], 'members_dir', false).' type="radio" name="'.$class->get_field_name('bp_component_type').'" value="members_dir"/> '.__('Members Directory page', 'bpew').'<br />
            <input '.checked($instance['bp_component_type'], 'groups', false).' type="radio" name="'.$class->get_field_name('bp_component_type').'" value="groups"/> '.__('Groups internal pages', 'bpew').'<br />
            <input '.checked($instance['bp_component_type'], 'groups_dir', false).' type="radio" name="'.$class->get_field_name('bp_component_type').'" value="groups_dir"/> '.__('Groups Directory page', 'bpew').'
        </p>';
    
    echo '<p>
            <label id="'.$class->get_field_id('bp_component_ids').'">'.__('IDs','bpew').':</label>
            <input id="'.$class->get_field_id('bp_component_ids').'" type="text" name="'.$class->get_field_name('bp_component_ids').'" value="'.$instance['bp_component_ids'].'"/><br />
            <span class="description">'.__('Comma separated, no spaces','bpew').'</span>
        </p>';
    
    add_action('bpew_extend_form', $class, $return, $instance);
    
    return $return;
}

function bpew_extend_update($instance, $new_instance, $old_instance, $this){
    $new_instance = apply_filters('bpew_extend_update', $new_instance, $old_instance, $instance, $this);
        
    return $new_instance;
}

function bpew_extend_display($instance, $this, $args){
    if(empty($instance['bp_component_type']))
        return $instance;
    
    global $bp;
    
    // display on profile pages only
    if($instance['bp_component_type'] == 'members' && bp_displayed_user_id() 
    && in_array(bp_displayed_user_id(), explode(',', $instance['bp_component_ids']))){
        return $instance;
    }
    
    if($instance['bp_component_type'] == 'members_dir' && bp_is_directory() && bp_current_component() == BP_MEMBERS_SLUG){
        return $instance;
    }
    
    // display on groups pages only
    $group_id = $bp->groups->current_group->id;
    if($instance['bp_component_type'] == 'groups' && !empty($group_id)
    && in_array($group_id, explode(',', str_replace(' ', '', trim($instance['bp_component_ids']))))){
        return $instance;
    }
    
    if($instance['bp_component_type'] == 'groups_dir' && bp_is_directory() && bp_current_component() == BP_GROUPS_SLUG){
        return $instance;
    }
    
    return false;
}