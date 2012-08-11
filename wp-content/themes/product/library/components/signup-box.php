<?php 
	$signupfeat_on = get_option('dev_product_signupfeat_on');
	$signupfeat_text = get_option('dev_product_signupfeat_text');
?>
<?php

if ($signupfeat_on != "no"){

?>
<div id="signup-section">
	
	<?php signup_button(); ?>
		<p class="signup">
			<?php echo stripslashes($signupfeat_text); ?>
		</p>
		<div class="clear"></div>
</div>
<?php
}
?>