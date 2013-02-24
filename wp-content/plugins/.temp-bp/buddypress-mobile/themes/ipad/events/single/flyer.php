<?php do_action( 'bp_before_event_flyer' ); ?>

<?php if (jes_bp_get_event_flyer() != null ) { ?>

<h4><?php _e('Flyer','jet-event-system'); ?></h4>
<?php do_action('bp_before_event_flyer_image'); ?>
<?php if (bp_event_is_member()) { ?>
			<img src="<?php jes_bp_event_flyer() ?>">

<?php	}
			else
		{
			if ($navi['jes_events_flyer_toall'])
				{ ?>
					<img src="<?php jes_bp_event_flyer() ?>">				
		<?php	}
					else
				{
					_e('Available only to participants of the event!','jet-event-system');
				}
		} 
	} else { ?>
	<?php _e('Currently unavailable','jet-event-system'); ?>
<?php } ?>
<?php do_action( 'bp_after_event_flyer' ); ?>