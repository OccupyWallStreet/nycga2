<?php
/**
 * Plugin Name: LiveStream.com Thumbnail
 * Plugin URI: http://dylan-brady.com/nickbrady
 * Description:
 * Version: 0.1
 * Author: Nick Brady
 * Author URI: http://dylan-brady.com/nickbrady
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'example_load_widgets' );

/**
 * Register our widget.
 * 'LiveStream_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function example_load_widgets() {
	register_widget( 'LiveStream_Widget' );
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class LiveStream_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function LiveStream_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'LiveStream.com Thumbnail', 'description' => __('This widget displays a thumbnail from your stream at www.livestream.com') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'nix-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'nix-widget', __('LiveStream.com Thumbnail', 'example'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$streamurl = $instance['streamurl'];
		$redirecturl = $instance['redirecturl'];
		$showlivestatus = isset( $instance['showlivestatus'] ) ? $instance['showlivestatus'] : false;
		$showdescription = isset( $instance['showdescription'] ) ? $instance['showdescription'] : false;
		$showviewercount = isset( $instance['showviewercount'] ) ? $instance['showviewercount'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		if ( $streamurl ) {
			$thumburl = "http://thumbnail.api.livestream.com/thumbnail?name=" . $streamurl;
			if ( $redirecturl ) {
				echo "<p><a href='$redirecturl'><img src='$thumburl' width='100%' /></a></p>";
			} else {
				echo "<p><a href='http://livestream.com/$streamurl'><img src='$thumburl' width='100%' /></a></p>";
			}
			
		} else {
			printf( '<p>' . __('Invalid Stream URL%1$s', 'example.') . '</p>' );
		}
		
		$feed = file_get_contents("http://x" . $streamurl . "x.api.channel.livestream.com/2.0/info.xml");
		$xml = new SimpleXmlElement($feed);
		foreach ($xml->channel as $entry){
			if ( $showdescription ) {
				printf( '<p>' . __($entry->description) . '</p>' );
			}
			$namespaces = $entry->getNameSpaces(true);
			$ls = $entry->children($namespaces['ls']); 
			$livestatus = $ls->isLive;
			if ( $showlivestatus ) {
				if ( $livestatus == "true" ) {
					printf( '<p>' . __('Live') . '</p>' );
				} else {
					printf( '<p>' . __('Offline') . '</p>' );
				}
			}
			
			$viewercount = $ls->currentViewerCount;
			if ( $showviewercount ) {
				printf( '<p>' . __($viewercount) . ' Viewers</p>' );
			}
			
		}
		
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['streamurl'] = strip_tags( $new_instance['streamurl'] );
		$instance['redirecturl'] = strip_tags( $new_instance['redirecturl'] );
		$instance['showlivestatus'] = strip_tags( $new_instance['showlivestatus'] );

		/* No need to strip tags for sex and show_sex. */
		$instance['showlivestatus'] = ( isset( $new_instance['showlivestatus'] ) ? 1 : 0 );  
		$instance['showdescription'] = ( isset( $new_instance['showdescription'] ) ? 1 : 0 );  
		$instance['showviewercount'] =( isset( $new_instance['showviewercount'] ) ? 1 : 0 );  
		$instance['streamurl'] = $new_instance['streamurl'];
		$instance['redirecturl'] = $new_instance['redirecturl'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
			'title' => __('On Our Stream:', 'example'), 
			'streamurl' => __('Stream URL (ex: electronicarts)', 'example'), 
			'redirecturl' => __('Redirect URL (leave blank for www.livestream.com/YOURURL)', 'example'), 
			'showlivestatus' => true,
			'showviewercount' => true,
			
			'showdescription' => true);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- redirecturl Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'redirecturl' ); ?>"><?php _e('Redirect URL:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'redirecturl' ); ?>" name="<?php echo $this->get_field_name( 'redirecturl' ); ?>" value="<?php echo $instance['redirecturl']; ?>" style="width:100%;" />
		</p>
		
		<!-- streamurl Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'streamurl' ); ?>"><?php _e('Stream URL:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'streamurl' ); ?>" name="<?php echo $this->get_field_name( 'streamurl' ); ?>" value="<?php echo $instance['streamurl']; ?>" style="width:100%;" />
		</p>

		<!-- showlivestatus Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['showlivestatus'], true ); ?> id="<?php echo $this->get_field_id( 'showlivestatus' ); ?>" name="<?php echo $this->get_field_name( 'showlivestatus' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'showlivestatus' ); ?>"><?php _e('Display Live Status?', 'example'); ?></label>
		</p>
		
		<!-- showdescription Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['showdescription'], true ); ?> id="<?php echo $this->get_field_id( 'showdescription' ); ?>" name="<?php echo $this->get_field_name( 'showdescription' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'showdescription' ); ?>"><?php _e('Display Description?', 'example'); ?></label>
		</p>
		
		<!-- showviewercount Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['showviewercount'], true ); ?> id="<?php echo $this->get_field_id( 'showviewercount' ); ?>" name="<?php echo $this->get_field_name( 'showviewercount' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'showviewercount' ); ?>"><?php _e('Display Viewer Count?', 'example'); ?></label>
		</p>

	<?php
	}
}

?>