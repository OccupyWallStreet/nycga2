<?php
/*
Plugin Name: Facebook Events Widget
Plugin URI: http://roidayan.com
Description: Widget to display facebook events
Version: 1.1.4
Author: Roi Dayan
Author URI: http://roidayan.com
License: GPLv2

Based on code by Mike Dalisay
  http://www.codeofaninja.com/2011/07/display-facebook-events-to-your-website.html


Copyright (C) 2011, 2012  Roi Dayan  (email : roi.dayan@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* TODO
 * setting if to display more info or not
 * link to all events
 * setting for date format for one day event and event that span multiple days
 * force height for widget container
*/

//error_reporting(E_ALL);

// requiring FB PHP SDK
if (!class_exists('Facebook')) {
    require_once('fb-sdk/src/facebook.php');
}

class Facebook_Events_Widget extends WP_Widget {
    var $default_settings = array(
        'title' => '',
        'pageId' => '',
        'appId' => '',
        'appSecret' => '',
        'accessToken' => '',
        'maxEvents' => 10,
        'smallPic' => false,
        'futureEvents' => false,
        'timeOffset' => 7,
        'newWindow' => false,
        'calSeparate' => false
        );

    function Facebook_Events_Widget() {
        // constructor
        $widget_ops = array(
            'classname' => 'widget_Facebook_Events_Widget',
            'description' => __('Display facebook events.')
            );
        $control_ops = array(
            'width' => '',
            'height' => ''
            );
        
        $this->WP_Widget('facebook_events_widget',
            __('Facebook Events Widget'), $widget_ops, $control_ops);
            
        //$this->admin_url = admin_url('admin.php?page=' . urlencode(plugin_basename(__FILE__)));
        $this->admin_url = admin_url('widgets.php');
        
        add_action('init', array($this, 'add_style'));
    }
    
    function add_style() {
        if (!is_admin()) {
            wp_enqueue_style('facebook-events',
                            plugin_dir_url(__FILE__).'style.css',
                            false, '1.0', 'all');
        }
    }

    function widget($args, $instance) {
        // print the widget
        extract($args, EXTR_SKIP);
        $instance = wp_parse_args(
            (array) $instance,
            $this->default_settings
        );
        extract($instance, EXTR_SKIP);
        $title = apply_filters('widget_title', empty($title) ? 'Facebook Events' : $title);
        $all_events_url = "http://www.facebook.com/pages/{$pageId}/?sk=events";

        echo $before_widget;
        if ($title)
            echo $before_title . $title . $after_title;

        $fqlResult = $this->query_fb_page_events($appId, $appSecret, $pageId,
                        $accessToken, $maxEvents, $futureEvents);

        echo '<div class="fb-events-container">';
        
        # looping through retrieved data
        if (!empty($fqlResult)) {
            $last_sep = '';
            foreach ($fqlResult as $keys => $values) {
                // Facebook now may reply time in format as "2012-07-21T12:00:00-400"
                // instead of in seconds
                if (strpos($values['start_time'], "T")) {
                    $values['start_time'] = strtotime($values['start_time']);
                    $values['end_time'] = strtotime($values['end_time']);
                }
                //adjust facebook timestamp offset
                if ($timeOffset != 0) {
                    $o = $timeOffset * 3600;
                    $values['start_time'] -= $o;
                    $values['end_time'] -= $o;
                }
                if ($smallPic)
                    $values['pic'] = $values['pic_small'];
                if ($calSeparate)
                    $last_sep = $this->cal_event($values, $last_sep);
                $this->create_event_div_block($values, $instance);
            }
        }
        echo '</div>';

        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        // save the widget
        $instance = $old_instance;
        foreach ($this->default_settings as $key => $val)
            $instance[$key] = strip_tags(stripslashes($new_instance[$key]));

        return $instance;
    }

