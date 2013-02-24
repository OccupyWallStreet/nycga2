<?php
/**
 * Shows all Blog categories.
 */
class Bcat_WidgetCategories extends WP_Widget {

	function Bcat_WidgetCategories () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows a list of all site categories.', SITE_CATEGORIES_I18N_DOMAIN));
		parent::WP_Widget(__CLASS__, __('Site Categories', SITE_CATEGORIES_I18N_DOMAIN), $widget_ops);
	}

	function form($instance) {

		// Set defaults
		// ...
		$defaults = array( 
			'title' 				=> 	'',
			'per_page'				=>	5,
			'ordering'				=>	'name',
			'order'					=>	'ASC',
			'show_style'			=>	'ul',
			'hide_empty'			=>	0,
			'show_counts'			=>	1,
			'icon_show'				=>	1,
			'icon_size'				=>	32,
			'show_more_link'		=>	1,
			'landing_link_label'	=>	__('more categories', SITE_CATEGORIES_I18N_DOMAIN)
			
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		//echo "instance<pre>"; print_r($instance); echo "</pre>";
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title') ?>"><?php _e('Title:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" 
				class="widefat" value="<?php echo $instance['title'] ?> "/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show_style') ?>"><?php _e('Display as:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			<select id="<?php echo $this->get_field_id( 'show_style' ); ?>" 
				name="<?php echo $this->get_field_name( 'show_style'); ?>" class="widefat" style="width:100%;">
				<option value="ol" <?php if ($instance['show_style'] == "ol") { echo ' selected="selected" '; } ?>><?php 
					_e('Ordered List (ol)', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="ul" <?php if ($instance['show_style'] == "ul") { echo ' selected="selected" '; } ?>><?php 
					_e('Unordered List (ul)', SITE_CATEGORIES_I18N_DOMAIN); ?></option>

<?php /* >
				<option value="select" <?php if ($instance['show_style'] == "select") { echo ' selected="selected" '; } ?>><?php 
					_e('Dropdown (select)', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
<?php */ ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'per_page' ); ?>"><?php 
				_e('Number of Site Categories to show:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

			<input type="text" id="<?php echo $this->get_field_id( 'per_page' ); ?>" value="<?php echo intval($instance['per_page']); ?>"
				name="<?php echo $this->get_field_name( 'per_page'); ?>" class="widefat" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('ordering') ?>"><?php _e('Ordering:', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
			<select id="<?php echo $this->get_field_id( 'ordering' ); ?>" 
				name="<?php echo $this->get_field_name( 'ordering'); ?>" class="widefat" style="width:60%;">
				<option value="name" <?php if ($instance['ordering'] == "name") { echo ' selected="selected" '; }?>><?php 
					_e('Name', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="id" <?php if ($instance['ordering'] == "id") { echo ' selected="selected" '; }?>><?php 
					_e('Category ID', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
			</select>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" 
				name="<?php echo $this->get_field_name( 'order'); ?>" class="widefat" style="width:25%;">
				<option value="ASC" <?php if ($instance['order'] == "ASC") { echo ' selected="selected" '; }?>><?php 
					_e('ASC', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				<option value="DESC" <?php if ($instance['order'] == "DESC") { echo ' selected="selected" '; }?>><?php 
					_e('DESC', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('hide_empty') ?>"><?php _e('Hide Empty Site Categories:', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
			<input type="radio" name="<?php echo $this->get_field_name( 'hide_empty'); ?>" id="<?php echo $this->get_field_id('hide_empty') ?>_yes" 
				value="1" <?php if ($instance['hide_empty'] == "1") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('hide_empty') ?>_yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			
			<input type="radio" name="<?php echo $this->get_field_name( 'hide_empty'); ?>" id="<?php echo $this->get_field_id('hide_empty') ?>_no" 
				value="0" <?php if ($instance['hide_empty'] == "0") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('hide_empty') ?>_no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show_counts') ?>"><?php _e('Show Site Category Count:', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
			<input type="radio" name="<?php echo $this->get_field_name( 'show_counts'); ?>" id="<?php echo $this->get_field_id('show_counts') ?>_yes" 
				value="1" <?php if ($instance['show_counts'] == "1") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('show_counts') ?>_yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			
			<input type="radio" name="<?php echo $this->get_field_name( 'show_counts'); ?>" id="<?php echo $this->get_field_id('show_counts') ?>_no" 
				value="0" <?php if ($instance['show_counts'] == "0") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('show_counts') ?>_no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
		</p>


		<p>
			<label for="<?php echo $this->get_field_id('icon_show') ?>"><?php _e('Show Site Category icons:', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
			<input type="radio" name="<?php echo $this->get_field_name( 'icon_show'); ?>" id="<?php echo $this->get_field_id('icon_show') ?>_yes" 
				value="1" <?php if ($instance['icon_show'] == "1") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('icon_show') ?>_yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			
			<input type="radio" name="<?php echo $this->get_field_name( 'icon_show'); ?>" id="<?php echo $this->get_field_id('icon_show') ?>_no" 
				value="0" <?php if ($instance['icon_show'] == "0") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('icon_show') ?>_no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('icon_size') ?>"><?php _e('Icon size:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('icon_size'); ?>" id="<?php echo $this->get_field_id('icon_size'); ?>" 
				class="" size="5" value="<?php echo $instance['icon_size'] ?>"/>px  <?php _e('square', SITE_CATEGORIES_I18N_DOMAIN); ?>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_more_link') ?>"><?php _e('Landing Page link below categories list:', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
			<input type="radio" name="<?php echo $this->get_field_name( 'show_more_link'); ?>" id="<?php echo $this->get_field_id('show_more_link') ?>_yes" 
				value="1" <?php if ($instance['show_more_link'] == "1") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('show_more_link') ?>_yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			
			<input type="radio" name="<?php echo $this->get_field_name( 'show_more_link'); ?>" id="<?php echo $this->get_field_id('show_more_link') ?>_no" 
				value="0" <?php if ($instance['show_more_link'] == "0") { echo ' checked="checked" '; } ?> /> <label for="<?php echo $this->get_field_id('show_more_link') ?>_no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'landing_link_label' ); ?>"><?php 
				_e('Label for link:', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

			<input type="text" id="<?php echo $this->get_field_id( 'landing_link_label' ); ?>" value="<?php echo $instance['landing_link_label']; ?>"
				name="<?php echo $this->get_field_name( 'landing_link_label'); ?>" class="widefat" style="width:100%;" />
		</p>
		
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] 				= strip_tags($new_instance['title']);
		$instance['per_page'] 			= strip_tags($new_instance['per_page']);
		$instance['ordering'] 			= strip_tags($new_instance['ordering']);
		$instance['order'] 				= strip_tags($new_instance['order']);
		$instance['hide_empty'] 		= intval($new_instance['hide_empty']);
		$instance['show_counts'] 		= intval($new_instance['show_counts']);
		$instance['show_style'] 		= strip_tags($new_instance['show_style']);
		$instance['icon_show'] 			= strip_tags($new_instance['icon_show']);
		$instance['icon_size'] 			= intval($new_instance['icon_size']);
		$instance['show_more_link'] 	= intval($new_instance['show_more_link']);
		$instance['landing_link_label'] = strip_tags($new_instance['landing_link_label']);

		delete_site_transient( 'site-categories-categories-data-'. $this->number );
		return $instance;
	}

	function widget($args, $instance) {

		global $site_categories, $current_site;		
		
		$site_categories->load_config();
		extract($args);

		$per_page 		= intval($instance['per_page']);
		if (!$per_page) {
			if (isset($site_categories->opts['blog_limit']))
				$per_page = intval($site_categories->opts['blog_limit']);
			else
				$per_page = 10;
		}
		$instance['per_page'] = intval($per_page);


		$ordering 		= strip_tags($instance['ordering']);
		if (!$ordering) {
			if (isset($site_categories->opts['blog_ordering']))
				$ordering = @$default_opts['blog_ordering'];			
			else
				$ordering = "alphabetical";
		}
		$instance['ordering'] = $ordering;
		
		$icon_size 	= intval($instance['icon_size']);
		if ((!$icon_size) || ($icon_size < 1)) {
			$icon_size = 32;
		}
		$instance['icon_size'] = $icon_size;

		$show_style 	= strip_tags($instance['show_style']);
		if (!$show_style) {
			$show_style = "ul";
		}
		$instance['show_style'] = $show_style;
		
		//echo "instance<pre>"; print_r($instance); echo "</pre>";
		
		$data = get_site_transient( 'site-categories-categories-data-'. $this->number );
		if (!$data) {
			
			switch_to_blog( $current_site->blog_id );
		
			$get_terms_args = array();
			$get_terms_args['hide_empty']	=	$instance['hide_empty'];
			$get_terms_args['orderby']		=	$instance['ordering'];

			$categories = get_terms( SITE_CATEGORIES_TAXONOMY, $get_terms_args );
			//echo "categories<pre>"; print_r($categories); echo "</pre>";
		
			if (($categories) && (count($categories))) {
				$data = array();
				$data['current_page'] = 1;

				if (count($categories) < $instance['per_page']) {

					$data['categories'] = $categories;

				} else {

					$data['offset'] 		= intval($instance['per_page']) * (intval($data['current_page'])-1); 
					$data['categories'] 	= array_slice($categories, $data['offset'], $instance['per_page'], true);
					$data['total_pages'] 	= ceil(count($categories)/intval($instance['per_page']));
				}
				
				if (count($data['categories'])) {

					foreach($data['categories'] as $idx => $data_category) {

						$data['categories'][$idx]->icon_image_src = $site_categories->get_category_term_icon_src($data_category->term_id,
						 	$instance['icon_size']);

						if ((isset($site_categories->opts['landing_page_rewrite'])) && ($site_categories->opts['landing_page_rewrite'] == true)) {
							$data['categories'][$idx]->bcat_url = trailingslashit($site_categories->opts['landing_page_slug']) . $data_category->slug;
						} else {
							$data['categories'][$idx]->bcat_url = $site_categories->opts['landing_page_slug'] .'&amp;category_name=' . $data_category->slug;
						}
					}
				}


				if (intval($instance['show_more_link'])) {
					if ((isset($site_categories->opts['landing_page_rewrite'])) && ($site_categories->opts['landing_page_rewrite'] == true)) {
						$data['landing']['link_url'] = trailingslashit($site_categories->opts['landing_page_slug']);
					} else {
						$data['landing']['link_url'] = $site_categories->opts['landing_page_slug'];
					}
					if (isset($instance['landing_link_label']))
						$data['landing']['link_label'] = $instance['landing_link_label'];
					else
						$data['landing']['link_label'] = __('More Categories', SITE_CATEGORIES_I18N_DOMAIN);								
				}			
			}		
			restore_current_blog();
			
			set_site_transient( 'site-categories-categories-data-'. $this->number, $data, 30);
		}
		
		$categories_content = apply_filters('categories_widget_list_display', '', $data, $instance);
		if (strlen($categories_content)) {
			echo $before_widget;

			$title = apply_filters('widget_title', $instance['title']);
			if ($title) echo $before_title . $title . $after_title;
		
			echo $categories_content;
		
			echo $after_widget;
			
		}
	}
}

function process_categories_widget_list_display($content, $data, $args) {
	//echo "args<pre>"; print_r($args); echo "</pre>";
	//echo "data<pre>"; print_r($data); echo "</pre>";

	if ((isset($data['categories'])) && (count($data['categories']))) {

		if ($args['show_style'] == "ol") { $content .= '<ol class="site-categories site-categories-widget">'; }
		else if ($args['show_style'] == "select") { $content .= '<select class="site-categories site-categories-widget">'; }
		else { $content .= '<ul class="site-categories site-categories-widget">'; }

		foreach ($data['categories'] as $category) { 
			if ($args['show_style'] != "select") { 

				$content .=	'<li><a href="'. $category->bcat_url .'">';
				
					if ( ($args['icon_show'] == true) && (isset($category->icon_image_src))) {
						$content .= '<img class="site-category-icon" width="'. $args['icon_size'] .'" height="'. $args['icon_size'] .'"
						 alt="'. $category->name .'" src="'. $category->icon_image_src .'" />';
					} 
					$content .= '<span class="site-category-title">'. $category->name .'</span>';
					if ($args['show_counts']) {
						$content .= '<span class="site-category-count">('. $category->count .')</span>';
					}
					$content .= '</a>';
				$content .= '</li>';
			} else {
				$content .= '<option value="'. $category->bcat_url .'">'. $category->name .'</option>';
			}
		}

		if ($args['show_style'] == "ol") { $content .= "</ol>"; }
		else if ($args['show_style'] == "select") { $content .= "</select>"; }
		else { $content .= "</ul>"; }

		if ((isset($args['show_more_link'])) && ($args['show_more_link']) && (isset($data['landing']))) { 

			$content .= '<div id="site-categories-navigation">';

				$content .= '<a href="'. $data['landing']['link_url'] .'">'. $data['landing']['link_label'] .'</a>';
			$content .= '</div>';
		}
	}

	return $content;
}
add_filter('categories_widget_list_display', 'process_categories_widget_list_display', 99, 3);
