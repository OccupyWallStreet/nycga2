<?php

// Creates the DOCTYPE section
function thematic_create_doctype() {
    $content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
    $content .= '<html xmlns="http://www.w3.org/1999/xhtml"';
    echo apply_filters('thematic_create_doctype', $content);
} // end thematic_create_doctype


// Creates the HEAD Profile
function thematic_head_profile() {
    $content = '<head profile="http://gmpg.org/xfn/11">' . "\n";
    echo apply_filters('thematic_head_profile', $content);
} // end thematic_head_profile


// Get the page number adapted from http://efficienttips.com/wordpress-seo-title-description-tag/
function pageGetPageNo() {
    if (get_query_var('paged')) {
        print ' | Page ' . get_query_var('paged');
    }
} // end pageGetPageNo


// Located in header.php 
// Creates the content of the Title tag
// Credits: Tarski Theme
if (function_exists('childtheme_override_doctitle'))  {
    function thematic_doctitle() {
    	childtheme_override_doctitle();
    }
} else {
	function thematic_doctitle() {
		$site_name = get_bloginfo('name');
	    $separator = '|';
	        	
	    if ( is_single() ) {
	      $content = single_post_title('', FALSE);
	    }
	    elseif ( is_home() || is_front_page() ) { 
	      $content = get_bloginfo('description');
	    }
	    elseif ( is_page() ) { 
	      $content = single_post_title('', FALSE); 
	    }
	    elseif ( is_search() ) { 
	      $content = __('Search Results for:', 'thematic'); 
	      $content .= ' ' . esc_html(stripslashes(get_search_query()));
	    }
	    elseif ( is_category() ) {
	      $content = __('Category Archives:', 'thematic');
	      $content .= ' ' . single_cat_title("", false);;
	    }
	    elseif ( is_tag() ) { 
	      $content = __('Tag Archives:', 'thematic');
	      $content .= ' ' . thematic_tag_query();
	    }
	    elseif ( is_404() ) { 
	      $content = __('Not Found', 'thematic'); 
	    }
	    else { 
	      $content = get_bloginfo('description');
	    }
	
	    if (get_query_var('paged')) {
	      $content .= ' ' .$separator. ' ';
	      $content .= 'Page';
	      $content .= ' ';
	      $content .= get_query_var('paged');
	    }
	
	    if($content) {
	      if ( is_home() || is_front_page() ) {
	          $elements = array(
	            'site_name' => $site_name,
	            'separator' => $separator,
	            'content' => $content
	          );
	      }
	      else {
	          $elements = array(
	            'content' => $content
	          );
	      }  
	    } else {
	      $elements = array(
	        'site_name' => $site_name
	      );
	    }
	
	    // Filters should return an array
	    $elements = apply_filters('thematic_doctitle', $elements);
		
	    // But if they don't, it won't try to implode
	    if(is_array($elements)) {
	      $doctitle = implode(' ', $elements);
	    }
	    else {
	      $doctitle = $elements;
	    }
	    
	    $doctitle = "\t" . "<title>" . $doctitle . "</title>" . "\n\n";
	    
	    echo $doctitle;
	} // end thematic_doctitle
}

// Creates the content-type section
function thematic_create_contenttype() {
    $content  = "\t";
    $content .= "<meta http-equiv=\"Content-Type\" content=\"";
    $content .= get_bloginfo('html_type'); 
    $content .= "; charset=";
    $content .= get_bloginfo('charset');
    $content .= "\" />";
    $content .= "\n\n";
    echo apply_filters('thematic_create_contenttype', $content);
} // end thematic_create_contenttype

// The master switch for SEO functions
function thematic_seo() {
		$content = TRUE;
		return apply_filters('thematic_seo', $content);
}

// Creates the canonical URL
function thematic_canonical_url() {
		if (thematic_seo()) {
    		if ( is_singular() ) {
        		$canonical_url = "\t";
        		$canonical_url .= '<link rel="canonical" href="' . get_permalink() . '" />';
        		$canonical_url .= "\n\n";        
        		echo apply_filters('thematic_canonical_url', $canonical_url);
				}
    }
} // end thematic_canonical_url


