<?php
/**
 * PageLine Inline Help System.
 *
 *
 * @author PageLines
 *
 * @since 2.0.b21
 */

class PageLines_Inline_Help {


	/**
	*
	* @TODO document
	*
	*/
	function __construct() {
		
		global $wp_version;
		if ( true == ( version_compare( $wp_version, '3.3-beta1', '>=' ) ) )	
			add_filter( 'contextual_help_list', array( &$this, 'get_help' ) ,9999);
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function get_help() {
		
		global $current_screen;		
		$this->screen = $current_screen;

		switch( $this->screen->id ) {
			
			case 'pagelines_page_pagelines_extend':
				$this->extend_help( array(
					'pagelines-store'	=> __( 'PageLines Store', 'pagelines' ),
					'integrations'		=> __( 'Integrations', 'pagelines' ),					
				));
			break;
					
			
			case 'pagelines_page_pagelines_special':
				$this->extend_help( array(
					'site-defaults'		=> __( 'About Site Defaults', 'pagelines' ),
					'special-about'		=> __( 'About Special Pages', 'pagelines' ),
					'special-blog'		=> __( 'Blog Page', 'pagelines' ),
					'special-archive'	=> __( 'Archive Page', 'pagelines' ),
					'special-category'	=> __( 'Category Page', 'pagelines' ),
					'special-search'	=> __( 'Search Results', 'pagelines' ),
					'special-tags'		=> __( 'Tag Listing', 'pagelines' ),
					'special-author'	=> __( 'Author Posts', 'pagelines' ),
					'special-404'		=> __( '404 Page', 'pagelines' )
					));
			break;
			
			case 'pagelines_page_pagelines_templates':
				$this->extend_help( array(
					'templates'		=> __( 'Drag & Drop', 'pagelines' ) 
					));
			break;
			
			case 'toplevel_page_pagelines':
			$this->extend_help( array(
				'welcome'		=> __( 'Welcome', 'pagelines' ),
				'website_setup'	=>	__( 'Website Setup', 'pagelines' ),
				'layout'		=> __( 'Layout Editor', 'pagelines' ),
				'color'			=> __( 'Color Control', 'pagelines' ),
				'typography'	=> __( 'Typography', 'pagelines' ),
				'header-footer'	=> __( 'Header and Footer', 'pagelines' ),
				'navbar'		=> __( 'NavBar', 'pagelines' ),
				'blog-posts'	=> __( 'Blog and Posts', 'pagelines' ),
				'advanced'		=> __( 'Advanced', 'pagelines' ),
				'custom'		=> __( 'Custom Code', 'pagelines' )
				));
			break;

			case 'pagelines_page_pagelines_account':
				$this->extend_help( array(
					'your_account'	=> __( 'Your Account', 'pagelines' ),
					'import'		=> __( 'Import-Export', 'pagelines' ),
				));							
			default:
			break;
		}

	}
	

	/**
	*
	* @TODO document
	*
	*/
	function extend_help( $helps ) {
		
		foreach( $helps as $id => $help ) {
			
			$this->screen->add_help_tab( array(
				'id'      => $id,
				'title'   => $help,
				'content' => $this->help_markup( $id ),
			));	
			$this->screen->set_help_sidebar(
		        '<p><strong>' . __( 'For more information:', 'pagelines' ) . '</strong></p>' .
		        '<p>' . sprintf( '<a href="http://www.pagelines.com/wiki/" target="_blank">%s</a>', __( 'Documentation', 'pagelines' ) ) . '</p>' .
		        '<p>' . sprintf( '<a href="http://www.pagelines.com/forum/" target="_blank">%s</a>', __( 'Support Forums', 'pagelines' ) ) . '</p>'
		);
		}	
	}


	/**
	*
	* @TODO document
	*
	*/
	function help_markup( $help ) {
		
		$markup = array(
			
			'welcome'			=>	__( '<p>Welcome to the PageLines Help Section! Here you can find a brief overview of each tab, as well as a link to a more detailed help doc.</p>', 'pagelines' ),
			
			'website_setup'		=>	__( "<p>Website Setup is generally the first thing people configure when they activate PageLines.<br />These are the options that get your logo, brand name, and other custom elements up on your site.<br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Website_Setup_Settings' target='_blank'>How to Use the Website Setup Settings</a></p>", 'pagelines' ),
			
			'layout'			=>	__( "<p>The Layout Editor is what changes the layout of your site. You can change the dimensions of your content, the number & location of your sidebar(s), etc... <br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Layout_Editor_Settings' target='_blank'>How to Use the Layout Editor Settings</a></p>", 'pagelines' ),
									
			'color'				=>	__( "<p>Color Control lets you choose the main colors that will be displayed on your website. It will then decide the best colors for your site's secondary and tertiary elements. You can always edit these manually by using CSS but Color Control chooses the best complementary colors to your site design. <br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Color_Control_Settings' target='_blank'>How to Use the Color Control Settings</a></p>", 'pagelines' ),
			
			'typography'		=>	__( "<p>Typography allows you to change the fonts that appear on your website. No need for html or css to make changes to the most common place that you might want to change your fonts. <br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Typography_Settings' target='_blank'>How to Use the Typography Settings</a></p>", 'pagelines' ),
			
			'header-footer'		=>	__( "<p>The Header & Footer  settings provide flexibility and ease in setting up important site content such as Dropdown Navigation, Search capability, Social links, and Copyright statements.<br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Header_and_Footer_Settings' target='_blank'>How to Use the Header and Footer Settings</a></p>", 'pagelines' ),
			
			'blog-posts'		=>	__( "<p>The Blog And Posts settings is where you can set up the general structure and appearance of your blog post content.<br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Blog_and_Posts_Settings' target='_blank'>How to Use the Blog and Posts Settings</a></p>", 'pagelines' ),
			
			'advanced'			=>	__( "<p>The Advanced settings contain some additional options that can be useful to solve some specific issues when developing your site. These include notorious browser compatibility issues with JS, server issues with Ajax and some useful other options for helping troubleshoot your site or connect with the affiliate program. <br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Advanced_Settings' target='_blank'>How to Use the Advanced Settings</a></p>", 'pagelines' ),
			
			'custom'			=>	__( "<p>The Custom Code setting is where you can insert your Custom CSS styling. If you have any Header, Footer, or Google Analytics script, all of that goes there as well. <br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Custom_Code_Settings' target='_blank'>How to Use the Custom Code Settings</a></p>", 'pagelines' ),
			
			'templates'			=> __( "<p>The Drag & Drop Settings are one of PageLine's most powerful features. Using drag and drop technology, you can easily place your sections wherever you want on your site. <br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_Template_Setup' target='_blank'>How to Use the Template Setup</a></p>", 'pagelines' ),
			
			'pagelines-store'	=>	__( "<p>The PageLines Store is your one stop online market place for everything you need to truely customize your website. Built by fellow members of the PageLines community, the Store offers the following types of components:<ul><li><strong>Sections -</strong> Drag and Drop pieces of web design that you can control on page templates</li><li><strong>Themes -</strong> The overall appearance and structure of the site; its visual presentation.</li><li><strong>Plugins -</strong> Extend the functionality of your website.</li></ul>For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_the_PageLines_Store' target='_blank'>How to Use the PageLines Store</a><br /><br />Interested in developing your own Sections/Themes/Plugins for the PageLines Store? Click on <a href='http://developer.pagelines.com/' target='_blank'>Getting Started as a Developer</a> for more information.</p>", 'pagelines' ),
				
				
			'integrations'		=>	__( "<p>If you purchased a PageLines Developer license, you have access to PageLines Integrations.<br />This allows you to use non-WordPress software on your website such as Vanilla forums & MediaWiki, and completely integrate it with the PageLines Framework. <br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_Integrations' target='_blank'>How to Use Integrations</a></p>", 'pagelines' ),
				
			'your_account'		=>	__( '<p>To be able to receive PageLines updates and purchase & download items from the Store, you must setup your account credentials.</p>', 'pagelines' ),	
			
			'import'			=>	__( "<p>You can easily transfer your PageLines 2.0 settings to another PageLines 2.0 site by using the Import-Export feature. Remember that this will only transfer your <em>PageLines</em> Settings. <br /><br />If you want to transfer your posts, pages, comments, custom fields, categories, and tags, you must use the WordPress Import-Export feature. This is located under 'Tools' inside your WordPress Administration Menu.<br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Migrate_PageLines_from_Another_Site' target='_blank'>How to Migrate PageLines from Another Site</a></p>", 'pagelines' ),
			
			'site-defaults'		=>	__( "<p>Site Defaults can be set for all active Sections on your site. They contain the exact same Section Settings found in a post or a page. Once you set a Site Default for a section, that setting will apply to the same section on any post or page that does not have the same <em>meta</em> setting set.  .<br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_Site_Defaults' target='_blank'>How to Use Site Defaults</a></p>", 'pagelines' ),
			
			
			'special-about'		=>	__( "<p>Special Pages refer to the WordPress pages which dynamically display data from your site. For example, your Blog is a Special Page, because it takes all of your individually entered posts and displays them on one page. <br /><br />Each type of Special Page has the same settings. Click on each tab to find out more on each page type.<br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_Use_Special_Pages' target='_blank'>How to Use the Special Pages</a></p>", 'pagelines' ),
									
			'special-blog'		=>	__( '<p>This page contains of all the blog posts on your site.</p>', 'pagelines' ),
			'special-archive'	=>	__( '<p>This page displays all blog posts created in a particular month. Users can select the month via the Archives Widget in the Sidebar.</</p>', 'pagelines' ),
			'special-category'	=>	__( '<p>If a blog post is assigned to a certain category, a user can click on that category and arrive at this page. It will contain all the other posts in that same category.</p>', 'pagelines' ),
			'special-search'	=>	__( '<p>When a user types in a search on your website, the Search Results page will appear containing the results.</p>', 'pagelines' ),
			'special-tags'		=>	__( '<p>You can assign tag words to a post. If a user clicks on the tag word, he will arrive at this page containing other posts with the same tag.</p>', 'pagelines' ),
			'special-author'	=>	__( '<p>This page will show all of the posts by a certain author. Clicking on the authors name will take the user there.</p>', 'pagelines' ),
			'special-404'		=>	__( "<p>If a user tries to go to a page on your site that doesn't exist, he will arrive at this page.</p>", 'pagelines' ),
			'navbar'			=>	__( "<p>The NavBar settings allow you to control the fixed navigation bar including themes, logo and menus.<br /><br />For more information, click on <a href='http://www.pagelines.com/wiki/How_to_use_NavBar' target='_blank'>How to Use the NavBar</a></p>", 'pagelines' )
		);
		
		return ( isset( $markup[$help] ) ) ? $markup[ $help ] : __( 'No help for this tab yet!', 'pagelines' );		
	}
	
} //end class
