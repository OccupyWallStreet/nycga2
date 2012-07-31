<?php

    define ('VIEWS_UPDATE_URL', 'http://wp-types.com/?views_plugin_info=1');

    $views_plugins = array('WP Views',);


    add_filter('pre_set_site_transient_update_plugins', 'check_for_views_plugin_updates');
    add_filter('plugins_api', 'get_views_plugin_page', 1, 3);
    
    function check_for_views_plugin_updates($value) {
        // called when the update_plugins transient is saved.
        
        global $views_plugins, $WP_Views;
        
        if(empty($views_plugins)) return $value;
        
	    if ( function_exists( 'get_plugins' )) {

            
            $plugins = get_plugins();
            // Filter Views plugins
            foreach ($plugins as $key => $plugin) {
                if (!in_array($plugin['Name'], $views_plugins)) {
                    unset($plugins[$key]);
                }
            }
            
			$options = $WP_Views->get_options();
			
            $request = wp_remote_post(VIEWS_UPDATE_URL, array(
                'timeout' => 15,
                'body' => array(
                    'action' => 'update_information',
                    'subscription_email' => isset($options['subscription_email'])?$options['subscription_email']:false,
                    'subscription_key' => isset($options['subscription_key'])?$options['subscription_key']:false,
                    'plugins' => $plugins,
                    'lc' => get_option('WPLANG'),
                    )));
            // TODO we're not returning anything as WP_Error yet
            if ( is_wp_error($request) ) {
                $res = false;
            } else {
                $res = maybe_unserialize($request['body']);
            }
            
            if ($res !== false) {        
                // check for VIEWS plugins
                foreach ($plugins as $key => $plugin) {
                    if(!empty($res[$key])){
                        $value->response[$key] = $res[$key];
                    } else {
                        if (isset($value->response[$key])) {
                            unset($value->response[$key]);
                        }
                    }
                }
            }
        }
        
        return $value;
    }
    
    function get_views_plugin_page($state, $action, $args) {
        global $wpdb, $WP_Views;
        
        global $views_plugins;

		$options = $WP_Views->get_options();
        
        $res = false;

        if (isset($args->slug) && $args->slug == "views_all" || @in_array(str_replace('_', ' ', $args->slug), $views_plugins)) {

            if (!isset($args->installed)) {
                $args->installed = "";
            }
            $body_array = array('action' => $action,
                                    'request' => serialize($args),
                                    'slug' => $args->slug,
                                    'installed' => $args->installed,
                                    'subscription_email' => isset($options['subscription_email'])?$options['subscription_email']:false,
                                    'subscription_key' => isset($options['subscription_key'])?$options['subscription_key']:false,
                                    'lc' => get_option('WPLANG'),
                                    );
            
            $request = wp_remote_post(VIEWS_UPDATE_URL, array( 'timeout' => 15, 'body' => $body_array) );
            if ( is_wp_error($request) ) {
                $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.', 'wpv-views'), $request->get_error_message() );
            } else {
                $res = maybe_unserialize($request['body']);
                if ( false === $res )
                    $res = new WP_Error('plugins_api_failed', __('An unknown error occurred.', 'sitepress'), $request['body']);
            }
        }
        
        return $res;
    }
?>