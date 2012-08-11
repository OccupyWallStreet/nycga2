<?php 
	$signupfeat_on = get_option('dev_studio_signupfeat_on');
	$signupfeat_text = get_option('dev_studio_signupfeat_text');
?>
<?php
if ($signupfeat_on == "yes"){

?>
<!-- start sign up box -->
<div id="signup-wrapper"><!-- start #signup-wrapper -->
<div id="signup-section"><!-- start #signup-section -->
	<div id="signup-about"><!-- start #signup-about -->
	<?php signup_button(); ?>
		<p class="signup">
			<?php echo stripslashes($signupfeat_text); ?>
		</p>
		<div class="clear"></div>
	</div><!-- end #signup-about -->
</div><!-- end #signup-section -->
</div><!-- end #signup-wrapper -->
<!-- end sign up box -->
<?php
}
?>