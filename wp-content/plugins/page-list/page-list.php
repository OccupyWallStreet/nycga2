<?php
/*
Plugin Name: Page-list
Plugin URI: http://web-profile.com.ua/wordpress/plugins/page-list/
Description: [pagelist], [subpages], [siblings] and [pagelist_ext] shortcodes
Version: 3.8
Author: webvitaly
Author Email: vitalymylo(at)gmail.com
Author URI: http://web-profile.com.ua/wordpress/
*/

add_action('wp_print_styles', 'pagelist_add_stylesheet');
function pagelist_add_stylesheet() {
	wp_enqueue_style( 'page-list-style', plugins_url( '/css/page-list.css', __FILE__ ), false, '3.8', 'all' );
}

$pagelist_powered_line = "\n".'<!-- Page-list plugin v.3.8 wordpress.org/extend/plugins/page-list/ -->'."\n";

if ( !function_exists('pagelist_shortcode') ) {
	function pagelist_shortcode( $atts ) {
		global $post, $pagelist_powered_line;
		$return = '';
		extract( shortcode_atts( array(
			'depth' => '0',
			'child_of' => '0',
			'exclude' => '0',
			'exclude_tree' => '',
			'include' => '0',
			'title_li' => '',
			'number' => '',
			'offset' => '',
			'meta_key' => '',
			'meta_value' => '',
			'show_date' => '',
			'sort_column' => 'menu_order, post_title',
			'sort_order' => 'ASC',
			'link_before' => '',
			'link_after' => '',
			'class' => ''
		), $atts ) );

		$page_list_args = array(
			'depth'        => $depth,
			'child_of'     => pagelist_norm_params($child_of),
			'exclude'      => pagelist_norm_params($exclude),
			'exclude_tree' => $exclude_tree,
			'include'      => $include,
			'title_li'     => $title_li,
			'number'       => $number,
			'offset'       => $offset,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'show_date'    => $show_date,
			'date_format'  => get_option('date_format'),
			'echo'         => 0,
			'authors'      => '',
			'sort_column'  => $sort_column,
			'sort_order'   => $sort_order,
			'link_before'  => $link_before,
			'link_after'   => $link_after,
			'walker' => ''
		);
		$list_pages = wp_list_pages( $page_list_args );

		if ($list_pages) {
			$return = $pagelist_powered_line;
			$return .= '<ul class="page-list '.$class.'">'."\n".$list_pages."\n".'</ul>';
		}else{
			$return = '';
		}
		return $return;
	}
	add_shortcode( 'pagelist', 'pagelist_shortcode' );
	add_shortcode( 'page_list', 'pagelist_shortcode' );
	add_shortcode( 'page-list', 'pagelist_shortcode' ); // not good (Shortcode names should be all lowercase and use all letters, but numbers and underscores (not dashes!) should work fine too.)
	add_shortcode( 'sitemap', 'pagelist_shortcode' );
}

if ( !function_exists('subpages_shortcode') ) {
	function subpages_shortcode( $atts ) {
		global $post, $pagelist_powered_line;
		$return = '';
		extract( shortcode_atts( array(
			'depth' => '0',
			//'child_of' => '0',
			'exclude' => '0',
			'exclude_tree' => '',
			'include' => '0',
			'title_li' => '',
			'number' => '',
			'offset' => '',
			'meta_key' => '',
			'meta_value' => '',
			'show_date' => '',
			'sort_column' => 'menu_order, post_title',
			'sort_order' => 'ASC',
			'link_before' => '',
			'link_after' => '',
			'class' => ''
		), $atts ) );

		$page_list_args = array(
			'depth'        => $depth,
			'child_of'     => $post->ID,
			'exclude'      => pagelist_norm_params($exclude),
			'exclude_tree' => $exclude_tree,
			'include'      => $include,
			'title_li'     => $title_li,
			'number'       => $number,
			'offset'       => $offset,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'show_date'    => $show_date,
			'date_format'  => get_option('date_format'),
			'echo'         => 0,
			'authors'      => '',
			'sort_column'  => $sort_column,
			'sort_order'   => $sort_order,
			'link_before'  => $link_before,
			'link_after'   => $link_after,
			'walker' => ''
		);
		$list_pages = wp_list_pages( $page_list_args );

		if ($list_pages) {
			$return = $pagelist_powered_line;
			$return .= '<ul class="page-list subpages-page-list '.$class.'">'."\n".$list_pages."\n".'</ul>';
		}else{
			$return = '';
		}
		return $return;
	}
	add_shortcode( 'subpages', 'subpages_shortcode' );
	add_shortcode( 'sub_pages', 'subpages_shortcode' );
	add_shortcode( 'sub-pages', 'subpages_shortcode' ); // not good (Shortcode names should be all lowercase and use all letters, but numbers and underscores (not dashes!) should work fine too.)
	add_shortcode( 'children', 'subpages_shortcode' );
}

