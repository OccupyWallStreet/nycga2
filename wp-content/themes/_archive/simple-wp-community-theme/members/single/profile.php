<?php if ( bp_is_my_profile() ) : ?>
	<div class="item-list-tabs no-ajax" id="">
		<ul>
			<?php bp_get_options_nav() ?>
		</ul>
	</div>
	<div class="item-list-tabs no-ajax" id="subnav"></div>
<?php endif; ?>

<?php do_action( 'bp_before_profile_content' ) ?>

<div class="profile">
	<?php if ( 'edit' == bp_current_action() ) : ?>
		<?php locate_template( array( 'members/single/profile/edit.php' ), true ) ?>

	<?php elseif ( 'change-avatar' == bp_current_action() ) : ?>
		<?php locate_template( array( 'members/single/profile/change-avatar.php' ), true ) ?>

	<?php else : ?>
		<?php locate_template( array( 'members/single/profile/profile-loop.php' ), true ) ?>

	<?php endif; ?>
</div><!-- .profile -->

<?php do_action( 'bp_after_profile_content' ) ?>