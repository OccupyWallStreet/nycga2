== views base ==

== Hooks== 

= header.php = 
before/after wp_head:
* views_base_before_header
* views_base_after_header

= footer.php = 
before/after wp_footer:
* views_base_before_footer
* views_base_after_footer
in div site-info:
* views_base_footer

= class_base_theme.php = 
in function theme_options:
* views_base_options

= sidebar.php =
* views_base_before_second_sidebar
* views_base_before_header_sidebar
* views_base_before_first_sidebar
* views_base_before_center_header_sidebar
* views_base_before_center_foot_sidebar
* views_base_before_foot_sidebar_1
* views_base_before_foot_sidebar_2
* views_base_before_foot_sidebar_3
* views_base_after_second_sidebar
* views_base_after_header_sidebar
* views_base_after_first_sidebar
* views_base_after_center_header_sidebar
* views_base_after_center_foot_sidebar
* views_base_after_foot_sidebar_1
* views_base_after_foot_sidebar_2
* views_base_after_foot_sidebar_3

== Changelog ==
= 1.0.3 =
* Update theme for WordPress 3.4

= 1.0.2 =
* Change the credit link to go to the right URL
* Show author info for blog posts only

= 1.0.1 =
* fix bug loosing "custom menu" and "background" settings. http://wp-types.com/forums/topic/views-base-feedback/

= 0.8 =
* Theme Name: Views Base
* Add readme.txt and theme docs