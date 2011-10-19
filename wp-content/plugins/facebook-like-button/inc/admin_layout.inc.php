<?php

/*
File Name: admin_layout.inc.php
Descrption: Form the admin layout. 
*/



 $Layout = '
 
            
			 
	          <div class="wrap">
			  <div class="has-sidebar">					
			
              <form action = "" method = "POST" id="form">
	          <h2>Facebook Like Button</h2>
			  <h3>Live Preview</h3>
			  <span id="live"></span>
			  <div class="has-sidebar-content" id="post-body-content">
			<h3 class="hndle"><span>General SettingsSettings</span></h3>
		  <table>
			  <tr>
				  <td>
				  App ID: 
				  </td>
				  <td>
				  <input type="text" name="appid" size="30" value = "' . $Value['appid'] .
        '"/> <a href="http://developers.facebook.com/setup/" target="_blank" title="Register Your Site on Facebook">Don&rsquo;t have one?</a>
				  </td>
			  </tr>
			  
			  <tr>
				  <td>
				  Type: 
				  </td>
			  </tr>
			  <tr>
				  <td><label for="xfbml">XFBML:</label><input type="radio" id="xfbml" name="type" value="xfbml" ' .
        $xfbml . '/></td>
				  <td><label for="iframe">iFrame:</label><input type="radio" id="iframe" name="type" value="iframe" ' .
        $iframe . '/></td>
			  </tr>
			  	  <tr>
			  <td>Show in Home:</td><td><input type = "checkbox" name = "home" value = "true" '.$home.'></td>
			  </tr>
			  <tr>
			  <td>Show in Pages:</td><td><input type = "checkbox" name = "page" value = "true" '.$page.'></td>
			  </tr>
			  <tr>
			  <td>Show in Posts:</td><td><input type = "checkbox" name = "post" value = "true" '.$post.'></td>
			  </tr>
              <tr>
              <td>
              Position:
              </td>
              <td>
              <select name = "pos" value="$pos" id="pos">
              <option value = "after" ' . $after . '>
              After Content
              </option>
              <option value = "before" ' . $before . '>
              Before Content
              </option>
              <option value = "baf" ' . $baf . '>
              Before and after content
              </option>
              </select>
              </td>
              </tr>
              <tr><td><label for="fblikes_locale">Language:</label></td>
                  <td><select name="fblikes_locale" id="fblikes_locale">';
if (get_option("fblikes_locale") == "default") {
    $Layout .= '<option value="default" selected="selected">Default</option>';
} else {
    $Layout .= '<option value="default">Default</option>';
}

$fblikes_locales = fblikes_get_locales();
$selectedLocale = get_option("fblikes_locale");
foreach($fblikes_locales as $locale => $language) {
    if ($locale == $selectedLocale) {
        $Layout .= '<option value="' . htmlentities($locale) .'" selected="selected">'. htmlentities($language) .'</option>';
    } else {
        $Layout .= '<option value="' . htmlentities($locale) .'">'. htmlentities($language) .'</option>';
    }
}

$Layout = '
  <style>
				.layout{
					
					float: right;
					position:absolute;
					padding: 1em;
					top:210px;
					right:50px;
					border: 1px solid #CCC;
					border-radius: 5px;

				   -moz-border-radius: 5px;
				
				   -webkit-border-radius: 5px;
				   -webkit-box-shadow: 1px 1px 10px #939393;
	               -moz-box-shadow: 1px 1px 10px #939393;
	               box-shadow: 1px 1px 10px #939393;
					
					
				}
				
				.layout input[type=submit]{
				
				cursor:pointer !important
					
				}
				.settings input[type=submit]{
				
				cursor:pointer !important
					
				}
				.settings{
					padding: 1em;
					float: left;
					position:absolute;
					top:210px;
					border: 1px solid #CCC;
					
					border-radius: 5px;

				   -moz-border-radius: 5px;
				
				   -webkit-border-radius: 5px;
				   -webkit-box-shadow: 1px 1px 10px #939393;
	               -moz-box-shadow: 1px 1px 10px #939393;
	               box-shadow: 1px 1px 10px #939393;
				   z-index:1000;
				}
				
				.api{
					padding: 5px;
					float: right;
					position:absolute;
					top:140px;
					right: 50px;
					background: #daffd9;
					border: 1px solid #CCC;
					
					border-radius: 5px;

				   -moz-border-radius: 5px;
				
				   -webkit-border-radius: 5px;
				   -webkit-box-shadow: 1px 1px 10px #939393;
	               -moz-box-shadow: 1px 1px 10px #939393;
	               box-shadow: 1px 1px 10px #939393;	
				}
             </style>
			 <script>
			 
