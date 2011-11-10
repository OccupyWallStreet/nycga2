<?php

$bpge_admin = new BPGE_ADMIN();

class BPGE_ADMIN{

    function BPGE_ADMIN() {
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
            //$columns[ $this->pagehook ] = 1;
        }
        return $columns;
    }
    
    function on_admin_menu() {
        $this->pagehook = add_submenu_page('bp-general-settings', __('Groups Extras', 'bpge'), __('Groups Extras', 'bpge'), 'manage_options', 'bpge-admin', array( &$this, 'on_show_page') );
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
        add_meta_box('bpge-admin-debug', __('Debug', 'bpge'), array(&$this, 'on_bpge_admin_debug'), $this->pagehook, $position, $priority );
        add_meta_box('bpge-admin-re', __('Rich Editor for Groups Pages', 'bpge'), array(&$this, 'on_bpge_admin_re'), $this->pagehook, $position, $priority );
        // main content - normal
        add_meta_box('bpge-admin-groups', __('Groups Management', 'bpge'), array( &$this, 'on_bpge_admin_groups'), $this->pagehook, 'normal', 'core');
        add_meta_box('bpge-admin-fields', __('Default Fields', 'bpge'), array( &$this, 'on_bpge_admin_fields'), $this->pagehook, 'normal', 'core');
    }

    function on_bpge_admin_re($bpge){
        echo '<p>';
            _e('Would you like to enable Rich Editor for easy use of html tags for groups pages?','bpge');
        echo '</p>';

        echo '<p>';
            echo '<input type="radio" name="bpge_re" '.($bpge['re'] == 1?'checked="checked"':'').' value="1">&nbsp'.__('Enable','bpge').'<br />';
            echo '<input type="radio" name="bpge_re" '.($bpge['re'] != 1?'checked="checked"':'').' value="0">&nbsp'.__('Disable','bpge');
        echo '</p>';
    }
    
    function on_bpge_admin_fields($bpge){
        echo '<p>';
            _e('Please create/edit here fields you want to be available as standard blocks of data.<br />This will be helpful for group admins - no need for them to create lots of fields from scratch.','bpge');
        echo '</p>';
        
        $def_fields = array();
        $def_fields = bp_get_option('bpge_def_fields');
        $def_fields = array(
            0 => array(
                'id' => 1,
                'name' => 'Group 1',
                'desc' => 'Some Description 1 Some Description 1 Some Description 1 Some Description 1 Some Description 1 Some Description 1 Some Description 1 Some Description 1 ',
                'active' => 1,
                'fields' => array()
            ),
            1 => array(
                'id' => 2,
                'name' => 'Group 2',
                'desc' => 'Some Description 2',
                'active' => 0,
                'fields' => array(
                    0 => array(
                        'name' => 'Name 1',
                        'desc' => 'Description',
                        'type' => 'checkbox',
                        'options' => array(
                            0 => array(
                                'slug' => 'option-one',
                                'name' => 'Option One'
                            ),
                            1 => array(
                                'slug' => 'option-two',
                                'name' => 'Option Two'
                            ),
                        )
                    )
                )
            )
        );
        
        echo '<ul class="sets">';
        if (count($def_fields) > 0){
            foreach($def_fields as $group){
                echo '<li id="set_'.$group['id'].'">';
                    echo '<span class="active">';
                        echo '<input type="checkbox" name="bpge_fields_g['.$group['id'].'][active]" '.($group['active'] == 1?'checked="checked"':'').' value="1" /> ';
                    echo '</span>';
                    
                    echo '<span class="name">';
                        echo $group['name'];
                    echo '</span>';
                    echo '<input type="hidden" name="bpge_fields_g['.$group['id'].'][name]" value="'.$group['name'].'" /> ';
                    
                    echo '<span class="desc">';
                        if(!empty($group['desc'])){
                            echo $group['desc'];
                        }
                    echo '</span>';
                    echo '<input type="hidden" name="bpge_fields_g['.$group['id'].'][desc]" value="'.$group['desc'].'" /> ';
                    
                    echo '<span class="actions">';
                        echo '<a href="#" class="button display_fields">'.__('Show Fields', 'bpge').' ('.count($group['fields']).')</a>&nbsp;';
                        echo '<a href="#" class="button field_edit">'.__('Edit','bpde').'</a>&nbsp;<a href="#" class="button field_delete">'.__('Delete','bpde').'</a>';
                    echo '</span>';
                    
                    if(!empty($group['fields'])){
                        echo '<ul class="fields" id="fields_'.$group['id'].'">';
                            foreach($group['fields'] as $field){
                                echo '<li>'.$field['name'].' &rarr; '.$field['desc'].' &rarr; '.$field['type'].'</li>';
                            }
                        echo '</ul>';
                    }
                    
                    echo '<div class="clear"></div>';
                    
                echo '</li>';
            }
        }else{
            echo '<li>';
                echo '<span class="no_fields">'.__('Currently there are no predefined fields. Groups admins should create all fields by themselves.', 'bpge') . '</span>';
                echo '<a href="#" class="button create_fields_set">'.__('Create a set of predefined fields','bpge').'</a>';
            echo '</li>';
        }
        echo '</ul><div class="clear"></div>';
        
        //print_var($def_fields);
    }

    function on_bpge_admin_debug($bpge){
        print_var($bpge);
    }
    
    function on_bpge_admin_groups($bpge){
        global $bp;
        ?>
        <table id="bp-gtm-admin-table" class="widefat link-group">
            <thead>
                <tr class="header">
                    <td colspan="2"><p><?php _e('Which groups do you allow to create custom fields and pages?', 'bpge') ?></p></td>
                </tr>
            </thead>
            <tbody id="the-list">
                <tr>
                    <td class="checkbox"><p><input type="checkbox" class="bpge_allgroups" name="bpge_groups" <?php echo ('all' == $bpge['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></p></td>
                    <td><p><strong><?php _e('All groups', 'bpge') ?></strong></p></td>
                </tr>
                <?php
                $arg['type'] = 'alphabetical';
                $arg['per_page'] = '1000';
                if ( bp_has_groups($arg) ){
                    while ( bp_groups() ) : bp_the_group();
                        $description = preg_replace( array('<<p>>', '<</p>>', '<<br />>', '<<br>>'), '', bp_get_group_description_excerpt() );
                        echo '<tr>
                                <td class="checkbox"><p><input name="bpge_groups['.bp_get_group_id().']" class="bpge_groups" type="checkbox" '.( ('all' == $bpge['groups'] || in_array(bp_get_group_id(), $bpge['groups']) ) ? 'checked="checked" ' : '').'value="'.bp_get_group_id().'" /></p></td>
                                <td><p></p><a href="'.bp_get_group_permalink().'admin/extras/" target="_blank">'. bp_get_group_name() .'</a> &rarr; '.$description.'</p></td>
                            </tr>';
                    endwhile;
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="header">
                    <td><p><input type="checkbox" class="bpge_allgroups" name="bpge_groups" <?php echo ('all' == $bpge['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></p></td>
                    <td><p><strong><?php _e('All groups', 'bpge') ?></strong></p></td>
                </tr>
            </tfoot>
        </table>
    <?php
    }
    
    //executed to show the plugins complete admin page
    function on_show_page() {
        global $bp, $wpdb, $screen_layout_columns;
        
        //define some data can be given to each metabox during rendering
        $bpge = bp_get_option('bpge');
        ?>
        
        <div id="bpge-admin-general" class="wrap">
            <?php screen_icon('options-general'); ?>
            <style>table.link-group li{margin:0 0 0 25px}</style>
            <h2><?php _e('BuddyPress Groups Extras','bpge') ?> <sup><?php echo 'v' . BPGE_VERSION; ?></sup> &rarr; <?php _e('Extend Your Groups', 'bpge') ?></h2>
        
            <?php 
            if ( isset($_POST['saveData']) ) {
                $bpge['groups'] = $_POST['bpge_groups'] ? $_POST['bpge_groups'] : array();
                $bpge['re'] = $_POST['bpge_re'];

                bp_update_option('bpge', $bpge);

                echo "<div id='message' class='updated fade'><p>" . __('All changes were saved. Go and check results!', 'bpge') . "</p></div>";
            }
            ?>

            <form action="" id="bpge-form" method="post">
                <?php 
                wp_nonce_field('bpge-admin-general');
                wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
                wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
            
                <div id="poststuff" class="metabox-holder<?php echo (2 == $screen_layout_columns) ? ' has-right-sidebar' : ''; ?>">
                    <div id="side-info-column" class="inner-sidebar">
                        <p style="text-align:center">
                            <input type="submit" value="<?php _e('Save Changes', 'bpge') ?>" class="button-primary" name="saveData"/>   
                            <a class="button" href="<?php echo site_url('wp-admin/admin.php?page=bpge-admin'); ?>" title="<?php _e('Refresh current page', 'bpge') ?>"><?php _e('Refresh', 'bpge') ?></a>
                        </p>
                        <?php do_meta_boxes($this->pagehook, 'side', $bpge); ?>
                    </div>
                    <div id="post-body" class="has-sidebar">
                        <div id="post-body-content" class="has-sidebar-content">
                            <?php do_meta_boxes($this->pagehook, 'normal', $bpge); ?>
                            <p>
                                <input type="submit" value="<?php _e('Save Changes', 'bpge') ?>" class="button-primary" name="saveData"/>   
                            </p>
                        </div>
                    </div>
                </div>  
            </form>
        </div>
        <script type="text/javascript">
            jQuery(document).ready( function() {
                jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
            });
        </script>
        
    <?php
    }
}
