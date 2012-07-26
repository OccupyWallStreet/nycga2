<?php
/**
* Options
*/
class Mappress_Options extends Mappress_Obj {
    var $directions = 'inline',                             // inline | google | none
        $directionsServer = 'maps.google.com',
        $mapTypeControl = true,
        $streetViewControl = true,
        $scrollwheel = false,
        $keyboardShortcuts = true,
        $navigationControlOptions = array('style' => 0),
        $overviewMapControl = true,
        $overviewMapControlOptions = array('opened' => false),
        $initialOpenInfo = false,
        $initialOpenDirections = false,
        $country = null,
        $language = null,
        $traffic = false,
        $initialTraffic = false,        // Initial setting for traffic checkbox (true = checked)        
        $tooltips = true,
        $alignment = 'default',
        $autodisplay = 'top',
        $editable = false,
        $mapName = null,
        $postid = null,
        $postTypes = array('post', 'page'),
        $geoRSS = false,
        $control = true,
        $poiList = false,
        $poiListTemplate = "<td class='mapp-marker'>[icon]</td><td><b>[title]</b>[directions]</td>",
        $metaKey = null,
        $metaSyncSave = true,
        $metaSyncUpdate = true,
        $metaKeyErrors = null,
        $mapSizes = array(array('label' => null, 'width' => 300, 'height' => 300), array('label' => null, 'width' => 425, 'height' => 350), array('label' => null, 'width' => 640, 'height' => 480)),
        $border = array('style' => null, 'width' => 1, 'radius' => 0, 'color' => '#000000', 'shadow' => false),
        $demoMap = true,
        $user = true,
        $userInitial = false,
        $userCenter = false,
        $userTitle = "Your location",
        $userBody = null
        ;
        
    // Options are saved as array because WP settings API is fussy about objects
    static function get() {
        $options = get_option('mappress_options');
        return new Mappress_Options($options);
    }

    function save() {
        return update_option('mappress_options', get_object_vars($this));
    }
}      // End class Mappress_Options


/**
* Options menu display
*/
class Mappress_Settings {
    
    var $options;
    
    function __construct() {
        // Register menu settings
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('admin_init', array(&$this, 'admin_init'));
    }

    function admin_menu() {
        // Add menu
        $pagehook = add_options_page('MapPress', 'MapPress', 'manage_options', 'mappress', array(&$this, 'options_page'));        
        
        // Add settings scripts
        add_action("admin_print_scripts-{$pagehook}", array(&$this, 'admin_print_scripts'));
        add_action("admin_print_styles-{$pagehook}", array(&$this, 'admin_print_styles'));
    }
    
    /**
    * Scripts and styles for settings screen
    *
    */
    function admin_print_scripts() {
        wp_enqueue_script('postbox');
        wp_enqueue_script( 'farbtastic' );  
    }                                 

    function admin_print_styles() {
        wp_enqueue_style('mappress', plugins_url('/css/mappress.css', __FILE__), null, Mappress::$version);
        wp_enqueue_style('farbtastic');          
    }
        
