<?php
/*
 * Use the key and secret to generate an auth_token, just to test if they're valid.
 * If so, return a Facebook API instance.  Otherwise, return null.
 * This is in a separate file because FB's php5 library uses exceptions, which I can't include in the main plugin with PHP4
 */

define('WPFBAUTOCONNECT_API', 1);

function jfb_validate_key($key, $secret)
{
      require_once('php/facebook.php');
      $facebook = new Facebook($key, $secret, null, true);
      $facebook->api_client->session_key = 0;
      try
      {
         $token = $facebook->api_client->auth_createToken();
         return $facebook;
      }
      catch(Exception $e)
      {
          return null;
      }
}

?>