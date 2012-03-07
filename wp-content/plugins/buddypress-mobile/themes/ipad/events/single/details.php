<?php do_action( 'bp_before_event_details_admin' ); ?>

<?php $jes_adata = get_option( 'jes_events' ); ?>

<table valign="top">
	<tr>
		<td width="49%" style="vertical-align:top;">
				<h4><?php _e('Base event details','jet-event-system'); ?></h4>
				<p><strong><?php _e('Event Description', 'jet-event-system') ?>:</strong></p>
					<?php jes_bp_event_description() ?>
		</td>
		<td style="vertical-align:top;">
				<h4><?php _e('Event Location:', 'jet-event-system') ?></h4>
				<span><strong><?php _e('The event will take place:','jet-event-system'); ?></strong>
						<?php
							if ( $jes_adata[ 'jes_events_countryopt_enable' ] )
								{
									jes_bp_event_placedcountry(); ?> ,
							<?php } ?>
					<?php	if ( $jes_adata[ 'jes_events_stateopt_enable' ] )
								{
									jes_bp_event_placedstate(); ?> ,
							<?php } ?>
					<?php	if ( $jes_adata[ 'jes_events_cityopt_enable' ] )
								{	?>
					<strong> <?php _e('in city:','jet-event-system') ?></strong> <?php jes_bp_event_placedcity() ?>
							<?php } ?></span>
	
		<?php if ( jes_bp_event_is_visible() ) { ?>
					<p><strong><?php _e('Event address', 'jet-event-system') ?>:</strong> <?php jes_bp_event_placedaddress() ?>
				<?php if ( $jes_adata[ 'jes_events_noteopt_enable' ] )
							{	?>
					<br />
					<strong><?php _e('Event note', 'jet-event-system') ?>:</strong> <?php jes_bp_event_placednote() ?>
				<?php } ?></p>
		<?php } ?>		
			<h4><?php _e('Event Date','jet-event-system') ?></h4>
				<p><strong><?php _e('Event Start date', 'jet-event-system') ?>:</strong> <?php jes_bp_event_edtsd() ?>, <strong><?php _e('Time:','jet-event-system') ?></strong> <?php jes_bp_event_edtsth() ?>:<?php jes_bp_event_edtstm() ?><br />
			<strong><?php _e('Event End date', 'jet-event-system') ?>:</strong> <?php jes_bp_event_edted() ?>, <strong><?php _e('Time:','jet-event-system') ?></strong> <?php jes_bp_event_edteth() ?>:<?php jes_bp_event_edtetm() ?></p>
	<?php	if ( $jes_adata[ 'jes_events_specialconditions_enable' ] )
				{ ?>
					<?php if ( jes_bp_get_event_eventterms() != null ) { ?>
							<h4><?php _e('Special Conditions', 'jet-event-system') ?>:</h4>
							<?php jes_bp_event_eventterms() ?>
					<?php } ?>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td width="49%" style="vertical-align:bottom;">
		<?php if ( $jes_adata[ 'jes_events_publicnews_enable' ] )
					{ ?>			
						<?php if ( jes_bp_get_event_newspublic() != null ) { ?>
								<h4><?php _e('Public Event News', 'jet-event-system') ?>:</h4>
								<p><?php jes_bp_event_newspublic() ?></p>
						<?php } ?>
				<?php } ?>		
		</td>
		<td style="vertical-align:top;">
		<?php if ( $jes_adata[ 'jes_events_privatenews_enable' ] )
					{ ?>			
						<?php if (bp_is_user_events()) { ?>
								<?php if ( jes_bp_get_event_newsprivate() != null ) { ?>
											<h4><?php _e('Private Event News', 'jet-event-system') ?>:</h4>
											<p><?php jes_bp_event_newsprivate() ?></p>
								<?php } ?>
						<?php } ?>
				<?php } ?>
		</td>
	</tr>
</table>

<?php do_action( 'bp_after_event_details_admin' ); ?>