<?php

function update_rec_options(){
	
	$appid  = $_POST['app_id'];
	$method = $_POST['method'];
	$domain = $_POST['domain'];
	$width  = $_POST['width'];
	$height = $_POST['height'];
	$header = $_POST['header'];
	$layout = $_POST['layout'];
	$font   = $_POST['font'];
	$border = $_POST['border'];
	$wid_title = $_POST['wid_title'];
	
	$Values = array(
	
			  1 => $appid,
			  2 => $method,
			  3 => $domain,
			  4 => ($width == '') ? 300 : $width,
			  5 => ($height == '') ? 300 : $height,
			  6 => ($header != true) ? 'false' : 'true',
			  7 => $layout,
			  8 => $font,
			  9 => $border,
			  10=> $wid_title
	);
	
	$Names = array(
	
			1  => 'appid',
			2  => 'method',
			3  => 'domain',
			4  => 'width',
			5  => 'height',
			6  => 'header',
			7  => 'layout',
			8  => 'font',
			9  => 'border',
			10 => 'wid_title'
	 
	);
	

	
	if($_POST['submit']){	
		
		for($i=0; $i <= 10; $i++){
			
			update_option("fpp_rec_".$Names[$i], $Values[$i]);
			
		}
	}
		
}

?>