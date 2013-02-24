<?php

function process_site_categories_accorion_display($content, $data, $args) {
//	echo "args<pre>"; print_r($args); echo "</pre>";
//	echo "data<pre>"; print_r($data); echo "</pre>";

	if ((isset($data['categories'])) && (count($data['categories']))) {

		$content .= '<div id="site-categories-wrapper">';

		$content .= '<div class="site-categories-accordion" style="width: 100%; float: left;">';

		foreach ($data['categories'] as $category) { 

			$content .= '<div class="site-categories-accordion-header">';

			//if ($category->count > 0)
			//	$content .= '<a href="#">';

			if ( ($args['icon_show'] == true) && (isset($category->icon_image_src)) && (strlen($category->icon_image_src)) ) {
				if (is_ssl()) {						
					$image_src = str_replace('http://', 'https://', $category->icon_image_src);
				} else {
					$image_src = $category->icon_image_src;
				}
				
				$content .= '<div style="float: left; width: '.$args['icon_size'] .'px; margin-right: 10px;"><img class="site-category-icon" 
					width="'. $args['icon_size'] .'" height="'. $args['icon_size'] .'" alt="'. $category->name .'" src="'. $image_src .'" /></div>';
			} 
			
			$content .= '<div style="float: left;"><span class="site-category-title">'. $category->name .'</span>';

			if ($args['show_counts']) {
				$count = $category->count;
				if ($count == 0) {
					if ((isset($category->children_count)) && ($category->children_count > 0)) {
						$count = $category->children_count;
					}
				}
				$content .= '<span class="site-category-count">('. $count .')</span>';
			}

			//if ($category->count > 0)
			//	$content .= '</a>';
				
			if (($args['show_description']) && (strlen($category->description))) {

				//$bact_category_description = apply_filters('the_content', $category->description);
				$bact_category_description = wpautop(stripslashes($category->description));
				$bact_category_description = str_replace(']]>', ']]&gt;', $bact_category_description);
			
				if (strlen($bact_category_description)) {
					$content .= '<div class="site-category-description-parent">'. $bact_category_description .'</div>';
				}
			}
			$content .= '</div></div>';

			$content .= '<div class="site-categories-accordion-details">';


			if ((isset($category->children)) && (count($category->children))) {
				$content .= '<ul class="site-categories-children">';

				foreach( $category->children as $category_child) {
					//echo "category_child<pre>"; print_r($category_child); echo "</pre>";
					
					$content .=	'<li class="site-categories-child" style="width: 100%">';

					if ( ($args['icon_show_children'] == true) && (isset($category_child->icon_image_src))) {
						$content .= '<img class="site-category-icon" width="'. $args['icon_size_children'] .'" height="'. $args['icon_size_children'] .'"
						 alt="'. $category_child->name .'" src="'. $category_child->icon_image_src .'" />';
					} 

					if ($category->count > 0)
						$content .=	'<a href="'. $category_child->bcat_url .'">';

					$content .= '<span class="site-category-title">'. $category_child->name .'</span>';

					if ($args['show_counts_children']) {
						$content .= '<span class="site-category-count">('. $category_child->count .')</span>';
					}

					if ($category->count > 0)
						$content .= '</a>';
					
					
					if (($args['show_description_children']) && (strlen($category_child->description))) {

						$bact_category_description = apply_filters('the_content', $category_child->description);
						$bact_category_description = str_replace(']]>', ']]&gt;', $bact_category_description);

						if (strlen($bact_category_description)) {
							$content .= '<div class="site-category-description">'. $bact_category_description .'</div>';
						}
					}
						
					$content .= '</li>';
				}

				$content .= '</ul>';						
			}
			$content .= '</li>';

			
			$content .= '</div>';
		}

		$content .= "</div>";

		if ((isset($data['prev'])) || (isset($data['next']))) { 

			$content .= '<div id="site-categories-navigation">';

			if (isset($data['prev'])) { 
				$content .= '<a href="'. $data['prev']['link_url'] .'">'. $data['prev']['link_label'] .'</a>';
			} 

			if (isset($data['next'])) { 
				$content .= '<a href="'. $data['next']['link_url'] .'">'. $data['next']['link_label'] .'</a>';
			}
			$content .= '</div>';
		}
		$content .= "</div>";
	}

	return $content;
}
add_filter('site_categories_landing_accordion_display', 'process_site_categories_accorion_display', 99, 3);
