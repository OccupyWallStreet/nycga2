<?php
/**
 * BuddyPress - Create Blog
 *
 * @package BuddyPress
 * @subpackage Theme
 */

get_header(); // Loads the header.php template. ?>

<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

<div id="content">

	<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

	<?php do_action( 'bp_before_directory_blogs_content' ); ?>

	<div class="hfeed">
	
		<?php if ( current_theme_supports( 'breadcrumb-trail' ) ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
		
		<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

		<?php do_action( 'template_notices' ); ?>

		<div class="loop-meta">
			<h1 class="loop-title dir-title"><?php _e( 'Create a Site', 'buddypress' ); ?> &nbsp;<a class="button" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_blogs_root_slug() ) ?>"><?php _e( 'Site Directory', 'buddypress' ); ?></a></h1>
		</div><!-- .loop-meta -->

		<?php do_action( 'bp_before_create_blog_content' ); ?>

		<?php if ( bp_blog_signup_enabled() ) : ?>
		
			<div class="entry-content">

				<?php bp_show_blog_signup_form(); ?>
				
			</div><!-- .entry-content -->

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'Site registration is currently disabled', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>

		<?php do_action( 'bp_after_create_blog_content' ); ?>

		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!-- .hfeed -->

	<?php do_action( 'bp_after_directory_blogs_content' ); ?>

	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>
