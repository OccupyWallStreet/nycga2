<?php

/*
File Name: buttons.inc.php
Descprtion: Buttons functions
*/

function Add_Like_Button($content)
{
	
	 $Names = array(
            
			'appid'  => 'appid', //AppID
			'type'   => 'type', //Button Type
			'pos'    => 'pos', //Position
			'layout' => 'layout', //Layout
			'face'   => 'face', //Show Faces
			'verb'   => 'verb', //Verb to display
			'color'  => 'color', //Button Color
			'width'  => 'width', //Container Width
			'height' => 'height', //Container Height
			'ht'     => 'ht', //Height Type px or em
			'home'   => 'home', //Show in home
			'page'   => 'page', //Show in pages
			'post'   => 'post',  //show in posts
			'cat'    => 'cat',
			'arch'   => 'arch',
			'css'    => 'css',
			'align'  => 'align',
			'send'   => 'send'
               
			   );

$Value = array();
			   
foreach($Names as $Na){ //Get Options Names
		
		$Value["$Na"] = get_option("fb_like_".$Na);
		
	}
    
$face = ($Value['face'] == "true") ? 'true' : 'false';

$heghit_style = ($Value['height'] == '') ? '' : 'height: '.$Value['height'].$Value['ht'].'; ';
$width_style = ($Value['width'] == '') ? '' : 'width: '.$Value['width'].$Value['ht'].'; ';

$float_style = ($Value['align'] == '') ? '' : 'float: '.$Value['align'].'; ';
$send = ($Value['send'] == 'true') ? 'true' : 'false';

$SDK = '<div id="fb-root"></div>
   <script>
   window.fbAsyncInit = function() {
   FB.init({appId: "' . $Value['appid'] . '", status: true, cookie: true,
		 xfbml: true});
	};
 (function() {
  var e = document.createElement("script"); e.async = true;
 e.src = document.location.protocol +
   "//connect.facebook.net/'. fblikes_get_locale() .'/all.js";
 document.getElementById("fb-root").appendChild(e);
}());
</script>';

$url = get_permalink(get_the_ID());
$Page_ID = get_the_ID();

$xfbml = '<span class = "' . $Value['css'] . '"  style = "'.$heghit_style.' '.$width_style.' '.$float_style.'"><fb:like href="' . $url . '" send = "'.$send.'" layout="' . $Value['layout'] . '" show_faces="' . $face .
	'" width="' . $Value['width'] . '" action="' . $Value['verb'] . '" colorscheme="' . $Value['color'] . '" font="' . get_option("fblikes_font") .
	'" /></span>';
$iframe = ' 
<span class = "' . $Value['css'] . '" style = "'.$heghit_style.' '.$float_style.'"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $url .
	'&layout=' . $Value['layout'] . '&send='.$send.'&show_faces=' . $face . '&width=' . $Value['width'] . '&action=' .
	$Value['verb'] . '&colorscheme=' . $Value['color'] . fblikes_get_url_locale() . '&font=' . get_option("fblikes_font") .
	'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $Value['width'] . 'px; height:' . $Value['height'] . $Value['ht'] .'"></iframe></span>';

if((is_front_page()) && ($Value['home'] == "true") || (is_single()) && ($Value['post'] == "true") && (get_option('disable_like_status_'.$Page_ID) != true) || (is_page() && $Value['page'] == "true" && get_option('disable_like_status_'.$Page_ID) != true) || 
(is_category() && $Value['cat'] == 'true') || (is_archive() && $Value['arch'] == 'true') ){  
if (($Value['type'] == "xfbml") && ($Value['pos'] == 'after')) {

	$but = $SDK . $xfbml;
	$content = $content . $but;

}
if (($Value['type'] == "xfbml") && ($Value['pos'] == 'before')) {

	$but = $SDK . $xfbml;
	$content = $but . $content;
	str_replace('[Like_Button]', $but , $content);
}

if (($Value['type'] == "xfbml") && ($Value['pos'] == 'baf')) {

	$but = $SDK . $xfbml;
	$content = $but . $content . $but;
	
	str_replace('[Like_Button]', $but, $content);
}

if (($Value['type'] == 'iframe') && ($Value['pos'] == 'after')) {
	$content .= $iframe;
}

if (($Value['type'] == 'iframe') && ($Value['pos'] == 'before')) {

	$content = $iframe . $content;
}

if (($Value['type'] == 'iframe') && ($Value['pos'] == 'baf')) {

	$content = $iframe . $content . $iframe;
}

}


if((is_front_page()) || (is_home()) && ($Value['home'] == "") || (is_archive() && $Value['arch'] == '') || (is_category() && $Value['cat'] == '') || (is_single()) && ($Value['post'] == "") || (is_page()) && ($Value['page'] == "" || 
   (get_option("fb_like_pos") == "man"))){  

$content = $content;


}


return $content;

}

