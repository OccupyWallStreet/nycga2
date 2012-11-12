<?php

	/**
	 * Get media object by url
	 *
	 * @param string $media_url Media file url
	 * @param int $width Media object width
	 * @param int $height Media object height
	 * @return string Object markup
	 */
	function su_get_media( $media_url, $width, $height, $jwplayer = false ) {

		// Youtube video
		$video_url = parse_url( $media_url );

		if ( $video_url['host'] == 'youtube.com' || $video_url['host'] == 'www.youtube.com' ) {
			parse_str( $video_url['query'], $youtube );
			$id = uniqid( '', false );
			$return = '
					<iframe width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $youtube['v'] . '" frameborder="0" allowfullscreen="true"></iframe>
				';
		}

		// Vimeo video
		$video_url = parse_url( $media_url );

		if ( $video_url['host'] == 'vimeo.com' || $video_url['host'] == 'www.vimeo.com' ) {
			$vimeo_id = mb_substr( $video_url['path'], 1 );
			$return = '<iframe src="http://player.vimeo.com/video/' . $vimeo_id . '?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="' . $width . '" height="' . $height . '" frameborder="0"></iframe>';
		}

		// Images (bmp/jpg/jpeg/png/gif)
		$images = array( '.bmp', '.BMP', '.jpg', '.JPG', '.png', '.PNG', 'jpeg', 'JPEG', '.gif', '.GIF' );
		$image_ext = mb_substr( $media_url, -4 );

		if ( in_array( $image_ext, $images ) ) {
			$return = '<img src="' . su_plugin_url() . '/lib/timthumb.php?src=' . $media_url . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;zc=1&amp;q=100" alt="" width="' . $width . '" height="' . $height . '" />';
		}

		// Video file (mp4/flv)
		$videos = array( '.mp4', '.MP4', '.flv', '.FLV' );
		$video_ext = mb_substr( $media_url, -4 );

		if ( in_array( $video_ext, $videos ) ) {
			$player_id = uniqid( '_', false );

			if ( is_array( $jwplayer ) ) {
				foreach ( $jwplayer as $jwplayer_option => $jwplayer_value ) {
					$jwplayer_options .= ',' . $jwplayer_option . ':"' . $jwplayer_value . '"';
				}
			}

			$return = '<div id="player' . $player_id . '"><script type="text/javascript">jwplayer("player' . $player_id . '").setup({flashplayer:"' . su_plugin_url() . '/lib/player.swf",file:"' . $media_url . '",height:' . $height . ',width:' . $width . $jwplayer_options . '});</script></div>';
		}

		// Audio file (mp3)
		if ( mb_substr( $media_url, -4 ) == '.mp3' ) {
			$player_id = uniqid( '_', false );

			$return = '<div id="player' . $player_id . '"><script type="text/javascript">jwplayer("player' . $player_id . '").setup({flashplayer:"' . su_plugin_url() . '/lib/player.swf",file:"' . $media_url . '",height: ' . $height . ',width:' . $width . ',controlbar:"bottom",image:"' . su_plugin_url() . '/images/media-audio.jpg",icons:"none",screencolor:"F0F0F0"});</script></div>';
		}

		return $return;
	}

?>