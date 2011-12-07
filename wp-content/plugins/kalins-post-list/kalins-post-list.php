<?php
/*
Plugin Name: Kalin's Post List
Version: 3.1
Plugin URI: http://kalinbooks.com/post-list-wordpress-plugin/
Description: Creates a shortcode, widget, or PHP snippet for inserting dynamic, highly customizable lists of posts or pages such as related posts or table of contents into your post content or theme.
Author: Kalin Ringkvist
Author URI: http://kalinbooks.com/

------Kalin's Post List WordPress Plugin------------------

Kalin's Post List by Kalin Ringkvist (email: kalin@kalinflash.com)


License:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

define("KALINSPOST_ADMIN_OPTIONS_NAME", "kalinsPost_admin_options");

function kalinsPost_admin_page() {//load php that builds our admin page
	require_once( WP_PLUGIN_DIR . '/kalins-post-list/kalinsPost_admin_page.php');
}

function kalinsPost_admin_init(){
	//add_action('contextual_help', 'kalinsPost_contextual_help', 10, 3);
	
	
	register_deactivation_hook( __FILE__, 'kalinsPost_cleanup' );
	
	/*
	add_action('wp_ajax_kalinsPost_reset_orig', 'kalinsPost_reset_orig');
	add_action('wp_ajax_kalinsPost_reset_my', 'kalinsPost_reset_my');
	add_action('wp_ajax_kalinsPost_save', 'kalinsPost_save');//kalinsPost_savePreset
	*/
	
	add_action('wp_ajax_kalinsPost_save_preset', 'kalinsPost_save_preset');
	add_action('wp_ajax_kalinsPost_delete_preset', 'kalinsPost_delete_preset');//kalinsPost_restore_preset
	add_action('wp_ajax_kalinsPost_restore_preset', 'kalinsPost_restore_preset');
}

function kalinsPost_save_preset(){	
	check_ajax_referer( "kalinsPost_save_preset" );
	
	$kalinsPostAdminOptions = kalinsPost_get_admin_options();
	
	$outputVar = new stdClass();
	
	$valArr = json_decode($kalinsPostAdminOptions['preset_arr']);
	$preset_name = stripslashes($_POST['preset_name']);
	
	$valObj = array();//$valArr[$preset_name];
	
	$valObj["categories"] = $_POST['categories'];//replace these lines with dynamic loop like in restore_preset()?
	$valObj["tags"] = $_POST['tags'];
	$valObj["post_type"] = $_POST['post_type'];
	$valObj["orderby"] = $_POST['orderby'];
	$valObj["order"] = $_POST['order'];
	$valObj['numberposts'] = $_POST['numberposts'];
	$valObj['before'] = stripslashes($_POST['before']);
	$valObj['content'] = stripslashes($_POST['content']);
	$valObj['after'] = stripslashes($_POST['after']);
	$valObj['excludeCurrent'] = $_POST['excludeCurrent'];
	
	$valObj['post_parent'] = $_POST['post_parent'];
	
	$valObj['includeCats'] = $_POST['includeCats'];
	$valObj['includeTags'] = $_POST['includeTags'];
	
	$valObj['requireAllCats'] = $_POST['requireAllCats'];
	$valObj['requireAllTags'] = $_POST['requireAllTags'];
	
	$valArr->$preset_name = $valObj;
	
	$kalinsPostAdminOptions['preset_arr'] = json_encode($valArr);
	
	$kalinsPostAdminOptions['doCleanup'] = $_POST['doCleanup'];
	
	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);//save options to database
	
	$outputVar->status = "success";
	$outputVar->preset_arr = json_decode($kalinsPostAdminOptions['preset_arr']);
	
	$outputVar->previewOutput = kalinsPost_execute($preset_name);
	
	//echo $kalinsPostAdminOptions['preset_arr'];
	
	echo json_encode($outputVar);
}

