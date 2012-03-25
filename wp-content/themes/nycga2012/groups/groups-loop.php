<?php

/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' )."&per_page=40") ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="group-dir-count-top">
			<?php bp_groups_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="group-dir-pag-top">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list clearfix" role="main">


	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li class="group grid_4 clearfix gridbox shadow tipTipActuator">
			<div class="padded clearfix">
				<div class="item-avatar">
					<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=thumb&width=42&height=42' ); ?></a>
				</div>
				<div class="item">
					<h5 class="item-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></h5>
					<div class="item-desc tipTipContent"><?php bp_group_description_excerpt(); ?></div>
					<?php do_action( 'bp_directory_groups_item' ); ?>
				</div>
				<div class="action">
					<?php do_action( 'bp_directory_groups_actions' ); ?>
					<div class="meta">
						<div class="small group-count"><?php bp_group_member_count(); ?></div>
						<div class="small last-active"><?php printf( __( 'Active %s', 'buddypress' ), bp_get_group_last_active() ); ?></div>
					</div>
				</div>
			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">
		<div class="pag-count" id="group-dir-count-bottom">
			<?php bp_groups_pagination_count(); ?>
		</div>
		<div class="pagination-links" id="group-dir-pag-bottom">
			<?php bp_groups_pagination_links(); ?>
		</div>
	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<!--
	Move this to funtions.php
	function bbg_print_script() { echo '<script>foo();</script>'; } add_action( 'bp_after_groups_loop', 'bbg_print_script' );
-->

<script type="text/javascript">
nycga.ui.addToolTips();
</script>
<?php do_action( 'bp_after_groups_loop' ); ?>
