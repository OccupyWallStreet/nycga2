<?php
/**
 * Shows all Blog categories.
 */
class Bcat_WidgetCloud extends WP_Widget {

	function Bcat_WidgetCloud () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows a tag cloud of Site Categories.', SITE_CATEGORIES_I18N_DOMAIN));
		parent::WP_Widget(__CLASS__, __('Site Categories Cloud', SITE_CATEGORIES_I18N_DOMAIN), $widget_ops);
	}

	function form($instance) {

		// Set defaults
		// ...
		$defaults = array( 
			'title' 			=> 	'',
			'number'			=>	'',
			'orderby'			=>	'name',
			'order'				=>	'ASC',
			'smallest'			=>	'8',
			'largest'			=>	'22',
			'unit'				=>	'pt',
			'category'			=>	'',
			'include_parent'	=>	'on'
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		//echo "instance<pre>"; print_r($instance); echo "</pre>";
		
		if (!empty($instance['per_page'])) {
			$instance['per_page'] = intval($instance['per_page']);
		}
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title') ?>"><?php _e('Title:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" 
				class="widefat" value="<?php echo $instance['title'] ?> "/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php 
				_e('Number of item to show: (blank for all)', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

			<input type="text" id="<?php echo $this->get_field_id( 'number' ); ?>" value="<?php echo $instance['number']; ?>"
				name="<?php echo $this->get_field_name( 'number'); ?>" class="widefat" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php 
				_e('Show Site Categories:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

			<?php
				$bcat_args = array(
					'taxonomy'			=> 	SITE_CATEGORIES_TAXONOMY,
					'hierarchical'		=>	true,
					'hide_empty'		=>	false,
					'show_option_none'	=>	__('All Categories', SITE_CATEGORIES_I18N_DOMAIN), 
					'show_count'		=>	1,
					'name'				=>	$this->get_field_name( 'category'),
					'selected'			=>	$instance['category'],
					'class'				=>	'bcat_category_widget',
					
				);

				wp_dropdown_categories( $bcat_args ); 
			?><input type="checkbox" id="<?php echo $this->get_field_id( 'include_parent' ); ?>" <?php if ($instance['include_parent'] == "on") {
				echo ' checked="checked" '; } ?>
				name="<?php echo $this->get_field_name( 'include_parent'); ?>"  /> <label for="<?php echo $this->get_field_id( 'include_parent' ); ?>"><?php 
				_e('Include Parent:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('orderby') ?>"><?php _e('Ordering:', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" 
				name="<?php echo $this->get_field_name( 'orderby'); ?>" class="widefat" style="width:60%;">
				<option value="name" <?php if ($instance['orderby'] == "name") { echo ' selected="selected" '; }?>><?php 
					_e('Name', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="count" <?php if ($instance['orderby'] == "count") { echo ' selected="selected" '; }?>><?php 
					_e('Count', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
			</select>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" 
				name="<?php echo $this->get_field_name( 'order'); ?>" class="widefat" style="width:25%;">
				<option value="ASC" <?php if ($instance['order'] == "ASC") { echo ' selected="selected" '; }?>><?php 
					_e('ASC', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="DESC" <?php if ($instance['order'] == "DESC") { echo ' selected="selected" '; }?>><?php 
					_e('DESC', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="RAND" <?php if ($instance['order'] == "RAND") { echo ' selected="selected" '; }?>><?php 
					_e('Random', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'smallest' ); ?>"><?php 
				_e('Small Font size:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

			<input type="text" id="<?php echo $this->get_field_id( 'smallest' ); ?>" value="<?php echo $instance['smallest']; ?>"
				name="<?php echo $this->get_field_name( 'smallest'); ?>" class="widefat" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'largest' ); ?>"><?php 
				_e('Large Font size:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

			<input type="text" id="<?php echo $this->get_field_id( 'largest' ); ?>" value="<?php echo $instance['largest']; ?>"
				name="<?php echo $this->get_field_name( 'largest'); ?>" class="widefat" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'unit' ); ?>"><?php 
				_e('Font size unit:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

			<select id="<?php echo $this->get_field_id( 'unit' ); ?>" 
				name="<?php echo $this->get_field_name( 'unit'); ?>" class="widefat" style="width:100%;">
				<option value="%" <?php if ($instance['unit'] == "%") { echo ' selected="selected" '; }?>><?php 
					_e('% Percentage', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="in" <?php if ($instance['unit'] == "in") { echo ' selected="selected" '; }?>><?php 
					_e('in Inch', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="cm" <?php if ($instance['unit'] == "cm") { echo ' selected="selected" '; }?>><?php 
					_e('cm Centimeter', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="mm" <?php if ($instance['unit'] == "mm") { echo ' selected="selected" '; }?>><?php 
					_e('mm Millimeter', SITE_CATEGORIES_I18N_DOMAIN); ?></option>					
				<option value="em" <?php if ($instance['unit'] == "em") { echo ' selected="selected" '; }?>><?php 
					_e('em', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="ex" <?php if ($instance['unit'] == "ex") { echo ' selected="selected" '; }?>><?php 
					_e('ex', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="pt" <?php if ($instance['unit'] == "pt") { echo ' selected="selected" '; }?>><?php 
					_e('pt Point', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="pc" <?php if ($instance['unit'] == "pc") { echo ' selected="selected" '; }?>><?php 
					_e('pc Pica', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="px" <?php if ($instance['unit'] == "px") { echo ' selected="selected" '; }?>><?php 
					_e('px Pixels', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
			</select>
		</p>

		<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] 				= strip_tags($new_instance['title']);

		if (!empty($new_instance['category']))
			$instance['category'] 		= intval($new_instance['category']);
		else
			$instance['category']			= '';


		if (isset($new_instance['include_parent']))
			$instance['include_parent'] 		= esc_attr($new_instance['include_parent']);
		else
			$instance['include_parent']			= '';


		if (!empty($new_instance['number']))
			$instance['number'] 		= intval($new_instance['number']);
		else
			$instance['number']			= '';
			

		if (isset($new_instance['orderby']))			
			$instance['orderby'] 		= esc_attr($new_instance['orderby']);
		else
			$instance['orderby']		= 'name';

			
		if (isset($new_instance['order']))
			$instance['order'] 			= esc_attr($new_instance['order']);
		else
			$instance['order']			= 'ASC';
			
			
		if (!empty($new_instance['smallest']))
			$instance['smallest'] 		= intval($new_instance['smallest']);
		else
			$instance['smallest']		= 8;


		if (!empty($new_instance['largest']))
			$instance['largest'] 		= intval($new_instance['largest']);
		else
			$instance['largest']		= 22;


		if (!empty($new_instance['unit']))
			$instance['unit'] 			= esc_attr($new_instance['unit']);
		else
			$instance['unit']		= "pt";
		
		delete_site_transient( 'site-categories-cloud-data-'. $this->number );
		return $instance;
	}

	function widget($args, $instance) {

		global $site_categories, $current_site;		
		
		$site_categories->load_config();
		extract($args);

		$data = get_site_transient( 'site-categories-cloud-data-'. $this->number );
		if (!$data) {
			
			switch_to_blog( $current_site->blog_id );

			$defaults = array(
				'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
				'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
				'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => true
			);
			$instance = wp_parse_args( $instance, $defaults );
			if ((isset($instance['category'])) && (intval($instance['category']))) {
				$instance['child_of'] 	= $instance['category'];
			}
			
			$tags = get_terms( SITE_CATEGORIES_TAXONOMY, $instance); // Always query top tags
			
			if ((isset($instance['include_parent']))	&& ($instance['include_parent'] == "on")) {
				$parent_tag = get_term_by('id', $instance['category'], SITE_CATEGORIES_TAXONOMY);
				if ( !empty( $parent_tag ) && !is_wp_error( $parent_tag ) ) {
					//echo "parent_tag<pre>"; print_r($parent_tag); echo "</pre>";
					$tags[] = $parent_tag;
				}
			}

			if ( empty( $tags ) || is_wp_error( $tags ) )
				return;

			foreach ( $tags as $key => $tag ) {
				
				$tags[ $key ]->id = $tag->term_id;
				if ((isset($site_categories->opts['landing_page_rewrite'])) && ($site_categories->opts['landing_page_rewrite'] == true)) {
					$tags[ $key ]->link = trailingslashit($site_categories->opts['landing_page_slug']) . $tag->slug;
				} else {
					$tags[ $key ]->link = $site_categories->opts['landing_page_slug'] .'&amp;category_name=' . $tag->slug;
				}
			}
			$data = wp_generate_tag_cloud( $tags, $instance ); // Here's where those top tags get sorted according to $args

			restore_current_blog();
			
			set_site_transient( 'site-categories-cloud-data-'. $this->number, $data, 30);
		}
		
		if ($data) {
			echo $before_widget;

			$title = apply_filters('widget_title', $instance['title']);
			if ($title) echo $before_title . $title . $after_title;
		
			echo $data;
		
			echo $after_widget;
			
		}
	}
}

