<?php 

function process_site_categories_landing_list_sites_display($content, $data, $args) {
	//echo "args<pre>"; print_r($args); echo "</pre>";
	//echo "data<pre>"; print_r($data); echo "</pre>";

	if (($data['sites']) && (count($data['sites']))) { 

		$content .= '<div id="site-categories-wrapper">';

		if ($args['show_style'] == "ol") { $content .= '<ol class="site-categories site-categories-list">'; }
		else if ($args['show_style'] == "select") { $content .= '<select class="site-categories site-categories-select">'; }
		else { $content .= '<ul class="site-categories site-categories-list">'; }

		foreach ($data['sites'] as $site) { 

			//echo "site<pre>"; print_r($site); echo "</pre>";

			if ($args['show_style'] != "select") { 

				if (($args['icon_show'] == true) && (isset($site->icon_image_src)) && (strlen($site->icon_image_src))) { 
					//$image_src = '<img class="site-category-site-icon" width="'. $args['icon_size'] .'" height="'. $args['icon_size'] .'" 
					//	alt="'. $site->blogname .'" src="'. $site->icon_image_src .'" />';
					if (is_ssl()) {						
						$image_src = str_replace('http://', 'https://', $site->icon_image_src);
					} else {
						$image_src = $site->icon_image_src;
					}
				} else {
					$image_src = '';
				}

				$content .= '<li class="site-category-site">';
				$content .=	'<a href="'. $site->siteurl .'" class="site-category-site-url">'. $image_src 
					.'<span class="site-category-site-title">'. $site->blogname .'</span></a>';

				if (($args['show_description'] == true) && (isset($site->bact_site_description)) && (strlen($site->bact_site_description))) {
					//echo "here<br />";
					$bact_site_description = wpautop(stripslashes($site->bact_site_description));
					$bact_site_description = str_replace(']]>', ']]&gt;', $bact_site_description);						

					if (strlen($bact_site_description)) {
						$content .= '<div class="site-category-site-description">'. $bact_site_description .'</div>';
					}
				}
				$content .= '</li>';
				
			} else {
				$content .= '<option>'. $site->blogname .'</option>';
			}
		}

		if ($args['show_style'] == "ol") { $content .= '</ol>'; }
		else if ($args['show_style'] == "select") { $content .= '</select>'; }
		else { $content .= '</ul>'; }

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
		$content .= '</div>';
	} 

	return $content;
}
add_filter('site_categories_landing_list_sites_display', 'process_site_categories_landing_list_sites_display', 99, 3);
