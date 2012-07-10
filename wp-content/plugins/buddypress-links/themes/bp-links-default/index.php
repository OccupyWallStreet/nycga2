<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<form action="" method="post" id="links-directory-form" class="dir-form">
			<h2><?php _e( 'Links Directory', 'buddypress-links' ) ?><?php if ( is_user_logged_in() ) : ?> &nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . BP_LINKS_SLUG . '/create/' ?>"><?php _e( 'Create a Link', 'buddypress-links' ) ?></a><?php endif; ?></h2>

			<?php do_action( 'bp_before_directory_links_content' ) ?>

			<div id="link-dir-search" class="dir-search">
				<?php bp_links_dtheme_search_form() ?>
			</div><!-- #link-dir-search -->

			<div class="item-list-tabs">
				<ul>
					<li class="selected" id="links-all"><a href="<?php bp_root_domain() ?>"><?php printf( __( 'All Links (%s)', 'buddypress-links' ), bp_get_links_total_link_count() ) ?></a></li>

					<?php if ( is_user_logged_in() && bp_links_total_links_for_user( bp_loggedin_user_id() ) ) : ?>
						<li id="links-mylinks"><a href="<?php echo bp_loggedin_user_domain() . BP_LINKS_SLUG . '/my-links/' ?>"><?php printf( __( 'My Links (%s)', 'buddypress-links' ), bp_links_total_links_for_user( bp_loggedin_user_id() ) ) ?></a></li>
					<?php endif; ?>

					<?php do_action( 'bp_links_directory_link_types' ) ?>
					
					<?php bp_links_dtheme_link_order_options_list() ?>
		
				</ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs no-ajax" id="subnav">
				<ul>
					<li class="feed"><a href="<?php bp_directory_links_feed_link() ?>" title="RSS Feed"><?php _e( 'RSS', 'buddypress' ) ?></a></li>
					<?php do_action( 'bp_links_syndication_options' ) ?>

					<?php bp_links_dtheme_link_category_filter_options_list() ?>
				</ul>
			</div><!-- .item-list-tabs -->

			<div id="links-dir-list" class="links dir-list">
				<?php bp_links_locate_template( array( 'links-loop.php' ), true ) ?>
			</div><!-- #links-dir-list -->

			<?php do_action( 'bp_directory_links_content' ) ?>

			<?php wp_nonce_field( 'directory_links', '_wpnonce-links-filter' ) ?>

		</form><!-- #links-directory-form -->

		<?php do_action( 'bp_after_directory_links_content' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>