$(document).ready(function(){
	
			if($("#enimg").is(":checked")){
			
			$("#dimage").attr("disabled", false);

			}else{
				
			   $("#dimage").attr("disabled", true);
			    $("#dimage").css("color", "#3d3d3d");
				
		   }
	
	$("#enimg").click(function(){
		
		if($("#enimg").is(":checked")){
			
			$("#dimage").attr("disabled", false);
			 $("#dimage").css("color", "black");

			}else{
				
			   $("#dimage").attr("disabled", true);
			    $("#dimage").css("color", "#3d3d3d");
				
		   }

		
		});	
	
	
	});
			 </script>
	          <div class="wrap">
			  	          <div class="icon32" id="icon-options-general"><br/></div><h2>Facebook Like Button Settings</h2>

			    <div class="metabox-holder has-right-sidebar" id="poststuff">
	
		<div class="inner-sidebar">
			<div style="position: relative;" class="meta-box-sortabless ui-sortable" id="side-sortables">
	
			<div class="postbox">
				<h3 class="hndle"><span>About Facebook Like Button</span></h3>
				<div class="inside">
				<p>
				<a href="http://blog.ahmedgeek.com/facebook-like-button-for-wordpress-v4"  target = "_blank"><b>Plugin Homepage</b></a><br /><br />
				<b>Facebook</b> : <a href="http://www.facebook.com/pages/Ahmed-The-Geek/164004377590?ref=ts" target = "_blank">Developer\'s Page</a><br />
				<b>Twitter</b> : <a href="http://twitter.com/valodes"  target = "_blank">@valodes</a><br />
				</p>
				
				
				</div><!--/inside-->
			</div><!--/postbox-->
			
			
			
			<div class="postbox">
		<h3 class="hndle"><span>Donate with Moneybookers</span></h3>
		<div class="inside">
			<p align="">
			
			<form action="https://www.moneybookers.com/app/payment.pl" method="post" target="_blank">
			<input type="hidden" name="pay_to_email" value="me@ahmedgeek.com">
			Please enter the amount you would like to give<br>
			<input type="hidden" name="return_url" value="http://www.blog.ahmedgeek.com/thanks">
			<input type="hidden" name="language" value="EN">
			<table>
			<tr>
			<td>Currency:</td>
			<td>
			<select name="currency" size="1">
			<option>Select a currency</option>
			<option value="USD">US dollar</option>
			<option value="GBP">GB pound</option>
			<option value="EUR">Euro</option>
			<option value="JPY">Yen</option>
			<option value="CAD">Canadian $</option>
			<option value="AUD">Australian $</option>
			</select>
			</td>
			</tr>
			<tr>
			<td>Amount:</td>
			<!–<input type="hidden" name="amount" value="5.00">–>
			<td><input type="text" name="amount" value="5.00" size="10"></td>
			<input type="hidden" name="detail1_description" value="Donation To Help Facebook Like Button for WP">
			
			<input type="hidden" name="detail1_text" value="Donation To Help Facebook Like Button for WP">
			<br>
			<br>
			</tr>
			<tr>
			<td><input type="submit" alt="Donate" value="Donate!" /></td>
			</tr>
			</table>
