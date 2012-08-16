<?php
/*
Plugin Name: Gravity Forms Popup Widget
Plugin URI: http://alex.frenkel-online.com/category/wordpress/plugin/gravity-forms-popup-widget/
Description: Integrate Gravity Forms Widget to dialogue window!
Version: 0.6
Author: Alex (Shurf) Frenkel
Author URI: http://alex.frenkel-online.com
*/

add_action('init', array('GFPopupWidgetClass', 'init'));
add_action("activated_plugin", array('GFPopupWidgetClass',"this_plugin_last"));

class GFPopupWidgetClass
{
    private static $path = "gf_register_popup_widget/widget.php";
    private static $url = "http://www.gravityforms.com";
    private static $slug = "gf_register_popup_widget";
    private static $version = "0.6";
    private static $min_gravityforms_version = "1.5.2";

    //Plugin starting point. Will load appropriate files
    public static function init ()
    {
        add_action("admin_notices", array('GFPopupWidgetClass', 'is_gravity_forms_installed'), 10);

        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css'); 
    }

    public static function uninstall ()
    {
        //Deactivating plugin
        $plugin = "gf_register_popup_widget/widget.php";
        deactivate_plugins($plugin);
        update_option('recently_activated', array($plugin => time()) + (array) get_option('recently_activated'));
    }

    public static function is_gravity_forms_installed ()
    {
        global $pagenow, $page;
        $message = '';
        if ($pagenow != 'plugins.php') {
            return;
        }
        if (! class_exists('RGForms')) {
            if (file_exists(WP_PLUGIN_DIR . '/gravityforms/gravityforms.php')) {
                $message .= '<p>Gravity Forms is installed but not active. <strong>Activate Gravity Forms</strong> to use the Gravity Forms Popup Widget plugin.</p>';
            } else {
                $message .= '<h2><a href="http://katz.si/gravityforms">Gravity Forms</a> is required.</h2><p>You do not have the Gravity Forms plugin enabled. <a href="http://katz.si/gravityforms">Get Gravity Forms</a>.</p>';
            }
        } else {
            if (class_exists("GFCommon")) {
                $is_correct_version = version_compare(GFCommon::$version, self::$min_gravityforms_version, ">=");
                if (!$is_correct_version) {
                    $message .= '<p>Gravity Forms is installed but incorrect version. Requiered version is <b>'.self::$min_gravityforms_version.'</b> current version is <b>'.GFCommon::$version.'</b>';
                }
            }
        }
        if (! empty($message)) {
            echo '<div id="message" class="error">' . $message . '</div>';
        }
    }

    //Returns the url of the plugin's root folder
    protected function get_base_url ()
    {
        return plugins_url(null, __FILE__);
    }

    //Returns the physical path of the plugin's root folder
    protected function get_base_path ()
    {
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . "/" . $folder;
    }

	public function this_plugin_last() {
	// ensure path to this file is via main wp plugin path
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
	if ($this_plugin_key !== false) { 
		array_splice($active_plugins, $this_plugin_key, 1);
		array_push($active_plugins, $this_plugin);
		update_option('active_plugins', $active_plugins);
		}
	}
}


