<?php
/*
Plugin Name: TinyMCE Excerpt
Plugin URI: http://www.simonwheatley.co.uk/wordpress-plugins/tinymce-excerpt/
Description: Use Tiny MCE for the excerpt while editing the excerpt.
Version: 1.33
Author: Simon Wheatley
Author URI: http://www.simonwheatley.co.uk/

Copyright 2007 Simon Wheatley

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// JQ JS to add the class 'mceEditor' to the excerpt textarea
function tme_convert_excerpt_js()
{
	// Only continue if this is an editing screen
	if ( ! tme_rich_editing() ) return;
?>
<script type="text/javascript">
	/* <![CDATA[ */
	// JQ JS to add the class 'mceEditor' to the excerpt textarea pre WP 2.5
	jQuery(document).ready( tme_convertExcerpt ); 
    function tme_convertExcerpt() {
		jQuery("#excerpt").addClass("mceEditor"); 
		if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
			// Ensure we don't double wrap stuff
			if ( ! jQuery("#excerpt").length )
				jQuery("#excerpt").wrap( "<div id='excerpteditorcontainer'></div>" ); 
			tinyMCE.execCommand("mceAddControl", false, "excerpt");
		}
	}
	/* ]]> */
</script>
<?php
}

// Enqueue script files, for inclusion by the standard WP magic
function tme_admin_enqueue_js()
{
	// Only continue if this is an editing screen
	if ( ! tme_rich_editing() ) return;
	wp_enqueue_script('jquery'); // Probably there anyway, but best to be sure
}

// Quick CSS make our new excerpt editor even more lovelier
function tme_admin_css()
{
	// Only continue if this is an editing screen
	if ( ! tme_rich_editing() ) return;
	// Fix the CSS, so the resize icon appears hard against the far right of the TinyMCE status bar.
?>
<style type='text/css'>
	#postexcerpt .mceStatusbarResize { margin-right: 0; }
	#postexcerpt #excerpteditorcontainer { border-style: solid; padding: 0; }	
</style>
<?php
}

// Are we on an editing screen?
function tme_rich_editing()
{
	global $editing;
	return ( $editing && user_can_richedit() );
}

// Hook it up to Wordpress

// We need to enqueue some scripts. This is not an ideal action 
// hook, but it does the business
add_action('admin_xml_ns', 'tme_admin_enqueue_js');
// Paragraphise the excerpt on save
add_filter('excerpt_save_pre', 'wpautop');
// Some CSS
add_action('admin_head', 'tme_admin_css');
// Some inline JS in the head, to avoid loading another file
add_action('admin_head', 'tme_convert_excerpt_js');

?>