</form>
			</p>
		
		</div>
	</div>
	
	<div class="postbox">
				<h3 class="hndle"><span>My Other Plugins</span></h3>
				<div class="inside">
				<p>
				<b><a href = "http://wordpress.org/extend/plugins/twitter-tweet-button/" target = "_blank">Twitter Tweet Button</a></b>
				<br>
				<b><a href = "http://wordpress.org/extend/plugins/geo-location-comments/"  target = "_blank">Geo-Location</a></b>
				</p>
				
				
				</div><!--/inside-->
			</div><!--/postbox-->
	
			</div>
		</div>
		
              <form action = "" method = "POST" id="form">

			 
			
			 <div class="has-sidebar-content" id="post-body-content">
			 
			 <div class="postbox" style="height:100px">
				<h3 class="hndle"><span>Live Preview</span></h3>
				<div class="inside">
			 <div style="z-index:0;" id="live" style="height:60px;"></div>
			 </div>
			 </div>

			<div class="postbox">
				<h3 class="hndle"><span>General Settings</span></h3>
				<div class="inside">
			  
		  <table>
			  <tr>
				  <td>
				  AppID for XFBML version: 
				  </td>
				  <td>
				  <input type="text" name="appid" size="30" value = "' . $Value['appid'] .
        '"/> <a href="http://developers.facebook.com/setup/" target="_blank" title="Register Your Site on Facebook">Don&rsquo;t have one?</a>
				  </td>
			  </tr>
			  
			  <tr>
				  <td>
				  Type: 
				  </td>
			  </tr>
			  <tr>
				  <td><label for="xfbml">XFBML:</label><input type="radio" id="xfbml" name="type" value="xfbml" ' .
        $xfbml . '/></td>
				  <td><label for="iframe">iFrame:</label><input type="radio" id="iframe" name="type" value="iframe" ' .
        $iframe . '/></td>
			  </tr>
			  	  <tr>
			  <td>Show in Home:</td><td><input type = "checkbox" name = "home" value = "true" '.$home.'></td>
			  </tr>
			  <tr>
			  <td>Show in Pages:</td><td><input type = "checkbox" name = "page" value = "true" '.$page.'></td>
			  </tr>
			  <tr>
			  <td>Show in Posts:</td><td><input type = "checkbox" name = "post" value = "true" '.$post.'></td>
			  </tr>
			   <tr>
			  <td>Show in Categories:</td><td><input type = "checkbox" name = "cat" value = "true" '.$cat.'></td>
			  </tr>
			   <tr>
			  <td>Show in Archive:</td><td><input type = "checkbox" name = "arch" value = "true" '.$arch.'></td>
			  </tr>
              <tr>
              <td>
              Position:
              </td>
              <td>
              <select name = "pos" value="$pos" id="pos">
              <option value = "after" ' . $after . '>
              After Content
              </option>
              <option value = "before" ' . $before . '>
              Before Content
              </option>
              <option value = "baf" ' . $baf . '>
              Before and after content
              </option>
			   </option>
              <option value = "man" ' . $man . '>
              Manually
              </option>
              </select>
              </td>
              </tr>
			  <tr>
              <td>
              Alignment:
              </td>
              <td>
			  <select name="align">
			   <option value="left" '.$left.'>Left</option>
			   <option value="right" '.$right.'>Right</option>
			   <option value="" '.$no_float.'>None</option>
			  </select>
			  </td>
              </tr>
			  <tr>
              <td>
              Admin ID:
              </td>
              <td>
                    <input type="text" size="30" name="admeta" value="'.get_option("fb_like_admeta").'" /><i><font size = "-2" color = "red">App ID Required!</font></i> <a href = "http://www.facebook.com/insights/" target = "_blank">View Insights</a>
              </td>
              </tr>
			  
			  	  <tr>
              <td>
              Defualt Image:
              </td>
              <td>
			  <input type="text" id="dimage" size="30" name="dimage" value="'.get_option("fb_like_dimage").'" disabled="'.$enable_image.'" /> <span>Check to enable:</span><input type="checkbox" name="enimg" id="enimg" value = "true" '.$check_image.'>
			  </td>
              </tr>
			  
			  <tr>
              <td>
              Enable Send Button:
              </td>
              <td>
			    <input type="checkbox" id="send" name="send" value="true" '.$send.'/> <font color="red" size="-2"> XFBML Only</font>
			  </td>
              </tr>
              <tr>
               <td>
              Enabled Social Tracking:
              </td>
              <td>
			    <input type="checkbox" id="send" name="social" value="true" '.$social.'/> <font color="red" size="-2"> This Will Enable TabPress Social Tracking</font>
			  </td>
              </tr>
			  
			  
              <tr><td><label for="fblikes_locale">Language:</label></td>
                  <td><select name="fblikes_locale" id="fblikes_locale">';
