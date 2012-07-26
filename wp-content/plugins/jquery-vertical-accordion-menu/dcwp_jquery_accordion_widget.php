<?php 

class dc_jqaccordion_widget extends WP_Widget {
    /** constructor */
    function dc_jqaccordion_widget() {
	
		$name =			'jQuery Accordion Menu';
		$desc = 		'Vertical Accordion From Custom Menus';
		$id_base = 		'dc_jqaccordion_widget';
		$widget_base =  'dc_jqaccordion_widget_item';
		$css_class = 	'';
		$alt_option = 	'widget_dcjq_accordion_navigation'; 

		$widget_ops = array(
			'classname' => $css_class,
			'description' => __( $desc, 'dcjq-accordion' ),
		);
		parent::WP_Widget( 'nav_menu', __('Custom Menu'), $widget_ops );

		$this->WP_Widget($id_base, __($name, 'dcjqaccordion'), $widget_ops);
		$this->alt_option_name = $alt_option;
		
		add_action( 'wp_head', array(&$this, 'styles'), 10, 1 );	
		add_action( 'wp_footer', array(&$this, 'footer'), 10, 1 );	

		$this->defaults = array(
			'title' => '',
			'event' => 'click',
			'hoverDelay' => '300',
			'menuClose' => 'on',
			'autoClose' => 'on',
			'saveState' => 'on',
			'autoExpand' => 'off',
			'showCount' => 'off',
			'speed' => 'slow',
			'disableLink' => 'on',
			'classDisable' => 'on',
			'classMenu' => '',
			'skin' => 'demo.css'
		);
    }
	
	function widget($args, $instance) {
		extract( $args );
		// Get menu
		
		if(! isset($instance['speed']) ){ $instance['speed'] = 'slow'; }
		
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		$nav_menu = wp_get_nav_menu_object( $instance['nav_menu'] );

		if (!$nav_menu)
			return;

		$instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		
		$classMenu = ($instance['classMenu'] != '') ? $instance['classMenu'] : 'menu'; 
	
		echo $args['before_widget'];
	
		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
			
		?>
		
		<div class="dcjq-accordion" id="<?php echo $this->id.'-item'; ?>">
		
			<?php
				wp_nav_menu( 
					array( 
						'fallback_cb' => '', 
						'menu' => $nav_menu, 
						'container' => false,
						'menu_class' => $classMenu
						) 
					);
			?>
		
		</div>
		<?php
		
		echo $args['after_widget'];
	}

    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		$instance['autoClose'] = $new_instance['autoClose'];
		$instance['menuClose'] = $new_instance['menuClose'];
		$instance['saveState'] = $new_instance['saveState'];
		$instance['autoExpand'] = $new_instance['autoExpand'];
		$instance['disableLink'] = $new_instance['disableLink'];
		$instance['classDisable'] = $new_instance['classDisable'];
		$instance['classMenu'] = $new_instance['classMenu'];
		$instance['showCount'] = $new_instance['showCount'];
		$instance['event'] = strip_tags( stripslashes($new_instance['event']) );
		$instance['skin'] = $new_instance['skin'];
		$instance['speed'] = $new_instance['speed'];
		$instance['hoverDelay'] = $new_instance['hoverDelay'];
		
