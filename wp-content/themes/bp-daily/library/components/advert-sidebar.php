
<?php 
$wide_advert = get_option('dev_buddydaily_wide_advert');		
$wide_advert_title = get_option('dev_buddydaily_wide_advert_title');
$wide_advert_link = get_option('dev_buddydaily_wide_advert_link');
$wide_advert_code = get_option('dev_buddydaily_wide_advert_code');
$left_advert = get_option('dev_buddydaily_sideleft_advert');		
$left_advert_title = get_option('dev_buddydaily_sideleft_advert_title');
$left_advert_link = get_option('dev_buddydaily_sideleft_advert_link');
$left_advert_code = get_option('dev_buddydaily_sideleft_advert_code');
$right_advert = get_option('dev_buddydaily_sideright_advert');		
$right_advert_title = get_option('dev_buddydaily_sideright_advert_title');
$right_advert_link = get_option('dev_buddydaily_sideright_advert_link');
$right_advert_code = get_option('dev_buddydaily_sideright_advert_code');
?>
	<div class="advert-columns">
<?php
if ($wide_advert != "" && $wide_advert_link == ""){
?>
<div class="advert">
	<img src="<?php echo $wide_advert; ?>" alt="<?php bloginfo('name'); ?>"/>
</div>
<?php
}
else if ($wide_advert != "" && $wide_advert_link != ""){
	?>
	<div class="advert">
			<a href="<?php echo $wide_advert_link ?>" title="<?php echo stripslashes($wide_advert_title); ?>"><img src="<?php echo $wide_advert; ?>" alt="<?php echo stripslashes($wide_advert_title); ?>"></a>
	</div>
	<?php }
	else if ($wide_advert_code != ""){
		?>
		<div class="advert">
			<?php echo stripslashes($wide_advert_code); ?>
		</div>
			<?php
		}
		else {
			
		}
		?>

		<?php
		if ($left_advert != "" && $left_advert_link == ""){
		?>
			<div class="advert-left">
			<img src="<?php echo $left_advert; ?>" alt="<?php bloginfo('name'); ?>"/>
		</div>
		<?php
		}
		else if ($left_advert != "" && $left_advert_link != ""){
			?>
				<div class="advert-left">
					<a href="<?php echo $left_advert_link ?>" title="<?php echo stripslashes($left_advert_title); ?>"><img src="<?php echo $left_advert; ?>" alt="<?php echo stripslashes($left_advert_title); ?>"></a>
			</div>
			<?php }
			else if ($left_advert_code != ""){
				?>
						<div class="advert-left">
					<?php echo stripslashes($left_advert_code); ?>
				</div>			
					<?php
				}
				else {

				}
				?>
					<?php
					if ($right_advert != "" && $right_advert_link == ""){
					?>
						<div class="advert-right">
						<img src="<?php echo $right_advert; ?>" alt="<?php bloginfo('name'); ?>"/>
					</div>			
					<?php
					}
					else if ($right_advert != "" && $right_advert_link != ""){
						?>
							<div class="advert-right">
								<a href="<?php echo $right_advert_link ?>" title="<?php echo stripslashes($right_advert_title); ?>"><img src="<?php echo $right_advert; ?>" alt="<?php echo stripslashes($right_advert_title); ?>"></a>
						</div>
						<?php }
						else if ($right_advert_code != ""){
							?>
									<div class="advert-right">
								<?php echo stripslashes($right_advert_code); ?>
							</div>			
								<?php
							}
							else {

							}
							?>
			<div class="clear"></div>
	</div>