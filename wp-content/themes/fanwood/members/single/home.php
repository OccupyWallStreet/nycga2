<?php

/**
 * BuddyPress - Single Member
 *
 * @package BuddyPress
 * @subpackage Theme
 */

get_header(); // Loads the header.php template. ?>

<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

<div id="content">

	<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

	<div class="hfeed">
		
		<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>
		
		<?php do_action( 'bp_before_member_home_content' ); ?>

		<div id="item-header" role="complementary">

			<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

		</div><!-- #item-header -->

		<div id="item-nav">
			<div class="item-list-tabs bp-tabs no-ajax" id="object-nav" role="navigation">
				<ul>
					<?php bp_get_displayed_user_nav(); ?>
					<?php do_action( 'bp_member_options_nav' ); ?>

				</ul>
			</div>
		</div><!-- #item-nav -->

		<div id="item-body">

			<?php do_action( 'bp_before_member_body' );

			if ( bp_is_user_activity() || !bp_current_component() ) :
				locate_template( array( 'members/single/activity.php'  ), true );

			elseif ( bp_is_user_blogs() ) :
				locate_template( array( 'members/single/blogs.php'     ), true );

			elseif ( bp_is_user_friends() ) :
				locate_template( array( 'members/single/friends.php'   ), true );

			elseif ( bp_is_user_groups() ) :
				locate_template( array( 'members/single/groups.php'    ), true );

			elseif ( bp_is_user_messages() ) :
				locate_template( array( 'members/single/messages.php'  ), true );

			elseif ( bp_is_user_profile() ) :
				locate_template( array( 'members/single/profile.php'   ), true );

			elseif ( bp_is_user_forums() ) :
				locate_template( array( 'members/single/forums.php'    ), true );

			elseif ( bp_is_user_settings() ) :
				locate_template( array( 'members/single/settings.php'  ), true );

			// If nothing sticks, load a generic template
			else :
				locate_template( array( 'members/single/plugins.php'   ), true );

			endif;

			do_action( 'bp_after_member_body' ); ?>

		</div><!-- #item-body -->

		<?php do_action( 'bp_after_member_home_content' ); ?>

		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!--. hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>