function Short_Button(){

	$Names = array(
            
			'appid'  => 'appid', //AppID
			'type'   => 'type', //Button Type
			'pos'    => 'pos', //Position
			'layout' => 'layout', //Layout
			'face'   => 'face', //Show Faces
			'verb'   => 'verb', //Verb to display
			'color'  => 'color', //Button Color
			'width'  => 'width', //Container Width
			'height' => 'height', //Container Height
			'ht'     => 'ht', //Height Type px or em
			'home'   => 'home', //Show in home
			'page'   => 'page', //Show in pages
			'post'   => 'post',  //show in posts
			'cat'    => 'cat',
			'arch'   => 'arch',
			'css'    => 'css',
			'align'  => 'align',
			'send'   => 'send'
               
			   );
$Value = array();
		   
foreach($Names as $Na){ //Get Options Names
	
	$Value["$Na"] = get_option("fb_like_".$Na);
	
}
$send = ($Value['send'] == 'true') ? 'true' : 'false';

$face = ($Value['face'] == "true") ? 'true' : 'false';

$SDK = '<div id="fb-root"></div>
   <script>
   window.fbAsyncInit = function() {
   FB.init({appId: "' . $Value['appid'] . '", status: true, cookie: true,
		 xfbml: true});
	};
 (function() {
  var e = document.createElement("script"); e.async = true;
 e.src = document.location.protocol +
   "//connect.facebook.net/'. fblikes_get_locale() .'/all.js";
 document.getElementsById("fb-root").appendChild(e);
}());
</script>';

$url = get_permalink(get_the_ID());


$xfbml = '<span class = "' . $Value['css'] . '"  style = "height: ' . $Value['height'] . $Value['ht'] .
	'"><fb:like href="' . $url . '" send = "'.$send.'" layout="' . $Value['layout'] . '" show_faces="' . $face .
	'" width="' . $Value['width'] . '" action="' . $Value['verb'] . '" colorscheme="' . $Value['color'] . '" font="' . get_option("fblikes_font") .
	'" /></span>';
$iframe = ' 
<span class = "' . $Value['css'] . '" style = "height: ' . $Value['height'] . $Value['ht'] .
	'"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $url .
	'&send='.$send.'&layout=' . $Value['layout'] . '&show_faces=' . $face . '&width=' . $Value['width'] . '&action=' .
	$Value['verb'] . '&colorscheme=' . $Value['color'] . fblikes_get_url_locale() . '&font=' . get_option("fblikes_font") .
	'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $Value['width'] .'px; height:' . $Value['height'] . $Value['ht'] . '"></iframe></span>';

 
if ($Value['type'] == "xfbml") {

	$but = $SDK . $xfbml;
	

}

if ($Value['type'] == 'iframe') {

	$but = $iframe;
	
	
}

return $but;


}

