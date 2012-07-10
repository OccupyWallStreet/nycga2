<?php

	define( 'MAGPIE_CACHE_ON', 1 ); //2.7 Cache Bug
	define( 'MAGPIE_CACHE_AGE', 900 );
	define( 'MAGPIE_INPUT_ENCODING', 'UTF-8' );
	define( 'MAGPIE_OUTPUT_ENCODING', 'UTF-8' );

	/**
	 * Tweet relative time (like: 5 seconds ago)
	 */
	if ( !function_exists( 'shortcodes_ultimate_relative_time' ) ) {

		function shortcodes_ultimate_relative_time( $original, $do_more = 0 ) {
			// array of time period chunks
			$chunks = array(
				array( 60 * 60 * 24 * 365, __( 'year', 'shortcodes-ultimate' ) ),
				array( 60 * 60 * 24 * 30, __( 'month', 'shortcodes-ultimate' ) ),
				array( 60 * 60 * 24 * 7, __( 'week', 'shortcodes-ultimate' ) ),
				array( 60 * 60 * 24, __( 'day', 'shortcodes-ultimate' ) ),
				array( 60 * 60, __( 'hour', 'shortcodes-ultimate' ) ),
				array( 60, __( 'minute', 'shortcodes-ultimate' ) ),
			);

			$today = time();
			$since = $today - $original;

			for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
				$seconds = $chunks[$i][0];
				$name = $chunks[$i][1];

				if ( ($count = floor( $since / $seconds )) != 0 )
					break;
			}

			$return = ($count == 1) ? '1 ' . $name : "$count {$name}" . __( 's', 'shortcodes-ultimate' );

			if ( $i + 1 < $j ) {
				$seconds2 = $chunks[$i + 1][0];
				$name2 = $chunks[$i + 1][1];

				// add second item if it's greater than 0
				if ( (($count2 = floor( ($since - ($seconds * $count)) / $seconds2 )) != 0) && $do_more )
					$return .= ( $count2 == 1) ? ', 1 ' . $name2 : ", $count2 {$name2}" . __( 's', 'shortcodes-ultimate' );
			}
			return $return;
		}

	}

	/**
	 * Add hyperlinks to tweets
	 */
	function su_add_hyperlinks( $text ) {
		// Props to Allen Shaw & webmancers.com
		// match protocol://address/path/file.extension?some=variable&another=asf%
		$text = preg_replace( '/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', "<a href=\"$1\" class=\"twitter-link\">$1</a>", $text );
		// match www.something.domain/path/file.extension?some=variable&another=asf%
		$text = preg_replace( '/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', "<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text );

		// match name@address
		$text = preg_replace( "/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i", "<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text );
		//mach #trendingtopics. Props to Michael Voigt
		$text = preg_replace( '/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitter-link\">#$2</a>$3 ", $text );
		return $text;
	}

	/**
	 * Get tweets by username
	 */
	function su_get_tweets( $username, $limit, $show_time ) {

		include_once( ABSPATH . WPINC . '/rss.php' );

		$messages = fetch_rss( 'http://twitter.com/statuses/user_timeline/' . $username . '.rss' );

		if ( $username == '' ) {
			$return = '<p class="su-error"><strong>Tweets:</strong> ' . __( 'username not specified', 'shortcodes-ultimate' ) . '&hellip;</p>';
		} else {
			if ( empty( $messages->items ) ) {
				$return = '<p class="su-error"><strong>Tweets:</strong> ' . __( 'no public messages', 'shortcodes-ultimate' ) . '&hellip;</p>';
			} else {
				$i = 0;

				foreach ( $messages->items as $message ) {
					$msg = substr( strstr( $message['description'], ': ' ), 2, strlen( $message['description'] ) ) . " ";
					if ( $encode_utf8 )
						$msg = utf8_encode( $msg );
					$link = $message['link'];
					$time = $message['pubdate'];

					$relative_time = ( $show_time ) ? '<span class="su-tweet-time">' . shortcodes_ultimate_relative_time( strtotime( $time ) ) . '</span>' : '';

					$last_tweet_class = ( $i >= ( $limit - 1 ) ) ? ' su-tweet-last' : '';

					$return .= '<div class="su-tweet' . $last_tweet_class . '">';
					$return .= '<a href="http://twitter.com/' . $username . '" class="su-tweet-username">@' . $username . '</a>: ';
					$return .= su_add_hyperlinks( $msg );
					$return .= $relative_time;
					$return .= '</div>';

					$i++;
					if ( $i >= $limit )
						break;
				}
			}
		}

		return $return;
	}

?>