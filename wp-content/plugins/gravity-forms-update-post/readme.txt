=== Gravity Forms - Update Post ===
Contributors: p51labs
Donate link: http://p51labs.com/
Tags: gravityforms, forms, update, edit
Requires at least: 2.9
Tested up to: 3.3.2
Stable tag: 0.5.3
License: GPLv2

Update or Delete Posts, Pages or Custom Post Types with Gravity Forms

== Description ==

Update or Delete Posts, Pages or Custom Post Types with Gravity Forms.   Through a variety of filters the form can be restricted to the author, use custom permissions, take advantage of conditional fields, disable entries, override post status and more!

**NOTE: This is the first official release, test with your own data and if something doesn't work as expected send bugs to support@p51labs.com**

[vimeo http://vimeo.com/41818285]

== Installation ==

1. Upload the folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I use it? =

Simply embed a post form into a page with Gravity Forms and them pass a post id to the page via 'gform_post_id'.

= Does it respect Capabilities =

Yes.

= Will it save custom fields as unique =

Yes, there is a checkbox you can check on each custom field you add to the form to have the field saved as unique.

= Are there any filters/options? =

Yes, see the readme.txt file for example of hooks and filters that can be used

Use the default status of the post being edited
`function gform_update_post_status($status, $form)
{
  return 'inherit';
}
add_filter('gform_update_post_status', 'gform_update_post_status', 10, 2);`

Disable Entries
`function gform_update_post_entries($status, $form)
{
  return false;
}
add_filter('gform_update_post_entries', 'gform_update_post_entries', 10, 2);`

Change the options for the plugin
`function gform_update_post_id($id)
{
  $options['request_id'] = 'gform_post_id_special';
  
  return $options;
}
add_filter('gform_update_post_id', 'gform_update_post_id');`

Update Delete Button
`function gform_update_post_delete_button($button, $form)
{
  return '<div class="some-container">' . $button . '</div>';
}
add_filter('gform_update_post_delete_button', 'gform_update_post_delete_button', 10, 2);`

Change the delete confirmation
`function gform_update_post_confirmation_delete($confirmation)
{
  return __('Ack, you deleted it... haha!');
}
add_filter('gform_update_post_confirmation_delete', 'gform_update_post_confirmation_delete', 10, 2);`

== Screenshots ==

== Upgrade Notice ==

== Changelog ==

= 0.5.3 =
* Custom Meta Fix

= 0.5.2 =
* Added banner-772x250.png

= 0.5.1 = 
* Updated the FAQ on the readme.txt

= 0.5 =
* Initial Build
