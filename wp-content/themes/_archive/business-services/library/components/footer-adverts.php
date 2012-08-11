				<?php
$largead = get_option('dev_businessservices_largead_one');
$largead_link = get_option('dev_businessservices_largead_one_link');
$largead_title = get_option('dev_businessservices_largead_one_title');

$footerad = get_option('dev_businessservices_footad_one');
$footerad_link = get_option('dev_businessservices_footerad_one_link');
$footerad_title = get_option('dev_businessservices_footerad_one_title');

$footerad_two = get_option('dev_businessservices_footad_two');
$footerad_two_link = get_option('dev_businessservices_footerad_two_link');
$footerad_two_title = get_option('dev_businessservices_footerad_two_title');

$footerad_three = get_option('dev_businessservices_footad_three');
$footerad_three_link = get_option('dev_businessservices_footerad_three_link');
$footerad_three_title = get_option('dev_businessservices_footerad_three_title');

$advert_title = get_option('dev_businessservices_advert_title');

				?>
			
<div id="businessservices-banner">
<a href="<?php echo $largead_link ?>" title="<?php echo stripslashes($largead_title) ?>">
		<?php
		if ($largead == ""){
			?>
			<img src="<?php bloginfo('template_directory'); ?>/library/styles/colour-images/advert02.png" alt="<?php echo stripslashes($largead_title) ?>" /></a>
			<?php
		}
		else {
			?>
				<img src="<?php echo $largead ?>" alt="<?php echo stripslashes($largead_title) ?>" /></a>
			<?php
		}
		?>
			<?php if ( !is_user_logged_in() ) : ?>
			<?php

				locate_template( array( '/library/components/signup-box.php' ), true );

			?>				
						<?php endif; ?>
</div>
<div id="footer">
<div id="footer-inner">
<div id="footer-content">
<div class="ubox">
	<?php

	if ($advert_title == ""){
		$advert_title = "Set this title in theme options";
	}

	?>
<h3 class="purple"><?php echo stripslashes($advert_title); ?></h3>
<ul class="list">
<li>
<a href="<?php echo $footerad_link; ?>" title="<?php echo stripslashes($footerad_title); ?>">
	<?php
	if ($footerad == ""){
		?>
		<img src="<?php bloginfo('template_directory'); ?>/library/styles/colour-images/advert03.png" alt="<?php echo stripslashes($footerad_title); ?>" /></a>
		<?php
	}
	else {
		?>
			<img src="<?php echo stripslashes($footerad); ?>" alt="<?php echo stripslashes($footerad_title); ?>" alt="<?php echo stripslashes($footerad_title); ?>" /></a>
		<?php
	}
	?>
	<br />
<a href="<?php echo $footerad_two_link; ?>" title="<?php echo stripslashes($footerad_two_title); ?>">
	<?php
	if ($footerad_two == ""){
		?>
		<img src="<?php bloginfo('template_directory'); ?>/library/styles/colour-images/advert03.png" alt="<?php echo stripslashes($footerad_two_title); ?>" /></a>
		<?php
	}
	else {
		?>
			<img src="<?php echo stripslashes($footerad_two); ?>" alt="<?php echo stripslashes($footerad_two_title); ?>" alt="<?php echo stripslashes($footerad_two_title); ?>" /></a>
		<?php
	}
	?>
<br />
<a href="<?php echo $footerad_three_link; ?>" title="<?php echo stripslashes($footerad_three_title); ?>">
	<?php
	if ($footerad_three == ""){
		?>
		<img src="<?php bloginfo('template_directory'); ?>/library/styles/colour-images/advert03.png" alt="<?php echo stripslashes($footerad_three_title); ?>" /></a>
		<?php
	}
	else {
		?>
			<img src="<?php echo stripslashes($footerad_three); ?>" alt="<?php echo stripslashes($footerad_three_title); ?>" alt="<?php echo stripslashes($footerad_three_title); ?>" /></a>
		<?php
	}
	?>
</li>
</ul>
</div>