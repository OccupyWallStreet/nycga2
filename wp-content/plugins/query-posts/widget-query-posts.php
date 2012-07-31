<?php
/**
 * Query Posts Widget.
 * Adds a widget with numerous options using the query_posts() function.
 * In 0.2, converted functions to a class that extends WP 2.8's widget class.
 *
 * @package QueryPosts
 */

/**
 * Output of the Query Posts widget.
 *
 * @since 0.2.0
 */
class Query_Posts_Widget extends WP_Widget {

	/**
	 * PHP4 constructor method.
	 *
	 * @since 0.2.0
	 */
	function Query_Posts_Widget() {
		$widget_ops = array( 'classname' => 'posts', 'description' => __( 'Display posts and pages however you want.', 'query-posts' ) );
		$control_ops = array( 'width' => 810, 'height' => 350, 'id_base' => 'query-posts' );
		$this->WP_Widget( 'query-posts', __( 'Query Posts', 'query-posts' ), $widget_ops, $control_ops );
	}

	/**
	 * Displays the widget on the front end.
	 *
	 * @since 0.2.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Arguments for the query. */
		$args = array();

		/* Widget title and things not in query arguments. */
		if ( $instance['enable_widget_title'] )
			$title = apply_filters('widget_title', $instance['title'] );

		/* Whether to reset $wp_query. */
		$wp_reset_query = $instance['wp_reset_query'] ? true : false;

		/* Whether to use post thumbnails. */
		$the_post_thumbnail = $instance['the_post_thumbnail'] ? true : false;

		/* Whether to show the post title. */
		$show_entry_title = $instance['show_entry_title'] ? true : false;

		/* Whether to show page links. */
		$wp_link_pages = $instance['wp_link_pages'] ? true : false;

		/* Sticky posts. */
		$args['caller_get_posts'] = $instance['caller_get_posts'] ? '1' : '0';

		/* Posts (by post type). */
		$post_types = get_post_types( array( 'publicly_queryable' => true ), 'names' );
		$post__in = array();
		foreach ( $post_types as $type ) {
			if ( isset( $instance[$type] ) && !empty( $instance[$type] ) ) {
				$post__in_new = explode( ',', $instance[$type] );
				$post__in = array_merge( $post__in, $post__in_new );
			}
		}
		$args['post__in'] = $post__in;

		/* Taxonomies. */
		$taxonomies = query_posts_get_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {

			/* If 'category' is the taxonomy. */
			if ( 'category' == $taxonomy && !empty( $instance[$taxonomy] ) )
				$args['cat'] = $instance[$taxonomy];

			/* If 'post_tag' is the taxonomy. */
			elseif ( 'post_tag' == $taxonomy && !empty( $instance[$taxonomy] ) )
				$args['tag'] = $instance[$taxonomy];

			/* All other taxonomies. */
			elseif ( !empty( $instance[$taxonomy] ) ) {
				$the_tax = get_taxonomy( $taxonomy );
				$args[$the_tax->query_var] = $instance[$taxonomy];
			}
		}

		/* Post type. */
		$post_type = (array) $instance['post_type'];
		if ( in_array( 'any', $post_type ) )
			$args['post_type'] = 'any';
		else
			$args['post_type'] = $instance['post_type'];

		/* Post mime type. */
		if ( !empty( $instance['post_mime_type'] ) )
			$args['post_mime_type'] = (array) $instance['post_mime_type'];

		/* Post status. */
		if ( $instance['post_status'] )
			$args['post_status'] = join( ', ', (array) $instance['post_status'] );

		/* Ordering and such. */
		if ( $instance['offset'] )
			$args['offset'] = absint( $instance['offset'] );
		if ( $instance['posts_per_page'] )
			$args['posts_per_page'] = intval( $instance['posts_per_page'] );
		if ( $instance['post_parent'] )
			$args['post_parent'] = absint( $instance['post_parent'] );

		/* Paged. */
		if ( $instance['paged'] )
			$args['paged'] = absint( $instance['paged'] );

		/* Order and orderby. */
		$args['order'] = $instance['order'];
		$args['orderby'] = $instance['orderby'];

		/* Author arguments. */
		if ( $instance['author'] )
			$args['author'] = $instance['author'];