if ( !function_exists('siblings_shortcode') ) {
	function siblings_shortcode( $atts ) {
		global $post, $pagelist_powered_line;
		$return = '';
		extract( shortcode_atts( array(
			'depth' => '0',
			//'child_of' => '0',
			'exclude' => '0',
			'exclude_tree' => '',
			'include' => '0',
			'title_li' => '',
			'number' => '',
			'offset' => '',
			'meta_key' => '',
			'meta_value' => '',
			'show_date' => '',
			'sort_column' => 'menu_order, post_title',
			'sort_order' => 'ASC',
			'link_before' => '',
			'link_after' => '',
			'class' => ''
		), $atts ) );

		if( $exclude == 'current' || $exclude == 'this' ){
			$exclude = $post->ID;
		}

		$page_list_args = array(
			'depth'        => $depth,
			'child_of'     => $post->post_parent,
			'exclude'      => pagelist_norm_params($exclude),
			'exclude_tree' => $exclude_tree,
			'include'      => $include,
			'title_li'     => $title_li,
			'number'       => $number,
			'offset'       => $offset,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'show_date'    => $show_date,
			'date_format'  => get_option('date_format'),
			'echo'         => 0,
			'authors'      => '',
			'sort_column'  => $sort_column,
			'sort_order'   => $sort_order,
			'link_before'  => $link_before,
			'link_after'   => $link_after,
			'walker' => ''
		);
		$list_pages = wp_list_pages( $page_list_args );

		if ($list_pages) {
			$return = $pagelist_powered_line;
			$return .= '<ul class="page-list siblings-page-list '.$class.'">'."\n".$list_pages."\n".'</ul>';
		}else{
			$return = '';
		}
		return $return;
	}
	add_shortcode( 'siblings', 'siblings_shortcode' );
}

