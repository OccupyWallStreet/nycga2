=== CustomPress ===
Contributors: Ivan Shaovchev, Andrey Shipilov (Incsub), Arnold Bailey (Incsub)
Tags: post types, taxonomies, custom fields, wpmu, wordpress multisite
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 1.2.1
License: GPLv2 or later

Start by reading http://premium.wpmudev.org/wpmu-manual/using-regular-plugins-on-wpmu/ Installing plugins section in our comprehensive 
WordPress and WordPress Multisite Manual if you are new to WordPress.

===To install:===

1. Download the plugin file
2. Unzip the file into a folder on your hard drive
3. Upload `custompress` folder to the `/wp-content/plugins/` directory.

==To Activate:==

1. Login to your admin panel for WordPress or Multisite and activate the plugin: 

2. On regular WordPress installs - visit Plugins and Activate the plugin.
3. For WordPress Multisite installs - visit Network Admin -> Plugins and Network Activate the plugin.
 
===CustomPress and Multisite:===

Network Activation of CustomPress provides each site with the ability to access custom post types.  You can choose to set this up in one of the following ways:

== 1.  Network-wide Post Types. ==

In this scenario all sites on the network share a particular custom post type.  If you're running a book review network for example, all sites would need to have access to your "books" post type.  This is how CustomPress works by default.  After network activation, simply go to Network Admin->CustomPress->Settings and make sure the checkbox is cleared next to "Enable sub-site content types".  You'll also want to make sure you have the radio button marked next to "Keep the network-wide content types on sub-sites."

== 2.  Site-by-site Post Types ==

In this scenario, each individual site on your network can create and manage there own custom post types.  One site can have post types for books, the next can have post types for cars.  To do this, after network activation, simply go to Network Admin->CustomPress->Settings and check the box next to "Enable sub-site content types". Then mark the radio button next to "Remove the network-wide content types from sub-sites."

== 3. Both Network and site-by-site Post Types ==

Finally, if you want to provide a specific post type to all sites AND allow them to create their own go to Network Admin->CustomPress->Settings and check the box next to "Enable sub-site content types". Then mark the radio button next to "Keep the network-wide content types on sub-sites."

==Other common Questions:==

==But what if I want to enable CustomPress on specific sites only?==

The easiest way to do this is to simply log into your site via FTP and go to wp-content/plugins/custompress/loader.php  Simply open this file in your preferred code or text editor and delete line 10 - which simply has  Network: true
This feature is still being tested and is why we currently limit it to network activation by default.

==What if I want to only allow this plugin for Premium users with the Supporter plugin?==

You can use the same method mentioned above to delete the 'Network: true' portion and then mark the plugin as 'supporter only'.

==About Custom Posts:==
Custom posts are an easy way to create, edit, and store information In the same way as blog posts but with much more creative control.  They are ideal for situation where you want to categorize content into different categories.
Examples of where you might use a custom post are:

	* Movie Database
	* Book Database
	* Real Estate listings
	* Design Gallery

Creating a custom post involves four step:

Step 1: Create your Custom Post Type
Step 2: Create your Taxonomies
Step 3: Create your Custom Fields
Step 4: Create your custom template for your custom post type

==How To Create Your Custom Post Type==

When you create a custom post type using CustomPress the administration interface is the same as what you get with blog posts and pages except you choose what labels are used and which features are included, 
In this example we are creating a Book database that can be used for reviewing books, 

1.  Go to CustomPress -> Content Types 
	* The custom post type you create is visible to your normal site users, 
2.  Click on Add Post Type 
3.  Add your Post Type.  By default, Post types normally start with a Capital letter and are plural.
	* Choose wisely because once created the name can't be changed
4.  Choose which features you want included in your custom post administration interface
5.  In most situations you would leave the Capacity Type as post.
6.  Add your labels -- these are the names that appear in your custom post menu and custom post admin interfaces
7.  Add a short description of what your post type is.
8.  Choose where you want your new custom post menu to appear in the dashboard.
9.  Add the URL for where your icon is located (if you want to use your own custom icon).
10.  In most situations you would leave the default settings for Public, Show UI, Show In Nav Menu, Publicly Queryable, Exclude from Search, Hierarchical, Rewrite, Query var and Can Export, 
11.  Click Add Post Type at the bottom of the page.
12.  Your custom post will now be created and you should see the new menu items for your custom post in your admin interface.