function kalinsPost_delete_preset(){
	check_ajax_referer( "kalinsPost_delete_preset" );
	
	$kalinsPostAdminOptions = kalinsPost_get_admin_options();
	
	$outputVar = new stdClass();
	
	$valArr = json_decode($kalinsPostAdminOptions['preset_arr']);
	$preset_name = stripslashes($_POST['preset_name']);
	
	unset($valArr->$preset_name);
	
	$kalinsPostAdminOptions['preset_arr'] = json_encode($valArr);
	
	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);//save options to database
	
	echo $kalinsPostAdminOptions['preset_arr'];
}

function kalinsPost_restore_preset(){
	check_ajax_referer( "kalinsPost_restore_preset" );
	
	$kalinsPostAdminOptions = kalinsPost_get_admin_options();
	$defaultAdminOptions = kalinsPost_getAdminSettings();
	
	$outputVar = new stdClass();
	
	$userValArr = json_decode($kalinsPostAdminOptions['preset_arr']);
	$defValArr = json_decode($defaultAdminOptions['preset_arr']);
	
	foreach ($defValArr as $key => $value){
		$userValArr->$key = $value;
	}
	
	$kalinsPostAdminOptions['preset_arr'] = json_encode($userValArr);
	
	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);//save options to database
	
	echo $kalinsPostAdminOptions['preset_arr'];
}


function kalinsPost_configure_pages() {
	
	global $kalinsPost_hook;
	$kalinsPost_hook = add_submenu_page('options-general.php', "Kalin's Post List", "Kalin's Post List", 'manage_options', "Kalins-Post-List", 'kalinsPost_admin_page');
	add_action( "admin_print_scripts-$kalinsPost_hook", 'kalinsPost_admin_head' );
	//add_filter('contextual_help', 'kalinsPost_contextual_help', 10, 3);
}

function kalinsPost_admin_head() {
	//echo "My plugin admin head";
	wp_enqueue_script("jquery");
	//wp_enqueue_script("jquery-ui-sortable");
	//wp_enqueue_script("jquery-ui-dialog");
}

/*function kalinsPost_admin_styles(){//not sure why this didn't work if called from pdf_admin_head
	wp_enqueue_style('kalinPDFStyle');
}*/

function kalinsPost_inner_custom_box($post) {//creates the box that goes on the post/page edit page
	require_once( WP_PLUGIN_DIR . '/kalins-edit-links/kalinsPost_custom_box.php');
}

/*function kalinsPost_contextual_help($contextual_help, $screen_id, $screen) {
	global $kalinsPost_hook;
	if($screen_id == $kalinsPost_hook){
		//$contextual_help = __FILE__;//DOMDocument::loadHTMLFile("kalins_post_admin_help.html");//require_once( WP_PLUGIN_DIR . '/kalins-post-list/kalins_post_admin_help.php');
		
		try {
			$helpFile = DOMDocument::loadHTMLFile(WP_PLUGIN_DIR . '/kalins-post-list/kalins_post_admin_help.html');
		} catch (Exception $e) {
			
			return "what?";
			
			$contextual_help = "Failed to display the help menu because there appears to be an issue with XML support in your PHP installation. Instead, view the <a href='http://kalinbooks.com/post-list-wordpress-plugin/post-list-help-menu'>Post List help page on my website.</a>";
		}
		
		$contextual_help = $helpFile->saveHTML();
	}
	return $contextual_help;
}*/

function kalinsPost_get_admin_options() {
	$kalinsPostAdminOptions = kalinsPost_getAdminSettings();
	
	$devOptions = get_option(KALINSPOST_ADMIN_OPTIONS_NAME);

	if (!empty($devOptions)) {
		foreach ($devOptions as $key => $option){
			$kalinsPostAdminOptions[$key] = $option;
		}
	}

	update_option(KALINSPOST_ADMIN_OPTIONS_NAME, $kalinsPostAdminOptions);

	return $kalinsPostAdminOptions;
}

