<?php
/**
 * General formating functions.
 * Used to format output data and send messages to user.
 *
 * @version		$Rev: 199485 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2008, 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	Framework
 *

	Copyright 2008, 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Converts vars with values equivalent to zero (0) to an empty string.
 * This is useful as an output filter, when we need and empty textbox.
 *
 * @param mixed $var Input value to convert.
 * @return string Empty string if value is equivalent to zero.
 */
function ak_zero_clean( $var )
{
    return ( 0 == intval($var) ) ? '' : $var;
}

/**
 * Displays admin notices.
 *
 * @param $message	Message to display.
 * @return void
 */
function ak_admin_notify( $message = '' )
{
    if ( is_admin() ) {
	    if ( empty($message) ) {
		    $message = __('Settings saved.', 'akfw');
    	}
    	echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
    }
}

/**
 * Displays admin ERRORS.
 *
 * @param $message	Message to display.
 * @return void
 */
function ak_admin_error( $message )
{
    if ( is_admin() ) {
        echo '<div id="error" class="error"><p><strong>' . $message . '</strong></p></div>';
    }
}

/**
 * Displays dashboard notices by setting the 'admin_notices' action hook.
 * This is used for global warnings that have to show in all dashboard pages.
 *
 * @uses akAdminNotice
 * @param $message	Message to display.
 * @return void
 */
function ak_dashboard_notice( $message )
{
    if ( is_admin() ) {
        require_once ( AK_CLASSES . '/admin-notices.php' );
        new akAdminNotice($message);
    }
}

/**
 * Generic pager.
 *
 * @param int $total	Total elements to paginate.
 * @param int $per_page	Number of elements per page.
 * @param $current		Current page number.
 * @param $url			Base url for links. Only page numbers are appended.
 * @return string		Formated pager.
 */
function ak_pager( $total, $per_page, $url, $current = 0 )
{
	if ( 0 == $current ) $current = 1;

	$pages = $total / $per_page;
	$pages = ( $pages == intval($pages) ) ? intval($pages) : intval($pages) + 1;

	if ( $pages == 1 ) {
		$out = '';
	} else {
		$out = "<div class='pager'>\n";
		if ( $current != 1 ) {
			$start = $current - 1;
			$out .= '<a class="prev page-numbers" href="'. $url . $start .'">&laquo;&laquo;</a>' . "\n";
		}

		for ( $i = 1; $i <= $pages; $i++ ) {
			if ( $i == $current ) {
				$out .= '<span class="page-numbers current">'. $i ."</span>\n";
			} else {
				$out .= '<a class="page-numbers" href="'. $url . $i .'">'. $i ."</a>\n";
			}
		}

		if ( $current != $pages ) {
			$start = $current + 1;
			$out .= '<a class="next page-numbers" href="'. $url . $start .'">&raquo;&raquo;</a>' . "\n";
		}
		$out .= "</div>\n";
	}

	return $out;
}

/**
 * Creates an string telling how many time ago
 *
 * @param $datetime Date Time in mySql Format.
 * @return string The time from the date to now, just looks to yesterday.
 */
function ak_time_ago( $datetime )
{
    $before = strtotime($datetime);
    $is_today = ( date('Y-m-d') == substr($datetime, 0, 10) );
    $now    = time();

    $times = array (
        'd' => 43200,   // 12 hours
        'h' => 3600,    // 1 hour
        'm' => 60,      // 1 minute
    	's' => 1        // 1 second
    );

    $diff = $now - $before;
    foreach ( $times as $unit => $seconds ) {
        if ( $diff >= $seconds ) {
            $value = intval($diff / $seconds);
            break;
        }
    }

    $format = get_option('date_format') . ' | ' . get_option('time_format');  // Date-Time format
    switch ( $unit ) {
        case 's':
            $ago = __('Just Now', 'akfw');
            break;
        case 'm':
            $ago = sprintf(_n('1 minute ago', '%d minutes ago', $value, 'akfw'), $value);
            break;
        case 'h' :
            $ago = sprintf(_n('1 hour ago', '%d hours ago', $value, 'akfw'), $value);
            break;
        case 'd' :
            if ( 1 == $value ) {
                $literal = ( $is_today ) ? __('Today at %s', 'akfw') : __('Yesterday at %s', 'akfw');
                $ago = sprintf($literal, date('H:i', $before));
            } else {
                $ago = mysql2date($format, $datetime);
            }
            break;
    }

    return $ago;
}