    function admin_init() {           
        $this->options = Mappress_Options::get();
        
        register_setting('mappress', 'mappress_options', array($this, 'set_options'));

        add_settings_section('basic_settings', __('Basic Settings', 'mappress'), array(&$this, 'section_settings'), 'mappress');
        add_settings_field('demoMap', __('Show a sample map on this page', 'mappress'), array(&$this, 'set_demo_map'), 'mappress', 'basic_settings');        
        add_settings_field('autodisplay', __('Automatic map display', 'mappress'), array(&$this, 'set_autodisplay'), 'mappress', 'basic_settings');
        add_settings_field('postTypes', __('Post types', 'mappress'), array(&$this, 'set_post_types'), 'mappress', 'basic_settings');
        add_settings_field('directions', __('Directions', 'mappress'), array(&$this, 'set_directions'), 'mappress', 'basic_settings');
        add_settings_field('poiList', __('Marker list', 'mappress'), array(&$this, 'set_poi_list'), 'mappress', 'basic_settings');
        add_settings_field('mapTypeControl', __('Map types', 'mappress'), array(&$this, 'set_map_type_control'), 'mappress', 'basic_settings');
        add_settings_field('streetViewControl', __('Street View', 'mappress'), array(&$this, 'set_streetview_control'), 'mappress', 'basic_settings');
        add_settings_field('scrollwheel', __('Scroll wheel zoom', 'mappress'), array(&$this, 'set_scrollwheel'), 'mappress', 'basic_settings');
        add_settings_field('keyboard', __('Keyboard shortcuts', 'mappress'), array(&$this, 'set_keyboard_shortcuts'), 'mappress', 'basic_settings');
        add_settings_field('initialOpenInfo', __('Open first marker', 'mappress'), array(&$this, 'set_initial_open_info'), 'mappress', 'basic_settings');
        add_settings_field('traffic', __('Show traffic button', 'mappress'), array(&$this, 'set_traffic'), 'mappress', 'basic_settings');
        add_settings_field('tooltips', __('Tooltips', 'mappress'), array(&$this, 'set_tooltips'), 'mappress', 'basic_settings');
        add_settings_field('overviewMapControl', __('Overview map', 'mappress'), array(&$this, 'set_overview_map_control'), 'mappress', 'basic_settings');

        add_settings_section('css_settings', __('CSS Settings', 'mappress'), array(&$this, 'section_settings'), 'mappress');
        add_settings_field('alignment', __('Map alignment', 'mappress'), array(&$this, 'set_alignment'), 'mappress', 'css_settings');
        add_settings_field('border', __('Map border', 'mappress'), array(&$this, 'set_border'), 'mappress', 'css_settings');        

        // Coming in 2.39
//        add_settings_section('user_settings', __('Geolocation', 'mappress'), array(&$this, 'section_settings'), 'mappress');
//        add_settings_field('user', __('Show user location', 'mappress'), array(&$this, 'set_user'), 'mappress', 'user_settings');
//        add_settings_field('userInitial', __('Open location marker', 'mappress'), array(&$this, 'set_user_initial'), 'mappress', 'user_settings');        
//        add_settings_field('userCenter', __('Center on user', 'mappress'), array(&$this, 'set_user_center'), 'mappress', 'user_settings');                
//        add_settings_field('userTitle', __('User marker title', 'mappress'), array(&$this, 'set_user_title'), 'mappress', 'user_settings');                        
//        add_settings_field('userBody', __('User marker body', 'mappress'), array(&$this, 'set_user_body'), 'mappress', 'user_settings');                                
                
        add_settings_section('localization_settings', __('Localization', 'mappress'), array(&$this, 'section_settings'), 'mappress');
        add_settings_field('language', __('Language', 'mappress'), array(&$this, 'set_language'), 'mappress', 'localization_settings');
        add_settings_field('country', __('Country', 'mappress'), array(&$this, 'set_country'), 'mappress', 'localization_settings');
        add_settings_field('directionsServer', __('Directions server', 'mappress'), array(&$this, 'set_directions_server'), 'mappress', 'localization_settings');

        add_settings_section('template_settings', __('Templates', 'mappress'), array(&$this, 'section_settings'), 'mappress');
        add_settings_field('poiListTemplate', __('Marker list template', 'mappress'), array(&$this, 'set_poi_list_template'), 'mappress', 'template_settings');

        add_settings_section('custom_field_settings', __('Custom Fields', 'mappress'), array(&$this, 'section_settings'), 'mappress');
        add_settings_field('metaKey', __('Custom fields', 'mappress'), array(&$this, 'set_meta_key'), 'mappress', 'custom_field_settings');

        //@todo add_settings_section('georss_settings', __('GeoRSS', 'mappress'), array(&$this, 'section_settings'), 'mappress');
        //@todo add_settings_field('geoRSS', __('GeoRSS', 'mappress'), array(&$this, 'set_georss'), 'mappress', 'georss_settings');

        add_settings_section('misc_settings', __('Micsellaneous', 'mappress'), array(&$this, 'section_settings'), 'mappress');
        add_settings_field('mapSizes', __('Map sizes', 'mappress'), array(&$this, 'set_map_sizes'), 'mappress', 'misc_settings');
        add_settings_field('forceresize', __('Force resize', 'mappress'), array(&$this, 'set_force_resize'), 'mappress', 'misc_settings');
        add_settings_field('link', __('MapPress link', 'mappress'), array(&$this, 'set_control'), 'mappress', 'misc_settings');
    }

