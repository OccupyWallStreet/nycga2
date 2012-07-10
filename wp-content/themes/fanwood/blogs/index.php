<?php
/**
 * BuddyPress - Blog Directory
 *
 * @package BuddyPress
 * @subpackage Theme
 */

get_header(); // Loads the header.php template. ?>

<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

<div id="content">

	<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

	<?php do_action( 'bp_before_directory_blogs_page' ); ?>

	<div class="hfeed">
	
		<?php if ( current_theme_supports( 'breadcrumb-trail' ) ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
		
		<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

		<?php do_action( 'bp_before_directory_blogs' ); ?>

		<div class="loop-meta dir-meta">
			
			<h1 class="loop-title dir-title"><?php _e( 'Sites Directory', 'buddypress' ); ?><?php if ( is_user_logged_in() && bp_blog_signup_enabled() ) : ?> &nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bp_get_blogs_root_slug() . '/create/' ?>"><?php _e( 'Create a Site', 'buddypress' ); ?></a><?php endif; ?></h1>
				
			<div id="blog-dir-search" class="dir-search" role="search">
				<?php bp_directory_blogs_search_form(); ?>
			</div><!-- #blog-dir-search -->
				
			<?php do_action( 'bp_before_directory_blogs_content' ); ?>
			
		</div><!-- .loop-meta -->

		<form action="" method="post" id="blogs-directory-form" class="dir-form">

			<div class="bp-tabs item-list-tabs" role="navigation">
				<ul>
					<li class="selected" id="blogs-all"><a href="<?php bp_root_domain(); ?>/<?php bp_blogs_root_slug() ?>"><?php printf( __( 'All Sites <span>%s</span>', 'buddypress' ), bp_get_total_blog_count() ); ?></a></li>

					<?php if ( is_user_logged_in() && bp_get_total_blog_count_for_user( bp_loggedin_user_id() ) ) : ?>

						<li id="blogs-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_blogs_slug() ?>"><?php printf( __( 'My Sites <span>%s</span>', 'buddypress' ), bp_get_total_blog_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

					<?php endif; ?>

					<?php do_action( 'bp_blogs_directory_blog_types' ); ?>

				</ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs bp-sub-tabs" id="subnav" role="navigation">
				<ul>

					<?php do_action( 'bp_blogs_directory_blog_sub_types' ); ?>

					<li id="blogs-order-select" class="last filter">

						<label for="blogs-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
						<select id="blogs-order-by">
							<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
							<option value="newest"><?php _e( 'Newest', 'buddypress' ); ?></option>
							<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

							<?php do_action( 'bp_blogs_directory_order_options' ); ?>

						</select>
					</li>
				</ul>
			</div>

			<div id="blogs-dir-list" class="blogs">

				<?php locate_template( array( 'blogs/blogs-loop.php' ), true ); ?>

			</div><!-- #blogs-dir-list -->

			<?php do_action( 'bp_directory_blogs_content' ); ?>

			<?php wp_nonce_field( 'directory_blogs', '_wpnonce-blogs-filter' ); ?>

			<?php do_action( 'bp_after_directory_blogs_content' ); ?>

		</form><!-- #blogs-directory-form -->

		<?php do_action( 'bp_after_directory_blogs' ); ?>
		
		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!-- .hfeed -->

	<?php do_action( 'bp_after_directory_blogs_page' ); ?>
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>
