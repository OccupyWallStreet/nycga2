<?php

include_once('olt-checklist/loader.php');
enqueue_olt_checklist_loader(plugins_url('section-widget/olt-checklist'));

include_once('section-widget-options-page.php');

/**
 * Tabbed section widget class
 */
/**
 * OLT_Tabbed_Section_Widget class.
 * 
 * @extends WP_Widget
 */
class OLT_Tabbed_Section_Widget extends WP_Widget {
    /**
     * OLT_Tabbed_Section_Widget function.
     * 
     * @access public
     * @return void
     */
    function OLT_Tabbed_Section_Widget() {
        $widget_ops = array('classname' => 'section-widget-tabbed', 'description' => __('Display section-specific content in tabs.'));
        $control_ops = array('width' => 400);
        $this->WP_Widget('section-tabbed', __('Section (Tabbed)'), $widget_ops, $control_ops);
    }
    /**
     * widget function.
     * 
     * @access public
     * @param mixed $args
     * @param mixed $instance
     * @return void
     */
    function widget( $args, $instance ) {
        extract($args);
        
        extract(wp_parse_args((array) get_option('section-widget-settings'), array(
            'heightfix' => false // This is all I care about
        )));
        
        if(isset($_GET['swt-scope-test'])) {
            echo $before_widget . '<div class="swt-wrapper">Section Widget Scope Test</div>' . $after_widget;
            return;
        }
        
        // olt_checklist_conditions_check is the replacement for $should_display
        if(olt_checklist_conditions_check($instance['conditions'])) {
            if(count($instance['tabs']) == 0)
                return;
            
            $list = '';
            $content = '';
            
            foreach($instance['tabs'] as $id => $tab) {
                $list .= "<li><a href=\"#{$widget_id}-tab-{$id}\">{$tab['title']}</a></li>";
                $content .= "<div id=\"{$widget_id}-tab-{$id}\">".do_shortcode($tab['body']).'</div>';
            }
            
            $heightFixClass = ($heightfix)? ' class="swt-height-fix"' : '';
            
            $html = "<ul{$heightFixClass}>".$list.'</ul>'.$content;
            
            echo $before_widget;
            
            if($instance['display-title']){
                echo $before_title;
                echo apply_filters('widget_title', $instance['title']);
                echo $after_title;
            }
            echo '<div class="swt-outter"><div class="swt-wrapper">';
            echo apply_filters('widget_text', $html);
            echo '</div></div>';
            echo $after_widget;
        }
    }
    /**
     * update function.
     * 
     * @access public
     * @param mixed $new_instance
     * @param mixed $old_instance
     * @return void
     */
    function update( $new_instance, $old_instance ) {
        // Mostly borrowed from text widget
        $instance = $old_instance;
        
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['display-title'] = (bool) $new_instance['display-title'];
        
        $instance['conditions'] = is_array($new_instance['conditions'])?
            $new_instance['conditions'] : array();
        
        $instance['conditions']['special-pages'] =
            is_array($new_instance['conditions']['special-pages'])?
                $new_instance['conditions']['special-pages'] : array();
        
        $instance['conditions']['pages'] =
            is_array($new_instance['conditions']['pages'])?
                $new_instance['conditions']['pages'] : array();
        
        $instance['conditions']['categories'] =
            is_array($new_instance['conditions']['categories'])?
                $new_instance['conditions']['categories'] : array();
        
        $instance['conditions']['tags'] =
            is_array($new_instance['conditions']['tags'])?
                $new_instance['conditions']['tags'] : array();
        
        $instance['tabs'] = array();
        
        if(is_array($new_instance['tabs'])) {
            $tabs = array();
            
            if(isset($new_instance['order']) && $new_instance['order'] != '') {
                // order=1&order=0&order=2...
                $order = explode('&', str_replace('order=', '', $new_instance['order']));
                
                foreach($order as $i) {
                    if(isset($new_instance['tabs'][intval($i)])) {
                        $tabs[] = $new_instance['tabs'][intval($i)];
                        unset($new_instance['tabs'][intval($i)]);
                    }
                }
            }
            
            $tabs = array_merge($tabs, $new_instance['tabs']);
            
            foreach($tabs as $tab){
                $title = strip_tags($tab['title']);
                if ( current_user_can('unfiltered_html') )
                    $body =  $tab['body'];
                else
                    $body = wp_filter_post_kses( $tab['body'] );
                
                $instance['tabs'][] = array(
                    'title' => $title,
                    'body' => $body
                );
            }
        }
        
        // Processing tabs below
        return $instance;
    }
    /**
     * form function.
     * 
     * @access public
     * @param mixed $instance
     * @return void
     */
    function form( $instance ) {
        // Provide the defaults here
        $instance = wp_parse_args((array) $instance, array(
            'title' => '',
            'display-title' => true,
            'tabs' => array(),
            'conditions' => array(
                'special-pages' => array(),
                'pages' => array(),
                'categories' => array(),
                'tags' => array()
            )
        ));
        
        // Make sure second level options are actually arrays
        foreach($instance['tabs'] as $i => $v)
            if(!is_array($v))
                $instance['tabs'][$i] = array();
        
        foreach($instance['conditions'] as $i => $v)
            if(!is_array($v))
                $instance['conditions'][$i] = array();
        
                
        $title = strip_tags($instance['title']);
        $display_title = (bool) $instance['display-title'];
        $special_pages = $instance['conditions']['special-pages'];
        $pages = $instance['conditions']['pages'];
        $categories = $instance['conditions']['categories'];
        $tags = $instance['conditions']['tags'];
                
        $tabs = is_array($instance['tabs'])? $instance['tabs'] : array();
        
        foreach($tabs as $i => $tab) {
            $tabs[$i]['title'] = strip_tags($tab['title']);
            $tabs[$i]['body'] = format_to_edit($tab['body']);
        }
?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','section-widget'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            <input id="<?php echo $this->get_field_id('display-title'); ?>" name="<?php echo $this->get_field_name('display-title'); ?>" type="checkbox" <?php checked($display_title); ?> />
            <label for="<?php echo $this->get_field_id('display-title'); ?>"><?php _e('Display title','section-widget'); ?></label>
        </p>
<?php   
        olt_checklist_pane(array(
            'id' => $this->get_field_id('conditions'),
            'name' => $this->get_field_name('conditions'),
            'special-pages' => array('selected' => $special_pages),
            'pages' => array('selected' => $pages),
            'categories' => array('selected' => $categories),
            'tags' => array('selected' => $tags)
        ));
?>
        <div class="olt-swt-designer">
            <input type="hidden" name="idprefix" value="<?php echo $this->get_field_id('tab') ?>" />
            <input type="hidden" name="nameprefix" value="<?php echo $this->get_field_name('tabs') ?>" />
            <input type="hidden" name="<?php echo $this->get_field_name('order') ?>" class="olt-swt-order" />
            <div class="olt-swt-designer-wrapper" id="<?php echo $this->get_field_id('designer-wrapper') ?>">
                <div class="olt-swt-designer-main" id="<?php echo $this->get_field_id('designer-main') ?>">
                    <ul>
                        <?php foreach($tabs as $id => $tab): ?>
                        <li class="olt-swt-designer-tab" id="<?php echo $this->get_field_id('tab-id-'.$id); ?>">
                            <a href="#<?php echo $this->get_field_id('tab-'.$id); ?>" id="<?php echo $this->get_field_id('tab-'.$id.'-title-link'); ?>">
                                <?php echo esc_html($tab['title']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <li class="olt-swt-designer-tabs-controls olt-swt-designer-add-tab">
                            <a><span class="ui-icon ui-icon-plusthick" style="float:left;margin-right:.3em;margin-top: -1px;"></span><?php _e('Add a new tab','section-widget'); ?></a>
                        </li>
                    </ul>
                    <?php foreach($tabs as $id => $tab): ?>
                    <div id="<?php echo $this->get_field_id('tab-'.$id) ?>">
                        <div class="olt-swt-designer-top">
                            <label for="<?php echo $this->get_field_id('tab-'.$id.'-title'); ?>"><?php _e('Title:','section-widget'); ?></label>
                            <input id="<?php echo $this->get_field_id('tab-'.$id.'-title'); ?>" class="olt-swt-designer-tab-title" name="<?php echo $this->get_field_name('tabs')."[$id][title]"; ?>" type="text" value="<?php echo esc_attr($tab['title']); ?>" />
                            <p class="olt-swt-designer-tabs-controls olt-swt-designer-delete-tab">
                                <a href="#"><span class="ui-icon ui-icon-trash" style="float:left;margin-right:.3em;margin-top: -2px;"></span><?php _e('Delete this tab','section-widget');?></a>
                            </p>
                        </div>
                        <div class="olt-sw-body">
                            <p class="olt-sw-body-help">
                                <?php _e('<strong>Formatting Help:</strong> You may use HTML in this widget, and it is probably a good idea to wrap the content in your own <code>&lt;div&gt;</code> to aid styling. Shortcodes are also allowed, but please beware not all of them will function properly on archive pages.','section-widget');?></p>
                            <textarea rows="16" cols="20" name="<?php echo $this->get_field_name('tabs')."[$id][body]"; ?>"><?php echo esc_html($tab['body']); ?></textarea>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            if(typeof OLTChecklistPaneInit == 'function')
                OLTChecklistPaneInit(jQuery('#<?php echo $this->get_field_id('conditions-wrapper'); ?>'));
            if(typeof OLTSWTInit == 'function')
                OLTSWTInit(jQuery('#<?php echo $this->get_field_id('designer-main') ?>'));
        </script>
<?php
    }
}
/**
 * tabbed_section_widget_init function.
 * 
 * @access public
 * @return void
 */
function tabbed_section_widget_init() {
    register_widget('OLT_Tabbed_Section_Widget');
}
/**
 * tabbed_section_widget_load_scripts function.
 * 
 * @access public
 * @return void
 */
function tabbed_section_widget_load_scripts() {
    extract(wp_parse_args((array) get_option('section-widget-settings'), array(
        'theme' => 'redmond',
        'scope' => '.swt-outter',
        'heightfix' => false
    )));
    
    if(is_admin() ) {
    	global $pagenow;
    	if( $pagenow == 'widgets.php' ):
    	
        	if($theme == 'none') $theme = 'base';
        	
        	wp_enqueue_style('section-widget-admin', plugins_url('section-widget/section-widget-admin.css'));
        	wp_enqueue_style("section-widget-theme-{$theme}", plugins_url("section-widget/themes/theme-loader.php?theme={$theme}&scope=.olt-swt-designer"));
        	wp_enqueue_script('section-widget-admin', plugins_url('section-widget/section-widget-admin.js'), array('jquery','jquery-ui-tabs','jquery-ui-sortable'));
        endif;
    } else {
        // Only load script and css if there is at least one active tabbed widget
        if(is_active_widget(false,false,'section-tabbed')) {    
            if($theme != 'none')
                wp_enqueue_style("section-widget-theme-{$theme}", plugins_url("section-widget/themes/theme-loader.php?theme={$theme}&scope=").urlencode($scope));
            
            wp_enqueue_script('section-widget', plugins_url('section-widget/section-widget.js'), array('jquery','jquery-ui-tabs'));
        }
    }
}

### Function: Init Section Widget
add_action('widgets_init', 'tabbed_section_widget_init');
add_action('init', 'tabbed_section_widget_load_scripts');

?>