    function set_options($input) {
        global $mappress;
        
        // If reset defaults was clicked
        if (isset($_POST['reset_defaults'])) {
            $options = new Mappress_Options();
            return get_object_vars($this);
        }

        // If resize was clicked then resize ALL maps
        if (isset($_POST['force_resize']) && $_POST['resize_from']['width'] && $_POST['resize_from']['height']
        && $_POST['resize_to']['width'] && $_POST['resize_to']['height']) {
            $maps = Mappress_Map::get_list();
            foreach ($maps as $map) {
                if ($map->width == $_POST['resize_from']['width'] && $map->height == $_POST['resize_from']['height']) {
                    $map->width = $_POST['resize_to']['width'];
                    $map->height = $_POST['resize_to']['height'];
                    $map->save($postid);
                }
            }
        }
        
        // If NO post types selected, set value to empty array
        if (!isset($input['postTypes']))
            $input['postTypes'] = array();
            
        // Force checkboxes to boolean
        foreach($input as &$item) 
            $item = Mappress::convert_to_boolean($item);

        if (!class_exists('Mappress_Pro')) {
            $input['control'] = true;
            unset($input['metaKey'], $input['metaSyncSave'], $input['metaSyncUpdate']);
        }
        return $input;
    }

    function section_settings() {
    }

    function set_country() {
        $country = $this->options->country;
        $cctld_link = '<a target="_blank" href="http://en.wikipedia.org/wiki/CcTLD#List_of_ccTLDs">' . __("country code", 'mappress') . '</a>';

        printf(__('Enter a %s to use when searching (leave blank for USA)', 'mappress'), $cctld_link);
        echo ": <input type='text' size='2' name='mappress_options[country]' value='$country' />";
    }

    function set_directions_server() {
        $directions_server = $this->options->directionsServer;

        echo __('Enter a google server URL for directions/printing');
        echo ": <input type='text' size='20' name='mappress_options[directionsServer]' value='$directions_server' />";
    }

    function set_scrollwheel() {      
        echo Mappress::checkbox($this->options->scrollwheel, 'mappress_options[scrollwheel]');
        _e('Enable zoom with the mouse scroll wheel', 'mappress');
    }

    function set_keyboard_shortcuts() {
        echo Mappress::checkbox($this->options->keyboardShortcuts, 'mappress_options[keyboardShortcuts]');
        _e('Enable keyboard panning and zooming', 'mappress');
    }

    function set_language() {
        $language = $this->options->language;
        $lang_link = '<a target="_blank" href="http://code.google.com/apis/maps/faq.html#languagesupport">' . __("language", 'mappress') . '</a>';

        printf(__('Use a specific %s for map controls (defaults to browser language)', 'mappress'), $lang_link);
        echo ": <input type='text' size='2' name='mappress_options[language]' value='$language' />";

    }

    function set_map_type_control() {
        echo Mappress::checkbox($this->options->mapTypeControl, 'mappress_options[mapTypeControl]');
        _e ('Allow your readers to change the map type (street, satellite, or hybrid)', 'mappress');
    }

    function set_streetview_control() {
        echo Mappress::checkbox($this->options->streetViewControl, 'mappress_options[streetViewControl]');
        _e ('Display the street view control "peg man"', 'mappress');
    }

    function set_directions() {
        $directions = $this->options->directions;

        echo "<input type='radio' name='mappress_options[directions]' value='inline' " . checked($directions, 'inline', false) . "/>";
        echo __('Inline (in your blog)', 'mappress');

        echo "&nbsp;&nbsp;";
        echo "<input type='radio' name='mappress_options[directions]' value='google' " . checked($directions, 'google', false) . "/>";
        echo __('Google', 'mappress');

        echo "&nbsp;&nbsp;";
        echo "<input type='radio' name='mappress_options[directions]' value='none' "  . checked($directions, 'none', false) . " />";
        echo __('None', 'mappress');

        echo "<br/><i>" . __("Select 'Google' if directions aren't displaying properly in your theme", 'mappress') . "</i>";
    }

    function set_poi_list() {
        $pro_link = "<a href='http://wphostreviews.com/mappress/mappress' title='MapPress Pro'>MapPress Pro</a>";

        printf(__("This setting requires %s.", 'mappress'), $pro_link);
        echo " " . __("Show a list of markers under each map", 'mappress');
    }

