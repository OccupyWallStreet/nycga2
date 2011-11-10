<?php

// GLOBAL VARIABLES
$GLOBALS['template_path'] = get_bloginfo('template_directory');
$options = array(); 
global $options;

// NUMBER SELECT OPTIONS
$other_entries = array("1","2","3","4","5","6","7","8","9","10");
$other_entries2 = array("0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20");

// BACKGROUND ARRAY OPTIONS
$background_path = BIZZ_LIB_THEME . '/images/stripes/'; 
$backgrounds = array();

if ( is_dir($background_path) ) {
	if ($background_dir = opendir($background_path) ) { 
		while ( ($background_file = readdir($background_dir)) !== false ) {
			if(stristr($background_file, ".gif") !== false) {
				$backgrounds[] = $background_file;
			}
		}	
	}
}	

// ALT STYLESHEET ARRAY OPTIONS
$alt_stylesheet_path = BIZZ_LIB_THEME . '/skins/';
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
	if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
		while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
			if(stristr($alt_stylesheet_file, ".css") !== false) {
				$alt_stylesheets[] = $alt_stylesheet_file;
			}
		}
        sort($alt_stylesheets);		
	}
}	

// CATEGORIES ARRAY OPTIONS
$pn_categories_obj = get_categories('sort=ASC');
$pn_categories = array();

// Categories Name Load
foreach ($pn_categories_obj as $pn_cat) {
	$pn_categories[$pn_cat->name] = $pn_cat->term_id;
}

// PAGES ARRAY OPTIONS
$pne_pages_obj = get_posts('orderby=title&numberposts=-1&order=ASC&post_type=page');
$pne_pages = array();

// Pages Exclude Load
foreach ($pne_pages_obj as $pne_pag) {
	$pne_pages[$pne_pag->post_title] = $pne_pag->ID;
}

// EXCLUDE CATEGORIES OPTIONS

