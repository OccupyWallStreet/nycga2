<?php
	/**
	 * Add function to widgets_init that'll load our widget.
	 * @since 0.1
	 */
	add_action( 'widgets_init', 'shortcodes_ultimate_load_widgets' );

	/**
	 * Register widget
	 */
	if ( !function_exists( 'shortcodes_ultimate_load_widgets' ) ) {

		function shortcodes_ultimate_load_widgets() {
			register_widget( 'Shortcodes_Ultimate_Widget' );
		}

	}

	/**
	 * Example Widget class.
	 * This class handles everything that needs to be handled with the widget:
	 * the settings, form, display, and update.  Nice!
	 *
	 * @since 0.1
	 */
	if ( !class_exists( 'Shortcodes_Ultimate_Widget' ) ) {

		class Shortcodes_Ultimate_Widget extends WP_Widget {

			/**
			 * Widget setup.
			 */
			function Shortcodes_Ultimate_Widget() {
				/* Widget settings. */
				$widget_ops = array( 'classname' => 'shortcodes-ultimate', 'description' => __( 'Special Shortcodes Ultimate widget', 'shortcodes-ultimate' ) );

				/* Widget control settings. */
				$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'shortcodes-ultimate' );

				/* Create the widget. */
				$this->WP_Widget( 'shortcodes-ultimate', __( 'Shortcodes Ultimate', 'shortcodes-ultimate' ), $widget_ops, $control_ops );
			}

			/**
			 * How to display the widget on the screen.
			 */
			function widget( $args, $instance ) {
				extract( $args );

				/* Our variables from the widget settings. */
				$title = apply_filters( 'widget_title', $instance['title'] );
				$content = $instance['content'];

				/* Before widget (defined by themes). */
				echo $before_widget;

				/* Display the widget title if one was input (before and after defined by themes). */
				if ( $title )
					echo $before_title . $title . $after_title;

				/* Display name from widget settings if one was input. */
				echo '<div class="textwidget">' . do_shortcode( $content ) . '</div>';

				/* After widget (defined by themes). */
				echo $after_widget;
			}

			/**
			 * Update the widget settings.
			 */
			function update( $new_instance, $old_instance ) {
				$instance = $old_instance;

				/* Strip tags for title */
				$instance['title'] = strip_tags( $new_instance['title'] );
				$instance['content'] = $new_instance['content'];

				return $instance;
			}

			/**
			 * Displays the widget settings controls on the widget panel.
			 * Make use of the get_field_id() and get_field_name() function
			 * when creating your form elements. This handles the confusing stuff.
			 */
			function form( $instance ) {

				/* Set up some default widget settings. */
				$defaults = array( 'title' => __( 'Shortcodes Ultimate', 'shortcodes-ultimate' ), 'content' => '' );
				$instance = wp_parse_args( ( array ) $instance, $defaults );
				?>

				<!-- Widget Title: Text Input -->
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'shortcodes-ultimate' ); ?></label>
					<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
				</p>

				<!-- Content: Textarea -->
				<p>
				<?php su_add_generator_button( 'widget', $this->get_field_id( 'content' ) ); ?><br/>
					<textarea name="<?php echo $this->get_field_name( 'content' ); ?>" id="<?php echo $this->get_field_id( 'content' ); ?>" rows="7" class="widefat"><?php echo $instance['content']; ?></textarea>
				</p>

				<?php
			}

		}

	}
?>