<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php get_header(); ?>

	<?php do_action( 'bp_before_directory_groups_page' ); ?>

	<div id="content" class="span8">
		<div class="padder">

		<?php do_action( 'bp_before_directory_groups' ); ?>

		<form action="" method="post" id="groups-directory-form" class="dir-form">

			<h3><?php _e( 'Groups Directory', 'cc' ); ?><?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> &nbsp;<a class="button" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><?php _e( 'Create a Group', 'cc' ); ?></a><?php endif; ?></h3>

			<?php do_action( 'bp_before_directory_groups_content' ); ?>

			<div id="group-dir-search" class="dir-search" role="search">

				<?php bp_directory_groups_search_form() ?>

			</div><!-- #group-dir-search -->

			<?php do_action( 'template_notices' ); ?>

			<div class="item-list-tabs" role="navigation">
                    <ul>
                        <li class="selected" id="groups-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php printf( __( 'All Groups <span>%s</span>', 'cc' ), bp_get_total_group_count() ); ?></a></li>

                        <?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

                            <li id="groups-personal"><a href="<?php echo trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups' ); ?>"><?php printf( __( 'My Groups <span>%s</span>', 'cc' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

                        <?php endif; ?>

                        <?php do_action( 'bp_groups_directory_group_filter' ); ?>

                    </ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs row-fluid" id="subnav" role="navigation">
				<ul>

					<?php do_action( 'bp_groups_directory_group_types' ); ?>

					<li id="groups-order-select" class="span5">

						<label for="groups-order-by"><?php _e( 'Order By:', 'cc' ); ?></label>
						<select id="groups-order-by">
							<option value="active"><?php _e( 'Last Active', 'cc' ); ?></option>
							<option value="popular"><?php _e( 'Most Members', 'cc' ); ?></option>
							<option value="newest"><?php _e( 'Newly Created', 'cc' ); ?></option>
							<option value="alphabetical"><?php _e( 'Alphabetical', 'cc' ); ?></option>

							<?php do_action( 'bp_groups_directory_order_options' ); ?>

						</select>
					</li>
					
					<li id="groups-displaymode-select" class="span4 displaymode">

						<label for="groups-displaymode"><?php _e( 'Display mode:', 'cc' ); ?></label>
						<select id="groups-displaymode">
							<option value="list"><?php _e( 'List', 'cc' ); ?></option>
							<option value="grid"><?php _e( 'Grid', 'cc' ); ?></option>

							<?php do_action( 'bp_groups_directory_displaymode_options' ); ?>

						</select>
					</li>
				</ul>
			</div>

			<div id="groups-dir-list" class="groups dir-list">

				<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>

			</div><!-- #groups-dir-list -->

			<?php do_action( 'bp_directory_groups_content' ); ?>

			<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

			<?php do_action( 'bp_after_directory_groups_content' ); ?>

		</form><!-- #groups-directory-form -->

		<?php do_action( 'bp_after_directory_groups' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_groups_page' ); ?>

<?php get_footer(); ?>

