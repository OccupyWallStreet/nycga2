<?php

/*Recommendations Admin Layout*/

include_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/rec_fill.php");

$block = '

<div class="wrap">

<script src="'. plugins_url('js/jquery.js',__FILE__).'" type = "text/javascript"></script>
<script src="'. plugins_url('js/prettyLoader.js',__FILE__).'" type = "text/javascript"></script>
<script src="'. plugins_url('js/rec.js',__FILE__).'" type = "text/javascript"></script>
<script src="'. plugins_url('js/facebox.js',__FILE__).'" type = "text/javascript"></script>

<link rel="stylesheet" href="'. plugins_url('css/rec_style.css',__FILE__).'" type="text/css" >
<link rel="stylesheet" href="'. plugins_url('css/facebox.css',__FILE__).'" type="text/css" >
<link rel="stylesheet" href="'. plugins_url('css/prettyLoader.css',__FILE__).'" type="text/css" >

  
<!-- <div id="SDK"></div> --!>
	<br>
	   <form id="form" action="" method="post">
	<table>
		<tr valign="top">
		  <td width="400px">
			<div class="settins">
			<img class="icon32" style="height:32px; width:auto;" src="'.plugins_url('images/settings_32.png',__FILE__).'" title="Facebook Like Button" alt="Icon"> 
			<h2>Recommendations Settings:</h2>
			<br>
			<table border="0">
			<tr>
				<td>App ID:</td>
				<td>
				<input type = "text" name = "app_id" id = "app_id" placeholder = "Facebook APP ID" size = "40" title="Facebook APP ID (Only required if you want to use XFBML)." value="'.$app_id_value.'">
				</td>
			  </tr>
			  <tr>
			  <tr>
			  <tr>
				<td>Connection Method: <br></td>
				<td>
				<span>XFBML:</span> <input type="radio" name="method" value="xfbml" id="xfbml" '.$check_xfbml.'>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span>iFrame:</span> <input type="radio" name="method" value="iframe" id="iframe" '.$check_iframe.'>
				</td>
			  </tr>
			  <tr>
			  <tr>
				<td>Domain:</td>
				<td>
				<input type = "text" name = "domain" id = "domain" placeholder = "Domain to Get Recommendations" size = "40" title="URL to Fetch Recommendations From, (Could be any URL in the world)." value="'.$domain_value.'">
				</td>
			  </tr>
			   <tr>
				<td>Widget\'s Title:</td>
				<td>
				<input type = "text" name = "wid_title" id = "wid_title" placeholder = "Widget\'s Title" size = "40" title="Sidebar Widget\'s Title" value="'.$widget_value.'">
				</td>
			  </tr>
			  <tr>
				<td>Width:</td>
				<td>
				<input type="text" name="width" id="width" placeholder = "Width" size = "5" title = "The width in pixels" value="'.$width_value.'">
				<span> px</span>
				</td>
			  </tr>
			  <tr>
				<td>Height:</td>
				<td><input type="text" name="height" id="height" placeholder = "Height" size = "5" title = "The height in pixels" 
				value="'.$height_value.'">
				<span> px</span>
				</td>
			  </tr>
			  <tr>
				<td>Header:</td>
				<td>
				<input type="checkbox" name = "header" id = "header" title = "Header is the top bar in the plugin which displays the word \'Recommendations\'" value="true" '.$check_header.'> 
				<span title="Header is the top bar in the plugin which displays the word \'Recommendations\'" >Check Show Header.</span>
				</td>
			  </tr>
			  <tr>
				<td>Color Scheme:</td>
				<td>
				  <select id = "layout" name = "layout" title="The Plugin\'s Style (Light or Dark).">
					<option  value="light" '.$sel_light.'>Light</option>
					<option  value="dark"  '.$sel_dark.'>Dark</option>
				  </select>
				</td>
			  </tr>
			  <tr>
				<td>Font:</td>
				<td>
				  <select id = "font" name = "font" title="The Plugin\'s General Font.">
					<option  value="arial" '.$sel_arial.'>arial</option>
					<option  value="lucida grande" '.$sel_lucida.'>lucida grande</option>
					<option  value="segoe ui" '.$sel_segoe.'>segoe ui</option>
					<option  value="tahoma" '.$sel_tahoma.'>tahoma</option>
					<option  value="trebuchet ms" '.$sel_trebuchet.'>trebuchet ms</option>
					<option  value="verdana" '.$sel_verdana.'>verdana</option>
				  </select>
				</td>
			  </tr>
			  <tr>
				<td>Border Color:</td>
				<td><input type = "text" name="border" id="border" placeholder="Color" size="10" title = "Border\'s Color Code or Name"
				value = "'.$border_value.'" ></td>
			  <tr>
			</table>
			<input name="submit" class="button-primary" type="submit" value="Save Settings" style="cursor: pointer;" id="submit" title="Save Settings">
			</div>
		  </td>
		  
		  <td width="400px">
			<div>
			<img class="icon32" style="height:32px; width:auto;" src="'.plugins_url('images/preview_32.png',__FILE__).'" 
			title="Facebook Like Button"  alt="Icon"> 
			<h2>Live Preview:</h2> <span>Turn off Live Preview ?</span> <input type="checkbox" id="close_live" value="true">
			   <div id="live_ref">
			   </form>
			   </div>
			  </div>
			
		  </td>
	</table>
	<br>
		<hr size="1" color="#CCCCCC">
<br>
	<span style="font-size:small;">P.S. After Saving if the settings appears like it didn\'t change, don\'t worry it changed but it\'s auto fill bug.</span>
	

</div>	

';


?>