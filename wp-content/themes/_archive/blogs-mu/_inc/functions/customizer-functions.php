<?php

// WP 3.4 Theme Customizer

$blogsmu_use_customizer_type = array('colorpicker');
$blogsmu_use_customizer_id = array(
	$shortname . $shortprefix  . "nav_font",
	$shortname . $shortprefix  . "body_font",
	$shortname . $shortprefix . "headline_font",
	$shortname . $shortprefix . "font_size",
	$shortname . $shortprefix . "font_line_height",
	
);

/* 
 * Custom control class 
 * 
 * Add description on control
 * */
if ( class_exists('WP_Customize_Control') ) {
class WPMUDEV_Customize_Control extends WP_Customize_Control {
	
	public $description = '';
	
	protected function render_content() {
		switch( $this->type ) {
			default:
				return parent::render_content();
			case 'text':
				?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php if ( isset($this->description) && !empty($this->description) ): ?>
					<span class="customize-control-description"><?php echo $this->description ?></span>
					<?php endif ?>
					<input type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
				</label>
				<?php
				break;
			case 'radio':
				if ( empty( $this->choices ) )
					return;

				$name = '_customize-radio-' . $this->id;

				?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( isset($this->description) && !empty($this->description) ): ?>
				<span class="customize-control-description"><?php echo $this->description ?></span>
				<?php endif ?>
				<?php
				foreach ( $this->choices as $value => $label ) :
					?>
					<label>
						<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
						<?php echo esc_html( $label ); ?><br/>
					</label>
					<?php
				endforeach;
				break;
			case 'select':
				if ( empty( $this->choices ) )
					return;

				?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php if ( isset($this->description) && !empty($this->description) ): ?>
					<span class="customize-control-description"><?php echo $this->description ?></span>
					<?php endif ?>
					<select <?php $this->link(); ?>>
						<?php
						foreach ( $this->choices as $value => $label )
							echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
						?>
					</select>
				</label>
				<?php
				break;
		}
	}
	
}
}

if ( class_exists('WP_Customize_Color_Control') ) {
class WPMUDEV_Customize_Color_Control extends WP_Customize_Color_Control {
	
	public $description = '';
	
	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( isset($this->description) && !empty($this->description) ): ?>
			<span class="customize-control-description"><?php echo $this->description ?></span>
			<?php endif ?>
			<div class="customize-control-content">
				<div class="dropdown">
					<div class="dropdown-content">
						<div class="dropdown-status"></div>
					</div>
					<div class="dropdown-arrow"></div>
				</div>
				<input class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e('Hex Value'); ?>" />
			</div>
			<div class="farbtastic-placeholder"></div>
		</label>
		<?php
	}
}
}

