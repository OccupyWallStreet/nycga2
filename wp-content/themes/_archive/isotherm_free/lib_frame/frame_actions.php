<?php 

/*-----------------------------------------------------------------------------------*/
/* Excerpt Length */
/*-----------------------------------------------------------------------------------*/
function bm_better_excerpt($length, $ellipsis) {
$text = get_the_content();
$text = strip_tags($text);
$text = substr($text, 0, $length);
$text = substr($text, 0, strrpos($text, " "));
$text = $text.$ellipsis;
return $text;
}

/*-----------------------------------------------------------------------------------*/
/* Reative Dates */
/*-----------------------------------------------------------------------------------*/
function relativeDate($posted_date) {
    
    $tz = 0;    // change this if your web server and weblog are in different timezones
                // see project page for instructions on how to do this
    
    $month = substr($posted_date,4,2);
    
    if ($month == "02") { // february
    	// check for leap year
    	$leapYear = isLeapYear(substr($posted_date,0,4));
    	if ($leapYear) $month_in_seconds = 2505600; // leap year
    	else $month_in_seconds = 2419200;
    }
    else { // not february
    // check to see if the month has 30/31 days in it
    	if ($month == "04" or 
    		$month == "06" or 
    		$month == "09" or 
    		$month == "11")
    		$month_in_seconds = 2592000; // 30 day month
    	else $month_in_seconds = 2678400; // 31 day month;
    }
  
/* 
some parts of this implementation borrowed from:
http://maniacalrage.net/archives/2004/02/relativedatesusing/ 
*/
  
    $in_seconds = strtotime(substr($posted_date,0,8).' '.
                  substr($posted_date,8,2).':'.
                  substr($posted_date,10,2).':'.
                  substr($posted_date,12,2));
    $diff = time() - ($in_seconds + ($tz*3600));
    $months = floor($diff/$month_in_seconds);
    $diff -= $months*2419200;
    $weeks = floor($diff/604800);
    $diff -= $weeks*604800;
    $days = floor($diff/86400);
    $diff -= $days*86400;
    $hours = floor($diff/3600);
    $diff -= $hours*3600;
    $minutes = floor($diff/60);
    $diff -= $minutes*60;
    $seconds = $diff;

    if ($months>0) {
        // over a month old, just show date ("Month, Day Year")
        echo ''; the_time('F jS, Y');
    } else {
        if ($weeks>0) {
            // weeks and days
            $relative_date .= ($relative_date?', ':'').$weeks.' '.stripslashes(__('week', 'bizzthemes')).''.($weeks>1?''.stripslashes(__('s', 'bizzthemes')).'':'');
            $relative_date .= $days>0?($relative_date?', ':'').$days.' '.stripslashes(__('day', 'bizzthemes')).''.($days>1?''.stripslashes(__('s', 'bizzthemes')).'':''):'';
        } elseif ($days>0) {
            // days and hours
            $relative_date .= ($relative_date?', ':'').$days.' '.stripslashes(__('day', 'bizzthemes')).''.($days>1?''.stripslashes(__('s', 'bizzthemes')).'':'');
            $relative_date .= $hours>0?($relative_date?', ':'').$hours.' '.stripslashes(__('hour', 'bizzthemes')).''.($hours>1?''.stripslashes(__('s', 'bizzthemes')).'':''):'';
        } elseif ($hours>0) {
            // hours and minutes
            $relative_date .= ($relative_date?', ':'').$hours.' '.stripslashes(__('hour', 'bizzthemes')).''.($hours>1?''.stripslashes(__('s', 'bizzthemes')).'':'');
            $relative_date .= $minutes>0?($relative_date?', ':'').$minutes.' '.stripslashes(__('minute', 'bizzthemes')).''.($minutes>1?''.stripslashes(__('s', 'bizzthemes')).'':''):'';
        } elseif ($minutes>0) {
            // minutes only
            $relative_date .= ($relative_date?', ':'').$minutes.' '.stripslashes(__('minute', 'bizzthemes')).''.($minutes>1?''.stripslashes(__('s', 'bizzthemes')).'':'');
        } else {
            // seconds only
            $relative_date .= ($relative_date?', ':'').$seconds.' '.stripslashes(__('minute', 'bizzthemes')).''.($seconds>1?''.stripslashes(__('s', 'bizzthemes')).'':'');
        }
        
        // show relative date and add proper verbiage
    	echo ''.stripslashes(__('Posted', 'bizzthemes')).' '.$relative_date.' '.stripslashes(__('ago', 'bizzthemes')).'';
    }
    
}

function isLeapYear($year) {
        return $year % 4 == 0 && ($year % 400 == 0 || $year % 100 != 0);
}

    if(!function_exists('how_long_ago')){
        function how_long_ago($timestamp){
            $difference = time() - $timestamp;

            if($difference >= 60*60*24*365){        // if more than a year ago
                $int = intval($difference / (60*60*24*365));
                $s = ($int > 1) ? ''.stripslashes(__('s', 'bizzthemes')).'' : '';
                $r = $int . ' '.stripslashes(__('year', 'bizzthemes')).'' . $s . ' '.stripslashes(__('ago', 'bizzthemes')).'';
            } elseif($difference >= 60*60*24*7*5){  // if more than five weeks ago
                $int = intval($difference / (60*60*24*30));
                $s = ($int > 1) ? ''.stripslashes(__('s', 'bizzthemes')).'' : '';
                $r = $int . ' '.stripslashes(__('month', 'bizzthemes')).'' . $s . ' '.stripslashes(__('ago', 'bizzthemes')).'';
            } elseif($difference >= 60*60*24*7){        // if more than a week ago
                $int = intval($difference / (60*60*24*7));
                $s = ($int > 1) ? ''.stripslashes(__('s', 'bizzthemes')).'' : '';
                $r = $int . ' '.stripslashes(__('week', 'bizzthemes')).'' . $s . ' '.stripslashes(__('ago', 'bizzthemes')).'';
            } elseif($difference >= 60*60*24){      // if more than a day ago
                $int = intval($difference / (60*60*24));
                $s = ($int > 1) ? ''.stripslashes(__('s', 'bizzthemes')).'' : '';
                $r = $int . ' '.stripslashes(__('day', 'bizzthemes')).'' . $s . ' '.stripslashes(__('ago', 'bizzthemes')).'';
            } elseif($difference >= 60*60){         // if more than an hour ago
                $int = intval($difference / (60*60));
                $s = ($int > 1) ? ''.stripslashes(__('s', 'bizzthemes')).'' : '';
                $r = $int . ' '.stripslashes(__('hour', 'bizzthemes')).'' . $s . ' '.stripslashes(__('ago', 'bizzthemes')).'';
            } elseif($difference >= 60){            // if more than a minute ago
                $int = intval($difference / (60));
                $s = ($int > 1) ? ''.stripslashes(__('s', 'bizzthemes')).'' : '';
                $r = $int . ' '.stripslashes(__('minute', 'bizzthemes')).'' . $s . ' '.stripslashes(__('ago', 'bizzthemes')).'';
            } else {                                // if less than a minute ago
                $r = ''.stripslashes(__('moments', 'bizzthemes')).' '.stripslashes(__('ago', 'bizzthemes')).'';
            }

            return $r;
        }
    }

/* 
Get post TAGS for specified post ID and list them in correct order
*/	
function bizzthemes_get_post_tags($post_id = false) {
	if ($post_id) {
		$tags_objects = wp_get_post_tags($post_id);
		
		if ($tags_objects) {
			foreach ($tags_objects as $tag_object)
				$tags[] = $tag_object->name;
			
			return $tags;
		}
	}
}

/*
Plugin Name: WP-PageNavi
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Adds a more advanced paging navigation to your WordPress blog.
Version: 2.50
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
*/

function bizz_wp_pagenavi($before = '', $after = '') {
    global $wpdb, $wp_query;
    if (!is_single()) {
        $request = $wp_query->request;
        $posts_per_page = intval(get_query_var('posts_per_page'));
        $paged = intval(get_query_var('paged'));
        $pagenavi_options = get_option('pagenavi_options');
        $numposts = $wp_query->found_posts;
        $max_page = $wp_query->max_num_pages;
		
        if(empty($paged) || $paged == 0) {
            $paged = 1;
        }
        $pages_to_show = intval($pagenavi_options['num_pages']);
        $pages_to_show_minus_1 = $pages_to_show-1;
        $half_page_start = floor($pages_to_show_minus_1/2);
        $half_page_end = ceil($pages_to_show_minus_1/2);
        $start_page = $paged - $half_page_start;
        if($start_page <= 0) {
            $start_page = 1;
        }
        $end_page = $paged + $half_page_end;
        if(($end_page - $start_page) != $pages_to_show_minus_1) {
            $end_page = $start_page + $pages_to_show_minus_1;
        }
        if($end_page > $max_page) {
            $start_page = $max_page - $pages_to_show_minus_1;
            $end_page = $max_page;
        }
        if($start_page <= 0) {
            $start_page = 1;
        }
        if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
            echo $before.'<ul class="lpag">'."\n";
            switch(intval($pagenavi_options['style'])) {
                case 1:                   
                    if ($start_page >= 2 && $pages_to_show < $max_page) {
                        $first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), '&laquo; '.stripslashes(__('First', 'bizzthemes')));
                        echo '<li><a href="'.esc_url(get_pagenum_link()).'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
                        if(!empty($pagenavi_options['dotleft_text'])) {
                            echo '<li>'.$pagenavi_options['dotleft_text'].'</li>';
                        }
                    }
					echo '<li>'."\n";
                    previous_posts_link($pagenavi_options['prev_text']);
					echo '</li>'."\n";
                    for($i = $start_page; $i  <= $end_page; $i++) {                        
                        if($i == $paged) {
                            $current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
                            echo '<li class="current"><span>'.$current_page_text.'</span></li>';
                        } else {
                            $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                            echo '<li><a href="'.esc_url(get_pagenum_link($i)).'" title="'.$page_text.'">'.$page_text.'</a></li>';
                        }
                    }
					echo '<li>'."\n";
                    next_posts_link($pagenavi_options['next_text'], $max_page);
					echo '</li>'."\n";
                    if ($end_page < $max_page) {
                        if(!empty($pagenavi_options['dotright_text'])) {
                            echo '<li>'.$pagenavi_options['dotright_text'].'</li>';
                        }
                        $last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), ''.stripslashes(__('Last', 'bizzthemes')).' &raquo;');
                        echo '<li><a href="'.esc_url(get_pagenum_link($max_page)).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
                    }
                    break;
                case 2;
                    echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="get">'."\n";
                    echo '<select size="1" onchange="document.location.href = this.options[this.selectedIndex].value;">'."\n";
                    for($i = 1; $i  <= $max_page; $i++) {
                        $page_num = $i;
                        if($page_num == 1) {
                            $page_num = 0;
                        }
                        if($i == $paged) {
                            $current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
                            echo '<option value="'.esc_url(get_pagenum_link($page_num)).'" selected="selected" class="current">'.$current_page_text."</option>\n";
                        } else {
                            $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                            echo '<option value="'.esc_url(get_pagenum_link($page_num)).'">'.$page_text."</option>\n";
                        }
                    }
                    echo "</select>\n";
                    echo "</form>\n";
                    break;
            }
            echo '</ul>'.$after."\n";
        }
    }
}