    function form($instance) {
        // widget form in backend
        $instance = wp_parse_args(
            (array) $instance,
            $this->default_settings
        );
        extract($instance, EXTR_SKIP);
        $title = htmlspecialchars($instance['title']);

        $this->create_input('title', $title, 'Title:');
        $this->create_input('pageId', $pageId, 'Facebook Page ID:');
        $this->create_input('appId', $appId, 'Facebook App ID:');
        $this->create_input('appSecret', $appSecret, 'Facebook App secret:');
        
        if (!empty($appId) && !empty($appSecret) && empty($accessToken) &&
            isset($_GET['wid']) && isset($_GET['code']) && $_GET['wid'] == $this->id)
        {
            $accessToken = $this->get_facebook_access_token($appId, $appSecret, $_GET['code']);
        }
        
        $this->create_input('accessToken', $accessToken, 'Access token:');
        echo '*Only needed if calendar is private.<br/><br/>';
        
        if (empty($access_token)) {
            echo '<p><a class="button-secondary" ';
            echo 'href="https://www.facebook.com/dialog/oauth?client_id=';
            echo urlencode($appId);
            echo '&redirect_uri=' . urlencode($this->admin_url.'?wid=' . $this->id);
            echo '&scope=' . urlencode('offline_access,user_events') . '">';
            echo __('Get facebook access token') . '</a></p>';
        }
        
        $this->create_input('maxEvents', $maxEvents, 'Maximum Events:', 'number');
        $this->create_input('smallPic', $smallPic, 'Use Small Picture:', 'checkbox');
        $this->create_input('futureEvents', $futureEvents, 'Show Future Events Only:', 'checkbox');
        $this->create_input('timeOffset', $timeOffset, 'Adjust events times in hours:', 'number');
        $this->create_input('newWindow', $newWindow, 'Open events in new window:', 'checkbox');
        $this->create_input('calSeparate', $calSeparate, 'Show calendar separators:', 'checkbox');
        
        echo '*To edit the style you need to edit the style.css file.<br/><br/>';
    }
    
    function get_facebook_access_token($appId, $appSecret, $code) {
        $request = new WP_Http;
        $api_url = 'https://graph.facebook.com/oauth/access_token?client_id='
                . urlencode($appId).'&redirect_uri='
                . urlencode($this->admin_url . '?wid=' . $this->id)
                . '&client_secret=' . urlencode($appSecret)
                . '&code=' . urlencode($code);
        $response = $request->get($api_url);
        if (isset($response->errors))
            return false;
        $json_response = json_decode($response['body']);
        if (is_object($json_response) &&
            property_exists($json_response,'error'))
        {
            echo '<p style="color: red;">Error getting access token.</p>';
            return false;
        }
        $token = explode('=', $response['body'], 2);
        if ($token[0] != 'access_token') {
            echo '<p style="color: red;">Error with access token.</p>';
            return false;
        }
        return $token[1];
    }
    
    function create_input($key, $value, $title, $type='text') {
        $name = $this->get_field_name($key);
        $id = $this->get_field_id($key);
        echo '<p><label for="' . $id . '">' . __($title);
        echo '&nbsp;<input id="' . $id . '" name="' . $name . '" type="' . $type . '"';
        $width = 80;
        if ($type == 'number')
            $width = 35;
        if ($type == 'checkbox') {
            checked( (bool) $value, true);
            $width = 0;
        } else
            echo ' value="' . $value . '"';
        if ($width > 0)
            echo ' style="width: '.$width.'px;"';
        echo ' /></label></p>';
    }

