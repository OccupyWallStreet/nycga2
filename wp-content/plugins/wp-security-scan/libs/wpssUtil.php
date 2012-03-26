<?php
/**
 * utility methods
 *
 * @author kos
 */
class wpssUtil
{
    //@since v3.0.8
    private static $_pluginID = 'acx_plugin_dashboard_widget';
    /**
     * @public
     * @static
     * @global WPSS_WSD_BLOG_FEED
     * Retrieve and display a list of links for an existing RSS feed, limiting the selection to the 5 most recent items.
	 * @return void
     */
    public static function displayDashboardWidget()
    {
        //@since v3.0.8
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $opt = get_option('WSD-RSS-WGT-DISPLAY');
            if (empty($opt)) {
                add_option('WSD-RSS-WGT-DISPLAY', 'no');
            }
            else {
                update_option('WSD-RSS-WGT-DISPLAY', 'no');
            }
            self::_hideDashboardWidget();
            return;
        }

        //@ flag
        $run = false;
        
        //@ check cache
        $optData = get_option('wsd_feed_data');
        if (! empty($optData))
        {
            if (is_object($optData))
            {

                $lastUpdateTime = @$optData->expires;
                // invalid cache
                if (empty($lastUpdateTime)) { $run = true; }
                else
                {
                    $nextUpdateTime = $lastUpdateTime+(24*60*60);
                    if ($nextUpdateTime >= $lastUpdateTime)
                    {
                        $data = @$optData->data;
                        if (empty($data)) { $run = true; }
                        else {
                            // still a valid cache
                            echo $data;
                            return;
                        }
                    }
                    else { $run = true; }
                }
            }
            else { $run = true; }
        }
        else { $run = true; }

        if (!$run) { return; }
        
        $rss = fetch_feed(WPSS_WSD_BLOG_FEED);

        $out = '';
        if (is_wp_error( $rss ) )
        {
            $out = '<li>'.__('An error has occurred while trying to load the rss feed!').'</li>';
            echo $out;
            return;
        }
        else
        {
            // Limit to 5 entries. 
            $maxitems = $rss->get_item_quantity(5); 

            // Build an array of all the items,
            $rss_items = $rss->get_items(0, $maxitems); 

            $out .= '<ul>';
                if ($maxitems == 0)
                {
                    $out.= '<li>'.__('There are no entries for this rss feed!').'</li>';
                }
                else
                {
                    foreach ( $rss_items as $item ) :
                        $url = esc_url($item->get_permalink());
                        $out.= '<li>';
                            $out.= '<h4><a href="'.$url.'" target="_blank" title="Posted on '.$item->get_date('F j, Y | g:i a').'">';
                                $out.= esc_html( $item->get_title() );
                            $out.= '</a></h4>';
                            $out.= '<p>';
                                    $d = $item->get_description();
                                    $p = substr($d, 0, 115).' <a href="'.$url.'" target="_blank" title="Read all article">[...]</a>';
                                $out.= $p;
                            $out.= '</p>';
                        $out.= '</li>';
                    endforeach;
                }
            $out.= '</ul>';
            
            $path = trailingslashit(get_option('siteurl')).'wp-content/plugins/wp-security-scan/';

            $out .= '<div style="border-top: solid 1px #ccc; margin-top: 4px; padding: 2px 0;">';
                $out .= '<p style="margin: 5px 0 0 0; padding: 0 0; line-height: normal; overflow: hidden;">';
                    $out .= '<a href="http://feeds.feedburner.com/Websitedefendercom"
                                style="float: left; display: block; width: 50%; text-align: right; margin-top: 0; margin-left: 30px;
                                padding-right: 22px; background: url('.$path.'images/rss.png) no-repeat right center;"
                                target="_blank">Follow us on RSS</a>';
                    $out .= '<a href="#" id="wsd_close_rss_widget"
                                style="float: right; display: block; width: 16px; height: 16px;
                                margin: 0 0; background: url('.$path.'images/close-button.png) no-repeat 0 0;"
                                    title="Close widget"></a><form id="wsd_form" method="post"></form>';
                $out .= '</p>';
                $out .= '<script type="text/javascript">
                    document.getElementById("wsd_close_rss_widget").onclick = function(){
                            document.getElementById("wsd_form").submit();
                        };
                </script>';
            $out .= '</div>';
        }
        
        // Update cache
        $obj = new stdClass();
            $obj->expires = time();
            $obj->data = $out;
        update_option('wsd_feed_data', $obj);

        echo $out;
    } 

    /**
     * @public
     * @static
     * Add the rss widget to dashboard
     * @return void
     */
    public static function addDashboardWidget()
    {
        // update 10/04/2011
        $opt = get_option('WSD-RSS-WGT-DISPLAY');
        if(strtolower($opt) == 'yes'):
            wp_add_dashboard_widget(self::$_pluginID,
                                    __('WebsiteDefender news and updates'),
                                    'wpssUtil::displayDashboardWidget');
        endif;
    }
    
    /**
     * Hide the dashboard rss widget
     * @static
     * @public
     * @since v3.0.8
     */
    public static function _hideDashboardWidget()
    {
        echo '<script>document.getElementById("'.self::$_pluginID.'").style.display = "none";</script>';
    }
    
}