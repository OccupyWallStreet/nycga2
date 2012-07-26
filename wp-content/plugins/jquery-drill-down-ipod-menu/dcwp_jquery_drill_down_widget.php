<?php 

class dc_jqdrilldown_widget extends WP_Widget {
    /** constructor */
    function dc_jqdrilldown_widget() {
	
		$name =			'jQuery Drill Down iPod Menu';
		$desc = 		'Drill Down iPod Style Menus From Wordpress Custom Menus';
		$id_base = 		'dc_jqdrilldown_widget';
		$widget_base =  'dc_jqdrilldown_widget_item';
		$css_class = 	'';
		$alt_option = 	'widget_dcjq_drill_down_menu'; 

		$widget_ops = array(
			'classname' => $css_class,
			'description' => __( $desc, 'dcjq-drilldown' ),
		);
		parent::WP_Widget( 'nav_menu', __('Custom Menu'), $widget_ops );

		$this->WP_Widget($id_base, __($name, 'dcjqdrilldown'), $widget_ops);
		$this->alt_option_name = $alt_option;
		
		add_action( 'wp_head', array(&$this, 'styles'), 10, 1 );	
		add_action( 'wp_footer', array(&$this, 'footer'), 10, 1 );	

		$this->defaults = array(
			'title' => '',
			'classParent' => 'dd-parent',
			'classActive' => 'active',
			'event' => 'click',
			'linkType' => 'backlink',
			'resetText' => 'All',
			'defaultText' => 'Select Option',
			'includeHdr' => 'on',
			'hoverDelay' => '300',
			'saveState' => 'on',
			'showCount' => 'on',
			'speed' => 'slow',
			'disableLink' => 'on',
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
	
		echo $args['before_widget'];
	
		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
			
		?>
		
		<div class="dcjq-drilldown" id="<?php echo $this->id.'-item'; ?>">
		
			<?php
				wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'container' => false ) );
			?>
		
		</div>
		<?php
		