    function set_initial_open_info() {
        echo Mappress::checkbox($this->options->initialOpenInfo, 'mappress_options[initialOpenInfo]');
        _e('Automatically open the first marker when a map is displayed', 'mappress');
    }

    function set_traffic() {
        
        echo Mappress::checkbox($this->options->traffic, 'mappress_options[traffic]');
        _e('Show a button for real-time traffic conditions', 'mappress');
        
        echo "<br/>" . Mappress::checkbox($this->options->initialTraffic, 'mappress_options[initialTraffic]');
        _e("Set traffic 'on' by default", 'mappress');        
    }

    function set_tooltips() {
        echo Mappress::checkbox($this->options->tooltips, 'mappress_options[tooltips]');
        _e('Show marker titles as a "tooltip" on mouse-over', 'mappress');
    }

    function set_overview_map_control() {       
        echo Mappress::checkbox($this->options->overviewMapControl, 'mappress_options[overviewMapControl]');
        _e('Show an overview map control in the bottom-right corner of the main map', 'mappress');
        
        echo "<br/>";
        echo Mappress::checkbox($this->options->overviewMapControlOptions['opened'], 'mappress_options[overviewMapControlOptions][opened]');
        _e ('Automatically open the overview map', 'mappress');
    }

    function set_alignment() {
        $alignment = $this->options->alignment;

        echo "<input type='radio' name='mappress_options[alignment]' value='default' " . checked($alignment, 'default', false) . "/>";
        echo __('Default', 'mappress');

        echo "&nbsp;&nbsp;";
        echo "<input type='radio' name='mappress_options[alignment]' value='center' " . checked($alignment, 'center', false) . "/>";
        echo "<img src='" . plugins_url('/images/justify_center.png', __FILE__) . "' style='vertical-align:middle' title='" . __('Center', 'mappress') . "' />";
        echo __('Center', 'mappress');

        echo "&nbsp;&nbsp;";
        echo "<input type='radio' name='mappress_options[alignment]' value='left' "  . checked($alignment, 'left', false) . " />";
        echo "<img src='" . plugins_url('/images/justify_left.png', __FILE__) . "' style='vertical-align:middle' title='" . __('Left', 'mappress') . "' />";
        echo __('Left', 'mappress');

        echo "&nbsp;&nbsp;";
        echo "<input type='radio' name='mappress_options[alignment]' value='right' "  . checked($alignment, 'right', false) . " />";
        echo "<img src='" . plugins_url('/images/justify_right.png', __FILE__) . "' style='vertical-align:middle' title='" . __('Right', 'mappress') . "' />";
        echo __('Right', 'mappress');
        
        echo "<br/><i>" . sprintf(__("Choose 'default' to override this with CSS class %s in your theme's %s", 'mappress'), "<code>.mapp-container</code>", "<code>styles.css</code>")  . "</i>";        
    }

    function set_border() {        
        $border = $this->options->border;
        
        $border_styles = array(
            '-none-' => '', 
            __('solid', 'mappress') => 'solid', 
            __('dashed', 'mappress') => 'dashed', 
            __('dotted', 'mappress') => 'dotted', 
            __('double', 'mappress') => 'double', 
            __('groove', 'mappress') => 'groove', 
            __('inset', 'mappress') => 'inset', 
            __('outset', 'mappress') => 'outset'
        );

        // Border style
        echo __("Style", 'mappress') . ": <select name='mappress_options[border][style]'>";
        foreach ($border_styles as $label => $value)
            echo "<option " . selected($value, $border['style'], false) . " value='$value'>$label</option>";
        echo "</select>";

        // Border width        
        for ($i = 1; $i <= 20; $i++)
            $widths[] = $i . "px";
        echo "&nbsp; " . __("Width", 'mappress') . ": <select name='mappress_options[border][width]'>";
        foreach ($widths as $width)
            echo "<option " . selected($width, $border['width'], false) . " value='$width'>$width</option>";
        echo "</select>";

        // Corners
        for ($i = 1; $i <= 10; $i++) 
            $radii[$i] = $i . "px";
        echo "&nbsp; " . __("Corner radius", 'mappress');
        echo Mappress::dropdown($radii, $border['radius'], 'mappress_options[border][radius]', array('none' => true));
                
        // Border color
        echo "&nbsp; " . __("Color", 'mappress');
        echo ": <input type='text' id='mappress_border_color' name='mappress_options[border][color]' value='" . $border['color'] . "' size='10'/>";

        // Shadow
        echo "&nbsp; " . Mappress::checkbox($border['shadow'], 'mappress_options[border][shadow]');
        echo "&nbsp;" . __("Shadow", 'mappress');
        
        echo "<br/><i>" . sprintf(__("Choose -none- to override settings with CSS class %s in your theme's %s", 'mappress'), "<code>.mapp-canvas-panel</code>", "<code>styles.css</code>")  . "</i>";

        // Color wheel                        
        echo "<div id='mappress_border_color_picker'></div>
            <script type='text/javascript'>
                    jQuery(document).ready(function() {
                        jQuery('#mappress_border_color_picker').hide();   
                        jQuery('#mappress_border_color_picker').farbtastic('#mappress_border_color');
                        jQuery('#mappress_border_color').click(function(){
                            jQuery('#mappress_border_color_picker').slideToggle();
                        });  
                    });
            </script>
        ";                
    }
    
