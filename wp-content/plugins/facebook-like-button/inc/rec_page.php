<?php

function Rec_Page(){
	
	include_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/rec_layout.php");
	
	if($_POST['submit']){
	   include_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/rec_save.php");

	   update_rec_options();
	   include_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/rec_fill.php");
	   ?>
       <div id="message" class="updated fade"><p><strong><?php _e('Settings saved!') ?></strong></p></div>
	   <?php
	   
	   
	}

	echo $block;
	
	}
	
	
function Add_Rec_Admin(){

	add_submenu_page("main.php", 'Recommendations Settings', 'Recommendations', 8, basename(__file__),
        "Rec_Page");
	
	}
	
	


?>