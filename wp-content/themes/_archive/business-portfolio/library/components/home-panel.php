<div id="main-wrap">
<div id="main-container">
<div id="main-content">
	<?php
		$home_title = get_option('dev_businessportfolio_home_title');
		$home_description = get_option('dev_businessportfolio_home_description');
		$home_link = get_option('dev_businessportfolio_home_link');
		$home_link_title = get_option('dev_businessportfolio_home_link_title');
		
		$home_image = get_option('dev_businessportfolio_home_image');
		$home_image_link = get_option('dev_businessportfolio_home_image_link');
		$home_image_title = get_option('dev_businessportfolio_home_image_link_title');
	?>
<div id="left-content">
			<?php
				if ($home_title == ""){
					$home_title = "Add your own title under options";
				}
			?>
	<h1><?php echo stripslashes($home_title); ?></h1>
	<p>
			<?php
				if ($home_description == ""){
					$home_description = "Add your own description under options";
				}
			?>
		<?php echo stripslashes($home_description); ?>
	</p>
<div id="connect">
	<a href="<?php echo $home_link; ?>" title="	<?php echo stripslashes($home_link_title); ?>" class="button">
			<?php
				if ($home_link_title == ""){
					$home_link_title = "Add your own link under options";
				}
			?>
	<?php echo stripslashes($home_link_title); ?></a>
</div>
</div>
<div id="right-content">
<div id="slider-content">
<div id="featured-slider">
<div class="contentdiv">
<div class="feat-main">
<a title="<?php echo stripslashes($home_image_title); ?>" href="<?php echo $home_image_link; ?>">
		<div style="background: url(<?php echo $home_image; ?>) no-repeat center center;  width: 320px; height: 230px; border: 10px #ffffff solid; margin-top: 10px;"></div>
</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>