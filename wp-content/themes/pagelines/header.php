<?php 
/**
 * HEADER
 *
 * This file controls the HTML <head> and top graphical markup (including
 * Navigation) for each page in your theme. You can control what shows up where
 * using WordPress and PageLines PHP conditionals.
 *
 * @package     PageLines Framework
 * @since       1.0
 *
 * @link        http://www.pagelines.com/
 * @link        http://www.pagelines.com/tour
 *
 * @author      PageLines   http://www.pagelines.com/
 * @copyright   Copyright (c) 2008-2012, PageLines  hello@pagelines.com
 *
 * @internal    last revised January 23, 2012
 * @version     ...
 *
 * @todo Define version
 */

pagelines_register_hook('pagelines_before_html'); // Hook
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php 		
		pagelines_register_hook('pagelines_head'); // Hook 
		
		wp_head(); // Hook (WordPress) 
			
		pagelines_register_hook('pagelines_head_last'); // Hook ?>

</head>
<?php 

echo pl_source_comment('Start >> HTML Body', 1); ?>
<body <?php body_class( pagelines_body_classes() ); ?>>
<?php 
pagelines_register_hook('pagelines_before_site'); // Hook
	
if(has_action('override_pagelines_body_output')):
	do_action('override_pagelines_body_output');

else:  ?>
<div id="site" class="<?php echo pagelines_layout_mode();?>">
<?php pagelines_register_hook('pagelines_before_page'); // Hook ?>
	<div id="page" class="thepage">
		<?php pagelines_register_hook('pagelines_page'); // Hook ?>
		<div class="page-canvas">
			<?php pagelines_register_hook('pagelines_before_header');?>
			<header id="header" class="container-group">
				<div class="outline">
					<?php pagelines_template_area('pagelines_header', 'header'); // Hook ?>
				</div>
			</header>
			<?php pagelines_register_hook('pagelines_before_main'); // Hook ?>
			<div id="page-main" class="container-group">
				<div id="dynamic-content" class="outline">	
<?php 
				pagelines_special_content_wrap_top();

endif;

