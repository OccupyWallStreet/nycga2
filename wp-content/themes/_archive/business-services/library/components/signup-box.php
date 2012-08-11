<?php 
	$signupfeat_on = get_option('dev_businessservices_signupfeat_on');
	$signupfeat_text = get_option('dev_businessservices_signupfeat_text');
?>
<?php
if ($signupfeat_on != "no"){
?>
<div id="signup-section">	
		<p class="signup">
			<?php signup_button(); ?>
			<?php echo stripslashes($signupfeat_text); ?>
		</p>
		<div class="clear"></div>
</div>
<?php
}
?>