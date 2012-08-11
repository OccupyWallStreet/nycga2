<?php 
	$signupfeat_on = get_option('dev_businessfeature_signupfeat_on');
	$signupfeat_text = get_option('dev_businessfeature_signupfeat_text');
?>
<?php
if ($signupfeat_on != "no"){
?>
<div id="signup-section">	
		<p class="signup">
			<?php echo stripslashes($signupfeat_text); ?>
		</p>
		
		<?php signup_button(); ?>
		<div class="clear"></div>
</div>
<?php
}
?>