if ( !function_exists('pagelist_ext_shortcode') ) {
	function pagelist_ext_shortcode( $atts ) {
		global $post, $pagelist_powered_line;
		$return = '';
		extract( shortcode_atts( array(
			'show_image' => 1,
			'show_first_image' => 0,
			'show_title' => 1,
			'show_content' => 1,
			'more_tag' => 1,
			'limit_content' => 250,
			'image_width' => '50',
			'image_height' => '50',
			'child_of' => '',
			'sort_order' => 'ASC',
			'sort_column' => 'menu_order, post_title',
			'hierarchical' => 1,
			'exclude' => '0',
			'include' => '0',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish',
			'class' => '',
			'strip_tags' => 1,
			'strip_shortcodes' => 1,
			'show_child_count' => 0,
			'child_count_template' => 'Subpages: %child_count%',
			'show_meta_key' => '',
			'meta_template' => '%meta%'
		), $atts ) );

		if( $child_of == '' ){ // show subpages if child_of is empty 
			$child_of = $post->ID;
		}

		$page_list_ext_args = array(
			'show_image' => $show_image,
			'show_first_image' => $show_first_image,
			'show_title' => $show_title,
			'show_content' => $show_content,
			'more_tag' => $more_tag,
			'limit_content' => $limit_content,
			'image_width' => $image_width,
			'image_height' => $image_height,
			'child_of' => pagelist_norm_params($child_of),
			'sort_order' => $sort_order,
			'sort_column' => $sort_column,
			'hierarchical' => $hierarchical,
			'exclude' => pagelist_norm_params($exclude),
			'include' => $include,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'authors' => $authors,
			'parent' => pagelist_norm_params($parent),
			'exclude_tree' => $exclude_tree,
			'number' => '', // $number - own counter
			'offset' => 0, // $offset - own offset
			'post_type' => $post_type,
			'post_status' => $post_status,
			'class' => $class,
			'strip_tags' => $strip_tags,
			'strip_shortcodes' => $strip_shortcodes,
			'show_child_count' => $show_child_count,
			'child_count_template' => $child_count_template,
			'show_meta_key' => $show_meta_key,
			'meta_template' => $meta_template
		);
		$page_list_ext_args_all = array(
			'show_image' => $show_image,
			'show_first_image' => $show_first_image,
			'show_title' => $show_title,
			'show_content' => $show_content,
			'more_tag' => $more_tag,
			'limit_content' => $limit_content,
			'image_width' => $image_width,
			'image_height' => $image_height,
			'child_of' => 0, // for showing all pages
			'sort_order' => $sort_order,
			'sort_column' => $sort_column,
			'hierarchical' => $hierarchical,
			'exclude' => pagelist_norm_params($exclude),
			'include' => $include,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'authors' => $authors,
			'parent' => pagelist_norm_params($parent),
			'exclude_tree' => $exclude_tree,
			'number' => '', // $number - own counter
			'offset' => 0, // $offset - own offset
			'post_type' => $post_type,
			'post_status' => $post_status,
			'class' => $class,
			'strip_tags' => $strip_tags,
			'strip_shortcodes' => $strip_shortcodes,
			'show_child_count' => $show_child_count,
			'child_count_template' => $child_count_template,
			'show_meta_key' => $show_meta_key,
			'meta_template' => $meta_template
		);
		$list_pages = get_pages( $page_list_ext_args );
		if( count( $list_pages ) == 0 ){ // if there is no subpages
			$list_pages = get_pages( $page_list_ext_args_all ); // we are showing all pages
		}
		$list_pages_html = '';
		$count = 0;
		$offset_count = 0;
		foreach($list_pages as $page){
			$count++;
			$offset_count++;
			if ( !empty( $offset ) && is_numeric( $offset ) && $offset_count <= $offset ) {
				$count = 0; // number counter to zero if offset is not finished
			}
			if ( ( !empty( $offset ) && is_numeric( $offset ) && $offset_count > $offset ) || ( empty( $offset ) ) || ( !empty( $offset ) && !is_numeric( $offset ) ) ) {
				if ( ( !empty( $number ) && is_numeric( $number ) && $count <= $number ) || ( empty( $number ) ) || ( !empty( $number ) && !is_numeric( $number ) ) ) {
					$link = get_permalink( $page->ID );
					$list_pages_html .= '<div class="page-list-ext-item">';
					if( $show_image == 1 ){
						if (function_exists('get_the_post_thumbnail')) {
							if( get_the_post_thumbnail($page->ID) ){
								$list_pages_html .= '<div class="page-list-ext-image"><a href="'.$link.'" title="'.esc_attr($page->post_title).'">';
								$list_pages_html .= get_the_post_thumbnail($page->ID, array($image_width,$image_height));
								$list_pages_html .= '</a></div> ';
							}else{
								if( $show_first_image == 1 ){
									$img_scr = page_list_get_first_image( $page->post_content );
									if( !empty( $img_scr ) ){
										$list_pages_html .= '<div class="page-list-ext-image"><a href="'.$link.'" title="'.esc_attr($page->post_title).'">';
										$list_pages_html .= '<img src="'.$img_scr.'" width="'.$image_width.'" />'; // not using height="'.$image_height.'" because images could be not square shaped and they will be stretched
										$list_pages_html .= '</a></div> ';
									}
								}
							}
						}else{
							if( $show_first_image == 1 ){
								$img_scr = page_list_get_first_image( $page->post_content );
								if( !empty( $img_scr ) ){
									$list_pages_html .= '<div class="page-list-ext-image"><a href="'.$link.'" title="'.esc_attr($page->post_title).'">';
									$list_pages_html .= '<img src="'.$img_scr.'" width="'.$image_width.'" />'; // not using height="'.$image_height.'" because images could be not square shaped and they will be stretched
									$list_pages_html .= '</a></div> ';
								}
							}
						}
					}


					if( $show_title == 1 ){
						$list_pages_html .= '<h3 class="page-list-ext-title"><a href="'.$link.'" title="'.esc_attr($page->post_title).'">'.$page->post_title.'</a></h3>';
					}
					if( $show_content == 1 ){
						//$content = apply_filters('the_content', $page->post_content);
						//$content = str_replace(']]>', ']]&gt;', $content); // both used in default the_content() function

						if( !empty( $page->post_excerpt ) ){
							$text_content = $page->post_excerpt;
						}else{
							$text_content = $page->post_content;
						}

						if ( post_password_required($page) ) {
							$content = '<!-- password protected -->';
						}else{
							$content = page_list_parse_content( $text_content, $limit_content, $strip_tags, $strip_shortcodes, $more_tag );
							$content = do_shortcode( $content );
							
							if( $show_title == 0 ){ // make content as a link if there is no title
								$content = '<a href="'.$link.'">'.$content.'</a>';
							}
						}

						$list_pages_html .= '<div class="page-list-ext-item-content">'.$content.'</div>';

					}
					if( $show_child_count == 1 ){
						$count_subpages = count(get_pages("child_of=".$page->ID));
						if( $count_subpages > 0 ){ // hide empty
							$child_count_pos = strpos($child_count_template, '%child_count%'); // check if we have %child_count% marker in template
							if($child_count_pos === false) { // %child_count% not found in template
								$child_count_template_html =  $child_count_template.' '.$count_subpages;
								$list_pages_html .= '<div class="page-list-ext-child-count">'.$child_count_template_html.'</div>';
							} else { // %child_count% found in template
								$child_count_template_html =  str_replace('%child_count%', $count_subpages, $child_count_template);
								$list_pages_html .= '<div class="page-list-ext-child-count">'.$child_count_template_html.'</div>';
							}
						}
					}
					if( $show_meta_key != '' ){
						$post_meta = get_post_meta($page->ID, $show_meta_key, true);
						if( !empty($post_meta) ){ // hide empty
							$meta_pos = strpos($meta_template, '%meta%'); // check if we have %meta% marker in template
							if($meta_pos === false) { // %meta% not found in template
								$meta_template_html =  $meta_template.' '.$post_meta;
								$list_pages_html .= '<div class="page-list-ext-meta">'.$meta_template_html.'</div>';
							} else { // %meta% found in template
								$meta_template_html =  str_replace('%meta%', $post_meta, $meta_template);
								$list_pages_html .= '<div class="page-list-ext-meta">'.$meta_template_html.'</div>';
							}
						}
					}
					$list_pages_html .= '</div>'."\n";
				}
			}
		}
		if ($list_pages_html) {
			$return = $pagelist_powered_line;
			$return .= '<div class="page-list page-list-ext '.$class.'">'."\n".$list_pages_html."\n".'</div>';
		}else{
			$return = '';
		}
		return $return;
	}
	add_shortcode( 'pagelist_ext', 'pagelist_ext_shortcode' );
}

