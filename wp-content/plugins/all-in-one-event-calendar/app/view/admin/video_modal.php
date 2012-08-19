<div class="timely">
	<div class="modal hide" id="<?php echo $modal_id; ?>" style="width: 600px; margin-left: -300px; margin-top: -235px;">
		<div class="modal-header">
			<button class="close" data-dismiss="modal">×</button>
			<h1><small><?php echo esc_html( $title ); ?></small></h1>
		</div>
		<div id="<?php echo $video_container_id; ?>"></div>
		<?php if ( isset( $footer ) ): ?>
			<div class="modal-footer">
				<?php echo $footer; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
	var ai1ecVideo = function() {
		// Load the YouTube IFrame Player API code asynchronously.
		var tag = document.createElement( 'script' );
		tag.src = "//www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName( 'script' )[0];
		firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );

		// Create an <iframe> (and YouTube player) after the API code downloads.
		function onYouTubeIframeAPIReady() {
			var player = new YT.Player( '<?php echo $video_container_id; ?>', {
				height: '368',
				width: '600',
				videoId: '<?php echo $youtube_id; ?>'
			});
			player.getIframe().style.display = "block";

			jQuery( '#<?php echo $modal_id; ?>' ).bind( 'hide', function() {
				player.stopVideo();
			});
		}

		return {
			onYouTubeIframeAPIReady: onYouTubeIframeAPIReady
		}
	}

	var onYouTubeIframeAPIReady = ai1ecVideo().onYouTubeIframeAPIReady;
/* ]]> */
</script>
