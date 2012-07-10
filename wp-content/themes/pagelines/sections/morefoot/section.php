<?php
/*
	Section: Morefoot Sidebars
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Three widgetized sidebars above footer
	Class Name: PageLinesMorefoot	
	Workswith: morefoot, footer
	Edition: pro
	Persistant: true
*/

/**
 * Morefoot Sidebars Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesMorefoot extends PageLinesSection {

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		
		// Setup master array
		$this->master_array();
		
		// Register Section Sidebars
		foreach($this->master as $key => $i){

			pagelines_register_sidebar(
				array(
					'name'			=> $i['name'], 
					'description'	=> $i['description'], 
					'before_widget' => '<div id="%1$s" class="%2$s widget fix"><div class="widget-pad">',
				    'after_widget' => '</div></div>',
				    'before_title' => '<h3 class="widget-title">',
				    'after_title' => '</h3>'
				)
			);	
		}
	}

	/**
	* Section template.
	*/
   function section_template() { 
		
		$grid_args = array(
			'data'		=> 'array_callback',
			'callback'	=> array(&$this, 'morefoot_sidebar'), 
			'per_row'	=> 3

		);

		// Call the Grid
			printf('<div class="morefoot fix"><div class="morefoot-pad">%s</div></div>', grid( $this->master, $grid_args ));
	
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function morefoot_sidebar($sidebar, $args){
		
		ob_start();
		if(!dynamic_sidebar( $sidebar['name']))
			echo $sidebar['default'];
			
		return sprintf('<div class="morefoot-col"><div class="morefoot-col-pad blocks">%s</div></div>', ob_get_clean());
			
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function master_array(){
		
			$left = sprintf(
				'<div class="widget"><div class="widget-pad"><h3 class="widget-title">%s</h3><p>%s</p>%s<br class="clear"/><p>%s</p></div></div>', 
				__('Looking for something?','pagelines'),
				__('Use the form below to search the site:','pagelines'), 
				pagelines_search_form(false), 
				__("Still not finding what you're looking for? Drop us a note so we can take care of it!",'pagelines')
			);
			
			$middle = sprintf(
				'<div class="widget"><div class="widget-pad"><h3 class="widget-title">%s</h3><p>%s</p><ul>%s</ul></div></div>', 
				__('Visit our friends!','pagelines'),
				__('A few highly recommended friends...','pagelines'), 
				wp_list_bookmarks('title_li=&categorize=0&echo=0')
			);
			
			$right = sprintf(
				'<div class="widget"><div class="widget-pad"><h3 class="widget-title">%s</h3><p>%s</p><ul>%s</ul></div></div>', 
				__('Archives','pagelines'),
				__('All entries, chronologically...','pagelines'), 
				wp_get_archives('type=monthly&limit=12&echo=0')
			);
			
			$this->master = array(
				
				'left'	=> array(
					'name'			=> 'MoreFoot Left', 
					'description' 	=> __('Left sidebar in morefoot section.', 'pagelines'),
					'default'		=> $left
				),
				'middle'	=> array(
					'name'			=> 'MoreFoot Middle', 
					'description' 	=> __('Middle sidebar in morefoot section.', 'pagelines'),
					'default'		=> $middle
				),
				'right'	=> array(
					'name'			=> 'MoreFoot Right', 
					'description' 	=> __('Right sidebar in morefoot section.', 'pagelines'),
					'default'		=> $right
				),
			);		
	}
	

}

/*
	End of section class
*/
