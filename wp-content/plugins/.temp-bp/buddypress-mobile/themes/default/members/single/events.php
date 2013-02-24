<?php get_header() ?>

	<div id="content">
		<div class="padder" id="jes-padder">

			<?php do_action( 'bp_before_member_home_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>

						<?php do_action( 'bp_events_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
				<?php do_action( 'bp_before_member_body' ) ?>

<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<?php if ( bp_is_my_profile() ) : ?>
			<?php bp_get_options_nav() ?>
		<?php endif; ?>

		<?php if ( 'invites' != bp_current_action() ) : ?>
		<li id="events-order-select" class="last filter">

			<?php _e( 'Order By:', 'buddypress' ) ?>
			<select id="events-sort-by">		
				<option value="active"><?php _e( 'Last Active', 'buddypress' ) ?></option>
				<option value="popular"><?php _e( 'Most Members', 'buddypress' ) ?></option>
				<option value="newest"><?php _e( 'Newly Created', 'buddypress' ) ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ) ?></option>
				<option value="soon"><?php _e( 'Soon', 'buddypress' ) ?></option>
				
				<?php do_action( 'bp_member_event_order_options' ) ?>
			</select>
		</li>
		<?php endif; ?>
	</ul>
</div><!-- .item-list-tabs -->

<?php if ( 'invites' == bp_current_action() ) : ?>
	<?php locate_template( array( 'members/single/events/invites.php' ), true ) ?>

<?php else : ?>

	<?php do_action( 'bp_before_member_events_content' ) ?>

	<div class="events myevents">
		<?php locate_template( array( 'events/events-loop.php' ), true ) ?>
	</div>

	<?php do_action( 'bp_after_member_events_content' ) ?>

<?php endif; ?>


				<?php do_action( 'bp_after_event_body' ) ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_event_home_content' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>