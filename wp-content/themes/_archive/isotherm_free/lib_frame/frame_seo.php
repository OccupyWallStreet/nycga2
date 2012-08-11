<?php

//------ SEO OPTIONS ------//
// Some parts of code used from Thesis 1.5 class 'Head' @Chris Pearson

//** REMOVE SOME DEFAULT WORDPRESS GARBAGE **//

    remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'start_post_rel_link');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'adjacent_posts_rel_link');
	remove_action('wp_head', 'next_post_rel_link');
	remove_action('wp_head', 'prev_post_rel_link');
	remove_action('wp_head', 'parent_post_rel_link');
	remove_action('wp_head', 'rel_canonical');

// Use Noindex for sections specified in theme admin
function bizzthemes_wp_seo() {

    // TITLE tags: sections specified in theme admin
	
	echo "<title>";
    // Is an SEO title tag plugin already being used? If so, defer to it to prevent conflict.
	if (function_exists('seo_title_tag'))
		seo_title_tag();
	else {
		global $post;
		$site_name = get_bloginfo('name');
		$separator = "|";
	
		if (is_home() || is_front_page()) {
			// Allow for custom home pages to have completely custom <title> tag, like pages and posts
			if (get_option('show_on_front') == 'page' && is_front_page())
				$title_override = strip_tags(stripslashes(get_post_meta($post->ID, 'bizzthemes_title', true)));
			elseif (get_option('show_on_front') == 'page' && is_home())
				$title_override = strip_tags(stripslashes(get_post_meta(get_option('page_for_posts'), 'bizzthemes_title', true)));

			if (!isset($title_override)) {
				$site_tagline = get_bloginfo('description');

				if ( isset($GLOBALS['opt']['bizzthemes_title_title']) && isset($GLOBALS['opt']['bizzthemes_title_tagline']) )
					echo "$site_tagline $separator $site_name";
				elseif ( isset($GLOBALS['opt']['bizzthemes_title_tagline']) )
					echo $site_tagline;
				else
					echo $site_name;
			}
			else
				echo $title_override;
		}
		
		elseif (is_category()) {
			$category_description = trim(strip_tags(category_description()));
			$category_title = (strlen($category_description)) ? $category_description : single_cat_title();

			if ( isset($GLOBALS['opt']['bizzthemes_title_other']) )
				echo "$category_title $separator $site_name";
			else
				echo $category_title;
		}
		elseif (is_search()) {
			$search_title = __('Search results for', 'bizzthemes') . ' &#8220;' . esc_attr(get_search_query()) . '&#8221;';
			
			if ( isset($GLOBALS['opt']['bizzthemes_title_other']) )
				echo "$search_title $separator $site_name";
			else
				echo $search_title;
		}
		else {
			$custom_title = (is_single() || is_page()) ? get_post_meta($post->ID, 'bizzthemes_title', true) : false;
			$page_title = ($custom_title) ? strip_tags(stripslashes($custom_title)) : trim(wp_title('', false));

			if ( isset($GLOBALS['opt']['bizzthemes_title_other']) )
				echo "$page_title $separator $site_name";
			else
				echo $page_title;
		}
		
		if (is_home() || is_archive() || is_search()) {
			$current_page = get_query_var('paged');
			
			if ($current_page > 1)
				echo " $separator " . __('Page') . " $current_page";
		}
	}
	
	echo "</title>";
	
	// NOINDEX, NOFOLLOW tags: sections specified in theme admin
	
		if (get_option('blog_public') <> '0') {
			$current_page = get_query_var('paged');

			// Index the content? specified for secific page or post by meta tags
			if (is_page() || is_single()) {
				global $post, $meta_noindex;
				if (get_post_meta($post->ID, 'bizzthemes_noindex', true))
					$meta_noindex .= '<meta name="robots" content="noindex, nofollow" />';
				
				$seopages = get_inc_pages("pag_exclude_seo_");
				$seoarr=split(",",$seopages);
				$seoarr = array_diff($seoarr, array(""));
				foreach ( $seoarr as $seoitem ) {
				    if (($post->ID == $seoitem) && !get_post_meta($post->ID, 'bizzthemes_noindex', true))
					    $meta_noindex .= '<meta name="robots" content="noindex, nofollow" />';
				}
					
			// Index the content? specified for global content in theme option panel
			} elseif (
			    is_search() || is_404() || // search & 404 page get noindex and nofollow by default
			    (is_category() && isset($GLOBALS['opt']['bizzthemes_noindex_category']) ) ||
			    (is_tag() && isset($GLOBALS['opt']['bizzthemes_noindex_tag']) ) || 
				(is_author() && isset($GLOBALS['opt']['bizzthemes_noindex_author']) ) || 
				(is_day() && isset($GLOBALS['opt']['bizzthemes_noindex_daily']) ) || 
				(is_month() && isset($GLOBALS['opt']['bizzthemes_noindex_monthly']) ) || 
				(is_year() && isset($GLOBALS['opt']['bizzthemes_noindex_yearly']) ) || 
				$current_page > 1 // noindex and nofollow paged content
			)
				$meta_noindex .= '<meta name="robots" content="noindex, nofollow" />';
				
			$nodir = array(); 
			
			if ( isset($GLOBALS['opt']['bizzthemes_noodp_meta']) )
				$nodir[] = 'noodp';
			if ( isset($GLOBALS['opt']['bizzthemes_noydir_meta']) )
				$nodir[] = 'noydir';
				
			$meta_noindex .= '<meta name="robots" content="' . implode(', ', $nodir) . '" />';
				
		}
		
		if (isset($meta_noindex))
		    echo $meta_noindex;
		
	// META tags: Is All-in-One SEO installed? If so, defer to it for SEO meta handling.
	
		if (!class_exists('All_in_One_SEO_Pack')) {
		
		    // Meta content specified for secific page or post by meta tags
			if (is_single() || is_page()) {
				global $post;

				$custom_description = get_post_meta($post->ID, 'bizzthemes_description', true);
				$custom_keywords = get_post_meta($post->ID, 'bizzthemes_keywords', true);
				if (strlen($custom_description))
					$meta_description = '<meta name="description" content="' . trim(wptexturize(strip_tags(stripslashes($custom_description)))) . '" />';
				else {
				    function bizz_meta_excerpt_length($length) {
					    return (apply_filters('bizz_meta_excerpt_length', 40));
					}
					setup_postdata($post);
					add_filter('excerpt_length', 'bizz_meta_excerpt_length');
					$excerpt = trim(str_replace('[...]', '', wp_trim_excerpt('')));
					remove_filter('excerpt_length', 'bizz_meta_excerpt_length');
					$meta_description = '<meta name="description" content="' . $excerpt . '" />';
				}

				if (strlen($custom_keywords))
					$meta_keywords = '<meta name="keywords" content="' . trim(wptexturize(strip_tags(stripslashes($custom_keywords)))) . '" />';
				else {
					$tags = bizzthemes_get_post_tags($post->ID);

					if ($tags)
						$meta_keywords = '<meta name="keywords" content="' . implode(', ', $tags) . '" />';
				}
			} elseif (is_category()) {
				$category_description = trim(strip_tags(stripslashes(category_description())));
				$meta_description = (strlen($category_description)) ? '<meta name="description" content="' . $category_description . '" />' : '<meta name="description" content="' . single_cat_title('', false) . '" />';
			
			// Meta content specified for global content in theme option panel
			} else {
				if (isset($GLOBALS['opt']['bizzthemes_meta_description']) && $GLOBALS['opt']['bizzthemes_meta_description'])
					$meta_description = '<meta name="description" content="' . trim(wptexturize(strip_tags(stripslashes($GLOBALS['opt']['bizzthemes_meta_description'])))) . '" />';
				elseif (strlen(get_bloginfo('description')))
					$meta_description = '<meta name="description" content="' . get_bloginfo('description') . '" />';
				
				if (isset($GLOBALS['opt']['bizzthemes_meta_keywords']) && $GLOBALS['opt']['bizzthemes_meta_keywords'])
					$meta_keywords = '<meta name="keywords" content="' . $GLOBALS['opt']['bizzthemes_meta_keywords'] . '" />';
				if (isset($GLOBALS['opt']['bizzthemes_meta_author']) && $GLOBALS['opt']['bizzthemes_meta_author'])
					$meta_author = '<meta name="author" content="' . $GLOBALS['opt']['bizzthemes_meta_author'] . '" />';
			}
			
		if (isset($meta_description))
		    echo $meta_description;
		if (isset($meta_keywords))
		    echo $meta_keywords;
		if (isset($meta_author))
		    echo $meta_author;
			
        }
		
	// CANONICAL URLs
	
		if (!function_exists('yoast_canonical_link') && !class_exists('All_in_One_SEO_Pack') && isset($GLOBALS['opt']['bizzthemes_canonical_url']) ) {
			if (is_single() || is_page()) {
				global $post;				
				$url = (is_page() && get_option('show_on_front') == 'page' && get_option('page_on_front') == $post->ID) ? trailingslashit(get_permalink()) : get_permalink();
			}
			elseif (is_author()) {
				$author = get_userdata(get_query_var('author'));
				$url = get_author_posts_url(get_query_var('author'));
			}
			elseif (is_category())
				$url = get_category_link(get_query_var('cat'));
			elseif (is_tag()) {
				$tag = get_term_by('slug', get_query_var('tag'), 'post_tag');

				if (!empty($tag->term_id))
					$url = get_tag_link($tag->term_id);
			}
			elseif (is_day())
				$url = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
			elseif (is_month())
				$url = get_month_link(get_query_var('year'), get_query_var('monthnum'));
			elseif (is_year())
				$url = get_year_link(get_query_var('year'));
			elseif (is_home())
				$url = (get_option('show_on_front') == 'page') ? trailingslashit(get_permalink(get_option('page_for_posts'))) : trailingslashit(get_option('home'));
			elseif (is_search())
				$url = get_search_link(get_query_var('s'));
			
			echo '<link rel="canonical" href="' . $url . '" />';
		}
	
}

add_action('bizz_head_before', 'bizzthemes_wp_seo');

// 301 Redirect
function bizz_redirect() {
	global $wp_query;
	if ($wp_query->is_singular) {
		$redirect = get_post_meta($wp_query->post->ID, 'bizzthemes_redirect', true);
		if ($redirect) wp_redirect($redirect, 301);
	}
}
add_action('template_redirect', 'bizz_redirect');

?>