// switch use of thematic_the_excerpt() - default: ON
function thematic_use_excerpt() {
    $display = TRUE;
    $display = apply_filters('thematic_use_excerpt', $display);
    return $display;
} // end thematic_use_excerpt


// switch use of thematic_the_excerpt() - default: OFF
function thematic_use_autoexcerpt() {
    $display = FALSE;
    $display = apply_filters('thematic_use_autoexcerpt', $display);
    return $display;
} // end thematic_use_autoexcerpt


// Creates the meta-tag description
function thematic_create_description() {
		$content = '';
		if (thematic_seo()) {
    		if (is_single() || is_page() ) {
      		  if ( have_posts() ) {
          		  while ( have_posts() ) {
            		    the_post();
										if (thematic_the_excerpt() == "") {
                    		if (thematic_use_autoexcerpt()) {
                        		$content ="\t";
														$content .= "<meta name=\"description\" content=\"";
                        		$content .= thematic_excerpt_rss();
                        		$content .= "\" />";
                        		$content .= "\n\n";
                    		}
                		} else {
                    		if (thematic_use_excerpt()) {
                        		$content ="\t";
                        		$content .= "<meta name=\"description\" content=\"";
                        		$content .= thematic_the_excerpt();
                        		$content .= "\" />";
                        		$content .= "\n\n";
                    		}
                		}
            		}
        		}
    		} elseif ( is_home() || is_front_page() ) {
        		$content ="\t";
        		$content .= "<meta name=\"description\" content=\"";
        		$content .= get_bloginfo('description');
        		$content .= "\" />";
        		$content .= "\n\n";
    		}
    		echo apply_filters ('thematic_create_description', $content);
		}
} // end thematic_create_description


// meta-tag description is switchable using a filter
function thematic_show_description() {
    $display = TRUE;
    $display = apply_filters('thematic_show_description', $display);
    if ($display) {
        thematic_create_description();
    }
} // end thematic_show_description


// create meta-tag robots
function thematic_create_robots() {
        global $paged;
		if (thematic_seo()) {
    		$content = "\t";
    		if((is_home() && ($paged < 2 )) || is_front_page() || is_single() || is_page() || is_attachment()) {
				$content .= "<meta name=\"robots\" content=\"index,follow\" />";
    		} elseif (is_search()) {
        		$content .= "<meta name=\"robots\" content=\"noindex,nofollow\" />";
    		} else {	
        		$content .= "<meta name=\"robots\" content=\"noindex,follow\" />";
    		}
    		$content .= "\n\n";
    		if (get_option('blog_public')) {
    				echo apply_filters('thematic_create_robots', $content);
    		}
		}
} // end thematic_create_robots


// meta-tag robots is switchable using a filter
function thematic_show_robots() {
    $display = TRUE;
    $display = apply_filters('thematic_show_robots', $display);
    if ($display) {
        thematic_create_robots();
    }
} // end thematic_show_robots


// Located in header.php
// creates link to style.css
function thematic_create_stylesheet() {
    $content = "\t";
    $content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"";
    $content .= get_bloginfo('stylesheet_url');
    $content .= "\" />";
    $content .= "\n\n";
    echo apply_filters('thematic_create_stylesheet', $content);
}


// rss usage is switchable using a filter
function thematic_show_rss() {
    $display = TRUE;
    $display = apply_filters('thematic_show_rss', $display);
    if ($display) {
        $content = "\t";
        $content .= "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"";
        $content .= get_bloginfo('rss2_url');
        $content .= "\" title=\"";
        $content .= esc_html(get_bloginfo('name'));
        $content .= " " . __('Posts RSS feed', 'thematic');
        $content .= "\" />";
        $content .= "\n";
        echo apply_filters('thematic_rss', $content);
    }
} // end thematic_show_rss


// comments rss usage is switchable using a filter
function thematic_show_commentsrss() {
    $display = TRUE;
    $display = apply_filters('thematic_show_commentsrss', $display);
    if ($display) {
        $content = "\t";
        $content .= "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"";
        $content .= get_bloginfo('comments_rss2_url');
        $content .= "\" title=\"";
        $content .= esc_html(get_bloginfo('name'));
        $content .= " " . __('Comments RSS feed', 'thematic');
        $content .= "\" />";
        $content .= "\n\n";
        echo apply_filters('thematic_commentsrss', $content);
    }
} // end thematic_show_commentsrss


