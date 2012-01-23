<?php 
/**
 * Echoes the content or the excerpt, depending on user preferences.
 * 
 * @access public
 * @return void
 */
function ci_e_content()
{
	global $post;
	if (is_single() or is_page())
		the_content(); 
	else
	{
		if(ci_setting('preview_content')=='enabled')
		{
			the_content(ci_setting('read_more_text'));
		}
		else
		{
			the_excerpt(); 
		}
	}
}

/**
 * Returns a string depending on the value of $num.
 * 
 * When $num equals zero, string $none is returned.
 * When $num equals one, string $one is returned.
 * When $num is greater than one, string $many is returned.
 * 
 * @access public
 * @param int $num
 * @param string $none
 * @param string $one
 * @param string $many
 * @return string
 */
function ci_inflect($num, $none, $one, $many){
	if ($num==0)
		return $none;
	if ($num==1)
		return $one;
	if ($num>1)
		return $many;
}

/**
 * Echoes a string depending on the value of $num.
 * 
 * When $num equals zero, string $none is echoed.
 * When $num equals one, string $one is echoed.
 * When $num is greater than one, string $many is echoed.
 * 
 * @access public
 * @param int $num
 * @param string $none
 * @param string $one
 * @param string $many
 * @return void
 */
function ci_e_inflect($num, $none, $one, $many){
	echo ci_inflect($num, $none, $one, $many);
}


/**
 * Returns a string of all the categories, tags and taxonomies the current post is under.
 * 
 * @access public
 * @param string $separator
 * @return string
 */
function ci_list_cat_tag_tax($separator=', ')
{
	global $post;

	$taxonomies = get_post_taxonomies();

	$i = 0;
	$the_terms = array();
	$the_terms_temp = array();
	$the_terms_list = '';
	foreach($taxonomies as $taxonomy)
	{
		$the_terms_temp[] = get_the_term_list($post->ID, $taxonomy, '', $separator, '');
	}

	foreach($the_terms_temp as $term)
	{
		if(!empty($term))
			$the_terms[] = $term;
	}
	
	$terms_count = count($the_terms);
	for($i=0; $i < $terms_count; $i++)
	{
		$the_terms_list .= $the_terms[$i];
		if ($i < ($terms_count-1))
			$the_terms_list .= $separator;
	}
	
	if (!empty($the_terms_list))
		return $the_terms_list;	
	else
		return __('Uncategorized');
}

/**
 * Echoes a string of all the categories, tags and taxonomies the current post is under.
 * 
 * @access public
 * @param string $separator
 * @return void
 */
function ci_e_list_cat_tag_tax($separator=', ')
{
	echo ci_list_cat_tag_tax($separator);
}



/**
 * Determines the number of posts parameter for pagination. Returns the number of posts, or boolean FALSE if pagination is disabled.
 * 
 * @access public
 * @return int|false Number of posts per page, or boolean false if paging is disabled. 
 */
function ci_posts_per_page()
{
	global $ci;
	
	if(is_home())
	{
		
		if ($ci['home_pagination']=='enabled' and (!empty($ci['home_posts_per_page']))){
			return $ci['home_posts_per_page'];
		}
		elseif ($ci['home_pagination']=='global' and $ci['pagination']=='enabled' and (!empty($ci['posts_per_page']))){
			return $ci['posts_per_page'];
		}
		else {
			return FALSE;
		}
	}

	if(is_category())
	{
		if ($ci['cat_pagination']=='enabled' and (!empty($ci['cat_posts_per_page']))){
			return $ci['cat_posts_per_page'];
		}
		elseif ($ci['cat_pagination']=='global' and $ci['pagination']=='enabled' and (!empty($ci['posts_per_page']))){
			return $ci['posts_per_page'];
		}
		else {
			return FALSE;
		}
	}

	if(is_tag())
	{
		if ($ci['tag_pagination']=='enabled' and (!empty($ci['tag_posts_per_page']))){
			return $ci['tag_posts_per_page'];
		}
		elseif ($ci['tag_pagination']=='global' and $ci['pagination']=='enabled' and (!empty($ci['posts_per_page']))){
			return $ci['posts_per_page'];
		}
		else {
			return FALSE;
		}
	}

	if(is_tax())
	{
		if ($ci['tax_pagination']=='enabled' and (!empty($ci['tax_posts_per_page']))){
			return $ci['tax_posts_per_page'];
		}
		elseif ($ci['tax_pagination']=='global' and $ci['pagination']=='enabled' and (!empty($ci['posts_per_page']))){
			return $ci['posts_per_page'];
		}
		else {
			return FALSE;
		}
	}
	
	// All other cases, return global if enabled.
	if ($ci['pagination']=='enabled' and (!empty($ci['posts_per_page'])))
	{
		return $ci['posts_per_page'];
	}
	else
	{
		return FALSE;
	}

}


