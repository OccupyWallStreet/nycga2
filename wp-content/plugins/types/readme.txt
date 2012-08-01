=== Types - Custom Fields and Custom Post Types Management ===
Contributors: brucepearson, AmirHelzer, jozik, mihaimihai
Donate link: http://wp-types.com
Tags: CMS, custom field, custom fields, custom post type, custom post types, post, post type, post types, cck, taxonomy, fields, types, relationships, WPML
License: GPLv2
Requires at least: 3.1
Tested up to: 3.4.1
Stable tag: 1.0.4

The complete and reliable plugin for managing custom post types, custom taxonomy and custom fields.

== Description ==

Types makes it easy to customize the WordPress admin. Define your own content using **custom post types** and **custom taxonomy**. Redesign editing screens using **custom fields**.

The integration between custom post types, taxonomy and fields lets you do things that no other custom-fields plugin can do.

[vimeo http://vimeo.com/43104695]

= CUSTOM FIELDS =

Types includes support for a wide list of custom fields.

* **Single-line text**
* **Multi-line text**
* **WYSIWYG** (WordPress Visual Editor)
* **Checkbox**
* **Multi-value Checkboxes**
* **Radio group**
* **Drop-down Select**
* **File upload**
* **Image** (Types includes a robust image-resize and caching engine)
* **Date** (includes a JS date-picker)
* **Email**
* **Numeric**
* **Phone**
* **Skype**
* **URL**
* **Post reference** (using Types Parent / Child relationships management)

Types custom fields use the standard WordPress post-meta table, making it cross-compatible with any theme or plugin. Additionally, all fields can be **repeating fields**.

Types includes a complete [API for displaying custom fields with PHP](http://wp-types.com/documentation/functions/). It also integrates with [Views](http://wp-types.com/home/views-create-elegant-displays-for-your-content/), for fast and easy display of custom content.

= CUSTOM POST TYPES AND TAXONOMY =

Types lets you easily setup custom post types and taxonomy. You can create new post types in seconds and use the advanced settings to customize every aspect.

* Quick setup mode
* Full control over every feature
* Associate taxonomy with custom post types
* Define parent-child relationships between different post types
* Edit child items within parent editor (field tables)

= BUILT FOR STABILITY =

Types is part of a family of plugins, developed and maintained by [OnTheGoSystems](http://www.onthegosystems.com). Our plugins power over 100,000 commercial sites, using WordPress as a complete CMS. While we love features, we know that stability, performance, usability and security are critical. All our plugins go through comprehensive testing, QA and security analysis before every release.

= RELIABLE SUPPORT =

When you need help, we're here for you. We have a dedicated team of expert supporters, who can help with anything from a simple how-to advice to complex problem solving. Depending on your needs, you can get free community support, or dedicated high-availability commercial support. See for yourself in our [support forum](http://wp-types.com/forums/).

= MULTILINGUAL READY =

Types is the only custom fields and post types plugin that's built multilingual-ready. It plays perfectly with [WPML](http://wpml.org). You'll be able to translate everything, including texts and labels in the WordPress admin and user-content for front-page.

== Installation ==

1. Upload 'types' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= How can I display custom post types on the home-page? =

By default, WordPress will either display your blog posts or a specific page on the home-page.

To display custom post types on the home-page, you have two options:

1. If you're comfortable with PHP and WordPress API, edit the site's template files (probably index.php) and load the custom post types there. Different themes do this differently, so we can't really say what single approach works best. You should look at [get_posts](http://codex.wordpress.org/Template_Tags/get_posts), which is part of the WordPress Template Tags system.
2. If you want to build sites right away, without becoming an expert in WordPress API and you can afford $49 (USD), try [Views](http://wp-types.com/home/views-create-elegant-displays-for-your-content/). You'll be able to load whatever content you need from the database and display it anywhere and in whatever way you choose.

We're sorry, but we don't know of any third option which is both free and requires no coding.

= Can I use Types without Views? =

Sure you can! Types, by itself, replaces several other plugins that define custom types and fields. We believe that it does it much better, but it's up to you to decide.

If you also buy Views, you'll have a complete solution for both **defining** and **displaying** custom data. You can achieve everything that Views does if you're fluent in PHP and know WordPress API. When you buy Views, you're also supporting Types development, but we're not looking for donations. You should consider Views for its value and nothing else.

= I am already a ninje developer, do I really need Views? =

We honestly think so. Even if you're an expert developer, do you really enjoy doing the same stuff over and over again? With Views, you can concentrate on the unique features of every new site that you build, without wasting time on routine stuff.

Views was originally inspired by the Drupal module with the same name. Around 90% of all Drupal sites use the Drupal Views module and many consider it as one of the most powerful features in Drupal. Now, you too can enjoy the same power (and even more), but without any of the complexity of Drupal.

= Can Types display custom fields that I defined somehow else? =

Yes! You can tell Types to manage any other custom fields. For example, if you're using an e-commerce plugin, you can tell Types to manage product pricing. This will greatly help you display these fields with Types API or with Views.

Go to Custom fields control, under the Types menu. There, you can tell Types to manage existing custom fields.

= How do I migrate an existing site to use Types? =

The most important thing is to remember not to define custom post types and taxonomy in more than one place. If you previously defined them in PHP, first, remove your PHP code with the manual definition. The content will appear to temporarily vanish. Don't panic. Now, redefine the same custom post types and taxonomy with Types. Everything will return :-)

Types also includes data import from other plugins such as Custom Post UI and Advanced Custom Fields.

= Can I import and export my Types settings? =

Yes! Types includes its own import and export features, using an XML settings file. If you create a development site, you can easily transfer settings to the production site.

= What is the advantage of using Types over separate plugins for custom post types, taxonomy and fields? =

Types offers a much richer integration, which is simply impossible with separate plugins. For example, you have fine-grained control of where to display custom meta-boxes. Because Types defines both the post types and fields, we have greater control of where things can go.

Additionally, Types is the only plugin that lets you define parent/child relationships between different post types AND use that information to edit child data when editing a parent.


== Screenshots ==

1. Defining custom post types and taxonomy
2. Defining custom fields (meta-groups)
3. Editing custom fields
4. Inserting custom fields to content
5. Bulk editing child content using Field Tables

== Changelog ==

= 0.9 =
* First release

= 0.9.1 =
* Added Embedded mode
* Allows to manage existing custom fields with Types
* Added a .po file for translating Types interface

= 0.9.2 =
* Added WYSIWYG custom fields
* Improved the usability for setting up custom taxonomies
* Date fields use the date format specified by WordPress
* Fixed a few bugs for WordPress 3.3
* Checks that fields cannot be created twice
* Checks that only local images are resized
* Added bulk-delete for custom fields
* Fixed a few issues with WPML support

= 0.9.3 =
* Added an import screen from Advanced Custom Fields
* Added an import screen from Custom Posts UI
* Added support for non-English character in custom field names
* Eliminated messages about how to insert custom fields in PHP
* Check if fields already exist with the same name before creating them
* Improved compatibility with WPML

= 0.9.4 =
* Added an option to display custom field groups on specific templates only
* Fixed a number of bugs with Javascript and with Windows servers

= 0.9.4.1 =
* Fixed a problem adding custom fields to a group on some servers
* Fixed so that standard tags and categories work again with custom post types
* Fixed custom field groups not being shown for some content templates

= 0.9.4.2 =
* Fixes a few bugs.

= 0.9.5 =
* Added support for parent/child post relationship between different types
* Added Field Tables, for bulk editing child fields from the parent editor
* Streamlined the field insert GUI

= 0.9.5.1 =
* Fixed a last-minute bug with post relationship

= 0.9.5.4 =
* Fixed a javascript bug on group edit pages

= 1.0 =
* Added an option to make fields repeatable
* Added multiple-option checkboxes
* Added an option to output just URLs for resized images
* Added support for global class and style for all fields
* Added AJAX support for conditional fields
* Added support for non-ASCII characters in CPT URLs
* Added translations for Spanish, French, German, Portuguese, Italian and Dutch
* Fixed many small bugs and glitches

= 1.0.1 =
* Fixes a number of small bugs, related with JS interaction with other plugins

= 1.0.2 =
* Improved WPML support with repeating fields
* Fixed problems with decimal repeating fields
* Post relationship meta box goes through standard WordPress filters
* Fixed field display conditions for date fields
* Fixed field count when adding or deleting fields
* Stopped saving child posts when saving the parent, to avoid conflicts with other plugins
* Checkboxes can save '0' for empty fields

= 1.0.3 =
* Fixes for repeating fields

= 1.0.4 =
* Some fixes for textarea rendering without automatic paragraph insertion
* Some fixes for WPML compatibility
* Support for Views 1.1.1

== Upgrade Notice ==

= 0.9.1 =
* The new Embedded mode allows integrating Types functionality in WordPress plugins and themes.

= 0.9.2 =
* Check out the new WYSIWYG custom fields.

= 0.9.3 =
* This version streamlines the admin screens and includes a importers from other plugins

= 0.9.4 =
* You can now enable custom field groups for content with specific templates

= 0.9.4.1 =
* Fix a few problems found in the 0.9.4 release

= 0.9.5 =
Try the new parent/child relationship between different post types!

= 0.9.5.1 =
Fixed a last-minute bug with post relationship

= 0.9.5.4 =
Fixed a javascript bug on group edit pages

= 1.0 =
You can make any field repeating now

= 1.0.1 =
Small bugfix release

= 1.0.2 =
Better support for multilingual sites with repeating fields
