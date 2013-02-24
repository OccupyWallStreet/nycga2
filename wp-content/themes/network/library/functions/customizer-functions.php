<?php

// WP 3.4 Theme Customizer
global $network_use_customizer_type, $network_use_customizer_id;

$network_use_customizer_type = array('colorpicker');
$network_use_customizer_id = array(
	$shortname . $shortprefix  . "header_font",
	$shortname . $shortprefix  . "body_font"
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

function network_customize_register( $wp_customize ) {
	global $options, $options3, $shortname, $shortprefix, $bp_existed, $network_use_customizer_type, $network_use_customizer_id;
	$sections = array(
		array(
			'section' => 'fonts',
			'title' => __("Fonts", 'network'),
			'priority' => 30
		), array(
			'section' => 'layout',
			'title' => __("Layout Colours", 'network'),
			'priority' => 31
		), array(
			'section' => 'text',
			'title' => __("Text Colours", 'network'),
			'priority' => 32
		), array( 
			'section' => 'navigation',
			'title' => __("Navigation Colours", 'network'),
			'priority' => 33
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
	foreach ( $options3 as $o => $option ) {
		if ( ! network_option_in_customize($option) || ( $bp_existed == 'false' && $option['inblock'] == 'buddypress' ) )
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
		add_action('wp_footer', 'network_customize_preview', 21);
}
add_action('customize_register', 'network_customize_register');

function network_customize_preview() {
	global $options3, $shortname, $shortprefix;
	?>
	<div id="theme-customizer-css"></div>
	
	<script type="text/javascript">
		var theme_customizer_css = [];
		function theme_update_css(){
			var css = '';
			for ( s in theme_customizer_css ){
				css += theme_customizer_css[s].selector + '{';
				for ( p in theme_customizer_css[s].properties ){
					var property = theme_customizer_css[s].properties[p];
					for ( v in property ){
						if ( v == 0 || v == 1 ) continue;
						css += property[0] + ':' + property[v] + property[1] + ';';
					}
				}
				css += '}';
			}
			jQuery('#theme-customizer-css').html('<style type="text/css">'+css+'</style>');
		}
		function theme_change_style( selector_list, property, values, priority ){
			if ( !priority ) priority = '';
			var prop = [property, priority];
			if ( typeof values == 'string' ) prop.push(values);
			else {
				for ( v in values ) prop.push(values[v]);
			}
			console.log(prop);
			var add_selector = true, add_property = true;
			for ( s in theme_customizer_css ){
				if ( theme_customizer_css[s].selector == selector_list ){
					add_selector = false;
					for ( p in theme_customizer_css[s].properties ){
						if ( theme_customizer_css[s].properties[p][0] == property ){
							theme_customizer_css[s].properties[p] = prop;
							add_property = false;
							break;
						}
					}
					if ( add_property ) theme_customizer_css[s].properties.push(prop)
				}
			}
			if ( add_selector ){
				theme_customizer_css.push({
					selector: selector_list,
					properties: [prop]
				});
			}
			theme_update_css();
		}
		function theme_change_font_family( selector, value ){
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
			theme_change_style(selector, 'font-family', value);
		}
		
		window.onload = function(){
		<?php foreach ( $options3 as $option ): ?>
			<?php if ( ! network_option_in_customize($option) ) continue; ?>
			wp.customize( '<?php echo $option['id'] ?>', function(value) {
				value.bind(function(to){
					if ( !to ) return;
					<?php
					// Use printf for better readibility, place selector in argument
					switch ( str_replace($shortname . $shortprefix, '', $option['id']) ){
						case 'body_font':
							printf("theme_change_font_family('%s', to);", "body");
							break;
						case 'header_font':
							printf("theme_change_font_family('%s', to);", "h1, h2, h3, h4, h5, h6, #site-logo");
							break;
						case 'feature_box_colour':
							printf("theme_change_style('%s', 'background-color', to); ", "#articleBox .articles li");
							break;
						case 'feature_box_hover_colour':
							printf("theme_change_style('%s', 'background-color', to); ", "#articleBox .articles li:hover");
							break;
						case 'header_background_colour':
							printf("theme_change_style('%s', 'background-color', to); ", "#top-navigation-bar");
							break;
						case 'content_background_colour':
							printf("theme_change_style('%s', 'background-color', to); ", ".generic-box");
							break;
						case 'font_colour':
							printf("theme_change_style('%s', 'color', to); ", "body");
							break;
						case 'link_colour':
							printf("theme_change_style('%s', 'color', to); ", "#panel a, #panel a:link, #panel a:visited, #panel a.button:hover, .tab ul.login li a, a:link");
							break;
						case 'link_visited_colour':
							printf("theme_change_style('%s', 'color', to); ", "#panel a:visited, a:visited");
							break;
						case 'link_hover_colour':
							printf("theme_change_style('%s', 'color', to); ", "#panel a.button:hover, a:hover, a:active");
							break;
						case 'header_colour':
							printf("theme_change_style('%s', 'color', to); ", "#strapline h1, #strapline h2, #site-wrapper-home h1, #site-wrapper-home h2, #site-wrapper-home h3, #site-wrapper-home h4, #site-wrapper-home h5, #site-wrapper-home h6, #site-wrapper h1, #site-wrapper h2, #site-wrapper h3, #site-wrapper h4, #site-wrapper h5, #site-wrapper h6");
							break;
						case 'site_header_colour':
							printf("theme_change_style('%s', 'color', to); ", "#header h1 a");
							break;
						case 'feature_text_colour':
							printf("theme_change_style('%s', 'color', to); ", "#articleBox h2, #articleBox h2 a");
							break;
						case 'feature_blog_title_colour':
							printf("theme_change_style('%s', 'color', to); ", "#articleBox h3, #articleBox h3 a");
							break;
						case 'nav_text_colour':
							printf("theme_change_style('%s', 'color', to); ", "#topbar ul li a");
							break;
						case 'nav_shadow_colour':
							printf("theme_change_style('%s', 'text-shadow', '-1px 2px 0 '+to); ", "#topbar ul li a");
							break;
						case 'nav_hover_text_colour':
							printf("theme_change_style('%s', 'color', to); ", "#topbar ul li a:hover");
							break;
						case 'nav_background_colour':
							printf("theme_change_style('%s', 'background-color', to); ", ".sf-menu li, .sf-menu li li, .sf-menu li li li");
							break;
						case 'nav_hover_background_colour':
							printf("theme_change_style('%s', 'background-color', to); ", ".sf-menu li:hover, #topbar ul li a:hover, .sf-menu li li:hover");
							break;
						case 'navigation_bar_background':
							printf("theme_change_style('%s', 'background-color', to); ", "#top-navigation-bar");
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
function network_customize_controls_footer() {
	?>
	<style type="text/css">
		.customize-control-title { line-height: 18px; padding: 2px 0; }
		.customize-control-description { font-size: 11px; color: #666; margin: 0 0 3px; display: block; }
	</style>
	<?php
}
add_action('customize_controls_print_footer_scripts', 'network_customize_controls_footer');

function network_option_in_customize( $option ) {
	global $network_use_customizer_type, $network_use_customizer_id;
	if ( in_array($option['type'], $network_use_customizer_type) || in_array($option['id'], $network_use_customizer_id) )
		return true;
	return false;
}

?>