    function query_fb_page_events($appId, $appSecret, $pageId, $accessToken, $maxEvents, $futureOnly=false) {
        //initializing keys
        $facebook = new Facebook(array(
            'appId'  => $appId,
            'secret' => $appSecret,
            'cookie' => true // enable optional cookie support
        ));

        //query the events
        //we will select name, start_time, end_time, location, description this time
        //but there are other data that you can get on the event table (https://developers.facebook.com/docs/reference/fql/event/)
        //as you've noticed, we have TWO select statement here
        //since we can't just do "WHERE creator = your_fan_page_id".
        //only eid is indexable in the event table, sow we have to retrieve
        //list of events by eids
        //and this was achieved by selecting all eid from
        //event_member table where the uid is the id of your fanpage.
        //*yes, you fanpage automatically becomes an event_member
        //once it creates an event

        $future = $futureOnly ? 'AND start_time > now()' : '';
        $maxEvents = intval($maxEvents) <= 0 ? 1 : intval($maxEvents);
        $fql = "SELECT eid, name, pic, pic_small, start_time, end_time, location, description 
            FROM event WHERE eid IN ( SELECT eid FROM event_member 
            WHERE uid = '{$pageId}' ) {$future}
            ORDER BY start_time ASC LIMIT {$maxEvents}";
        
        $param = array (
            'method' => 'fql.query',
            'query' => $fql,
            'callback' => ''
        );
        
        if (!empty($accessToken))
            $param['access_token'] = $accessToken;

        $fqlResult = '';

        try {
            $fqlResult = $facebook->api($param);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        return $fqlResult;
    }

    function cal_event($values, $last_sep = '') {
        $today = false;
        $tomorrow = false;
        $this_week = false;
        $this_month = false;

        if (date('Ymd') == date('Ymd', $values['start_time']))
            $today = true;
            
        if (date('Ymd') == date('Ymd', $values['start_time'] - 86400))
            $tomorrow = true;

        if (date('Ym') == date('Ym', $values['start_time'])) {
            $this_month = true;
            
            if (( date('j', $values['start_time']) - date('j') ) < 7) {
                if (date('w', $values['start_time']) >= date('w') ||
                    date('w', $values['start_time']) == 0)
                {
                    // comparing to 0 because seems its 0-6 where sunday is 1 and saturday is 0
                    // docs says otherwise.. need to check,
                    $this_week = true;
                }
            }
        }
        
        $month = date('F', $values['start_time']);
        
        if ($today) {
            $t = 'Today';
            $r = 'today';
        } else if ($tomorrow) {
            $t = 'Tomorrow';
            $r = 'tomorrow';
        } else if ($this_week) {
            $t = 'This Week';
            $r = 'thisweek';
        } else if ($this_month) {
            $t = 'This Month';
            $r = 'thismonth';
        } else {
            $t = $month;
            $r = $month;
        }
        
        if ($r != $last_sep) {
            echo '<div class="fb-event-cal-head">';
            echo $t;
            echo '</div>';
        }
        
        return $r;
    }
    
    function create_event_div_block($values, $instance) {
        extract($instance, EXTR_SKIP);
        
        //see here http://php.net/manual/en/function.date.php for the date format I used
        //The pattern string I used 'l, F d, Y g:i a'
        //will output something like this: July 30, 2015 6:30 pm

        //getting 'start' and 'end' date,
        //'l, F d, Y' pattern string will give us
        //something like: Thursday, July 30, 2015
        //$start_date = date( 'l, F d, Y', $values['start_time'] );
        //$end_date = date( 'l, F d, Y', $values['end_time'] );

        //getting 'start' and 'end' time
        //'g:i a' will give us something
        //like 6:30 pm
        //$start_time = date( 'G:i', $values['start_time'] );
        //$end_time = date( 'G:i', $values['end_time'] );

        //with localization
        $start_date = date_i18n(get_option('date_format'), $values['start_time']);
        $end_date = date_i18n(get_option('date_format'), $values['end_time']);
        $start_time = date_i18n(get_option('time_format'), $values['start_time']);
        $end_time = date_i18n(get_option('time_format'), $values['end_time']);
        
        $event_url = 'http://www.facebook.com/event.php?eid=' . $values['eid'];

        //printing the data
        echo "<div class='fb-event'>";
        echo '<a class="fb-event-anchor" href="' . $event_url . '"';
        if ($newWindow)
            echo ' target="_blank"';
        echo '><div>';
        echo "<img src={$values['pic']} />";
        echo "<div class='fb-event-title'>{$values['name']}</div>";
        if ($start_date == $end_date) {
            //if $start_date and $end_date is the same
            //it means the event will happen on the same day
            //so we will have a format something like:
            //July 30, 2015 - 6:30 pm to 9:30 pm
            //$on = $start_date . "<br>" . $start_time . " - " . $end_time;
            $on = "{$start_date} &#183; {$start_time} - {$end_time}";
        } else {
            //else if $start_date and $end_date is NOT the equal
            //it means that the event will will be
            //extended to another day
            //so we will have a format something like:
            //July 30, 2013 9:00 pm to Wednesday, July 31, 2013 at 1:00 am
            //$on = "$start_date $start_time <br> $end_date $end_time";
            $on = "{$start_date} -<br>{$end_date}";
        }
        echo "<div class='fb-event-time'>{$on}</div>";
        if (!empty($values['location']))
            echo "<div class='fb-event-location'>" . $values['location'] . "</div>";
        if (!empty($values['description']))
            echo "<div class='fb-event-description'>" . nl2br($values['description']) . "</div>";
        //echo "<div style='clear: both'></div>";
        echo "</div></a>";
        echo "</div>";
    }
}

// register the widget
add_action('widgets_init',
            create_function('', "return register_widget('Facebook_Events_Widget');"));