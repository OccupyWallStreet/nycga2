<?php
/*
	Section: Posts Info
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows information about posts being viewed (e.g. "Currently Viewing Archives from...")
	Class Name: PageLinesPostsInfo
	Workswith: main
*/

/**
 * Posts Info Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesPostsInfo extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() { 	
	
		if( is_category() || is_archive() || is_search() || is_author() ):
			echo '<div class="current_posts_info">';
			if( is_search() ):
				printf( '%s <strong>"%s"</strong>', __( 'Search results for', 'pagelines' ), get_search_query() );
			elseif( is_category() ):
				printf( '%s <strong>"%s"</strong>', __( 'Currently viewing the category:', 'pagelines' ), single_cat_title( false, false ) );
			elseif( is_tag() ):
				printf( '%s <strong>"%s"</strong>', __( 'Currently viewing the tag:', 'pagelines' ), single_tag_title( false, false ) );
			elseif( is_archive() ):
			
				if (is_author()) { 
					global $author;
					global $author_name;
					$curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author ) );
					printf( '%s <strong>"%s"</strong>', __( 'Posts by:', 'pagelines' ), $curauth->display_name );
				} elseif ( is_day() ) {
					printf( '%s <strong>"%s"</strong>', __( 'From the daily archives:', 'pagelines' ), get_the_time('l, F j, Y') );
				} elseif ( is_month() ) {
					printf( '%s <strong>"%s"</strong>', __( 'From the monthly archives:', 'pagelines' ), get_the_time('F Y') );
				} elseif ( is_year() ) {
					printf( '%s <strong>"%s"</strong>', __( 'From the yearly archives:', 'pagelines' ), get_the_time('Y') );
				} else {
					if ( is_post_type_archive() )
						$title =  post_type_archive_title( null,false );
					if ( ! isset( $title ) ) {	
						$o = get_queried_object();
						if ( isset( $o->name ) )
							$title = $o->name;	
					}
					if ( ! isset( $title ) )
						$title = the_date();
					printf( '%s <strong>"%s"</strong>', __( 'Viewing archives for ', 'pagelines'), $title );
				}
				endif;
			echo '</div>';
		endif;
	}
}