/**
 * Retrieves the posts_per_page setting. Valid parameters are 'home', 'cat', 'tag' and 'tax'.
 * 
 * @access public
 * @param "home"|"cat"|"tag"|"tax"
 * @return void. 
 */
function get_pagination_option($name)
{
	global $ci;
	if ($ci[$name.'_pagination']=='enabled' and (!empty($ci[$name.'_posts_per_page']))){
		return $ci[$name.'_posts_per_page'];
	}
	elseif ($ci[$name.'_pagination']=='global' and $ci['pagination']=='enabled' and (!empty($ci['posts_per_page']))){
		return $ci['posts_per_page'];
	}
	else {
		return FALSE;
	}

}

// Intercepts the request and injects the appropriate posts_per_page parameter according to the request.
add_action('pre_get_posts', 'ci_paging_request');
function ci_paging_request($wp)
{
	//We don't want to mess with the admin panel.
	if(is_admin()) return;
	
	global $ci;
	$num_of_pages = '';

	if (is_home())
		$num_of_pages = get_pagination_option('home');

	if (is_category())
		$num_of_pages = get_pagination_option('cat');

	if (is_tag())
		$num_of_pages = get_pagination_option('tag');

	if (is_tax())
		$num_of_pages = get_pagination_option('tax');

	// If false is returned, it means pagination is disabled.
	if($num_of_pages===FALSE)
		$num_of_pages = -1;

	// No conditions met. Get global option.
	if ($num_of_pages == '')
	{
		if($ci['pagination']=='enabled' and !empty($ci['posts_per_page']))
			$num_of_pages = $ci['posts_per_page'];
	}

	// Don't mess with the posts if the query is explicit.
	if (!isset($wp->query_vars['posts_per_page']))
	{
		// Assign a number only if a number was found, otherwise, disable pagination.
		if ($num_of_pages != '' and absint(intval($num_of_pages))>0)
			$wp->query_vars['posts_per_page'] = $num_of_pages;
		else
			$wp->query_vars['posts_per_page'] = -1;
	}

}




/**
 * Echoes pagination links if applicable. If wp_pagenavi plugin exists, it uses it instead.
 * 
 * @access public
 * @return void. 
 */
function ci_pagination()
{ 
	global $wp_query;
	if ($wp_query->max_num_pages > 1): ?>
		<div id="paging" class="navigation group">
			<?php if (function_exists('wp_pagenavi')): ?>
				<?php wp_pagenavi(); ?>
			<?php else: ?>
				<div class="nav-previous alignleft shadow"><?php next_posts_link( __( '<span class="nav-prev-symbol nav-symbol">&larr;</span> Older posts', CI_DOMAIN ) ); ?></div>
				<div class="nav-next alignright shadow"><?php previous_posts_link( __( 'Newer posts <span class="nav-next-symbol nav-symbol">&rarr;</span>', CI_DOMAIN) ); ?></div>
			<?php endif; ?>
		</div>
	<?php endif;
}


/**
 * Echoes a CSSIgniter setting.
 * 
 * @access public
 * @param string $setting
 * @return void
 */
function ci_e_setting($setting)
{
	echo ci_setting($setting);
}

/**
 * Returns a CSSIgniter setting, or boolean FALSE on failure.
 * 
 * @access public
 * @param string $setting
 * @return string|false
 */
function ci_setting($setting)
{
	global $ci;
	if (isset($ci[$setting]) and (!empty($ci[$setting])))
		return $ci[$setting];
	else
		return FALSE;
}


