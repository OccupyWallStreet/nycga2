<html>
<head>
<style type="text/css">
<!--
body {
	font: 13px Arial, Helvetica, sans-serif;
	padding: 10px;
	overflow: hidden;
}
.txtBox{
	font-size: 20px;
	padding: 5px;
	width: 25%;
}
p {
	font-size: 12px;
}
#close{
	background: #dfdfdf;
	height: 21px;
	width: 15px;
	top: -5px;
	right: -5px;
	position: absolute;
	padding: 8px 5px 1px 10px;
	-webkit-border-radius: 35px;
	-moz-border-radius: 35px;
	border-radius: 35px;
	cursor: pointer;
}
-->
</style>

</head>
<body>
<?php $id = intval($_GET['i']); ?>

<div id="close" onClick="parent.sc_closeiframe();">x</div>

<?php if ($id == 1): ?>
<div id="donate">
<h3>Donate and support this plugin !</h3>
<p>If you like this plugin, then just make a small donation for this plugin.</p>
<p style="font-size:10px">This plugin is free with premium features, so your donation will encourage me to work on the plugin to add more features.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"> 
<input name="cmd" value="_donations" type="hidden"> 
<input name="business" value="donations@aakashweb.com" type="hidden">$&nbsp; 
<input type="number" name="amount" class="txtBox" value="20">&nbsp; 
<input align="right" src="../images/paypal.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" type="image" border="0">
<input name="item_name" value="Donation for Shortcoder plugin" type="hidden"> 
<input name="no_shipping" value="0" type="hidden"> 
<input name="no_note" value="1" type="hidden"> 
<input name="currency_code" value="USD" type="hidden"> 
<input name="tax" value="0" type="hidden"> 
<input name="lc" value="US" type="hidden"> 
<input name="bn" value="PP-DonationsBF" type="hidden">
</form>
</div>
<?php endif; ?>

<?php if ($id == 2): ?>
<div id="share">
	<h3>Share this plugin !!</h3>
	<div class="addthis_toolbox addthis_default_style">
		<a class="addthis_button_facebook_like" fb:like:layout="button_count" fb:like:href="http://facebook.com/aakashweb"></a>
		<a class="addthis_button_google_plusone" g:plusone:size="medium" g:plusone:href="http://www.aakashweb.com"></a>
		<a class="addthis_button_tweet" tw:count="horizontal" tw:url="http://www.aakashweb.com/wordpress-plugins/shortcoder/" tw:text="Shortcoder - Awesome plugin for creating custom Shortcodes in Wordpress !"></a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=vaakash"></script>
</div>
<?php endif; ?>

</body>
</html>