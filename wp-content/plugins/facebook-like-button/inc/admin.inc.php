<?php


/*
File Name: admin.inc.php
Descrption: Form the admin section 
*/

/*P.S. I like how i handle the arrays!*/

function FB_Admin_Cont()
{

// available fonts
$fblikes_fonts = array("Default"        => "",
                       "Arial"          => "arial",
                       "Lucida Grande"  => "lucida grande",
                       "Segoe Ui"       => "segoe ui",
                       "Tahoma"         => "tahoma",
                       "Trebuchet MS"   => "trebuchet ms",
                       "Verdana"        => "verdana",
                      );
			

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
			'cat'    => 'cat',
			'arch'   => 'arch',
			'enimg'  => 'enimg',
			'align'  => 'align',
			'send'   => 'send',
			'social' => 'social'
			
               
			   );

$Value = array();
			   
foreach($Names as $Na){ //Get Options Names
		
		$Value["$Na"] = get_option("fb_like_".$Na);
		
	}

    
	/* Check and Selected the saved options */
    $xfbml = ($Value['type'] == 'xfbml') ? 'CHECKED' : '';
    $iframe = ($Value['type'] == 'iframe') ? 'CHECKED' : '';

    $stan = ($Value['layout'] == 'standard') ? 'SELECTED' : '';
    $count = ($Value['layout'] == 'button_count') ? 'SELECTED' : '';
	$box_c = ($Value['layout'] == 'box_count') ? 'SELECTED' : '';

    $face = ($Value['face'] == 'true') ? 'CHECKED' : '';

    $like = ($Value['verb'] == 'like') ? 'SELECTED' : '';
    $reco = ($Value['verb'] == 'recommend') ? 'SELECTED' : '';

    $light = ($Value['color'] == 'light') ? 'SELECTED' : '';
    $dark = ($Value['color'] == 'dark') ? 'SELECTED' : '';
    $evil = ($Value['color'] == 'evil') ? 'SELECTED' : '';

    $after = ($Value['pos'] == 'after') ? 'SELECTED' : '';
    $before = ($Value['pos'] == 'before') ? 'SELECTED' : '';
    $baf = ($Value['pos'] == 'baf') ? 'SELECTED' : '';
	$man = ($Value['pos'] == 'man') ? 'SELECTED' : '';

    $px = ($Value['ht'] == "px") ? 'SELECTED' : '';
    $em = ($Value['ht'] == "em") ? 'SELECTED' : '';
	
	$left = ($Value['align'] == "left") ? 'SELECTED' : '';
    $right = ($Value['align'] == "right") ? 'SELECTED' : '';
    $no_float = ($Value['align'] == "") ? 'SELECTED' : '';
	
	$home = ($Value['home'] == 'true') ? 'CHECKED' : '';
	$page = ($Value['page'] == 'true') ? 'CHECKED' : '';
	$post = ($Value['post'] == 'true') ? 'CHECKED' : '';
	$cat = ($Value['cat'] == 'true') ? 'CHECKED' : '';
	$arch = ($Value['arch'] == 'true') ? 'CHECKED' : '';
	
    $Width = ($Value['width'] == null) ? '450' : $width;
	
	$enable_image = ($Value['enimg'] == true) ? '' : 'disabled="disabled"';
	$check_image  = ($Value['enimg'] == true) ? 'checked="checked"' : '';
	$send  = ($Value['send'] == 'true') ? 'checked="checked"' : '';
	$social  = ($Value['social'] == 'true') ? 'checked="checked"' : '';
	
	
	/*Include jquery and the live preview main file*/
    
	$Live  = 
            "<script src='". plugins_url('js/jquery.js',__FILE__)."' type = 'text/javascript'></script>
             <script src='". plugins_url('js/live.js',__FILE__)."' type = 'text/javascript'></script>"; 
	
	/*Get the layout*/
	 require_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/admin_layout.inc.php");
	
	 require_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/activation.php");
	 
	
	 
    echo $Live.$Layout;

}


?>