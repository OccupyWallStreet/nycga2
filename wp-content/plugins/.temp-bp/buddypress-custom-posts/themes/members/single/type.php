<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<?php if ( bp_is_my_profile() ) : ?>
			<?php bp_get_options_nav() ?>
		<?php endif; ?>

		<li id="groups-order-select" class="last filter">

			<?php _e( 'Order By:', 'buddypress' ) ?>
			<select id="type-sort-by">
				<option value="active"><?php _e( 'Last Active', 'buddypress' ) ?></option>
				<option value="newest"><?php _e( 'Newly Created', 'buddypress' ) ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ) ?></option>
				<?php do_action( 'bpcp_members_type_sort' ); ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<div class="<?php global $bp; echo $bp->active_components[ $bp->current_component ]; ?>">
	<?php 
		$load_template = apply_filters( 'bpcp_members_type_loop', Array( 'type/type-loop.php' ) );
		bpcp_locate_template( $load_template, true );
	?>
</div>
