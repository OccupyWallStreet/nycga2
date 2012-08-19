<?php
/* 
Plugin Name: Burnman's Diaspora Button
Plugin URI: http://theburnman.com/wordpress-plugins/burnmans-diaspora-button/
Description: This plugin adds a Diaspora button to posts.
Author: Burnman
Version: 0.1 
Author URI: http://theburnman.com/
License: GPL2
*/

/*  Copyright 2011  Burnman  (email : contact@theburnman.com)

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

function diaspora_button_display($content)
{
	// this is where we will display the subjot_button

	$options["page"] = get_option("button_on_page");
	$options["post"] = get_option("button_on_post");

	if ( (is_single() && $options["post"]) || (is_page() && $options["page"]) )
	{
		$button_box =
		"<div id=\"diaspora-button-box\" style=\"float:left; margin-right: 10px;\">
			<a href=\"javascript:(function(){f='https://joindiaspora.com/bookmarklet?url='+encodeURIComponent(window.location.href)+'&amp;title='+encodeURIComponent(document.title)+'&amp;notes='+encodeURIComponent(''+(window.getSelection?window.getSelection():document.getSelection?document.getSelection():document.selection.createRange().text))+'&amp;v=1&amp;';a=function(){if(!window.open(f+'noui=1&amp;jump=doclose','diasporav1','location=yes,links=no,scrollbars=no,toolbar=no,width=620,height=250'))location.href=f+'jump=yes'};if(/Firefox/.test(navigator.userAgent)){setTimeout(a,0)}else{a()}})()\"><div id=\"header_sharetodiaspora\" title=\"Share this at Diaspora!\"><img src=\"/wp-content/plugins/burnmans-diaspora-button/images/diaspora-share-button.png\"></div></a>
		</div>";
		return $content . $button_box;
		
	} else {
		$button_box =
		"<div id=\"diaspora-button-box\" style=\"float:left; margin-right: 10px;\">
			<a href=\"javascript:(function(){f='https://joindiaspora.com/bookmarklet?url='+encodeURIComponent(window.location.href)+'&amp;title='+encodeURIComponent(document.title)+'&amp;notes='+encodeURIComponent(''+(window.getSelection?window.getSelection():document.getSelection?document.getSelection():document.selection.createRange().text))+'&amp;v=1&amp;';a=function(){if(!window.open(f+'noui=1&amp;jump=doclose','diasporav1','location=yes,links=no,scrollbars=no,toolbar=no,width=620,height=250'))location.href=f+'jump=yes'};if(/Firefox/.test(navigator.userAgent)){setTimeout(a,0)}else{a()}})()\"><div id=\"header_sharetodiaspora\" title=\"Share this at Diaspora!\"><img src=\"/wp-content/plugins/burnmans-diaspora-button/images/diaspora-share-button.png\"></div></a>
		</div>";
		return $content . $button_box;
	}
}

add_action("the_content", "diaspora_button_display");

?>