<?php
/* Tribe Related Posts Class */
if( !class_exists( 'TribeRelatedPosts' ) ) {
	include('tribe-related-posts-widget.php');
	class TribeRelatedPosts {

		private static $instance;
		private static $cache = array();

		/**
		 * Instance function
		 *
		 * @since 1.0
		 * @author Modern Tribe
		 * @return object the instance of the class.
		 */
		public static function instance() {
			if ( !isset( self::$instance ) ) {
				$className = __CLASS__;
				self::$instance = new $className;
			}
			return self::$instance;
		}

		/**
		 * Class constructor.
		 *
		 * @since 1.0
		 * @author Modern Tribe
		 * @return void
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'registerShortcodes' ), 5 );
			add_action( 'init', array( $this, 'setUpThumbnails' ), 5 );
		}

		/**
		 * Set up post thumbnails.
		 *
		 * @since 1.0
		 * @author Modern Tribe
		 * @return void
		 */
		public function setUpThumbnails() {
			if ( current_theme_supports( 'post-thumbnails' ) ) {
				global $_wp_additional_image_sizes;
				if ( !isset( $_wp_additional_image_sizes['tribe-related-posts-thumbnail'] ) ) {
					add_image_size('tribe-related-posts-thumbnail', 150, 100, true);
				}
			}
		}

		/**
		 * Register wordpress shortcodes.
		 *
		 * @since 1.0
		 * @author Modern Tribe
		 * @return void
		 */
		public function registerShortcodes() {
			add_shortcode( 'tribe-related-posts', array($this, 'shortcodeFeature' ) );
		}

		/**
		 * Fetch and display the related posts from the shortcode.
		 *
		 * @since 1.0
		 * @author Modern Tribe
		 * @param array $atts the shortcode attributes.
		 * @param string $content
		 * @return void
		 */
		public function shortcodeFeature( $atts, $content = null ) {
			$defaults = array( 'tag' => false, 'blog' => false, 'count' => 5, 'only_display_related' => false, 'thumbnails' => true, 'post_type' => 'post' );
			$atts = shortcode_atts( $defaults, $atts );
			return self::displayPosts( $atts['tag'], $atts['count'], $atts['blog'], $atts['only_display_related'], $atts['thumbnails'], $atts['post_type'] );
		}

		/**
		 * Get related posts
		 *
		 * @since 1.0
		 * @author Modern Tribe
		 * @param string $tags comma-separated list of tags.
		 * @param int $count number of related posts to return.
		 * @param string $blog the blog from which to fetch the related posts.
		 * @param bool $only_display_related whether to display only related posts or others as well.
		 * @param string $post_type the type of post to return.
		 * @return array the related posts.
		 */
		public function getPosts( $tags = array(), $count = 5, $blog = false, $only_display_related = false, $post_type = 'post' ) {
			$post_id = get_the_ID();
			if ( is_string( $tags ) ) {
				$tags = explode( ',', $tags );
			}
			if (isset( self::$cache[$post_id] ) && true == false ) {
				return self::$cache[$post_id];
			}
			if ( count( $tags ) == 0 || $tags == false) {
				// get tag from current post.
				$posttags = get_the_tags(get_the_ID());
				// Abstract the list of slugs from the tags.
				if ( is_array( $posttags ) ) {
					foreach( $posttags as $k => $v ) {
						$tags[] = $v->slug;
					}
				}
			}
			if (count( $tags ) > 0 ) {
				if ( $blog && !is_numeric( $blog ) ) { $blog = get_id_from_blogname( $blog ); }
				if ( $blog ) { switch_to_blog( $blog ); }
				$exclude = array( $post_id );
				if ( is_array( $tags ) ) {
					$tags = join( ',', $tags );
				}
				$args = array( 'tag' => $tags, 'numberposts' => $count, 'exclude' => $exclude, 'post_type' => $post_type, 'orderby' => 'rand' );
				// filter the args
				$args = apply_filters( 'tribe-related-posts-args', $args );
				$posts = get_posts( $args );
				// If result count is not high enough, then find more unrelated posts to fill the extra slots
				if ( $only_display_related==false && count( $posts ) < $count ) {
					foreach ( $posts as $post ) {
						$exclude[] = $post->ID;
					}
					$args = array( 'numberposts' => ( $count-count( $posts ) ), 'exclude' => $exclude, 'post_type' => $post_type, 'orderby' => 'rand' );
					$args = apply_filters( 'tribe-related-posts-args-extra', $args );
					$posts = array_merge( $posts, get_posts( $args ) );
				}
				if ( $blog ) { restore_current_blog(); }
				self::$cache[$post_id] = $posts;
			} else {
				self::$cache[$post_id] = array();
			}
			return self::$cache[$post_id];
		}

		/**
		 * Display the related posts
		 *
		 * @since 1.0
		 * @author Modern Tribe
		 * @param string $tags comma-separated list of tags.
		 * @param int $count number of related posts to return.
		 * @param string $blog the blog from which to fetch the related posts.
		 * @param bool $only_display_related whether to display only related posts or others as well.
		 * @param bool $thumbnails whether to display thumbnails or not.
		 * @param string $post_type the type of post to return.
		 * @return void
		 */
		public function displayPosts( $tag = false, $count = 5, $blog = false, $only_display_related = false, $thumbnails = false, $post_type = 'post' ) {
			// Create an array of types if the user submitted more than one.
			$post_type = explode( ',', $post_type );
			$posts = self::getPosts( $tag, $count, $blog, $only_display_related, $post_type );
			if (is_array( $posts ) && count( $posts ) ) {
				echo '<ul class="tribe-related-posts">';
				foreach ( $posts as $post ) {
					echo '<li>';
					if ( $thumbnails ) {
						$thumb = get_the_post_thumbnail( $post->ID, 'tribe-related-posts-thumbnail' );
						if ( $thumb ) { echo '<div class="tribe-related-posts-thumbnail"><a href="'.get_permalink( $post ).'">'.$thumb.'</a></div>'; }
					}
					echo '<div class="tribe-related-posts-title"><a href="'.get_permalink($post).'">'.get_the_title($post).'</a></div>';
					echo '</li>';
					echo '<hr />';
				}
				echo '</ul>';
			}
		}
	}
}