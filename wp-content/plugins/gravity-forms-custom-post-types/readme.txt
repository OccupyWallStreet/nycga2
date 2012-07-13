=== Gravity Forms + Custom Post Types ===
Contributors: bradvin
Donate link: http://themergency.com/donate/
Tags: form,forms,gravity,gravity form,gravity forms,CPT,custom post types,custom post type,taxonomy,taxonomies
Requires at least: 3.0.1
Tested up to: 3.3.1
Stable tag: 3.0.1

Easily map your forms that create posts to a custom post type. Also map dropdown select, radio buttons list and checkboxes lists to a custom taxonomy.

== Description ==

> This plugin is an add-on for the [Gravity Forms Plugin](http://bit.ly/getgravityforms "visit the Gravity Forms website"). 
> If you don't yet own a license of the best forms plugin for WordPress, go and [buy one now!](http://bit.ly/getgravityforms "purchase Gravity Forms!")

Gravity forms allows you to create posts from a form using 'post fields'. By default the submitted form will create a post, but I wanted a way to save a custom post type instead. It can be done quite easily with some php code, but I wanted it to be easier without any code at all. Now it is easy. Maybe too easy :)

You can also link a custom taxomony to the field types : Drop Downs, Mulit Selects, Radio Buttons, Checkboxes and Single Line Text. So when the form is displayed a list of terms for the custom taxonomy are listed. And then when the post (or custom post type) is created, it automatically links the post to the selected taxonomy term(s).

**features**

*   Map a form to a custom post type (using the post title field)
*   Map fields (Drop Downs, Multiple Choice or Checkboxes) to a custom taxonomy
*   Supports Gravity Forms v1.5 and over (including v1.6)
*   Ability to have more than 1 taxonomy linked in a form (see screenshots)
*   Hierarchical dropdowns for hierarchical taxonomies (see screenshots)
*   Populate a dropdown with posts
*   Ability to set a parent post using the dropdown control
*   Single line text field can link to taxonomies by entering a comma seperated list
*   Enhanced UI on single line text fields to show a "tag input" styled control (see screenshots)

**How to map a form to a custom post type**

Add a post title field to your form and under the advanced tab, tick the "Save As Post Type" checkbox. A dropdown will appear with the available post types. Select the one you want.

**How to link a field to a custom taxonomy**

Custom taxonomies can be linked to Drop Downs, Mulit Selects, Radio Buttons and Checkboxes. Under the advanced tab for your field, tick the "Populate with a Taxonomy" checkbox. A dropdown will appear and you can select your custom taxonomy from the list. 

**How to link the saved post to taxonomies using a single line text field**

You can also link a Single Line Text field to a taxonomy, so it can be used to input existing and new taxonomy terms. Under the Advanced tab, click "Save to Taxonomy" checkbox and then select the taxonomy from thr drop down. You can also choose to show an advanced UI by checking the "Enable enhanced UI" checkbox. This will enable an awesome "tag input" style control. See screenshot for more info. If the control contains exising terms, then they are linked to the saved post. If there are new terms in the control, then the terms are created and they are also linked.

**How to set a parent post with the dropdown field**

You can now link the dropdown field to a post type and try to make it set the parent item. Under the advanced tab, check the "Populate with Post Type" checkbox. Select the SAME post type that you selected when adding a post title field (above), and check "Try to set parent". This then fills the dropdown with a list of existing posts, so when you save the form and the post is created, it trys to set the parent post to whatever was selected in the dropdown.

== Installation ==

1. Upload the plugin folder 'gravity-forms-custom-post-types' to your `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure you also have Gravity Forms activated.

== Screenshots ==

1. An example of what the end result can look like
2. Support for hierarchical taxonomies
3. How to map a form to a post type
4. How to map a field to a custom taxonomy
5. How to map a dropdown field to save the parent post
6. Single Line Text field with enhanced UI
7. How to map a single line text field to a taxonomy

== Changelog ==

= 3.0.1 =
* Fixed minor bug causing a PHP warning (_FILE_)
* removed the restriction of not including scripts when a call is ajax

= 3.0 =
* Removed support for Gravity Forms v1.4.5. Now supports v1.5 and up (including 1.6)
* Added support for single line text fields
* Added ability to populate a dropdown with posts
* Added ability to set a parent post when saving a post form
* Multiselect control now supports "populate with taxonomy" too
* "first value" default overriden when populating with a taxonomy
* Shows taxonomy selections when designing the form
* Fixed support for conditional logic
* Previews now load taxonomy terms
* Previews can show enhanced UI (only in V1.6 and above)

= 2.0 =
* Added support for both Gravity Forms v1.5 beta and v.1.4.5
* Now supports linking taxonomies to Drop Downs, Multiple Choice or Checkboxes
* Integrated with GF v1.5 hooks for easier configuration (thanks to Alex and Carl from RocketGenius)
* Support linking more than 1 taxonomy to a form
* To keep in line with the GF standards, mapping a form to a CPT in GF v1.4.5 can now be done via the 'post title' field

= 1.0 =
* Initial Relase. First version.

== Frequently Asked Questions ==

= Does this plugin rely on anything? =
Yes, you need to install the [Gravity Forms Plugin](http://bit.ly/getgravityforms "visit the Gravity Forms website") for this plugin to work.

== Upgrade Notice ==

Please upgrade to the latest version
