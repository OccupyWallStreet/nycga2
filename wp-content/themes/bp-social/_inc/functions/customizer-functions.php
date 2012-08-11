<?php

// WP 3.4 Theme Customizer
global $buddysocial_use_customizer_type, $buddysocial_use_customizer_id;

$buddysocial_use_customizer_type = array('colorpicker', 'colourpicker');
$buddysocial_use_customizer_id = array(
	$shortname . $shortprefix  . "body_font",
	$shortname . $shortprefix  . "headline_font",
	$shortname . $shortprefix  . "font_size",
	$shortname . $shortprefix  . "font_line_height",
	$shortname . $shortprefix  . "message_text",
	//$shortname . $shortprefix  . "blog_intro_header_video",
	//$shortname . $shortprefix  . "blog_intro_header_video_alt",
	//$shortname . $shortprefix  . "blog_intro_header_image",
	//$shortname . $shortprefix  . "blog_post_style",
	//$shortname . $shortprefix  . "post_meta_status",
	//$shortname . $shortprefix  . "facebook_like_status",
	//$shortname . $shortprefix  . "member_page_layout_style",
	//$shortname . $shortprefix  . "privacy_status",
	//$shortname . $shortprefix  . "privacy_redirect",
	//$shortname . $shortprefix  . "friend_privacy_status",
	//$shortname . $shortprefix  . "friend_privacy_redirect",
	//$shortname . $shortprefix  . "create_group_status",
	//$shortname . $shortprefix  . "create_group_redirect",
	//$shortname . $shortprefix  . "stream_facebook_like_status",
	//$shortname . $shortprefix  . "header_logo",
	//$shortname . $shortprefix  . "header_on",
	//$shortname . $shortprefix  . "image_height",
	//$shortname . $shortprefix  . "ads_code",
);
$buddysocial_use_customizer_not_id = array(
	$shortname . $shortprefix  . "bg_colour",
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
			// Handle textarea
			case 'textarea':
				?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php if ( isset($this->description) && !empty($this->description) ): ?>
					<span class="customize-control-description"><?php echo $this->description ?></span>
					<?php endif ?>
					<textarea rows="10" cols="40" <?php $this->link(); ?>><?php echo esc_attr( $this->value() ); ?></textarea>
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

function buddysocial_customize_register( $wp_customize ) {
	global $options, $shortname, $shortprefix, $bp_existed, $buddysocial_use_customizer_type, $buddysocial_use_customizer_id;
	$options_list = $options;
	$sections = array(
		array(
			'section' => 'layout',
			'title' => __("Layout Settings", TEMPLATE_DOMAIN),
			'priority' => 30
		), array(
			'section' => 'gallery',
			'title' => __("Gallery Settings", TEMPLATE_DOMAIN),
			'priority' => 31
		), array(
			'section' => 'css',
			'title' => __("CSS Settings", TEMPLATE_DOMAIN),
			'priority' => 32
		), array(
			'section' => 'top-header',
			'title' => __("Top Header Settings", TEMPLATE_DOMAIN),
			'priority' => 33
		), array(
			'section' => 'intro',
			'title' => __("Intro Settings", TEMPLATE_DOMAIN),
			'priority' => 34
		), array(
			'section' => 'header',
			'title' => __("Header Settings", TEMPLATE_DOMAIN),
			'priority' => 35
		), array(
			'section' => 'nav',
			'title' => __("Navigation Settings", TEMPLATE_DOMAIN),
			'priority' => 36
		), array(
			'section' => 'post',
			'title' => __("Post Settings", TEMPLATE_DOMAIN),
			'priority' => 37
		), array(
			'section' => 'buddypress',
			'title' => __("BuddyPress Settings", TEMPLATE_DOMAIN),
			'priority' => 38
		), array(
			'section' => 'button',
			'title' => __("Button Settings", TEMPLATE_DOMAIN),
			'priority' => 39
		)
	);
	// Add sections
	foreach ( $sections as $section ) {
		if ( $bp_existed != "true" && $section['section'] == 'buddypress' )
			continue;
		$wp_customize->add_section( $shortname . $shortprefix . $section['section'], array(
			'title' => $section['title'],
			'priority' => $section['priority']
		) );
	}
	// Add settings and controls
	foreach ( $options_list as $o => $option ) {
		if ( ! buddysocial_option_in_customize($option) )
			continue;
		if ( $option['inblock'] == 'content-layout' )
			$option['inblock'] = 'layout';
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
		if ( $option['type'] == 'colorpicker' || $option['type'] == 'colourpicker' || ( isset($option['custom']) && ( $option['custom'] == 'colorpicker' || $option['custom'] == 'colourpicker' ) ) ) {
			$wp_customize->add_control( new WPMUDEV_Customize_Color_Control( $wp_customize, $option['id'].'_control', $control_param ) );
		}
		else if ( $option['type'] == 'text' || $option['type'] == 'textarea' ) {
			$control_param['type'] = $option['type'];
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
	}

	// Support Wordpress custom background
	$wp_customize->get_setting('background_color')->transport = 'postMessage';
	$wp_customize->get_setting('background_image')->transport = 'postMessage';
	$wp_customize->get_setting('background_repeat')->transport = 'postMessage';
	$wp_customize->get_setting('background_position_x')->transport = 'postMessage';
	$wp_customize->get_setting('background_attachment')->transport = 'postMessage';
	$wp_customize->get_setting('header_image')->transport = 'postMessage';
	$wp_customize->get_setting('blogname')->transport = 'postMessage';
	$wp_customize->get_setting('blogdescription')->transport = 'postMessage';

	// Add transport script
	if ( $wp_customize->is_preview() && ! is_admin() )
		add_action('wp_footer', 'buddysocial_customize_preview', 21);
}
add_action('customize_register', 'buddysocial_customize_register');

function buddysocial_customize_preview() {
	global $options, $shortname, $shortprefix;
	$options_list = $options;
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
						if ( v == 0 || v == 1 || typeof property[v] != 'string' ) continue;
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
		function theme_change_font_family( selector, value, priority ){
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
			theme_change_style(selector, 'font-family', value, priority);
		}
		function theme_change_bg_gradient( selector, main_color, secondary_color, priority ) {
			var bg_values = [main_color];
			bg_values.push('-moz-linear-gradient(top, ' + main_color + ' 0%, ' + secondary_color + ' 99%)');
			bg_values.push('-webkit-gradient(linear, left top, left bottom, color-stop(0%,' + main_color + '), color-stop(99%,' + secondary_color + '))');
			theme_change_style(selector, 'background', bg_values, priority);
			theme_change_style(selector, 'filter', 'progid:DXImageTransform.Microsoft.gradient( startColorstr="' + main_color + '", endColorstr="' + secondary_color + '",GradientType=0);', priority);
		}
		function theme_color_creator(color, per){
			color = color.toString().substring(1);
			rgb = '';
			per = per/100*255;
			if  (per < 0 ){
		        per =  Math.abs(per);
		        for (x=0;x<=4;x+=2)
		        {
		        	c = parseInt(color.substring(x, x+2), 16) - per;
		        	c = Math.floor(c);
		            c = (c < 0) ? "0" : c.toString(16);
		            rgb += (c.length < 2) ? '0'+c : c;
		        }
		    }
		    else{
		        for (x=0;x<=4;x+=2)
		        {
		        	c = parseInt(color.substring(x, x+2), 16) + per;
		        	c = Math.floor(c);
		            c = (c > 255) ? 'ff' : c.toString(16);
		            rgb += (c.length < 2) ? '0'+c : c;
		        }
		    }
		    return '#'+rgb;
		}
		
		window.onload = function(){
			wp.customize( 'blogname', function(value) {
				value.bind(function(to){
					jQuery('#home-logo h1 a').text(to);
				})
			});
			wp.customize( 'blogdescription', function(value) {
				value.bind(function(to){
					jQuery('#home-logo p').text(to);
				})
			});
			wp.customize( 'tn_buddysocial_featured_bg_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#myGallery, #myGallerySet, #flickrGallery, .jdGallery .slideElement, .jdGallery .loadingElement', 'background-color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_featured_slider_bg_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('.jdGallery .slideInfoZone', 'background-color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_featured_text_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#myGallery, #myGallery p, #myGallerySet, #flickrGallery, .jdGallery .slideElement, .jdGallery .loadingElement, #custom #myGallery a, #custom #myGallerySet a, #custom #flickrGallery a, #custom .jdGallery .slideElement a, #custom .jdGallery .loadingElement a, #myGallery a:hover, #myGallerySet a:hover, #flickrGallery a:hover, .jdGallery .slideElement a:hover, .jdGallery .loadingElement a:hover', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_body_font', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_font_family('body', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_headline_font', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_font_family('h1,h2,h3,h4,h5,h6', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_font_size', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom .activity-header p, .content p, .content .padder div, .content .padder li, #custom div.widget, #custom div.widget blockquote, #custom div.bp-widget, #custom .post-content, #custom .item-list li', 'font-size', to+'px', '!important');
				})
			});
			wp.customize( 'tn_buddysocial_font_line_height', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom .activity-header p, .content p, .content .padder div, .content .padder li, #custom div.widget, #custom div.widget blockquote, #custom div.bp-widget, #custom .post-content, #custom .item-list li', 'line-height', to+'px', '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_global_links_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('.footer a, .content a, #custom .post-content a, .post-tag a, #custom p a, #custom .widget a, #custom small a, #custom h4 a, .content-inner a, #custom li.load-more a, #custom h1.post-title a, #custom .post-tag a', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_global_links_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('.footer a:hover, .content a:hover, #custom .post-content a:hover, .post-tag a:hover, #custom p a:hover, #custom .widget a:hover, #custom small a:hover, #custom h4 a:hover, .content-inner a:hover, #custom li.load-more a:hover, #custom h1.post-title a:hover, #custom .post-tag a:hover', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_header_bg_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					var bg_sec_color = wp.customize('tn_buddysocial_blog_header_bg_sec_color').get();
					if ( bg_sec_color ) {
						theme_change_bg_gradient('#top-bar, #user-status p, .avatar-box, #wire-post-new-input', to, bg_sec_color, '!important');
						theme_change_style('#top-bar', 'border-bottom', '1px solid ' + bg_sec_color, '!important');
						theme_change_style('ul#options-nav li', 'border-right', '1px solid ' + bg_sec_color, '!important');
					}
					else {
						theme_change_style('#top-bar, #user-status p, .avatar-box, #wire-post-new-input', 'background', to, '!important');
					}
				})
			});
			wp.customize( 'tn_buddysocial_blog_header_bg_sec_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					var bg_main_color = wp.customize('tn_buddysocial_blog_header_bg_color').get();
					if ( bg_main_color ) {
						theme_change_bg_gradient('#top-bar, #user-status p, .avatar-box, #wire-post-new-input', bg_main_color, to, '!important');
						theme_change_style('#top-bar', 'border-bottom', '1px solid ' + to, '!important');
						theme_change_style('ul#options-nav li', 'border-right', '1px solid ' + to, '!important');
					}
				})
			});
			wp.customize( 'tn_buddysocial_blog_header_text_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#top-bar p, #top-bar li, #custom #user-status p a, .avatar-box', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_header_text_link_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#top-bar a, #user-status p,  #custom .avatar-box h3, #custom .avatar-box a', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_header_text_link_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#top-bar a:hover, #user-status p a:hover, #custom .avatar-box a:hover', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_message_text', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					jQuery('.home-intro-text').html(to);
				})
			});
			wp.customize( 'tn_buddysocial_blog_intro_header_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					var bg_sec_color = wp.customize('tn_buddysocial_blog_intro_header_sec_color').get();
					if ( bg_sec_color ) {
						theme_change_bg_gradient('#top-header', to, bg_sec_color, '!important');
					}
					else {
						theme_change_style('#top-header', 'background', to, '!important');
					}
				})
			});
			wp.customize( 'tn_buddysocial_blog_intro_header_sec_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					var bg_main_color = wp.customize('tn_buddysocial_blog_intro_header_color').get();
					if ( bg_main_color ){
						theme_change_bg_gradient('#top-header', bg_main_color, to, '!important');
					}
				})
			});
			wp.customize( 'tn_buddysocial_blog_intro_text_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom div#top-header .home-intro p, #custom div#top-header .home-intro strong, #custom div#top-header .home-intro h1, #custom div#top-header .home-intro h3', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_intro_header_link_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom #top-header a', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_intro_header_link_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom #top-header a:hover', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_subnav_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom ul#nav li a, #custom #nav li a:hover, #custom #nav li a, #custom #nav li.current a, #custom #nav li.selected a, #custom ul#nav li.current_page_item ul li a,#custom ul#nav li.current-menu-item ul li a', 'background-color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_subnav_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom #nav li a:hover, #custom #nav li.current a:hover, #custom #nav li.selected a:hover, #custom ul#nav li.current_page_item ul li a:hover,#custom ul#nav li.current-menu-item ul li a:hover, #custom ul#nav li.current a, #custom ul#nav li.selected a, #custom ul#nav li.current_page_item a,#custom ul#nav li.current-menu-item a', 'background-color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_subnav_link_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom ul#nav li a, #custom #nav li a:hover, #custom #nav li a, #custom #nav li.current a, #custom #nav li.selected a, #custom ul#nav li.current_page_item ul li a,#custom ul#nav li.current-menu-item ul li a', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_blog_subnav_link_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('#custom #nav li a:hover, #custom #nav li.current a:hover, #custom #nav li.selected a:hover, #custom ul#nav li.current_page_item ul li a:hover,#custom ul#nav li.current-menu-item ul li a:hover, #custom ul#nav li.current a, #custom ul#nav li.selected a, #custom ul#nav li.current_page_item a,#custom ul#nav li.current-menu-item a', 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_button_bg_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("a.button", 'background-color', to, '!important');
					theme_change_style('button, a.button, input[type=submit], input[type=button], input[type=reset], ul.button-nav li a, div.generic-button a, .comment-reply-link, input.button, input.submit, #custom ul#nav li#current_user a, button:hover, a.button:hover, a.button:focus, input[type=submit]:hover, input[type=button]:hover, input[type=reset]:hover, ul.button-nav li a:hover, ul.button-nav li.current a, div.generic-button a:hover, .comment-reply-link:hover', 'background', '-moz-linear-gradient(top, #ffffff 0%, ' + to + ' 99%);', '!important');
						theme_change_style('button, a.button, input[type=submit], input[type=button], input[type=reset], ul.button-nav li a, div.generic-button a, .comment-reply-link, input.button, input.submit, #custom ul#nav li#current_user a, button:hover, a.button:hover, a.button:focus, input[type=submit]:hover, input[type=button]:hover, input[type=reset]:hover, ul.button-nav li a:hover, ul.button-nav li.current a, div.generic-button a:hover, .comment-reply-link:hover', 'background', '-webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,' + to + '));', '!important');
						theme_change_style('button, a.button, input[type=submit], input[type=button], input[type=reset], ul.button-nav li a, div.generic-button a, .comment-reply-link, input.button, input.submit, #custom ul#nav li#current_user a, button:hover, a.button:hover, a.button:focus, input[type=submit]:hover, input[type=button]:hover, input[type=reset]:hover, ul.button-nav li a:hover, ul.button-nav li.current a, div.generic-button a:hover, .comment-reply-link:hover', 'filter', 'progid:DXImageTransform.Microsoft.gradient( startColorstr="#ffffff", endColorstr="' + to + '",GradientType=0);', '!important');
				})
			});
			wp.customize( 'tn_buddysocial_button_border_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("input[type='submit'], input[type='button'], input.button, input.submit, #custom #item-buttons .generic-button a, #item-meta .generic-button a, a.button", 'border', '1px solid ' + to, '!important');
					theme_change_style("#custom ul#nav li#current_user a", 'border-top', '1px solid ' + to, '!important');
					theme_change_style("#custom ul#nav li#current_user a", 'border-left', '1px solid ' + to, '!important');
					theme_change_style("#custom ul#nav li#current_user a", 'border-right', '1px solid ' + to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_button_text_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("input[type='submit'], input[type='button'], input.button, input.submit, #custom #item-buttons .generic-button a, #item-meta .generic-button a, #custom ul#nav li#current_user a, a.button, #custom .activity-list div.activity-meta a.fav, #custom .activity-list div.activity-meta a:hover.fav, #custom .activity-list div.activity-meta a.acomment-reply", 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_span_meta_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("#custom .activity-list .activity-header a:first-child, #custom span.highlight, span.activity", 'background-color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_span_meta_border_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("#custom .activity-list .activity-header a:first-child, #custom span.highlight, span.activity", 'border', '1px solid ' + to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_span_meta_text_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("#custom .activity-list .activity-header a:first-child, #custom span.highlight, span.activity", 'color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_span_meta_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("#custom .activity-list .activity-header a:first-child:hover, #custom span.highlight:hover", 'background-color', to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_span_meta_border_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("#custom .activity-list .activity-header a:first-child:hover, #custom span.highlight:hover", 'border', '1px solid ' + to, '!important');
				})
			});
			wp.customize( 'tn_buddysocial_span_meta_text_hover_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style("#custom .activity-list .activity-header a:first-child:hover, #custom span.highlight:hover", 'color', to, '!important');
				})
			});
			
			
		
			wp.customize( 'background_color', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('body', 'background-color', to, '!important');
				})
			});
			wp.customize( 'background_image', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('body', 'background-image', 'url('+to+')', '!important');
					theme_change_style('body', 'background-repeat', wp.customize('background_repeat').get(), '!important');
					theme_change_style('body', 'background-position', 'top '+wp.customize('background_position_x').get(), '!important');
					theme_change_style('body', 'background-attachment', wp.customize('background_attachment').get(), '!important');
				})
			});
			wp.customize( 'background_repeat', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('body', 'background-repeat', to, '!important');
				})
			});
			wp.customize( 'background_position_x', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('body', 'background-position', 'top '+to, '!important');
				})
			});
			wp.customize( 'background_attachment', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					theme_change_style('body', 'background-attachment', to, '!important');
				})
			});
			wp.customize( 'header_image', function(value) {
				value.bind(function(to){
					if ( ! to )
						return;
					jQuery('#custom-img-header img').attr('src', to);
				})
			});
			
		};
	</script>
	<?php
}

// Add additional styling to better fit on Customizer options
function buddysocial_customize_controls_footer() {
	?>
	<style type="text/css">
		.customize-control-title { line-height: 18px; padding: 2px 0; }
		.customize-control-description { font-size: 11px; color: #666; margin: 0 0 3px; display: block; }
		.customize-control input[type="text"], .customize-control textarea { width: 98%; line-height: 18px; margin: 0; }
	</style>
	<?php
}
add_action('customize_controls_print_footer_scripts', 'buddysocial_customize_controls_footer');

function buddysocial_option_in_customize( $option ) {
	global $buddysocial_use_customizer_type, $buddysocial_use_customizer_id, $buddysocial_use_customizer_not_id;
	if ( in_array($option['id'], $buddysocial_use_customizer_not_id) )
		return false;
	if ( in_array($option['type'], $buddysocial_use_customizer_type) || in_array($option['id'], $buddysocial_use_customizer_id) || ( isset($option['custom']) && in_array($option['custom'], $buddysocial_use_customizer_type) ) )
		return true;
	return false;
}

?>