    function set_user() {
        echo Mappress::checkbox($this->options->user, 'mappress_options[user]');
        _e("Show the user's location on the map", 'mappress');
    }
    
    function set_user_initial() {
        echo Mappress::checkbox($this->options->userInitial, 'mappress_options[userInitial]');
        _e("Open the user's marker when the map is displayed", 'mappress');
    }
    
    function set_user_center() {
        echo Mappress::checkbox($this->options->userCenter, 'mappress_options[userCenter]');
        _e("Center the map on the user's location", 'mappress');
    }

    function set_user_title() {
        echo "<input type='text' size='30' name='mappress_options[userTitle]' value='{$this->options->userTitle}'/>";
    }

    function set_user_body() {
        echo "<textarea type='text' rows='2' cols='80' name='mappress_options[userBody]'>";
        echo esc_attr($this->options->userBody);
        echo "</textarea>";
    }
                    
    function set_demo_map() {
        echo Mappress::checkbox($this->options->demoMap, 'mappress_options[demoMap]');
        _e('Show a sample map on this page', 'mappress');
    }
        
    function set_autodisplay() {
        $autodisplay = $this->options->autodisplay;

        echo "<input type='radio' name='mappress_options[autodisplay]' value='top' " . checked($autodisplay, 'top', false) . "/>";
        echo __('Top of post', 'mappress');

        echo "&nbsp;&nbsp;";
        echo "<input type='radio' name='mappress_options[autodisplay]' value='bottom' " . checked($autodisplay, 'bottom', false) . "/>";
        echo __('Bottom of post', 'mappress');

        echo "&nbsp;&nbsp;";
        echo "<input type='radio' name='mappress_options[autodisplay]' value='none' " . checked($autodisplay, 'none', false) . "/>";
        echo __('No automatic display', 'mappress');
    }

    function set_post_types() {
        $post_types = $this->options->postTypes;
        $all_post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
        $all_post_types[] = 'post';
        $all_post_types[] = 'page';
        $codex_link = "<a href='http://codex.wordpress.org/Custom_Post_Types'>" . __('post types', 'mappress') . "</a>";

        echo sprintf(__("Mark the %s where the MapPress Editor should be available", "mappress"), $codex_link) . ": <br/>";
        
        foreach ($all_post_types  as $post_type ) {
            $checked = (in_array($post_type, (array)$post_types)) ? " checked='checked' " : "";
            // Translate standard types
            $label = $post_type;
            if ($label == 'post')
                $label = __('post', 'mappress');
            if ($label == 'page')
                $label = __('page', 'mappress');

            echo "<input type='checkbox' name='mappress_options[postTypes][]' value='$post_type' $checked />$label ";
        }
    }

    function set_force_resize() {
        $from = "<input type='text' size='2' name='resize_from[width]' value='' />"
            . "x<input type='text' size='2' name='resize_from[height]' value='' /> ";
        $to = "<input type='text' size='2' name='resize_to[width]]' value='' />"
            . "x<input type='text' size='2' name='resize_to[height]]' value='' /> ";
        echo __('Permanently resize existing maps:', 'mappress');
        echo "<br/>";
        printf(__('from %s to %s', 'mappress'), $from, $to);
        echo "<input type='submit' name='force_resize' class='button' value='" . __('Force Resize') . "' />";
    }

