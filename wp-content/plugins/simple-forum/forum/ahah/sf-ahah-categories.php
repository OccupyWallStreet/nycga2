<?php
/*
Simple:Press
Display Categories for Post Linking
$LastChangedDate: 2010-05-13 19:49:45 -0700 (Thu, 13 May 2010) $
$Rev: 4017 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
# --------------------------------------------

global $current_user;

# get out of here if no forum id specified
$fid = sf_esc_int($_GET['forum']);
if (empty($fid)) die();

sf_initialise_globals($fid);

if ($current_user->sflinkuse)
{
	global $catlist;

	$catlist ='<br /><br /><fieldset><legend>'.__("Select Categories for Post", "sforum").'</legend>'.sf_write_nested_categories(sf_get_nested_categories(), 1).'</fieldset><br />';
	echo $catlist;
} else {
	echo (__('Access Denied', "sforum"));
}

die();

function sf_write_nested_categories($categories, $level)
{
	global $catlist;

	foreach ( $categories as $category )
	{
		for($x=0; $x<$level; $x++)
		{
			$catlist.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		$catlist.= '<label class="sfcatlist" for="in-category-'.$category["cat_ID"].'"><input value="'.esc_attr($category['cat_ID']).'" type="checkbox" name="post_category[]" id="in-category-'.$category['cat_ID'].'"/>&nbsp;'.esc_html($category['cat_name']).'</label><br />';

		if ( $category['children'] )
		{
			$level++;
			sf_write_nested_categories( $category['children'], $level );
			$level--;
		}
	}
	return $catlist;
}

function sf_get_nested_categories( $default = 0, $parent = 0 ) {

	$cats = sf_return_categories_list( $parent);
	$result = array ();

	if ( is_array( $cats ) ) {
		foreach ( $cats as $cat) {
			$result[$cat]['children'] = sf_get_nested_categories( $default, $cat);
			$result[$cat]['cat_ID'] = $cat;
			$result[$cat]['cat_name'] = get_the_category_by_ID( $cat);
		}
	}
	return $result;
}

function sf_return_categories_list( $parent = 0 ) {

	$args=array();
	$args['parent']=$parent;
	$args['hide_empty']=false;
	$cats = get_categories($args);

	if($cats)
	{
		$catids=array();
		foreach($cats as $cat)
		{
			$catids[] = $cat->term_id;
		}
		return $catids;
	}
	return;
}

?>