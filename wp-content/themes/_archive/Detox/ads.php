<?php 
	$ban1 = get_option('detox_banner1'); 
	$url1 = get_option('detox_url1'); 
	?>
<?php 
	$ban2 = get_option('detox_banner2'); 
	$url2 = get_option('detox_url2'); 
	?>
<div class="ads">
<a href="<?php echo ($url1); ?>" rel="bookmark" title="Visit the sponsors"><img src="<?php echo ($ban1); ?>" alt="ads" /></a>
<a href="<?php echo ($url2); ?>" rel="bookmark" title="Visit the sponsors"><img src="<?php echo ($ban2); ?>" alt="ads" /></a>

</div>