<div id="wdqs-dashboard-widget">
	<div class="error below-h2"><p><a title="Upgrade Now" href="http://premium.wpmudev.org/project/quick-status">Upgrade to Pro version to enable additional features</a></p></div>
	<?php if ($status) { ?>
		<div class="wdqs-update-notice updated below-h2">
			<p><?php printf( __('Post updated. <a href="%s">View post</a>'), esc_url( get_permalink($status) ) );?></p>
		</div>
	<?php } ?>
	<div id="wdqs-form-root">
		<p>
			<div id="wdqs-types-tabs">
				<b>Post:</b>
				<a href="#generic" class="wdqs-type-switch" id="wdqs-generic-switch"><?php _e("Status", "wdqs")?></a>
				<a href="#videos" class="wdqs-type-switch" id="wdqs-video-switch"><?php _e("Video", "wdqs")?></a>
				<a href="#images" class="wdqs-type-switch" id="wdqs-image-switch"><?php _e("Image", "wdqs")?></a>
				<a href="#links" class="wdqs-type-switch" id="wdqs-link-switch"><?php _e("Link", "wdqs")?></a>
			</div>
			<div id="wdqs-status-arrow-container">
			<div id="wdqs-status-arrow"></div>
			</div>
			<textarea rows="1" class="widefat" id="wdqs-status" name="wdqs-status"></textarea>
		</p>
		<p id="wdqs-controls">
			<input type="button" class="button" id="wdqs-preview" value="<?php _e("Preview", 'wdqs');?>" />
			<input type="button" class="button" id="wdqs-reset" value="<?php _e("Forget it", 'wdqs');?>" />
			<input type="button" class="button-primary" id="wdqs-post" value="<?php _e("Post", 'wdqs');?>" />
			<input type="button" class="button" id="wdqs-draft" value="<?php _e("Draft", 'wdqs');?>" />
		</p>
	</div>
	<input type="hidden" id="wdqs-link-type" value="" />
	<div id="wdqs-preview-root"></div>
</div>