if ( !function_exists('pagelist_norm_params') ) {
	function pagelist_norm_params( $str ) {
		global $post;
		$new_str = $str;
		$new_str = str_replace('this', $post->ID, $new_str); // exclude this page
		$new_str = str_replace('current', $post->ID, $new_str); // exclude current page
		$new_str = str_replace('curent', $post->ID, $new_str); // exclude curent page with mistake
		$new_str = str_replace('parent', $post->post_parent, $new_str); // exclude parent page
		return $new_str;
	}
}

if ( !function_exists('page_list_parse_content') ) {
	function page_list_parse_content($content, $limit_content = 250, $strip_tags = 1, $strip_shortcodes = 1, $more_tag = 1) {

		$more_tag_found = 0;
		$content_before_more_tag_length = 0;

		if( $more_tag ){ // "more_tag" have higher priority than "limit_content"
			if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
				$more_tag_found = 1;
				$more_tag = $matches[0];
				$content_temp = explode($matches[0], $content);
				$content_temp = $content_temp[0];
				$content_before_more_tag_length = strlen($content_temp);
				$content = substr_replace($content, '###more###', $content_before_more_tag_length, 0);
			}
		}

		// replace php and comments tags so they do not get stripped
		//$content = preg_replace("@<\?@", "#?#", $content);
		//$content = preg_replace("@<!--@", "#!--#", $content); // save html comments
		// strip tags normally
		//$content = strip_tags($content);
		if( $strip_tags ){
			$content = str_replace('</', ' </', $content); // <p>line1</p><p>line2</p> - adding space between lines
			$content = strip_tags($content); // ,'<p>'
		}
		// return php and comments tags to their origial form
		//$content = preg_replace("@#\?#@", "<?", $content);
		//$content = preg_replace("@#!--#@", "<!--", $content);

		if( $strip_shortcodes ){
			$content = strip_shortcodes( $content );
		}

		if( $more_tag && $more_tag_found ){ // "more_tag" have higher priority than "limit_content"
			$fake_more_pos = mb_strpos($content, '###more###', 0, 'UTF-8');
			if( $fake_more_pos === false ) {
				// substring not found in string and this is strange :)
			} else {
				$content = mb_substr($content, 0, $fake_more_pos, 'UTF-8');
			}
		}else{
			if( strlen($content) > $limit_content ){ // limiting content
				$pos = strpos($content, ' ', $limit_content); // find first space position
				if ($pos !== false) {
					$first_space_pos = $pos;
				}else{
					$first_space_pos = $limit_content;
				}
				$content = mb_substr($content, 0, $first_space_pos, 'UTF-8') . '...';
			}
		}

		$output = force_balance_tags($content);
		return $output;
	}
}

if ( !function_exists('page_list_get_first_image') ) {
	function page_list_get_first_image( $content='' ) {
		$first_img = '';
		//ob_start();
		//ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
		$first_img = $matches[1][0];
		if(empty($first_img)){ // no image found
			$first_img = '';
		}
		return $first_img;
	}
}