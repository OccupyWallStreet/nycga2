<?php 

class dc_jqfloatingmenu_widget extends WP_Widget {
    /** constructor */
    function dc_jqfloatingmenu_widget() {
	
		$name =			'Floating Menu';
		$desc = 		'Create Floating Menus From Any Wordpress Custom Menu';
		$id_base = 		'dc_jqfloatingmenu_widget';
		$css_class = 	'';
		$alt_option = 	'widget_dcjq_slick_menu_navigation'; 

		$widget_ops = array(
			'classname' => $css_class,
			'description' => __( $desc, 'dcjq-floating-menu' ),
		);
		parent::WP_Widget( 'nav_menu', __('Custom Menu'), $widget_ops );
		
		$this->WP_Widget($id_base, __($name, 'dcjqfloatingmenu'), $widget_ops);
		$this->alt_option_name = $alt_option;
		
		add_action( 'wp_head', array(&$this, 'styles'), 10, 1 );	
		add_action( 'wp_footer', array(&$this, 'footer'), 10, 1 );	

		$this->defaults = array(
			'title' => '',
			'event' => 'click',
			'width' => 200,
			'location' => 'top',
			'align' => 'left',
			'offsetL' => 50,
			'offsetA' => 50,
			'center' => '',
			'speedMenu' => 600,
			'speedFloat' => 1500,
			'tabText' => 'Menu',
			'skin' => 'white'
		);
    }
	
