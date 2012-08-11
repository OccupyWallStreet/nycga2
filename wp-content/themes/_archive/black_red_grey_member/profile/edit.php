<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">
	
	<ul class="content-header-nav">
		<?php bp_profile_group_tabs(); ?>
	</ul>
	
</div>

<div id="content">
	
	<h2><?php printf( __( "Editing '%s'", "buddypress" ), bp_profile_group_name(false) ); ?></h2>
	
	<?php bp_edit_profile_form() ?>

</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>