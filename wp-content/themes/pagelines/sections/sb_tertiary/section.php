<?php
/*
	Section: Tertiary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A 3rd widgetized sidebar for the theme that can be used in standard sidebar templates.
	Class Name: TertiarySidebar
	Workswith: sidebar1, sidebar2, sidebar_wrap
	Persistant: true
*/

/**
 * Tertiary Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class TertiarySidebar extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name);
	}
}
