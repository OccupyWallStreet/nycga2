=== Advanced Custom Fields ===
Contributors: Elliot Condon
Donate link: https://www.paypal.com/au/cgi-bin/webscr?cmd=_flow&SESSION=-B2MHZ-ioHQb-z1o22AMmhjSI08rxFqQdljyfqVa1R-4QrbQWPNcfL37jYi&dispatch=5885d80a13c0db1f8e263663d3faee8d5fa8ff279e37c3d9d4e38bdbee0ede69
Tags: custom, field, custom field, advanced, simple fields, magic fields, more fields, repeater, matrix, post, type, text, textarea, file, image, edit, admin
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 3.3

Completely Customise your edit pages with an assortment of field types: Wysiwyg, Repeater, text, image, select, checkbox, page link, post object and more! Hide unwanted metaboxes and assign to any edit page!

== Description ==

Advanced Custom Fields is the perfect solution for any wordpress website which needs more flexible data like other Content Management Systems. 

* Visually create your Fields
* Select from multiple input types (text, textarea, wysiwyg, image, file, page link, post object, relationship, select, checkbox, radio buttons, repeater, more to come)
* Assign your fields to multiple edit pages (specific ID's, post types, post slugs, parent ID's, template names)
* Add, Edit and reorder infinite rows to your fields
* Easily load data through a simple and friendly API
* Uses the native WordPress custom post type for ease of use and fast processing
* Now uses custom Database tables to improve speed, reliability and future development

= Field Types =
* Text (type text, api returns text)
* Text Area (type text, api returns text with `<br />` tags)
* WYSIWYG (a wordpress wysiwyg editor, api returns html)
* Image (upload an image, api returns the url)
* File (upload a file, api returns the url)
* Select (drop down list of choices, api returns chosen item)
* Checkbox (tick for a list of choices, api returns array of choices)
* Page Link (select 1 or more page, post or custom post types, api returns the url)
* Post Object (select 1 or more page, post or custom post types, api returns post objects)
* Date Picker (jquery date picker, options for format, api returns string)
* True / False (tick box with message, api returns true or false)
* Repeater (ability to create repeatable blocks of fields!)
* Relationship	(select and order post objects with a tidy interface)
* Color Picker (Farbtastic!)

= Tested on =
* Mac Firefox 	:)
* Mac Safari 	:)
* Mac Chrome	:)
* PC Firefox	:)
* PC ie7	:S

= Demonstration =
http://plugins.elliotcondon.com/advanced-custom-fields/demonstration/

= Documentation =
http://plugins.elliotcondon.com/advanced-custom-fields/documentation/

= Field Type Info =
http://plugins.elliotcondon.com/advanced-custom-fields/field-types/

= Website =
http://plugins.elliotcondon.com/advanced-custom-fields/

= Bug Submission and Forum Support =
http://support.plugins.elliotcondon.com/categories/advanced-custom-fields/

= Please Vote and Enjoy =
Your votes really make a difference! Thanks.


== Installation ==

1. Upload 'advanced-custom-fields' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You may be prompted for a Database Upgrade. This is necessary for ACF to function. Please backup your database and click the Upgrade button
3. Click on Settings -> Adv Custom Fields and create your first Custom Field Group!
4. Your ACF field group will now appear on the page / post / template you specified in the field group's location rules!
5. Read the documentation to display your data: 


== Frequently Asked Questions ==

= Q. I can't see the "Select Image" button for my image field! =
A. For Image uploads to work, your post type must support "editor"

= Q. I have a question =
A. Chances are, someone else has asked it. Check out the support forum at: 
http://support.plugins.elliotcondon.com/categories/advanced-custom-fields/


== Screenshots ==
1. Creating the Advanced Custom Fields

2. Adding the Custom Fields to a page and hiding the default meta boxes

3. The Page edit screen after creating the Advanced Custom Fields

4. Simple and intuitive API. Read the documentation at: http://plugins.elliotcondon.com/advanced-custom-fields/documentation/


== Changelog ==

= 3.0.6 =
* Bug Fix: Location meta box now shows all pages / posts
* Bug Fix: upgrade and settings url should now work / avoid conflicts with other plugins

