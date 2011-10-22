<?php

/*
File Name: options.inc.php
Descreption: Add options and update them
*/


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
			'css'    => 'css', //Container CSS Class
			'home'   => 'home', //Show in home
			'page'   => 'page', //Show in pages
			'post'   => 'post',  //show in posts
			'cat'    => 'cat',   // show in cats
			'arch'   => 'arch', // show in archive
			'admeta' => 'admeta',
			'dimage' => 'dimage',
			'enimg'  => 'enimg',
			'align'  => 'align',
			'social'  => 'social',
			'add'    => 'add'
			
               
			   );


	
	foreach($Names as $Na){ //Get Options Names
		
		add_option("fb_like_".$Na, '');
		
	}
	
/*
Add the recommendations options
*/

$RecName = array(

        'appid'  => 'appid',
		'method' => 'method',
		'domain' => 'domain',
		'width'  => 'width',
		'height' => 'height',
		'layout' => 'layout',
		'font'   => 'font',
		'border' => 'border',
		'header' => 'header',
		'wid_title' => 'wid_title'

);

foreach($RecName as $RecN){
	add_option('fpp_rec_'.$RecN, '');
	}


/*
Add locale and fonts options by Anty
*/	
add_option("fblikes_locale", "default");
add_option("fblikes_font", "");

?>