function kalinsPost_getAdminSettings(){//simply returns all our default option values
	
	$kalinsPostAdminOptions = array();
	
	$kalinsPostAdminOptions['preset_arr'] = '{"pageContentDivided_5":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"postExcerptDivided_5":{"categories":"","tags":"","post_type":"post","orderby":"post_date","order":"DESC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simpleAttachmentList_10":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"10","before":"<ul>","content":"<li><a href=\"[post_permalink]\">[post_title]<\/a><\/li>","after":"<\/ul>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"images_5":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"5","before":"<hr \/>","content":"<p><a href=\"[post_permalink]\"><img src=\"[guid]\" \/><\/a><\/p>","after":"<hr \/>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"pageDropdown_100":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"100","before":"<p><select id=\"postList_dropdown\" style=\"width:200px; margin-right:20px\">","content":"<option value=\"[post_permalink]\">[post_title]<\/option>","after":"<\/ select> <input type=\"button\" id=\"postList_goBtn\" value=\"GO!\" onClick=\"javascript:window.location=document.getElementById(\'postList_dropdown\').value\" \/><\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simplePostList_5":{"categories":"","tags":"","post_type":"post","orderby":"date","order":"DESC","numberposts":"5","before":"<p>","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"footerPageList_10":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"10","before":"<p align=\"center\">","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end] | ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"everythingNumbered_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[item_number]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"everythingID_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[ID]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"relatedPosts_5":{"categories":"","tags":"","post_type":"post","orderby":"rand","order":"DESC","numberposts":"5","before":"<p>Related posts: ","content":"<a href=\"[post_permalink]\" title=\"[post_excerpt]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"true"},"CSSTable":{"categories":"","tags":"","post_type":"post","orderby":"post_date","order":"DESC","numberposts":"15","before":"<style>\n.k_ul{width: 320px;text-align:center;list-style-type:none;}\n.k_li{width: 100px; height:65px; float: left; padding:3px;}\n.k_a{border:1px solid #f00;display:block;text-decoration:none;font-weight:bold;width:100%; height:65px}\n.k_a:hover{border:1px solid #00f;background:#00f;color:#fff;}\n.k_a:active{background:#f00;color:#fff;}\n<\/style><ul class=\"k_ul\">","content":"<li class=\"k_li\"><a class=\"k_a\" href=\"[post_permalink]\">[post_title]<\/a><\/li>","after":"<\/ul>","excludeCurrent":"true","post_parent":"None","includeCats":"false","includeTags":"false","requireAllCats":"false","requireAllTags":"false"}}';
	$kalinsPostAdminOptions['default_preset'] = '';
	$kalinsPostAdminOptions['doCleanup'] = 'true';
	//$kalinsPostAdminOptions['doCleanup'] = "true";
	
	return $kalinsPostAdminOptions;
}

function kalinsPost_cleanup() {//deactivation hook. Clear all traces of Post List
	$adminOptions = kalinsPost_get_admin_options();
	if($adminOptions['doCleanup'] == 'true'){//if user set cleanup to true, remove all options and post meta data
		delete_option(KALINSPOST_ADMIN_OPTIONS_NAME);//remove all options for admin
	}
}

function kalinsPost_init(){
	//setup internationalization here
	//this doesn't actually run and perhaps there's another better place to do internationalization
}