if (get_option("fblikes_locale") == "default") {
    $Layout .= '<option value="default" selected="selected">Default</option>';
} else {
    $Layout .= '<option value="default">Default</option>';
}

$fblikes_locales = fblikes_get_locales();
$selectedLocale = get_option("fblikes_locale");
foreach($fblikes_locales as $fblikes_locale => $fblikes_language) {
    if ($fblikes_locale == $selectedLocale) {
        $Layout .= '<option value="' . htmlentities($fblikes_locale) .'" selected="selected">'. htmlentities($fblikes_language) .'</option>';
    } else {
        $Layout .= '<option value="' . htmlentities($fblikes_locale) .'">'. htmlentities($fblikes_language) .'</option>';
    }
}

$Layout .= '
                      </select></td>
              </tr>
			  <tr>
			<td>
			<input type = "submit" name = "sub" value = "Save Settings">
            </td>
			</tr>
		  </table>
		  </div>
		  </div>
		  </div>
		  
		  <div class="postbox">
				<h3 class="hndle"><span>Layout Settings:</span></h3>
				<div class="inside">
		  <table width="400px" border="0">
			<tr>
			  <td>Layout Style:</td>
			  <td>
			  <select name="layout" id="layout">
			  <option value="standard" ' . $stan . '>Standard</option>
			  <option value="button_count" ' . $count . '>Count Button</option>
			   <option value="box_count" ' . $box_c . '>Count Box</option>
			  </select>
			  </td>
			</tr>
			<tr>
			  <td>Show Faces:</td>
			  <td><input type="checkbox" id="face" name="face" value="true" ' . $face . '/>
			</tr>
			<tr>
			  <td>Verb to display:</td>
			  <td>
			  <select name="verb" id="verb">
			  <option value="like" ' . $like . '>Like</option>
			  <option value="recommend" ' . $reco . '>Recommend</option>
			  </select>
			  </td>
			</tr>
			<tr><td><label for="fblikes_font">Font:</label></td>
                            <td><select name="fblikes_font" id="fblikes_font">';
$selectedFont = get_option("fblikes_font");
foreach ($fblikes_fonts as $fontName => $font) {
    if ($font == $selectedFont) {
        $Layout .= '<option value="'. $font .'" selected=selected>'. $fontName .'</option>';
    } else {
        $Layout .= '<option value="'. $font .'">'. $fontName .'</option>';
    }
}

$Layout .= '
                                </select></td>
                        </tr>
			<tr>
			  <td>Color Scheme:</td>
			  <td>
			  <select name="color" id="color">
			  <option value="light" ' . $light . '>Light</option>
			  <option value="dark" ' . $dark . '>Dark</option>
			  </select>
			  </td>
			</tr>
            	<tr>
			  <td>Width:</td>
			  <td>
			  <input type = "text" name = "width" value = "' . $Value['width'] . '"  id="width"/>
			  </td>
			</tr>
            <tr>
			  <td>Height:</td>
			  <td>
			  <input type = "number" name = "height" value = "' . $Value['height'] . '" id="height" />
			  </td>
              <td>
              <select name = "ht" id="ht">
              <option value = "px" ' . $px . '>px</option>
              <option value = "em" ' . $em . '>em</option>
              </selecte>
              </td>
              <td style = "width: 100px;"><div><i>Default is 40px!</i></div></td>
			</tr>
            	<tr>
			  <td>Container Class:</td>
			  <td>
			  <input type = "text" name = "css" value = "' . $Value['css'] . '" id="css" />
			  </td>
			</tr>
			<tr>
			<td>
			<input type = "submit" name = "sub" value = "Save Settings">
            </td>
			</tr>
		  </table>
		  </div>
		  </div>
		 
		  
		  <br>
		  
		  
         
		</form>	
		<br>
		</div>



	
	
	';
	

	


?>