<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">
	
</div>

<div id="content">	
	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
	
	<div class="left-menu">
		<?php load_template( TEMPLATEPATH . '/groups/group-menu.php' ) ?>
	</div>

	<div class="main-column">
		<div class="inner-tube">

			<div id="group-name">
				<h1><a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a></h1>
				<p class="status"><?php bp_group_type() ?></p>
			</div>

			<div class="info-group">
				<h4><?php _e( 'Confirm Leave Group', 'buddypress' ); ?></h4>
				<h3><?php _e( 'Are you sure you want to leave this group?', 'buddypress' ); ?></h3>
	
				<p>
					<a href="<?php bp_group_leave_confirm_link() ?>"><?php _e( "Yes, I'd like to leave this group.", 'buddypress' ) ?></a> | 
					<a href="<?php bp_group_leave_reject_link() ?>"><?php _e( "No, I'll stay!", 'buddypress' ) ?></a>
				</p>
			</div>
		
		</div>
	</div>
	
	<?php endwhile; endif; ?>
</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>