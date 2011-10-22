<?php

//SDK File With Custom Channel File

$SDK = 

'
<div id="fb-root"></div>
<script>
FB.init({
  appId: "'.get_option("fb_like_appid").'",
  status: true, // check login status
  cookie: true, // enable cookies to allow the server to access the session
  xfbml: true, // parse XFBML
  channelUrl: "'.plugins_url().'/facebook-like-button/inc/channel.html"
});

document.getElementById("fb-root").appendChild(e);

</script>
';

?>