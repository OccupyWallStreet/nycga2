<?php
/**
 * Define translate domain
 */
if (!defined('CC_TRANSLATE_DOMAIN')) {
    define('CC_TRANSLATE_DOMAIN', 'cc');
}
require_once( dirname(__FILE__) . '/admin/cheezcap.php');
require_once( dirname(__FILE__) . '/core/loader.php');

/** Tell WordPress to run cc_setup() when the 'after_setup_theme' hook is run. */
add_action('after_setup_theme', 'cc_setup');
if (!function_exists('cc_setup')):

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * To override cc_setup() in a child theme, add your own cc_setup to your child theme's
     * functions.php file.
     *
     * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
     * @uses register_nav_menus() To add support for navigation menus.
     * @uses add_custom_background() To add support for a custom background.
     * @uses add_editor_style() To style the visual editor.
     * @uses load_theme_textdomain() For translation/localization support.
     * @uses add_custom_image_header() To add support for a custom header.
     * @uses register_default_headers() To register the default custom header images provided with the theme.
     * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
     * @uses $content_width To set a content width according to the sidebars.
     * @uses BP_DISABLE_ADMIN_BAR To disable the admin bar if set to disabled in the themesettings.
     *
     */
    function cc_setup() {
        global $cap, $content_width;

        // This theme styles the visual editor with editor-style.css to match the theme style.
        add_editor_style();

        // This theme uses post thumbnails
        if (function_exists('add_theme_support')) {
            add_theme_support('post-thumbnails');
            set_post_thumbnail_size(222, 160, true);
            add_image_size('slider-top-large', 1006, 250, true);
            add_image_size('slider-large', 990, 250, true);
            add_image_size('slider-middle', 756, 250, true);
            add_image_size('slider-thumbnail', 80, 50, true);
            add_image_size('post-thumbnails', 222, 160, true);
            add_image_size('single-post-thumbnail', 598, 372, true);
        }

        // Add default posts and comments RSS feed links to head
        add_theme_support('automatic-feed-links');

        // Make theme available for translation
        // Translations can be filed in the /languages/ directory
        load_theme_textdomain('cc', get_template_directory() . '/languages');

        $locale = get_locale();
        $locale_file = get_template_directory() . "/languages/$locale.php";
        if (is_readable($locale_file))
            require_once( $locale_file );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'menu_top' => __('Header top menu', 'cc'),
            'primary' => __('Header bottom menu', 'cc'),
        ));

        // This theme allows users to set a custom background
        if ($cap->add_custom_background == true) {
            add_theme_support('custom-background');
        }
        // Your changeable header business starts here
        define('HEADER_TEXTCOLOR', '888888');

        // No CSS, just an IMG call. The %s is a placeholder for the theme template directory URI.
        define('HEADER_IMAGE', '%s/images/default-header.png');

        // The height and width of your custom header. You can hook into the theme's own filters to change these values.
        // Add a filter to cc_header_image_width and cc_header_image_height to change these values.
        define('HEADER_IMAGE_WIDTH', apply_filters('cc_header_image_width', 1000));
        define('HEADER_IMAGE_HEIGHT', apply_filters('cc_header_image_height', 233));


        // Add a way for the custom header to be styled in the admin panel that controls
        // custom headers. See cc_admin_header_style(), below.
        if ($cap->add_custom_image_header == true) {
            $defaults = array(
                /* 'default-image'          => '',
                  'random-default'         => false,
                  'width'                  => 0,
                  'height'                 => 0,
                  'flex-height'            => false,
                  'flex-width'             => false,
                  'default-text-color'     => '',
                  'header-text'            => true,
                  'uploads'                => true, */
//            'wp-head-callback'       => 'cc_admin_header_style',
//            'admin-head-callback'    => 'cc_header_style',
                'admin-preview-callback' => 'cc_admin_header_image',
            );
            add_theme_support('custom-header', $defaults);
            //add_custom_image_header( 'cc_header_style', 'cc_admin_header_style', 'cc_admin_header_image' );
        }

        // Define Content with
        $content_width = "670";
        if ($cap->sidebar_position == "left and right") {
            $content_width = "432";
        }

        // Define disable the admin bar
        if ($cap->bp_login_bar_top == 'off' || $cap->bp_login_bar_top == __('off', 'cc')) {
            define('BP_DISABLE_ADMIN_BAR', true);
        }
    }