		echo $args['after_widget'];
	}

    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		$instance['saveState'] = $new_instance['saveState'];
		$instance['disableLink'] = $new_instance['disableLink'];
		$instance['showCount'] = $new_instance['showCount'];
		$instance['classParent'] = strip_tags( stripslashes($new_instance['classParent']) );
		$instance['classActive'] = strip_tags( stripslashes($new_instance['classActive']) );
		$instance['event'] = strip_tags( stripslashes($new_instance['event']) );
		$instance['linkType'] = strip_tags( stripslashes($new_instance['linkType']) );
		$instance['resetText'] = strip_tags( stripslashes($new_instance['resetText']) );
		$instance['defaultText'] = strip_tags( stripslashes($new_instance['defaultText']) );
		$instance['includeHdr'] = $new_instance['includeHdr'];
		$instance['skin'] = $new_instance['skin'];
		$instance['speed'] = $new_instance['speed'];
		$instance['hoverDelay'] = $new_instance['hoverDelay'];
		
		return $instance;
	}

    /** @see WP_Widget::form */
    function form($instance) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		if(! isset($instance['saveState']) ){ $instance['saveState'] = 'false'; }
		if(! isset($instance['disableLink']) ){ $instance['disableLink'] = 'false'; }
		if(! isset($instance['showCount']) ){ $instance['showCount'] = 'false'; }
		if(! isset($instance['includeHdr']) ){ $instance['includeHdr'] = 'true'; }
		$classParent = isset( $instance['classParent'] ) ? $instance['classParent'] : '';
		$classActive = isset( $instance['classActive'] ) ? $instance['classActive'] : '';
		$event = isset( $instance['event'] ) ? $instance['event'] : '';
		$event = isset( $instance['linkType'] ) ? $instance['linkType'] : '';
		$event = isset( $instance['resetText'] ) ? $instance['resetText'] : '';
		$event = isset( $instance['defaultText'] ) ? $instance['defaultText'] : '';
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
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
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
		
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('saveState'); ?>" name="<?php echo $this->get_field_name('saveState'); ?>"<?php checked( $saveState, 'true'); ?> />
		<label for="<?php echo $this->get_field_id('saveState'); ?>"><?php _e( 'Save Menu State (uses cookies)' , 'dcjq-drilldown' ); ?></label><br />
		
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('showCount'); ?>" name="<?php echo $this->get_field_name('showCount'); ?>"<?php checked( $showCount, 'true' ); ?> />
		<label for="<?php echo $this->get_field_id('showCount'); ?>"><?php _e( 'Show Count' , 'dcjq-drilldown' ); ?></label><br />
		
		<input type="checkbox" value="true" class="checkbox" id="<?php echo $this->get_field_id('includeHdr'); ?>" name="<?php echo $this->get_field_name('includeHdr'); ?>"<?php checked( $includeHdr, 'true' ); ?> />
		<label for="<?php echo $this->get_field_id('includeHdr'); ?>"><?php _e( 'Show Header' , 'dcjq-drilldown' ); ?></label><br />

	
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('linkType'); ?>"><?php _e('Link Type:', 'dcjq-drilldown'); ?>
		<select name="<?php echo $this->get_field_name('linkType'); ?>" id="<?php echo $this->get_field_id('linkType'); ?>" >
			<option value='breadcrumb' <?php selected( $linkType, 'breadcrumb'); ?> >Breadcrumb</option>
			<option value='backlink' <?php selected( $linkType, 'backlink'); ?>> Back Link</option>
			<option value='link' <?php selected( $linkType, 'link'); ?> >Header Link</option>
		</select>
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('defaultText'); ?>"><?php _e('Default Header Text:') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('defaultText'); ?>" name="<?php echo $this->get_field_name('defaultText'); ?>" value="<?php echo $defaultText; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('resetText'); ?>"><?php _e('Reset Link Text:') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('resetText'); ?>" name="<?php echo $this->get_field_name('resetText'); ?>" value="<?php echo $resetText; ?>" />
	</p>
	<p><label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Animation Speed:', 'dcjq-drilldown'); ?>
		<select name="<?php echo $this->get_field_name('speed'); ?>" id="<?php echo $this->get_field_id('speed'); ?>" >
			<option value='slow' <?php selected( $speed, 'slow'); ?> >Slow</option>
			<option value='normal' <?php selected( $speed, 'normal'); ?> >Normal</option>
			<option value='fast' <?php selected( $speed, 'fast'); ?> >Fast</option>
		</select>
		</label>
	</p>
	<p><label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Skin:', 'dcjq-drilldown'); ?>  <?php 
		
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
	<input type="hidden" id="<?php echo $this->get_field_id('classParent'); ?>" name="<?php echo $this->get_field_name('classParent'); ?>" value="dd-parent" />
	<input type="hidden" id="<?php echo $this->get_field_id('classActive'); ?>" name="<?php echo $this->get_field_name('classActive'); ?>" value="active" />
	<input type="hidden" id="<?php echo $this->get_field_id('event'); ?>" name="<?php echo $this->get_field_name('event'); ?>" value="click" /> 
	<input type="hidden" id="<?php echo $this->get_field_id('hoverDelay'); ?>" name="<?php echo $this->get_field_name('hoverDelay'); ?>" value="300" /> 
	<input type="hidden" value="true" id="<?php echo $this->get_field_id('disableLink'); ?>" name="<?php echo $this->get_field_name('disableLink'); ?>" />
		
	<div class="widget-control-actions alignright">
		<p><small><a href="http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-drill-down-ipod-menu-widget/"><?php esc_attr_e('Visit plugin site', 'dcjq-drilldown'); ?></a></small></p>
	</div>
	
	<?php 
	}
	
	/** Adds ID based dropdown menu skin to the header. */
	function styles(){
		
		if(!is_admin()){

			$all_widgets = $this->get_settings();
		
			foreach ($all_widgets as $key => $wpdcjqdrilldown){
				$widget_id = $this->id_base . '-' . $key;		
				if(is_active_widget(false, $widget_id, $this->id_base)){
		
					$skin = $wpdcjqdrilldown['skin'];
					$skin = htmlspecialchars(ucfirst(preg_replace('/\..*$/', '', $skin)));
					if($skin != 'No-theme'){
						echo "\n\t<link rel=\"stylesheet\" href=\"".dc_jqdrilldown::get_plugin_directory()."/skin.php?widget_id=".$key."&skin=".strtolower($skin)."\" type=\"text/css\" media=\"screen\"  />";
					}
				}
			}
		}
	}

	/** Adds ID based activation script to the footer */
	function footer(){
		
		if(!is_admin()){
		
			$all_widgets = $this->get_settings();
		
			foreach ($all_widgets as $key => $wpdcjqdrilldown){
		
				$widget_id = $this->id_base . '-' . $key;

				if(is_active_widget(false, $widget_id, $this->id_base)){
			
					$saveState = $wpdcjqdrilldown['saveState'];
					if($saveState == ''){$saveState = 'false';};
				
					$disableLink = $wpdcjqdrilldown['disableLink'];
					if($disableLink == ''){$disableLink = 'false';};
					
					$showCount = $wpdcjqdrilldown['showCount'];
					if($showCount == ''){$showCount = 'false';};
					
					$includeHdr = $wpdcjqdrilldown['includeHdr'];
					if($includeHdr == ''){$includeHdr = 'false';};
					
					$linkType = $wpdcjqdrilldown['linkType'];
					if($linkType == ''){$linkType = 'breadcrumb';};
					
					$resetText = $wpdcjqdrilldown['resetText'];
					if($resetText == ''){$resetText = 'All';};
					
					$defaultText = $wpdcjqdrilldown['defaultText'];
					if($defaultText == ''){$defaultText = 'Select Option';};
				
					$hoverDelay = $wpdcjqdrilldown['hoverDelay'];
					if($hoverDelay == ''){$hoverDelay = 600;};

			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('#<?php echo $widget_id.'-item'; ?> .menu').dcDrilldown({
						classParent: '<?php echo $wpdcjqdrilldown['classParent']; ?>',
						classActive: '<?php echo $wpdcjqdrilldown['classActive']; ?>',
						eventType: '<?php echo $wpdcjqdrilldown['event']; ?>',
						linkType: '<?php echo $wpdcjqdrilldown['linkType']; ?>',
						hoverDelay: <?php echo $hoverDelay; ?>,
						saveState: <?php echo $saveState; ?>,
						disableLink: <?php echo $disableLink; ?>,
						resetText: '<?php echo $resetText; ?>',
						defaultText: '<?php echo $defaultText; ?>',
						includeHdr: <?php echo $includeHdr; ?>,
						showCount: <?php echo $showCount; ?>,
						speed: '<?php echo $wpdcjqdrilldown['speed']; ?>'
					});
				});
			</script>
		
			<?php
			}		
		}
		}
	}
} // class dc_jqdrilldown_widget