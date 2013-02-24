<?php 
/****************************************/
/* Custom Template for Event Loop		*/
/* based on Standart Templates			*/
/* Base Template						*/
/****************************************/
?>
<?php
$_eventstatus = eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
?>
<li>
	<div class="item-avatar" id="jes-avatar">
		<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar( 'type=thumb&width='.$jes_adata['jes_events_show_avatar_directory_size'].'&height='.$jes_adata['jes_events_show_avatar_directory_size'] ) ?></a>
	</div>

	<div class="item" style="width:80%;" id="jes-item">
		<div class="item-title" id="jes-title"><a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_name() ?></a></div>			
			<div class="item-meta">
				<em><?php echo $_eventstatus; ?></em> , 
				<p class="meta"><em><?php jes_bp_event_type() ?></em></p>
				<div class="item-desc" id="jes-timedate">
					<?php _e('From: ','jet-event-system') ?><span class="meta"><?php jes_bp_event_edtsd() ?>, <?php jes_bp_event_edtsth() ?>:<?php jes_bp_event_edtstm() ?></span> <?php _e('to: ','jet-event-system') ?> <span><?php jes_bp_event_edted() ?>, <?php jes_bp_event_edteth() ?>:<?php jes_bp_event_edtetm() ?></span>
				</div>
		<?php if ($jes_adata['jes_events_style'] == 'Standart') { ?>
			<?php _e('Short description:','jet-event-system') ?> <?php jes_bp_event_description_excerpt() ?>
		<?php } else { ?>
			<?php _e('Description:','jet-event-system') ?> <?php jes_bp_event_description() ?>
		<?php } ?>
			</div>				
			<div class="item-desc" id="jes-desc">
				<span><?php _e('The event will take place:','jet-event-system');
				if ( $jes_adata[ 'jes_events_countryopt_enable' ] )
					{
						jes_bp_event_placedcountry(); ?> ,
				<?php }
				if ( $jes_adata[ 'jes_events_stateopt_enable' ] )
					{
						jes_bp_event_placedstate(); ?> ,
				<?php } ?></span>
											
				<span><?php _e('in city:','jet-event-system') ?> <?php jes_bp_event_placedcity() ?><?php if ( jes_bp_event_is_visible() ) { ?>, <?php _e('at ','jet-event-system') ?><?php jes_bp_event_placedaddress() ?> <?php } ?></span><br />
			</div>
<?php do_action( 'bp_directory_events_item' ) ?>
		</div>
		<div class="action" id="jes-button">
			<?php 
				if (!strpos($_eventstatus,__('Past event','jet-event-system')))
					{
						bp_event_join_button();
					}
			?>
				<div class="meta" id="jes-approval">
					<?php if ( $shiftcan ) 
							{ ?>
								<span class="meta"><em><?php _e('Event requires approval!','jet-event-system'); ?></em></span>
					<?php }	?>
					<p><strong><?php jes_bp_event_etype() ?></strong>, <?php jes_bp_event_member_count() ?></p>
				</div>
<?php do_action( 'bp_directory_events_actions' ) ?>
			</div>
		<div class="clear"></div>
</li>
<?php
// End Template
?>