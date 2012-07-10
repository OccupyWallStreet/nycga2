<?php
/*
	Section: Secondary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The secondary widgetized sidebar for the theme.
	Class Name: SecondarySidebar	
	Workswith: sidebar1, sidebar2, sidebar_wrap
	Persistant: true
	Edition: pro
*/

/**
 * Secondary Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class SecondarySidebar extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name);
	}

}