==How To Create Your Taxomonies ==

Taxonomies are used to arrange, classify and group things. By default, Taxonomies in WordPress are tags and categories that WordPress uses for the post.  With custom posts you can create your own taxonomies.  
The purpose of the custom taxonomies is to give you the ability to organize your content in the best way you can think of.
For example, for our book database we are going to create Taxonomy for Genre and Author.

1.  Go to CustomPress -> Content Types
2.  Click on the Taxonomies tab
3.  Click on Add Taxonomy 
4.  Add your Taxonomy.  By default, Taxonomy normally start with a Capital letter and are singular.
* Choose wisely because once created the name can't be changed
5. Select your post type.
6.  Add your labels -- these are the names that appear in your custom post menu and custom post admin interfaces
7.  In most situations you would leave the default settings for Public, Show UI, Show Tagcloud,  Show in Nav menu, Heirarchical, Rewrite and Query var.
	*  One important setting is the "Hierarchical" option.  Setting this value to "true will enable the taxonomy to work like regular categories.  Setting the value to "false" will enable the taxonomy to work like regular tags.
8.  Click Add Taxonomy at the bottom of the page.
9.  Your Taxonomy will now be created and you should see the new menu items for your Taxonomy in your admin interface.
	* When you add your new Taxonomy it creates embed coded for your new custom post type.  
	* Simply copy the embed code and place it inside your WordPress loop to display the new taxonomy with your theme
==How To Create your Custom Fields==

Custom fields are used to insert custom user input field.  Your aim is to create field that you would like completed when some one write a custom post. 
For our book database we are going to create Custom Fields for Year and Book Rating.

1.  Go to CustomPress -> Content Types
2.  Click on the Custom Fields tab
3.  Click on Add Custom Field
4.  Add your Field Title
5. Add your field type
6. Select your Post Type
7. Click Add Custom Field
8.  Your Custom field will now be created and you should see the custom fields on your custom post admin interface.
	* When you add your new custom field it creates an embed code and shortcode for your new custom post type.  
	* Simply copy the embed code into your templates and place it inside your WordPress loop to display the new custom field with your theme
	* Use the shortcode in posts or widgets. Note that on multipost pages like you Blog page or archives the shortcode will only show the last post.
	* Embed codes and shortcode can display metadata about your custom field. For example 
		[ct id="_ct_radio_4f64ede7607ee" property="title"] would display the title of your custom field.
		[ct id="_ct_radio_4f64ede7607ee" property="description"] would display the description of your custom field.
		[ct id="_ct_radio_4f64ede7607ee" property="value"] would display the value of your custom field. If you leave property off the code displays the value by default.
 
==How to create your custom template for your custom post type==

This plugin includes the ability to create a custom template to display your custom post type.  Once created you can add the embed code for your taxonomies and custom field and these will be displayed in the post when you go to the Post URL.  
We'll be using the Theme Editor included in WordPress for sake of ease in this guide, however, using a proper code-editing program is always recommended.

1.  Go to CustomPress -> Settings
2. Select what type of posts you want to display in your Homepage.
3.  Select your Custom post name that you want to create a template file for
3.  Open up your FTP program and change your active theme folder permissions to 777.
4.  Click Save Changes
5.  Change your active theme folder permissions back to 755.
6.  Copy the  embed code for your Taxonomies and custom field by going to CustomPress -> Content Types (click on Taxonomies and Custom Field tabs)
7.  Go to Appearance -> Editor
8.  Click on your newly created custom template -- in this example it is single-books.php
9.  Place your Taxonomies and custom field embed code inside your WordPress loop in your newly created custom template
10.  Click Update File When you publish a custom post and go to the post URL it will now display your custom post template:

==Learn more about Custom Posts:==

If you want to obtain a better understanding of:

*  Custom Post Types and their power, you can go through this article on Codex.WordPress.org: http://codex.wordpress.org/Custom_Post_Types
* Custom Taxonomies and their application you can go through this article on Codex.WordPress.org: http://codex.wordpress.org/Custom_Taxonomies
* Custom Fields and their application you can go through this article on Codex.WordPress.org: http://codex.wordpress.org/Custom_Fields

18246-1344462992-ai