=== ProjectManager ===
Contributors: Kolja Schleich
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2329191
Tags: datamanager, CMS, Content Management System
Requires at least: 2.7
Tested up to 3.0.1
Stable tag: 3.0.4

This plugin can be used to manage any number of projects with recurrent datasets (e.g. portrait system, dvd collection)

== Description ==

This plugin is a datamanager for any recurrent datasets. It can be used to manage and list a DVD collection, to to present portraits (e.g. athlets of a team), simple tabular calendar or anything you can think of. Below is a least of features

**Features**

* add as many different projects as you want to
* widget for any project, controlled via admin panel
* adding of form fields (text, textfield, e-mail, date, url, selection, checkbox and radio list) for each project independently
* simple search of any form field and category names
* template system to easily customize frontend display
* Ajax enabled editing of datasets
* easy adding of shortcodes via TinyMCE Button
* change colorscheme for output tables via admin panel
* dataset sorting by any form field
* import and export of datasets from/to CSV file
* hook projects into user profile
* manual drag & drop sorting of datasets

[Randy Hoyt](http://randyhoyt.com/) created a [Screencast](http://randyhoyt.com/wordpress/snippets/) on the usage of ProjectManager. Thanks a lot.


**Translations**

* German
* French
* Italian ([Adriano Urso](http://heyyoudesign.it/))

== Installation ==

To install the plugin to the following steps

1. Unzip the zip-file and upload the content to your Wordpress Plugin directory.
2. Activiate the plugin via the admin plugin page.


== Screenshots ==
1. Project Overview Page
2. Settings page
3. Add different form fields dynamically
4. Easy adding of new datasets
5. Widget control panel
6. Easy adding of shortcode tags via TinyMCE Button
7. Output of projects with gallery template
8. Individual dataset


== Credits ==
The ProjectManager menue icons and TinyMCE Button are taken from the Fugue Icons of http://www.pinvoke.com/.


== Changelog ==

= 3.0.4 =
* UPDATE: French Translation

= 3.0.3 =
* NEW: Italian translation
* BUGFIX: formfields

= 3.0.2 =
* BUGFIX: only load javascript files on projectmanager pages to avoid malfunction of WP image editor

= 3.0.1 =
* BUGFIX: fixed css to avoid linebreak in gallery display

= 3.0 =
* NEW: documentation included with plugin
* UPDATED: Screenshots
* BUGFIX: empty property when form field label is empty
* BUGFIX: external fields, such as statistics from LeagueManager

= 2.9.8 =
* BUGFIX: dataset image got lost in search
* BUGFIX: date lost upon editing dataset
* BUGFIX: dataset meta data not available in single view
* BUGFIX: check if dataset and meta objects have values to avoid fatal error with empty property

= 2.9.7 =
* NEW: include dataset meta data in dataset object accesible through $dataset->LABEL. LABEL is the label of the formfield cleaned with the function sanitize_title. Use var_dump($dataset) to display object contents.

= 2.9.6 =
* BUGFIX: slideshow conflict with NextGen Gallery

= 2.9.5 =
* NEW: french translation

= 2.9.4 =
* BUGFIX: User registration when project is hooked into profile

= 2.9.3 =
* NEW: method to duplicate dataset
* NEW: sort formfield options alphabetically
* BUGFIX: import of datasets

= 2.9.2 =
* NEW: WP User field type
* NEW: custom fields with input callback functions
* BUGFIX: missed argument 'order' in project() function
* BUGFIX: division by zero bug in dataset.php with "NaN" datasets per page

= 2.9.1 =
* BUGFIX: TinyMCE Editor content displayed HTML tags

= 2.9 =
* NEW: time formfield
* NEW: filter for each formfield type *projectmanager_$type* (select, radio, checkbox, project all text)
* BUGFIX: strange PHP 4 bug

= 2.8.2 =
* BUGFIX: removed NumberFormatter from lib/ajax.php

= 2.8.1 =
* NEW: template tag for widget
* REMOVED: NumberFormatter caused syntax error for some webhosts

= 2.8 =
* NEW: widget with 2.8 API
* NEW: template tags

= 2.7.3 =
* BUGFIX: unable to delete all formfields

= 2.7.2 =
* BUGFIX: table template

= 2.7.1 =
* BUGFIX: parse error in lib/core.php line 713

= 2.7 =
* NEW: image upload formfield type
* NEW: video field with upload
* NEW: internal link to other project
* BUGFIX: dataset ordering

= 2.6 =
* NEW: shortcode attribute to limit number of results
* NEW: shortcode attribute to get datasets with specific meta key and meta value
* NEW: shortcode attribute to get random datasets with limit of results

= 2.5.8 =
* BUGIFX: call-time pass-by-reference deprecated

= 2.5.7 =
* BUGFIX: parse error

= 2.5.6 =
* CHANGED: moved AJAX functions to separate class in lib/ajax.php
* BUGFIX: chmod of image uploads
* BUGFIX: access control for editing datasets

= 2.5.5 =
* BUGFIX: access to dataset editing page with only cap 'projectmanager_user'
* BUGFIX: profile update

= 2.5.4 =
* BUGFIX: access control for adding/editing of datasets

= 2.5.3 =
* BUGFIX: access control for adding/editing of datasets
* code cleaning

= 2.5.2 =
* BUGFIX: profile hook

= 2.5.1 =
* BUGFIX: check for double datasets

= 2.5 =
* NEW: fine grained access control
* NEW: thickbox box for adding WP Users
* NEW: add dataset upon user registration if user has capablity
* NEW: profile Hook for any number of projects
* CHANGED: only users with matching datasets are shown form in profile

= 2.4.7 =
* NEW: display ID of dataset in admin overview

= 2.4.6 =
* BUGFIX: MySQL string to get datasets of certain category

= 2.4.5 =
* BUGFIX: options

= 2.4.4 =
* CHANGED: moved project settings into project table

= 2.4.3 =
* BUGFIX: display of admin menu icons
* CHANGED: moved main ProjectManager menu back down

= 2.4.2 =
* NEW: table template with tiny image
* BUGFIX: unsetting of widget option if deleted
* UPDATED: german translation with slideshow effects

= 2.4.1 =
* NEW: numeric and currency formfield types (require NumberFormatter or WP filter)
* NEW: filters projectmanager_numeric and projectmanager_currency for display
* BUGFIX: popup window size for newly added formfield options
* BUGFIX: image upload in user profile
* BUGFIX: single view in gallery template
* CHANGED: took out separate option for displaying image form in profile
* CHANGED: default limit on datasets of 15, use "NaN" for no limit

= 2.4 =
* NEW: custom icon directory in THEMEDIR/projectmanager/icons
* BUGFIX: dataset ordering is overriden by dropdown selection
* BUGFIX: IE Slideshow Bug fixed with new jQuery Cycle Plugin
* BUGFIX: Popup Window for Formfield Options
* BUGFIX: TinyMCE Button for searchform
* BUGFIX: total number of datasets in search
* CHANGED: use function add_object_page for menu if present
* CHANGED: use of jQuery Cycle Plugin for Slideshow

= 2.3 =
* NEW: URL and E-Mail with alternative text in format "www.example.com|Example Website"
* NEW: multiple widget support
* NEW: file-upload form field without AJAX uploading
* BUGFIX: AJAX editing if datafield value is empty
* BUGFIX: manual ordering of datasets
* BUGFIX: options link for select, checkbox and radio form types
* BUGFIX: save options for formfields
* CHANGED: apply stripslashes in function getDatasetMeta instead of getDatasetMetaData

= 2.2 =
* NEW: set file permission for image uploads
* NEW: image as new formfield type. Image needds to be uploaded already
* BUGFIX: AJAX editing of datasets
* BUGFIX: single and double quotes in names, formfields and formfield labels

= 2.1 =
* NEW: filter for formfields to enable bridging

= 2.0 =
* NEW: manual drag & drop sorting of datasets (ajax saving, but not updating tr class)
* NEW: set order of datasets in option
* NEW: more shortcode atts for project display
* NEW: delete image folder upon plugin uninstallation
* BUGFIX: dataset division by zero
* CHANGED: save image name in database also if file already exists

= 1.9 =
* NEW: show formfield ID on admin page
* NEW: shortcode supports ordering of datasets including TinyMCE Button
* BUGFIX: project deletion
* CHANGED: varchar lengths for project title, dataset name and image

= 1.8.2 =
* NEW: remove line breaks from form field values upon export
* BUGFIX: image upload and thumbnail creation

= 1.8.1 =
* BUGFIX: shortcode display of single dataset

= 1.8 =
* NEW: dataset ordering selection in frontend
* NEW: hook one project into profile
* NEW: set custom menu icon for each project
* NEW: shortcode to display single dataset
* NEW: template engine for frontend display
* CHANGED: new menu icons
* CHANGED: major restructuring of plugin
* CHANGED: shortcodes
* DELETED: Ajax adding of category dropdown in TinyMCE Button. Using text field for direct input of Category ID.

= 1.7 =
* NEW: sorting of datasets by form fields
* NEW: import/export of datasets from/to CSV file
* BUGFIX: form field saving

= 1.6.2 =
* CHANGED: moved image directory to wp-content/uploads

= 1.6.1 =
* BUGFIX: pretty permalinks

= 1.6 =
* NEW: Slideshow Widget
* CHANGED: usability enhancements

= 1.5 =
* NEW: coupled datasets to user id who entered them.

= 1.4 =
* NEW: selection, checkbox list, radio list form field types

= 1.3 =
* NEW: support for multiple categorization
* NEW: customization of dataset output via wordpress hooks
* NEW: search for category names (comma separated list of cat names)
* NEW: set colorschemes of tables

= 1.2.3 =
* NEW: option to add direct link to project in navigation panel
* BUGFIX: adding of new project failed

= 1.2.2 =
* BUGFIX: upgrade bug

= 1.2.1 =
* BUGFIX: database collation

= 1.2 =
* NEW: Ajax editing of datasets
* NEW: display of specific group only

= 1.1 =
* NEW: full control of display via shortcodes
* NEW: TinyMCE Button for better usability
* CHANGED: major restructuring of plugin

= 1.0 =
* initial release
