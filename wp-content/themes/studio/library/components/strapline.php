<?php
	$headeron = get_option('dev_studio_header_show');
	$headertitle = get_option('dev_studio_header_title');
	$headerlink = get_option('dev_studio_header_link');
	$headerurl = get_option('dev_studio_header_url');
	$headeralt = get_option('dev_studio_header_alt');
	$headerdescription = get_option('dev_studio_header_description');
	
	if (($headeron == "yes") || ($headeron == "")){
	/* function to check blanks */
	if ($headertitle == ""){
		$headertitle = "Set this page up in theme options";
	}
	if ($headerlink == ""){
		$headerlink = "Add your link";
	}
	if ($headerdescription == ""){
		$headerdescription = "Simply log into your admin panel then go to apperance > theme options and set up all your site settings.";
	}
?>
<!-- start site wide call to action section -->
<div id="strapline-wrapper"><!-- start #strapline-wrapper -->
	<div id="strapline-holder"><!-- start #strapline-holder -->
<div id="strapline"><!-- start #strapline -->
	<h1>
		<?php echo stripslashes($headertitle); ?>
	</h1>
	<a href="<?php echo $headerurl; ?>" title="<?php echo stripslashes($headeralt); ?>" class="button alignright"><?php echo stripslashes($headerlink); ?></a>
	<h2><?php echo stripslashes($headerdescription); ?></h2>
	<div class="clear"></div>
</div><!-- end #strapline -->
</div><!-- end #strapline-holder -->
</div><!-- end #strapline-wrapper  -->
<!-- end call to action section -->
<?php } ?>
