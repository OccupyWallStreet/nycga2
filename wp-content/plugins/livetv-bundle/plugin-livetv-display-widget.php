<?php 
/*
Plugin Name: liveTV Team - 4 - Widget
Plugin URI: http://kwark.allwebtuts.net
Description: liveTV Team - Display widgets - require 0 + 1 + 2 activated - Activate this part if you need to add a new widget with your livestreams list.
Author: Laurent (KwarK) Bertrand
Version: 1.3.1.1
Author URI: http://kwark.allwebtuts.net
*/

/*  
	Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)
	
	Please consider a small donation for my work. Behind each code, there is a geek who has to eat.
	Thank you for my futur bundle...pizza-cola. Bundle vs bundle, it's a good deal, no ? 
	Small pizza donation @ http://kwark.allwebtuts.net
	
	You can not remove these comments such as my informations.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// disallow direct access to file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	wp_die(__('Sorry, but you cannot access this page directly.', 'livetv'));
}

/**
 * Adds Livetv_Widget widget.
 */
class Livetv_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'livetv_widget', // Base ID
			'LiveTV_widget', // Name
			array( 'description' => __( 'display livestreams widget', 'livetv' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance)
	{
		global $blog_id, $livetv_plugin_path;
		
		$post_to_check = get_page(get_the_ID());
		
		$result = strpos($post_to_check->post_content, '[LivesOnline]');
		
		if($result === false)
		{
			extract( $args );
			
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			
			if(!empty($title))
			{
				echo $before_title . $title . $after_title;
			}
			
			$cache = $livetv_plugin_path . 'cache/temp_'.$blog_id.'_live.html';
			
			if(file_exists($cache))
			{
				readfile($cache);
			}
			
			$widget_offline = get_option('livetv_view_offline'); 
			
			if($widget_offline == 'widget_off')
			{
				wp_enqueue_style('livetv-widget-off');
			}
			
			echo $after_widget;
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'livetv' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

} // class Livetv_Widget
// register Livetv_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "livetv_widget" );' ) );
?>