// pingback usage is switchable using a filter
function thematic_show_pingback() {
    $display = TRUE;
    $display = apply_filters('thematic_show_pingback', $display);
    if ($display) {
        $content = "\t";
        $content .= "<link rel=\"pingback\" href=\"";
        $content .= get_bloginfo('pingback_url');
        $content .= "\" />";
        $content .= "\n\n";
        echo apply_filters('thematic_pingback_url',$content);
    }
} // end thematic_show_pingback


// comment reply usage is switchable using a filter
function thematic_show_commentreply() {
    $display = TRUE;
    $display = apply_filters('thematic_show_commentreply', $display);
    if ($display)
        if ( is_singular() ) 
            wp_enqueue_script( 'comment-reply' ); // support for comment threading
} // end thematic_show_commentreply


// Load scripts for the jquery Superfish plugin http://users.tpg.com.au/j_birch/plugins/superfish/#examples
if (function_exists('childtheme_override_head_scripts'))  {
    function thematic_head_scripts() {
    	childtheme_override_head_scripts();
    }
} else {
    function thematic_head_scripts() {
	    $scriptdir_start = "\t";
	    $scriptdir_start .= '<script type="text/javascript" src="';
	    $scriptdir_start .= get_bloginfo('template_directory');
	    $scriptdir_start .= '/library/scripts/';
	    
	    $scriptdir_end = '"></script>';
	    
	    $scripts = "\n";
	    $scripts .= $scriptdir_start . 'hoverIntent.js' . $scriptdir_end . "\n";
	    $scripts .= $scriptdir_start . 'superfish.js' . $scriptdir_end . "\n";
	    $scripts .= $scriptdir_start . 'supersubs.js' . $scriptdir_end . "\n";
	    $dropdown_options = $scriptdir_start . 'thematic-dropdowns.js' . $scriptdir_end . "\n";
	    
	    $scripts = $scripts . apply_filters('thematic_dropdown_options', $dropdown_options);
	
	    	$scripts .= "\n";
	    	$scripts .= "\t";
	    	$scripts .= '<script type="text/javascript">' . "\n";
	    	$scripts .= "\t\t" . '/*<![CDATA[*/' . "\n";
	    	$scripts .= "\t\t" . 'jQuery.noConflict();' . "\n";
	    	$scripts .= "\t\t" . '/*]]>*/' . "\n";
	    	$scripts .= "\t";
	    	$scripts .= '</script>' . "\n";
	
	    // Print filtered scripts
	    print apply_filters('thematic_head_scripts', $scripts);
	}

	if (apply_filters('thematic_use_superfish', TRUE)) {
		add_action('wp_head','thematic_head_scripts');
	}
}

// Create the default arguments for wp_page_menu()
function thematic_page_menu_args() {
	$args = array (
		'sort_column' => 'menu_order',
		'menu_class'  => 'menu',
		'include'     => '',
		'exclude'     => '',
		'echo'        => FALSE,
		'show_home'   => FALSE,
		'link_before' => '',
		'link_after'  => ''
	);
	return $args;
}
add_filter('wp_page_menu_args','thematic_page_menu_args');


// Create the default arguments for wp_page_menu()
function thematic_nav_menu_args() {
	$args = array (
		'theme_location'	=> apply_filters('thematic_primary_menu_id', 'primary-menu'),
		'menu'				=> '',
		'container'			=> 'div',
		'container_class'	=> 'menu',
		'menu_class'		=> 'sf-menu',
		'fallback_cb'		=> 'wp_page_menu',
		'before'			=> '',
		'after'				=> '',
		'link_before'		=> '',
		'link_after'		=> '',
		'depth'				=> 0,
		'walker'			=> '',
		'echo'				=> false
	);
	
	return apply_filters('thematic_nav_menu_args', $args);
}

