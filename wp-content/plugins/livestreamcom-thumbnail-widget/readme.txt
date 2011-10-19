=== Plugin Name ===
Contributors: fatalmuffin
Donate link: http://dylan-brady.com/nickbrady
Tags: stream, live, thumnb, thumbnail, livestream, display, livestream.com
Requires at least: 2.0.2
Tested up to: 3.2.1
Stable tag: 4.3

This plugin will allow you to display a thumbnail from any livestream.com account!

== Description ==

This plugin will allow you to display a thumbnail from any livestream.com account!

Features:

- Displays most recent live thumbnail from LiveStream.com account
- (Optional) Displays Live Status. Users can easily know whether your stream is live or offline!
- (Optional) Display livestream description
- (Optional) Display livestream viewer count
- (Optional) Thumbnail redirect url lets you customize wherever you want the thumbnail to link to.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

- STREAM URL! This is the most important step.
Your STREAM URL will be the name of your livestream.com stream.
So if your livestream is for example: 'http://www.livestream.com/mylivestream',
then your STREAM URL should be set to 'mylivestream'.

- Your Redirect URL is wherever users who click your thumbnail will be sent to.
Make sure to include HTTP:// at the front! if not will be sent to 'http://-BLOG URL-/-REDIRECT URL-'

- Customize options to your hearts content.

== Frequently Asked Questions ==

Please submit any feedback/questions to fatalmuffin@gmail.com

== Screenshots ==

1. The widget running on the Mystique theme.

== Changelog ==

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 1.0 =
Initial Release

`<?php code(); // goes in backticks ?>`