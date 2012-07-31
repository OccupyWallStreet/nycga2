<?php get_header() ?>

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

<?php get_footer() ?>