if (class_exists("GFWidget")) {

    add_action('widgets_init', 'gf_register_popup_widget');
    if (! function_exists("gf_register_popup_widget")) {

        function gf_register_popup_widget ()
        {
            register_widget('GFPopupWidget');
        }
    }
    if (! class_exists("GFPopupWidget")) {

        class GFPopupWidget extends GFWidget
        {

            function GFPopupWidget ()
            {
                $this->WP_Widget('gform_popup_widget', 'Form Active Popup', array('classname' => 'gform_popup_widget', 'description' => __('Gravity Forms Popup Widget', "gravity-forms-popup-widget")), 
                array('width' => 'auto', 'height' => 250, 'id_base' => 'gform_popup_widget'));
            }

            function widget ($args, $instance)
            {
		if ($instance['gf_widget_show_homepage'] != 1){
	                if (is_front_page()) { return true; }
		}

		if (!empty($instance['gf_widget_btn_text'])){
			echo '<button type="button" onClick="jQuery(\'#gform_popup_widget\').dialog(\'open\')" class="gf_widget_btn">'.$instance['gf_widget_btn_text']."</button>";
		} elseif (!empty($instance['gf_widget_show_one_of'])){
			$intRandom = rand(1,$instance['gf_widget_show_one_of']);
	                if ($instance['gf_widget_show_one_of'] != $intRandom) { return true; }
		}

                
                $args['before_widget'] = sprintf($instance['introtext'],get_the_title());
                echo '<div id="gform_popup_widget">';                
                parent::widget($args, $instance);

		$strPosition = $instance['gf_widget_position'];
		if (!empty($strPosition)){
			$strPosition = ", position: ".$strPosition;
		}

                echo '</div><script type="text/javascript">';
                echo 'jQuery(document).ready(function(){';
                echo 'jQuery( "#gform_popup_widget" ).dialog({width:"auto",autoOpen:false'.$strPosition.'}).delay(' . $instance['delay'] . ');';

		if (empty($instance['gf_widget_btn_text'])){
                	echo 'jQuery( "#gform_popup_widget" ).delay(' . $instance['delay'] . ').queue(function(){jQuery(this).dialog("open").dequeue();});';
		}
                echo '});';
                echo '</script>';

		
            }

            function update ($new_instance, $old_instance)
            {
                $instance = parent::update($new_instance, $old_instance);
                $instance["delay"] = $new_instance["delay"];
                $instance["introtext"] = $new_instance["introtext"];   
                $instance["gf_widget_position"] = $new_instance["gf_widget_position"];
		$instance["gf_widget_show_homepage"] = $new_instance["gf_widget_show_homepage"]; 
		$instance["gf_widget_btn_text"] = $new_instance["gf_widget_btn_text"]; 
		$instance['gf_widget_show_one_of'] = (int)$new_instance['gf_widget_show_one_of'];
                return $instance;
            }

            function form ($instance)
            {
                parent::form($instance);
                $instance = wp_parse_args((array) $instance, array('title' => __("Contact Us", "gravity-forms-popup-widget"), 'tabindex' => '1'));
                ?>
<hr />
<p><label
	for="<?php
                echo $this->get_field_id('delay');
                ?>"><?php
                _e("Delay Time (milliseconds)", "gravity-forms-popup-widget");
                ?>:</label> <input
	id="<?php
                echo $this->get_field_id('delay');
                ?>"
	name="<?php
                echo $this->get_field_name('delay');
                ?>"
	value="<?php
                echo $instance['delay'];
                ?>"
	style="width: 90%;" /></p>
<p><label
	for="<?php
                echo $this->get_field_id('gf_widget_btn_text');
                ?>"><?php
                _e("Text for opening Button (If the text exists, it overides the delay. In this case the popup only opens the the button is pressed)", "gravity-forms-popup-widget");
                ?>:</label> <input
	id="<?php
                echo $this->get_field_id('gf_widget_btn_text');
                ?>"
	name="<?php
                echo $this->get_field_name('gf_widget_btn_text');
                ?>"
	value="<?php
                echo $instance['gf_widget_btn_text'];
                ?>"
	style="width: 90%;" /></p>
	<p><label
	for="<?php
                echo $this->get_field_id('introtext');
                ?>"><?php
                _e("Introduction Text - Use %s for the page title.", "gravity-forms-popup-widget");
                ?>:</label> <input
	id="<?php
                echo $this->get_field_id('introtext');
                ?>"
	name="<?php
                echo $this->get_field_name('introtext');
                ?>"
	value="<?php
                echo $instance['introtext'];
                ?>"
	style="width: 90%;" /></p>
	<p><label
	for="<?php
                echo $this->get_field_id('gf_widget_position');
                ?>"><?php
                _e("<strong>Position</strong><br/>Specifies where the dialog should be displayed. <br/><strong>Possible values</strong>:<br/>
1) a single string representing position within viewport: 'center', 'left', 'right', 'top', 'bottom'.<br/>
2) an array containing an x,y coordinate pair in pixel offset from left, top corner of viewport (e.g. [350,100])<br/>
3) an array containing x,y position string values (e.g. ['right','top'] for top right corner)", "gravity-forms-popup-widget");
                ?>:</label> <input
	id="<?php
                echo $this->get_field_id('gf_widget_position');
                ?>"
	name="<?php
                echo $this->get_field_name('gf_widget_position');
                ?>"
	value="<?php
                echo $instance['gf_widget_position'];
                ?>"
	style="width: 90%;" /></p>
	<p>
		    <input type="checkbox" name="<?php echo $this->get_field_name( 'gf_widget_show_homepage' ); ?>" id="<?php echo $this->get_field_id( 'gf_widget_show_homepage' ); ?>" <?php checked($instance['gf_widget_show_homepage']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'gf_widget_show_homepage' ); ?>"><?php _e("Display Popup on Homepage", "gravity-forms-popup-widget"); ?></label>
		</p>
	<p>
<label for="<?php echo $this->get_field_id( 'gf_widget_show_one_of' ); ?>"><?php _e("Display only 1 out of X views (random)", "gravity-forms-popup-widget"); ?></label><input type="text" name="<?php echo $this->get_field_name( 'gf_widget_show_one_of' ); ?>" id="<?php echo $this->get_field_id( 'gf_widget_show_one_of' ); ?>" value="<?php echo $instance['gf_widget_show_one_of'];?>" /> 
		</p>

<?php
            }
        }
    }
}