/**
 * Returns the CSSIgniter logo snippet, either text or image if available.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return string
 */
function ci_logo($before="", $after=""){ 
	$snippet = $before;
		
    $snippet .= '<a href="'.home_url().'">';

    if(ci_setting('logo')){
		$snippet .= '<img src="'.ci_setting('logo').'" alt="'.ci_setting('logotext').'" />';
	} 
	else{
		$snippet .= ci_setting('logotext');
	}

    $snippet .= '</a>';
    
    $snippet .= $after;

    return $snippet;
}

/**
 * Echoes the CSSIgniter logo snippet, either text or image if available.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return void
 */
function ci_e_logo($before="", $after=""){ 
	echo ci_logo($before, $after);
}


/**
 * Returns the CSSIgniter slogan snippet, surrounded by optional strings.
 * When slogan is empty, false is returned.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return string
 */
function ci_slogan($before="", $after=""){ 
	$slogan = ci_setting('slogan');
	$snippet = $before.$slogan.$after;
	if (!empty($slogan))
		return $snippet;
	else
		return FALSE;
}

/**
 * Echoes the CSSIgniter slogan snippet, surrounded by optional strings.
 * When slogan is empty, nothing is echoed.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return void
 */
function ci_e_slogan($before="", $after=""){ 
	$slogan = ci_slogan($before, $after);
	if ($slogan) echo $slogan;
}




/**
 * Returns the date and time of the last posted post.
 * 
 * @access public
 * @return array
 */
function ci_last_update()
{
	global $post;
	$data = array();
	$posts = get_posts('numberposts=1&order=DESC&orderby=date');
	foreach ($posts as $post)
	{
		setup_postdata($post);	
		$data['date'] = get_the_date();
		$data['time'] = get_the_time();
	}
	return $data;
}


/**
 * Checks whether the current post has a Read More tag. Must be used inside the loop.
 * 
 * @access public
 * @return true|false
 */
function has_readmore()
{
	global $post;
	if(strpos(get_the_content(), "#more-")===FALSE)
		return FALSE;
	else
		return TRUE;
}



//////////////////////////////////////////////////
//
// FEEDS
//
//////////////////////////////////////////////////

/**
 * Returns the site's custom feed URL, or the default if custom doesn't exist.
 * 
 * @access public
 * @return string
 */
function ci_rss_feed()
{
	if (ci_setting('feedburner_feed'))
		return ci_setting('feedburner_feed');
	else
		return get_bloginfo('rss2_url');
}

function ci_register_custom_feed()
{
	// Register FeedBurner feed if exists, else register automatic feeds.
	if (ci_setting('feedburner_feed'))
		add_action('wp_head', 'ci_feedburner_feed');
	else
		add_theme_support( 'automatic-feed-links' );
}

function ci_feedburner_feed()
{
	$s = '<link rel="alternate" type="application/rss+xml" title="'.get_bloginfo('name').' RSS Feed" href="'.ci_setting('feedburner_feed').'" />';
	echo $s;
}






add_action('wp_head', 'ci_register_head_scripts');
function ci_register_head_scripts()
{
	// Load Google Analytics code, if available.
	if(ci_setting('google_analytics_code'))
	{
		echo html_entity_decode(ci_setting('google_analytics_code'));
	}
}

add_action('after_open_body_tag', 'ci_register_after_open_body_scripts');
function ci_register_after_open_body_scripts()
{
	// Load Buy Sell Ads code, if available.
	if(ci_setting('buysellads_code'))
	{
		echo html_entity_decode(ci_setting('buysellads_code'));
	}

}

add_action('after_setup_theme', 'ci_default_fields_set');
function ci_default_fields_set() { ci_default_options(false); }
function ci_default_options($assign_defaults=false)
{
	global $ci, $ci_defaults;
	
	if ($assign_defaults==true)
	{
		$ci = wp_parse_args($ci, $ci_defaults);
		update_option(THEME_OPTIONS, $ci);
	}
	else
	{
		foreach ($ci_defaults as $name=>$value)
		{
			if(!isset($ci[$name]))
				$ci[$name]='';
		}
	}
	
}

?>