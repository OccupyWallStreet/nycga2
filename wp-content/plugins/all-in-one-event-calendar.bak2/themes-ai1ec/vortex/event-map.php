<?php if( $hide_maps_until_clicked ) : ?>
  <div class="ai1ec-gmap-placeholder"><strong><i class="icon-map-marker"></i> <?php _e( 'Click to view map', AI1EC_PLUGIN_NAME ) ?></strong></div>
<?php endif; ?>
<div class="ai1ec-gmap-container<?php echo $hide_maps_until_clicked ? ' ai1ec-gmap-container-hidden' : '' ?>">
	<div id="ai1ec-gmap-canvas"></div>
	<input type="hidden" id="ai1ec-gmap-address" value="<?php echo esc_attr( $address ) ?>" />
	<a class="ai1ec-gmap-link btn btn-mini"
		href="<?php echo $gmap_url_link ?>" target="_blank">
		<?php _e( 'View Full-Size Map', AI1EC_PLUGIN_NAME ) ?> <i class="icon-arrow-right"></i>
	</a>
</div>
