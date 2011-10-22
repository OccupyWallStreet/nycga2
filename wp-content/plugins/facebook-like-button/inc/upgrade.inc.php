<?php

/*
File Name: upgrade.inc.php
Descreption: Transform user's previous setting from SQL to options just once the user reactivate the plugin.
*/

 function Upgrade_Latest(){
	 
	 global $wpdb; // WP database api
     
    $table_name = $wpdb->prefix . "FBLikes";
	
	require_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/options.inc.php"); // Create Options
	
    if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {
		
	$Type = $wpdb->get_var("SELECT type FROM $table_name WHERE id = '1'"); //Get Type
    $AppID = $wpdb->get_var("SELECT appid FROM $table_name WHERE id = '1'"); //Get appid
    $Pos = $wpdb->get_var("SELECT pos FROM $table_name WHERE id = '1'"); //Get posetion
    $Layout = $wpdb->get_var("SELECT layout FROM $table_name WHERE id = '1'"); //Get layout
    $verb = $wpdb->get_var("SELECT verb FROM $table_name WHERE id = '1'"); //Get verb
    $color = $wpdb->get_var("SELECT color FROM $table_name WHERE id = '1'"); //Get color
    $Face = $wpdb->get_var("SELECT face FROM $table_name WHERE id = '1'"); //Get Face
    $width = $wpdb->get_var("SELECT width FROM $table_name WHERE id = '1'"); //Get Width
    $CSS = $wpdb->get_var("SELECT css FROM $table_name WHERE id = '1'"); //Container Class
    $height = $wpdb->get_var("SELECT height FROM $table_name WHERE id = '1'"); //Get Heigh
	$HT = $wpdb->get_var("SELECT ht FROM $table_name WHERE id = '1'"); //Get Height Type
	
    $Home = $wpdb->get_var("SELECT home FROM $table_name WHERE id = '1'"); //Get Home Type
	$Page = $wpdb->get_var("SELECT page FROM $table_name WHERE id = '1'"); //Get Page Type
	$Post = $wpdb->get_var("SELECT post FROM $table_name WHERE id = '1'"); //Get Post Type
		
	//Options' Values
	$Values = array(
	 
	        '0'  => $AppID, //AppID
			'1'   => $Type, //Button Type
			'2'    => $Pos, //Position
			'3' => $Layout, //Layout
			'4'   => $Face, //Show Faces
			'5'   => $verb, //Verb to display
			'6'  => $color, //Button Color
			'7'  => $width, //Container Width
			'8' => $height, //Container Height
			'9'     => $HT, //Height Type px or em
			'10'    => $CSS, //Container CSS Class
			'11'   => $Home, //Show in home
			'12'   => $Page, //Show in pages
			'13'   => $Post  //show in posts
	
	               );
	$Names = array(
	 
	        '0'  => 'appid', //AppID
			'1'   => 'type', //Button Type
			'2'    => 'pos', //Position
			'3' => 'layout', //Layout
			'4'   => 'face', //Show Faces
			'5'   => 'verb', //Verb to display
			'6'  => 'color', //Button Color
			'7'  => 'width', //Container Width
			'8' => 'height', //Container Height
			'9'     => 'ht', //Height Type px or em
			'10' => 'css',
			'11'   => 'home', //Show in home
			'12'   => 'page', //Show in pages
			'13'   => 'post'  //show in posts
	
	               );
				   
	
		for($i = 0; $i <=13 ; $i++){
			
		update_option("fb_like_".$Names[$i], $Values[$i]);

	       }//End for Names
		
		$wpdb->query("DROP TABLE $table_name"); // Drop The Old Table
		
		}//End If
	 
 }

?>