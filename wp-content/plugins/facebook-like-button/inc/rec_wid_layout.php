<?php

/*Recommendations Widget Layout Code*/

class Widget_Layout
{
	public function Layout()
	
	{

		$Names = array(
		
				1  => 'appid',
				2  => 'method',
				3  => 'domain',
				4  => 'width',
				5  => 'height',
				6  => 'header',
				7  => 'layout',
				8  => 'font',
				9  => 'border'
		 
		);
	
		
		for($i=0; $i <= 9; $i++){
			
			$Values[$i] = get_option("fpp_rec_".$Names[$i]);
			
		} //end for
		
		
	    if($Values[2] == 'xfbml')
		
		{
			$Includes = '<script src="'. plugins_url('js/jquery.js',__FILE__).'" type = "text/javascript"></script>';
			$SDK = "
			    <script>
					  $(document).ready(function(){
					
					  var SDK  = '<div id=\"fb-root\"></div>';
					  SDK += '<script>';
					  SDK += 'window.fbAsyncInit = function() {';
					  SDK += 'FB.init({appId: ".$Values[1].", status: true, cookie: true, xfbml: true}); };';
					  SDK += '(function() {';
					  SDK += 'var e = document.createElement(\"script\"); e.async = true;';
					  SDK += 'e.src = document.location.protocol +';
					  SDK += '\"//connect.facebook.net/en_US/all.js\";';
					  SDK += 'document.getElementById(\"fb-root\").appendChild(e); }()); <\/script>';
					  
					  $(\"#SDK\").append(SDK);
				  
				    });
				</script>
			
			"; //end $SDK
			
			$Layout = 
			'
			<div id = "SDK"></div>
			<fb:recommendations 
			site="'.$Values[3].'" width="'.$Values[4].'" height="'.$Values[5].'" header="'.$Values[6].'" 
			colorscheme="'.$Values[7].'" font="'.$Values[8].'" border_color="'.$Values[9].'"></fb:recommendations>
			'
			;
			
			return $Includes . $SDK . $Layout;
			
			}//end if
			
			if($Values[2] == 'iframe')
			{
				$Layout = 
				
				'
				<iframe src="http://www.facebook.com/plugins/recommendations.php?
				site='.$Values[3].'&amp;width='.$Values[4].'&amp;height='.$Values[5].'&amp;header='.$Values[6].'&amp;
				colorscheme='.$Values[7].'&amp;font='.$Values[8].'&amp;border_color='.$Values[9].'" 
				scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$Values[4].'; height:'.$Values[5].'px;" 
				allowTransparency="true"></iframe>
				';
				
				return $Layout;
				
				}
	
		
		} //end function
	
	} //end class


?>