if (function_exists('childtheme_override_init_navmenu'))  {
    function thematic_init_navmenu() {
    	childtheme_override_init_navmenu();
    }
} else {
    function thematic_init_navmenu() {
    	if (function_exists( 'register_nav_menu' )) {
    		register_nav_menu( apply_filters('thematic_primary_menu_id', 'primary-menu'), apply_filters('thematic_primary_menu_name', __( 'Primary Menu', 'thematic' ) ) );
		}
	}
}
add_action('init', 'thematic_init_navmenu');

// Add ID and CLASS attributes to the first <ul> occurence in wp_page_menu
function thematic_add_menuclass($ulclass) {
	if (apply_filters('thematic_use_superfish', TRUE)) {
		return preg_replace('/<ul>/', '<ul class="sf-menu">', $ulclass, 1);
	} else {
		return $ulclass;
	}
} // end thematic_add_menuclass

// Just after the opening body tag, before anything else.
function thematic_before() {
    do_action('thematic_before');
} // end thematic_before


// Just before the header div
function thematic_aboveheader() {
    do_action('thematic_aboveheader');
} // end thematic_aboveheader


// Used to hook in the HTML and PHP that creates the content of div id="header">
function thematic_header() {
    do_action('thematic_header');
} // end thematic_header


// Functions that hook into thematic_header()

	// Open #branding
	// In the header div
	if (function_exists('childtheme_override_brandingopen'))  {
	    function thematic_brandingopen() {
	    	childtheme_override_brandingopen();
	    }
	} else {
		function thematic_brandingopen() {
			echo "<div id=\"branding\">\n";
		}
	    add_action('thematic_header','thematic_brandingopen',1);
	}	
	
	// Create the blog title
	// In the header div
	if (function_exists('childtheme_override_blogtitle'))  {
	    function thematic_blogtitle() {
	    	childtheme_override_blogtitle();
	    }
	} else {
	    function thematic_blogtitle() { ?>
	    		
	    		<div id="blog-title"><span><a href="<?php bloginfo('url') ?>/" title="<?php bloginfo('name') ?>" rel="home"><?php bloginfo('name') ?></a></span></div>
	    		
	    <?php }
	    add_action('thematic_header','thematic_blogtitle',3);
	}
	
	// Create the blog description
	// In the header div
	if (function_exists('childtheme_override_blogdescription'))  {
	    function thematic_blogdescription() {
	    	childtheme_override_blogdescription();
	    }
	} else {
	    function thematic_blogdescription() {
	    	$blogdesc = '"blog-description">' . get_bloginfo('description');
			if (is_home() || is_front_page()) { 
	        	echo "\t\t<h1 id=$blogdesc</h1>\n\n";
	        } else {	
	        	echo "\t\t<div id=$blogdesc</div>\n\n";
	        }
	    }
	    add_action('thematic_header','thematic_blogdescription',5);
	}
	
	// Close #branding
	// In the header div
	if (function_exists('childtheme_override_brandingclose'))  {
	    function thematic_brandingclose() {
	    	childtheme_override_brandingclose();
	    }
	} else {
	    function thematic_brandingclose() {
	    	echo "\t\t</div><!--  #branding -->\n";
	    }
	    add_action('thematic_header','thematic_brandingclose',7);
	}
	
	// Create #access
	// In the header div
	if (function_exists('childtheme_override_access'))  {
	    function thematic_access() {
	    	childtheme_override_access();
	    }
	} else {
	    function thematic_access() { ?>
	    
	    <div id="access">
	    		
	    	<div class="skip-link"><a href="#content" title="<?php _e('Skip navigation to the content', 'thematic'); ?>"><?php _e('Skip to content', 'thematic'); ?></a></div><!-- .skip-link -->
	    		
	    	<?php 
	    		
	    	if ((function_exists("has_nav_menu")) && (has_nav_menu(apply_filters('thematic_primary_menu_id', 'primary-menu')))) {
	    		echo  wp_nav_menu(thematic_nav_menu_args());
    		} else {
    			echo  thematic_add_menuclass(wp_page_menu(thematic_page_menu_args()));	
    		}
    		
	    	?>
	        
		</div><!-- #access -->
		
		<?php }
	}

    add_action('thematic_header','thematic_access',9);
    
// End of functions that hook into thematic_header()

		
// Just after the header div
function thematic_belowheader() {
    do_action('thematic_belowheader');
} // end thematic_belowheader
		

?>