	function widget($args, $instance) {
		extract( $args );
		// Get menu
		
		if(! isset($instance['event']) ){ $instance['event'] = 'click'; }	
		if(! isset($instance['width']) ){ $instance['width'] = '200'; }	
		if(! isset($instance['speedMenu']) ){ $instance['speedMenu'] = '600'; }	
		if(! isset($instance['speedFloat']) ){ $instance['speedFloat'] = '1500'; }
		if(! isset($instance['location']) ){ $instance['location'] = 'left'; }
		if(! isset($instance['align']) ){ $instance['align'] = 'left'; }
		if(! isset($instance['offsetL']) ){ $instance['offsetL'] = '50'; }
		if(! isset($instance['offsetA']) ){ $instance['offsetA'] = '50'; }
		if(! isset($instance['tabText']) ){ $instance['tabText'] = 'Menu'; }
		if(! isset($instance['center']) ){ $instance['center'] = ''; }
		if(! isset($instance['autoClose']) ){ $instance['autoClose'] = ''; }
		if(! isset($instance['tabClose']) ){ $instance['tabClose'] = ''; }	
		if(! isset($instance['disableFloat']) ){ $instance['disableFloat'] = ''; }	
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		$nav_menu = wp_get_nav_menu_object( $instance['nav_menu'] );

		if ( !$nav_menu )
			return;
			
		?>
		<div class="dcjq-floating-menu" id="<?php echo $this->id.'-item'; ?>">
		
		<?php 
			$corner = '<div class="dc-corner"><span></span></div>';
			if($location == 'bottom'){
				echo $corner;
			}
		
			wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'container' => false ) );
		
			if($location == 'top'){
				echo $corner;
			}
		?>
		</div>
		<?php
	}

    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		$instance['event'] = $new_instance['event'];
		$instance['width'] = (int) strip_tags( stripslashes($new_instance['width']) );
		$instance['speedMenu'] = (int) strip_tags( stripslashes($new_instance['speedMenu']) );
		$instance['speedFloat'] = (int) strip_tags( stripslashes($new_instance['speedFloat']) );
		$instance['location'] = $new_instance['location'];
		$instance['align'] = $new_instance['align'];
		$instance['center'] = $new_instance['center'];
		$instance['offsetL'] = (int) strip_tags( stripslashes($new_instance['offsetL']) );
		$instance['offsetA'] = (int) strip_tags( stripslashes($new_instance['offsetA']) );
		$instance['skin'] = $new_instance['skin'];
		$instance['autoClose'] = $new_instance['autoClose'];
		$instance['tabClose'] = $new_instance['tabClose'];
		$instance['disableFloat'] = $new_instance['disableFloat'];
		$instance['tabText'] = strip_tags( stripslashes($new_instance['tabText']) );
		
		return $instance;
	}

    /** @see WP_Widget::form */
    function form($instance) {
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		$event = isset( $instance['event'] ) ? $instance['event'] : 'click';
		$width = isset( $instance['width'] ) ? $instance['width'] : '180';
		$speedMenu = isset( $instance['speedMenu'] ) ? $instance['speedMenu'] : '600';
		$speedFloat = isset( $instance['speedFloat'] ) ? $instance['speedFloat'] : '1500';
		$location = isset( $instance['location'] ) ? $instance['location'] : 'top';
		$align = isset( $instance['align'] ) ? $instance['align'] : 'left';
		$center = isset( $instance['center'] ) ? $instance['center'] : '';
		$offsetL = isset( $instance['offsetL'] ) ? $instance['offsetL'] : '10';
		$offsetA = isset( $instance['offsetA'] ) ? $instance['offsetA'] : '10';
		$skin = isset( $instance['skin'] ) ? $instance['skin'] : '';
		$autoClose = isset( $instance['autoClose'] ) ? $instance['autoClose'] : '';
		$disableFloat = isset( $instance['disableFloat'] ) ? $instance['disableFloat'] : '';
		$tabClose = isset( $instance['tabClose'] ) ? $instance['tabClose'] : '';
		$tabText = isset( $instance['tabText'] ) ? $instance['tabText'] : 'Menu';
		
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
		<label for="<?php echo $this->get_field_id('event1'); ?>"><?php _e( 'Click' , 'dcjq-floating-menu' ); ?></label>
		<input type="radio" id="<?php echo $this->get_field_id('event2'); ?>" name="<?php echo $this->get_field_name('event'); ?>" value="hover"<?php checked( $event, 'hover' ); ?> /> 
		<label for="<?php echo $this->get_field_id('event2'); ?>"><?php _e( 'Hover' , 'dcjq-floating-menu' ); ?></label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('tabText'); ?>"><?php _e('Tab Text:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('tabText'); ?>" name="<?php echo $this->get_field_name('tabText'); ?>" value="<?php echo $tabText; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width (px):') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $width; ?>" />
	</p>
	
	<p>
	  <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e( 'Location' , 'dcjq-floating-menu' ); ?></label>
		<select name="<?php echo $this->get_field_name('location'); ?>" id="<?php echo $this->get_field_id('location'); ?>" >
			<option value='top' <?php selected( $location, 'top'); ?> >Top</option>
			<option value='bottom' <?php selected( $location, 'bottom'); ?> >Bottom</option>
		</select>
		<input type="text" id="<?php echo $this->get_field_id('offsetL'); ?>" name="<?php echo $this->get_field_name('offsetL'); ?>" value="<?php echo $offsetL; ?>" size="4" />px
	</p>
	<p>
	  <label for="<?php echo $this->get_field_id('align'); ?>"><?php _e( 'Alignment' , 'dcjq-floating-menu' ); ?></label>
		<select name="<?php echo $this->get_field_name('align'); ?>" id="<?php echo $this->get_field_id('align'); ?>" >
			<option value='left' <?php selected( $align, 'left'); ?> >Left</option>
			<option value='right' <?php selected( $align, 'right'); ?> >Right</option>
		</select>
		<input type="text" id="<?php echo $this->get_field_id('offsetA'); ?>" name="<?php echo $this->get_field_name('offsetA'); ?>" value="<?php echo $offsetA; ?>" size="4" />px
	</p>
	<p>
	  <input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('center'); ?>" name="<?php echo $this->get_field_name('center'); ?>"<?php checked( $center, 'true'); ?> />
		<label for="<?php echo $this->get_field_id('center'); ?>"><?php _e( 'Set Alignment from Center' , 'dcjq-floating-menu' ); ?></label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('speedFloat'); ?>"><?php _e('Float Speed (ms):') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('speedFloat'); ?>" name="<?php echo $this->get_field_name('speedFloat'); ?>" value="<?php echo $speedFloat; ?>" size="5" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('speedMenu'); ?>"><?php _e('Menu Speed (ms):') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('speedMenu'); ?>" name="<?php echo $this->get_field_name('speedMenu'); ?>" value="<?php echo $speedMenu; ?>" size="5" />
	</p>
	<p>
	  <input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('autoClose'); ?>" name="<?php echo $this->get_field_name('autoClose'); ?>"<?php checked( $autoClose, 'true'); ?> />
		<label for="<?php echo $this->get_field_id('autoClose'); ?>"><?php _e( 'Auto-Close Menu' , 'dcjq-floating-menu' ); ?></label>
	</p>
	<p>
	  <input type="checkbox" value="false" class="checkbox" id="<?php echo $this->get_field_id('tabClose'); ?>" name="<?php echo $this->get_field_name('tabClose'); ?>"<?php checked( $tabClose, 'false'); ?> />
		<label for="<?php echo $this->get_field_id('tabClose'); ?>"><?php _e( 'Keep Open' , 'dcjq-floating-menu' ); ?></label>
	</p>
	<p>
	  <input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('disableFloat'); ?>" name="<?php echo $this->get_field_name('disableFloat'); ?>"<?php checked( $disableFloat, 'true'); ?> />
		<label for="<?php echo $this->get_field_id('disableFloat'); ?>"><?php _e( 'Disable Float' , 'dcjq-floating-menu' ); ?></label>
	</p>
	<p><label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Skin:', 'dcjq-floating-menu'); ?>  <?php 
		
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
		<p><small><a href="http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-floating-menu/"><?php esc_attr_e('Visit plugin site', 'dcjq-floating-menu'); ?></a></small></p>
	</div>
	
	<?php 
	}
	
	/** Adds ID based slick menu skin to the header. */
	function styles(){
		
		if(!is_admin()){

			$all_widgets = $this->get_settings();
		
			foreach ($all_widgets as $key => $wpdcjqfloatingmenu){
				$widget_id = $this->id_base . '-' . $key;		
				if(is_active_widget(false, $widget_id, $this->id_base)){
		
					$skin = $wpdcjqfloatingmenu['skin'];
					$skin = htmlspecialchars(ucfirst(preg_replace('/\..*$/', '', $skin)));
					if($skin != 'No-theme'){
						echo "\n\t<link rel=\"stylesheet\" href=\"".dc_jqfloatingmenu::get_plugin_directory()."/skin.php?widget_id=".$key."&amp;skin=".strtolower($skin)."\" type=\"text/css\" media=\"screen\"  />";
					}
				}
			}
		}
	}

	/** Adds ID based activation script to the footer */
	function footer(){
		
		if(!is_admin()){
		
		$all_widgets = $this->get_settings();
		
		foreach ($all_widgets as $key => $wpdcjqfloatingmenu){
		
			$widget_id = $this->id_base . '-' . $key;
			
			$floater_id = 'dc-floater-' . $key;

			if(is_active_widget(false, $widget_id, $this->id_base)){
			
				$event = $wpdcjqfloatingmenu['event'];
				if($event == ''){$event = 'click';};
				
				$width = $wpdcjqfloatingmenu['width'];
				if($width == ''){$width = '200';};
				
				$speedMenu = $wpdcjqfloatingmenu['speedMenu'];
				if($speedMenu == ''){$speedMenu = '600';};
				
				$speedFloat = $wpdcjqfloatingmenu['speedFloat'];
				if($speedFloat == ''){$speedFloat = '1500';};
				
				$location = $wpdcjqfloatingmenu['location'];
				if($location == ''){$location = 'top';};
				
				$align = $wpdcjqfloatingmenu['align'];
				if($align == ''){$align = 'left';};
			
				$offsetL = $wpdcjqfloatingmenu['offsetL'];
				if($offsetL == ''){$offsetL = '0';};
				$offsetA = $wpdcjqfloatingmenu['offsetA'];
				if($offsetA == ''){$offset = '0';};
				
				$center = $wpdcjqfloatingmenu['center'];
				$opt_center = $center == 'true' ? 'center: true, centerPx: '.$offsetA.',' : '' ;
				
				$autoClose = $wpdcjqfloatingmenu['autoClose'];
				if($autoClose == ''){$autoClose = 'false';};
				
				$disableFloat = $wpdcjqfloatingmenu['disableFloat'];
				if($disableFloat == ''){$disableFloat = 'false';};
				
				$tabClose = $wpdcjqfloatingmenu['tabClose'];
				if($tabClose == ''){$tabClose = 'true';};

				$tabText = $wpdcjqfloatingmenu['tabText'];
				if($tabText == ''){$tabText = 'Click';};
				
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('#<?php echo $widget_id.'-item'; ?>').dcFloater({
						event: '<?php echo $event; ?>',
						width: <?php echo $width; ?>,
						location: '<?php echo $location; ?>',
						align: '<?php echo $align; ?>',
						speedMenu: <?php echo $speedMenu; ?>,
						speedFloat: <?php echo $speedFloat; ?>,
						offsetLocation: <?php echo $offsetL; ?>,
						offsetAlign: <?php echo $offsetA; ?>,
						<?php echo $opt_center; ?>
						autoClose: <?php echo $autoClose; ?>,
						tabClose: <?php echo $tabClose; ?>,
						disableFloat: <?php echo $disableFloat; ?>,
						tabText: '<?php echo $tabText; ?>',
						idWrapper: '<?php echo $floater_id ?>',
						classOpen: 'dcfl-open',
						classClose: 'dcfl-close',
						classToggle: 'dcfl-link'
						
					});
				});
			</script>
		
			<?php
			
			}		
		}
		}
	}
} // class dc_jqfloatingmenu_widget