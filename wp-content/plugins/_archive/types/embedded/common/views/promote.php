<?php

if (!function_exists('promote_types_and_views')) {

    function is_promote_views() {
        $promote_views = false;
        if (defined('WPV_VERSION')) {
            global $WP_Views;
            $promote_views = $WP_Views->is_embedded();
        }
        return $promote_views;        
    }
    
    function promote_types_and_views() {
        static $promoted = false;
        
        if (!$promoted) {
            $promote_types = defined('WPCF_RUNNING_EMBEDDED');
            $promote_views = is_promote_views();
            
            if ($promote_types || $promote_views) {
                add_action('admin_menu', 'promote_types_and_views_menu');
            }
        }
    }
    
    function promote_types_and_views_menu() {
        $promote_types = defined('WPCF_RUNNING_EMBEDDED');
        $promote_views = is_promote_views();
        if ($promote_types || $promote_views) {
            add_theme_page(__('Get Types and Views', 'wpv-views'), 'Get Types and Views', 'manage_options', 'wpv-get-types-views', 'promote_types_and_views_admin');
        }
    }
    
    function promote_types_and_views_admin() {
        ?>
        <div class="wrap">
        <?php
        
        $promote_types = defined('WPCF_RUNNING_EMBEDDED');
        $promote_views = is_promote_views();
        $affiliate_url = '';
        if (function_exists('wpv_get_affiliate_url')) {
            $affiliate_url = wpv_get_affiliate_url();
        }
        
        $icon_url = icl_get_file_relpath(dirname(__FILE__) . '/res/img/views-32.png') . '/views-32.png';
        ?>
            <div class="icon32" style='background:url("<?php echo $icon_url; ?>") no-repeat;'><br /></div>
            <h2><?php _e('Get Types and Views', 'wpv-views') ?></h2>
            
            <p style="font-size: 130%;"><?php _e('Your theme was created using <strong>Types</strong> and <strong>Views</strong>. Developers use these two plugins to build complex websites, without coding.', 'wpv-views'); ?></p>
            <p style="font-size: 120%;"><?php _e("Right now, you're using the embedded version, which creates the layout but doesn't include the editing interface. You can upgrade to the full version and customize your site yourself - you don't even need to know how to program!", 'wpv-views'); ?></p>

            <p style="font-size: 120%;"><?php echo sprintf(__('<a href="%s" target="_blank">Types</a> is available for free and <a href="%s">Views</a> costs only $49. Once you have installed the full versions of Types and Views you\'ll be able to create and edit your own content types, layouts and listings.', 'wpv-views'),
                                'http://wordpress.org/extend/plugins/types/',
								'http://wp-types.com' . $affiliate_url); ?></p>

            <p style="font-size: 140%; font-weight: bold;	"><?php echo sprintf(__('<a href="%s" target="_blank">Learn more</a>', 'wpv-views'),
                                'http://wp-types.com' . $affiliate_url); ?></p>

			<br /><hr /><br />
			<ol>
			<li><?php _e('Every purchase of Views entitles you to commercial-grade support and upgrades for one year.','wpv-views'); ?></li>
			<li><?php _e('You can use Types and Views for as many themes and websites as you like.','wpv-views'); ?></li>
			</ol>

        <?php
        
        //if ($promote_types && $promote_views) {
        //    
        //    wpv_promote_views_admin();
        //    echo "<hr />\n";
        //    wpcf_promote_types_admin();
        //} else {
        //    if ($promote_types) {
        //        wpcf_promote_types_admin();
        //    } else {
        //        wpv_promote_views_admin();
        //    }
        //}

        ?>
        </div>        
        <?php
        
    }
    

}

