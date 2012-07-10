<?php
/*
	Section: PostLoop
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The Main Posts Loop. Includes content and post information.
	Class Name: PageLinesPostLoop	
	Workswith: main
	Failswith: 404_page
*/

/**
 * Main Post Loop Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesPostLoop extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() { 
		//Included in theme root for easy editing.
		$theposts = new PageLinesPosts();
		$theposts->load_loop();
	}

}
