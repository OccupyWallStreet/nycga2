<?php
/** 

Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)

This file is for EXEMPLE usage ONLY of "extends" folder Api. Leave that #commented. liveTV Bundle plugin listed all php file in the "extends" folder. You must CREATE YOUR PERSONAL NEW PHP FILE in order to haven't disturbance when you make the update of the plugin. You must name your new php file with your desired name and put this file in "extends" folder. Nothing else, just view example to make your first hook in your personal php file.

**/

// disallow direct access to file
/*if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	wp_die(__('Sorry, but you cannot access this page directly.', 'livetv'));
}*/


//One exemple to make a 1st hook
/****

add_action( 'livetv_extends', 'your_personal_function_home'); //This part is required for all your personal action

function your_personal_function_home(){
	// Respects SSL
	if(is_home() || is_front_page()){
		wp_register_style( 'your_personal_style_for_home_page', plugins_url('css/widget_style_home_page.css', __FILE__) );
		wp_enqueue_style( 'your_personal_style_for_home_page' );
	}
}

****/


//Another exemple to make a 2nd hook
/****

add_action( 'livetv_extends', 'your_personal_function_single'); //You can create as many functions as you want 

function your_personal_function_single(){
	// Respects SSL
	if(is_single()){
		wp_register_style( 'your_personal_style_for_article', plugins_url('css/widget_style_article_page.css', __FILE__) );
		wp_enqueue_style( 'your_personal_style_for_article' );
	}
}

****/


//An exemple to add your personal extension menu tab in administration menu of the plugin
/****

function your_new_menu_to_extends_options()
{
	add_submenu_page('plugin-livetv-fork.php', 'Your personal options', __('Your personal options', 'livetv'), 'manage_options', 'extends/your-personal.php', 'your_personal_function_to_add_page');
}
add_action( 'livetv_add_submenu_page', 'your_new_menu_to_extends_options'); //plugin api necessary part

function your_personal_function_to_add_page(){
		//Your code here for administration options
	}
	
****/
?>