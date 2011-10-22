<?php

 function ActivationRobot($url,$ref="")
    {
        if((function_exists("curl_init")) && ($url != "")){
            $ch = curl_init();
            $user_agent = "ZingySo FBLike Button Activation Robot";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION , 1 );
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION , 1 );
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_REFERER, $ref);
            curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
            $html = curl_exec($ch);
            curl_close($ch);
        }
      
        return true;
    }
	
   function Activate(){
	   
		add_option("fb_like_activate", 'false');
		$state = get_option("fb_like_activate");
		
		if($state == 'false'){
			
		$uri = "http://zingyso.com/ext/fbactive.php?url=".get_bloginfo("url")."";
			
			}else{
				
				$uri = "";
				
				}
				
		update_option('fb_like_activate', 'true');	
			
		
		
		return $uri;
		
		
		}
		

	
	

?>