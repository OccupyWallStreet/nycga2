<?php
/**
 * 
 * Many WordPress customization tutorials suggest editing a theme’s functions.php file. 
 * Use the top part of this file to do so. This way you can find things easily again :)
 * 
**/


##################################################################################################################################
// 	                                          common sense security precautions
##################################################################################################################################
//hide login errors
add_filter('login_errors',create_function('$a', "return null;"));

//hide wordpress version
add_filter( 'the_generator', create_function('$a', "return null;") );

##################################################################################################################################
// 	                                              Loading JS the right way
##################################################################################################################################
//load jquery from google
if( !is_admin()){
	wp_deregister_script('jquery'); 
	wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"), false, '1.3.2'); 
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'jquery.tools.min',get_template_directory_uri().'/js/jquery.tools.min.js', array('jquery'), '1', true );
	wp_enqueue_script( 'myjquery', get_template_directory_uri().'/js/myjquery.js', array('jquery'), '1', true );
}

##################################################################################################################################
//                                                 	Translating the theme
##################################################################################################################################
// Add translations
function theme_init(){
	load_theme_textdomain('smashingMultiMedia', get_template_directory());
}
add_action('init', 'theme_init');

##################################################################################################################################
// 	Some good functions from Theme Shaper (http://themeshaper.com/wordpress-themes-templates-tutorial/)
##################################################################################################################################
// Get the page number
function get_page_number() {
    if (get_query_var('paged')) {
        print ' | ' . __( 'Page ' , 'smashingMultiMedia') . get_query_var('paged');
    }
} 

// For category lists on category archives: Returns other categories except the current one (redundant)
function cats_meow($glue) {
        $current_cat = single_cat_title( '', false );
        $separator = "\n";
        $cats = explode( $separator, get_the_category_list($separator) );
        foreach ( $cats as $i => $str ) {
                if ( strstr( $str, ">$current_cat<" ) ) {
                        unset($cats[$i]);
                        break;
                }
        }
        if ( empty($cats) )
                return false;

        return trim(join( $glue, $cats ));
}

// For tag lists on tag archives: Returns other tags except the current one (redundant)
function tag_ur_it($glue) {
	$current_tag = single_tag_title( '', '',  false );
	$separator = "\n";
	$tags = explode( $separator, get_the_tag_list( "", "$separator", "" ) );
	foreach ( $tags as $i => $str ) {
		if ( strstr( $str, ">$current_tag<" ) ) {
			unset($tags[$i]);
			break;
		}
	}
	if ( empty($tags) )
		return false;
 
	return trim(join( $glue, $tags ));
}

##################################################################################################################################
// 	                                             The Comments Template
##################################################################################################################################
//comments
function mytheme_comment($comment, $args, $depth) {

$noAvatarPath = get_option('siteurl').'/wp-content/themes/'.get_option('wps_child_theme').'/images/noAvatar.jpg';

   $GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" class="clearfix">
			<div class="who_when">
				<div class="comment-author vcard">
					<?php echo get_avatar($comment,$size='80',$default = $noAvatarPath ); ?>
					<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
				</div>
				<div class="comment-meta commentmetadata">
					<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
						<?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?>
					</a>
				</div>
			</div>
			
			<div class="what">
				<?php if ($comment->comment_approved == '0') : ?>
					<em><?php _e('Your comment is awaiting moderation.', 'smashingMultiMedia') ?></em>
					<br />
				<?php endif; ?>	
				<?php comment_text() ?>
				<div class="reply">
					<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</div>
			</div>
		</div>
<?php
}

function list_pings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
?>
        <li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php }

add_filter('get_comments_number', 'comment_count', 0);
function comment_count( $count ) {
        if ( ! is_admin() ) {
                global $id;
                $get_comments= get_comments('post_id=' . $id);
				$comments_by_type = &separate_comments($get_comments);
                return count($comments_by_type['comment']);
        } else {
                return $count;
        }
}

//stop comment spam!
function check_referrer() {
    if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == “”) {
        wp_die( __('Please enable referrers in your browser, or, if you\'re a spammer, bugger off!') );
    }
}

add_action('check_comment_flood', 'check_referrer');


##################################################################################################################################
// 												Register Widget Areas
##################################################################################################################################

