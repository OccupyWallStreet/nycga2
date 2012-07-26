<?php

/**
 * Custom Widget Display
 *
 * This file extends the WP_Widget_Factory and WP_Widget classes to provide
 * multi-instance widgets for posts
 *
 * @author Timothy Wood @codearachnid
 * @copyright Modern Tribe, Inc.
 * @package Tribe_Widget_Builder
 **/

// Block direct requests
if ( !defined('ABSPATH') )
	die();

if ( ! class_exists('Tribe_Widget_Builder_Display') ) {
	class Tribe_Widget_Builder_Display extends WP_Widget {

		var $token;
		var $widget_builder;

		/**
		 *
		 * Tribe_Widget_Builder Constructor
		 * requires Tribe_WP_Widget_Factory to extend the register method to pass args through
		 *
		 */
		function Tribe_Widget_Builder_Display($param = null) {
			extract($param);
			$this->token = $token;

			// new instance of the widget builder class
			$this->widget_builder = Tribe_Widget_Builder::get_instance();

			// blank out the widget description so that it doesn't duplicate the widget title
			$widget_description = empty($widget_description) ? ' ' : $widget_description;

			// override default post title by filter for customization
			$title = ( $title != '' ) ? apply_filters('tribe_widget_builder_title', $title) : $title;

			// allow for class overrides
			$classname = apply_filters( 'tribe_widget_builder_classes', array( 'widget_' . $this->token . '_' . $ID ) );

			$widget_ops = array( 'classname' => implode(" ", $classname), 'description' => __($widget_description, 'widget-builder'), 'data' => $param );
			$control_ops = array( 'width' => 200, 'height' => 200, 'id_base' => 'widget_' . $this->token . '_' . $ID );
			parent::__construct( 'widget_' . $this->token . '_' . $ID, __($title, 'widget-builder'), $widget_ops, $control_ops );
		}

		/**
		 * widget function.
		 *
		 * @access public
		 * @return void
		 */
		public function widget( $args, $instance ) {

			global $wp_registered_widgets;
			extract($args);
			extract($wp_registered_widgets[ $widget_id ]['data']);

			// apply filters
			$content = apply_filters( 'the_content', empty( $content ) ? '' : $content );
			$content = str_replace(']]>', ']]&gt;', $content);
			$title = apply_filters( 'the_title', empty( $title ) ? '' : $title );

			// get template hierarchy
			include( $this->widget_builder->get_template_hierarchy( 'widget' ) );

		}

		/**
		 * form function.
		 *
		 * @access public
		 * @return void
		 */
		public function form(){
		}

	}
}