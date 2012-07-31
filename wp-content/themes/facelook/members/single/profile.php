<?php if ( bp_is_my_profile() ) : ?>
	<div class="item-list-tabs no-ajax" id="subnav">
		<ul>
			<?php bp_get_options_nav() ?>
		</ul>
	</div><!-- .item-list-tabs -->
<?php endif; ?>

<?php do_action( 'bp_before_profile_content' ) ?>

<div class="profile">
	<?php if ( 'edit' == bp_current_action() ) : ?>
		<?php locate_template( array( 'members/single/profile/edit.php' ), true ) ?>

	<?php elseif ( 'change-avatar' == bp_current_action() ) : ?>
		<?php locate_template( array( 'members/single/profile/change-avatar.php' ), true ) ?>

	<?php else : ?>
		<?php query_posts( 'name=' . bp_get_displayed_user_username() . '-profile' ); ?>
		<?php if ( have_posts() ) : ?>
			<div id="extended-profile"><p>
			<?php	the_post(); the_content(); 	wp_reset_query(); ?>
			</p></div>
		<?php else : ?>
			<?php locate_template( array( 'members/single/profile/profile-loop.php' ), true ) ?>
		<?php endif; ?>
	<?php endif; ?>
</div><!-- .profile -->

<?php do_action( 'bp_after_profile_content' ) ?>

