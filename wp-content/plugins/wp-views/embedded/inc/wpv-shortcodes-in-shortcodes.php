<?php
/**
 * Filter the_content tag 
 * Added support for internal shortcode execution
 * This handles Types shortcodes within other shortcodes
 * eg.  [app [types field="my_field"]]
 */

function wpv_resolve_internal_shortcodes($content) {
	$content = wpv_parse_content_shortcodes($content);
	
	return $content;
}

// adding filter with priority before do_shortcode and other WP standard filters
add_filter('the_content', 'wpv_resolve_internal_shortcodes', 9);

/**
 * Parse shortcodes in the page content
 * @param string page content to be evaluated for internal shortcodes
 */
function wpv_parse_content_shortcodes($content) {
	$inner_expressions = array();
	$inner_expressions[] = "/\\[types.*?\\].*?\\[\\/types\\]/i";
	$inner_expressions[] = "/\\[(wpv-post-|types).*?\\]/i";
	
	// search for shortcodes
	$matches = array();
	$counts = _find_outer_brackets($content, $matches);
	
	// iterate 0-level shortcode elements
	if($counts > 0) {
		foreach($matches as $match) {
			
			foreach ($inner_expressions as $inner_expression) {
				$inner_counts = preg_match_all($inner_expression, $match, $inner_matches);
				
				// replace all 1-level inner shortcode matches
				if($inner_counts > 0) {
					foreach($inner_matches[0] as &$inner_match) {
						// execute shortcode content and replace
						$replacement = do_shortcode($inner_match);
						$resolved_match = $replacement;
						$content = str_replace($inner_match, $resolved_match, $content);
						$match = str_replace($inner_match, $resolved_match, $match);
					}
				}
			}
		}
	}
	
	return $content;
}

function _find_outer_brackets($content, &$matches) {
	$count = 0;
	
	$first = strpos($content, '[');
	if ($first !== FALSE) {
		$length = strlen($content);
		$brace_count = 0;
		$brace_start = -1;
		for ($i = $first; $i < $length; $i++) {
			if ($content[$i] == '[') {
				if($brace_count == 0) {
					$brace_start = $i + 1;
				}
				$brace_count++;
			}
			if ($content[$i] == ']') {
				if ($brace_count > 0) {
					$brace_count--;
					if ($brace_count == 0) {
						$matches[] = substr($content, $brace_start, $i - $brace_start);
						$count++;
					}
				}
			}
		}
	}
	
	return $count;
}

// register filter for the wpv_do_shortcode Views rendering
add_filter('wpv-pre-do-shortcode', 'wpv_parse_content_shortcodes');


// Special handling to get shortcodes rendered in widgets.
function wpv_resolve_internal_shortcodes_for_widgets($content) {
	$content = wpv_parse_content_shortcodes($content);
	
	return do_shortcode($content);
}

add_filter('widget_text', 'wpv_resolve_internal_shortcodes_for_widgets');
