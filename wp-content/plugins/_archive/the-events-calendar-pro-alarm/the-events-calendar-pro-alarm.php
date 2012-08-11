<?php
/*
Plugin Name: The Events Calendar PRO Alarm
Plugin URI: http://wordpress.org/extend/plugins/the-events-calendar-pro-alarm/
Description: This plugin adds an alarm functionality to The Events Calendar PRO plugin by using the Additional Fields feature of Events Calendar PRO. This evolved from the following <a href="http://tri.be/support/forums/topic/add-alarm-to-event/">Add Alarm to Event</a> forum discussion topic. The <a href="http://tri.be/wordpress-events-calendar-pro/">Events Calendar PRO plugin</a> is required.
Version: 1.0
Text Domain: events-calendar-pro-alarm
Author: Andy Fragen & Joey Kudish (Modern Tribe, Inc.)
Author URI: http://thefragens.com/blog/2012/05/add-alarm-to-events-calendar-pro/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
* The Events Calendar PRO Alarm
*
* This plugin adds an alarm functionality to The Events Calendar PRO plugin by using
* the Additional Fields feature of Events Calendar PRO.
*
* @package      the-events-calendar-pro-alarm
* @link         http://thefragens.com/blog/2012/05/add-alarm-to-events-calendar-pro/
* @link         https://github.com/afragen/events-calendar-pro-alarm/
* @link         http://wordpress.org/extend/plugins/the-events-calendar-pro-alarm/
* @author       Andy Fragen <andy@thefragens.com>
* @copyright    Copyright (c) 2012, Andrew Fragen
*
* The Events Calendar PRO Alarm is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License version 2, as published by the
* Free Software Foundation.
*
* You may NOT assume that you can use any other version of the GPL.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
*
* You should have received a copy of the GNU General Public License along with
* this program; if not, write to:
*
*      Free Software Foundation, Inc.
*      51 Franklin St, Fifth Floor
*      Boston, MA  02110-1301  USA
*
* The license for this software can also likely be found here:
* http://www.gnu.org/licenses/gpl-2.0.html*
*/
/* Add your functions below this line */

add_filter( 'tribe_ical_feed_item', 'tribe_ical_add_alarm', 10, 2 );
function tribe_ical_add_alarm( $item, $eventPost ) {
	$alarm = tribe_get_custom_field( 'Alarm', $eventPost->ID );
	if ( !empty( $alarm ) && is_numeric( $alarm ) ) {
		$item[] = 'BEGIN:VALARM';
		$item[] = 'TRIGGER:-PT' . $alarm . "M";
		$item[] = 'END:VALARM';
	}
	return $item;
}

/* Add your functions above this line */
?>