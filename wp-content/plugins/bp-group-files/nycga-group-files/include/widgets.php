<?php

function nycga_group_files_register_widgets() {
	add_action('widgets_init', create_function('', 'return register_widget("NYCGA_Group_Files_Newest_Widget");') );
	add_action('widgets_init', create_function('', 'return register_widget("NYCGA_Group_Files_Popular_Widget");') );
}
add_action( 'plugins_loaded', 'nycga_group_files_register_widgets' );

class NYCGA_Group_Files_Newest_Widget extends WP_Widget {

	function nycga_group_files_newest_widget() {
		$widget_ops = array('description' => __('The most recently uploaded group files','nycga-group-files'));
		parent::WP_Widget( false, $name = __( 'Recent Group Files', 'nycga-group-files' ),$widget_ops );
	}

	function widget( $args, $instance ) {
		global $bp;

		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title'])?__('Recent Group Files','nycga-group-files'):$instance['title']);
		echo $before_widget;
		echo $before_title .
			 $title .
		     $after_title; ?>

	<?php

	do_action('nycga_group_files_newest_widget_before_html');

	/***
	 * Main HTML Display
	 */

	$document_list = NYCGA_Group_Files::get_list_for_newest_widget( $instance['num_items'], $instance['group_filter'], $instance['featured'] ); 

	if( $document_list && count($document_list) >=1 ) {
		echo '<ul class="group-files-recent">';
		foreach( $document_list as $item ) {
			$document = new NYCGA_Group_Files( $item['id'] );
			$group = new NYCGA_Group_Files( $document->group_id );
			echo '<li>';
			if( $instance['group_filter'] ) { 
				echo '<a href="' . $document->get_url() . '">' . $document->icon() . ' ' . esc_attr( $document->name ) . '</a>';
			} else {
				echo sprintf( __('%s posted in %s','nycga-group-files'),'<a href="' . $document->get_url() . '">' . esc_attr( $document->name ) . '</a>','<a href="' . bp_get_group_permalink( $group ) . '">' . esc_attr( $group->name ) . '</a>');
			}
			echo '</li>';
		}
		echo '</ul>';
	} else {
		echo '<div class="widget-error">' . __('There are no files to display.', 'nycga-group-files') .'</div></p>';	
	}

	?>

	<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		do_action('nycga_group_files_widget_update');

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['group_filter'] = strip_tags( $new_instance['group_filter'] );
		$instance['featured'] = strip_tags( $new_instance['featured'] );
		$instance['num_items'] = strip_tags( $new_instance['num_items'] );

		return $instance;
	}

	function form( $instance ) {
		do_action('nycga_group_files_newest_widget_form');

		$instance = wp_parse_args( (array) $instance, array('title'=> '', 'num_items' => 5 ) );
		$title = esc_attr( $instance['title'] );
		$group_filter = esc_attr( $instance['group_filter'] );
		$featured = esc_attr( $instance['featured'] );
		$num_items = esc_attr( $instance['num_items'] );
		?>

		<p><label><?php _e('Title:','nycga-group-files'); ?></label><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<?php if( NYCGA_GROUP_FILES_WIDGET_GROUP_FILTER ) { ?>
			<p><label><?php _e('Filter by Group:','nycga-group-files'); ?></label>
			<select id="<?php echo $this->get_field_id('group_filter'); ?>" name="<?php echo $this->get_field_name('group_filter'); ?>" >
			<option value="0"><?php _e('Select Group...','nycga-group-files'); ?></option>
			<?php $groups_list = BP_Groups_Group::get_alphabetically();
			foreach( $groups_list['groups'] as $group ) {
				echo '<option value="' . $group->id . '" ';
				if( $group->id == $group_filter ) echo 'selected="selected"';
				echo '>' . stripslashes($group->name) . '</option>';
			} ?>
			</select></p>
		<?php } ?>

		<?php if( NYCGA_GROUP_FILES_FEATURED ) { ?>
			<p><input type="checkbox" id="<?php echo $this->get_field_id('featured'); ?>" name="<?php echo $this->get_field_name('featured'); ?>" value="1" <?php if( $featured ) echo 'checked="checked"'; ?>>
			<label><?php _e('Show featured files only','nycga-group-files'); ?></label></p>
		<?php } ?>

		<p><label><?php _e( 'Number of items to show:', 'nycga-group-files' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'num_items' ); ?>" name="<?php echo $this->get_field_name( 'num_items' ); ?>" type="text" value="<?php echo esc_attr( $num_items ); ?>" style="width: 30%" /></p>

	<?php
	}
}