function blogsmu_customize_register( $wp_customize ) {
	global $options, $shortname, $shortprefix, $bp_existed, $blogsmu_use_customizer_type, $blogsmu_use_customizer_id;
	$sections = array(
		array(
			'section' => 'header',
			'title' => __("Header Settings", TEMPLATE_DOMAIN),
			'priority' => 30
		), array(
			'section' => 'homepage',
			'title' => __("Homepage Settings", TEMPLATE_DOMAIN),
			'priority' => 31
		), array(
			'section' => 'navigation',
			'title' => __("Navigation Settings", TEMPLATE_DOMAIN),
			'priority' => 32
		), array( 
			'section' => 'css',
			'title' => __("CSS Settings", TEMPLATE_DOMAIN),
			'priority' => 33
		), array(
			'section' => 'footer',
			'title' => __("Footer Settings", TEMPLATE_DOMAIN),
			'priority' => 34
		), array(
			'section' => 'buddypress',
			'title' => __("BuddyPress Settings", TEMPLATE_DOMAIN),
			'priority' => 35
		)
	);
	// Add sections
	foreach ( $sections as $section ) {
		if ( $bp_existed == 'false' && $section['section'] == 'buddypress' )
			continue;
		$wp_customize->add_section( $shortname . $shortprefix . $section['section'], array(
			'title' => $section['title'],
			'priority' => $section['priority']
		) );
	}
	// Add settings and controls
	foreach ( $options as $o => $option ) {
		if ( ! blogsmu_option_in_customize($option) || ( $bp_existed == 'false' && $option['inblock'] == 'buddypress' ) )
			continue;
		$wp_customize->add_setting( $option['id'], array(
			'default' => $option['std'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'transport' => 'postMessage'
		) );
		$control_param = array(
			'label' => strip_tags($option['name']),
			'description' => ( isset($option['description']) && !empty($option['description']) ? $option['description'] : '' ),
			'section' => $shortname . $shortprefix . $option['inblock'],
			'settings' => $option['id'],
			'priority' => $o // make sure we have the same order as theme options :)
		);
		if ( $option['type'] == 'text' ) {
			$control_param['type'] = 'text';
			$wp_customize->add_control( new WPMUDEV_Customize_Control( $wp_customize, $option['id'].'_control', $control_param) );
		}
		else if ( $option['type'] == 'select' || $option['type'] == 'select-preview' ) {
			$control_param['type'] = 'select';
			// @TODO choices might get removed in future
			$choices = array();
			foreach ( $option['options'] as $choice )
				$choices[$choice] = $choice;
			$control_param['choices'] = $choices;
			$wp_customize->add_control( new WPMUDEV_Customize_Control( $wp_customize, $option['id'].'_control', $control_param) );
		}
		else if ( $option['type'] == 'colorpicker' ) {
			$wp_customize->add_control( new WPMUDEV_Customize_Color_Control( $wp_customize, $option['id'].'_control', $control_param ) );
		}
	}

	// Add transport script
	if ( $wp_customize->is_preview() && ! is_admin() )
		add_action('wp_footer', 'blogsmu_customize_preview', 21);
}
add_action('customize_register', 'blogsmu_customize_register');

function blogsmu_customize_preview() {
	global $options, $shortname, $shortprefix;
	?>
	<script type="text/javascript">
		function blogsmu_change_style( selector_list, property, value ){
			if ( typeof selector_list == 'string' )
				var selectors = selector_list.split(',');
			else
				var selectors = [selector_list];
			for ( s in selectors ){
				var selector = selectors[s];
				var style_attr = jQuery(selector).attr('style');
				if ( style_attr ) var styles = style_attr.split(';');
				else var styles = [];
				var in_style = false;
				for ( i in styles ){
					var style = styles[i].split(':');
					if ( property == style[0] ){
						styles[i] = property+':'+value+'!important';
						in_style = true;
						break;
					} 
				}
				if ( ! in_style )
					styles.push(property+':'+value+'!important');
				jQuery(selector).attr('style', styles.join(';'));
			}
		}
		function blogsmu_change_style_hover( selector_list, property, value, nohover_option ){
			var selectors = selector_list.split(',');
			for ( s in selectors ){
				var selector = selectors[s];
				jQuery(selector).hover(function(){
					blogsmu_change_style(this, property, value);
				}, function(){
					var nohover = wp.customize(nohover_option).get();
					if ( nohover )
						blogsmu_change_style(this, property, nohover);
					else
						blogsmu_change_style(this, property, 'none');
				});
			}
		}
		
		function blogsmu_change_font_family( selector, value ){
			// load font from Google Fonts API
			var fonts = value.split(',');
			var font = fonts[0];
			var supported_fonts = ["Cantarell", "Cardo", "Crimson Text", "Droid Sans", "Droid Serif", "IM Fell DW Pica",
				"Josefin Sans Std Light", "Lobster", "Molengo", "Neuton", "Nobile", "OFL Sorts Mill Goudy TT", 
				"Reenie Beanie", "Tangerine", "Old Standard TT", "Volkorn", "Yanone Kaffessatz", "Just Another Hand", 
				"Terminal Dosis Light", "Ubuntu"];
			var load_external = false;
			for ( i in supported_fonts ){
				if ( font == supported_fonts[i] ){
					load_external = true;
					break;
				}
			}
			if ( load_external ){
				if ( font == 'Ubuntu' ) font += ":light,regular,bold";
				font = font.replace(' ', '+');
				jQuery('body').append("<link href='http://fonts.googleapis.com/css?family="+font+"' rel='stylesheet' type='text/css'/>");
			}
			blogsmu_change_style(selector, 'font-family', value);
		}
		
		function blogsmu_change_background_gradient( selector, main_color, secondary_color ) {
			var main = wp.customize(main_color).get();
			var secondary = wp.customize(secondary_color).get();
			var background = 'background:'+main+';';
			if ( secondary ){
				background += 'background: -moz-linear-gradient(top, '+main+' 0%, '+secondary+' 100%);';
				background += 'background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'+main+'), color-stop(100%,'+secondary+'));';
				background += 'background: -webkit-linear-gradient(top, '+main+' 0%,'+secondary+' 100%);';
				background += 'background: -o-linear-gradient(top, '+main+' 0%,'+secondary+' 100%);';
				background += 'background: -ms-linear-gradient(top, '+main+' 0%,'+secondary+' 100%);';
				background += 'filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="'+main+'", endColorstr="'+secondary+'",GradientType=0 );';
				background += 'background: linear-gradient(top, '+main+' 0%,'+secondary+' 100%);';
			}
			var style = jQuery(selector).attr('style');
			jQuery(selector).attr('style', (style ? style.replace(/(background|filter):.*?;/ig, '') : '')+background);
		}
		
		window.onload = function(){
		<?php foreach ( $options as $option ): ?>
			<?php if ( ! blogsmu_option_in_customize($option) ) continue; ?>
			wp.customize( '<?php echo $option['id'] ?>', function(value) {
				value.bind(function(to){
					if ( !to ) return;
					<?php
					// Use printf for better readibility, place selector in argument
					switch ( str_replace($shortname . $shortprefix, '', $option['id']) ){
						case  'body_font':
							printf("blogsmu_change_font_family('%s', to);", "body, #custom .widget blockquote p, #custom .item-list blockquote p");
							break;
						case 'headline_font':
							printf("blogsmu_change_font_family('%s', to);", "h1, h2, h3, h4, h5, h6, #custom #header-name-alt, .one-community");
							break;
						case  'nav_font':
							printf("blogsmu_change_font_family('%s', to);", "#custom #navigation li");
							break;
						case 'font_size':
							printf("blogsmu_change_style('%s', 'font-size', (to+1)+'px'); ", "#custom .post-content");
							printf("blogsmu_change_style('%s', 'font-size', (to-1)+'px'); ", "#custom .activity-header p");
							printf("blogsmu_change_style('%s', 'font-size', to+'px'); ", "#custom .widget, #custom div.widget blockquote, #custom .widget blockquote p, #custom .bp-widget, #custom .sidebar_list, #custom .item-list li, #services-content p");
							printf("blogsmu_change_style('%s', 'font-size', (to+3)+'px'); ", "#custom div.widget h4.item-title");
							printf("blogsmu_change_style('%s', 'font-size', (to+3)+'px'); ", "#custom .widget h2, #custom .bp-widget h4");
							break;
						case 'font_line_height':
							printf("blogsmu_change_style('%s', 'line-height', (to+2)+'px'); ", "#custom .post-content");
							printf("blogsmu_change_style('%s', 'line-height', (to-2)+'px'); ", "#custom .activity-header p");
							printf("blogsmu_change_style('%s', 'line-height', to+'px'); ", "#custom .widget, #custom div.widget blockquote, #custom .widget blockquote p, #custom .bp-widget, #custom .sidebar_list, #custom .item-list li, #services-content p");
							printf("blogsmu_change_style('%s', 'line-height', (to+5)+'px'); ", "#custom div.widget h4.item-title");
							printf("blogsmu_change_style('%s', 'line-height', (to+5)+'px'); ", "#custom .widget h2, #custom .bp-widget h4");
							break;
						case 'top_header_bg_main_color':
						case 'top_header_bg_secondary_color':
							printf("blogsmu_change_background_gradient('%s', '%s', '%s');", "#top-bg", $shortname.$shortprefix.'top_header_bg_main_color', $shortname.$shortprefix.'top_header_bg_secondary_color' );
							break;
						case 'main_header_bg_main_color':
						case 'main_header_bg_secondary_color':
							printf("blogsmu_change_style('%s', 'background', '%s'); ", "#header-gfx-inner", "transparent");
							printf("blogsmu_change_background_gradient('%s', '%s', '%s');", "#header", $shortname.$shortprefix.'main_header_bg_main_color', $shortname.$shortprefix.'main_header_bg_secondary_color' );
							break;
						case 'top_header_text_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#top-bg, #top-bg .alignright, #top-bg li.user-tab");
							break;
						case 'top_header_text_link_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#top-bg a");
							break;
						case 'main_header_text_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#right-panel h4, #right-panel p.headtext");
							break;
						case 'main_header_text_link_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#right-panel p.headtext a");
							break;
						case 'featured_intro_text_shadow':
							printf("blogsmu_change_style('%s', 'text-shadow', '1px 1px 1px '+to); ", "#right-panel h4, #right-panel p.headtext");
							printf("blogsmu_change_style('%s', '-webkit-text-shadow', '1px 1px 1px '+to); ", "#right-panel h4, #right-panel p.headtext");
							printf("blogsmu_change_style('%s', '-moz-text-shadow', '1px 1px 1px '+to); ", "#right-panel h4, #right-panel p.headtext");
							break;
						case 'featured_intro_button_color':
							printf("blogsmu_change_style('%s', 'background', to); ", "#right-panel div.submit-button a");
							break;
						case 'featured_intro_button_text_link_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#right-panel div.submit-button a");
							break;
						case 'nav_bg_main_color':
						case 'nav_bg_secondary_color':
							printf("blogsmu_change_background_gradient('%s', '%s', '%s');", "#custom #navigation", $shortname.$shortprefix.'nav_bg_main_color', $shortname.$shortprefix.'nav_bg_secondary_color' );
							break;
						case 'nav_border_color':
							printf("blogsmu_change_style('%s', 'border-top', '1px solid '+to); ", "#custom #navigation");
							printf("blogsmu_change_style('%s', 'border-bottom', '1px solid '+to); ", "#custom #navigation");
							break;
						case 'nav_text_link_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#custom #nav li a");
							break;
						case 'nav_dropdown_bg_color':
							printf("blogsmu_change_style('%s', 'background', to); ", "#nav li ul, #nav li.current_page_item a, #nav li.current_menu_item a, #nav li.current-menu-item a, #nav li.home a, #nav li.selected a");
							break;
						case 'nav_dropdown_link_hover_color':
							printf("blogsmu_change_style_hover('%s', 'background', to, '%s'); ", "#nav ul li a, #nav li.current_page_item a, #nav li.current_menu_item a, #nav li.current-menu-item a, #nav li.home a, #nav li.selected a", $shortname.$shortprefix.'nav_dropdown_bg_color');
							break;
						case 'blog_global_links_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#container a, .one-community a, .services-box a, #custom ul.hlist a");
							printf("blogsmu_change_style('%s', 'color', '#fff'); ", "#post-navigator a");
							printf("blogsmu_change_style('%s', 'background', to); ", "#post-navigator a");
							printf("blogsmu_change_style('%s', 'border', '1px solid '+to); ", "#post-navigator a");
							break;
						case 'blog_global_links_hover_color':
							printf("blogsmu_change_style('%s', 'color', '#fff'); ", "#post-navigator a");
							printf("blogsmu_change_style_hover('%s', 'background', to, '%s'); ", "#post-navigator a", $shortname.$shortprefix.'blog_global_links_color');
							printf("blogsmu_change_style_hover('%s', 'border-color', to, '%s'); ", "#post-navigator a", $shortname.$shortprefix.'blog_global_links_color');
							printf("blogsmu_change_style('%s', 'background', to); ", "#post-navigator .current");
							printf("blogsmu_change_style('%s', 'border', '1px solid '+to); ", "#post-navigator .current");
							break;
						case 'footer_bg_main_color':;
						case 'footer_bg_secondary_color':
							if ( get_option('home_footer_block') == 'disable' )
								printf("blogsmu_change_background_gradient('%s', '%s', '%s');", "#custom #bottom-content", $shortname.$shortprefix.'footer_bg_main_color', $shortname.$shortprefix.'footer_bg_secondary_color' );
							printf("blogsmu_change_background_gradient('%s', '%s', '%s');", "#custom #footer", $shortname.$shortprefix.'footer_bg_main_color', $shortname.$shortprefix.'footer_bg_secondary_color' );
							break;
						case 'footer_text_color':
							if ( get_option('home_footer_block') == 'disable' )
								printf("blogsmu_change_style('%s', 'color', to); ", ".bottom-content-inner");
							printf("blogsmu_change_style('%s', 'color', to); ", "#footer, #footer li");
							break;
						case 'footer_text_link_color':
							if ( get_option('home_footer_block') == 'disable' )
								printf("blogsmu_change_style('%s', 'color', to); ", "#custom div#bottom-content a");
							printf("blogsmu_change_style('%s', 'color', to); ", "#footer a, #footer h3 a, #footer li a");
							break;
						case 'footer_header_text_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#footer h3, #footer h3 a");
							break;
						case 'footer_text_link_hover_color':
							printf("blogsmu_change_style_hover('%s', 'color', to, '%s'); ", "#footer a, #footer h3 a, #footer li a", $shortname.$shortprefix.'footer_text_link_color');
							break;
						case 'activity_block_color':
							printf("blogsmu_change_style('%s', 'background', to); ", ".activity-list .activity-content .activity-inner, .activity-list .activity-content blockquote");
							break;
						case 'activity_block_text_color':
							printf("blogsmu_change_style('%s', 'color', to); ", ".activity-list .activity-content .activity-inner, .activity-list .activity-content blockquote");
							break;
						case 'span_meta_color':
							printf("blogsmu_change_style('%s', 'background', to); ", "#custom .activity-list .activity-header a:first-child, #custom span.highlight");
							printf("blogsmu_change_style('%s', 'background', to); ", "span.activity");
							break;
						case 'span_meta_text_color':
							printf("blogsmu_change_style('%s', 'color', to); ", "#custom .activity-list .activity-header a:first-child, #custom span.highlight");
							printf("blogsmu_change_style('%s', 'color', to); ", "span.activity");
							break;
						case 'span_meta_border_color':
							printf("blogsmu_change_style('%s', 'border', '1px solid '+to); ", "#custom .activity-list .activity-header a:first-child, #custom span.highlight");
							printf("blogsmu_change_style('%s', 'border', '1px solid '+to); ", "span.activity");
							break;
						case 'span_meta_hover_color':
							printf("blogsmu_change_style_hover('%s', 'background', to, '%s'); ", "#custom .activity-list .activity-header a:first-child, #custom span.highlight", $shortname.$shortprefix.'span_meta_color');
							break;
						case 'span_meta_text_hover_color':
							printf("blogsmu_change_style_hover('%s', 'color', to, '%s'); ", "#custom .activity-list .activity-header a:first-child, #custom span.highlight", $shortname.$shortprefix.'span_meta_text_color');
							break;
						case 'span_meta_border_hover_color':
							printf("blogsmu_change_style_hover('%s', 'border-color', to, '%s'); ", "#custom .activity-list .activity-header a:first-child, #custom span.highlight", $shortname.$shortprefix.'span_meta_border_color');
							break;
					}
					?>
				});
			} );
		<?php endforeach ?>
		};
	</script>
	<?php
}

// Add additional styling to better fit on Customizer options
function blogsmu_customize_controls_footer() {
	?>
	<style type="text/css">
		.customize-control-title { line-height: 18px; padding: 2px 0; }
		.customize-control-description { font-size: 11px; color: #666; margin: 0 0 3px; display: block; }
	</style>
	<?php
}
add_action('customize_controls_print_footer_scripts', 'blogsmu_customize_controls_footer');

function blogsmu_option_in_customize( $option ) {
	global $blogsmu_use_customizer_type, $blogsmu_use_customizer_id;
	if ( in_array($option['type'], $blogsmu_use_customizer_type) || in_array($option['id'], $blogsmu_use_customizer_id) )
		return true;
	return false;
}

?>