=== User meta shortcodes ===
Contributors: rvencu
Tags: user, user-meta, shortcode, meta, custom, field
Requires at least: 2.7
Tested up to: 3.2.1
Stable tag: 0.2.1
Use user meta as shortcode in post content without edit your theme files.

== Description ==

USAGE:use [userinfo field="fieldname"]some content[/userinfo] or [authorinfo field="fieldname"]some content[/authorinfo] shortcodes in your post content to show the "fieldname" meta value from the user-meta for the current logged-in user or for the post author without editing your theme files.EXAMPLES:[userinfo field="last_name"]{{empty}}[/userinfo]returns the last name of the current logged-in user. If no user is logged in then the value is empty string[userinfo field="user_login" if="admin"]You are the admin[/userinfo]this shortcode tests the field against the value and if true it displays the included content[userinfo field="user_login"] is your username and you are reading a post by [authorinfo field="user_login"]{{empty}}[/authorinfo].[/userinfo]this will display the username followed with the processed content. Note: never use the nested shortcode same as the enclosing shortcode. This will produce unexpected results!List of some of the available meta field names: ID, user_login, user_pass, user_nicename, user_email, user_url, user_registered, display_name, first_name, last_name,nickname, description, user_level, admin_color (Theme of your admin page. Default is fresh.), closedpostboxes_page, nickname, primary_blog, rich_editing, source_domain[authorinfo field="last_name"]returns the last name of the current post/page author.[authorinfo field="ID"]returns the user ID of the current post/page author.List of some of the available meta field names: user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name,nickname, first_name, last_name, description, jabber, aim, yim, user_level, user_firstname, user_lastname, user_description, rich_editing, comment_shortcuts, admin_color,plugins_per_page, plugins_last_view, ID If you add custom user meta via additional plugins, then the meta should be available for the above shortcodes

== Installation ==

1. Upload `user-meta-shortcodes` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

not applicable
== Changelog ==
0.2 - shortcodes accept content now. In addition, conditional display of content by comparing the profile field with a value. Content is only displayed if the values are identical.0.1 - initial version

== Frequently Asked Questions ==

Q. Can I use another user besides current user or post author?
A. It would be added in future versions of the plugin. I did not received any usage scenarios so far for this feature.