class NYCGA_Group_Files_Popular_Widget extends WP_Widget {

	function nycga_group_files_popular_widget() {
		$widget_ops = array('description'=> __('The most commonly downloaded group files','nycga-group-files'));
		parent::WP_Widget( false, $name = __( 'Popular Group Files', 'nycga-group-files' ),$widget_ops );
	}

	function widget( $args, $instance ) {
		global $bp;

		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title'])?__('Popular Group Files','nycga-group-files'):$instance['title']);

		echo $before_widget;
		echo $before_title .
			 $title .
		     $after_title; ?>

	<?php

	do_action('nycga_group_files_popular_widget_before_html');

	/***
	 * Main HTML Display
	 */

	$document_list = NYCGA_Group_Files::get_list_for_popular_widget( $instance['num_items'], $instance['group_filter'], $instance['featured'] ); 

	if( $document_list && count($document_list) >=1 ) {
		echo '<ul class="group-files-popular">';
		foreach( $document_list as $item ) {
			$document = new NYCGA_Group_Files( $item['id'] );
			$group = new BP_Groups_Group( $document->group_id );
			echo '<li>';
			if( $instance['group_filter'] ) { 
				echo '<a href="' . $document->get_url() . '">' . $document->icon() . ' ' . esc_attr( $document->name ) . '</a>';
			} else {
				echo sprintf( __('%s posted in %s','nycga-group-files'),'<a href="' . $document->get_url() . '">' . esc_attr( $document->name ) . '</a>','<a href="' . bp_get_group_permalink( $group ) . '">' . esc_attr( $group->name ) . '</a>');
			}
			echo '</li>';
		}
		echo '</ul>';
	} else {
		echo '<div class="widget-error">' . __('There are no files to display.', 'nycga-group-files') .'</div></p>';	
	}

	?>

	<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		do_action('nycga_group_files_newest_widget_update');

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['group_filter'] = strip_tags( $new_instance['group_filter'] );
		$instance['featured'] = strip_tags( $new_instance['featured'] );
		$instance['num_items'] = strip_tags( $new_instance['num_items'] );

		return $instance;
	}

	function form( $instance ) {
		do_action('nycga_group_files_newest_widget_form');

		$instance = wp_parse_args( (array) $instance, array( 'num_items' => 5 ) );
		$title = esc_attr( $instance['title'] );
		$group_filter = esc_attr( $instance['group_filter'] );
		$featured = esc_attr( $instance['featured'] );
		$num_items = esc_attr( $instance['num_items'] );
		?>

		<p><label><?php _e('Title:','nycga-group-files'); ?></label><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<?php if( NYCGA_GROUP_FILES_WIDGET_GROUP_FILTER ) { ?>
			<p><label><?php _e('Filter by Group:','nycga-group-files'); ?></label>
			<select id="<?php echo $this->get_field_id('group_filter'); ?>" name="<?php echo $this->get_field_name('group_filter'); ?>" >
			<option value="0"><?php _e('Select Group...','nycga-group-files'); ?></option>
			<?php $groups_list = BP_Groups_Group::get_alphabetically();
			foreach( $groups_list['groups'] as $group ) {
				echo '<option value="' . $group->id . '" ';
				if( $group->id == $group_filter ) echo 'selected="selected"';
				echo '>' . stripslashes($group->name) . '</option>';
			} ?>
			</select></p>
		<?php } ?>

		<?php if( NYCGA_GROUP_FILES_FEATURED ) { ?>
			<p><input type="checkbox" id="<?php echo $this->get_field_id('featured'); ?>" name="<?php echo $this->get_field_name('featured'); ?>" value="1" <?php if( $featured ) echo 'checked="checked"'; ?>>
			<label><?php _e('Show featured files only','nycga-group-files'); ?></label></p>
		<?php } ?>

		<p><label><?php _e( 'Number of items to show:', 'nycga-group-files' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'num_items' ); ?>" name="<?php echo $this->get_field_name( 'num_items' ); ?>" type="text" value="<?php echo esc_attr( $num_items ); ?>" style="width: 30%" /></p>
	<?php
	}
}