= 3.0.5 =
* Support: use wp native functions to add all user roles to location metabox
* Update: gave acf a css update + new menu structure
* Bug fix: fixed a few issues with wysiwyg js/css in wp3.3
* Bug fix:  fixed page_name conflicting with normal pages / posts by adding a "acf_" to the page_name on save / update
* Performance: location metabox - limited taxonomies to hierarchial only.

= 3.0.4 =
* Bug fix: WYSIWYG is now compatible with WP 3.3 (May have incidentally added support for gravity forms media button! But not 100% sure...)
* Fix : Taxonomy Location rule now only shows hierarchal taxonomies to improve speed and reduce php memory issues

= 3.0.3 =
* New translation: French (thanks to Netactions)
* Support: added support for new wp3.3 editor
* Bug fix: fixed WYSIWYG editor localised errors
* Bug fix: removed trailing commas for ie7

= 3.0.2 =
* New Feature: Added Export tab to export a WP native .xml file
* New Option: Relationship / Post type - filter by taxonomy
* New Option: default values for checkbox, select and radio
* New Function: register_options_page - add custom options pages (Requires the option page addon)
* Bug fix: WYSIWYG + repeater button issues
* Bug fix: general house keeping

= 3.0.1 =
* Bug Fix - repeater + wysiwyg delete / add duplicate id error
* Bug fix - repeater + file - add file not working
* Bug Fix - image / file no longer need the post type to support "editor"
* WYSIWYG - fixed broken upload images
* misc updates to accommodate the soon to be released "Flexible Field"

= 3.0.0 =
* ACF doesn't use any custom tables anymore! All data is saved as post_meta!
* Faster and more stable across different servers
* Drag-able / order-able metaboxes
* Fields extend from a parent object! Now you can create you own field types!
* New location rule: Taxonomy
* New function: register_field($class, $url);
* New Field: Color Picker
* New Option: Text + Textarea formatting
* New Option: WYSIWYG Show / Hide media buttons, Full / Basic Toolbar buttons (Great for a basic wysiwyg inside a repeater for your clients)
* Lots of bug fixes

= 2.1.4 =
* Fixed add image tinymce error for options Page WYSIWYG
* API: added new function: update_the_field($field_name, $value, $post_id)
* New field: Relationship field
* New Option for Relationship + Post Object: filter posts via meta_key and meta_value
* Added new option: Image preview size (thumb, medium, large, full)
* Fixed duplicate posts double value problem
* API update: get_field($repeater) will return an array of values in order, or false (like it used to!)
* Radio Button: added labels around values
* Post object + Page Link: select drop down is now hierarchal
* Input save errors fixed
* Add 'return_id' option to get_field / get_sub_field
* Many bug fixes

= 2.1.3 =
* Fixed API returning true for repeater fields with no data
* Added get_fields back into the api!
* Fixed field type select from showing multiple repeater activation messages 

= 2.1.2 =
* Fixed repeater sortable bug on options page
* Fixed wysiwyg image insert on options page
* Fixed checkbox value error
* Tidied up javascript + wysiwyg functions


= 2.1.1 =
* Fixed Javascript bugs on edit pages

= 2.1.0 =
* Integrate acf_values and wp_postmeta! Values are now saved as custom fields!
* Ajax load in fields + update fields when the page / post is modified
* API has been completely re written for better performance
* Default Value - text / textarea
* New upgrade database message / system
* Separate upgrade / activate scripts
* Select / page link / post object add Null option
* Integrate with Duplicate Posts plugin
* New location rule: post format
* Repeater field attach image to post
* Location: add children to drop down menu for page parent
* Update script replaces image urls with their id's
* All images / Files save as id's now, api formats the value back into a url
* Simple CSS + JS improvements
* New Field: Radio Buttons (please note Firefox has a current bug with jquery and radio buttons with the checked attribute)

= 2.0.5 =
* New Feature: Import / Export
* Bug Fixed: Wysiwyg javascript conflicts
* Bug Fixed: Wysiwyg popups conflicting with the date picker field
* New style for the date picker field

