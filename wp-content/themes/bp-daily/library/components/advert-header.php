
	<?php 
		$advert = get_option('dev_buddydaily_header_advert');		
		$advert_title = get_option('dev_buddydaily_header_advert_title');
		$advert_link = get_option('dev_buddydaily_header_advert_link');
		$advert_code = get_option('dev_buddydaily_header_advert_code');
	?>
	<?php
	if ($advert != "" && $advert_link == ""){
	?>
	<div class="top-advert-block">
		<img src="<?php echo $advert; ?>" alt="<?php bloginfo('name'); ?>"/>
	</div>
	<?php
	}
	else if ($advert != "" && $advert_link != ""){
		?>
		<div class="top-advert-block">
			<a href="<?php echo $advert_link ?>" title="<?php echo stripslashes($advert_title); ?>"><img src="<?php echo $advert; ?>" alt="<?php echo stripslashes($advert_title); ?>"></a>
		</div>
		<?php }
		else if ($advert_code != ""){
			?>
			<div class="top-advert-block">
				<?php echo stripslashes($advert_code); ?>
			</div>
				<?php
			}
			else {
				
			}
			?>