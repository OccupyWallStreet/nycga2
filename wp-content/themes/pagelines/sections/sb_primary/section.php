<?php
/*
	Section: Primary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The main widgetized sidebar.
	Class Name: PrimarySidebar	
	Workswith: sidebar1, sidebar2, sidebar_wrap
	Persistant: true
*/

/**
 * Primary Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
*/
class PrimarySidebar extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name, 'includes/widgets.default');
	}

}