= 2.0.4 = 
* New Addon: Options Page (available on the plugins store: http://plugins.elliotcondon.com/shop/) 
* API: all functions now accept 'options' as a second parameter to target the options page
* API: the_field() now implodes array's and returns as a string separated by comma's
* Fixed Bug: Image upload should now work on post types without editor
* Fixed Bug: Location rule now returns true if page_template is set to 'Default' and a new page is created
* General Housekeeping

= 2.0.3 =
* Added Option: Repeater Layout (Row / Table)
* Fixed bug: Now you can search for media in the image / file fields
* Added Option: Image field save format (image url / attachment id)
* Added Option: File field save format (file url / attachment id)
* Fixed bug: Location rules for post categories now work
* Added rule: Page parent
* Fixed bug: "what's new" button now shows the changelog
* included new css style to fit in with WordPress 3.2
* minor JS improvements

= 2.0.2 =
* Added new database table "acf_rules"
* Removed database table "ac_options"
* Updated location meta box to now allow for custom location queries
* Hid Activation Code from logged in users
* Fixed JS bugs with wp v3.2 beta 2
* Added new option "Field group layout" - you can now wrap your fields in a metabox!
* General housekeeping

= 2.0.1 =
* Added Field Option: Field Instructions
* Added Field Option: Is field searchable? (saves field value as a normal custom field so you can use the field against wp queries)
* Added Media Search / Pagination to Image / File thickbox
* Added Media Upload support to post types which do not have a Content Editor.
* Fixed "Select Image" / "Select File" text on thickbox buttons after upload
* Repeater field now returns null if no data was added

= 2.0.0 =
* Completely re-designed the ACF edit page
* Added repeater field (unlocked through external purchase)
* Fixed minor js bugs
* Fixed PHP error handling
* Fixed problem with update script not running
* General js + css improvements

= 1.1.4 =
* Fixed Image / File upload issues
* Location now supports category names
* Improved API - now it doesn't need any custom fields!
* Fixed table encoding issue
* Small CSS / Field changes to ACF edit screen


= 1.1.3 =
* Image Field now uses WP thickbox!
* File Field now uses WP thickbox!
* Page Link now supports multiple select
* All Text has been wrapped in the _e() / __() functions to support translations!
* Small bug fixes / housekeeping
* Added ACF_WP_Query API function

= 1.1.2 =
* Fixed WYSIWYG API format issue
* Fixed Page Link API format issue
* Select / Checkbox can now contain a url in the value or label
* Can now unselect all user types form field options
* Updated value save / read functions
* Lots of small bug fixes

= 1.1.1 =
* Fixed Slashes issue on edit screens for text based fields

= 1.1.0 =
* Lots of Field Type Bug Fixes
* Now uses custom database tables to save and store data!
* Lots of tidying up
* New help button for location meta box
* Added $post_id parameter to API functions (so you can get fields from any post / page)
* Added support for key and value for select and checkbox field types
* Re wrote most of the core files due to new database tables
* Update script should copy across your old data to the new data system
* Added True / False Field Type

= 1.0.5 =
* New Field Type: Post Object
* Added multiple select option to Select field type

= 1.0.4 =
* Updated the location options. New Override Option!
* Fixed un ticking post type problem
* Added JS alert if field has no type

= 1.0.3 =
* Heaps of js bug fixes
* API will now work with looped posts
* Date Picker returns the correct value
* Added Post type option to Page Link Field
* Fixed Image + File Uploads!
* Lots of tidying up!

= 1.0.2 =
* Bug Fix: Stopped Field Options from loosing data
* Bug Fix: API will now work with looped posts

= 1.0.1 =
* New Api Functions: get_fields(), get_field(), the_field()
* New Field Type: Date Picker
* New Field Type: File
* Bug Fixes
* You can now add multiple ACF's to an edit page
* Minor CSS + JS improvements

= 1.0.0 =
* Advanced Custom Fields.


== Upgrade Notice ==

= 3.0.0 =
* Editor is broken in WordPress 3.3

= 2.1.4 =
* Adds post_id column back into acf_values