add_action('init', 'bizz_wp_pagenavi_init');
function bizz_wp_pagenavi_init() {
    // Add Options
    $pagenavi_options = array();
    $pagenavi_options['current_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['page_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['first_text'] = __('&laquo; '.stripslashes(__('First', 'bizzthemes')).'','wp-pagenavi');
    $pagenavi_options['last_text'] = __(''.stripslashes(__('Last', 'bizzthemes')).' &raquo;','wp-pagenavi');
    $pagenavi_options['next_text'] = __('&raquo;','wp-pagenavi');
    $pagenavi_options['prev_text'] = __('&laquo;','wp-pagenavi');
    $pagenavi_options['dotright_text'] = __('...','wp-pagenavi');
    $pagenavi_options['dotleft_text'] = __('...','wp-pagenavi');
    $pagenavi_options['style'] = 1;
    $pagenavi_options['num_pages'] = 5;
    $pagenavi_options['always_show'] = 0;
	add_option('pagenavi_options', $pagenavi_options);
}

// Related Posts, tutorial found on: http://curtishenson.com/write-your-own-related-posts-plugin/

function bizz_get_related($post) {

    global $wpdb;
    
	$now = current_time('mysql', 1);
    $tags = wp_get_post_tags($post->ID);
    $show_date = 0;
    $limit = 3;
    $show_comments_count = 0;
            
    $taglist = "'" . $tags[0]->term_id. "'";
    $tagcount = count($tags);
    if ($tagcount > 1) {
        for ($i = 1; $i <= $tagcount; $i++) {
            $taglist = $taglist . ", '" . $tags[$i]->term_id . "'";
        }
    }
                            
    $q = "SELECT p.ID, p.post_title, p.post_date, p.comment_count, count(t_r.object_id) as cnt FROM $wpdb->term_taxonomy t_t, $wpdb->term_relationships t_r, $wpdb->posts p WHERE t_t.taxonomy ='post_tag' AND t_t.term_taxonomy_id = t_r.term_taxonomy_id AND t_r.object_id  = p.ID AND (t_t.term_id IN ($taglist)) AND p.ID != $post->ID AND p.post_status = 'publish' AND p.post_date_gmt < '$now' GROUP BY t_r.object_id ORDER BY cnt DESC, p.post_date_gmt DESC LIMIT $limit;";
    $related_posts = $wpdb->get_results($q);
	
        foreach ($related_posts as $related_post ){
            $output .= '<p class="rellist">&rsaquo;&nbsp;';
                    
                if ($show_date){
                    $dateformat = get_option('date_format');
                    $output .=   mysql2date($dateformat, $related_post->post_date) . " -- ";
                }
                    
                $output .=  '<a href="'.get_permalink($related_post->ID).'" title="'.wptexturize($related_post->post_title).'">'.wptexturize($related_post->post_title).'';
                    
                if ($show_comments_count){
                    $output .=  " (" . $related_post->comment_count . ")";
                }
                    
                $output .=  '</a></p>';
        }

    $output = '' . $output . '';
    return $output;   
}      

// add LAST class to page and category lists
function add_last_class($input) {
	if( !empty($input) ) {

		$pattern = '/<li class="(?!.*<li class=")/is';
		$replacement = '<li class="last ';

		$input = preg_replace($pattern, $replacement, $input);

		echo $input;
	}
}

/*
Plugin Name:  Yoast Breadcrumbs
Plugin URI:   http://yoast.com/wordpress/breadcrumbs/
*/

function bizz_breadcrumb($prefix = '', $suffix = '', $display = true) {
	global $wp_query, $post, $curauth;
	
	$prefix = '<div id="breadcrumb"><p>';
	$suffix = '</p></div>';
	
	function bold_or_not_bizz($input) {
		if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs_boldlast']) ) {
			return '<strong>'.$input.'</strong>';
		} else {
			return $input;
		}
	}

	// Copied and adapted from WP source
	function bizz_get_category_parents($id, $link = FALSE, $separator = '/', $nicename = FALSE){
		$chain = '';
		$parent = &get_category($id);
		if ( is_wp_error( $parent ) )
		   return $parent;

		if ( $nicename )
		   $name = $parent->slug;
		else
		   $name = $parent->cat_name;

		if ( $parent->parent && ($parent->parent != $parent->term_id) )
		   $chain .= get_category_parents($parent->parent, true, $separator, $nicename);

		$chain .= bold_or_not_bizz($name);
		return $chain;
	}
	
	$nofollow = ' ';
	if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs_nofollowhome']) ) {
		$nofollow = ' rel="nofollow" ';
	}
	
	$on_front = get_option('show_on_front');
	
	if ($on_front == "page") {
		$homelink = '<a'.$nofollow.'href="'.get_permalink(get_option('page_on_front')).'">'.$GLOBALS['opt']['bizzthemes_breadcrumbs_home'].'</a>';
		$bloglink = $homelink.' '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' <a href="'.get_permalink(get_option('page_for_posts')).'">'.$GLOBALS['opt']['bizzthemes_breadcrumbs_blog'].'</a>';
	} else {
		$homelink = '<a'.$nofollow.'href="'.get_bloginfo('url').'">'.$GLOBALS['opt']['bizzthemes_breadcrumbs_home'].'</a>';
		$bloglink = $homelink;
	}
	
	$templateid = get_post_meta($wp_query->post->ID, '_wp_page_template'); // check for page template
		
	if ( ($on_front == "page" && is_front_page()) || ($on_front == "posts" && is_home()) ) {
		$output = bold_or_not_bizz($GLOBALS['opt']['bizzthemes_breadcrumbs_home']);
	} elseif ( $on_front == "page" && is_home() ) {
		$output = $homelink.' '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' '.bold_or_not_bizz($GLOBALS['opt']['bizzthemes_breadcrumbs_blog']);
	} elseif ( !is_page() ) {
		$output = $bloglink.' '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' ';
		if ( ( is_category() || is_tag() || is_date() || is_author() || ( isset($templateid['0']) && ($templateid['0'] == '/template-blog.php' || $templateid['0'] == 'template-blog.php') ) ) && isset($GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent']) && $GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent'] != 0 && !is_page(''.$GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent'].'') ) {
			$output .= '<a href="'.get_permalink($GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent']).'">'.get_the_title($GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent']).'</a> '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' ';
		} 
		if ( is_single() && isset($GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent']) && $GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent'] != 0 && get_post_type( $wp_query->post->ID ) == 'post' )
			$output .= '<a href="'.get_permalink($GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent']).'">'.get_the_title($GLOBALS['opt']['bizzthemes_breadcrumbs_singleparent']).'</a> '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' ';
		if ( is_single() && isset($GLOBALS['opt']['bizzthemes_breadcrumbs_faqparent']) && $GLOBALS['opt']['bizzthemes_breadcrumbs_faqparent'] != 0 && get_post_type( $wp_query->post->ID ) == 'faqs' )
			$output .= '<a href="'.get_permalink($GLOBALS['opt']['bizzthemes_breadcrumbs_faqparent']).'">'.get_the_title($GLOBALS['opt']['bizzthemes_breadcrumbs_faqparent']).'</a> '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' ';
		
		if (is_single() && isset($GLOBALS['opt']['bizzthemes_breadcrumbs_singlecatprefix']) ) {
		
		$cats = get_the_category();
		$post_terms = get_the_term_list( $post->ID, 'faq_category', '', ', ', '' );
		    if ($cats) {		
			    $cat = $cats[0];
			    if ( is_object($cat) ) {
				    if ($cat->parent != 0) {
					    $output .= get_category_parents($cat->term_id, true, " ".$GLOBALS['opt']['bizzthemes_breadcrumbs_sep']." ");
				    } else {
					    $output .= '<a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a> ';
						if (!is_single())
						   $output .= $GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' ';
				    }
			    }
		    } elseif ($post_terms) {
		        $output .= $post_terms;
		    }
	    }
		if ( is_category() ) {
			$cat = intval( get_query_var('cat') );
			$output .= bizz_get_category_parents($cat, false, " ".$GLOBALS['opt']['bizzthemes_breadcrumbs_sep']." ");
		} elseif ( is_tag() ) {
			$output .= bold_or_not_bizz($GLOBALS['opt']['bizzthemes_breadcrumbs_archiveprefix']." ".single_cat_title('',false));
		} elseif ( is_date() ) { 
			$output .= bold_or_not_bizz($GLOBALS['opt']['bizzthemes_breadcrumbs_archiveprefix']." ".single_month_title(' ',false));
		} elseif ( is_author() ) { 
			$user = get_the_author_meta('display_name', get_query_var('author'));
			$output .= bold_or_not_bizz($GLOBALS['opt']['bizzthemes_breadcrumbs_archiveprefix']." ".$user);
		} elseif ( is_search() ) {
			$output .= bold_or_not_bizz($GLOBALS['opt']['bizzthemes_breadcrumbs_searchprefix'].' "'.get_search_query().'"');
		} else if ( is_tax() ) {
			$taxonomy 	= get_taxonomy ( get_query_var('taxonomy') );
			$taxonomy 	= get_taxonomy ( get_query_var('taxonomy') );
			if ( get_query_var('taxonomy') && $GLOBALS['opt']['bizzthemes_breadcrumbs_faqparent'] != 0 && (get_post_type( $wp_query->post->ID ) == 'faqs') )
			    $output .= '<a href="'.get_permalink($GLOBALS['opt']['bizzthemes_breadcrumbs_faqparent']).'">'.get_the_title($GLOBALS['opt']['bizzthemes_breadcrumbs_faqparent']).'</a> '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' ';
			$term_id    = get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
			$output .= $taxonomy->label .': '.bold_or_not_bizz( $term_id->name );
		} else {
		    if (!is_single())
			    $output .= bold_or_not_bizz(get_the_title());
		}
	} else {
		$post = $wp_query->get_queried_object();

		// If this is a top level Page, it's simple to output the breadcrumb
		if ( 0 == $post->post_parent ) {
			$output = $homelink." ".$GLOBALS['opt']['bizzthemes_breadcrumbs_sep']." ".bold_or_not_bizz(get_the_title());
		} else {
			if (isset($post->ancestors)) {
				if (is_array($post->ancestors))
					$ancestors = array_values($post->ancestors);
				else 
					$ancestors = array($post->ancestors);				
			} else {
				$ancestors = array($post->post_parent);
			}

			// Reverse the order so it's oldest to newest
			$ancestors = array_reverse($ancestors);

			// Add the current Page to the ancestors list (as we need it's title too)
			$ancestors[] = $post->ID;

			$links = array();			
			foreach ( $ancestors as $ancestor ) {
				$tmp  = array();
				$tmp['title'] 	= strip_tags( get_the_title( $ancestor ) );
				$tmp['url'] 	= get_permalink($ancestor);
				$tmp['cur'] = false;
				if ($ancestor == $post->ID) {
					$tmp['cur'] = true;
				}
				$links[] = $tmp;
			}

			$output = $homelink;
			foreach ( $links as $link ) {
				$output .= ' '.$GLOBALS['opt']['bizzthemes_breadcrumbs_sep'].' ';
				if (!$link['cur']) {
					$output .= '<a href="'.$link['url'].'">'.$link['title'].'</a>';
				} else {
					$output .= bold_or_not_bizz($link['title']);
				}
			}
		}
	}
	if ($GLOBALS['opt']['bizzthemes_breadcrumbs_prefix'] != "") {
		$output = $GLOBALS['opt']['bizzthemes_breadcrumbs_prefix']."&nbsp;".$output;
	}
	if ($display) {
		echo $prefix.$output.$suffix;
	} else {
		return $prefix.$output.$suffix;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Original Addon Author: WooThemes */
/* Author URI: http://woothemes.com */
/* License: GPL */
/* About: Modified WooThemes script for resizing images with thumb.php script */
/*-----------------------------------------------------------------------------------*/

function bizz_get_image($key = 'image', $width = null, $height = null, $class = "thumbnail", $quality = 90,$id = null,$link = 'src',$repeat = 1,$offset = 0,$before = '', $after = '',$single = false, $force = false, $return = false) {
	// Run new function
	bizz_image( 'key='.$key.'&width='.$width.'&height='.$height.'&class='.$class.'&quality='.$quality.'&id='.$id.'&link='.$link.'&repeat='.$repeat.'&offset='.$offset.'&before='.$before.'&after='.$after.'&single='.$single.'&force='.$force.'&return='.$return );
	return;
}

function bizz_image($args) {
	global $post;
	
	//Defaults
	$key = 'image'; // Custom field key eg. "image"
	$width = null; // Set width manually without using $type
	$height = null; // Set height manually without using $type
	$class = ''; // CSS class to use on the img tag eg. "alignleft". Default is "thumbnail"
	$quality = 90; // Enter a quality between 80-100. Default is 90
	$id = null; // Assign a custom ID, if alternative is required.
	$link = 'src'; // Echo with image links ('src') or just echo as image ('img').
	$repeat = 1; // Auto Img Function. Adjust amount of images to return for the post attachments.
	$offset = 0; // Auto Img Function. Offset the $repeat with assigned amount of objects.
	$before = ''; // Auto Img Function. Add Syntax before image output.
	$after = ''; // Auto Img Function. Add Syntax after image output.
	$single = false; // Auto Img Function Only. Forces "img" return on images, like on single.php template
	$force = false; // Force smaller images to not be effected with image width and height dimentions (proportions fix)
	$return = false; // Return results instead of echoing out.
	$is_auto_image = false; // A parameter that accepts a img url for resizing. (No anchor)
	$src = ''; // A parameter that accepts a img url for resizing. (No anchor)
	$auto_meta = true; // Disables meta generated by the post_id. When src is used, this setting is automatically set to false.
	$meta = ''; // Add a custom meta text to the image and anchor of the image.
	
	$alt = 'alt=""';
	
	$attachment_id = array();
	$attachment_src = array();
	$thumb_id = get_post_meta($post->ID,'_thumbnail_id',true);
	$thumb_url = wp_get_attachment_image_src($thumb_id,'large');  
	$thumb_url = $thumb_url[0];
	$meta_id = get_post_meta($post->ID, $key, true);
	
	if ( !is_array($args) ) 
		parse_str( $args, $args );
	
	extract($args);
	
    if ( empty($id) )
		$id = $post->ID;

	if ( $src != '' ) { // When a custom image is sent through
		$custom_field = $src;
		$link = 'img';
		$auto_meta = false;
	} elseif( isset($GLOBALS['opt']['bizzthemes_thumb_show']) && !empty($thumb_url) ){
		$thumb_field = $thumb_url;		
	} else {
    	$custom_field = $meta_id;
	} 

	if ( empty($custom_field) && empty($thumb_field) && isset($GLOBALS['opt']['bizzthemes_auto_img']) ) { // Get the image from post attachments
        
        if( $offset >= 1 ) 
			$repeat = $repeat + $offset;
    
        $attachments = get_children( array(	'post_parent' => $id,
											'numberposts' => $repeat,
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'order' => 'DESC', 
											'orderby' => 'menu_order date')
											);

		if ( !empty($attachments) ) { // Search for and get the post attachment
       
			$counter = -1;
			$size = 'large';
			foreach ( $attachments as $att_id => $attachment ) {            
				$counter++;
				if ( $counter < $offset ) 
					continue;
			
				$src = wp_get_attachment_image_src($att_id, $size, true);
				$custom_field = $src[0];
				$is_auto_image = true;
				$attachment_id[] = $att_id;
				$src_arr[] = $custom_field;
			}

		} else { // Get the first img tag from the content

			$first_img = '';
			$post = get_post($id); 
			ob_start();
			ob_end_clean();
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			if ( !empty($matches[1][0]) )
				$custom_field = $matches[1][0];

		}
		
	}
	
	// Return if there is no custom field and no thumbnail set
	if ( empty($custom_field) && empty($thumb_field) ) 
        return;
	
	if(empty($src_arr) && empty($custom_field) ){ 
	    $src_arr[] = $thumb_field; 
	} elseif(empty($src_arr)){
	    $src_arr[] = $custom_field;
	}
	
    $output = '';

	// Get standard sizes
	if ( !$width && !$height ) {
		$width = '100';
		$height = '100';
	}
	
    $set_width = ' width="' . $width .'" ';
    $set_height = ' height="' . $height .'" '; 
    
    if($height == null OR $height == '')
        $set_height = '';
		
	// Set standard class
	if ( $class )
		$class = 'bizz-thumb ' . $class;
	else 
		$class = 'bizz-thumb';
		
	// Do check to verify if images are smaller then specified.
	if($force == true){  
		$set_width = '';
		$set_height = '';
	}

	// RESIZE IMAGES AUTOMATICALLY
	if ( isset($GLOBALS['opt']['bizzthemes_resize']) ) {
	
		foreach($src_arr as $key => $custom_field){
	
			// Clean the image URL
			$href = $custom_field; 		
			$custom_field = cleanSource( $custom_field );

			// Check if WPMU and set correct path
			if ( function_exists('get_current_site') ) {
				global $blog_id;
				if ( !$blog_id ) {
					global $current_blog;
					$blog_id = $current_blog->blog_id;				
				}
				if ( isset($blog_id) && $blog_id > 0 ) {
					$imageParts = explode( 'files/', $custom_field );
					if ( isset($imageParts[1]) ) 
						$custom_field = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
				}
			}
		
			//Set the ID to the Attachent's ID if it is an attachment
			if($is_auto_image == true){	
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}
			
			if($auto_meta == true) {
				$alt = 'alt="'. get_the_title($quick_id) .'"';
				$title = 'title="'. get_the_title($quick_id) .'"';
			} elseif($auto_meta == false) {
				$alt = 'alt="'. $meta.'"';
				$title = 'title="'. $meta .'"';
			} else {
				$alt = 'alt=""';
				$title = '';
			}
			
			$img_link = '<img src="'. BIZZ_FRAME_ROOT . '/thumb.php?src='. $custom_field .'&amp;w='. $width .'&amp;h='. $height .'&amp;zc=1&amp;q='. $quality .'" '.$alt.' class="'. stripslashes($class) .'" '. $set_width . $set_height .' />';
			
			if( $link == 'img' ) {  // Just output the image
				$output .= $before; 
				$output .= $img_link;
				$output .= $after;  
				
			} else {  // Default - output with link				

				if ( ( is_single() OR is_page() ) AND $single == false ) {
					$rel = 'rel="lightbox"';
				} else { 
					$href = get_permalink($id);
					$rel = '';
				}
			
				$output .= $before; 
				$output .= '<a '.$title.' href="' . $href .'" '.$rel.'>' . $img_link . '</a>';
				$output .= $after;  
			}
		}
		
	// DO NOT RESIZE IMAGES AUTOMATICALLY
	} else {
		
		foreach($src_arr as $key => $custom_field){
		
			//Set the ID to the Attachent's ID if it is an attachment
			if($is_auto_image == true){	
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}
			
			if($auto_meta == true) {
				$alt = 'alt="'. get_the_title($quick_id) .'"';
				$title = 'title="'. get_the_title($quick_id) .'"';
			} elseif($auto_meta == false) {
				$alt = 'alt="'. $meta.'"';
				$title = 'title="'. $meta .'"';
			} else {
				$alt = 'alt=""';
				$title = '';
			}
		
			$img_link =  '<img src="'. $custom_field .'" '. $alt .' '. $set_width . $set_height . ' class="'. stripslashes($class) .'" '. $set_width . $set_height .' />';
		
			if ( $link == 'img' ) {  // Just output the image 
				$output .= $before;                   
				$output .= $img_link; 
				$output .= $after;  
				
			} else {  // Default - output with link
			
				if ( ( is_single() OR is_page() ) AND $single == false ) { 
					$href = $custom_field;
					$rel = 'rel="lightbox"';
				} else { 
					$href = get_permalink($id);
					$rel = '';
				}
				 
				$output .= $before;   
				$output .= '<a '. $alt .' href="' . $href .'" '. $rel .'>' . $img_link . '</a>';
				$output .= $after;   
			}
		}
	}
	
	// Return or echo the output
	if ( $return == TRUE )
		return $output;
	else 
		echo $output; // Done  

}

/*-----------------------------------------------------------------------------------*/
/* Tidy up the image source url */
/* Original Addon Author: WooThemes */
/*-----------------------------------------------------------------------------------*/
function cleanSource($src) {

	// remove slash from start of string
	if(strpos($src, "/") == 0) {
		$src = substr($src, -(strlen($src) - 1));
	}

	// Check if same domain so it doesn't strip external sites
	$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	if ( !strpos($src,$host) )
		return $src;


	$regex = "/^((ht|f)tp(s|):\/\/)(www\.|)" . $host . "/i";
	$src = preg_replace ($regex, '', $src);
	$src = htmlentities ($src);
    
    // remove slash from start of string
    if (strpos($src, '/') === 0) {
        $src = substr ($src, -(strlen($src) - 1));
    }
	
	return $src;
}

/*-----------------------------------------------------------------------------------*/
/* Show image in RSS feed */
/* Original code by Justin Tadlock http://justintadlock.com */
/*-----------------------------------------------------------------------------------*/

if (
isset($GLOBALS['opt']['bizzthemes_image_rss']) &&
$GLOBALS['opt']['bizzthemes_image_rss'] == "true"
)
	add_filter('the_content', 'add_image_RSS');
	
function add_image_RSS( $content ) {
	
	global $post, $id;
	$blog_key = substr( md5( get_bloginfo('url') ), 0, 16 );
	if ( ! is_feed() ) return $content;

	// Get the "image" from custom field
	$image = get_post_meta($post->ID, 'image', $single = true);
	$image_width = '200';

	// If there's an image, display the image with the content
	if($image !== '') {
		$content = '<p style="float:right; margin:0 0 10px 15px; width:'.$image_width.'px;">
		<img src="'.$image.'" width="'.$image_width.'" />
		</p>' . $content;
		return $content;
	} 

	// If there's not an image, just display the content
	else {
		$content = $content;
		return $content;
	}
	
}

/*-----------------------------------------------------------------------------------*/
/* Theme head options */
/*-----------------------------------------------------------------------------------*/
add_action( 'bizz_head_before', 'bizz_head_options' );
function bizz_head_options() {
	
    echo '<meta http-equiv="Content-Type" content="' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset') . '" />'."\n";
	echo '<link rel="pingback" href="' . get_bloginfo('pingback_url') . '" />'."\n";
	if (!is_admin()){ if ( is_singular() && comments_open() && (get_option('thread_comments') == 1)) wp_enqueue_script( 'comment-reply' ); }
	
}

/*-----------------------------------------------------------------------------------*/
/* Theme activation alert (if options are not saved yet) */
/*-----------------------------------------------------------------------------------*/
add_action('bizz_body_after', 'bizz_activation_alert');
function bizz_activation_alert() {
    if ( ($GLOBALS['opt']['themeid'] != $GLOBALS['themeid']) or (empty($GLOBALS['opt'])) ) {
        echo '<div class="activation">'.stripslashes(__('Theme configuration is not saved!<br/></br><br/><small>After theme activation go to theme options panel,<br/>where you may set up your theme ;)', 'bizzthemes')).'</small></div>';
        die; 
    }
}

/*-----------------------------------------------------------------------------------*/
/* Theme comments */
/*-----------------------------------------------------------------------------------*/
function bizz_comments() {

    global $post, $wp_query;
	
    // Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('' . __('Please do not load this page directly. Thanks!', 'bizzthemes') . '');
	if ( post_password_required() ) { 
	    echo "<p>" . __('This post is password protected. Enter the password to view comments.', 'bizzthemes') . "</p>\n";
    return; } 
		
	$wp_query->comments_by_type = &separate_comments($wp_query->comments);
	$comments_by_type_comment = $wp_query->comments_by_type['comment'];
	$comments_by_type_pings = $wp_query->comments_by_type['pings'];
	
?>

<!-- You can start editing here. -->

<div id="comments">
<?php if ( have_comments() ) : ?>

	<?php if ( ! empty($comments_by_type_comment) ) : ?>
	    <h3 class="tcomm"><?php comments_number(''.stripslashes(__('No Comments', 'bizzthemes')).'', ''.stripslashes(__('One Comment', 'bizzthemes')).'', ''.stripslashes(__('% Comments', 'bizzthemes')).''); ?> &rarr; &#8220;<?php the_title(); ?>&#8221;</h3>
	    <ol class="commentlist">
	        <?php wp_list_comments('avatar_size=48&callback=custom_comment&type=comment'); ?>
	    </ol>
		<div class="navigation clearfix">
		    <div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
	<?php endif; ?>
	<?php if ( !empty($comments_by_type_pings) ) : ?>
	    <h3 class="tcomm"><?php echo stripslashes(__('Trackbacks For This Post', 'bizzthemes')); ?></h3>
		<ol class="commentlist">
		    <?php wp_list_comments('type=pings&callback=list_pings'); ?>
		</ol>
    <?php endif; ?>

<?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php echo stripslashes(__('Comments are closed.', 'bizzthemes')); ?></p>
	<?php endif; ?>

<?php endif; ?>
</div> <!-- /#comments_wrap -->

<?php if ('open' == $post->comment_status) : ?>
<?php global $user_ID, $user_identity; ?>
<div id="respond">

    <h3 class="tcomm"><?php comment_form_title( ''.stripslashes(__('Leave a Reply', 'bizzthemes')).'', ''.stripslashes(__('Leave a Reply', 'bizzthemes')).' &rarr; %s' ); ?></h3>
    <div class="cancel-comment-reply">
		<small><?php cancel_comment_reply_link(); ?></small>
	</div>
    <?php if ( get_option('comment_registration') && !$user_ID ) : ?>
        <p><?php echo stripslashes(__('You must be', 'bizzthemes')); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php echo stripslashes(__('logged in', 'bizzthemes')); ?></a> <?php echo stripslashes(__('to post a comment.', 'bizzthemes')); ?></p>
    <?php else : ?>
	
        <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
            <?php if ( $user_ID ) : ?>
                <p><?php echo stripslashes(__('logged in as', 'bizzthemes')); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a> &rarr;&nbsp; <a href="<?php echo wp_logout_url(); ?>" title="<?php echo __('Log out of this account', 'bizzthemes'); ?>"><?php echo stripslashes(__('Logout', 'bizzthemes')); ?> &raquo;</a></p>
            <?php else : ?>
			<?php $req = (bool) get_option('require_name_email'); ?>
                <p class="commpadd"><input type="text" name="author" id="author" value="<?php if ( isset($comment_author) ) echo $comment_author; ?>" size="22" tabindex="1"<?php if ( isset($req) ) echo ' aria-required="true"'; ?> />
				<label for="author"><small><?php echo stripslashes(__('Name', 'bizzthemes')); ?> <?php if ( isset($req) ) _e('*'); ?></small></label></p>
                <p class="commpadd"><input type="text" name="email" id="email" value="<?php if ( isset($comment_author_email) ) echo $comment_author_email; ?>" size="22" tabindex="2"<?php if ( isset($req) ) echo ' aria-required="true"'; ?> />
				<label for="email"><small><?php echo stripslashes(__('Email', 'bizzthemes')); ?> <?php if ( isset($req) ) _e('*'); ?></small></label></p>
				<p class="commpadd"><input type="text" name="url" id="url" value="<?php if ( isset($comment_author_url) ) echo $comment_author_url; ?>" size="22" tabindex="3" />
			    <label for="url"><small><?php echo stripslashes(__('Website', 'bizzthemes')); ?></small></label></p>
            <?php endif; ?>
        <p class="commpadd"><textarea name="comment" id="comment" style="width:98%" rows="10" cols="10" tabindex="4"></textarea></p>
        <p style="padding:5px 0px 10px 0px;"><input name="submit" type="submit" id="submit" tabindex="5" value="<?php echo stripslashes(__('Add Comment', 'bizzthemes')); ?>" />
		    <?php comment_id_fields(); ?>
		</p>
		<?php do_action( 'comment_form', $post->ID); ?>
        </form>
    <?php endif; // If logged in ?>
    <div class="fix"></div>
	
</div> <!-- /#respond -->
<?php endif; // if you delete this the sky will fall on your head
	
}

function delete_comment_link($id) {
    if (current_user_can('edit_post')) {
        echo '&nbsp;-&nbsp; <a href="'.admin_url("comment.php?action=cdc&c=$id").'">'.__('Delete', 'bizzthemes').'</a> ';
        echo '&nbsp;-&nbsp; <a href="'.admin_url("comment.php?action=cdc&dt=spam&c=$id").'">'.__('Spam', 'bizzthemes').'</a>';
    }
}

// Custom comment loop
function custom_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; 
?>
	
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
    
	<div class="comment-container">
		
		<div class="avatar-wrap">
			<?php echo get_avatar( $comment, 48, BIZZ_THEME_IMAGES .'/gravatar.png' ); ?>
		</div><!-- /.meta-wrap -->
							    
		<div class="text-right">
		
			<div class="comm-reply <?php if (1 == $comment->user_id) echo "authcomment"; ?>">
				<span class="author fl"><?php comment_author_link() ?></span>
			    <span class="fr">
				    <small><?php if(!function_exists('how_long_ago')){comment_date('M d, Y'); } else { echo how_long_ago(get_comment_time('U')); } ?></small>&nbsp;&nbsp;
				    <?php 
					if (current_user_can('manage_options') && is_user_logged_in())
					    edit_comment_link(''.stripslashes(__('Edit', 'bizzthemes')).'', '<span class="edit_post">', '</span>&nbsp;');
					?>
					<?php comment_reply_link(array_merge( $args, array('reply_text' => __(''.stripslashes(__('Reply', 'bizzthemes')).''), 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</span>
			</div><!-- /.comm-reply -->
							
			<div class="comment-entry" id="comment-<?php comment_ID(); ?>">
			    <?php comment_text() ?>
				<?php if ($comment->comment_approved == '0') : ?>
				    <b><?php echo stripslashes(__('Your comment is awaiting moderation.', 'bizzthemes')); ?></b>
				<?php endif; ?>
			</div><!-- /.comment-entry -->
			
		</div><!-- /.text-right -->
			
	</div><!-- /.comment-container -->
		 
<?php }

// PINGBACK / TRACKBACK OUTPUT
function list_pings($comment, $args, $depth) {

	$GLOBALS['comment'] = $comment; ?>
	
<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<span class="author"><?php comment_author_link(); ?></span> - 
		<span class="date"><?php if(!function_exists('how_long_ago')){comment_date('M d, Y'); } else { echo how_long_ago(get_comment_time('U')); } ?></span>
		<div class="comment-entry" id="comment-<?php comment_ID(); ?>">
		    <?php comment_text() ?>
		</div><!-- /.comment-entry -->

<?php 
}

/*-----------------------------------------------------------------------------------*/
/* Search form */
/*-----------------------------------------------------------------------------------*/
function bizz_search_form() {
    
?>
<form method="get" class="search searchform" action="<?php bloginfo('url'); ?>">
    <div>
    <input type="text" class="field s" name="s" value="" />
    <button><span><!----></span></button>
    <input type="hidden" class="submit" name="submit" />
    </div>
</form>
<?php
	
}

/*-----------------------------------------------------------------------------------*/
/* Logo spot */
/*-----------------------------------------------------------------------------------*/
function bizz_logo_spot() {
    
    if ( isset($GLOBALS['opt']['bizzthemes_show_blog_title']) && $GLOBALS['opt']['bizzthemes_show_blog_title'] == 'true' ) { 
?>
			<div class="blog-title"><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></div>
		    <div class="blog-description"><?php bloginfo('description'); ?></div>
<?php 
    } else { 
?>
			<h1 class="logo">
			    <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>">
			        <img src="<?php if ( isset($GLOBALS['opt']['bizzthemes_logo_url']) && $GLOBALS['opt']['bizzthemes_logo_url'] <> "" ) { echo $GLOBALS['opt']['bizzthemes_logo_url']; } else { echo BIZZ_THEME_IMAGES .'/logo-trans.png'; } ?>" alt="<?php bloginfo('name'); ?>" />
				</a>
			</h1><!--/.logo-->
<?php 
    }
	
}

/*-----------------------------------------------------------------------------------*/
/* Footer branding */
/*-----------------------------------------------------------------------------------*/
function bizz_footer_branding() {
    
        // front branding constructor
		if ( isset($GLOBALS['opt']['bizzthemes_branding_front']) && $GLOBALS['opt']['bizzthemes_branding_front'] == 'true') { // show custom credentials
		    if ( isset($GLOBALS['opt']['bizzthemes_branding_front_logo']) && $GLOBALS['opt']['bizzthemes_branding_front_logo'] <> '')
			    $blogo = $GLOBALS['opt']['bizzthemes_branding_front_logo'];
		    else
			    $blogo = BIZZ_THEME_IMAGES .'/credits-trans.png';
		    if ( isset($GLOBALS['opt']['bizzthemes_branding_front_link']) && $GLOBALS['opt']['bizzthemes_branding_front_link'] <> '')
			    $blink = $GLOBALS['opt']['bizzthemes_branding_front_link'];
		    else
			    $blink = 'http://bizzthemes.com';
		    if ( isset($GLOBALS['opt']['bizzthemes_branding_front_alt']) && $GLOBALS['opt']['bizzthemes_branding_front_alt'] <> '')
			    $balt = $GLOBALS['opt']['bizzthemes_branding_front_alt'];
		    else
			    $balt = '';
				
		    if ( isset($GLOBALS['opt']['bizzthemes_branding_front_remove']) && $GLOBALS['opt']['bizzthemes_branding_front_remove'] == 'true')
				$credentials .= '';
			else {
			    if ($GLOBALS['opt']['bizzthemes_branding_front_logo'] <> '')
				    $credentials .= '<div class="powered">'."\n";
			    else
				    $credentials .= '<div class="last">'."\n";
			    if ($GLOBALS['opt']['bizzthemes_branding_front_link'] <> '')
			        $credentials .= '<a href="'.$blink.'" title="'.$balt.'">'."\n";
			    if ($GLOBALS['opt']['bizzthemes_branding_front_logo'] <> '')
				    $credentials .= '<img src="'.$blogo.'" alt="'.$balt.'" />'."\n";
			    else
			        $credentials .= ''.$balt.''."\n";
			    if ($GLOBALS['opt']['bizzthemes_branding_front_link'] <> '')
			        $credentials .= '</a>'."\n";
			        $credentials .= '</div>'."\n";
				
			}

	    } 
		else { // show default credentials
            if ( isset($GLOBALS['opt']['bizzthemes_branding_front_remove']) && $GLOBALS['opt']['bizzthemes_branding_front_remove'] == 'true' )
				$credentials = ''; 
			else			
			    $credentials = '<div class="powered"><a href="http://bizzthemes.com" title="Designed by BizzThemes"><img src="'. BIZZ_THEME_IMAGES .'/credits-trans.png" alt="BizzThemes" height="28" width="115" /></a></div>'."\n";
		}
		
		echo $credentials;
		// End front branding options
	
}

/*-----------------------------------------------------------------------------------*/
/* Bizz headline */
/*-----------------------------------------------------------------------------------*/
function bizz_headline() {
    global $post, $wp_query;
	
	global $wp_query;
	
	$templateid = get_post_meta($wp_query->post->ID, '_wp_page_template'); // check for page template
	if (is_paged()) { $ispaged = ' paged'; } else { $ispaged = ''; } 
	
	if (is_404()) {
		echo "\t\t\t\t\t<h1 class='title'>" . stripslashes(__('Error 404 | Nothing found!', 'bizzthemes')) . "</h1>\n";
		
	} elseif (is_page()) {
		if (is_front_page() || ( $templateid['0'] == '/template-blog.php' || $templateid['0'] == 'template-blog.php' ))
			echo "\t\t\t\t\t<h2 class='title".$ispaged."'>" . $wp_query->post->post_title . "</h2>\n";
		else
			echo "\t\t\t\t\t<h1 class='title'>" . $wp_query->post->post_title . "</h1>\n";

	} elseif (is_single()) {
	    echo "\t\t\t\t\t<h1 class='title'>" . $wp_query->post->post_title . "</h1>\n";

	} elseif (is_category()) {
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo stripslashes(__('Browsing Category', 'bizzthemes')); ?> &#39;<?php echo single_cat_title(); ?>&#39;</h2>
<?php 
	} elseif (is_tag()) {
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo stripslashes(__('Browsing Tag', 'bizzthemes')); ?> &#39;<?php echo single_tag_title('', true); ?>&#39;</h2>
<?php
	} elseif (is_author()) {
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo stripslashes(__('Browsing Posts of', 'bizzthemes')); ?> &#39;<?php echo get_the_author_meta('display_name', get_query_var('author')); ?>&#39;</h2>
<?php
	} elseif (is_day()) {
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo stripslashes(__('Browsing Day', 'bizzthemes')); ?> &#39;<?php the_time('F jS, Y'); ?>&#39;</h2>
<?php
	} elseif (is_month()) { 
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo stripslashes(__('Browsing Month', 'bizzthemes')); ?> &#39;<?php the_time('F, Y'); ?>&#39;</h2>
<?php 
	} elseif (is_year()) {
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo stripslashes(__('Browsing Year', 'bizzthemes')); ?> &#39;<?php the_time('Y'); ?>&#39;</h2>
<?php
	} elseif (is_search()) {
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo __('Search results for', 'bizzthemes'); ?> &#39;<?php echo esc_attr(get_search_query()); ?>&#39;</h2>
<?php
	} elseif (is_tax()) {
	
	            $taxonomy 	= get_taxonomy ( get_query_var('taxonomy') );
		        $term_id    = get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
?>
				<h2 class="title<?php echo $ispaged; ?>"><?php echo $taxonomy->label .': '.$term_id->name; ?></h2>
<?php
	} else {
		$queried_object = $wp_query->get_queried_object();
	    echo "\t\t\t\t\t<h2 class='title".$ispaged."'><a href='" . $queried_object->guid . "' rel='bookmark' title='" . $queried_object->post_title . "'>" . $queried_object->post_title . "</a></h2>\n";

	}

}

/*-----------------------------------------------------------------------------------*/
/* Bizz subheadline */
/*-----------------------------------------------------------------------------------*/
function bizz_subheadline() {

?>
	<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<?php

}

/*-----------------------------------------------------------------------------------*/
/* Bizz 404 Error */
/*-----------------------------------------------------------------------------------*/
function bizz_404_error() {
?>
<h2><?php echo stripslashes(__('Sorry, but you are looking for something that is not here.', 'bizzthemes')); ?></h2>
<p><?php _e('Surfin&#8217; ain&#8217;t easy, and right now, you&#8217;re lost at sea. But don&#8217;t worry; simply pick an option from the list below, and you&#8217;ll be back out riding the waves of the Internet in no time.', 'bizzthemes'); ?></p>
<ul>
	<li><?php _e('Hit the &#8220;back&#8221; button on your browser. It&#8217;s perfect for situations like this!', 'bizzthemes'); ?></li>
	<li><?php printf(__('Head on over to the <a href="%s" rel="nofollow">home page</a>.', 'bizzthemes'), get_bloginfo('url')); ?></li>
	<li><?php _e('You will find what you are looking for.', 'bizzthemes'); ?></li>
</ul>
<?php	
}

/*-----------------------------------------------------------------------------------*/
/* Bizz post meta */
/*-----------------------------------------------------------------------------------*/
function bizz_post_meta() {

if (is_single()) {
?>
<p class="meta">
<?php 
    if ( isset($GLOBALS['opt']['bizzthemes_smeta_date']) ) { 
?>
	<span class="date"><abbr class="published" title="<?php the_time(get_option('date_format')); ?>"><?php the_time(get_option('date_format')); ?></abbr></span>
<?php 
    }
	if ( isset($GLOBALS['opt']['bizzthemes_nofollow_author']) ) { $nofollow = 'rel="nofollow"'; } else { $nofollow = ''; }
	if ( isset($GLOBALS['opt']['bizzthemes_smeta_auth']) ) {
		echo '<span class="auth"><a href="' . get_author_posts_url(get_the_author_meta('ID') ) . '" class="auth" '. $nofollow .'>' . get_the_author() . '</a></span>'; 
	}
	if ( isset($GLOBALS['opt']['bizzthemes_smeta_com']) ) { 
?>
	<span class="comm"><?php comments_popup_link(''.stripslashes(__('No Comments', 'bizzthemes')).'', ''.stripslashes(__('One Comment', 'bizzthemes')).'', ''.stripslashes(__('% Comments', 'bizzthemes')).''); ?></span>
<?php 
    }
	if (isset($GLOBALS['opt']['bizzthemes_smeta_cat'])) {
		seo_post_cats();
	}
	if ( isset($GLOBALS['opt']['bizzthemes_smeta_edit']) ) {
	    if (current_user_can('manage_options') && is_user_logged_in())
            edit_post_link(__('Edit', 'bizzthemes'), '<span class="edit">', '</span>');
	}
?>
</p><!-- /.meta -->
<?php	
} elseif (is_front_page()) {
?>
<p class="meta">
<?php 
    if (isset($GLOBALS['opt']['bizzthemes_nofollow_author'])) { $nofollow = 'rel="nofollow"'; } else { $nofollow = ''; }
	if (isset($GLOBALS['opt']['bizzthemes_fmeta_auth'])) {
		echo '<span class="auth"><a href="' . get_author_posts_url(get_the_author_meta('ID') ) . '" class="auth" '. $nofollow .'>' . get_the_author() . '</a></span>'; 
	}
	if (isset($GLOBALS['opt']['bizzthemes_fmeta_date'])) { 
?>
		<span class="date"><abbr class="published" title="<?php the_time(get_option('date_format')); ?>"><?php the_time(get_option('date_format')); ?></abbr></span>
<?php 
    }
	if (isset($GLOBALS['opt']['bizzthemes_fmeta_com'])) { 
?>
	    <span class="comm"><?php comments_popup_link(''.stripslashes(__('No Comments', 'bizzthemes')).'', ''.stripslashes(__('One Comment', 'bizzthemes')).'', ''.stripslashes(__('% Comments', 'bizzthemes')).''); ?></span>
<?php 
    }
	if (isset($GLOBALS['opt']['bizzthemes_fmeta_cat'])) {
		seo_post_cats();
	}
	if (isset($GLOBALS['opt']['bizzthemes_fmeta_edit'])) {
	    if (current_user_can('manage_options') && is_user_logged_in())
            edit_post_link(__('Edit', 'bizzthemes'), '<span class="edit">', '</span>');
	}
?>
</p><!-- /.meta -->
<?php
} else {
?>
<p class="meta">
<?php 
    if (isset($GLOBALS['opt']['bizzthemes_nofollow_author'])) { $nofollow = 'rel="nofollow"'; } else { $nofollow = ''; }
	if (isset($GLOBALS['opt']['bizzthemes_meta_auth'])) {
		echo '<span class="auth"><a href="' . get_author_posts_url(get_the_author_meta('ID') ) . '" class="auth" '. $nofollow .'>' . get_the_author() . '</a></span>'; 
	}
	if (isset($GLOBALS['opt']['bizzthemes_meta_date'])) { 
?>
		<span class="date"><abbr class="published" title="<?php the_time(get_option('date_format')); ?>"><?php the_time(get_option('date_format')); ?></abbr></span>
<?php 
    }
	if (isset($GLOBALS['opt']['bizzthemes_meta_com'])) { 
?>
		<span class="comm"><?php comments_popup_link(''.stripslashes(__('No Comments', 'bizzthemes')).'', ''.stripslashes(__('One Comment', 'bizzthemes')).'', ''.stripslashes(__('% Comments', 'bizzthemes')).''); ?></span>
<?php 
    }
	if (isset($GLOBALS['opt']['bizzthemes_meta_cat'])) {
		seo_post_cats();
	}
	if (isset($GLOBALS['opt']['bizzthemes_meta_edit'])) {
	    if (current_user_can('manage_options') && is_user_logged_in())
            edit_post_link(__('Edit', 'bizzthemes'), '<span class="edit">', '</span>');
	}
?>
</p><!-- /.meta -->
<?php
}

}

/*-----------------------------------------------------------------------------------*/
/* Bizz FAQs */
/*-----------------------------------------------------------------------------------*/
function bizz_faqs_list() {

    $args = array( 'parent' => '0', 'pad_counts' => '1' );
	$terms = get_terms( 'faq_category', $args );
	foreach ( $terms as $term ) {		
?>
        <li class="faq-cat">
			<a href="<?php echo get_bloginfo('url'); ?>/?taxonomy=<?php echo $term->taxonomy; ?>&term=<?php echo $term->slug; ?>" class="faq-clink" title="<?php echo $term->description; ?>"><?php echo $term->name; ?> (<?php echo $term->count; ?>)</a>
            <ul>
<?php
            global $post;
			$myposts = get_posts('post_type=faqs&nopaging=1');
			foreach($myposts as $post) :
				setup_postdata($post);
                $faq_categories = get_the_terms($post->ID, "faq_category");
				$faq_categories_html = array();
				if ( !empty( $faq_categories ) ):
				foreach ($faq_categories as $faq_cat)
				    if ($faq_cat->slug == $term->slug)
				        echo '<li class="faq-q"><a href="'.get_post_permalink().'">'.get_the_title().'</a></li>';
				endif;
            endforeach; 
?> 
			</ul>
			<!-- Term Childes (if any) -->
			<?php if ( get_term_children($term->term_id, $term->taxonomy) ) { ?>
				<ul>
<?php 
				$args2 = array( 'parent' => $term->term_id, 'pad_counts' => '1' );
				$terms2 = get_terms( 'faq_category', $args2 ); 
				foreach ( $terms2 as $term2 ) {
?>
				    <li class="faq-cat">
					    <a href="<?php echo get_bloginfo('url'); ?>/?taxonomy=<?php echo $term2->taxonomy; ?>&term=<?php echo $term2->slug; ?>" class="faq-clink" title="<?php echo $term2->description; ?>"><?php echo $term2->name; ?> (<?php echo $term2->count; ?>)</a>
                        <ul>
<?php
                        global $post;
						$myposts = get_posts('post_type=faqs&nopaging=1');
						foreach($myposts as $post) :
						    setup_postdata($post);
							$faq_categories = get_the_terms($post->ID, "faq_category");
							$faq_categories_html = array();
							if ( !empty( $faq_categories ) ):
							foreach ($faq_categories as $faq_cat)
							    if ($faq_cat->slug == $term2->slug)
								    echo '<li class="faq-q"><a href="'.get_post_permalink().'">'.get_the_title().'</a></li>';
							endif;
							endforeach; 
?> 
                        </ul>
						<!-- Term Childes (if any) -->
						<?php if ( get_term_children($term2->term_id, $term->taxonomy) ) { ?>
						    <ul>
<?php
							$args3 = array( 'child_of' => $term2->term_id, 'pad_counts' => '1' );
							$terms3 = get_terms( 'faq_category', $args3 );
							foreach ( $terms3 as $term3 ) {
?>
							    <li class="faq-cat">
								    <a href="<?php echo get_bloginfo('url'); ?>/?taxonomy=<?php echo $term3->taxonomy; ?>&term=<?php echo $term3->slug; ?>" class="faq-clink" title="<?php echo $term3->description; ?>"><?php echo $term3->name; ?> (<?php echo $term3->count; ?>)</a>
									<ul>
<?php
                                    global $post;
									$myposts = get_posts('post_type=faqs&nopaging=1');
									foreach($myposts as $post) :
									    setup_postdata($post);
										$faq_categories = get_the_terms($post->ID, "faq_category");
										$faq_categories_html = array();
										if ( !empty( $faq_categories ) ):
										foreach ($faq_categories as $faq_cat)
										    if ($faq_cat->slug == $term3->slug)
											    echo '<li class="faq-q"><a href="'.get_post_permalink().'">'.get_the_title().'</a></li>';
										endif;
										endforeach; 
?> 
                                    </ul>
					            </li>
				            <?php } ?>
							</ul>
						<?php } ?>
					</li>
				<?php } ?>
				</ul>
			<?php } ?>
		</li>
<?php  
    }

}

/*-----------------------------------------------------------------------------------*/
/* Bizz FAQ Popular list */
/*-----------------------------------------------------------------------------------*/
function bizz_faqs_popular_list() {
   
				global $wpdb;
				$now = gmdate("Y-m-d H:i:s",time());
				$lastmonth = gmdate("Y-m-d H:i:s",gmmktime(date("H"), date("i"), date("s"), date("m")-12,date("d"),date("Y")));
				$popularposts = "SELECT ID, post_title, COUNT($wpdb->comments.comment_post_ID) AS 'stammy' FROM $wpdb->posts, $wpdb->comments WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish' AND post_type = 'faqs' AND post_date < '$now' AND post_date > '$lastmonth' AND comment_status = 'open' GROUP BY $wpdb->comments.comment_post_ID ORDER BY stammy DESC LIMIT 8";
				$posts = $wpdb->get_results($popularposts);
				$popular = '';
				if($posts){
?>
                <ul class="faq-popular">
	                <h4><?php echo stripslashes(__('Top Questions', 'bizzthemes')); ?></h4>
<?php
                foreach($posts as $post){
	                $post_title = stripslashes($post->post_title);
		            $guid = get_permalink($post->ID);
					$first_post_title=substr($post_title,0,30);
?>
		            <li>
					    <a href="<?php echo $guid; ?>" title="<?php echo $post_title; ?>"><?php echo $post_title; ?></a>
					</li>
<?php 
                }
?>
                </ul>
<?php
				}
				
}

/*-----------------------------------------------------------------------------------*/
/* Bizz Post TAGS - SEO optimized */
/*-----------------------------------------------------------------------------------*/
function seo_post_tags() {

    global $post;
	$post_tags = get_the_tags();
	$post_terms = get_the_term_list( $post->ID, 'faq_tag', '', ', ', '' );
		
		if ($post_tags) {
		
		    echo '<span class="tag">';
			$num_tags = count($post_tags);
			$tag_count = 1;
			
			if ( isset($GLOBALS['opt']['bizzthemes_nofollow_tags']) ) { $nofollow = ' nofollow'; } else { $nofollow = ''; }

			foreach ($post_tags as $tag) {			
				$html_before = '<a href="' . get_tag_link($tag->term_id) . '" rel="tag' . $nofollow . '">';
				$html_after = '</a>';
				
				if ($tag_count < $num_tags)
					$sep = ', ' . "\n";
				elseif ($tag_count == $num_tags)
					$sep = "\n";
				
				echo $html_before . $tag->name . $html_after . $sep;
				$tag_count++;
			}
			echo '</span>';
			
		} elseif ($post_terms) {
		    echo '<span class="tag">';
		    echo $post_terms;
			echo '</span>';
		}
		
}

/*-----------------------------------------------------------------------------------*/
/* Bizz Post CATEGORIES - SEO optimized */
/*-----------------------------------------------------------------------------------*/
function seo_post_cats() {
    
	global $post;
	$post_cats = get_the_category();
	$post_terms = get_the_term_list( $post->ID, 'faq_category', '', ', ', '' );
		
		if ($post_cats) {
		    
			echo '<span class="cat">';
			$num_cats = count($post_cats);
			$cat_count = 1;
			
			if ( isset($GLOBALS['opt']['bizzthemes_nofollow_cats']) ) { $nofollow = ' nofollow'; } else { $nofollow = ''; }

			foreach ($post_cats as $cat) {			
				$html_before = '<a href="' . get_category_link($cat->term_id) . '" rel="cat' . $nofollow . '">';
				$html_after = '</a>';
				
				if ($cat_count < $num_cats)
					$sep = ', ' . "\n";
				elseif ($cat_count == $num_cats)
					$sep = "\n";
				
				echo $html_before . $cat->name . $html_after . $sep;
				$cat_count++;
			}
			echo '</span>';
			
		} elseif ($post_terms) {
		    echo '<span class="cat">';
		    echo $post_terms;
			echo '</span>';
		}
}

/*-----------------------------------------------------------------------------------*/
/* Bizz Feed Icon */
/*-----------------------------------------------------------------------------------*/
function bizz_feed_spot() {
    
?>
    <div class="feed-spot">
<?php 
	if ( isset($GLOBALS['optd']) && isset($GLOBALS['optd']['bizzthemes_img_rss']) )
	    $rss_icon = $GLOBALS['optd']['bizzthemes_img_rss'];
    else
	    $rss_icon = BIZZ_THEME_IMAGES .'/rss-small.png';
    
	if ( isset($GLOBALS['opt']) && $GLOBALS['opt']['bizzthemes_feedburner_url'] <> '' ) { ?> 
		<a class="rss-button" href="<?php echo stripslashes($GLOBALS['opt']['bizzthemes_feedburner_url']); ?>"><img src="<?php echo $rss_icon; ?>" alt="RSS" /></a>
<?php 
    } else { 
?>
		<a class="rss-button" href="<?php echo get_bloginfo_rss('rss2_url'); ?>"><img src="<?php echo $rss_icon; ?>" alt="RSS" /></a>
<?php 
    }
?>
	</div><!--/.feed-spot-->
<?php
	
}

/*-----------------------------------------------------------------------------------*/
/* Hosted Twitter API */
/*-----------------------------------------------------------------------------------*/
/*  Original author: 2009 Eric Lightbody */
/*
Arguments:
	$username - Your twitter username
	$count - Maximum number of latest tweets to display (default 1)	
	$show_time - Show timestamp of tweet
	$link_time - Link to tweet from timestamp
	$before_all_tweets - Text to append before tweets. (default <ul id="twitter_update_list">)
	$after_all_tweets -  Text to append after tweets. (default </ul>)
	$before_tweet - Text to append before individual tweet ( default <li class="twitter-item"> )
	$after_tweet - Text to apppend after individual tweet ( default </li>)
	$between_tweets - Text to separate tweets. (default '')
*/
function hosted_twitter_script( $twitid = '', $username = '', $count=1 ) {
	$account = urlencode( $username );
	if ( empty($account) ) return;
	$show = absint( $count );  // # of Updates to show
	$hidereplies = '';
	if ( empty($hidereplies) ) $hidereplies = false;
	$before_timesince = esc_html( $instance['beforetimesince'] );
	if ( empty($before_timesince) ) $before_timesince = ' ';

	$tweet_saved = get_transient( 'widget-twitter-' . $twitid );
	$tweet_saved_stream = get_option( 'widget-twitter-response-' . $twitid );
	if ( empty($tweet_saved) ) {
		$twitter_json_url = esc_url( "http://api.twitter.com/1/statuses/user_timeline.json?include_rts=true&screen_name=$account", null, 'raw' );
		$response = wp_remote_get( $twitter_json_url, array( 'User-Agent' => 'Twitter Updates' ) );
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 == $response_code ) {
			$tweets = wp_remote_retrieve_body( $response );
			$tweets = json_decode( $tweets);
			$expire = 200;
			if ( !is_array( $tweets ) || isset( $tweets['error'] ) ) {
				$tweets = 'error';
				$expire = 30;
			} 
			elseif ( is_array( $tweets ) || !isset( $tweets['error'] ) ) {
				// update only if no error
				update_option( 'widget-twitter-response-' . $twitid, $tweets);
			}
			set_transient( 'widget-twitter-' . $twitid, $tweets, $expire);
		} else {
			$tweets = 'error';
			$expire = 30;
			set_transient( 'widget-twitter-response-code-' . $twitid, $response_code, $expire);
		}

	}
	
	$tweets = $tweet_saved_stream;
		
	if ( 'error' != $tweets ) :
		echo "<ul id='twitter_update_list'>\n";

		$tweets_out = 0;

		foreach ( (array) $tweets as $tweet ) {
			if ( $tweets_out >= $show )
				break;

			if ( empty( $tweet->text ) || ($hidereplies && !empty($tweet->in_reply_to_user_id)) )
				continue;

			$text = make_clickable(esc_html($tweet->text));
			$text = preg_replace_callback('/(^|\s)@(\w+)/', '_widget_twitter_username', $text);
			$text = preg_replace_callback('/(^|\s)#(\w+)/', '_widget_twitter_hashtag', $text);

			// Move the year for PHP4 compat
			$created_at = substr($tweet->created_at, 0, 10) . substr($tweet->created_at, 25, 5) . substr($tweet->created_at, 10, 15);

			echo "<li class='twitter-item'>{$text}{$before_timesince}<span class='date'><a href='" . esc_url( "http://twitter.com/{$account}/statuses/" . urlencode($tweet->id_str) ) . "' class='timesince'>" . str_replace(' ', '&nbsp;', wpcom_time_since(strtotime($created_at))) . "&nbsp;ago</a></span></li>\n";
			$tweets_out++;
		}

		echo "</ul>\n";
	else :
		if ( 401 == get_transient( 'widget-twitter-response-code-' . $twitid , 'widget' ) )
			echo "<p>" . __("Error: Please make sure the Twitter account is <a href='http://help.twitter.com/forums/10711/entries/14016'>public</a>.") . "</p>";
		else
			echo "<p>" . __("Error: Twitter did not respond. Please wait a few minutes and refresh this page.") . "</p>";
	endif;
		
}

function _widget_twitter_username( $matches ) { // $matches has already been through esc_html
	return "$matches[1]@<a href='" . esc_url( 'http://twitter.com/' . urlencode( $matches[2] ) ) . "'>$matches[2]</a>";
}

function _widget_twitter_hashtag( $matches ) { // $matches has already been through esc_html
	return "$matches[1]<a href='" . esc_url( 'http://search.twitter.com/search?q=%23' . urlencode( $matches[2] ) ) . "'>#$matches[2]</a>";
}

if ( !function_exists('wpcom_time_since') ) :
/*
 * Time since function taken from WordPress.com
 */

function wpcom_time_since( $original, $do_more = 0 ) {
        // array of time period chunks
        $chunks = array(
                array(60 * 60 * 24 * 365 , 'year'),
                array(60 * 60 * 24 * 30 , 'month'),
                array(60 * 60 * 24 * 7, 'week'),
                array(60 * 60 * 24 , 'day'),
                array(60 * 60 , 'hour'),
                array(60 , 'minute'),
        );

        $today = time();
        $since = $today - $original;

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
                $seconds = $chunks[$i][0];
                $name = $chunks[$i][1];

                if (($count = floor($since / $seconds)) != 0)
                        break;
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

        if ($i + 1 < $j) {
                $seconds2 = $chunks[$i + 1][0];
                $name2 = $chunks[$i + 1][1];

                // add second item if it's greater than 0
                if ( (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) && $do_more )
                        $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
        return $print;
}
endif;

//linkify the twitter string
function add_hyperlinks_bizz( $text ) { 
	$text = preg_replace("/((^@)|([^A-Za-z0-9@]@))([A-Za-z0-9_]{1,})/", "$1<a class=\"twitter_user\" href=\"http://www.twitter.com/$4\">$4</a>", $text); //add the username link
 	$text = preg_replace("/((^#)|([^A-Za-z0-9#]#))([A-Za-z0-9_]{1,})/", "$1<a class=\"twitter_hash\" href=\"http://search.twitter.com/search?q=$4\">$4</a>", $text);  //add the hash tag links
	if ( function_exists ( 'make_clickable' ) ) { //make_clickable exists since 0.71
		$text = make_clickable($text); }
	return $text;
}

/*-----------------------------------------------------------------------------------*/
/* Bizz Contact Form */
/*-----------------------------------------------------------------------------------*/
function bizz_contact_form($wid_email,$wid_trans1,$wid_trans2,$wid_trans3,$wid_trans5,$wid_trans6,$wid_trans7,$wid_trans9,$wid_trans10,$wid_trans11,$wid_trans12,$wid_trans13,$wid_trans14,$wid_trans15,$wid_trans16,$wid_trans17,$wid_trans18,$wid_trans19,$wid_id) {

// FORM SETTINGS: start
//If the form is submitted
if( isset($_POST['submitted'.$wid_id.'']) ) {

	//Check to see if the honeypot captcha field was filled in
	if(trim($_POST['checking']) !== '') {
		$captchaError = true;
	} else {
	
		//Check to make sure that the name field is not empty
		if(trim($_POST['contactName']) === '') {
			$nameError =  __($wid_trans1); 
			$hasError = true;
		} else {
			$name = trim($_POST['contactName']);
		}
		
		//Check to make sure sure that a valid email address is submitted
		if(trim($_POST['email']) === '')  {
			$emailError = __($wid_trans1);
			$hasError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
			$emailError = __($wid_trans2);
			$hasError = true;
		} else {
			$email = trim($_POST['email']);
		}
			
		//Check to make sure comments were entered	
		if(trim($_POST['comments']) === '') {
			$commentError = __($wid_trans1);
			$hasError = true;
		} else {
			if(function_exists('stripslashes')) {
				$comments = stripslashes(wpautop(trim($_POST['comments'])));
			} else {
				$comments = wpautop(trim($_POST['comments']));
			}
		}
		
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
		$headers .= 'From: <'.$email.'>' . "\r\n";
		$emailTo = $wid_email; 
		$subject = $wid_trans3.$name;
		$sendCopy = trim($_POST['sendCopy']);
		$body = '<html><body>';
		$body .= '<table rules="all" style="border-color:#dddddd;" cellpadding="10">';
		$body .= "<tr style='background: #eee;'><td><strong>$wid_trans14</strong> </td><td>$name</td></tr>";
		$body .= "<tr><td><strong>$wid_trans15</strong> </td><td>$email</td></tr>";
		$body .= "<tr><td><strong>$wid_trans16</strong> </td><td>$comments</td></tr>";
		$body .= "</table>";
		$body .= "</body></html>";
		
		// Load array with comment data.
		$commentx = array(
                'author' => $name,
                'email' => $email,
                'website' => '',
                'body' => $comments,
                'permalink' => ''
		);
				
		// Instantiate an instance of the class.
		$wpcom_api_key = get_option('wordpress_api_key');
		$wpcom_api_key = (isset($wpcom_api_key)) ? $wpcom_api_key : '';
		$wpcom_url = get_option('siteurl');
		//setup akismet
		$akismet = new Akismet($wpcom_url, $wpcom_api_key);
		$akismet->setAuthor($name);
		$akismet->setAuthorEmail($email);
		$akismet->setContent($comments);
		$akismet->setType("contact_form");
		
		// No errors, check for spam.
		if($akismet->isSpam()) { #Returns true if Akismet thinks the comment is spam.
			// Do something with the spam comment.
			// echo 'AKISMET_SPAM_TRUE';
			$hasSpam = true;
		} else {
			// Do something with the non-spam comment.
			$hasSpam = false;
			// echo 'AKISMET_SPAM_FALSE';
		}
				
		// deactivate spam checking if akismet plugin is disabled
		if (!function_exists('akismet_init')){ 
			$hasSpam = false; 
		}
				
		// Send mail if there is no error and no spam
		if( $hasError || $captchaError || $hasSpam) { #error detected
			// error or spam deted
		}
		else { #If there is no error, send the email
			mail($emailTo, $subject, $body, $headers);
			if($sendCopy == true) {
				$subject = $wid_trans7.$wid_email;
				mail($email, $subject, $body, $headers);
			}
			$emailSent = true;
		}
					
	}
}
?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
jQuery(document).ready(function() {
	jQuery('form#contactForm<?php _e($wid_id); ?>').submit(function() {
		jQuery('form#contactForm<?php _e($wid_id); ?> .error').remove();
		var hasError = false;
		jQuery(this).find('.requiredField').each(function() {
			if(jQuery.trim(jQuery(this).val()) == '') {
				var labelText = jQuery(this).prev('label').text();
				jQuery(this).parent().append('<span class="error"><?php _e($wid_trans9); ?> '+labelText+'.</span>');
				jQuery(this).addClass('inputError');
				hasError = true;
			} else if(jQuery(this).hasClass('email')) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(!emailReg.test(jQuery.trim(jQuery(this).val()))) {
					var labelText = jQuery(this).prev('label').text();
					jQuery(this).parent().append('<span class="error"><?php _e($wid_trans10); ?> '+labelText+'.</span>');
					jQuery(this).addClass('inputError');
					hasError = true;
				}
			}
		});
		if(!hasError) {
			var formInput = jQuery(this).serialize();
			jQuery.post(jQuery(this).attr('action'),formInput, function(data){
				jQuery('form#contactForm<?php _e($wid_id); ?>').slideUp("fast", function() {				   
					jQuery(this).before('<p class="tick"><?php _e($wid_trans11); ?></p>');
				});
			});
		}
		
		return false;
		
	});
});
//-->!]]>
</script>
<?php
// FORM SETTINGS: end
?>
		
	<?php if(isset($hasError) ) { ?>
        <p class="alert"><?php _e($wid_trans12); ?></p>
    <?php } ?>
    
	<?php if ( $wid_email == '' ) { ?>
        <p class="alert"><?php _e($wid_trans13); ?></p>
    <?php } ?>
	                    
    <form action="<?php echo bizz_cur_URL(); ?>" id="contactForm<?php _e($wid_id); ?>" method="post">
                
        <ol class="forms">
            <li>
				<label for="contactName"><?php _e($wid_trans14); ?></label>
				<input type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="txt requiredField" />
                <?php if(isset($nameError) && $nameError != '') { ?>
                    <span class="error"><?php echo $nameError;?></span> 
                <?php } ?>
            </li>
			<li>
				<label for="email"><?php _e($wid_trans15); ?></label>
                <input type="text" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="txt requiredField email" />
                <?php if(isset($emailError) && $emailError != '') { ?>
                    <span class="error"><?php echo $emailError;?></span>
                <?php } ?>
            </li>
            <li class="textarea">
				<label for="commentsText"><?php _e($wid_trans16); ?></label>
                <textarea name="comments" id="commentsText" rows="20" cols="30" class="requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
                <?php if(isset($commentError) && $commentError != '') { ?>
                    <span class="error"><?php echo $commentError;?></span> 
                <?php } ?>
            </li>
            <li class="inline">
				<input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if(isset($_POST['sendCopy']) && $_POST['sendCopy'] == true) echo ' checked="checked"'; ?> />
				<label for="sendCopy"><?php _e($wid_trans17); ?></label>
			</li>
            <li class="screenReader">
				<label for="checking" class="screenReader"><?php _e($wid_trans18) ?></label>
				<input type="text" name="checking" id="checking" class="screenReader" value="<?php if(isset($_POST['checking']))  echo $_POST['checking'];?>" />
			</li>
            <li class="buttons">
				<input type="hidden" name="submitted<?php _e($wid_id); ?>" id="submitted<?php _e($wid_id); ?>" value="true" />
				<input class="submit button" type="submit" value="<?php _e($wid_trans19); ?>" />
			</li>
        </ol>
						
    </form>
		
<?php
	
}

/*-----------------------------------------------------------------------------------*/
/* Bizz Check for Spam */
/*-----------------------------------------------------------------------------------*/
function bizz_checkspam ($content) {

	// innocent until proven guilty
	$isSpam = FALSE;

	$content = (array) $content;

	if (function_exists('akismet_init')) {

		$wpcom_api_key = get_option('wordpress_api_key');

		if (!empty($wpcom_api_key)) {

			global $akismet_api_host, $akismet_api_port;

			// set remaining required values for akismet api
			$content['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
			$content['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$content['referrer'] = $_SERVER['HTTP_REFERER'];
			$content['blog'] = get_option('home');

			if (empty($content['referrer'])) {
				$content['referrer'] = get_permalink();
			}

			$queryString = '';

			foreach ($content as $key => $data) {
				if (!empty($data)) {
					$queryString .= $key . '=' . urlencode(stripslashes($data)) . '&';
				}
			}

			$response = akismet_http_post($queryString, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);

			if ($response[1] == 'true') {
				update_option('akismet_spam_count', get_option('akismet_spam_count') + 1);
				$isSpam = TRUE;
			}

		}

	}

	return $isSpam;

}

/*-----------------------------------------------------------------------------------*/
/* Bizz Get current site URL */
/*-----------------------------------------------------------------------------------*/
function bizz_cur_URL(){
    $pageURL = 'http';
	if ( $_SERVER["HTTPS"] == "on" ) { $pageURL .= "s"; }
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
	    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
	    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

/*-----------------------------------------------------------------------------------*/
/* Tabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/

function bizz_tabs_popular( $posts = 5, $size = 35 ) {
	$popular = new WP_Query('orderby=comment_count&ignore_sticky_posts=1&posts_per_page='.$posts);
	while ($popular->have_posts()) : $popular->the_post();
?>
<li>
	<?php if ($size <> 0) bizz_get_image('image',$size,$size,'thumbnail',90,null,'src',1,0,'','',true,false,false); ?>
	<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
	<span class="meta"><?php the_time('M j, Y'); ?></span>
	<div class="fix"></div>
</li>
<?php endwhile; 
}



/*-----------------------------------------------------------------------------------*/
/* Tabs - Latest Posts */
/*-----------------------------------------------------------------------------------*/

function bizz_tabs_latest( $posts = 5, $size = 35 ) {
	$the_query = new WP_Query('showposts='. $posts .'&ignore_sticky_posts=1&orderby=post_date&order=desc');	
	while ($the_query->have_posts()) : $the_query->the_post(); 
?>
<li>
	<?php if ($size <> 0) bizz_get_image('image',$size,$size,'thumbnail',90,null,'src',1,0,'','',true,false,false); ?>
	<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
	<span class="meta"><?php the_time('M j, Y'); ?></span>
	<div class="fix"></div>
</li>
<?php endwhile; 
}



/*-----------------------------------------------------------------------------------*/
/* Tabs - Latest Comments */
/*-----------------------------------------------------------------------------------*/

function bizz_tabs_comments( $posts = 5, $size = 35 ) {
	global $wpdb;
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
	comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved,
	comment_type,comment_author_url,
	SUBSTRING(comment_content,1,50) AS com_excerpt
	FROM $wpdb->comments
	LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
	$wpdb->posts.ID)
	WHERE comment_approved = '1' AND comment_type = '' AND
	post_password = ''
	ORDER BY comment_date_gmt DESC LIMIT ".$posts;
	
	$comments = $wpdb->get_results($sql);
	
	foreach ($comments as $comment) {
	?>
	<li>
		<?php echo get_avatar( $comment, $size ); ?>
	
		<a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php echo $comment->post_title; ?>">
			<?php echo strip_tags($comment->comment_author); ?>: <?php echo strip_tags($comment->com_excerpt); ?>...
		</a>
		<div class="fix"></div>
	</li>
	<?php 
	}
}

/*-----------------------------------------------------------------------------------*/
/* Bizz Blog Code */
/*-----------------------------------------------------------------------------------*/
function bizz_prog_blog($cat = ''){
		
	if (is_front_page()){
		global $query_string; 
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if ($cat != '')
		    $args=array( 'paged'=>$paged, 'ignore_sticky_posts'=>1, 'cat'=>$cat );
		else
		    $args=array( 'paged'=>$paged, 'ignore_sticky_posts'=>1 );
		query_posts($query_string . '&'.$args);
	} else {
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if ($cat != '')
		    $args=array( 'paged'=>$paged, 'ignore_sticky_posts'=>1, 'cat'=>$cat );
		else
		    $args=array( 'paged'=>$paged, 'ignore_sticky_posts'=>1 );
		query_posts($args);
	}
	
	if (have_posts()) : $postcount = 0;
	while (have_posts()) : the_post(); $postcount++;
?>
		<div class="blog clearfix">
		    <div class="headline">
				<?php bizz_subheadline(); ?>
				<?php bizz_post_meta(); ?>
			</div><!-- /.headline -->
<?php 
			if ( isset($GLOBALS['opt']['bizzthemes_thumb_show']) ) {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_thumb_width'],$GLOBALS['opt']['bizzthemes_thumb_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_thumb_align']);
			} 
 
			if ( isset($GLOBALS['opt']['bizzthemes_archive_full']) ) {
				the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).''));
			} else {
				the_excerpt();
				if ( isset($GLOBALS['opt']['bizzthemes_readmore']) ) { 
?>
					<span class="read-more"><a rel="nofollow" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php echo stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']); ?></a></span>
<?php
				} 
			}
?>
		</div><!-- /.blog -->
				
<?php 
    endwhile; endif; 
?>
	
<?php 
    if (function_exists('bizz_wp_pagenavi')) { 
?>
	    <div class="fix"><!----></div>
		<div class="lpagination fpagination clearfix">
		    <?php bizz_wp_pagenavi(); ?>
		</div>
<?php 
    } 
	wp_reset_query();
}

?>