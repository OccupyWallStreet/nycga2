<?php
/*
	Section: PostNav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Post Navigation - Shows titles for next and previous post.
	Class Name: PageLinesPostNav	
	Workswith: main-single
	Cloning: true
	Failswith: pagelines_special_pages()
*/

/**
 * Next/Prev Post Navigation Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesPostNav extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() {
	
		pagelines_register_hook( 'pagelines_section_before_postnav' ); // Hook ?>
		<div class="post-nav fix"> 
			<span class="previous"><?php previous_post_link('%link') ?></span> 
			<span class="next"><?php next_post_link('%link') ?></span>
		</div>
<?php 	pagelines_register_hook( 'pagelines_section_after_postnav' ); // Hook 
	
	}
}