    function set_poi_list_template() {
        $pro_link = "<a href='http://wphostreviews.com/mappress/mappress' title='MapPress Pro'>MapPress Pro</a>";
        printf(__("This setting requires %s.", 'mappress'), $pro_link);
        echo " " . __("Set a template for the marker list", 'mappress');
    }

    function set_control() {
        $pro_link = "<a href='http://wphostreviews.com/mappress/mappress' title='MapPress Pro'>MapPress Pro</a>";

        printf(__("This setting requires %s.", 'mappress'), $pro_link);
        echo " " . __("Toggle the 'powered by' message", 'mappress');
    }

    function set_meta_key() {
        $pro_link = "<a href='http://wphostreviews.com/mappress/mappress' title='MapPress Pro'>MapPress Pro</a>";

        printf(__("This setting requires %s.", 'mappress'), $pro_link);
        echo " " . __("Automatically create maps from custom field data", 'mappress');
    }

    function set_map_sizes() {
        $pro_link = "<a href='http://wphostreviews.com/mappress/mappress' title='MapPress Pro'>MapPress Pro</a>";

        printf(__("This setting requires %s.", 'mappress'), $pro_link);
        echo " " . __("Set custom map sizes", 'mappress');
    }

    function set_georss() {
        $checked = ($this->options->geoRSS) ? " checked='checked'" : "";

        $georss_title = __('simple GeoRSS', 'mappress');
        $georss_link = "<a href='http://www.georss.org/Main_Page' title='$georss_title'>$georss_title</a>";

        echo Mappress::checkbox($this->options->geoRSS, 'mappress_options[geoRSS]');
        printf(__('Enable %s for your RSS feeds', 'mappress'), $georss_link);
        echo "<i> (beta - see readme.txt)</i>";
    }

    /**
    * RSS metabox
    *
    */
    function metabox_rss() {
        $news_rss_url = 'http://www.wphostreviews.com/category/news/feed';
        $news_url = 'http://wphostreviews.com/category/news';

        include_once(ABSPATH . WPINC . '/feed.php');
        $rss = fetch_feed( $news_rss_url );

        if ( is_wp_error($rss) ) {
            echo "<li>" . __('No new items') . "</li>";
            return false;
        }

        $maxitems = $maxitems = $rss->get_item_quantity(5);
        $rss_items = $rss->get_items( 0, $maxitems );

        echo '<ul>';
        if ( !$rss_items ) {
            echo "<li>" . __('No new items') . "</li>";
        } else {
            foreach ( $rss_items as $item ) {
                echo '<li>'
                    . '<a class="rsswidget" href="' . esc_url( $item->get_permalink() ). '">' . esc_html( $item->get_title() ) .'</a> '
                    . '</li>';
            }
        }
        echo '</ul>';
        echo "<br/><img src='" . plugins_url('images/news.png', __FILE__) . "'/> <a href='$news_url'>" . __("Read More", 'mappress') . "</a>";
        echo "<br/><br/><img src='" . plugins_url('images/rss.png', __FILE__) . "'/> <a href='$news_rss_url'>" . __("Subscribe with RSS", 'mappress') . "</a>";
    }


    /**
    * Like metabox
    *
    */
    function metabox_like() {
        $rate_link = "<a href='http://wordpress.org/extend/plugins/mappress-easy-google-maps'>" . __('5 Stars', 'mappress') . "</a>";
        echo "<ul>";
        echo "<li>" . __('Please take a moment to support future development ', 'mappress') . ':</li>';
        echo "<li>" . sprintf(__('* Rate it %s on WordPress.org', 'mappress'), $rate_link) . "</li>";
        echo "<li>" . __('* Make a donation') . "<br/>";
        echo "<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
            <input type='hidden' name='cmd' value='_s-xclick' />
            <input type='hidden' name='hosted_button_id' value='4339298' />
            <input type='image' src='https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!' />
            <img alt='' border='0' src='https://www.paypal.com/en_US/i/scr/pixel.gif' width='1' height='1' />
            </form>";
        echo "</li>";
        echo "<li>" . __('Thanks for your support!', 'mappress') . "</li>";
        echo "</ul>";
    }

