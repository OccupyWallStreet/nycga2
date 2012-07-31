<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">

</div>

<div id="content">
	<h2><?php _e( 'Create a Blog', 'buddypress' ) ?></h2>
	<?php do_action( 'template_notices' ) // (error/success feedback) ?>

	<?php if ( bp_blog_signup_enabled() ) : ?>
		
		<?php bp_show_blog_signup_form() ?>
	
	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'Blog registration is currently disabled', 'buddypress' ); ?></p>
		</div>

	<?php endif; ?>

</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>

