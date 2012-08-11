<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">
	<ul class="content-header-nav">
		<?php bp_group_creation_tabs(); ?>
	</ul>
</div>

<div id="content">	
	<h2><?php _e( 'Create a Group', 'buddypress' ) ?> <?php bp_group_creation_stage_title() ?></h2>
	<?php do_action( 'template_notices' ) // (error/success feedback) ?>
	
	<?php bp_group_create_form() ?>
	
</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>