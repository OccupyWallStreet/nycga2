=== WP PageNavi Style ===
Plugin Author: http://www.snilesh.com
Contributors: Neel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YKY7SHDT8GTQG
Tags: navigation, pagination, paging, pages,navigation,pagenavi style,wp pagenavi styling,pagenavi styling,pagenavi css
Requires at least: 3.0
Tested up to: 3.3
Stable tag: trunk

Adds a more styling options to Wp-PageNavi wordpress plugin.

== Description ==

First i will like to say thanks to Lester 'GaMerZ' Chan & scribu for this beautiful wordpress page navigation plugin. 

To Use this plugin you must have [Wp Pagenavi](http://wordpress.org/extend/plugins/wp-pagenavi/)  plugin installed on your wordpress blog.


Links: [Demo 1](http://www.snilesh.com/resources/wordpress/wordpress-pagenavi-style-plugin/) | [Demo 2](http://themeforwordpress.org/) 

[Documentation](http://www.snilesh.com/resources/wordpress/wordpress-pagenavi-style-plugin/) | [wp pagenavi Plugin News](http://scribu.net/wordpress/wp-pagenavi/) 

== Installation ==

To use this plugin you must install wp-pagenavi plugin first.

You can either install it automatically from the WordPress admin, or do it manually:

1. Unzip the archive and put the `wp-pagenavi` folder into your plugins folder (/wp-content/plugins/).
1. Activate the plugin from the Plugins menu.

After you installed `wp-pagenavi` plugin next step is to install wp-pagenavi-style plugin.

You can either install it automatically from the WordPress admin, or do it manually:

1. Unzip the archive and put the `wp-pagenavi-style` folder into your plugins folder (/wp-content/plugins/).
1. Activate the plugin `wp pagenavi style` from the Plugins menu.

= Usage =

In your theme, replace code like this:

`
<div class="navigation">
	<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
	<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
</div>
`

with this:

`
<div class="navigation">
<?php wp_pagenavi(); ?>
</div>

`

Go to *WP-Admin -> Settings -> PageNavi* for configuration.

Visit [Wp PageNavi Style Documentation] (http://www.snilesh.com/resources/wordpress/wordpress-pagenavi-style-plugin/) for more details.

= Changing Style =

Visit [Wp PageNavi Style Documentation] (http://www.snilesh.com/resources/wordpress/wordpress-pagenavi-style-plugin/) for more details.


== Screenshots ==

Visit [Wp PageNavi Style Documentation] (http://www.snilesh.com/resources/wordpress/wordpress-pagenavi-style-plugin/) for more details.

== Changelog ==
= 1.3 =
* Menu Capability options changed. Pagenvi style options will be managed by administrator only.

= 1.1 =
* Removed CSS errors.

= 1.0 =
* First Version Of this Plugin