<?php
/*
	Section: Secondary Nav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates secondary site navigation.
	Class Name: PageLinesSecondNav
	Workswith: header, content
	Edition: pro
*/

/**
 * Secondary Nav Section
 *
 * @package PageLines Framework
 * @author PageLines
*/
class PageLinesSecondNav extends PageLinesSection {

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		
			$metatab_array = array(
					'_second_nav_menu' => array(
						'type' 			=> 'select_menu',			
						'title' 		=> 'Select Secondary Nav Menu',
						'shortexp' 		=> 'Select the menu you would like to use for your secondary nav.', 
						'inputlabel'	=> 'Select Secondary Navigation Menu'
					)
				);
				
			add_global_meta_options( $metatab_array );
		
	}

	/**
	* Section template.
	*/	
   	function section_template() { 

		$second_menu = (ploption('_second_nav_menu', $this->oset)) ? ploption('_second_nav_menu', $this->oset) : null;
		
		if(isset($second_menu)){
			
			wp_nav_menu( 
				array(
					'menu_class'  => 'secondnav_menu fix lcolor3', 
					'menu' => $second_menu, 
					'container' => null, 
					'container_class' => '', 
					'depth' => 1, 
					'fallback_cb'=>'pagelines_page_subnav'
				) 
			);
					
		}elseif(ploption('nav_use_hierarchy', $this->oset))
			pagelines_page_subnav();
	}
}