		/* Time arguments. */
		if ( $instance['hour'] )
			$args['hour'] = absint( $instance['hour'] );
		if ( $instance['minute'] )
			$args['minute'] = absint( $instance['minute'] );
		if ( $instance['second'] )
			$args['second'] = absint( $instance['second'] );
		if ( $instance['day'] )
			$args['day'] = absint( $instance['day'] );
		if ( $instance['monthnum'] )
			$args['monthnum'] = absint( $instance['monthnum'] );
		if ( $instance['year'] )
			$args['year'] = absint( $instance['year'] );
		if ( $instance['w'] )
			$args['w'] = absint( $instance['w'] );

		/* Meta arguments. */
		if ( $instance['meta_key'] )
			$args['meta_key'] = strip_tags( $instance['meta_key'] );
		if ( $instance['meta_value'] )
			$args['meta_value'] = $instance['meta_value'];
		if ( $instance['meta_compare'] )
			$args['meta_compare'] = $instance['meta_compare'];

		/* Begin display of widget. */
		if ( 'widget' !== $instance['entry_container'] ) {
			echo $before_widget;

			if ( $title )
				echo $before_title . apply_filters( 'widget_title',  $title, $instance, $this->id_base ) . $after_title;
		}

		/* The global $more is so that the <!--more--> quicktag works. */
		global $more;

		/* Query posts. */
		$new_query = new WP_Query( $args );

		/* If posts were found, let's loop through them. */
		if ( $new_query->have_posts() ) {

			/* Open wrapper. */
			if ( 'ol' == $instance['entry_container'] || 'ul' == $instance['entry_container'] )
				echo "<{$instance['entry_container']}>";

			while ( $new_query->have_posts() ) {

				$new_query->the_post();

				/* Get post class. */
				$post_class = join( ' ', get_post_class( $instance['post_class'] ) );

				/* Post container. */
				if ( 'widget' == $instance['entry_container'] ) {
					$before_widget = preg_replace( '/id="[^"]*"/','', $before_widget );
					echo preg_replace( '/class="[^"]*"/', "class=\"{$post_class}\"", $before_widget );
					if ( $show_entry_title )
						echo $before_title . "<a href='" . get_permalink() . "' title='" . the_title_attribute( 'echo=0' ) . "' rel='bookmark'>" . apply_filters( 'widget_title', the_title( '', '', false ) ) . "</a>" . $after_title;
				}
				elseif ( 'ol' == $instance['entry_container'] || 'ul' == $instance['entry_container'] ) {
					echo "<li class='{$post_class}'>";
				}
				elseif ( !empty( $instance['entry_container'] ) ) {
					echo "<{$instance['entry_container']} class='{$post_class}'>";
				}

				/* Post thumbnails. */
				if ( $the_post_thumbnail && function_exists( 'get_the_image' ) )
					get_the_image( array( 'custom_key' => array( 'Thumbnail', 'thumbnail' ), 'default_size' => $instance['size'] ) );

				elseif ( $the_post_thumbnail && current_theme_supports( 'post-thumbnails' ) ) {
					echo '<a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '">';
					the_post_thumbnail( $instance['size'] );
					echo '</a>';
				}

				/* Entry title. */
				if ( 'widget' !== $instance['entry_container'] && $instance['entry_title'] && $show_entry_title )
					the_title( "<{$instance['entry_title']} class='entry-title'><a href='" . get_permalink() . "' title='" . the_title_attribute( 'echo=0' ) . "' rel='bookmark'>", "</a></{$instance['entry_title']}>" );

				elseif ( 'widget' !== $instance['entry_container'] && $show_entry_title )
					the_title( "<a href='" . get_permalink() . "' title='" . the_title_attribute( 'echo=0' ) . "' rel='bookmark'>", "</a>" );

				/* Byline. */
				if ( $instance['byline'] )
					echo do_shortcode( "<p class='byline'>{$instance['byline']}</p>" );

				/* Content/Excerpt. */
				if ( 'the_content' == $instance['entry_content'] ) {
					echo '<div class="entry-content">';
					$more = 0; // Make sure <!--more--> link works
					the_content( $instance['more_link_text'] );
					echo '</div>';
				}
				elseif ( 'the_excerpt' == $instance['entry_content'] ) {
					echo '<div class="entry-summary">';
					the_excerpt();
					echo '</div>';
				}

				/* <!--nextpage--> links. */
				if ( $wp_link_pages )
					wp_link_pages( array( 'before' => '<p class="pages page-links">' . __( 'Pages:', 'query-posts' ), 'after' => '</p>' ) );

				/* Entry meta. */
				if ( $instance['entry_meta'] )
					echo do_shortcode( "<p class='entry-meta'>{$instance['entry_meta']}</p>" );

				/* Close post container. */
				if ( 'widget' == $instance['entry_container'] )
					echo $after_widget;
				elseif ( 'ol' == $instance['entry_container'] || 'ul' == $instance['entry_container'] )
					echo '</li>';
				elseif ( !empty( $instance['entry_container'] ) )
					echo "</{$instance['entry_container']}>";
			}

			/* Close wrapper. */
			if ( 'ol' == $instance['entry_container'] || 'ul' == $instance['entry_container'] )
				echo "</{$instance['entry_container']}>";
		}

