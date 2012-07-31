<div id="group-loop">
<?php if ( bp_has_groups() ) : ?>
	<div class="pagination-links" id="<?php bp_group_pag_id() ?>">
		<?php bp_group_pagination() ?>
	</div>
	
	<ul id="group-list" class="item-list">
	<?php while ( bp_groups() ) : bp_the_group(); ?>
		<li>
			<?php bp_group_avatar_thumb() ?>
			<h4><a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a><span class="small"> - <?php printf( __( '%s members', 'buddypress' ), bp_group_total_members( false ) ) ?></span></h4>
			
			<?php if ( bp_group_has_requested_membership() ) : ?>
				<p class="request-pending"><?php _e( 'Membership Pending Approval', 'buddypress' ); ?></p>
			<?php endif; ?>
			
			<p class="desc">
				<?php bp_group_description_excerpt() ?>
			</p>
		</li>
	<?php endwhile; ?>
	</ul>
<?php else: ?>

	<?php if ( bp_group_show_no_groups_message() ) : ?>
		
		<div id="message" class="info">
			<p><?php bp_word_or_name( __( "You haven't joined any groups yet.", 'buddypress' ), __( "%s hasn't joined any groups yet.", 'buddypress' ) ) ?></p>
		</div>
	
		<?php if ( bp_is_home() ) : ?>
			<h3><?php _e( 'Why not join a few of these groups?', 'buddypress') ?></h3>
			<?php bp_groups_random_selection() ?>
		<?php endif; ?>
	
	<?php else: ?>
		
		<div id="message" class="error">
			<p><?php _e( "No matching groups found.", 'buddypress' ) ?></p>
		</div>
	
	<?php endif; ?>

<?php endif;?>
</div>