    /**
    * Output a metabox for a settings section
    *
    * @param mixed $object - required by WP, but ignored, always null
    * @param mixed $metabox - arguments for the metabox
    */
    function metabox_settings($object, $metabox) {
        global $wp_settings_fields;

        $page = $metabox['args']['page'];
        $section = $metabox['args']['section'];

        call_user_func($section['callback'], $section);
        if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
            return;

        echo '<table class="form-table">';
        do_settings_fields($page, $section['id']);
        echo '</table>';
    }

    // Defer this until 'click to display map' is implemented - too slow right now
    function metabox_demo($object, $metabox) {                           
        $poi = new Mappress_Poi(array("title" => sprintf("<a href='http://www.wphostreviews.com/mappress'>%s</a>", __("MapPress", 'mappress')), "body" => "", "address" => "California"));
        $poi->geocode();
        $pois = array($poi);
        
        $map = new Mappress_Map(array("width" => "100%", "height" => 300, "pois" => $pois));

        // Display the map
        // Note that the alignment options "left", "center", etc. cause the map to not display properly in the metabox, so force it off        
        echo $map->display(array("alignment" => "default"));
    }

    /**
    * Replacement for standard WP do_settings_sections() function.
    * This version creates a metabox for each settings section instead of just outputting the section to the screen
    *
    */
    function do_settings_sections($page) {
        global $wp_settings_sections, $wp_settings_fields;

        if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
            return;

        // Add a metabox for each settings section
        foreach ( (array) $wp_settings_sections[$page] as $section ) {
            add_meta_box('metabox_' . $section['id'], $section['title'], array(&$this, 'metabox_settings'), 'mappress', 'normal', 'high', array('page' => 'mappress', 'section' => $section));
        }

        // Display all the registered metaboxes
        do_meta_boxes('mappress', 'normal', null);
    }

    /**
    * Options page
    *
    */
    function options_page() { 
        global $mappress;       
        ?>
        <div class="wrap">

            <h2>
                <a target='_blank' href='http://wphostreviews.com/mappress'><img alt='MapPress' title='MapPress' src='<?php echo plugins_url('images/mappress_logo_med.png', __FILE__);?>'></a>
                <span style='font-size: 12px'>
                    <?php echo $mappress->get_version(); ?>
                    | <a target='_blank' href='http://wphostreviews.com/mappress/mappress-documentation'><?php _e('Documentation', 'mappress')?></a>
                    | <a target='_blank' href='http://wphostreviews.com/mappress/chris-contact'><?php _e('Report a bug', 'mappress')?></a>
                </span>
            </h2>

            <div id="poststuff" class="metabox-holder has-right-sidebar">
                <div id="side-info-column" class="inner-sidebar">
                    <?php
                        // Output sidebar metaboxes                        
                        if (!class_exists('Mappress_Pro'))
                            add_meta_box('metabox_like', __('Like this plugin?', 'mappress'), array(&$this, 'metabox_like'), 'mappress_sidebar', 'side', 'core');
                        
                        add_meta_box('metabox_rss', __('MapPress News', 'mappress'), array(&$this, 'metabox_rss'), 'mappress_sidebar', 'side', 'core');
                        
                        if ($this->options->demoMap)
                            add_meta_box('metabox_demo', __('Sample Map', 'mappress'), array(&$this, 'metabox_demo'), 'mappress_sidebar', 'side', 'core');
                            
                        do_meta_boxes('mappress_sidebar', 'side', null);
                    ?>
                </div>

                <div id="post-body">
                    <div id="post-body-content" class="has-sidebar-content">
                        <form action="options.php" method="post">
                            <?php
                                // Nonces needed to remember metabox open/closed settings
                                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

                                // Output the option settings as metaboxes
                                settings_fields('mappress');
                                $this->do_settings_sections('mappress');
                            ?>
                            <br/>

                            <input name='submit' type='submit' class='button-primary' value='<?php _e("Save Changes", 'mappress'); ?>' />
                            <input name='reset_defaults' type='submit' class='button' value='<?php _e("Reset Defaults", 'mappress'); ?>' />
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script type='text/javascript'>
            jQuery(document).ready( function() {
                // Initialize metaboxes
                postboxes.add_postbox_toggles('mappress');
            });
        </script>
        <?php
    }
} // End class Mappress_Options_Menu
?>