function Count_Button(){


$Names = array(
            
			'appid'  => 'appid', //AppID
			'type'   => 'type', //Button Type
			'pos'    => 'pos', //Position
			'layout' => 'layout', //Layout
			'face'   => 'face', //Show Faces
			'verb'   => 'verb', //Verb to display
			'color'  => 'color', //Button Color
			'width'  => 'width', //Container Width
			'height' => 'height', //Container Height
			'ht'     => 'ht', //Height Type px or em
			'home'   => 'home', //Show in home
			'page'   => 'page', //Show in pages
			'post'   => 'post',  //show in posts
			'cat'    => 'cat',
			'arch'   => 'arch',
			'css'    => 'css',
			'align'  => 'align',
			'send'   => 'send'
               
			   );
			   
$Value = array();
		   
foreach($Names as $Na){ //Get Options Names
	
	$Value["$Na"] = get_option("fb_like_".$Na);
	
}
$send = ($Value['send'] == 'true') ? 'true' : 'false';

$face = ($Value['face'] == "true") ? 'true' : 'false';

$SDK = '<div id="fb-root"></div>
   <script>
   window.fbAsyncInit = function() {
   FB.init({appId: "' . $Value['appid'] . '", status: true, cookie: true,
		 xfbml: true});
	};
 (function() {
  var e = document.createElement("script"); e.async = true;
 e.src = document.location.protocol +
   "//connect.facebook.net/'. fblikes_get_locale() .'/all.js";
 document.getElementById("fb-root").appendChild(e);
}());
</script>';

$url = get_permalink(get_the_ID());


$xfbml = '<span class = "' . $Value['css'] . '"  style = "height: ' . $Value['height'] . $Value['ht'] .
	'"><fb:like href="' . $url . '" send = "'.$send.'" layout="button_count" show_faces="' . $face .
	'" width="' . $Value['width'] . '" action="' . $Value['verb'] . '" colorscheme="' . $Value['color'] . '" font="' . get_option("fblikes_font") .
	'" /></span>';
$iframe = ' 
<span class = "' . $Value['css'] . '" style = "height: ' . $Value['height'] . $Value['ht'] .
	'"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $url .
	'&layout=button_count&send='.$send.'&show_faces=' . $face . '&width=' . $Value['width'] . '&action=' .
	$Value['verb'] . '&colorscheme=' . $Value['color'] . fblikes_get_url_locale() . '&font=' . get_option("fblikes_font") .
	'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $Value['width'] .'px; height:' . $Value['height'] . $Value['ht'] . '"></iframe></span>';

 
if ($Value['type'] == "xfbml") {

	$but = $SDK . $xfbml;
	

}

if ($Value['type'] == 'iframe') {

	$but = $iframe;
	
	
}

return $but;



}


function fb_box_count(){


$Names = array(
            
			'appid'  => 'appid', //AppID
			'type'   => 'type', //Button Type
			'pos'    => 'pos', //Position
			'layout' => 'layout', //Layout
			'face'   => 'face', //Show Faces
			'verb'   => 'verb', //Verb to display
			'color'  => 'color', //Button Color
			'width'  => 'width', //Container Width
			'height' => 'height', //Container Height
			'ht'     => 'ht', //Height Type px or em
			'home'   => 'home', //Show in home
			'page'   => 'page', //Show in pages
			'post'   => 'post',  //show in posts
			'cat'    => 'cat',
			'arch'   => 'arch',
			'css'    => 'css',
			'align'  => 'align',
			'send'   => 'send'
               
			   );

$Value = array();
		   
foreach($Names as $Na){ //Get Options Names
	
	$Value["$Na"] = get_option("fb_like_".$Na);
	
}
$send = ($Value['send'] == 'true') ? 'true' : 'false';

$face = ($Value['face'] == "true") ? 'true' : 'false';

$SDK = '<div id="fb-root"></div>
   <script>
   window.fbAsyncInit = function() {
   FB.init({appId: "' . $Value['appid'] . '", status: true, cookie: true,
		 xfbml: true});
	};
 (function() {
  var e = document.createElement("script"); e.async = true;
 e.src = document.location.protocol +
   "//connect.facebook.net/'. fblikes_get_locale() .'/all.js";
 document.getElementById("fb-root").appendChild(e);
}());
</script>';

$url = get_permalink(get_the_ID());


$xfbml = '<span class = "' . $Value['css'] . '"  style = "height: ' . $Value['height'] . $Value['ht'] .
	'"><fb:like href="' . $url . '" send = "'.$send.'" layout="box_count" show_faces="' . $face .
	'" width="' . $Value['width'] . '" action="' . $Value['verb'] . '" colorscheme="' . $Value['color'] . '" font="' . get_option("fblikes_font") .
	'" /></span>';
$iframe = ' 
<span class = "' . $Value['css'] . '" style = "height: ' . $Value['height'] . $Value['ht'] .
	'"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $url .
	'&layout=button_count&send='.$send.'&show_faces=' . $face . '&width=' . $Value['width'] . '&action=' .
	$Value['verb'] . '&colorscheme=' . $Value['color'] . fblikes_get_url_locale() . '&font=' . get_option("fblikes_font") .
	'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $Value['width'] .'px; height:' . $Value['height'] . $Value['ht'] . '"></iframe></span>';

 
if ($Value['type'] == "xfbml") {

	$but = $SDK . $xfbml;
	

}

if ($Value['type'] == 'iframe') {

	$but = $iframe;
	
	
}

return $but;



}




?>