// Exclude Categories by Name 1
function category_exclude($options) {
	$options[] = array(	"type" => "wraptop");														
	$cats = get_categories('sort=ASC');
	foreach ($cats as $cat) {	
	    if ($cat->cat_ID == '1') { $disabled = "disabled"; } else { $disabled = ""; };
			$options[] = array(	"name" => "",
						"label" => $cat->name . " (" . $cat->count . ") &nbsp;<small style='color:#aaaaaa'>id=" . $cat->cat_ID . "</small>",
						"id" => "cat_exclude_".$cat->cat_ID,
						"disabled" => "".$disabled."",
						"type" => "checkbox2");						
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Categories by Name 2
function category_exclude2($options) {
	$options[] = array(	"type" => "wraptop");														
	$cats = get_categories('sort=ASC');
	foreach ($cats as $cat) {	
	    if ($cat->cat_ID == '1') { $disabled = "disabled"; } else { $disabled = ""; };
			$options[] = array(	"name" => "",
						"label" => $cat->name . " (" . $cat->count . ") &nbsp;<small style='color:#aaaaaa'>id=" . $cat->cat_ID . "</small>",
						"id" => "cat_exclude2_".$cat->cat_ID,
						"disabled" => "".$disabled."",
						"type" => "checkbox2");						
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Categories by Name 3
function category_exclude3($options) {
	$options[] = array(	"type" => "wraptop");														
	$cats = get_categories('sort=ASC');
	foreach ($cats as $cat) {	
	    if ($cat->cat_ID == '1') { $disabled = "disabled"; } else { $disabled = ""; };
			$options[] = array(	"name" => "",
						"label" => $cat->name . " (" . $cat->count . ") &nbsp;<small style='color:#aaaaaa'>id=" . $cat->cat_ID . "</small>",
						"id" => "cat_exclude3_".$cat->cat_ID,
						"disabled" => "".$disabled."",
						"type" => "checkbox2");						
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Categories by Name 4
function category_exclude4($options) {
	$options[] = array(	"type" => "wraptop");														
	$cats = get_categories('sort=ASC');
	foreach ($cats as $cat) {	
	    if ($cat->cat_ID == '1') { $disabled = "disabled"; } else { $disabled = ""; };
			$options[] = array(	"name" => "",
						"label" => $cat->name . " (" . $cat->count . ") &nbsp;<small style='color:#aaaaaa'>id=" . $cat->cat_ID . "</small>",
						"id" => "cat_exclude4_".$cat->cat_ID,
						"disabled" => "".$disabled."",
						"type" => "checkbox2");						
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// EXCLUDE PAGES OPTIONS

// Exclude Pages by Name 1
function pages_exclude($options) {
	$options[] = array(	"type" => "wraptop");						
	$pags = get_posts('orderby=title&numberposts=-1&order=ASC&post_type=page');	
	foreach ($pags as $pag) {
	    $thumb = get_post_meta($pag->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pag->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pag->ID . "</small> ".$img_link."",
						        "id" => "pag_exclude_".$pag->ID,
								"type" => "checkbox2");					
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Pages by Name 2
function pages_exclude2($options) {
	$options[] = array(	"type" => "wraptop");						
	$pags = get_posts('orderby=title&numberposts=-1&order=ASC&post_type=page');	
	foreach ($pags as $pag) {
        $thumb = get_post_meta($pag->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pag->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pag->ID . "</small> ".$img_link."",
						        "id" => "pag_exclude2_".$pag->ID,
								"type" => "checkbox2");				
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Pages by Name 3
function pages_exclude3($options) {
	$options[] = array(	"type" => "wraptop");						
	$pags = get_posts('orderby=title&numberposts=-1&order=ASC&post_type=page');	
	foreach ($pags as $pag) {
        $thumb = get_post_meta($pag->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pag->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pag->ID . "</small> ".$img_link."",
						        "id" => "pag_exclude3_".$pag->ID,
								"type" => "checkbox2");				
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Pages by Name 4
function pages_exclude4($options) {
	$options[] = array(	"type" => "wraptop");						
	$pags = get_posts('orderby=title&numberposts=-1&order=ASC&post_type=page');	
	foreach ($pags as $pag) {	
	    $thumb = get_post_meta($pag->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pag->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pag->ID . "</small> ".$img_link."",
						        "id" => "pag_exclude4_".$pag->ID,
								"type" => "checkbox2");					
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Pages by Name 4
function pages_exclude_seo($options) {
	$options[] = array(	"type" => "wraptop");						
	$pags = get_posts('orderby=title&numberposts=-1&order=ASC&post_type=page');	
	foreach ($pags as $pag) {	
	    $thumb = get_post_meta($pag->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pag->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pag->ID . "</small> ".$img_link."",
						        "id" => "pag_exclude_seo_".$pag->ID,
								"type" => "checkbox2");					
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// EXCLUDE POSTS OPTIONS

// Exclude Posts by Name 1
function posts_exclude($options) {
	$options[] = array(	"type" => "wraptop");						
	$psts = get_posts('orderby=title&order=ASC&numberposts=-1');	
	foreach ($psts as $pst) {	
	    $thumb = get_post_meta($pst->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pst->ID . "</small> ".$img_link."",
						        "id" => "pst_exclude_".$pst->ID,
								"type" => "checkbox2");					
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Posts by Name 2
function posts_exclude2($options) {
	$options[] = array(	"type" => "wraptop");						
	$psts = get_posts('orderby=title&order=ASC&numberposts=-1');	
	foreach ($psts as $pst) {	
	$thumb = get_post_meta($pst->ID, 'image', true);
	    $thumb = get_post_meta($pst->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pst->ID . "</small> ".$img_link."",
						        "id" => "pst_exclude2_".$pst->ID,
								"type" => "checkbox2");				
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Posts by Name 3
function posts_exclude3($options) {
	$options[] = array(	"type" => "wraptop");						
	$psts = get_posts('orderby=title&order=ASC&numberposts=-1');	
	foreach ($psts as $pst) {	
	$thumb = get_post_meta($pst->ID, 'image', true);
	    $thumb = get_post_meta($pst->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pst->ID . "</small> ".$img_link."",
						        "id" => "pst_exclude3_".$pst->ID,
								"type" => "checkbox2");				
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// Exclude Posts by Name 4
function posts_exclude4($options) {
	$options[] = array(	"type" => "wraptop");						
	$psts = get_posts('orderby=title&order=ASC&numberposts=-1');	
	foreach ($psts as $pst) {	
	$thumb = get_post_meta($pst->ID, 'image', true);
	    $thumb = get_post_meta($pst->ID, 'image', true);
	    if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=" . $pst->ID . "</small> ".$img_link."",
						        "id" => "pst_exclude4_".$pst->ID,
								"type" => "checkbox2");					
	}	
	$options[] = array(	"type" => "wrapbottom");		
	return $options;
}

// LIST EXCLUDED CATEGORIES
function get_inc_categories($label) {	
	$include = '';
	$counter = 0;
	$catsx = get_categories('hide_empty=0');	
	foreach ($catsx as $cat) {		
		$counter++;		
		if ( isset($GLOBALS['opt'][$label.$cat->cat_ID]) && $GLOBALS['opt'][$label.$cat->cat_ID] ) {
			if ( $counter >= 1 ) { $include .= ','; }
			$include .= $cat->cat_ID;
		}
	}	
	return $include;
}

// LIST EXCLUDED PAGES
function get_inc_pages($label) {	
	$include = '';
	$counter = 0;
	$pagsx = get_posts('orderby=title&numberposts=-1&order=ASC&post_type=page');	
	foreach ($pagsx as $pag) {		
		$counter++;		
		if ( $GLOBALS['opt'][$label.$pag->ID]['true'] ) {
			if ( $counter <> 1 ) { $include .= ','; }
			$include .= $pag->ID;
			}	
	}	
	return $include;
}

// LIST EXCLUDED POSTS
function get_inc_posts($label) {	
	$include = '';
	$counter = 0;
	$pstsx = get_posts('sort_order=ASC&numberposts=-1');	
	foreach ($pstsx as $pst) {		
		$counter++;		
		if ( $GLOBALS['opt'][$label.$pst->ID] ) {
			if ( $counter <> 1 ) { $include .= ','; }
			$include .= $pst->ID;
			}	
	}	
	return $include;
}

// LIST EXCLUDED ATTACHMENTS
function get_inc_att($label) {	
	$include = '';
	if ( isset($GLOBALS['opt']) && $GLOBALS['opt'] <> '' ){
	foreach ($GLOBALS['opt'] as $key => $value) {
		if(substr($key, 0, 10) == $label) {
		    $include .= ",";
			$include .= preg_replace("/[^0-9]/", '', $key);
		}
	}
	}
	return $include;
}

// SELECT IMAGE ATTACHMENTS

// Select multiple images
function select_uploads($options,$selid) {
	$options[] = array(	"type" => "sorttop");	
	global $bloghomeurl;
	
	// get selected attachements in correct order
	$sliderpages = get_inc_att($selid);
	$sliderarray=split(",",$sliderpages);
	$sliderarray = array_diff($sliderarray, array(""));
	foreach ( $sliderarray as $featitem ) {
	    $pstsx = get_posts('post_type=attachment&post_mime_type=image&include='.$featitem.'');	
	    foreach ($pstsx as $pst) {	
	        $thumb = $pst->guid;
	        // if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
			$image_attributes = wp_get_attachment_image_src( $pst->ID ); // returns an array
			$img_link = ( $image_attributes ) ? '<img src="'.$image_attributes[0].'" width="21" height="21" style="vertical-align:middle">&nbsp;' : '';
		    $edit_link = '<a style="font-size:10px" href="'.$bloghomeurl.'wp-admin/media.php?attachment_id='.$pst->ID.'&action=edit">edit</a>';
			$options[] = array(	"label" => $img_link . $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=".$pst->ID."&nbsp;&nbsp;".$edit_link."</small>",
						        "id" => $selid.$pst->ID,
								"type" => "checkbox2");			
	    }
	}
	
	// get remaining attachements in alphabetic order
	$psts = get_posts('orderby=title&order=ASC&numberposts=-1&post_type=attachment&post_mime_type=image&exclude='.get_inc_att($selid).'');	
	foreach ($psts as $pst) {	
	    $thumb = $pst->guid;
	    // if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
		$image_attributes = wp_get_attachment_image_src( $pst->ID ); // returns an array
		$img_link = ( $image_attributes ) ? '<img src="'.$image_attributes[0].'" width="21" height="21" style="vertical-align:middle">&nbsp;' : '';
		$edit_link = '<a style="font-size:10px" href="'.$bloghomeurl.'wp-admin/media.php?attachment_id='.$pst->ID.'&action=edit">edit</a>';
			$options[] = array(	"label" => $img_link . $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=".$pst->ID."&nbsp;&nbsp;".$edit_link."</small>",
						        "id" => $selid.$pst->ID,
								"type" => "checkbox2");					
	}
	
	$options[] = array(	"type" => "sortbottom");		
	return $options;
}

// SELECT PAGES

// Select multiple pages
function select_pages($options,$selid) {
	$options[] = array(	"type" => "sorttop");	
	global $bloghomeurl;
	
	// get selected pages in correct order
	$sliderpages = get_inc_att($selid);
	$sliderarray=split(",",$sliderpages);
	$sliderarray = array_diff($sliderarray, array(""));
	foreach ( $sliderarray as $featitem ) {
	    $pstsx = get_posts('post_type=page&include='.$featitem.'');	
	    foreach ($pstsx as $pst) {	
	        $thumb = $pst->guid;
	        // if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
		    $edit_link = '<a style="font-size:10px" href="'.$bloghomeurl.'wp-admin/post.php?post='.$pst->ID.'&action=edit">edit</a>';
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=".$pst->ID."&nbsp;&nbsp;".$edit_link."</small>",
						        "id" => $selid.$pst->ID,
								"type" => "checkbox2");			
	    }
	}
	
	// get remaining pages in alphabetic order
	$psts = get_posts('orderby=title&order=ASC&numberposts=-1&post_type=page&exclude='.get_inc_att($selid).'');	
	foreach ($psts as $pst) {	
	    $thumb = $pst->guid;
	    // if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
		$edit_link = '<a style="font-size:10px" href="'.$bloghomeurl.'wp-admin/post.php?post='.$pst->ID.'&action=edit">edit</a>';
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=".$pst->ID."&nbsp;&nbsp;".$edit_link."</small>",
						        "id" => $selid.$pst->ID,
								"type" => "checkbox2");					
	}
	
	$options[] = array(	"type" => "sortbottom");		
	return $options;
}

// SELECT POSTS

// Select multiple posts
function select_posts($options,$selid) {
	$options[] = array(	"type" => "sorttop");	
	global $bloghomeurl;
	
	// get selected posts in correct order
	$sliderpages = get_inc_att($selid);
	$sliderarray=split(",",$sliderpages);
	$sliderarray = array_diff($sliderarray, array(""));
	foreach ( $sliderarray as $featitem ) {
	    $pstsx = get_posts('post_type=post&include='.$featitem.'');	
	    foreach ($pstsx as $pst) {	
	        $thumb = $pst->guid;
	        // if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
		    $edit_link = '<a style="font-size:10px" href="'.$bloghomeurl.'wp-admin/post.php?post='.$pst->ID.'&action=edit">edit</a>';
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=".$pst->ID."&nbsp;&nbsp;".$edit_link."</small>",
						        "id" => $selid.$pst->ID,
								"type" => "checkbox2");			
	    }
	}
	
	// get remaining posts in alphabetic order
	$psts = get_posts('orderby=title&order=ASC&numberposts=-1&post_type=post&exclude='.get_inc_att($selid).'');	
	foreach ($psts as $pst) {	
	    $thumb = $pst->guid;
	    // if ($thumb <> '') { $img_link = '<img src="'. $thumb .'" width="21" height="21" alt="" class="fr" />'; } else { $img_link = ''; }
		$edit_link = '<a style="font-size:10px" href="'.$bloghomeurl.'wp-admin/post.php?post='.$pst->ID.'&action=edit">edit</a>';
			$options[] = array(	"label" => $pst->post_title . " &nbsp;<small style='color:#aaaaaa'>id=".$pst->ID."&nbsp;&nbsp;".$edit_link."</small>",
						        "id" => $selid.$pst->ID,
								"type" => "checkbox2");					
	}
	
	$options[] = array(	"type" => "sortbottom");		
	return $options;
}

/* Fix quotes for serialized data */
/*------------------------------------------------------------------*/
function bizz_reverse_escape($str) {
  $search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
  $replace=array("\\","\0","\n","\r","\x1a","'",'"');
  return str_replace($search,$replace,$str);
}

// check to see if a string needs to be escaped for database input
	function bizz_escapeit ( $text ) {
		
		if ( get_magic_quotes_gpc() ) {
			$text = stripslashes($text);
		}
		
		if ( !is_numeric($text) ) {
			
			$text = mysql_real_escape_string($text);
			
		}
		
		return $text;
		
	}


?>