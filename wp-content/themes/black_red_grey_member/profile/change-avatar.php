<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">

</div>

<div id="content">
	
	<h2><?php _e( 'Change Avatar', 'buddypress' ) ?></h2>
	
	<?php bp_avatar_upload_form() ?>

</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>