function kalinsPostinternalShortcodeReplace($str, $page, $count){
	$SCList =  array("[ID]", "[post_name]", "[guid]", "[post_content]", "[comment_count]");//not much left of this array, since there's so little post data that I can still just grab unmodified
	
	$l = count($SCList);
	for($i = 0; $i<$l; $i++){//loop through all possible shortcodes
		$scName = substr($SCList[$i], 1, count($SCList[$i]) - 2);
		$str = str_replace($SCList[$i], $page->$scName, $str);
	}
	
	$str = str_replace("[post_author]", get_userdata($page->post_author)->user_login, $str);//post_author requires an extra function call to convert the userID into a name so we can't do it in the loop above
	$str = str_replace("[post_permalink]", get_permalink( $page->ID ), $str);
	$str = str_replace("[post_title]", htmlspecialchars ($page->post_title), $str);
	
	$postCallback = new KalinsPostCallback;
	$postCallback->itemCount = $count;
	$postCallback->page = $page;
	
	$str = preg_replace_callback('#\[ *item_number *(offset=[\'|\"]([^\'\"]*)[\'|\"])? *(increment=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postCountCallback'), $str);
	
	$postCallback->curDate = $page->post_date;//change the curDate param and run the regex replace for each type of date/time shortcode
	$str = preg_replace_callback('#\[ *post_date *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);
	$postCallback->curDate = $page->post_date_gmt;
	$str = preg_replace_callback('#\[ *post_date_gmt *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);
	$postCallback->curDate = $page->post_modified;
	$str = preg_replace_callback('#\[ *post_modified *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);
	$postCallback->curDate = $page->post_modified_gmt;
	$str = preg_replace_callback('#\[ *post_modified_gmt *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);
	
	if(preg_match('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', $str)){
		
		
		
		$str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postExcerptCallback'), $str);
		
		/*if($page->post_excerpt == ""){//if there's no excerpt applied to the post, extract one
			//$postCallback->pageContent = strip_tags($page->post_content);
			$str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postExcerptCallback'), $str);
		}else{//if there is a post excerpt just use it and don't generate our own
			$str = preg_replace('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', $page->post_excerpt, $str);
		}*/
		
		
		
	}
	
	$postCallback->post_type = $page->post_type;
	$postCallback->post_id = $page->ID;
	$str = preg_replace_callback('#\[ *post_pdf *\]#', array(&$postCallback, 'postPDFCallback'), $str);
	
	if (current_theme_supports('post-thumbnails') ){
		$arr = wp_get_attachment_image_src( get_post_thumbnail_id( $page->ID ), 'single-post-thumbnail' );
		$str = str_replace("[post_thumb]", $arr[0], $str);
	}
	
	$postCallback->page = $page;
	$str = preg_replace_callback('#\[ *post_meta *(name=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postMetaCallback'), $str);
	$str = preg_replace_callback('#\[ *post_categories *(delimeter=[\'|\"]([^\'\"]*)[\'|\"])? *(links=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postCategoriesCallback'), $str);
	$str = preg_replace_callback('#\[ *post_tags *(delimeter=[\'|\"]([^\'\"]*)[\'|\"])? *(links=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postTagsCallback'), $str);
	$str = preg_replace_callback('#\[ *post_comments *(before=[\'|\"]([^\'\"]*)[\'|\"])? *(after=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'commentCallback'), $str);
	$str = preg_replace_callback('#\[ *post_parent *(link=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postParentCallback'), $str);
	
	$str = preg_replace_callback('#\[ *php_function *(name=[\'|\"]([^\'\"]*)[\'|\"])? *(param=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'functionCallback'), $str);
	
	return $str;
}

class KalinsPostCallback{//class used just for all the preg_replace_callback function calls
	
	function postPDFCallback(){
		if($this->post_type == "page"){
			$postID = "pg_" .$this->post_id;
		}else{
			$postID = "po_" .$this->post_id;
		}
		return get_bloginfo('wpurl') . '/wp-content/plugins/kalins-pdf-creation-station/kalins_pdf_create.php?singlepost=' .$postID;
	}
	
	function postExcerptCallback($matches){
		
		
		$pageContent = strip_tags($this->page->post_content);
		//return "blah" .$exLength;
		if($this->page->post_excerpt == ""){//if there's no excerpt applied to the post, extract one
			//$postCallback->pageContent = strip_tags($page->post_content);
			//$str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postExcerptCallback'), $str);
			
			if(isset($matches[2])){
				$exLength = intval($matches[2]);
			}else{
				$exLength = 250;
			}
			
			//return "blah";
			
			if(strlen($pageContent) > $exLength){
				return strip_shortcodes(substr($pageContent, 0, $exLength)) ."...";//clean up and return excerpt
			}else{
				return strip_shortcodes($pageContent);
			}
			
		}else{//if there is a post excerpt just use it and don't generate our own
			/*if(isset($matches[2])){//uncomment this if/else statement if you want the manual excerpt to be trimmed to the passed in length
				$exLength = intval($matches[2]);
				return substr($this->page->post_excerpt, 0, $exLength);
			}else{
				return $this->page->post_excerpt;
			}*/
			return $this->page->post_excerpt;
		}
		
		
		
		
		
	}
	
	function postDateCallback($matches){
		if(isset($matches[2])){//geez, regex's are awesome. the [2] grabs the second internal portion of the regex, the actual shortcode param value, the () within the ()
			return mysql2date($matches[2], $this->curDate, $translate = true);//translate the wordpress formatted date into whatever date formatting the user passed in
		}else{
			return mysql2date("m-d-Y", $this->curDate, $translate = true);//otherwise do a simple day-month-year format
		}
	}	
	
	function postCountCallback($matches){
		if(isset($matches[4])){
			$increment = $matches[4];
		}else{
			$increment = 1;//default is to increment by 1 each loop
		}
		
		if(isset($matches[2])){
			return $this->itemCount * $increment + $matches[2];
		}else{
			return $this->itemCount * $increment + 1;//default is to start at 1
		}
	}
	
	function postMetaCallback($matches){
		$arr = get_post_meta($this->page->ID, $matches[2]);
		return $arr[0];
	}
	
	function postCategoriesCallback($matches){
		$catString = "";
		
		$categories = get_the_category($this->page->ID);
		$last_item = end($categories);
		
		if(isset($matches[2])){
			$delimeter = $matches[2];
		}else{
			$delimeter = ', ';
		}
		
		if(isset($matches[4]) && strtolower($matches[4]) == 'false'){
			$links = false;
		}else{
			$links = true;
		}
		
		foreach($categories as $category) {
			if($links){
				$catString = $catString .'<a href="' .get_category_link( $category->cat_ID ) .'" >' .$category->cat_name .'</a>';
			}else{
				$catString = $catString .$category->cat_name;
			}
			if($category != $last_item){
				$catString = $catString .$delimeter;
			}
		}
		
		return $catString;
	}
	
	function postTagsCallback($matches){
		
		$catString = "";
		$categories = get_the_tags($this->page->ID);
		
		if(!$categories){
			return "";
		}
		
		$last_item = end($categories);
		
		if(isset($matches[2])){
			$delimeter = $matches[2];
		}else{
			$delimeter = ', ';
		}
		
		if(isset($matches[4]) && strtolower($matches[4]) == 'false'){
			$links = false;
		}else{
			$links = true;
		}
		
		foreach($categories as $category) {
			if($links){
				$catString = $catString .'<a href="' .get_tag_link( $category->term_id ) .'" >' .$category->name .'</a>';
			}else{
				$catString = $catString .$category->name;
			}
			
			if($category != $last_item){
				$catString = $catString .$delimeter;
			}
		}
		
		return $catString;
	}
	
	function commentCallback($matches) {
		
		/*
		global $post;
		$post = $this->page;//set global post object just for comments
		query_posts('p=' .$this->page->ID);//for some reason this is also necessary so other plugins have access to values normally inside The Loop
		*/
		
		if(defined("KALINS_PDF_COMMENT_CALLBACK")){
			return call_user_func(KALINS_PDF_COMMENT_CALLBACK);
		}
		
		$comments = get_comments('status=approve&post_id=' .$this->page->ID);
		$commentString = $matches[2];
		
		foreach($comments as $comment) {
			if($comment->comment_author_url == ""){
				$authorString = $comment->comment_author;
			}else{
				$authorString = '<a href="' .$comment->comment_author_url .'" >' .$comment->comment_author ."</a>";
			}
			$commentString = $commentString .'<p>' .$authorString ."- " .$comment->comment_author_email ." - " .get_comment_date(null, $comment->comment_ID) ." @ " .get_comment_date(get_option('time_format'), $comment->comment_ID) ."<br />" . $comment->comment_content ."</p>";	
		}
		
		return $commentString .$matches[4];
	}
	
	function postParentCallback($matches){
		$parentID = $this->page->post_parent;
		
		if($parentID == 0){
			return "";
		}
		
		if($matches[2] == "false"){
			return get_the_title($parentID);
		}else{
			return '<a href="' .get_permalink( $parentID ) .'" >' .get_the_title($parentID) .'</a>';
		}
	}
	
	function functionCallback($matches){//call a user defined function through shortcode
	
		if(!defined("KALINS_ALLOW_PHP") || KALINS_ALLOW_PHP !== true){
			return ' Error: add define("KALINS_ALLOW_PHP", true); to your wp-config.php for php_function to work. ';
		}
		
		if(!$matches[2]){
			return ' Error: injected PHP function must have a name. Add a name parameter to your php_function shortcode. ';
		}
		
		/*
		global $post;
		$post = $this->page;
		query_posts('p=' .$this->page->ID);//set global post object and post data so custom function has access to it
		*/
		
		if($matches[4]){
			return call_user_func($matches[2], $this->page, $matches[4]);
		}else{
			return call_user_func($matches[2], $this->page);
		}
	}
}

function kalinsPost_shortcode($atts){
	return kalinsPost_execute($atts['preset']);
}

function kalinsPost_show($preset){
	echo kalinsPost_execute($preset);
}

function kalinsPost_execute($preset) {
	
	$adminOptions = kalinsPost_get_admin_options();
	$presetObj = json_decode($adminOptions['preset_arr']);
	
	if(isset($presetObj->$preset)){
		$newVals = $presetObj->$preset;
	}else{//they passed in a wrong preset name, so we must error out :(
		if (current_user_can('manage_options')) { 
			 return "<p>Kalin's Post List has a problem. A non-existent preset name has been passed in! This message only displays for admins.</p>";
		}else{ 
			return "";
		}
	}
	
	$excludeList = "";
	
	global $post;
	
	if($newVals->post_type == "none"){//if we're not showing a list of anything, only show the content, ignore everything else, and apply the shortcodes to the page being currently viewed
		$output = kalinsPostinternalShortcodeReplace($newVals->content, $post, 0);
	}else{
	
		if($newVals->excludeCurrent == "true"){
			$excludeList = $post->ID;
		}
		
		$newVals->before = kalinsPostinternalShortcodeReplace($newVals->before, $post, 0);
		
		$newVals->after = kalinsPostinternalShortcodeReplace($newVals->after, $post, 0);
		
		$catString = $newVals->categories;
		if($newVals->includeCats == "true"){
			$post_categories = wp_get_post_categories($post->ID);
			foreach($post_categories as $c){
				$catString = $catString .$c .",";
			}
		}
		
		$tagString = $newVals->tags;
		if($newVals->includeTags == "true"){
			$post_tags = wp_get_post_tags( $post->ID);
			foreach($post_tags as $c){
				$tagString = $tagString .$c->slug .",";
			}
		}
		
		if(!isset($newVals->post_parent)){
			$newVals->post_parent = "None";
		}else{
			if($newVals->post_parent == "current"){
				$newVals->post_parent = $post->ID;
			}
		}
		
		if($newVals->requireAllCats == "true" || $newVals->requireAllTags == "true"){
			$origNumberposts = $newVals->numberposts;
			$newVals->numberposts = -1;
		}
		
		//return '--------------------numberposts=' .$newVals->numberposts .'&category=' .$catString .'&post_type=' .$newVals->post_type .'&tag=' .$tagString .'&orderby=' .$newVals->orderby .'&order=' .$newVals->order .'&exclude=' .$excludeList .'&post_parent=' .$newVals->post_parent;
		
		$posts = get_posts('numberposts=' .$newVals->numberposts .'&category=' .$catString .'&post_type=' .$newVals->post_type .'&tag=' .$tagString .'&orderby=' .$newVals->orderby .'&order=' .$newVals->order .'&exclude=' .$excludeList .'&post_parent=' .$newVals->post_parent);
		
		//$args = array('orderby' => 'name');
		//$posts = query_posts("orderby=tax_query");
		
		if($newVals->requireAllCats == "true"){//if every post must lie in every selected category
			$requiredCats = explode(",", $newVals->categories);//create array from list of categories
			foreach ($posts as $key => $page) {
				$pageCats = implode(",", wp_get_post_categories($page->ID));//get each post's cats and concat into string
				foreach($requiredCats as $key2 => $value){
					if($value){
						$strPosVal = strpos($pageCats, $value);//for every cat in requiredCats, check if it's in this page list of cats
						if($strPosVal === false && $value != ""){
							unset($posts[$key]);//if it's not in the page's list, delete it
							break;
						}
					}
				}
			}
		}
		
		if($newVals->requireAllTags == "true"){
			$requiredTags = explode(",", $newVals->tags);
			foreach ($posts as $key => $page) {
				$tagArr = wp_get_post_tags($page->ID);
				$pageTags = "";
				foreach($tagArr as $tag){//tags came as an array of objects instead of ID values, so we loop to create our searchable string, which for tags is based on slugs instead of IDs
					$pageTags = $pageTags .$tag->slug .",";
				}
				foreach($requiredTags as $key2 => $value){	//works the same as categor section above, except we're looking for slugs instead of IDs
				
					if($value){
						$strPosVal = strpos($pageTags, $value);
						if($strPosVal === false && $value != ""){
							unset($posts[$key]);
							break;
						}
					}
				}
			}
		}
		
		if($newVals->requireAllCats == "true" || $newVals->requireAllTags == "true"){
			$posts = array_slice($posts, 0, $origNumberposts);
		}
		
		if(count($posts) == 0){//return nothing if no results
			return "";
		}
		
		$output = $newVals->before;
		
		$count = 0;
		foreach ($posts as $page) {
			
			$output = $output .kalinsPostinternalShortcodeReplace($newVals->content, $page, $count);
			$count = $count + 1;
		}
		
		$finalPos = strrpos ($output , "[final_end]");
		if($finalPos > 0){//if ending exists (the last item where we don't want to add any more commas or ending brackets or whatever)
			$output = substr($output, 0, $finalPos);//cut everything off at the final position of {final_end}
			$output = str_replace("[final_end]", "", $output);//replace all the other instances of {final_end}, since we only care about the last one
		}
		
		$output = $output .$newVals->after;
	}
	
	return $output;
}


class WP_Kalins_Post_List_Widget extends WP_Widget {
 
	function WP_Kalins_Post_List_Widget() {
		$widget_ops = array( 'classname' => 'widget_KalinsPostList', 'description' => __( "Display a customized list of posts or pages" ) );
		$this->WP_Widget('kalinsPostList', __("Kalin's Post List"), $widget_ops);
	}
 
	// This code displays the widget on the screen.
	function widget($args, $instance) {
		extract($args);
		echo $before_widget;
		if(!empty($instance['title'])) { 
			echo $before_title . $instance['title'] . $after_title; 
		}
		
		kalinsPost_show($instance['k_preset']);
		
		echo $after_widget;
	}
 
	// Updates the settings.
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
	
	function form($instance) {		
	
		$adminOptions = kalinsPost_get_admin_options();	
		$presetArr = json_decode($adminOptions["preset_arr"]);
		
		echo '<div>';
		echo '<label for="' . $this->get_field_id("title") .'">Title:</label>';
		echo '<input type="text" class="widefat" ';
		echo 'name="' . $this->get_field_name("title") . '" '; 
		echo 'id="' . $this->get_field_id("title") . '" ';
		echo 'value="' . $instance["title"] . '" /><br/><br/>';
		
		echo '<label for="' . $this->get_field_id("k_preset") .'">Preset Name:</ label>';
		echo '<select class="widefat" ';
		echo 'name="' . $this->get_field_name("k_preset") . '" ';
		echo 'id="' . $this->get_field_id("k_preset") . '" >';
		
		$selectVal = $instance['k_preset'];
		
		foreach($presetArr as $key => $value){
			if($key == $instance['k_preset']){
				echo '<option value="' .$key .'" selected="yes" >' .$key .'</ option>';
			}else{
				echo '<option value="' .$key .'">' .$key .'</ option>';
			}
		}
		
		echo '</select><br/><br/></div>';
		
	} // end function form
 
} // end class WP_Widget_BareBones
 
// Register the widget.
add_action('widgets_init', create_function('', 'return register_widget("WP_Kalins_Post_List_Widget");'));

add_shortcode('post_list', 'kalinsPost_shortcode');

//wp actions to get everything started
add_action('admin_init', 'kalinsPost_admin_init');
add_action('admin_menu', 'kalinsPost_configure_pages');
//add_action( 'init', 'kalinsPost_init' );//just keep this for whenever we do internationalization - if the function is actually needed, that is.


?>