		/* If no posts were found and there is a custom error message. */
		elseif ( $instance['error_message'] ) {

			/* Post container. */
			if ( 'widget' == $instance['entry_container'] ) {
				$before_widget = preg_replace( '/id="[^"]*"/','id="post-0"', $before_widget );
				echo preg_replace( '/class="[^"]*"/', "class=\"hentry\"", $before_widget );
			}
			elseif ( 'ol' == $instance['entry_container'] || 'ul' == $instance['entry_container'] )
				echo "<{$instance['entry_container']}><li id='post-0'>";
			else
				echo "<{$instance['entry_container']} id='post-0'>";

			/* Output error message. */
			echo '<div class="entry-content"><p>' . do_shortcode( $instance['error_message'] ) . '</p></div>';

			/* Close post container. */
			if ( 'widget' == $instance['entry_container'] )
				echo $after_widget;
			elseif ( 'ol' == $instance['entry_container'] || 'ul' == $instance['entry_container'] )
				echo "</li></{$instance['entry_container']}>";
			else
				echo "</{$instance['entry_container']}>";
		}

		/* Reset query. */
		if ( $wp_reset_query )
			wp_reset_query();

		/* Close widget. */
		if ( 'widget' !== $instance['entry_container'] )
			echo $after_widget;
	}

	/**
	 * Saves the widget settings.
	 *
	 * @since 0.2.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;

		/* Strip tags from elements that don't need them. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['offset'] = strip_tags( $new_instance['offset'] );
		$instance['posts_per_page'] = strip_tags( $new_instance['posts_per_page'] );
		$instance['paged'] = strip_tags( $new_instance['paged'] );
		$instance['post_parent'] = strip_tags( $new_instance['post_parent'] );
		$instance['p'] = strip_tags( $new_instance['p'] );
		$instance['year'] = strip_tags( $new_instance['year'] );

		/* Checkboxes. */
		$instance['the_post_thumbnail'] = ( isset( $new_instance['the_post_thumbnail'] ) ? 1 : 0 );
		$instance['wp_reset_query'] = ( isset( $new_instance['wp_reset_query'] ) ? 1 : 0 );
		$instance['caller_get_posts'] = ( isset( $new_instance['caller_get_posts'] ) ? 1 : 0 );
		$instance['show_entry_title'] = ( isset( $new_instance['show_entry_title'] ) ? 1 : 0 );
		$instance['enable_widget_title'] = ( isset( $new_instance['enable_widget_title'] ) ? 1 : 0 );
		$instance['wp_link_pages'] = ( isset( $new_instance['wp_link_pages'] ) ? 1 : 0 );

		/* If individual posts are widgets, disable the widget title. */
		if ( 'widget' == $new_instance['entry_container'] )
			$instance['enable_widget_title'] = false;

		return $instance;
	}

	/**
	 * Displays the widget settings form.
	 *
	 * @since 0.2.0
	 */
	function form( $instance ) {

		/* Set up the defaults. */
		$defaults = array(
			'title' => '',
			'display' => 'ul',
			'post_status' => array( 'publish' ),
			'post_type' => array( 'post' ),
			'post_mime_type' => array( '' ),
			'order' => 'DESC',
			'orderby' => 'date',
			'caller_get_posts' => true,
			'posts_per_page' => get_option( 'posts_per_page' ),
			'offset' => '0',
			'paged' => '',
			'post_parent' => '',
			'meta_key' => '',
			'meta_value' => '',
			'author' => '',
			'wp_reset_query' => true,
			'meta_compare' => '',
			'year' => '',
			'monthnum' => '',
			'w' => '',
			'day' => '',
			'hour' => '',
			'minute' => '',
			'second' => '',
			'enable_widget_title' => true,
			'entry_container' => 'div',
			'post_class' => '',
			'the_post_thumbnail' => true,
			'size' => 'thumbnail',
			'show_entry_title' => true,
			'entry_title' => 'h2',
			'byline' => __( 'By [entry-author] on [entry-published]', 'query-posts' ),
			'entry_content' => 'the_excerpt',
			'more_link_text' => __( 'Continue reading...', 'query-posts' ),
			'wp_link_pages' => true,
			'entry_meta' => __( '[entry-terms taxonomy="category" before="Category: "]', 'query-posts' ),
			'error_message' => __( 'Apologies, but no results were found.', 'query-posts' )
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div style="float:left;width:18.4%;"><?php

		/* Widget title. */
		query_posts_input_text_small( 'title', $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $instance['title'] );

		/* Order. */
		query_posts_select_single( 'order', $this->get_field_id( 'order' ), $this->get_field_name( 'order' ), $instance['order'], array( 'ASC' => __( 'Ascending', 'query-posts' ), 'DESC' => __( 'Descending', 'query-posts' ) ), false );

		/* Order By. */
		$orderby_options = array( 'author' => __( 'Author', 'query-posts' ), 'comment_count' => __( 'Comment Count', 'query-posts' ), 'date' => __( 'Date', 'query-posts' ), 'ID' => __( 'ID', 'query-posts' ), 'menu_order' => __( 'Menu Order', 'query-posts' ), 'meta_value' => __( 'Meta Value', 'query-posts' ), 'modified' => __( 'Modified', 'query-posts' ), 'none' => __( 'None', 'query-posts' ), 'parent' => __( 'Parent', 'query-posts' ), 'rand' => __( 'Random', 'query-posts' ), 'title' => __( 'Title', 'query-posts' ) );
		query_posts_select_single( 'orderby', $this->get_field_id( 'orderby' ), $this->get_field_name( 'orderby' ), $instance['orderby'], $orderby_options, false );

		/* Post statuses. */
		$stati = get_post_stati( '', 'objects' );
		foreach ( $stati as $status )
			$statuses[$status->name] = $status->label;
		$statuses['inherit'] = __( 'Inherit', 'query-posts' );
		query_posts_select_multiple( 'post_status', $this->get_field_id( 'post_status' ), $this->get_field_name( 'post_status' ), $instance['post_status'], $statuses, false );

		/* Post types. */
		$post_types = array( 'any' => __( 'Any', 'query-posts' ) );
		foreach ( get_post_types( array( 'publicly_queryable' => true ), 'objects' ) as $post_type ) {
			$type_label = ( ( $post_type->labels->singular_name ) ? $post_type->labels->singular_name : $post_type->labels->name );
			$post_types[$post_type->name] = $type_label;
		}
		query_posts_select_multiple( 'post_type', $this->get_field_id( 'post_type' ), $this->get_field_name( 'post_type' ), $instance['post_type'], $post_types, false );

		?></div>

		<div style="float:left;width:18.4%;margin-left:2%;"><?php

		/* Posts per page. */
		query_posts_input_text_small( 'posts_per_page', $this->get_field_id( 'posts_per_page' ), $this->get_field_name( 'posts_per_page' ), $instance['posts_per_page'] );

		/* Offset. */
		query_posts_input_text_small( 'offset', $this->get_field_id( 'offset' ), $this->get_field_name( 'offset' ), $instance['offset'] );

		/* Paged. */
		query_posts_input_text_small( 'paged', $this->get_field_id( 'paged' ), $this->get_field_name( 'paged' ), $instance['paged'] );

		/* Parent. */
		query_posts_input_text_small( 'post_parent', $this->get_field_id( 'post_parent' ), $this->get_field_name( 'post_parent' ), $instance['post_parent'] );

		/* Post mime type. */
		$post_mime_types = get_available_post_mime_types();
		foreach ( $post_mime_types as $post_mime_type )
			$mime_types[$post_mime_type] = $post_mime_type;
		query_posts_select_multiple( 'post_mime_type', $this->get_field_id( 'post_mime_type' ), $this->get_field_name( 'post_mime_type' ), $instance['post_mime_type'], $mime_types, false );

		/* Meta key. */
		foreach ( get_meta_keys() as $meta )
			$meta_keys[$meta] = $meta;
		query_posts_select_single( 'meta_key', $this->get_field_id( 'meta_key' ), $this->get_field_name( 'meta_key' ), $instance['meta_key'], $meta_keys, true );

		/* Meta value. */
		query_posts_input_text_small( 'meta_value', $this->get_field_id( 'meta_value' ), $this->get_field_name( 'meta_value' ), $instance['meta_value'] );

		/* Meta compare. */
		$operators = array( '=' => '=', '!=' => '!=', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=' );
		query_posts_select_single( 'meta_compare', $this->get_field_id( 'meta_compare' ), $this->get_field_name( 'meta_compare' ), $instance['meta_compare'], $operators, true, 'smallfat', 'float:right;' );

		?></div>

		<div style="float:left;width:18.4%;margin-left:2%;"><?php

		/* Authors. */
		query_posts_input_text( 'author', $this->get_field_id( 'author' ), $this->get_field_name( 'author' ), $instance['author'] );

		/* Year. */
		query_posts_input_text_small( 'year', $this->get_field_id( 'year' ), $this->get_field_name( 'year' ), $instance['year'] );

		/* Months. */
		query_posts_select_single( 'monthnum', $this->get_field_id( 'monthnum' ), $this->get_field_name( 'monthnum' ), $instance['monthnum'], range( 1, 12 ), true, 'smallfat', 'float:right;' );

		/* Weeks. */
		query_posts_select_single( 'w', $this->get_field_id( 'w' ), $this->get_field_name( 'w' ), $instance['w'], range( 1, 53 ), true, 'smallfat', 'float:right;' );

		/* Days. */
		query_posts_select_single( 'day', $this->get_field_id( 'day' ), $this->get_field_name( 'day' ), $instance['day'], range( 1, 31 ), true, 'smallfat', 'float:right;' );

		/* Hours. */
		query_posts_select_single( 'hour', $this->get_field_id( 'hour' ), $this->get_field_name( 'hour' ), $instance['hour'], range( 1, 23 ), true, 'smallfat', 'float:right;' );

		/* Minutes. */
		query_posts_select_single( 'minute', $this->get_field_id( 'minute' ), $this->get_field_name( 'minute' ), $instance['minute'], range( 1, 60 ), true, 'smallfat', 'float:right;' );

		/* Seconds. */
		query_posts_select_single( 'second', $this->get_field_id( 'second' ), $this->get_field_name( 'second' ), $instance['second'], range( 1, 60 ), true, 'smallfat', 'float:right;' );

		/* Stickies. */
		query_posts_input_checkbox( __( 'Disable sticky posts', 'query-posts' ), $this->get_field_id( 'caller_get_posts' ), $this->get_field_name( 'caller_get_posts' ), checked( $instance['caller_get_posts'], true, false ) );

		/* Pagination / Reset Query. */
		query_posts_input_checkbox( __( 'Reset query', 'query-posts' ), $this->get_field_id( 'wp_reset_query' ), $this->get_field_name( 'wp_reset_query' ), checked( $instance['wp_reset_query'], true, false ) );

		?></div>

		<div style="float:left;width:18.4%;margin-left:2%;"><?php

		/* Show widget title. */
		query_posts_input_checkbox( __( 'Enable widget title', 'query-posts' ), $this->get_field_id( 'enable_widget_title' ), $this->get_field_name( 'enable_widget_title' ), checked( $instance['enable_widget_title'], true, false ) );

		/* Post container. */
		$containers = array( 'widget' => 'widget', 'div' => 'div', 'ul' => 'ul', 'ol' => 'ol' );
		query_posts_select_single( 'entry_container', $this->get_field_id( 'entry_container' ), $this->get_field_name( 'entry_container' ), $instance['entry_container'], $containers, true );

		/* Post class. */
		query_posts_input_text_small( 'post_class', $this->get_field_id( 'post_class' ), $this->get_field_name( 'post_class' ), $instance['post_class'] );


		/* Only show thumbnail settings if supported. */
		if ( current_theme_supports( 'post-thumbnails' ) || function_exists( 'get_the_image' ) ) {

			/* Post thumbnails. */
			query_posts_input_checkbox( __( 'Enable post thumbnails', 'query-posts' ), $this->get_field_id( 'the_post_thumbnail' ), $this->get_field_name( 'the_post_thumbnail' ), checked( $instance['the_post_thumbnail'], true, false ) );

			/* Thumbnail size. */
			$sizes = array();
			foreach ( get_intermediate_image_sizes() as $image_size )
				$sizes[$image_size] = $image_size;
			query_posts_select_single( 'size', $this->get_field_id( 'size' ), $this->get_field_name( 'size' ), $instance['size'], $sizes, false );
		}

		/* Entry title. */
		query_posts_input_checkbox( __( 'Enable entry titles', 'query-posts' ), $this->get_field_id( 'show_entry_title' ), $this->get_field_name( 'show_entry_title' ), checked( $instance['show_entry_title'], true, false ) );

		/* Entry title markup. */
		$elements = array( 'h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6', 'p' => 'p', 'div' => 'div', 'span' => 'span' );
		query_posts_select_single( 'entry_title', $this->get_field_id( 'entry_title' ), $this->get_field_name( 'entry_title' ), $instance['entry_title'], $elements, true, 'smallfat', 'float:right;' );

		?></div>

		<div style="float:left;width:18.4%;margin-left:2%;"><?php

		/* Entry byline. */
		query_posts_input_text( 'byline', $this->get_field_id( 'byline' ), $this->get_field_name( 'byline' ), $instance['byline'] );

		/* Post content. */
		query_posts_select_single( 'entry_content', $this->get_field_id( 'entry_content' ), $this->get_field_name( 'entry_content' ), $instance['entry_content'], array( 'the_content' => __( 'Content', 'query-posts' ), 'the_excerpt' => __( 'Excerpt', 'query-posts' ) ), true );

		/* More link text. */
		query_posts_input_text( '&lt;!--more-->', $this->get_field_id( 'more_link_text' ), $this->get_field_name( 'more_link_text' ), $instance['more_link_text'] );

		/* Page links wp_link_pages(). */
		query_posts_input_checkbox( __( 'Enable page links', 'query-posts' ), $this->get_field_id( 'wp_link_pages' ), $this->get_field_name( 'wp_link_pages' ), checked( $instance['wp_link_pages'], true, false ) );

		/* Entry metadata. */
		query_posts_input_text( 'entry_meta', $this->get_field_id( 'entry_meta' ), $this->get_field_name( 'entry_meta' ), $instance['entry_meta'] );

		/* Error message. */
		query_posts_textarea( 'error_message', $this->get_field_id( 'error_message' ), $this->get_field_name( 'error_message' ), $instance['error_message'] );

		?></div>

		<div style="clear: left;"><?php

		/* Posts by post_type. */
		$post_types = get_post_types( array( 'publicly_queryable' => true ), 'names' );
		$i = 0;

		foreach ( $post_types as $type ) {
			echo '<div style="float:left;width:18.4%;margin-right:' . ( ++$i % 5 ? '2' : '0' ) . '%;">';
			query_posts_input_text( $type, $this->get_field_id( $type ), $this->get_field_name( $type ), ( isset( $instance[$type] ) ? $instance[$type] : '' ) );
			echo '</div>';
		}

		?></div><div style="clear: left;"><?php

		/* Taxonomies. */
		$taxonomies = query_posts_get_taxonomies();
		$i = 0;
		foreach ( $taxonomies as $taxonomy ) {
			echo '<div style="float:left;width:18.4%;margin-right:' . ( ++$i % 5 ? '2' : '0' ) . '%;">';
			query_posts_input_text( $taxonomy, $this->get_field_id( $taxonomy ), $this->get_field_name( $taxonomy ), ( isset( $instance[$taxonomy] ) ? $instance[$taxonomy] : '' ) );
			echo '</div>';
		}

		?></div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>