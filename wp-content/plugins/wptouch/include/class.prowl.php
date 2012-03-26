<?php

class Prowl
{
   var $apikey;
   var $application;
   
   function Prowl($apikey, $application)
   {
      $this->apikey = $apikey;
      $this->application = $application;
     // $this->verify();
   }
   
   function add($priority, $event, $description)
   {
      $options = array(
         'apikey' => $this->apikey,
         'priority' => $priority,
         'application' => urlencode($this->application),
         'event' => urlencode($event),
         'description' => urlencode($description)
      );
      
      $response = $this->request('https://prowl.weks.net/publicapi/add', $options);
      return $this->getresult($response);
   }
   
   function getresult($response) {
		$response = str_replace("\n", " ", $response);
	
		if(preg_match("/code=\"200\"/i", $response))
			return true;
		else
		{
			preg_match("/<error.*?>(.*?)<\/error>/i", $response, $out);
			return $out[1];
		}
   }
   
   function verify()
   {
      $options = array('apikey' => $this->apikey);
      return $this->getresult( $this->request('https://prowl.weks.net/publicapi/verify', $options) );
   }
   
   function request($file, $options)
   {
      $url = $file;
      
      $first = true;
      foreach ($options as $key => $value) {
         $url .= ($first ? '?' : '&') . $key . '=' . $value;
         $first = false;
      }
      
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      $response = curl_exec($ch);
      curl_close($ch);

      return $response;
   }
}