endif;

if (!function_exists('cc_admin_header_image')) :

    /**
     * Custom header image markup displayed on the Appearance > Header admin panel.
     *
     * Referenced via add_custom_image_header() in cc_setup().
     *
     */
    function cc_admin_header_image() {
        ?>
        <div id="headimg">
            <?php
            if ('blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || '' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR))
                $style = ' style="display:none;"';
            else
                $style = ' style="color:#' . get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) . ';"';
            ?>
            <h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo home_url('/'); ?>"><?php bloginfo('name'); ?></a></h1>
            <div id="desc"<?php echo $style; ?>><?php bloginfo('description'); ?></div>
            <img src="<?php esc_url(header_image()); ?>" alt="" />
        </div>
    <?php
    }

endif;

add_filter('widget_text', 'do_shortcode');
add_action('widgets_init', 'cc_widgets_init');

function cc_widgets_init() {
    register_sidebars(1, array(
        'name' => 'sidebar right',
        'id' => 'sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'sidebar left',
        'id' => 'leftsidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    ### Add Sidebars
    register_sidebars(1, array(
        'name' => 'header full width',
        'id' => 'headerfullwidth',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'header left',
        'id' => 'headerleft',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'header center',
        'id' => 'headercenter',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'header right',
        'id' => 'headerright',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'footer full width',
        'id' => 'footerfullwidth',
        'before_widget' => '<div id="%1$s" class="span12">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'footer left',
        'id' => 'footerleft',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'footer center',
        'id' => 'footercenter',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'footer right',
        'id' => 'footerright',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'member header',
        'id' => 'memberheader',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'member header left',
        'id' => 'memberheaderleft',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'member header center',
        'id' => 'memberheadercenter',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'member header right',
        'id' => 'memberheaderright',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'member sidebar left',
        'id' => 'membersidebarleft',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'member sidebar right',
        'id' => 'membersidebarright',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'group header',
        'id' => 'groupheader',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'group header left',
        'id' => 'groupheaderleft',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'group header center',
        'id' => 'groupheadercenter',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'group header right',
        'id' => 'groupheaderright',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'group sidebar left',
        'id' => 'groupsidebarleft',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(1, array(
        'name' => 'group sidebar right',
        'id' => 'groupsidebarright',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
    register_sidebars(15, array(
        'name' => 'shortcode %1$s',
        'id' => 'shortcode',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div><div class="clear"></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
            )
    );
}




if ($cap->buddydev_search == true && defined('BP_VERSION') && function_exists('bp_is_active')) {

    //* Add these code to your functions.php to allow Single Search page for all buddypress components*/
    //    Remove Buddypress search drowpdown for selecting members etc
    add_filter("bp_search_form_type_select", "cc_remove_search_dropdown");

    function cc_remove_search_dropdown($select_html) {
        return '';
    }

    remove_action('init', 'bp_core_action_search_site', 5); //force buddypress to not process the search/redirect
    add_action('init', 'cc_bp_buddydev_search', 10); // custom handler for the search

    function cc_bp_buddydev_search() {
        global $bp;
        if ($bp->current_component == BP_SEARCH_SLUG)//if thids is search page
            bp_core_load_template(apply_filters('bp_core_template_search_template', 'search-single')); //load the single searh template
    }

    add_action("advance-search", "cc_show_search_results", 1); //highest priority

    /* we just need to filter the query and change search_term=The search text */

    function cc_show_search_results() {
        //filter the ajaxquerystring
        add_filter("bp_ajax_querystring", "cc_global_search_qs", 100, 2);
    }

    //show the search results for member*/
    function cc_show_member_search() {
        ?>
        <div class="memberss-search-result search-result">
            <h2 class="content-title"><?php _e("Members Results", "cc"); ?></h2>
        <?php locate_template(array('members/members-loop.php'), true); ?>
        <?php global $members_template;
        if ($members_template->total_member_count > 1 && !empty($_REQUEST['search-terms'])):
            ?>
                <a href="<?php echo bp_get_root_domain() . '/' . BP_MEMBERS_SLUG . '/?s=' . $_REQUEST['search-terms'] ?>" ><?php echo sprintf(__("View all %d matched Members", 'cc'), $members_template->total_member_count); ?></a>
        <?php endif; ?>
        </div>
        <?php
    }

    //Hook Member results to search page
    add_action("advance-search", "cc_show_member_search", 10); //the priority defines where in page this result will show up(the order of member search in other searchs)

    function cc_show_groups_search() {
        ?>
        <div class="groups-search-result search-result">
            <h2 class="content-title"><?php _e("Group Search", "cc"); ?></h2>
            <?php locate_template(array('groups/groups-loop.php'), true); ?>
        <?php if (!empty($_REQUEST['search-terms'])): ?>
                <a href="<?php echo bp_get_root_domain() . '/' . BP_GROUPS_SLUG . '/?s=' . $_REQUEST['search-terms'] ?>" ><?php _e("View All matched Groups", "cc"); ?></a>
        <?php endif; ?>
        </div>
        <?php
        //endif;
    }

    //Hook Groups results to search page
    if (bp_is_active('groups'))
        add_action("advance-search", "cc_show_groups_search", 10);

    /**
     *
     * Show blog posts in search
     */
    function cc_show_site_blog_search() {
        ?>
        <div class="blog-search-result search-result">

            <h2 class="content-title"><?php _e("Blog Search", "cc"); ?></h2>

        <?php locate_template(array('search-loop.php'), true); ?>
        <?php if (!empty($_REQUEST['search-terms'])): ?>
                <a href="<?php echo bp_get_root_domain() . '/?s=' . $_REQUEST['search-terms'] ?>" ><?php _e("View All matched Posts", "cc"); ?></a>
        <?php endif; ?>
        </div>
        <?php
    }

    //Hook Blog Post results to search page
    add_action("advance-search", "cc_show_site_blog_search", 10);

    //show forums search
    function cc_show_forums_search() {
        ?>
        <div class="forums-search-result search-result">
            <h2 class="content-title"><?php _e("Forums Search", "cc"); ?></h2>
        <?php locate_template(array('forums/forums-loop.php'), true); ?>
        <?php if (!empty($_REQUEST['search-terms'])): ?>
                <a href="<?php echo bp_get_root_domain() . '/' . BP_FORUMS_SLUG . '/?s=' . $_REQUEST['search-terms'] ?>" ><?php _e("View All matched forum posts", "cc"); ?></a>
        <?php endif; ?>
        </div>
        <?php
    }

    //Hook Forums results to search page
    if (bp_is_active('forums') && bp_is_active('groups') && ( function_exists('bp_forums_is_installed_correctly')))
        add_action("advance-search", "cc_show_forums_search", 20);

    //show blogs search result

    function cc_show_blogs_search() {

        if (!is_multisite())
            return;
        ?>
        <div class="blogs-search-result search-result">
            <h2 class="content-title"><?php _e("Blogs Search", "cc"); ?></h2>
        <?php locate_template(array('blogs/blogs-loop.php'), true); ?>
        <?php if (!empty($_REQUEST['search-terms'])): ?>
                <a href="<?php echo bp_get_root_domain() . '/' . BP_BLOGS_SLUG . '/?s=' . $_REQUEST['search-terms'] ?>" ><?php _e("View All matched Blogs", "cc"); ?></a>
        <?php endif; ?>
        </div>
        <?php
    }

    //Hook Blogs results to search page if blogs comonent is active
    if (bp_is_active('blogs'))
        add_action("advance-search", "cc_show_blogs_search", 10);

    //modify the query string with the search term
    function cc_global_search_qs() {
        if (empty($_REQUEST['search-terms']))
            return;

        return "search_terms=" . $_REQUEST['search-terms'];
    }

    function cc_is_advance_search() {
        global $bp;
        if ($bp->current_component == BP_SEARCH_SLUG)
            return true;
        return false;
    }

    remove_action('bp_init', 'bp_core_action_search_site', 7);
}

//load current displaymode template - loop-list.php or loop-grid.php
function cc_get_displaymode($object) {
    $_BP_COOKIE = &$_COOKIE;
    if (isset($_BP_COOKIE['bp-' . $object . '-displaymode'])) {
        get_template_part("{$object}/{$object}-loop", $_BP_COOKIE['bp-' . $object . '-displaymode']);
    } else {
        get_template_part("{$object}/{$object}-loop", 'list');
    }
}

//check if displaymode grid
function cc_is_displaymode_grid($object) {
    $_BP_COOKIE = &$_COOKIE;
    return ( isset($_BP_COOKIE['bp-' . $object . '-displaymode']) && $_BP_COOKIE['bp-' . $object . '-displaymode'] == 'grid');
}

/**
 * Get pro version
 */
function cc_get_pro_version() {
    $pro_enabler = get_template_directory() . DIRECTORY_SEPARATOR . '_pro' . DIRECTORY_SEPARATOR . 'pro-enabler.php';
    if (file_exists($pro_enabler)) {
        require_once $pro_enabler;
    }
}

/**
 * Fix ...[]
 */
function cc_replace_read_more($text) {
    return ' <a class="read-more-link" href="' . get_permalink() . '"><br />' . __("read more...", "cc") . '</a>';
}

add_filter('excerpt_more', 'cc_replace_read_more');

/**
 * Display the rate for us message
 */
function cc_add_rate_us_notice() {
    $hide_message = get_option('cc_hide_activation_message', false);
    if (!$hide_message) {
        echo '<div class="update-nag cc-rate-it">
                ' . cc_get_add_rate_us_message() . '<a href="#" class="dismiss-activation-message">' . __('Dismiss', 'cc') . '</a>
            </div>';
    }
}

function cc_get_add_rate_us_message() {
    return 'Please rate for <a class="go-to-wordpress-repo" href="http://wordpress.org/extend/themes/custom-community" target="_blank">Custom Community</a> theme on WordPress.org';
}

/**
 * Ajax processor for show/hide Please rate for
 */
add_action('wp_ajax_dismiss_activation_message', 'cc_dismiss_activation_message');

function cc_dismiss_activation_message() {
    echo update_option('cc_hide_activation_message', $_POST['value']);
    die();
}

/**
 * Ajax processor for show/hide Please info for
 */
add_action('wp_ajax_cc_dismiss_info_messages', 'cc_dismiss_info_messages');

function cc_dismiss_info_messages() {
    echo update_option($_POST['action'], $_POST['value']);
    die();
}

/**
 * Add css
 */
function cc_add_styles() {
    global $cap;
    if ($cap->cc_responsive_enable) {
        wp_enqueue_style('bootstrap', get_template_directory_uri() . '/_inc/css/bootstrap-responsive.css');
        wp_enqueue_style('custom', get_template_directory_uri() . '/_inc/css/custom-responsive.css');
    }
}

add_action('wp_head', 'cc_add_styles', 10);

/**
 * Add class span%2 for menu items
 * @param string $items
 * @param array $args
 * @return string items with new class
 */
function cc_add_spanclass($items, $args) {

    $items = explode('</li>', $items);
    $newitems = array();
    // loop through the menu items, and add the new link at the right position
    foreach ($items as $item) {
        $newitems[] = str_replace('class="', 'class="span2 ', $item);
    }
    // finally put all the menu items back together into a string using the ending <li> tag and return
    $newitems = implode('</li>', $newitems);

    return $newitems;
}

//add_filter('wp_list_pages', 'cc_add_spanclass', 10, 2);
//add_filter('wp_nav_menu_items', 'cc_add_spanclass', 10, 2);

/**
 * Slider functions, used in slideshow parts
 * @global object $post post object
 * @global type $cc_js
 * @global type $cap
 * @global type $post
 * @param type $atts
 * @param type $content
 * @return type
 */
function slider($atts, $content = null) {
    global $post, $cc_js, $cap;
    extract(shortcode_atts(array(
                'amount' => '4',
                'category__in' => array(),
                'category_name' => '',
                'page_id' => '',
                'post_type' => 'post',
                'orderby' => 'DESC',
                'slider_nav' => 'on',
                'caption' => 'on',
                'caption_height' => '',
                'caption_top' => '',
                'caption_width' => '',
                'reflect' => '',
                'width' => '',
                'height' => '',
                'id' => '',
                'background' => '',
                'slider_nav_color' => '',
                'slider_nav_hover_color' => '',
                'slider_nav_selected_color' => '',
                'slider_nav_font_color' => '',
                'time_in_ms' => '5000',
                'allow_direct_link' => __('no', 'cc')
                    ), $atts));
    if($category_name == 'all-categories'){
        $category_name = '0';
    }
    
    if (empty($category__in)) {
        $category__in = array();
        $categories = get_categories();
        foreach ($categories as $category) {
            $category__in[] = $category->term_id;
        }
    } else if (!is_array($category__in)) {
        $category__in = explode(',', $category__in);
    }
    

    if ($page_id != '' && $post_type == 'post') {
        $post_type = 'page';
    }

    if ($page_id != '') {
        $page_id = explode(',', $page_id);
    }

    $tmp = chr(13);

    $tmp .= '<style type="text/css">' . chr(13);
    $tmp .= 'div.post img {' . chr(13);
    $tmp .= 'margin: 0 0 1px 0;' . chr(13);
    $tmp .= '}' . chr(13);

    if ($slider_nav == 'off') {
        $tmp .= '#featured' . $id . ' ul.ui-tabs-nav {
                    visibility: hidden;
                }
                #featured' . $id . ' { 
                    background: none;
                    padding:0
                }';
    }

    if ($width != "") {
        $tmp .= '#featured' . $id . ' ul.ui-tabs-nav {' . chr(13);
        $tmp .= 'left:' . $width . 'px;' . chr(13);
        $tmp .= '}' . chr(13);
    }
    
    if ($caption_height != "") {
        $tmp .= '#featured' . $id . ' .ui-tabs-panel .info{' . chr(13);
        $tmp .= 'height:' . $caption_height . 'px;' . chr(13);
        $tmp .= '}' . chr(13);
    }

    if ($caption_width != "") {
        $tmp .= '#featured' . $id . ' .ui-tabs-panel .info{' . chr(13);
        $tmp .= 'width:' . $caption_width . 'px;' . chr(13);
        $tmp .= '}' . chr(13);
    }

    if ($caption_top != "") {
        $tmp .= '#featured' . $id . ' .ui-tabs-panel .info{' . chr(13);
        $tmp .= 'top:' . $caption_top . 'px;' . chr(13);
        $tmp .= '}' . chr(13);
    }

    if ($background != '') {
        $tmp .= '#featured' . $id . '{' . chr(13);
        $tmp .= 'background: #' . $background . ';' . chr(13);
        $tmp .= '}' . chr(13);
    }

    if ($width != '' || $height != '' || $slider_nav == 'off') {
        $tmp .= '#featured' . $id . '{' . chr(13);
        $tmp .= 'width:' . $width . 'px;' . chr(13);
        $tmp .= 'height:' . $height . 'px;' . chr(13);
        $tmp .= '}' . chr(13);
        $tmp .= '#featured' . $id . ' .ui-tabs-panel{' . chr(13);
        $tmp .= 'width:' . $width . 'px; height:' . $height . 'px;' . chr(13);
        $tmp .= 'background:none; position:relative;' . chr(13);
        $tmp .= '}' . chr(13);
    }

    if ($slider_nav_color != '') {
        $tmp .= '#featured' . $id . ' li.ui-tabs-nav-item a{' . chr(13);
        $tmp .= '    background: none repeat scroll 0 0 #' . $slider_nav_color . ';' . chr(13);
        $tmp .= '}' . chr(13);
    }
    if ($slider_nav_hover_color != '') {
        $tmp .= '#featured' . $id . ' li.ui-tabs-nav-item a:hover{' . chr(13);
        $tmp .= '    background: none repeat scroll 0 0 #' . $slider_nav_hover_color . ';' . chr(13);
        $tmp .= '}' . chr(13);
    }

    if ($slider_nav_selected_color != '') {
        $tmp .= '#featured' . $id . ' .ui-tabs-selected {' . chr(13);
        $tmp .= 'padding-left:0;' . chr(13);
        $tmp .= '}' . chr(13);
        $tmp .= '#featured' . $id . ' .ui-tabs-selected a{' . chr(13);
        $tmp .= '    background: none repeat scroll 0 0 #' . $slider_nav_selected_color . ' !important;' . chr(13);
        $tmp .= 'padding-left:0;' . chr(13);
        $tmp .= '}' . chr(13);
    }

    if ($slider_nav_font_color != '') {
        $tmp .= '#featured' . $id . ' ul.ui-tabs-nav li span{' . chr(13);
        $tmp .= 'color:#' . $slider_nav_font_color . chr(13);
        $tmp .= '}' . chr(13);
    }
    $tmp .= '</style>' . chr(13);

    $args = array(
        'orderby' => $orderby,
        'post_type' => $post_type,
        'post__in' => $page_id,
        'category__in' => $category__in,
        'category_name'  => $category_name,
        'posts_per_page' => $amount
    );

    remove_all_filters('posts_orderby');
    query_posts($args);
    if (have_posts()) {
        $shortcodeclass = '';
        if ($id == "top")
            $shortcodeclass = "cc_slider_shortcode";

        $tmp .='<div id="cc_slider' . $id . '" class="cc_slider hidden-phone span12' . $shortcodeclass . '">' . chr(13);
        $tmp .='<div id="featured' . $id . '" class="featured">' . chr(13);

        $i = 1;
        $slider_class = $slider_nav == 'off' ? 'span12' : 'span8';
        while (have_posts()) : the_post();
            global $post;
            $url = get_permalink();
            $theme_fields = get_post_custom_values('my_url');
            if (isset($theme_fields[0])) {
                $url = $theme_fields[0];
            }
            $tmp .='<div id="fragment-' . $id . '-' . $i . '" class="ui-tabs-panel '.$slider_class.'">' . chr(13);

            if ($width != '' || $height != '') {
                $ftrdimg = get_the_post_thumbnail($post->ID, array($width + 10, $height), "class={$reflect}");
                if (empty($ftrdimg)) {
                    if ($cap->slideshow_img) {
                        $ftrdimg = '<img src="' . $cap->slideshow_img . '" />';
                    } else {
                        $ftrdimg = '<img src="' . get_template_directory_uri() . '/images/slideshow/noftrdimg-1006x250.jpg" />';
                    }
                }
            } else {
                $ftrdimg = get_the_post_thumbnail($post->ID, array(756, 250), "");
                if (empty($ftrdimg)) {
                    if ($cap->slideshow_img) {
                        $ftrdimg = '<img src="' . $cap->slideshow_img . '" width="756" height="250"/>';
                    } else {
                        $ftrdimg = '<img src="' . get_template_directory_uri() . '/images/slideshow/noftrdimg.jpg" />';
                    }
                }
            }

            $tmp .='    <a class="reflect" href="' . $url . '">' . $ftrdimg . '</a>' . chr(13);

            if ($caption == 'on') {
                $tmp .=' <div class="info span8" >' . chr(13);
                $tmp .='    <h2><a href="' . $url . '" >' . get_the_title() . '</a></h2>' . chr(13);
                $tmp .='    <p>' . get_the_excerpt() . '</p>' . chr(13);
                $tmp .=' </div>' . chr(13);
            }
            $tmp .='</div>' . chr(13);
            $i++;
        endwhile;

        $tmp .='<ul class="ui-tabs-nav span4 offset1">' . chr(13);
        $i = 1;
        while (have_posts()) : the_post();
            if (get_the_post_thumbnail($post->ID, 'slider-thumbnail') == '') {
                if (!empty($cap->slideshow_small_img) || $cap->slideshow_small_img != '') {
                    $ftrdimgs = '<img src="' . $cap->slideshow_small_img . '" width="80" height="50"/>';
                } else {
                    $ftrdimgs = '<img src="' . get_template_directory_uri() . '/images/slideshow/noftrdimg-80x50.jpg" />';
                }
            } else {
                $ftrdimgs = get_the_post_thumbnail($post->ID, 'slider-thumbnail');
            }
            if ($allow_direct_link == __('yes', 'cc')) {
                $ftrdimgs = '<a href="#fragment-' . $id . '-' . $i . '" class="allow-dirrect-links" data-url="' . get_permalink($post->ID) . '">' . $ftrdimgs . '<span>' . get_the_title() . '</span></a>';
            } else {
                $ftrdimgs = '<a href="#fragment-' . $id . '-' . $i . '">' . $ftrdimgs . '<span>' . get_the_title() . '</span></a>';
            }
            $tmp .='<li class="ui-tabs-nav-item ui-tabs-selected" id="nav-fragment-' . $id . '-' . $i . '">' . $ftrdimgs . '</li>' . chr(13);
            $i++;
        endwhile;
        $tmp .='</ul>' . chr(13);

        $tmp .= '</div></div>' . chr(13);
    } else {
        $tmp .='<div id="cc_slider_prev" class="cc_slider">' . chr(13);
        $tmp .='<div id="featured_prev" class="featured">' . chr(13);
        $tmp .='<h2 class="center">' . __('Empty Slideshow', 'cc') . '</h2>' . chr(13);
        $tmp .='<p class="center">' . __('Something went wrong here. Some help: <br>Check your theme settings and write a post with an featured image! <br> Have a look how to setup your <a href="http://support.themekraft.com/entries/21647926-slideshow" target="_blank">Slideshow</a> or check out our <a href="http://themekraft/support" target="_blank">Support</a> if you still get stuck.', 'cc') . '</p>' . chr(13);
        $tmp .='</div></div>' . chr(13);
    }
    wp_reset_query();

    // js vars
    $cc_js['slideshow'][] = array(
        'id' => $id,
        'time_in_ms' => $time_in_ms
    );

    return $tmp . chr(13);
}

/**
 * Get class by sidebar position settings in Themes Options
 */
function cc_get_class_by_sidebar_position() {
    global $cap, $post;
    $class = '';
    $tmp = get_post_meta($post->ID, '_wp_page_template', true);

    switch ($cap->sidebar_position) {
        case "left and right": $class = 'left-right-sidebar';
            break;
        case 'full-width' : $class = 'full-width';
            break;
    };
    switch ($tmp) {
        case '_pro/tpl-search-right-and-left-sidebar.php': $class .= ' left-right-template';
            break;
        case 'tpl-search-full-width.php': $class .= ' full-search-width';
            break;
    }
    switch ($cap->archive_template) {
        case __("left and right", 'cc'): $class .= ' archive-width';
    }
    return $class;
}

/**
 * Add info before tabs in Theme Options
 */
function cc_add_settins_info($tab_id) {
    if ('cap_slideshow' == $tab_id) {
        $show = get_option('cc_dismiss_info_messages', FALSE);
        if (empty($show)) {
            _e('<p class="slideshow_info">
                    <button type="button" class="close" data-dismiss="alert">x</button>
                    Slideshow settings of the single pages are stronger and will overwrite the global slideshow settings
                </p>', CC_TRANSLATE_DOMAIN);
        }
    }
}

add_action('cc_before_settings_tab', 'cc_add_settins_info');

/**
 * Add rotate function to jquery iu 1.9 
 */
function cc_add_rotate_tabs() {
    wp_enqueue_script('cc_rotate', get_template_directory_uri() . '/_inc/js/jquery-ui-tabs-rotate.js', array('jquery', 'jquery-ui-tabs'));
}

add_action('wp_enqueue_scripts', 'cc_add_rotate_tabs');

/**
 * Enqueue theme javascript safely for admin console
 *
 * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
 * @since 1.9.1
 */
function admin_dtheme_enqueue_scripts() {
    global $cap;
    $cap = new autoconfig();
    //add for imadiatly view settings after save options
    $responsive = !empty($_POST) && !empty($_POST['custom_community_theme_options']) ?
            $_POST['custom_community_theme_options']['cap_cc_responsive_enable'] == __('Enabled', 'cc') ? 1 : 0  : $cap->cc_responsive_enable;

    // Enqueue the global JS - Ajax will not work without it
    wp_register_script( 'autogrow-textarea', get_template_directory_uri()."/admin/js/jquery.autogrow-textarea.js", array(), true );
    wp_enqueue_script('cc-theme-admin-js', get_template_directory_uri() . '/_inc/js/admin.js', array('jquery', 'autogrow-textarea'));
    wp_localize_script('cc-theme-admin-js', 'admin_params', array(
        'ajax_url' => site_url('/wp-admin/admin-ajax.php'),
        'blog' => __('blog', 'cc'),
        'flux_slider' => __('flux slider', 'cc'),
        'default_slider' => __('default', 'cc'),
        'responsive' => $responsive
        )
    );

}

add_action('admin_enqueue_scripts', 'admin_dtheme_enqueue_scripts');