// Register widgetized areas - 8
function theme_widgets_init() {
// single
	register_sidebar( array (
		'name' 			=> 'Single Widget Area',
		'id' 			=> 'single_widget_area',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetPadding clearfix">',
		'after_widget' 	=> '</div></div>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	));
	
// category
	register_sidebar( array (
		'name' 			=> 'Category Widget Area',
		'id' 			=> 'category_widget_area',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetPadding clearfix">',
		'after_widget' 	=> '</div></div>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	));
	
// archive
	register_sidebar( array (
		'name' 			=> 'Archive Widget Area',
		'id' 			=> 'archive_widget_area',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetPadding clearfix">',
		'after_widget' 	=> '</div></div>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	));
	
// search
	register_sidebar( array (
		'name' 			=> 'Search Widget Area',
		'id' 			=> 'search_widget_area',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetPadding clearfix">',
		'after_widget' 	=> '</div></div>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	));

// page 404
	register_sidebar( array (
		'name' 			=> 'Page 404 Widget Area',
		'id' 			=> 'page404_widget_area',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetPadding clearfix">',
		'after_widget' 	=> '</div></div>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	));
	
// blog
	register_sidebar( array (
		'name' 			=> 'Front Page Widget Area',
		'id' 			=> 'blog_widget_area',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetPadding clearfix">',
		'after_widget' 	=> '</div></div>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	));
	
// index page seperator
	register_sidebar( array (
		'name' 			=> 'Front Page Seperator Widget Area',
		'id' 			=> 'frontpage_seperator_widget_area',
		'before_widget' => '<div id="%1$s" class="seperator">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h3>',
		'after_title' 	=> '</h3>',
	));
	
// page
	register_sidebar( array (
		'name' 			=> 'Page Widget Area',
		'id' 			=> 'page_widget_area',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetPadding clearfix">',
		'after_widget' 	=> '</div></div>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	));
} // end theme_widgets_init

add_action( 'init', 'theme_widgets_init' );




// Check for static widgets in widget-ready areas
function is_sidebar_active( $index ){
  global $wp_registered_sidebars;

  $widgetcolums = wp_get_sidebars_widgets();
                 
  if ($widgetcolums[$index]) return true;
  
        return false;
} // end is_sidebar_active

##################################################################################################################################
// 												VARIOUS FUNCTIONS :-) 
##################################################################################################################################
//pull the latest comment from twitter
function wp_echoTwitter($username){
     include_once(ABSPATH.WPINC.'/rss.php');
     $tweet = fetch_rss("http://search.twitter.com/search.atom?q=from:" . $username . "&rpp=1");
     echo $tweet->items[0]['atom_content'];
}
//"give it a tweet" call for the single post
if(!function_exists('dd_tiny_tweet_init')){
 
	function dd_tiny_tweet_init($content){
	
			//Thanks to http://briancray.com for the one line $tiny_tweet_url solution. 
			$tiny_tweet_url = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode('http://' . $_SERVER['HTTP_HOST']  . '/' . $_SERVER['REQUEST_URI']));
			//Grab the title of the current post
			$tiny_tweet_title = get_the_title();
			//Reduce title to 100 characters
			$tiny_tweet_title = substr($tiny_tweet_title, 0,100);
			//Append an ellipsis to the end
			$tiny_tweet_title .='...';
			//Set up the status and url to send to twitter
			$tiny_tweet_status_url = 'http://twitter.com/home?status=Currently reading "'.$tiny_tweet_title."\" ".$tiny_tweet_url;	
			
			if(is_single()){
				$content .=  '<div class=\'tiny_tweet\'><a href=\''.$tiny_tweet_status_url.'\'>Enjoyed this post? Then give it a tweet!</a></div>';
			}	
			
			elseif (is_front_page() == true){
				$content .=  '<a href=\''.$tiny_tweet_status_url.'\' target="_blank" rel="nofollow">Twitter</a>';
			}		
			
	return $content;
  }
  

	//add_filter('the_content', 'dd_tiny_tweet_init');

 
}

//custom fields
function get_custom_field($key, $echo = FALSE) {
	global $post;
	$custom_field = get_post_meta($post->ID, $key, true);
	if ($echo == FALSE) return $custom_field;
	echo $custom_field;
}

//check to see if any category has a single.php asigned to it and use that over the other
add_filter('single_template', create_function('$t', 'foreach( (array) get_the_category() as $cat ) { if ( file_exists(TEMPLATEPATH . "/single-{$cat->term_id}.php") ) return TEMPLATEPATH . "/single-{$cat->term_id}.php"; } return $t;' ));


//  give every  xyz  css class a different value 
function alternating_css_class($counter,$number,$css_class_string){

	if(($counter % $number) == 0){
		$the_div_class = $css_class_string;
	}
	else {
		$the_div_class = NULL;
	}
return $the_div_class; 
}

function insert_clearfix($counter,$number,$clearing_element){
	$counter++;
	
	if((($counter % $number) == 0)&&($number < $counter)){
		$clear_output = $clearing_element;
	}
	else {
		$clear_output = NULL;
	}
return $clear_output; 
}

// Get the id of a page by its slug
function get_page_id($page_name){
	global $wpdb;
	$page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_id;
}

// Get the slug of a page by its title
function get_page_slug($page_title){
	global $wpdb;
	$page_slug = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_title = '".$page_title."'");
	return $page_slug;
}

// Get the Title of a page by its slug
function get_page_title($page_slug){
	global $wpdb;
	$page_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_title;
}

// conditional if page belongs in the tree
function is_tree($pid) {    // $pid = The page we're looking for pages underneath
	global $post;       // We load this as we're outside of the post
	if(is_page()&&($post->post_parent==$pid||is_page($pid))) return true; // Yes, it's in the tree
	else return false;  // No, it's outside
};

// get the name of the parent category
function get_parent_cat_name($tag_open,$tag_close)
{
		foreach (get_the_category() as $cat) {
		  $parent 		= get_category($cat->category_parent);  
		  $parent_name 	= $parent->cat_name;
		  $p_name 		= $tag_open.$parent_name.$tag_close;
		}

return $p_name;
}

// get the cat_nice_name ( slug ) of the parent category
function get_parent_cat_nicename()
{
		foreach (get_the_category() as $cat) {
		  $parent 		= get_category($cat->category_parent);  
		  $p_nname		= $parent->category_nicename;
		}
return $p_nname;
}

// get the cat_ID of the parent category
function get_parent_cat_id()
{
		foreach (get_the_category() as $cat) {
		  $parent 		= get_category($cat->category_parent);  
		  $parent_ID	= $parent->cat_ID;
		}

return $parent_ID;
}

// Get the slug or the ID of the Root Category. This will check parents, grandparents, etc.. All the way up!
function get_root_category($cat,$option='slug'){

	$result = NULL;

	$parentCatList 		= get_category_parents($cat,false,',');	
    $parentCatListArray = split(",",$parentCatList);
    $topParentName 		= $parentCatListArray[0];
    $replace 			= array(" " => "-", "(" => "", ")" => "");
    $topParentSlug 		= strtolower(strtr($topParentName,$replace));
	
	if($option == 'name'){
		$result = $topParentName;
	}
	else{
		$result = $topParentSlug;
	}
	
return $result;	
}

// find the top category parent when on a single post and return it's ID when found
function get_post_top_parent(){
		
		$this_category  = get_category(get_parent_cat_id());
		
		#var_dump($this_category);
		
		
		$parent_cat 	= $this_category->category_parent;	
		//when first level category  return it's ID
		if($parent_cat == NULL){
			$catsy 		= get_the_category();
			$parent_cat = $catsy[0]->cat_ID;
		}
return $parent_cat;
}

// get the featured category id
if (!function_exists('any_cat')) {
	function any_cat($cat = 'some-category'){
	
	global $wpdb;
	
	$query 				= " SELECT 
						    * 
						   FROM 
						    $wpdb->terms 
						   WHERE 
						    slug = '$cat' 
						   LIMIT 0 , 1";

	$result    			= mysql_query($query);
	$row    			= mysql_fetch_assoc($result);
	$id_any_cat  		= $row[term_id];

	return $id_any_cat;
	}
}

//Access wordpress post data outside the loop. It will return an array, containing post title, date, content, author id, post id, etc.
function get_post_data($postId) {
global $wpdb;
return $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$postId");
}

function get_only_main_cats($option='startpage'){

	global $wpdb;
	$qStr 	= "SELECT * FROM 
					$wpdb->terms AS wterms 
				INNER JOIN 
					$wpdb->term_taxonomy AS wtaxonomy 
				ON(wterms.term_id = wtaxonomy.term_id) 
				WHERE 
					wtaxonomy.taxonomy = 'category' 
				AND 
					wtaxonomy.parent = 0 
				AND 
					wterms.term_id NOT IN (1) 
				ORDER BY wterms.name ASC";																	
	$res 	= mysql_query($qStr);
return $res;
}

##################################################################################################################################
// 											IMAGES
##################################################################################################################################
function get_upload_img_url($img){

	$parts 		= explode("http://",$img);													
	$findme 	= array('.gif','.jpg','.jpeg','.png');
	$picSuffix	= NULL;
													
		foreach($findme as $v){
			$pos 	= strpos($parts[1], $v);
				if ($pos !== false) {
					$picSuffix = $v; 
			} 
		}
																				
		$parts2 = explode("$picSuffix",$parts[1]);
		$imgURL = 'http://'.$parts2[0].$picSuffix;

return $imgURL;
}

function get_upload_img_type($imgURL){

	$picinfo = getimagesize($imgURL);
				
	// what case is it 										
	if($picinfo[0] > $picinfo[1]){
		$picType = 'landscape';
	}
	elseif($picinfo[0] < $picinfo[1]){
		$picType = 'portrait';
	}
	elseif($picinfo[0] == $picinfo[1]){
		$picType = 'square';
	}
	else{}	


return $picType;
}

function mkthumb($img_src,$des_src,$img_dimension=120,$option='height')    	
{
	//find name of img_file
	$img_file 	= substr(strrchr($img_src,"/"),1);
	$thumb_path	= $des_src . '/' . $img_file;
	
	
		clearstatcache(); // file_exists chaches results, needs to be cleared first
	
		if(!file_exists($thumb_path))  // thumbnail creation only if not existing
		{
		
			// find sizes + type
			#list($src_width,$src_height,$src_typ) = getimagesize($img_src);
			$absolute_img_src = WP_CONTENT_DIR . substr(strstr($img_src,'wp-content'),10);
			list($src_width,$src_height,$src_typ) = getimagesize($absolute_img_src);
			   
			
			// give them new (thumb) sizes
			switch($option){
			
				case 'height':
				$ratio 				= $src_height / $img_dimension;
				$new_image_width 	= round(($src_width / $ratio),0); 
				$new_image_height	= $img_dimension;
				break;
				
				case 'width':
				$ratio 				= $src_width / $img_dimension;
				$new_image_height 	= round(($src_height / $ratio),0); 
				$new_image_width	= $img_dimension;
				break;
			}
			

			if($src_typ == 1)     // GIF
			{
			  #$image 		= imagecreatefromgif($img_src);
			  $image 		= imagecreatefromgif($absolute_img_src);
			  $new_image 	= imagecreate($new_image_width, $new_image_height);
			  imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_image_width,$new_image_height, $src_width, $src_height);
			  imagegif($new_image, $des_src."/".$img_file, 100);
			  imagedestroy($image);
			  imagedestroy($new_image);
			}
			elseif($src_typ == 2) // JPG
			{
			  #$image 		= imagecreatefromjpeg($img_src);
			  $image 		= imagecreatefromjpeg($absolute_img_src);
			  $new_image 	= imagecreatetruecolor($new_image_width, $new_image_height);
			  imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_image_width,$new_image_height, $src_width, $src_height);
			  imagejpeg($new_image, $des_src."/".$img_file, 100);
			  imagedestroy($image);
			  imagedestroy($new_image);
			}
			elseif($src_typ == 3) // PNG
			{
			  #$image 		= imagecreatefrompng($img_src);
			  $image 		= imagecreatefrompng($absolute_img_src);
			  $new_image 	= imagecreatetruecolor($new_image_width, $new_image_height);
			  imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_image_width,$new_image_height, $src_width, $src_height);
			  imagepng($new_image, $des_src."/".$img_file);
			  imagedestroy($image);
			  imagedestroy($new_image);
			}
			else
			{
			  $img_file = 'error';
			}
		}
		
return $img_file;
}

function filter_img_from_descr($searchPattern,$postContent){

	// we remove the images from the content
	$szDescription = preg_replace($searchPattern, '' ,$postContent);

	// Apply filters for correct content display
	$szDescription = apply_filters('the_content', $szDescription);

	// Echo the Content
	#echo $szDescription;
return $szDescription;
}


function my_attachment_image($postid=0, $size='thumbnail', $attributes='',$option='echo'){

	if ($postid<1){ $postid = get_the_ID(); }
	
	if ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'order' => 'ASC', 
		'orderby' => 'menu_order ID',
		'numberposts' => 1,
		'post_mime_type' => 'image',))){
		
		foreach($images as $image) {
		
			$output 			= array();
		
			$attachment 		= wp_get_attachment_image_src($image->ID, $size);
			$parts 				= explode("/wp-content/uploads/",$attachment[0]);				
			$relPath 			= 'wp-content/uploads/'.$parts[1];
			$output[css_class] 	= get_upload_img_type($relPath);

			
			if($option == 'echo'){
				echo "<img class='img_{$output[css_class]}' src='$attachment[0]'  $attributes />";
			}
			elseif($option == 'return'){
				
				$output[img_path] 	= $attachment[0];
				$output[attr] 		= $attributes;
			
			}
			else {}
		}
	}
return $output;
}

function title_to_picname($title)
{	
	$rawPicname = NULL;
	$words 		= str_word_count($title,1);
	
	foreach($words as $v)
	{
		$rawPicname .= $v .'-';
	}
    $picname	= strtolower(substr($rawPicname,0,-1));
	
return $picname;	
}

function get_childtheme(){

	global $wpdb;
	
	$table = $wpdb->prefix . 'options';
	
	$qStr 		= "SELECT option_value FROM $table WHERE option_name = 'stylesheet' LIMIT 0,1";
	$res 		= mysql_query($qStr);	
	$row 		= mysql_fetch_assoc($res);
	$childTh	= $row[option_value];

return $childTh;
}

function get_all_themes(){

	if(is_admin()){
			$result = array();
			
			$path 	= '../wp-content/themes';

			
			if($handle = opendir($path)){
				while (false !== ($file = readdir($handle))){
				
					if($file != '.' && $file != '..' && $file != 'classic' && $file != 'default' && $file != 'index.php' && $file != 'smashingMultiMedia'){
						$result[] = "$file";
					}	
				}
				closedir($handle);
			}
		return $result;
	}
}

##################################################################################################################################
// 												THEME - ADMIN - OPTIONS
##################################################################################################################################
$themename = "smashingMultiMedia";
$shortname = "wps";
$options   = array (
//tab 1 begin
array (				"type" 	=> "section_start",
					"class" => "pane",
					"id" 	=> "tabs-1"),
/***
General Options
***/
array ( 			"name" 	=> "General Set up Options",
					"type" 	=> "title"),
					"id" 	=> "generalSetUpOptions",

array(    			"type" 	=> "open"),

array(    			"name" 	=> "Child Theme",
					"desc" 	=> "Choose the name of the child theme you have activated.",
					"id" 	=> $shortname."_child_theme",
					"std" 	=> get_childtheme(),
					"vals" 	=> get_all_themes(),
					"type" 	=> "select"),
					
array(    			"type" 	=> "close"),
array(				"type" => "section_end"),
//tab 2 begin
array(				"type" 	=> "section_start",
					"class" => "pane",
					"id" 	=> "tabs-2"),

/***
Main Navi Options
***/
array( 				"name" 	=> "Your Main Site Page Navigation Options",
					"type" 	=> "title"),
					"id" 	=> "pageNavigationOptions",
					
array(    			"type" 	=> "open"),	

array(  			"name" 	=> "Page Navigation - Sorting",
			        "desc" 	=> "If you have chosen a PAGE Site Navigation choose how they should be sorted. All available options are in the drop down",
			        "id" 	=> $shortname."_pgNavi_sortOption",
			        "type" 	=> "select2",
			        "std" 	=> "Sort Pages by Page Order",
					"vals" 	=> array("Sort Pages by Page Order|menu_order","Sort by numeric Page ID|id","Sort Pages alphabetically by title|post_title","Sort by creation time|post_date","Sort by time last modified|post_modified","Sort by the Page author's numeric ID|post_author","Sort alphabetically by Post slug|post_name")),

array(    			"name" 	=> "Page Navigation - Show 'Home'",
					"desc" 	=> "If you like to have 'Home' as the first item in the list of your page navigation then enter 1 or type the link text you'd like to have if it's other than 'Home'. Enter 0 if you want none. The URL assigned to 'Home' is pulled from the Blog address (URL)",
					"id" 	=> $shortname."_pgNavi_homeOption",
					"std" 	=> "",
					"type" 	=> "text"),
					
array(    			"name" 	=> "Page Navigation - Include Only",
					"desc" 	=> "The page navigation will by default include all your 1st level pages so if you only want to include certain pages then enter the page ID's you'd like to have included. Leave empty if you want the Default Option.",
					"id" 	=> $shortname."_pgNavi_inclOption",
					"std" 	=> "",
					"type" 	=> "text"),
					
array(    			"name" 	=> "Page Navigation - Exclude Only",
					"desc" 	=> "The page navigation will by default exclude none of your 1st level pages so if you want to exclude certain pages then enter the page ID's you'd like to have excluded. Leave empty if you want the Default Option.",
					"id" 	=> $shortname."_pgNavi_exclOption",
					"std" 	=> "",
					"type" 	=> "text"),
					
array(    			"type" 	=> "close"),
array(				"type" => "section_end"),
//tab 3 begin
array(				"type" 	=> "section_start",
					"class" => "pane",
					"id" 	=> "tabs-3"),

/***
Post Options
***/
// in featured area
array( 				"name" 	=> "Post Settings for the Featured Area",
					"type" 	=> "title"),
					"id" 	=> "featuredAreaOptions",
					
array(    			"type" 	=> "open"),	

array(  			"name" 	=> "Number of Sticky Posts",
			        "desc" 	=> "Enter how many Sticky Posts you'd like to have inside the Featured Area.",
			        "id" 	=> $shortname."_sticky_showposts",
			        "std" 	=> "4",
					"type" 	=> "text"),

array(    			"type" 	=> "close"),

// in Frontpage and Multible Post Pages
array( 				"name" 	=> "Post Settings on Frontpage and Multible Post Pages",
					"type" 	=> "title"),
					"id" 	=> "postSettingsOptions",
					
array(    			"type" 	=> "open"),	


array(  			"name" 	=> "Media Posts (non-featured) on Front page - Number of posts to show ",
			        "desc" 	=> "Enter how many Recent non-sticky posts should appear in the Main Content area below the Featured Area",
			        "id" 	=> $shortname."_nonSticky_showposts",
			        "std" 	=> "6",
					"type" 	=> "text"),
					
array(  			"name" 	=> "Media Post Layout for the Front Page",
			        "desc" 	=> "Choose your Media Post Splash Image type",
			        "id" 	=> $shortname."_mediaPostDisplay_frPgOption",
			        "type" 	=> "select2",
			        "std" 	=> "Option 1:Sliding Splash Image",
					"vals" 	=> array("Option 1: Sliding Splash Image|option1","Option 2: Static Splash Image|option2")),
					
array(  			"name" 	=> "Media Post Layout for the Category Pages",
			        "desc" 	=> "Choose your Media Post Splash Image type",
			        "id" 	=> $shortname."_mediaPostDisplay_catOption",
			        "type" 	=> "select2",
			        "std" 	=> "Option 1:Sliding Splash Image",
					"vals" 	=> array("Option 1: Sliding Splash Image|option1","Option 2: Static Splash Image|option2")),
					
array(    			"type" 	=> "close"),	

// post teaser options
array( 				"name" 	=> "Your Post Teaser Options",
					"type" 	=> "title"),
					"id" 	=> "postTeaserOptions",
					
array(    			"type" 	=> "open"),				
array(  			"name" 	=> "For Sticky Posts in Featured Area",
			        "desc" 	=> "Choose the type of teaser content you'd like to have for the featured sticky posts.",
			        "id" 	=> $shortname."_stickyContent_option",
			        "type" 	=> "select2",
			        "std" 	=> "Post Excerpt with 'read more button'",
					"vals" 	=> array("Post Excerpt with 'read more' button|excerpt_btn","Post Excerpt with 'read more' link |excerpt_link","Post Content with 'read more' link |content_link","Post Content with 'read more' button |content_btn")),

array(  			"name" 	=> "For media posts on the Front Page",
			        "desc" 	=> "Choose the type of teaser content you'd like to have for your media posts.",
			        "id" 	=> $shortname."_postContent_frPgOption",
			        "type" 	=> "select2",
			        "std" 	=> "Post Excerpt with 'read more button'",
					"vals" 	=> array("Post Excerpt with 'read more' button|excerpt_btn","Post Excerpt with 'read more' link |excerpt_link","Post Content with 'read more' link |content_link","Post Content with 'read more' button |content_btn")),
					
array(  			"name" 	=> "For media posts on Category Pages",
			        "desc" 	=> "Choose the type of teaser content you'd like to have for your media posts.",
			        "id" 	=> $shortname."_postContent_catOption",
			        "type" 	=> "select2",
			        "std" 	=> "Post Excerpt with 'read more button'",
					"vals" 	=> array("Post Excerpt with 'read more' button|excerpt_btn","Post Excerpt with 'read more' link |excerpt_link","Post Content with 'read more' link |content_link","Post Content with 'read more' button |content_btn")),
					
array(  			"name" 	=> "'Read More' Link Text",
			        "desc" 	=> "If you have chosen a 'read more' link to display from above enter the link text to display",
			        "id" 	=> $shortname."_readMoreLink",
			        "std" 	=> "read more",
					"type" 	=> "text"),

array(  			"name" 	=> "Sticky Featured Posts - Teaser Word Limit",
			        "desc" 	=> "If you have chosen 'Post content' above enter the number of words to appear before the 'cut' off point",
			        "id" 	=> $shortname."_stickyWordLimit",
			        "std" 	=> "25",
					"type" 	=> "text"),	

array(  			"name" 	=> "Multimedia Posts - Teaser Word Limit",
			        "desc" 	=> "If you have chosen 'Post content' above enter the number of words to appear before the 'cut' off point",
			        "id" 	=> $shortname."_multimediaWordLimit",
			        "std" 	=> "20",
					"type" 	=> "text"),

array(    			"type" 	=> "close"),
array(				"type" => "section_end"),
//tab 4 begin
array(				"type" 	=> "section_start",
					"class" => "pane",
					"id" 	=> "tabs-4"),

/***
Sidebar & Footer Options
***/
// sidebar widget options
array( 				"name" 	=> "Your Sidebar Widget Options",
					"type" 	=> "title"),
					"id" 	=> "sidebarOptions",
					
array(    			"type" 	=> "open"),		
		
array(  			"name" 	=> "Latest Tweet",
			        "desc" 	=> "Choose whether you'd like to use the 'Latest Tweet' widget or whether you prefer to replace it with a plugin of your choice",
			        "id" 	=> $shortname."_latestTweet_option",
			        "type" 	=> "select2",
			        "std" 	=> "Keep it. It'll do.",
					"vals" 	=> array("Keep it. It'll do.|lt_yes","Chuck it! I prefer a plugin. |lt_no")),
					
array(  			"name" 	=> "Number of Related Posts",
			        "desc" 	=> "Enter how many 'Related Posts' you'd like to have in the sidebar when viewing a single post",
			        "id" 	=> $shortname."_related_showposts",
					"std" 	=> "5",
					"type" 	=> "text"),
					
array(  			"name" 	=> "Your Categories - Order by",
			        "desc" 	=> "All available options are in the drop down",
			        "id" 	=> $shortname."_catNavi_orderbyOption",
			        "type" 	=> "select2",
			        "std" 	=> "Sort categories by name",
					"vals" 	=> array("Sort categories by name|name","Sort by ID|ID","Sort by slug|slug","Sort by count|count")),

array(    			"name" 	=> "Your Categories - Order",
					"desc" 	=> "All available options are in the drop down",
			        "id" 	=> $shortname."_catNavi_orderOption",
			        "type" 	=> "select2",
			        "std" 	=> "Ascending",
					"vals" 	=> array("Ascending|ASC","Descending|DESC")),
					
array(    			"name" 	=> "Your Categories - Include Only",
					"desc" 	=> "Your Cateogories list will by default include all your categories so if you want to include only certain certain then enter the category ID's you'd like to have included. Leave empty if you want the Default Option.",
					"id" 	=> $shortname."_catNavi_inclOption",
					"std" 	=> "",
					"type" 	=> "text"),
					
array(    			"name" 	=> "Your Categories - Exclude Only",
					"desc" 	=> "Your Categories list will by default exclude none of your categories so if you want to exclude certain categories then enter the page ID's you'd like to have excluded. Leave empty if you want the Default Option.",
					"id" 	=> $shortname."_catNavi_exclOption",
					"std" 	=> "",
					"type" 	=> "text"),
					
array(    			"type" 	=> "close"),

array(				"type" => "section_end"),
//tab 5 begin
array(				"type" 	=> "section_start",
					"class" => "pane",
					"id" 	=> "tabs-5"),


/***
'Social' and 'Subscribe' Options
***/
array( 				"name" 	=> "Your 'Share' and 'Subscribe' Options",
					"type" 	=> "title"),
					"id" 	=> "socialOptions",
					
array(    			"type"	=> "open"),
					
array(				"name" 	=> "Feedburner RSS Link",
					"desc" 	=> "Enter Your Feedburner Link It will look something like this: 'http://feeds2.feedburner.com/snDesign'",
					"id" 	=> $shortname."_feedburner_rsslink",
					"std" 	=> "http://feeds2.feedburner.com/snDesign",
					"type" 	=> "text"),
					
array(				"name" 	=> "Feedburner Email Subscription Link",
					"desc" 	=> "Enter Your Feedburner Email Subscription link you are given after you have activated 'Email Subscriptions' from your account. It will look something like this: 'http://feedburner.google.com/fb/a/mailverify?uri=snDesign&amp;loc=en_US'",
					"id" 	=> $shortname."_feedburner_emaillink",
					"std" 	=> "http://feedburner.google.com/fb/a/mailverify?uri=snDesign&loc=en_US",
					"type" 	=> "text"),
					
array(				"name" 	=> "Twitter",
					"desc" 	=> "Enter Your Twitter name",
					"id" 	=> $shortname."_twitter",
					"std" 	=> "srhnbr",
					"type" 	=> "text"),
					
array(				"name" 	=> "Subscribe Text",
					"desc" 	=> "Enter the text you'd like to have appear in place of 'Choose the way you would like to be notified for latest posts.' (inside the overlay that opens when on the post's single page)",
					"id" 	=> $shortname."_subscribe_text",
					"std" 	=> "Choose the way you would like to be notified for latest posts.",
					"type"	=> "text"),
					
array(				"name" 	=> "Share Text",
					"desc" 	=> "Enter the text you'd like to have appear in place of 'Share this with your friends.' (inside the overlay that opens when on the post's single page)",
					"id" 	=> $shortname."_share_text",
					"std" 	=> "Share this with your friends.",
					"type" 	=> "text"),
					
array(   			 "type" => "close"),
array(				"type" => "section_end"),
);


function mytheme_add_admin() {

    global $themename, $shortname, $options;

    if ( $_GET['page'] == basename(__FILE__) ) {

        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=functions.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=functions.php&reset=true");
            die;

        }
    }

    add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');
}




function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

?>
<div class="wrap">
<h2>
<?php echo $themename; ?> settings
</h2>

<form method="post">
	<div id="tabs">
		<ul class="tabNav mainTabNav">
			<li><a href="#tabs-1"><?php _e('General Options','smashingMultiMedia') ?></a></li>
			<li><a href="#tabs-2"><?php _e('Main Navi Options','smashingMultiMedia') ?></a></li>
			<li><a href="#tabs-3"><?php _e('Post Options','smashingMultiMedia') ?></a></li>
			<li><a href="#tabs-4"><?php _e('Sidebar Options','smashingMultiMedia') ?></a></li>
			<li><a href="#tabs-5"><?php _e('Subscribe and Social Options','smashingMultiMedia') ?></a></li>
		</ul>
		<?php 	foreach ($options as $value) {

			switch ( $value['type'] ) {

			case "open":
			?>
				<table width="100%" border="0" style="background-color:#f9f9f9; padding:10px;">

			<?php 
			break;

			case "close":
			?>

				</table><br />

			<?php 
			break;

			case "title":
			?>
				<table id="<?php echo $value['id']; ?>" width="100%" border="0" style="background-color:#ececec; padding:5px 10px;"><tr>
					<td colspan="2"><h3 style="font-family:Georgia,'Times New Roman',Times,serif;"><?php echo $value['name']; ?></h3></td>
				</tr>

			<?php break;

			case 'text':
			?>

				<tr>
					<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%"><input style="width:400px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" /></td>
				</tr>

				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
				</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

			<?php
			break;

			case 'text_invisible':
			?>
				<tr>
					<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%">&nbsp;</td>
				</tr>

				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
				</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
			<?php
			break;

			case 'textarea':
			?>

				<tr>
					<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%"><textarea name="<?php echo $value['id']; ?>" style="width:400px; height:200px;" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if(get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])); } else { echo $value['std']; } ?></textarea></td>

				</tr>

				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
				</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

			<?php
			break;

			case 'select':
			?>
				<tr>
					<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%"><select style="width:240px;" 
					name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
					<?php 
					
							$o 		= get_settings($value['id']);
							$len	= strlen($o);
					
						foreach ($value['vals'] as $option) {
						
						
							?><option<?php 	
							if($len > 0){
								if(get_settings( $value['id']) == $option) { 
									echo ' selected="selected"'; 
								} 			
							}
							else {
								if($option == $value['std']){
									echo ' selected="selected"'; 
								}
							}
							?>><?php echo $option ?></option><?php 
						} 			
						?></select></td>
				</tr>
				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
				</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

			<?php
			break;

			case 'select2':
			?>
				<tr>
					<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%"><select style="width:240px;" 
					name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
					<?php
					
						$o 		= get_settings($value['id']);
						$len	= strlen($o);
					
						foreach ($value['vals'] as $option) {
									
							$parts = explode("|",$option);
							
							if($len > 0){	// a value was already chosen 
							
								if(get_settings($value['id']) == $parts[1]){
									$selected = 'selected="selected"';
								}
								else{
									$selected = NULL;
								}
							}
							else {  // a value was not previously chosen, we fall back on std
								if($parts[1] == $value['std']){
									$selected = 'selected="selected"';
								}
								else{
									$selected = NULL;
								}			
							}
											
							$op = "<option value='$parts[1]' $selected >$parts[0]</option>";
							echo $op;	
						} 			
						?>
						</select></td>
				</tr>
				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
				</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

			<?php
			break;


			case 'pathinfo':
			?>

				<tr>
					<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%"><input readonly="readonly" style="width:400px;" name="<?php echo $value['id']; ?>" 
					id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" 
					value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" />
					</td>
				</tr>

				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
				</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

			<?php
			break;


			case "iframe":
			?>
				<tr>
				<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%">
						<iframe src="<?php echo $value['vals'] ?>" width="90%" height="400" name="<?php echo $value['id'] ?>"></iframe>
						</td>       
				</tr>
				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
			   </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
			<?php
			break;


			case "section_start":
			?>
				<div class="<?php echo $value['class'] ?>" id="<?php echo $value['id'] ?>">
			<?php
			break;


			case "section_end":
			?>
				</div>
			<?php
			break;

			case "tabsubnav_start":
			?>
				<ul class="tabs trinaryTabs <?php echo $value['class'] ?>" id="<?php echo $value['id'] ?>">

					<?php foreach ($value['vals'] as $option){ 
							$parts = explode("|",$option);?>
						<li><a href="#<?php echo $parts[1];?>"><?php echo $parts[0];?></a></li><?php 
					} 
			break;


			case "tabsubnav_end":
			?>
				</ul>
			<?php
			break;


			case "checkbox":
			?>
				<tr>
				<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%"><? if(get_settings($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = ""; } ?>
							<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
							</td>
				</tr>

				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
			   </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

			<?php         
			break;


			case "multi-checkbox":
			?>
				<tr>
				<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
					<td width="80%">

				<?php			
						$table  = $wpdb->prefix . 'options';
						$qStr 	= "SELECT option_value FROM $table WHERE option_name = '$value[id]' LIMIT 0,1";
						$res 	= mysql_query($qStr);
						$row 	= mysql_fetch_assoc($res);			
						$payP	= explode("|",$row['option_value']);
				
						foreach($value['vals'] as $k => $v){	

							$data 		= explode("|",$v);
						
							if(in_array($data[1],$payP)){	
								$checked = "checked=\"checked\""; 
							}else{ 
								$checked = ""; 
							} 
						?>		
					 <input type="checkbox" name="<?php echo $value['id'];?>|<?php echo $data[1];?>" id="<?php echo $value['id']; ?>|<?php echo $data[1];?>" 
					 value="true" <?php echo $checked; ?> />
					<?php echo "$data[0] <br/>";  
				} ?>
					</td>
				</tr>
				<tr>
					<td><small><?php echo $value['desc']; ?></small></td>
			   </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #DFDFDF;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

				<?php         
			break;
		}
		}
		?>



	
		<div id="themeOptionsSave">
			<p class="submit">
				<input name="save" type="submit" value="Save changes" />
				<input type="hidden" name="action" value="save" />
			</p>
		</div>
	</div>
</form>

<form id="themeOptionsReset" method="post">
	<p class="submit">
	<input name="reset" type="submit" value="Reset" />
	<input type="hidden" name="action" value="reset" />
	</p>
	<small><?php _e('The save button will save all theme options so no need to save each section separately.','smashingMultiMedia') ?></small><br/>
	<small><?php _e('Pressing the Reset button will reset ALL your Settings so please think twice before you do so!','smashingMultiMedia') ?></small>
</form>

<?php
}

// Add admin css
function mytheme_admin_styles(){
    echo "<link rel='stylesheet' media='all' type='text/css' href='".get_bloginfo('template_url')."/css/admin.css' />";
}

// Add admin scripts
function mytheme_admin_scripts(){
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script( 'myadmin.js', get_template_directory_uri().'/js/myadmin.js', array('jquery'), '1' );
}



if(is_admin()){
	add_action('admin_head', 'mytheme_admin_styles');
	add_action('init', 'mytheme_admin_scripts');
	add_action('admin_menu', 'mytheme_add_admin');
} 

##################################################################################################################################
// 												   CUSTOM - FIELDS
##################################################################################################################################
$post_custom_fields =
array(
	"videoHeight" 		=> array(
		"name" 			=> "videoHeight",
		"std" 			=> "",
		"title" 		=> "Video Height for Sticky Posts inside the Featured Area",
		"description" 	=> "If the post is made Sticky the Video width inside the featured area is set to 442. So as the Video not to appear stretched adjust the height accordingly. For a 4:3 ratio enter 332, for 16:9 enter 249, for 21:9 enter 189 etc. "
	),
	"stickyMediaSplashImg" 	=> array(
		"name" 				=> "stickyMediaSplashImg",
		"std" 				=> "",
		"title" 			=> "Media Splash Image for the sticky media post in the Featured Area (front page) - Optional for video posts using the Multimedia Shortcode",
		"description" 		=> "Upload your image using the media uploader and paste the full path to it like so: <b>http://your-site.com/wp-content/uploads/2009/07/mediaSplashImage.png</b>.It will be automatically resized for you (according to it's ratio) down to 442px in width. Leave empty if your post uses the SmashingMultiMedia video embed shortcode and you want your video to appear in the Featured Area instead."
	),
	"stickyMediaSplashThumbImg" => array(
		"name" 					=> "stickyMediaSplashThumbImg",
		"std" 					=> "",
		"title" 				=> "Optional Media Splash Image for the thumbnail in the Featured Area (front page)",
		"description" 			=> "Upload your image using the media uploader and paste the full path to it like so: <b>http://your-site.com/wp-content/uploads/2009/07/mediaSplashImage.png</b>. It will be automatically resized for you (according to it's ratio) down to 80px in width."
	),
	"3colmediaSplashImg" 	=> array(
		"name" 				=> "3colmediaSplashImg",
		"std" 				=> "",
		"title" 			=> "Media Splash Image for 3column-multible post pages (when selected from Theme Options)",
		"description" 		=> "Upload your image using the media uploader and paste the full path to it like so: <b>http://your-site.com/wp-content/uploads/2009/07/mediaSplashImage.png</b>. It will be automatically resized for you (according to it's ratio) down to 183px in width."
	),
	"3colmediaSplashImgAlt" 	=> array(
		"name" 				=> "3colmediaSplashImgAlt",
		"std" 				=> "",
		"title" 			=> "Alternative Media Splash Image for 3column-multible post pages (when selected from Theme Options)",
		"description" 		=> "This was initially added in here in order to demonstrate the alternative media post layout on category pages. If you have a use for it good, otherwise leave empty. <small>Upload your image using the media uploader and paste the full path to it like so: <b>http://your-site.com/wp-content/uploads/2009/07/mediaSplashImage.png</b>. It will be automatically resized for you (according to it's ratio) down to 183px in width.</small>"
	)
);


function post_custom_fields() {
	global $post, $post_custom_fields;

	foreach($post_custom_fields as $meta_box) {
		$meta_box_value = stripslashes(get_post_meta($post->ID, $meta_box['name'].'_value', true));
		
		if($meta_box_value == "")
			$meta_box_value = $meta_box['std'];
			echo '<p style="margin-bottom:10px;">';
			echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
			echo'<strong>'.$meta_box['title'].'</strong>';
			echo'<input type="text" name="'.$meta_box['name'].'_value" value="'.attribute_escape($meta_box_value).'" style="width:98%;" /><br />';
			echo'<label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label>';
			echo '</p>';
			
	}
}

function create_meta_box() {
	global $theme_name;
		if ( function_exists('add_meta_box') ) {
			add_meta_box( 'new-meta-boxes', 'Media', 'post_custom_fields', 'post', 'normal', 'high' );
	}
}

function save_postdata( $post_id ) {
	global $post, $post_custom_fields;

	foreach($post_custom_fields as $meta_box) {
		// Verify
		if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
	}

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ))
			return $post_id;
		} else {
		if ( !current_user_can( 'edit_post', $post_id ))
			return $post_id;
	}

	$data = $_POST[$meta_box['name'].'_value'];

	if(get_post_meta($post_id, $meta_box['name'].'_value') == "")
		add_post_meta($post_id, $meta_box['name'].'_value', $data, true);
	elseif($data != get_post_meta($post_id, $meta_box['name'].'_value', true))
		update_post_meta($post_id, $meta_box['name'].'_value', $data);
	elseif($data == "")
		delete_post_meta($post_id, $meta_box['name'].'_value', get_post_meta($post_id, $meta_box['name'].'_value', true));
	}
}

add_action('admin_menu', 'create_meta_box');
add_action('save_post', 'save_postdata');

##################################################################################################################################
// 												SHORTCODES
##################################################################################################################################

/*
Vimeo, YouTube, Google Video, Blip TV, Veoh, Viddler, Revver
*/

#### Vimeo eg http://vimeo.com/5363880 id="5363880"
function vimeo_code($atts,$content = null){

	extract(shortcode_atts(array(  
		"id" 		=> '',
		"width"		=> 480, 
		"height" 	=> 360
	), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://vimeo.com/moogaloop.swf?clip_id=$id&amp;server=vimeo.com'
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://vimeo.com/moogaloop.swf?clip_id=$id&amp;server=vimeo.com' />
		</object>";
	return $data;
} 
add_shortcode("vimeo", "vimeo_code"); 

#### YouTube eg http://www.youtube.com/v/MWYi4_COZMU&hl=en&fs=1& id="MWYi4_COZMU&hl=en&fs=1&"
function youTube_code($atts,$content = null){

	extract(shortcode_atts(array(  
			 "id" 		=> '',
			 "width"	=> 480, 
			 "height" 	=> 360
		 ), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://www.youtube.com/v/$id' 
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='FlashVars' value='playerMode=embedded' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://www.youtube.com/v/$id' />
		</object>";
	return $data;
} 
add_shortcode("youtube", "youTube_code");

#### Google Video eg http://video.google.com/googleplayer.swf?docid=7664206256212725581&hl=en&fs=true id="7664206256212725581&hl=en&fs=true"
function googleVideo_code($atts,$content = null){

	extract(shortcode_atts(array(  
			 "id" 		=> '',
			 "width"	=> 480, 
			 "height" 	=> 360
		 ), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://video.google.com/googleplayer.swf?docid=$id' 
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://video.google.com/googleplayer.swf?docid=$id' />
		</object>";
	return $data;
} 
add_shortcode("googlevideo", "googleVideo_code");

#### Meta Cafe eg http://www.metacafe.com/fplayer/3025424/blue_iceberg.swf id="3025424/blue_iceberg.swf"
function metaCafe_code($atts,$content = null){

	extract(shortcode_atts(array(  
			 "id" 		=> '',
			 "width"	=> 480, 
			 "height" 	=> 360
		 ), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://www.metacafe.com/fplayer/$id' 
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://www.metacafe.com/fplayer/$id' />
		</object>";
	return $data;
} 
add_shortcode("metacafe", "metaCafe_code");

#### Blip TV eg http://blip.tv/play/AYGPryCBum0 id="AYGPryCBum0"
function blipTv_code($atts,$content = null){

	extract(shortcode_atts(array(  
			 "id" 		=> '',
			 "width"	=> 480, 
			 "height" 	=> 360
		 ), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://blip.tv/play/$id' 
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://blip.tv/play/$id' />
		</object>";
	return $data;
} 
add_shortcode("bliptv", "blipTv_code");

#### veoh eg http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.2.20.1002&permalinkId=v17847048KQG6QD2r&player=videodetailsembedded&videoAutoPlay=0&id=anonymous id="v17847048KQG6QD2r"
function veoh_code($atts,$content = null){

	extract(shortcode_atts(array(  
			 "id" 		=> '',
			 "width"	=> 480, 
			 "height" 	=> 360
		 ), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.2.20.1002&permalinkId=$id&player=videodetailsembedded&videoAutoPlay=0&id=anonymous' 
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.2.20.1002&permalinkId=$id&player=videodetailsembedded&videoAutoPlay=0&id=anonymous' />
		</object>";
	return $data;
} 
add_shortcode("veoh", "veoh_code");

#### Viddler eg http://www.viddler.com/player/90b36677/ id="90b36677"
function viddler_code($atts,$content = null){

	extract(shortcode_atts(array(  
			 "id" 		=> '',
			 "width"	=> 480, 
			 "height" 	=> 360
		 ), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://www.viddler.com/player/$id/' 
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://www.viddler.com/player/$id/' />
		</object>";
	return $data;
} 
add_shortcode("viddler", "viddler_code");

#### Revver eg http://flash.revver.com/player/1.0/player.js?mediaId:99898;width:480;height:392; id="99898"
function revver_code($atts,$content = null){

	extract(shortcode_atts(array(  
			 "id" 		=> '',
			 "width"	=> 480, 
			 "height" 	=> 360
		 ), $atts)); 
	 
	$data = "<object	
		width='$width'
		height='$height'
		data='http://flash.revver.com/player/1.0/player.swf?mediaId=$id' 
		type='application/x-shockwave-flash'>
			<param name='allowfullscreen' value='true' />
			<param name='allowscriptaccess' value='always' />
			<param name='wmode' value='opaque'>
			<param name='movie' value='http://flash.revver.com/player/1.0/player.swf?mediaId=$id' />
		</object>";
	return $data;
} 
add_shortcode("revver", "revver_code");

##################################################################################################################################
// 												WIDGETS
##################################################################################################################################

class CategoryRssListWidget extends WP_Widget {

	function CategoryRssListWidget() {
		$widget_ops 	= array('classname' => 'smashingMultiMedia', 'description' => __( 'Display a category rss list', 'smashingMultiMedia') );
		$control_ops 	= array('width' => 300, 'height' => 300, 'id_base' => 'category-rss');
		$this->WP_Widget('category-rss', __('Category Rss List', 'smashingMultiMedia'), $widget_ops, $control_ops);
    }
	
	function widget($args, $instance){
		extract($args);
		$title 		= apply_filters('widget_title', $instance['title'] );
		$orderby 	= $instance['orderby'];
		$order 		= $instance['order'];
		$include 	= $instance['include'];
		$exclude 	= $instance['exclude'];
		$feed_image = $instance['feed_image'];
		
		# Before the widget
		echo $before_widget;

		# The title
		if ( $title )
		echo $before_title . $title . $after_title;

		# Make the Category RSS List widget
		$catRssArg 	= array(
			'include'    	=> $include,
			'exclude'		=> $exclude,
			'title_li'		=> '', 
			'orderby'       => $orderby,
			'order'         => $order,
			'depth'			=> 1,
			'feed_image'	=> $feed_image,
			'feed'			=> 'XML Feed',
			'optioncount'	=> 1,
			'children'		=> 0
		); ?>
		<ul class="catRssFeed">
			<?php wp_list_categories($catRssArg); ?>
		</ul>
		<?php
		# After the widget
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['orderby'] 	= $new_instance['orderby'];
		$instance['order'] 		= $new_instance['order'];
		$instance['include'] 	= $new_instance['include'];
		$instance['exclude'] 	= $new_instance['exclude'];
		$instance['feed_image'] = $new_instance['feed_image'];
		return $instance;
	}
	
	function form($instance){
		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Subscribe', 'smashingMultiMedia'), 'orderby' => 'name', 'order' => 'ASC', 'include' => '', 'exclude' => '', 'feed_image' => 'http://smashingmultimedia.sarah-neuber.de/wp-content/themes/smashingMultiMedia/images/feed.jpg' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'smashingMultiMedia'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php _e('Include:', 'smashingMultiMedia'); ?></label>
			<input id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo $instance['include']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e('Exclude:', 'smashingMultiMedia'); ?></label>
			<input id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo $instance['exclude']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_image' ); ?>"><?php _e('Feed Image:', 'smashingMultiMedia'); ?></label>
			<input id="<?php echo $this->get_field_id( 'feed_image' ); ?>" name="<?php echo $this->get_field_name( 'feed_image' ); ?>" value="<?php echo $instance['feed_image']; ?>" style="width:100%;" />
		</p>
			
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e('Order By:', 'smashingMultiMedia'); ?></label>
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'name' == $instance['format'] ) echo 'selected="selected"'; ?>>name</option>
				<option <?php if ( 'slug' == $instance['format'] ) echo 'selected="selected"'; ?>>slug</option>
				<option <?php if ( 'count' == $instance['format'] ) echo 'selected="selected"'; ?>>count</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e('Order:', 'smashingMultiMedia'); ?></label>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'ASC' == $instance['format'] ) echo 'selected="selected"'; ?>>ASC</option>
				<option <?php if ( 'DESC' == $instance['format'] ) echo 'selected="selected"'; ?>>DESC</option>
			</select>
		</p>
		
		
	<?php }
}

function SmashingMultiMediaWidgets() {
  register_widget('CategoryRssListWidget');
}
  add_action('widgets_init', 'SmashingMultiMediaWidgets');
?>