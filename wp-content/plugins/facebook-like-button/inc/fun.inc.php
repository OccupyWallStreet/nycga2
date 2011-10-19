<?php

/*
File Name: functions.inc.php
Descreption: All kind of functions will be here
*/


/*
Add the website name to the header using
og:sitename meta for opengraph
*/

function Add_Site_Name(){
	$Page_ID = get_the_ID();
	$Name = get_bloginfo('name');
	$parent_title = get_the_title($post->post_parent);
	$prem = get_permalink(get_the_ID());
	$post_by_id = get_post(get_the_ID(), ARRAY_A);
        if(function_exists('get_post_thumbnail_id'))
        {
        $image_id = get_post_thumbnail_id();
        $image_url = wp_get_attachment_image_src($image_id,'large');
        $image_url = $image_url[0];
        }
        else
        {
            $image_url = '';
        }
	  
	
	$Defualt_Image = ($image_url == '' ? get_option('fb_like_dimage') : $image_url);
	
	$Meta = '
	<!--Facebook Like Button OpenGraph Settings Start-->';
	
	if(get_option('fb_like_social') == 'true')
		{
			$Meta .= '
<script type="text/javascript" src="https://app.tabpress.com/js/ga_social_tracking.js"></script>
<script type="text/javascript">_ga.trackFacebook();</script>
';
		}
	
	$Meta .= '
	<meta property="og:site_name" content="'.$Name.'"/>';
	if(is_front_page()){
		
		$Title ='
	<meta property="og:title" content="'.get_bloginfo('name').'"/>';
		$URL = 
	'
	<meta property="og:url" content="'.get_bloginfo('url').'"/>
	';
	}
	else
	{
	$Title = '
	<meta property="og:title" content="'.$parent_title.'"/>';
	$URL = 
	'
	<meta property="og:url" content="'.$prem.'"/>
	';
	$Title .= '
		<meta property="og:description" content="'.@htmlentities(@trim(substr(strip_tags($post_by_id['post_content']), 0, 140))).'"/>
	';
	}
	
	$Admeta = '<meta property="fb:admins" content="'.get_option("fb_like_admeta").'" />';
	$Admeta.= 
	'
	<meta property="fb:app_id" content="'.get_option("fb_like_appid").'" />
	';
	
	if(get_option("fb_like_enimg") != false){
		
		$Admeta .= '<meta property="og:image" content="'.$Defualt_Image.'" />
	';
		}
	
	
	if((is_single) || (is_page())){
		$PageType = (get_option("disable_like_pagetype_".$Page_ID) == null ? "article" : get_option("disable_like_pagetype_".$Page_ID));
		$Admeta .= '<meta property="og:type" content="'.$PageType.'" />
		';
		}
	if(is_front_page()){
		$Admeta .= '<meta property="og:type" content="blog" />
		';
		}
	$Admeta .= '<!--Facebook Like Button OpenGraph Settings End-->
	';
	echo $Meta . $Title . $URL . $Admeta;
	
}





?>