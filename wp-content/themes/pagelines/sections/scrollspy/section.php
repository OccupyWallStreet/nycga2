<?php
/*
	Section: Scroll Spy
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A special section with auto scroll content.
	Class Name: ScrollSpy
	Workswith: templates
	Cloning: false
	Failswith: pagelines_special_pages()
*/

/**
 * Content Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class ScrollSpy extends PageLinesSection {

	/**
	 * Load styles and scripts
	 */
	function section_styles(){
	
		wp_enqueue_script( 'scrollspy', $this->base_url.'/scrollspy.js', array( 'jquery' ) , false, true);		
	}
	
	/**
	* Section template.
	*/
	function section_template() {  
	
		global $post;
			
		printf('
			<div id="spynav" class="spynav">
	          <ul class="nav nav-pills"></ul>
	        </div><div class="spynav-space"></div>');
	 
	}

}