		return $instance;
	}

    /** @see WP_Widget::form */
    function form($instance) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		if(! isset($instance['autoClose']) ){ $instance['autoClose'] = 'false'; }
		if(! isset($instance['saveState']) ){ $instance['saveState'] = 'false'; }
		if(! isset($instance['menuClose']) ){ $instance['menuClose'] = 'false'; }
		if(! isset($instance['disableLink']) ){ $instance['disableLink'] = 'false'; }
		if(! isset($instance['classDisable']) ){ $instance['classDisable'] = ''; }
		if(! isset($instance['classMenu']) ){ $instance['classMenu'] = ''; }
		if(! isset($instance['autoExpand']) ){ $instance['autoExpand'] = 'false'; }
		if(! isset($instance['showCount']) ){ $instance['showCount'] = 'false'; }
		$event = isset( $instance['event'] ) ? $instance['event'] : '';
		$skin = isset( $instance['skin'] ) ? $instance['skin'] : '';
		$speed = isset( $instance['speed'] ) ? $instance['speed'] : '';
		$hoverDelay = isset( $instance['hoverDelay'] ) ? $instance['hoverDelay'] : '';
		
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );

		// Get menus
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		// If no menus exists, direct the user to go and create some.
		if ( !$menus ) {
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"  size="20" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
		<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
		<?php
			foreach ( $menus as $menu ) {
				$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
				echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
			}
		?>
		</select>
	</p>
	<p>
		<input type="radio" id="<?php echo $this->get_field_id('event1'); ?>" name="<?php echo $this->get_field_name('event'); ?>" value="click"<?php checked( $event, 'click' ); ?> /> 
		<label for="<?php echo $this->get_field_id('event1'); ?>"><?php _e( 'Click' , 'dcjq-accordion' ); ?></label>
		<input type="radio" id="<?php echo $this->get_field_id('event2'); ?>" name="<?php echo $this->get_field_name('event'); ?>" value="hover"<?php checked( $event, 'hover' ); ?> /> 
		<label for="<?php echo $this->get_field_id('event2'); ?>"><?php _e( 'Hover' , 'dcjq-accordion' ); ?></label>
	</p>
	<p>
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('autoClose'); ?>" name="<?php echo $this->get_field_name('autoClose'); ?>"<?php checked( $autoClose, 'true' ); ?> />
		<label for="<?php echo $this->get_field_id('autoClose'); ?>"><?php _e( 'Auto Close Open Menus' , 'dcjq-accordion' ); ?></label><br />
		
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('saveState'); ?>" name="<?php echo $this->get_field_name('saveState'); ?>"<?php checked( $saveState, 'true'); ?> />
		<label for="<?php echo $this->get_field_id('saveState'); ?>"><?php _e( 'Save Menu State (uses cookies)' , 'dcjq-accordion' ); ?></label><br />
		
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('autoExpand'); ?>" name="<?php echo $this->get_field_name('autoExpand'); ?>"<?php checked( $autoExpand, 'true' ); ?> />
		<label for="<?php echo $this->get_field_id('autoExpand'); ?>"><?php _e( 'Auto Expand Based on Current Page/Item' , 'dcjq-accordion' ); ?></label><br />
		
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('disableLink'); ?>" name="<?php echo $this->get_field_name('disableLink'); ?>"<?php checked( $disableLink, 'true' ); ?> />
		<label for="<?php echo $this->get_field_id('disableLink'); ?>"><?php _e( 'Disable Parent Links' , 'dcjq-accordion' ); ?></label><br />

		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('menuClose'); ?>" name="<?php echo $this->get_field_name('menuClose'); ?>"<?php checked( $menuClose, 'true' ); ?> />
		<label for="<?php echo $this->get_field_id('menuClose'); ?>"><?php _e( 'Close Menu (hover only)' , 'dcjq-accordion' ); ?></label><br />
		
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('showCount'); ?>" name="<?php echo $this->get_field_name('showCount'); ?>"<?php checked( $showCount, 'true' ); ?> />
		<label for="<?php echo $this->get_field_id('showCount'); ?>"><?php _e( 'Show Count' , 'dcjq-accordion' ); ?></label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('classMenu'); ?>"><?php _e('Menu Class:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('classMenu'); ?>" name="<?php echo $this->get_field_name('classMenu'); ?>" value="<?php echo $classMenu; ?>" size="15" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('classDisable'); ?>"><?php _e('Disable Class:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('classDisable'); ?>" name="<?php echo $this->get_field_name('classDisable'); ?>" value="<?php echo $classDisable; ?>" size="15" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('hoverDelay'); ?>"><?php _e('Hover Delay:', 'dcjq-accordion'); ?>
		<select name="<?php echo $this->get_field_name('hoverDelay'); ?>" id="<?php echo $this->get_field_id('hoverDelay'); ?>" >
			<option value='0' <?php selected( $hoverDelay, '0'); ?> >No Delay</option>
			<option value='200' <?php selected( $hoverDelay, '200'); ?> >0.2 sec</option>
			<option value='400' <?php selected( $hoverDelay, '400'); ?> >0.4 sec</option>
			<option value='600' <?php selected( $hoverDelay, '600'); ?> >0.6 sec</option>
			<option value='800' <?php selected( $hoverDelay, '800'); ?> >0.8 sec</option>
			<option value='1000' <?php selected( $hoverDelay, '1000'); ?> >1.0 sec</option>
		</select>
		</label>
	</p>
	<p><label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Animation Speed:', 'dcjq-accordion'); ?>
		<select name="<?php echo $this->get_field_name('speed'); ?>" id="<?php echo $this->get_field_id('speed'); ?>" >
			<option value='slow' <?php selected( $speed, 'slow'); ?> >Slow</option>
			<option value='normal' <?php selected( $speed, 'normal'); ?> >Normal</option>
			<option value='fast' <?php selected( $speed, 'fast'); ?> >Fast</option>
		</select>
		</label>
	</p>
	<p><label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Skin:', 'dcjq-accordion'); ?>  <?php 
		
		// http://www.codewalkers.com/c/a/File-Manipulation-Code/List-files-in-a-directory-no-subdirectories/

		echo "<select name='".$this->get_field_name('skin')."' id='".$this->get_field_id('skin')."'>";
		echo "<option value='no-theme' ".selected( $skin, 'no-theme', false).">No theme</option>";
			
		//The path to the style directory
		$dirpath = plugin_dir_path(__FILE__) . 'skins/';	
			
		$dh = opendir($dirpath);
		while (false !== ($file = readdir($dh))) {
			//Don't list subdirectories
			if (!is_dir("$dirpath/$file")) {
				//Remove file extension
				$newSkin = htmlspecialchars(ucfirst(preg_replace('/\..*$/', '', $file)));
				echo "<option value='$newSkin' ".selected($skin, $newSkin, false).">" . $newSkin . '</option>';
			}
		}
		closedir($dh); 
		echo "</select>"; ?> </label><br />
	</p>
	<div class="widget-control-actions alignright">
		<p><small><a href="http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-vertical-accordion-menu-widget/"><?php esc_attr_e('Visit plugin site', 'dcjq-accordion'); ?></a></small></p>
	</div>
	
	<?php 
	}
	
	/** Adds ID based dropdown menu skin to the header. */
	function styles(){
		
		if(!is_admin()){

			$all_widgets = $this->get_settings();
			
			foreach ($all_widgets as $key => $wpdcjqaccordion){
				$widget_id = $this->id_base . '-' . $key;
				
				if(is_active_widget(false, $widget_id, $this->id_base)){
		
					$skin = $wpdcjqaccordion['skin'];
					$skin = htmlspecialchars(ucfirst(preg_replace('/\..*$/', '', $skin)));
					if($skin != 'No-theme'){
						echo "\n\t<link rel=\"stylesheet\" href=\"".dc_jqaccordion::get_plugin_directory()."/skin.php?widget_id=".$key."&amp;skin=".strtolower($skin)."\" type=\"text/css\" media=\"screen\"  />";
					}
				}
			}
		}
	}

	/** Adds ID based activation script to the footer */
	function footer(){
		
		if(!is_admin()){
		
			$all_widgets = $this->get_settings();
		
			foreach ($all_widgets as $key => $wpdcjqaccordion){
		
				$widget_id = $this->id_base . '-' . $key;

				if(is_active_widget(false, $widget_id, $this->id_base)){
			
					$autoClose = $wpdcjqaccordion['autoClose'];
					if($autoClose == ''){$autoClose = 'false';}
				
					$saveState = $wpdcjqaccordion['saveState'];
					if($saveState == ''){$saveState = 'false';}
				
					$disableLink = $wpdcjqaccordion['disableLink'];
					if($disableLink == ''){$disableLink = 'false';}
					
					$classDisable = $wpdcjqaccordion['classDisable'];
					
					$classMenu = $wpdcjqaccordion['classMenu'];
					if($classMenu == ''){$classMenu = 'menu';}
				
					$menuClose = $wpdcjqaccordion['menuClose'];
					if($menuClose == ''){$menuClose = 'false';}
					
					$autoExpand = $wpdcjqaccordion['autoExpand'];
					if($autoExpand == ''){$autoExpand = 'false';}
					
					$showCount = $wpdcjqaccordion['showCount'];
					if($showCount == ''){$showCount = 'false';}
				
					$hoverDelay = $wpdcjqaccordion['hoverDelay'];
					if($hoverDelay == ''){$hoverDelay = 600;}
					
					$accordionId = '#'.$widget_id.'-item .'.$classMenu;

			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('<?php echo $accordionId; ?>').dcAccordion({
						eventType: '<?php echo $wpdcjqaccordion['event']; ?>',
						hoverDelay: <?php echo $hoverDelay; ?>,
						menuClose: <?php echo $menuClose; ?>,
						autoClose: <?php echo $autoClose; ?>,
						saveState: <?php echo $saveState; ?>,
						autoExpand: <?php echo $autoExpand; ?>,
						classExpand: 'current-menu-item',
						classDisable: '<?php echo $classDisable; ?>',
						showCount: <?php echo $showCount; ?>,
						disableLink: <?php echo $disableLink; ?>,
						cookie: '<?php echo $widget_id; ?>',
						speed: '<?php echo $wpdcjqaccordion['speed']; ?>'
					});
				});
			</script>
		
			<?php
			}		
		}
		}
	}
} // class dc_jqaccordion_widget