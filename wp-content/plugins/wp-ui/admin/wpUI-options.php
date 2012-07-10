<?php

require_once( dirname( __FILE__ ) . '/admin-options.php' );

$wpui_options = get_option( 'wpUI_options' );

$wpui_skins_list_pre = wpui_get_skins_list();


global $wpui_options_list;

// $wpui_option_page->set_sections($sects);
$wpui_options_list = array(
	'enable_tabs'	=>	array(
		'id'		=>	'enable_tabs',
		'title'		=>	__('Tabs', 'wp-ui'),
		'desc'		=>	__('Enable', 'wp-ui'),
		'section'	=>	'general',
		'type'		=>	'checkbox'				
	),
	'enable_accordion'	=>	array(
		'id'		=>	'enable_accordion',
		'title'		=>	__('Accordions', 'wp-ui'),
		'desc'		=>	__('Enable', 'wp-ui'),
		'section'	=>	'general',
		'type'		=>	'checkbox'				
	),
	'enable_spoilers'	=>	array(
		'id'		=>	'enable_spoilers',
		'title'		=>	__('Enable Collapsibles', 'wp-ui'),
		'desc'		=>	__('Enable', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'general',
	),
	'enable_dialogs'	=>	array(
		'id'		=>	'enable_dialogs',
		'title'		=>	__('Dialogs', 'wp-ui'),
		'desc'		=>	__('Enable', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'general',
	),
	'enable_pagination'	=>	array(
		'id'		=>	'enable_pagination',
		'title'		=>	__('Pagination support', 'wp-ui'),
		'desc'		=>	__('Enable', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'general',
	),
	'enable_quicktags_buttons'	=>	array(
		'id'		=>	'enable_quicktags_buttons',
		'title'		=>	__('HTML editor buttons', 'wp-ui'),
		'desc'		=>	__('Enable', 'wp-ui'),
		'section'	=>	'general',
		'type'		=>	'checkbox'	
	),
	'topnav'	=>	array(
		'id'		=>	'topnav',
		'title'		=>	__('Top next/previous links', 'wp-ui'),
		'desc'		=>	__('Enable top navigation on tabs.', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'general',
	),
	'bottomnav'	=>	array(
		'id'		=>	'bottomnav',
		'title'		=>	__('Bottom next/previous links', 'wp-ui'),
		'desc'		=>	__('Enable bottom navigation on tabs.', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'general',
	),
	'enable_tinymce_menu'	=>	array(
		'id'		=>	'enable_tinymce_menu',
		'title'		=>	__('TinyMCE menu', 'wp-ui'),
		'desc'		=>	__('Enable.', 'wp-ui'),
		'section'	=>	'general',
		'type'		=>	'checkbox'	
	),
	'enableWidgets'	=>	array(
		'id'		=>	'enable_widgets',
		'title'		=>	__('WP Widgets', 'wp-ui'),
		'desc'		=>	__('Enable wordpress widgets.', 'wp-ui'),
		'section'	=>	'general',
		'type'		=>	'checkbox'	
	),
	'enablePostWi'	=>	array(
		'id'		=>	'enable_post_widget',
		'title'		=>	__('Post bottom widget', 'wp-ui'),
		'desc'		=>	__('Enable the popular/recent/random/related posts shown at the end of each post. Requires cache option to be set.', 'wp-ui'),
		'section'	=>	'general',
		'type'		=>	'checkbox'	
	),	
	'enableCacheWi'	=>	array(
		'id'		=>	'enable_cache',
		'title'		=>	__( 'Cache images and scripts', 'wp-ui'),
		'desc'		=>	__( 'Uncheck to disable the cache feature for thumbnail images and scripts. ', 'wp-ui' ),
		'section'	=>	'general',
		'type'		=>	'checkbox'	
	),	
	
	
	
	
	
	'load_all_styless'=>	array(
		'id'		=>	'load_all_styles',
		'title'		=>	__('Load Multiple Styles', 'wp-ui'),
		'desc'		=>	sprintf( __( 'Load multiple css3 styles. %1$s Select your styles %2$s', 'wp-ui' ), '<a href="#" id="wpui-combine-css3-files" class="button-secondary">', '</a>' ),
		'type'		=>	'checkbox',
		'section'	=>	'style'
	),
	'selected_styles'	=>	array(
		'id'		=>	'selected_styles',
		'title'		=>	__( 'Style selection', 'wp-ui' ),
		'desc'		=>	__( 'Select styles you want to load.', 'wp-ui' ),
		'section'	=>	'style',
		'type'		=>	'textarea',
		'textarea_size'	=>	array(
			'cols'	=>	'10',
			'rows'	=>	'5',
			'autocomplete'	=>	'off'
		)		
	),
	'tabstyle'=>	array(
		'id'		=>	'tab_scheme',
		'title'		=>	__('Default style<br /><small>All widgets</small>', 'wp-ui'),
		'desc'		=>	__('Select a <u>default</u> style. Shortcode argument "style" overrides.<br /> ex. <code>[wptabs style="wpui-quark"]</code>', 'wp-ui'),
		'type'		=>	'select',
		'section'	=>	'style',
		'choices'	=>	$wpui_skins_list_pre,
		'extras'	=>	'' . __( 'Preview ', 'wp-ui' ) . '<a id="wpui_styles_preview" href="" class="button-secondary">' . __( 'WP UI CSS3 Styles', 'wp-ui' ) . '</a>  <a id="jqui_styles_preview" href="#" class="button-secondary">' . __( 'jQuery UI themes', 'wp-ui' ) . '</a>'
	),	
	
	'jqui_custom'	=>	array(
		'id'		=>	'jqui_custom_themes',
		'title'		=>	__('jQuery UI custom themes<br /><small>Manage Custom themes. Not sure? <a target="_blank" href="http://kav.in/wp-ui-using-jquery-ui-custom-themes/">follow this guide</a>.</small>'),
		'desc'		=>	'<div id="jqui_theme_list" ></div><a href="#" class="button-secondary" title="' . __( 'This will scan the directory wp-ui under uploads for themes.', 'wp-ui' ) . '" id="jqui_scan_uploads">' . __('Scan Uploads', 'wp-ui' ) . '</a>&nbsp;<a href="#" class="button-secondary" id="jqui_add_theme">' . __( 'Add a theme/style', 'wp-ui' ). '</a>',
		'type'		=>	'textarea',
		'section'	=> 'style',
		'textarea_size'	=>	array(
			'cols'	=>	'60',
			'rows'	=>	'5',
			'autocomplete'	=>	'off'
		)
	),
	'custom_styles'		=>	array(
		'id'		=>	'custom_css',
		'title'		=>	__('Custom CSS', 'wp-ui'),
		'desc'		=>	__('Enter additional css rules here, taking care on the CSS syntax.', 'wp-ui'),
		'type'		=>	'textarea',
		'section'	=>	'style',
		'textarea_size'	=>	array(
			'cols'	=>	'60',
			'rows'	=>	'10'
		)
	),
	'dialog_wid'	=>	array(
		"id"		=>	'dialog_width',
		'title'		=>	__('Dialog Width', 'wp-ui'),
		'desc'		=>	__('Default width of dialogs with the suffix', 'wp-ui') . '( px | em | % )',
		'type'		=>	'text',
		'section'	=>	'style'
	),
	
	
	// =====================
	// = Effects and other =
	// =====================
	
	'tabsfx'		=>	array(
		'id'		=>	'tabsfx',
		'title'		=>	__('Tabs effects', 'wp-ui'),
		'desc'		=>	__('Tabs show effect.', 'wp-ui'),
		'type'		=>	'select',
		'section'	=>	'effects',
		'choices'	=>	array(
			'none'		=>	'None',
			'slideDown'	=> 'Slide down',
			'fadeIn'	=>	'Fade In',
		)
		
	),
	'fxSpeed'	=>	array(
		'id'		=>	'fx_speed',
		'title'		=>	__('Effect speed', 'wp-ui'),
		'desc'		=>	__("Enter the animation speed. Larger number - slow, smaller number - quick. Example - 200, 600, 900, 'fast', 'slow'.", 'wp-ui'),
		'type'		=>	'text',
		'section'	=>	'effects'
	),

	'tabsrotate'	=>	array(
		'id'	=>	'tabs_rotate',
		'title'	=>	__('Tabs rotation', 'wp-ui'),
		'desc'	=>	__('Behavior on auto rotation. Rotation can be enabled with shortcode attribute "rotate". Example : ', 'wp-ui' ) . '<code>[wptabs rotate="6000"]</code> or <code>[wptabs rotate="6s"]</code>',
		'type'	=>	'select',
		'section'	=>	'effects',
		'choices'	=>	array(
			'disable'	=>	__( 'None', 'wp-ui' ),
			'always'	=>	__( 'Keep rotating', 'wp-ui' ),			
			'stop'		=>	__( 'Stop on Click', 'wp-ui' ),
		)
	),
	

	'tabz_event'	=>	array(
		'id'	=>	'tabs_event',
		'title'	=>	__('Tabs trigger event', 'wp-ui'),
		'desc'	=>	__('Open Tabs on click or mouseover.', 'wp-ui'),
		'type'	=>	'select',
		'section'	=>	'effects',
		'choices'	=>	array(
			'click'		=>	__( 'Click', 'wp-ui' ),			
			'mouseover'	=>	__( 'Mouseover', 'wp-ui' ),
		)
	),	
	'collapsible_tabbies' => array(
		'id'		=>	'collapsible_tabs',
		'title'		=>	__('Collapsible Tabs', 'wp-ui'),
		'desc'		=>	__( 'Enable all panels in a tabset to be collapsed.', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'effects'
	),		
	
	
	// Accordion

	'accord_event'	=>	array(
		'id'	=>	'accord_event',
		'title'	=>	__('Accordion trigger event', 'wp-ui'),
		'desc'	=>	__('Open accordion on click or mouseover.', 'wp-ui'),
		'type'	=>	'select',
		'section'	=>	'effects',
		'choices'	=>	array(
			'click'		=>	__( 'Click', 'wp-ui' ),			
			'mouseover'	=>	__( 'Mouseover', 'wp-ui' ),
		)
	),	
	'accordion_autoheight'	=>	array(
		'id'		=>	'accord_autoheight',
		'title'		=>	__('Accordion auto height', 'wp-ui'),
		'desc'		=>	__('Uniform accordion panel height.', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'effects'
	),
	'collapsible_accordions' => array(
		'id'		=>	'accord_collapsible',
		'title'		=>	__('Collapsible Accordions', 'wp-ui'),
		'desc'		=>	__('Enable all sections of accordion to be closed, and at load.', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'effects'
	),
	'accord_Easing'	=>	array(
		'id'		=>	'accord_easing',
		'title'		=>	__('Easing for the Accordion', 'wp-ui'),
		'desc'		=>	__('Optional : easing effect on accordion animation', 'wp-ui'),
		'section'	=>	'effects',
		'type'		=>	'select',
		'choices'	=>	array(
			"false"       	   =>	"disable",
			"linear"           =>	"linear",
			"swing"            =>	"swing",
			"easeInQuad"       =>	"easeInQuad",
			"easeOutQuad"      =>	"easeOutQuad",
			"easeInOutQuad"    =>	"easeInOutQuad",
			"easeInCubic"      =>	"easeInCubic",
			"easeOutCubic"     =>	"easeOutCubic",
			"easeInOutCubic"   =>	"easeInOutCubic",
			"easeInQuart"      =>	"easeInQuart",
			"easeOutQuart"     =>	"easeOutQuart",
			"easeInOutQuart"   =>	"easeInOutQuart",
			"easeInQuint"      =>	"easeInQuint",
			"easeOutQuint"     =>	"easeOutQuint",
			"easeInOutQuint"   =>	"easeInOutQuint",
			"easeInSine"       =>	"easeInSine",
			"easeOutSine"      =>	"easeOutSine",
			"easeInOutSine"    =>	"easeInOutSine",
			"easeInExpo"       =>	"easeInExpo",
			"easeOutExpo"      =>	"easeOutExpo",
			"easeInOutExpo"    =>	"easeInOutExpo",
			"easeInCirc"       =>	"easeInCirc",
			"easeOutCirc"      =>	"easeOutCirc",
			"easeInOutCirc"    =>	"easeInOutCirc",
			"easeInElastic"    =>	"easeInElastic",
			"easeOutElastic"   =>	"easeOutElastic",
			"easeInOutElastic" =>	"easeInOutElastic",
			"easeInBack"       =>	"easeInBack",
			"easeOutBack"      =>	"easeOutBack",
			"easeInOutBack"    =>	"easeInOutBack",
			"easeInBounce"     =>	"easeInBounce",
			"easeOutBounce"    =>	"easeOutBounce",
			"easeInOutBounce"  =>	"easeInOutBounce"

		)
	),
	'mousewheel_tabs'	=>	array(
		'id'		=>	'mouse_wheel_tabs',
		'title'		=>	__( 'Tabs mousewheel navigation', 'wp-ui' ),
		'desc'		=>	__( 'Scroll and switch between tabs.', 'wp-ui'),
		'section'	=>	'effects',
		'type'		=>	'select',
		'choices'	=>	array(
			"false"		=>	"disable",
			"list"      =>	"On List",
			"panels"    =>	"On Panels"
		)
	),
	
	
	// ================
	// = Text options =
	// ================
	'tab_nav_prev'	=>	array(
		'id'		=>	'tab_nav_prev_text',
		'title'		=>	__('Previous tab - button text<br /><small>Tabs Navigation</small>', 'wp-ui'),
		'desc'		=>	__( 'Tabs previous button. Default is "Previous".', 'wp-ui'),
		'section'	=>	'text',
		'type'		=>	'text'
	),
		
	'tab_nav_next'	=>	array(
		'id'		=>	'tab_nav_next_text',
		'title'		=>	__('Next tab - button text<br /><small>Tabs Navigation</small>', 'wp-ui'),
		'desc'		=>	__('Tabs Next button. Default is "Next". ', 'wp-ui'),
		'section'	=>	'text',
		'type'		=>	'text'
	),
	
	'showtext'		=>	array(
		'id'		=>	'spoiler_show_text',
		'title'		=>	__('Closed WP Spoiler text', 'wp-ui'),
		'desc'		=>	__( 'Displayed on hovering over collapsed spoiler\'s header. Leave blank to disable.', 'wp-ui'),
		'section'	=>	'text',
		'type'		=>	'textarea',
		'textarea_size'	=>	array(
			'cols'	=>	'60',
			'rows'	=>	'2'
		)
	),
	
	'hidetext'		=>	array(
		'id'		=>	'spoiler_hide_text',
		'title'		=>	__('Open WP spoiler text', 'wp-ui' ),
		'desc'		=>	__( 'Displayed on hovering over header on open spoiler.', 'wp-ui'),
		'section'	=>	'text',
		'type'		=>	'textarea',
		'textarea_size'	=>	array(
			'cols'	=>	'60',
			'rows'	=>	'2'
		)
	),
	
	/**
	 * Advanced options
	 */
	'bleeding_edge'	=> array(
		'id'		=>	'bleeding_edge',
		'title'		=>	__( 'Bleeding Edge', 'wp-ui' ),
		'desc'		=>	__( 'Enable experimental features.', 'wp-ui' ),
		'type'		=>	'checkbox',
		'section'	=>	'advanced'
	),
	'alternative_codes'	=>	array(
		'id'		=>	'alt_sc',
		'title'		=>	__( 'Alternative shortcodes, Shorter.' ),
		'desc'		=>	__( 'Use shorter codes. ', 'wp-ui' ) . 'ex.<br /><ul><li>[<strong>tabs</strong>] instead of [wptabs]</li><li>[<strong>tabname</strong>] instead of [wptabtitle]</li><li>[<strong>tabcont</strong>] => [wptabcontent]</li><li>[<strong>spoiler</strong>] instead of [wpspoiler]</li><li>[<strong>dialog</strong>] instead of [wpdialog]</li></ul>',
		'type'		=>	'checkbox',
		'section'	=>	'advanced'
	),
	'jquery_include'	=>	array(
		'id'		=>	'jquery_disabled',
		'title'		=>	__('Donot load the jQuery copy', 'wp-ui'),
		'desc'		=>	__('Check this box to prevent loading jQuery & UI libs from Google CDN. <br /> <br />Please note: Recent versions of jQuery and jQuery UI javascript libraries are required for the functionality of WP UI. This Plugin\'s components might <b>not</b> work as expected with the older versions of jQuery and UI.', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'advanced'
	),
	'script_cond'		=>	array(
		'id'		=>	'script_conditionals',
		'title'		=>	__('Conditional script load logic', 'wp-ui'),
		'desc'		=>	__( 'Use <a href="http://codex.wordpress.org/Conditional_Tags" title="Learn more on Wordpress conditional tags on Codex" target="_blank">wordpress conditional tags</a>  to load limit/prevent scripts from loading. <font style="" <br><strong>Examples</strong> - <br><ul style="list-style: disc inside none"> <li>To load only on single pages, input <code>is_single()</code>, similarly <code>is_front_page()</code> to load only on frontpage.</li> <li><code>!is_page()</code> disables it on all pages.</li><li>Use <code>||</code> (or) or <code>&&</code> operators to define a complex conditional clause. <code>is_single() && is_page( \'about\' ) && in_category( array( 1,2,3 ) ) </li></ul>', 'wp-ui'),
		'type'		=>	'textarea',
		'section'	=>	'advanced',
		'textarea_size'	=>	array(
			'cols'	=>	'60',
			'rows'	=>	'2'
		)
	),	
	
	"scripts_adv"	=>	array(
		'id'		=>	'load_scripts_on_demand',
		'title'		=>	__(	'Demand load scripts', 'wp-ui' ),
		'desc'		=>	__( 'Load needed components on demand. With jQuery. This scripts are significantly newer versions than the regular ones.' ),
		'type'		=>	'checkbox',
		'section'	=>	'advanced'
	),	
	"cookies"		=>	array(
		'id'		=>	'use_cookies',
		'title'		=>	__(	'Use cookies for tabs', 'wp-ui' ),
		'desc'		=>	__( 'WP UI makes use of cookies to remember the state of the selected tabs. Click here to disable the behavior. jQuery cookie plugin by Klaus Hartl.'),
		'type'		=>	'checkbox',
		'section'	=>	'advanced'
	),
	
	"link_hist"		=>	array(
		'id'		=>	'linking_history',
		'title'		=>	__(	'Linking and History', 'wp-ui' ),
		'desc'		=>	__( 'Uncheck here to disable history and linking. jQuery hashchange plugin by Ben Alman.'),
		'type'		=>	'checkbox',
		'section'	=>	'advanced'
	),	
	'doc_write_fix'	=>	array(
		'id'		=>	'docwrite_fix',
		'title'		=>	__( 'Blank page fix<br /><small>document.write issue</small>', 'wp-ui' ),
		'desc'		=>	__( 'Enable to fix the blank page issue that results when including other scripts within tabs/accordion<br> <small>Known scripts : Twitter profile widget, e-commerce widgets. </small>', 'wp-ui'),
		'type'		=>	'checkbox',
		'section'	=>	'advanced'
	),

	'relative_timez'	=>	array(
		'id'		=>	'relative_times',
		'title'		=>	__( 'Relative time', 'wp-ui' ),
		'desc'		=>	__( 'Display relative time on posts retrieved by WP UI.' , 'wp-ui' ) . '<code>Example : 9 days ago.</code>',
		'type'		=>	'checkbox',
		'section'	=>	'posts'
	),
	'excerpt_length'	=>	array(
		'id'		=>	'excerpt_length',
		'title'		=>	__( 'Default excerpt length', 'wp-ui' ),
		'desc'		=>	__( 'Maximum limit for the excerpts. Default is upto the ', 'wp-ui' ) . ' <code>&lt;!--more--&gt;</code>',
		'type'		=>	'text',
		'section'	=>	'posts'
	),
	
	'postss_widgets' => array(
		'id'		=>	'post_widget',
		'type'		=>	'multiple',
		'title'		=>	__( 'Post widget', 'wp-ui' ),
		'desc'		=>	__( 'Popular/Recent/Random/Related posts can be shown at end of each post.', 'wp-ui' ),
		'section'	=>	'posts',
		'fields'	=>	array(
			array(
				'idkey'		=>	'title',
				'type'		=>	'textinput',
				'desc'		=>	'',
				'text_length' => '40',
				'enclose'	=>	array(
					'before'	=>	'Title : ',
					'after'		=>	'<br /><br />'
				)
			),
			array(
				'idkey'		=>	'type',
				'type'		=>	'select',
				'desc'		=>	__( 'Type of posts shown', 'wp-ui' ),
				'choices'	=>	array(
					"popular"	=>	__( "Popular", 'wp-ui' ),
					"recent"    =>	__( "Recent", 'wp-ui' ),
					"related"   =>	__( 'Related', 'wp-ui' ),
					"random"	=>	__( 'Random', 'wp-ui' )
				),
				'enclose'	=>	array(
					'before'	=>	'   Type',
					'after'		=>	''
				)
			),
			array(
				'idkey'		=>	'number',
				'type'		=>	'textinput',
				'desc'		=>	'Number',
				'text_length'=> '3',
				'enclose'	=>	array(
					'before'	=>	'   Number of posts',
					'after'		=>	''
				)
			),
			array(
				'idkey'		=>	'per_row',
				'type'		=>	'select',
				'desc'		=>	'Per row',
				'choices'	=>	array( 2 => ' 2 ', 3 => ' 3 ', 4 => ' 4 ' ),
				'enclose'	=>	array(
					'before'	=>	'   Per row',
					'after'		=>	''
				)
			),
			// array(
			// 	'idkey'		=> 'default_image',
			// 	'desc'		=>	'Default image',
			// 	'type'		=>	'media-upload',
			// 	'text_length' => 12,
			// 	'enclose'	=>	array(
			// 		'before'	=>	'<br /><br />Default thumbnail image',
			// 		'after'		=>	'(will automatically be resized)'
			// 	)
			// )
		)
	),

	'post_widgets_image'	=>	array(
		'id'		=>	'post_default_thumbnail',
		'title'		=>	__( 'Default thumbnail image', 'wp-ui' ),
		'desc'		=>	__( 'The default image in case, post thumbnail is not available.', 'wp-ui' ),
		'type'		=>	'multiple',
		'section'	=>	'posts',
		'fields'	=>	array(
			array(
				'idkey'		=>	'url',
				'type'		=>	'media-upload',
				'desc'		=>	'',
				'text_length' => 18,
				'enclose'	=>	array(
					'before' => 'Upload a file or select one<br />',
					'after'	=>	'    ',
				)
			),
			array(
				'idkey'		=>	'width',
				'type'		=>	'textinput',
				'text_length'=> '3',
				'desc'		=>	'',
				'enclose'	=>	array(
					'before' => 'Width : ',
					'after'	=>	'    ',
				)
			),
			array(
				'idkey'		=>	'height',
				'type'		=>	'textinput',
				'text_length'=> '3',
				'desc'		=>	'',
				'enclose'	=>	array(
					'before' => 'Height : ',
					'after'	=>	'<br />',
				)
			)
	
		)
		
	),
	
	
	
	'post_template_one'	=>	array(
		'id'		=>	'post_template_1',
		'title'		=>	__('Template 1', 'wp-ui' ) . '<br /><small>' . __( 'Usually the default for the Tabs and accordions on posts/feeds', 'wp-ui') . '</small>',
		'desc'		=> __( 'Modify the template structure here. Use the variables within curled brackets. Click the top-right help link->Posts for reference.'),
		'type'		=>	'textarea',
		'section'	=>	'posts',
	'textarea_size'	=>	array(
		'cols'	=>	'60',
		'rows'	=>	'10',
		'autocomplete'	=>	'off'
	)
	),

	'post_template_two'	=>	array(
		'id'		=>	'post_template_2',
		'title'		=>	__('Template 2' , 'wp-ui').'<br /><small>' . __('Usually the default for spoilers and dialogs', 'wp-ui') . '</small>',
		'desc'		=> __( 'Modify the template structure here. Use the variables within curled brackets.'),
		'type'		=>	'textarea',
		'section'	=>	'posts',
	'textarea_size'	=>	array(
		'cols'	=>	'60',
		'rows'	=>	'10',
		'autocomplete'	=>	'off'
	)
	),

);

if ( ! wpui_less_33() )
$wpui_options_list[ 'wpui_tour' ] = array(
		'id'		=>	'tour',
		'title'		=>	__('View Tour', 'wp-ui'),
		'desc'		=>	__('View editor page tour to learn more about WP UI.', 'wp-ui'),
		'section'	=>	'general',
		'type'		=>	'checkbox'				
);



/**
 * Like preg_grep wildcard search, but this searches keys.
 */
function preg_grep_keys( $pattern, $array ) {
	if ( !is_array( $array ) ) return;
	$results = preg_grep( $pattern, array_keys( $array ) );
	if ( empty( $results ) )
		return false;
	$resultArr = array();
	foreach( $results as $result ) {
		$resultArr[ $result ] = $array[ $result ]; 
	}
	return $resultArr;	
} // end function preg_grep_keys


/**
 * Adds custom HTML templates if found.
 */
if ( isset( $wpui_options ) ) {
	$wpui_addl_templates = preg_grep_keys( '/^post_template_[3-9]{1,2}$/', $wpui_options );
	if ( is_array( $wpui_addl_templates ) ) {
		foreach( $wpui_addl_templates as $key=>$value ) {
			$valKey = intval( str_ireplace( 'post_template_' , '', $key ) );
			$wpui_options_list[ 'post_template_' . $valKey ] = array(
				'id'		=>	'post_template_' . $valKey,
				'title'		=>	__('Template ' . $valKey . '<br /><small>Use <code>template="' . $valKey . '" </code>with any of the compatible shortcodes.</small>', 'wp-ui'),
				'desc'		=> __( 'Modify the template structure here. Use the variables within curled brackets. <input name="wpUI_options[reset_post_template_' . $valKey . ']"  type="submit" value="Delete">' ),
				'type'		=>	'textarea',
				'section'	=>	'posts',
			'textarea_size'	=>	array(
				'cols'	=>	'60',
				'rows'	=>	'10',
				'autocomplete'	=>	'off'
			)				
			);			
			
		}
	}
}




if ( class_exists( 'quark_admin_options' ) ) {
/**
 *	WP UI options
 */
class wpUI_options extends quark_admin_options
{
	
	function __construct() {
		
		$this->name	= 'WP UI';
		$this->db_prefix = 'wpUI';
		$this->page_prefix = 'wpUI';

		$this->sections = array(
			'general'	=>	__('General', 'wp-ui'),
			'style'		=>	__('Style', 'wp-ui'),
			'effects'	=>	__('Effects', 'wp-ui'),
			'text'		=>	__('Text', 'wp-ui'),
			'posts'	=>	__('Posts', 'wp-ui'),
			'advanced'	=>	__('Advanced', 'wp-ui')	
		);
		
		global $wpui_options_list;
		$this->fields = $wpui_options_list;
		$this->options = get_option( 'wpUI_options', array() );
		add_action('plugin_wpUI_load_scripts', array(&$this, 'admin_scripts_styles'));
		add_action('plugin_wpUI_load_scripts', array(&$this, 'admin_styles'));		

		add_action( 'admin_print_scripts', array( &$this, 'editor_vars' ) );
		
		if ( is_admin() )
			add_action( 'admin_init', array( &$this, 'wpui_editor_dialogs' ) );
		
		parent::__construct();
	}
	
	function editor_buttons_check() {
		
	}

	function editor_vars() {
		if (( in_array( basename( $_SERVER['PHP_SELF'] ), array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' ) ) ) && 
		( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) 
		) {
			// Editor buttons and JS vars.
			wp_enqueue_script('editor');
			wp_localize_script( 'editor', 'pluginVars', array(
				'wpUrl'		=>	site_url(),
				'pluginUrl'	=>	wpui_url()
			));

			if ( ! wpui_less_33( '3.1' ) ) {
				wp_enqueue_script( 'wpui-editor-dialog', wpui_url( '/js/editor_dialog.js' ), array( 'jquery-ui-dialog' ), WPUI_VER );
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
			}
		}
	}

	function wpui_editor_dialogs() {
			if ( ( isset( $this->options['enable_tinymce_menu'] ) && $this->options[ 'enable_tinymce_menu' ] == 'on' ) ||
			( isset( $this->options['enable_quicktags_buttons'] ) && $this->options[ 'enable_quicktags_buttons' ] == 'on'  ) ) {
				@include_once wpui_dir( 'inc/editor-dialogs.php' );
			}
	}
	
	
	
	/**
	 * 	Load the scripts and styles for the admin.
	 * 
	 * 	@uses wp_enqueue_style and wp_enqueue_script.
	 * 	@since 0.1
	 */
	function admin_scripts_styles() {
		global $wp_version;
		$plugin_url = plugins_url('/wp-ui/');
		
		if ( ( isset($_GET['page']) && $_GET['page'] == 'wpUI-options' )) {
				
			// Load newer jQuery for older versions. Will be removed in WP UI 1.0. 
			// if ( version_compare( $wp_version, '3.0', '<' ) ) {	

				wp_deregister_script( 'jquery-ui-tabs' );
				wp_deregister_script( 'jquery-ui-dialog' );
				
				wp_enqueue_script('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', array( 'jquery'));
			// }
			$wp_ui_main = new wpUI();
			
			// $admin_deps = ( floatval( get_bloginfo( 'version' ) ) >= 3.3 ) ? array( 'jquery-ui' ) : array( 'jquery-ui-tabs', 'jquery-ui-dialog' );
			$admin_deps = array( 'jquery-ui' );

			if ( isset( $this->options[ 'bleeding_edge' ] ) && $this->options[ 'bleeding_edge' ] == 'on' ) {
				wp_enqueue_script( 'admin-wpui-1' , $plugin_url . 'js/qtabs.js', $admin_deps, WPUI_VER );
			} else {
				wp_enqueue_script( 'admin-wpui-1' , $plugin_url . 'js/select/tabs.js', $admin_deps, WPUI_VER );
			}
			
			// wp_enqueue_script( 'admin-wpui-2' , $plugin_url . 'js/select/init.js', $admin_deps, WPUI_VER );
			wp_localize_script( 'admin-wpui-1' , 'wpUIOpts' , $wp_ui_main->get_script_options() );
			wp_enqueue_script( 'admin_wp_ui' , $plugin_url . 'js/admin.js', $admin_deps, WPUI_VER );
			wp_localize_script('admin_wp_ui' , 'initOpts', array(
				'wpUrl'				=>	site_url(),
				'pluginUrl' 		=>	plugins_url('/wp-ui/'),
				// 'queryVars1'	=>	add_query_arg( array(
				// 	 	'action' => 'WPUIstyles',
				// 	 	'height' => '200',
				// 	 	'width' => '300'
				// 	 ), 'admin-ajax.php' ),
				// 	
				// 'queryVars2'	=>	add_query_arg( array(
				// 	 	'action' => 'jqui_custom_css',
				// 	 ), 'admin-ajax.php' )
				));
			
		wp_enqueue_script( 'admin_jq_ui' , $plugin_url . 'js/jqui-admin.js', $admin_deps, WPUI_VER);
		wp_localize_script( 'admin_jq_ui' , 'jqui_admin', array(
			'upNonce'	=>	wp_create_nonce( 'wpui-jqui-custom-themes' )
		));


		/**
		 * Options page only ColorBox.
		 */
		wp_deregister_script( 'colorbox' );
		wp_enqueue_script( 'colorbox', $plugin_url . 'js/jquery.colorbox-min.js', array( 'jquery' ) );
	
		} // end the $_GET page conditional.




		
	} // END method admin_scripts_styles


	function admin_styles() {
		$plugin_url = plugins_url('/wp-ui/');
		
		// Load the css on options page.
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wpUI-options' ) {
			wp_enqueue_style('wp-tabs-admin-js', $plugin_url . 'css/admin.css');
			// wp_enqueue_style('wp-admin-jqui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/smoothness/jquery.ui.all.css');
		}		
	}
	
	
	
	public function validate_options( $input ) {
		$new_input = $input;
		$db_options = get_option( 'wpUI_options' );
		$reset = ( ! empty( $input['reset'] )) ? true : false;
		if ( $reset ) {
			$defaults = get_wpui_default_options();
			return $defaults;
		}
		
		$reset_tmpls = preg_grep_keys( '/^reset_post_template_[3-9]{1,2}$/', $input );
		if( $reset_tmpls ) {
			foreach( $reset_tmpls as $tmpls=>$data ) {
				$template_num = str_ireplace( 'reset_', '', $tmpls );
				unset( $new_input[ $template_num ] );
			}
		}
		
		if ( ! empty( $input[ 'script_conditionals' ] ) ) {
			$cond = $input[ 'script_conditionals' ];
			if (preg_match('/(x=x\s--|1=1|Or\s?1=1\s--|$_GET|SELECT|DROP\sTABLE|base64)/im', $cond ))
				wp_die( 'UN SAFE code detected in the conditionals.' );
		}

		foreach( $this->fields as $option=>$opt ) {
			if ( $opt[ 'type' ] == 'checkbox' && ! isset($input[ $opt[ 'id' ] ]) ) {
				$new_input[ $opt[ 'id' ] ] = 'off';
			} 			
		}
		
		$new_input[ 'version' ] = WPUI_VER;
		
/*		die();*/


/*		parent::validate_options( $input );*/

		return $new_input;
	}

} // end class wpUI_options

}

/**
 * Out the options page!
 */
$wpui_option_page = new wpUI_options();



$wpui_admin_help_tabs = array(
	
	'main'	=>	array(
		'id'		=>	'wpui_general',
		'title'		=>	'General',
		'content'	=>	"<h3>" . __('WP UI - General options', 'wp-ui') . "</h3><p>Enable/disable the plugin components.  </p><h4><strong>Tabs</strong></h4>
		<p>Uncheck the box to disable tabs. <em>Default is enabled</em>. Tabs are used to present content in separate parallel views ( panels ), that can be clicked open to reveal content</p><p><strong>Accordion</strong></p><p>Uncheck the box to disable accordions. <em>Default is enabled</em>. Accordions are vertically stacked list of items each of which can be clicked to expand the content associated with that item.</p>
		<p><strong>Collapsibles/Spoilers</strong></p>
		<p>Spoilers are widgets that are used to hide content at load, that can later be clicked open. In short - Click to reveal content.</p>
		<p><strong>Dialogs</strong></p>
		<p>Dialogs are inline modal windows that display content in a box.</p>
		<p><strong>Pagination support</strong></p>
		<p>Pagination is used with the wpui_loop shortcode with the argument &lt;code&gt;[wpui_loop num_per_page=&quot;4&quot; cat=&quot;10&quot; number=&quot;20&quot;]&lt;/code&gt;, will display 20 posts in 5 pages. It is powered with Javascript. </p>
		<p><strong>Editor Buttons</strong></p>
		<p>Wordpress post editor buttons makes it easy to insert widgets into posts. Buttons are available for both Visual and HTML(recommended) mode editors. </p>
		<p>Tinymce and Quicktags buttons allows you to insert </p>
		<ol style='list-style : decimal'>
		  <li>Tabs</li>
		  <li>Accordion</li>
		  <li>Spoilers</li>
		  <li>Dialogs</li>
		  <li>Posts!</li>
		  <li>And Help </li>
		</ol>
		<p>&nbsp;</p>
		<p><strong>Navigation</strong></p>
		<p>The tabs only navigation buttons, enables us to move through tabs sequentially without actually clicking one. Default : Bottom navigation buttons are enabled.</p>
		<p><strong>Tour</strong></p>
		<p>View informative detailed tour on using editor buttons, by enabling this button. </p>
		"
	),
	'style'	=>	array(
		'id'		=>	'wpui_styles',
		'title'		=>	'Styles',
		'content'	=>	"<h3>WP UI - Style options</h3>
		<h4>Load Multiple styles</h4>
		<p>Enable to load multiple styles, so they can be used in the same page. Click open &quot;Select multiple styles&quot; button to select the styles you want to include. </p>
		<p><strong>Default style</strong></p>
		<p>Select a default style for the widgets.</p>
		<p>Using the default style for the tabs/accordion. For e.g.</p>
		<pre>[wptabs] ..content.. [/wptabs]</pre>
		<p>To use a different styled tabs on the same page, example:
		<pre>[wptabs style='wpui-dark'] ..Content..[/wptabs]</pre>
		<p><strong>jQuery UI Custom themes</strong></p>
		<p>Load your own custom theme. <a href=\"http://kav.in/wp-ui-using-jquery-ui-custom-themes\">Follow this guide</a> for instructions on doing so.</p>
		<p><strong>Custom CSS </strong></p>
		<p>Need to modify some style rules, like fonts or color? Enter your custom CSS here. For some rules, particularly the jQuery ui themes, you might need to use &lt;code&gt;!important&lt;/code&gt;.</p>
		<p><strong>Dialog Width</strong></p>
		<p>Normal width of the dialog box.</p>
		<blockquote>&nbsp;</blockquote> 
		<h4>IE gradients</h4> <p>Choose whether to enable Internet Explorer gradients support, using microsoft<code> filter: </code>. A seperate stylesheet is additionally served for IE.</p>"
	),
	'Effects'	=>	array(
		'id'		=>	'wpui_effects',
		'title'		=>	'Effects',
		'content'	=>	"<h3>WP UI - Effects options</h3><h4>Effects</h4><p>Two effects are available for now, slide and fade. Choose the default effect here.</p><p><strong>Effects speed</strong></p>
		<p>Effects speed is a value in microseconds. For a swift animation, limit the value within 1000ms. </p>
		<h4>Tabs auto rotation</h4><p>Tabs can be set to  automatically rotate at specified intervals by passing the <code>rotate</code> attribute on the tabs wrapping shortcode. For eg.</p>	<pre>[wptabs rotate=&quot;6000&quot;] ..content.. [/wptabs]<br />[wptabs rotate=&quot;10s&quot;] ..content.. [/wptabs]</pre>
		<p><strong>Tabs event</strong></p>
		<p>Default is click, choose mouseover to open tabs on hover.</p>
		<p><strong>Collapsible tabs</strong></p>
		<p>Choose this option to enable all panels to be closed on click( collapsed ). </p>
		<p><strong>Accordion Event</strong></p>
		<p>Choose how you want to open an accordion panel.</p>
		<p><strong>Accordion autoheight</strong></p>
		<p>Sets all accordion panels to the height of tallest panel. This ensures the accordion animation as smooth. </p>
		<p><strong>Collapsible Accordions</strong></p>
		<p>Generally atleast one accordion panel needs to open. Click to be able to close all at once. </p>
		<p><strong>Tabs mousewheel navigation</strong></p>
		<p>Scroll through tabs with your mousewheel. Choose the element you want to apply the scroll handling, list or panel. </p>
		"
	),
	'Text'	=>	array(
		'id'		=>	'wpui_text',
		'title'		=>	'Text',
		'content'	=>	"<h3>WP UI - Text options</h3>
		<h4>These options are pretty much self explanatory.</h4>
		<p>Enter a different value to override the default text.</p>
		<p><br /> 
		  For tabs</p>
		<ol>  <li>Button for switching to Previous tab</li>  <li>Button for switching to Next tab</li></ol>
		<p>For spoilers.</p>
		<ol> <li>Collapsible/spoilers Show (hidden) content html.</li>
		  <li>Collapsible/spoilers Hide (shown) content html.</li></ol>"
	),
	'posts'	=>	array(
		'id'		=>	'wpui_Posts',
		'title'		=>	'Posts',
		'content'	=>	'<h3>Posts</h3><h4>Relative time</h4><p>When enabled, relative time is displayed, Example : <code>9 days ago</code>, <code>2 millenia ago</code></p><h4>Excerpt length</h4><p>By default, excerpt of the post is displayed, that is what is upto the more tag. Tweak this to display more text.</p><p>Want to display the whole content? Replace the{$excerpt}with{$content}in the first textbox.</p><h4>Template for the posts and accordion</h4><p>The html structure here is used for the posts that are displayed within tabs and accordions.</p><h4>Templates for the sliders and dialogs</h4><p>The structure here is the template structure for the post displayed within Dialogs and sliders.</p><h4>Additional templates</h4><p>You can add additional templates with &quot;Add New templates&quot;button, and choose this template with the shortcode argument &quot;template&quot;.</p><pre>[wptabposts template="3"cat="1"number="8"]</pre><h4>Template tags - Reference</h4><p>Following table illustrated available template tags. Put them some where in your template, they will be replaced with values.</p><table class="widefat"width="492"border="0"cellpadding="2"cellspacing="0"><thead><tr><th valign="top"width="123">Variable</th><th valign="top"width="367">Explanation</th></tr></thead><tbody><tr><td valign="top"width="123">{$title}</td><td valign="top"width="367">Title of the post/page.</td></tr><tr><td valign="top"width="123">{$date}</td><td valign="top"width="367">Time and date of the post. Also available as relative time.</td></tr><tr><td valign="top"width="123">{$author}</td><td valign="top"width="367">Post author</td></tr><tr><td valign="top"width="123">{$thumbnail}</td><td valign="top"width="367">Post\'s featured image ( thumbnail )</td></tr><tr><td valign="top"width="123">{$excerpt}</td><td valign="top"width="367">Excerpt of the post.</td></tr><tr><td valign="top"width="123">{$content}</td><td valign="top"width="367">Full content of the post.</td></tr><tr><td valign="top"width="123">{$url}</td><td valign="top"width="367">Permalink to the post.</td></tr><tr><td valign="top"width="123">{$author_post_link}</td><td valign="top"width="367">More posts by author –Link.</td></tr><tr><td valign="top"width="123">$cats</td><td valign="top"width="367">The categories of the posts</td></tr><tr><td valign="top"width="123">$cat</td><td valign="top"width="367">Displays the first category.</td></tr><tr><td valign="top"width="123">$tags</td><td valign="top"width="367">Post\'s tags.</td></tr><tr><td valign="top"width="123">$num_comments</td><td valign="top"width="367">Returns the total number of comments.</td></tr></tbody></table>'
	),
	'Advanced'	=>	array(
		'id'		=>	'wpui_advanced',
		'title'		=>	'Advanced',
		'content'	=>	'<h3>Advanced options</h3><p style="font-weight: bold; font-style : italic; text-align : center;"><strong>It\'s better to skip these options if you\'re new to wordpress or not sure of.</strong> </p><h4>Custom CSS</h4> Use this tab to output additional CSS. For example, this might be for a simple layout fix, or maybe your own skin. <h4>Alternative Shortcodes</h4> When enabled,  it is possible to use shorter codes , e.g <div><ul>	<li>[<strong>tabs</strong>] instead of [wptabs]</li><li>[<strong>tabname</strong>] instead of [wptabtitle]</li><li>[<strong>tabcont</strong>] instead of [wptabcontent]</li><li>[<strong>wslider</strong>] instead of [wpspoiler]</li></ul></div><h4><span style="color: #F33;">Disable jQuery loading</span></h4><div>Please be careful about this option. When checked, jquery will not be loaded by wp-ui. Thereby widgets will not be rendered, when globally jQuery/UI is not available.</div> <h4 style="color: #F03">Conditional script loading</h4> 
		<p>Use the <a href="http://codex.wordpress.org/Conditional_Tags">conditional statements</a> to limit/prevent wp-ui loading on pages. For example :  <code>!is_home()</code> prevents wp-ui from loading scripts and styles on the index page. Like wise, <code>is_page(\'About\')</code> only loads the scripts and styles on the Page with the name About. </p> <h4 style="color: #F33">Demand load scripts!</h4>
		<p>This is a new experimental option. When this is enabled, the necessity of each components are assessed from the current page and loaded only as necessary. To prevent unwanted requests and load on the server, the selected combination are stored and reused. </p>
		<h4 style="color: #F33">Cookies!</h4>
		Cookies are used to store information about the browser state. In our case jQuery UI tabs are able to remember the selected tabs across page reloads and re-visit.<h4 style="color: #F33">Linking and history</h4>
		<p>With this option enabled, you can link to the tabs and have them activated on click without reload. History support, i.e. users can click the back button to re open the previous tabs.</p>
		<p style="color: #F33"><strong>Blank Page fix</strong></p>
		<p>Enable this to fix the blank page issue that occurs with some external scripts, such as twitter profile and some ecommerce widgets. Almost all these scripts use <code>document.write</code> , that is the cause of this issue.</p>
		<style type="text/css"> pre, code { background: #F4F2F4 !important; padding: 2px 5px; border-radius : 3px; box-shadow : 1px 1px 0 #FFF inset, -1px -1px 0 #FFF inset, 0 1px 0 #999; border : 1px solid #DDD; text-shadow : 0 1px 0 #FFF; } </style>'
	)
	
	
);


$wpui_option_page->set_help_tabs( $wpui_admin_help_tabs );

// Insert content into the options page.
add_action( 'wpUI_above_options_page', 'wpui_plugin_info_above' );
add_action( 'wpUI_below_options_page', 'wpui_plugin_info_below' );


function wpui_plugin_info_above() {
	?>
	<div class="click-for-help"></div>
	<div class="info-above">
	<noscript>
		<p style="background: pink; border:1px solid darkred; padding: 10px; color: #600"> <?php _e( 'Please enable the javascript in your browser.', 'wp-ui' ) ?></p>
	</noscript>
	
	<div id="wpui-cap" style="position: relative">
	<div class="cap-icon">
		<img width="50px" src="<?php echo plugins_url( '/wp-ui/images/cap-badge.png' ) ?>" />

	</div><!-- end div.cap-icon -->
	
	<div class="wpui-desc">
		<p> <?php _e( 'Help -', 'wp-ui' ); ?> <a class="wpui_forums_link" href="http://kav.in/forum">Support forum</a> |<a class="wpui_options_help" href="#">Options Help ( 3.3 + )</a> | <a target="_blank" href="http://kav.in/projects/blog/tag/wp-ui/"><?php _e( 'Plugin documentation, demo @ projects', 'wp-ui' ); ?></a>. </p>
		
		<p> <?php _e( 'Help improve this plugin : ', 'wp-ui' ); ?><a target="_blank" href="http://kav.in/forum" title="Improve the plugin by sharing your opinion"><?php _e( 'Suggestions? Ideas?', 'wp-ui' ); ?></a> | <?php _e( 'Report -', 'wp-ui' ); ?> <a target="_blank" title="Report the issues you find and improve the plugin!" href="http://kav.in/forum"><?php _e( 'Bugs / Issues / conflicts</a> on Support forums', 'wp-ui' ); ?></p>
				<p> <?php _e( 'Support this plugin :', 'wp-ui' ); ?> <a href="http://www.facebook.com/pages/Capability/136970409692187" title="Facebook likes!" target="_blank"><?php _e( 'Like us on Facebook', 'wp-ui' ); ?></a> | <a title="Motivate and see us performing better!" href="http://twitter.com/kavingray" target="_blank"><?php _e( 'Follow us on Twitter', 'wp-ui' ); ?></a>. </p>
	</div><!-- end div.wpui-desc -->
	
	<!-- <div style="  position: absolute;right: 0;top: 25%;width: 63px;">	
		<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fwpuiplugin&amp;send=false&amp;layout=box_count&amp;width=450&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font=lucida+grande&amp;height=90" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:90px;" allowTransparency="true"></iframe>	
	</div> -->
	</div>
	<div id="hidden-info">
		
		
	</div>
</div><!-- end div.info-above -->
	
	<?php
}	


function wpui_plugin_info_below() {
	?>

	<div class="info-below">

	<div id="wpui-cap-below">

	<div class="actions cols cols-1">
		<h4><span></span><?php _e( 'Actions', 'wp-ui' ); ?></h4>
		<ul>
			<li>
				<a href="#" class="wpui-clean-cache"><?php _e( 'Empty cache', 'wp-ui' ) ?></a>
				<input type="hidden" value="<?php echo wp_create_nonce( 'wpui-cache-nonce' ); ?>" />
			</li>			
			<li>
				<a class="wpui-clean-meta" href="#"><?php _e( 'Clean old post meta', 'wp-ui' ) ?>			</a>
				<input type="hidden" value="<?php echo wp_create_nonce( 'wpui-clean-meta-nonce' ); ?>" />
				
			</li>
			<li>
				<a class="wpui-no-bleeding" href="#"><?php _e( 'Toggle Bleeding Edge', 'wp-ui' ) ?></a>
			</li>
		</ul>		
	</div><!--  end.actions -->


	<div class="support-plugin cols">
		<h4><span></span><?php _e( 'Support this plugin', 'wp-ui' ); ?></h4>
		<ul>
		<li>
			<a target="_blank" href="http://wordpress.org/extend/plugins/wp-ui/"><?php _e( 'Give it a nice rating at Wordpress.org', 'wp-ui' ); ?></a>
		</li>
		<li>
			<a target="_blank" href="http://wordpress.org/extend/plugins/wp-ui/" title="<?php _e( 'Please login and choose It \'works\' at wordpress.org', 'wp-ui' ); ?>"><?php _e( 'Tell others that it works!', 'wp-ui' ) ?></a>
		</li>
		
		<!-- <li>
			<i><a target="_blank" href="http://www.facebook.com/WPUIplugin" title="Show us your support by liking the plugin on Facebook.">Like the plugin on FaceBook!<span class="wpui-new-feature">NEW</span></a></i>
		</li> -->		
		<li>
			<a target="_blank" href="http://www.facebook.com/#!/pages/Capability/136970409692187" title="Encourage/Motivate me on making more plugins!"><?php _e( 'Like us on facebook!', 'wp-ui' ); ?></a>				
			</li>
		<li>
			<a href="http://twitter.com/kavingray" title="Follow Kavin on twitter."><?php _e( '@Kavingray on Twitter', 'wp-ui' ); ?></a>
		</li>

			</ul>

		</div>		
		
		<div class="help cols col-1">
			<h4><span></span><?php _e( 'Get Support!', 'wp-ui' ); ?></h4>
			<ul>
		<li>
			<a target="_blank" href="http://kav.in/projects/blog/tag/wp-ui/"><?php _e( 'Documentation', 'wp-ui' ); ?></a>
		</li>
		<li>
			<a target="_blank" href="http://kav.in/forum/categories/wp-ui-tabs-accordion"><?php _e( 'Help, Bugs and Issues', 'wp-ui' ); ?></a>
		</li>
		<li class="last-li">
			<a target="_blank" href="http://kav.in/forum/categories/suggestionsideas"><?php _e( 'Suggestions / Ideas', 'wp-ui' ); ?></a>
		</li>
		
		</ul>

	</div>

	<div class="wpui-credits cols cols-1">

		<h4><?php _e( 'Credits', 'wp-ui' ); ?></h4>


			<p><?php _e( 'Thanks to the WordPress team and jQuery (&amp;) UI team. Also thanks to the all people out there, who spend their invaluable time for the spirit of The Open Source - Sharing and helping everyone. Icons on this page -', 'wp-ui' ); ?> <a target="_blank" rel="nofollow" href="http://www.woothemes.com/2010/08/woocons1/"><?php _e( 'GPL licensed Woocons', 'wp-ui' ); ?></a>.

			</p>	

	</div>
	
	
	</div><!-- end #wpui-cap-below -->
</div><!-- end div.info-below -->

	<?php
}




/**
 * Default options and like.
 */

$wpui_default_post_template_1 = '<h2 class="wpui-post-title">{$title}</h2>
<div class="wpui-post-meta">{$date} |  {$author}</div>
<div class="wpui-post-thumbnail">{$thumbnail}</div>
<div class="wpui-post-content">{$excerpt}</div>
<p><a class="ui-button ui-widget ui-corner-all" href="{$url}" title="Read more of {$title}">Read More...</a></p>';

$wpui_default_post_template_2 = '<div class="wpui-post-meta">{$date}</div>
<div class="wpui-post-thumbnail">{$thumbnail}</div>
<div class="wpui-post-content">{$excerpt}</div>
<p><a href="{$url}" title="Read more of {$title}">Read More...</a></p>';


function get_wpui_default_options() {
	$defaults = array(
	    "enable_tabs" 				=>	"on",
	    "enable_accordion"			=>	"on",
	    "enable_pagination"			=>	"on",
	    "enable_tinymce_menu"		=>	"on",
	    "enable_quicktags_buttons"	=>	"on",
	    "enable_widgets"			=>	"on",
	    "enable_post_widget"		=>	"off",
		"topnav"					=>	"",
	    "bottomnav"					=>	"on",
		"enable_spoilers"			=>	"on",
		"enable_dialogs"			=>	"on",
		"load_all_styles"			=>	"on",
		"selected_styles"			=>		'["wpui-light","wpui-blue","wpui-red","wpui-green","wpui-dark","wpui-quark","wpui-alma","wpui-macish","wpui-redmond","wpui-sevin"]',
		"enable_ie_grad"			=>	"on",
		"dialog_width"				=>	"300px",
	    "tab_scheme" 				=>	"wpui-light",
		"jqui_custom_themes"		=>	"{}",
	    "tabsfx"					=>	"slide",
		"fx_speed"					=>	"400",
		"tabs_rotate"				=>	"stop",
		"tabs_event"				=>	"click",
		"collapsible_tabs"			=>	"off",
		"accord_event"				=>	"click",
		"accord_autoheight"			=>	"on",
		"accord_collapsible"		=>	"off",
		"accord_easing"				=>	'false',
		"mouse_wheel_tabs"			=>	'false',
		"tab_nav_prev_text"			=>	'Prev',
		"tab_nav_next_text"			=>	"Next",
		"spoiler_show_text"			=>	"Click to show",
		"spoiler_hide_text"			=>	"Click to hide",
		"relative_times"			=>	"off",
		"custom_css"				=>	"",
		"use_cookies"				=>	"on",
		"script_conditionals"		=>	"",
		"load_scripts_on_demand"	=>	"off",
		"linking_history"			=>	"on",
		"widget_rich_text"			=>	"off",
		'post_template_1'			=>	'<h2 class="wpui-post-title">{$title}</h2>
		<div class="wpui-post-meta">{$date} |  {$author}</div>
		<div class="wpui-post-thumbnail">{$thumbnail}</div>
		<div class="wpui-post-content">{$excerpt}</div>
		<p class="wpui-readmore"><a class="ui-button ui-widget ui-corner-all" href="{$url}" title="Read more from {$title}">Read More...</a></p>',
		'post_template_2'			=>	'<div class="wpui-post-meta">{$date}</div>
		<div class="wpui-post-thumbnail">{$thumbnail}</div>
		<div class="wpui-post-content">{$excerpt}</div>
		<p class="wpui-readmore"><a href="{$url}" title="Read more from {$title}">Read More...</a></p>',
		'excerpt_length'			=>	'more',
		'post_widget'				=> array (
			'title'		=>	'We recommend',
		    'type' => 'popular',
		    'number' => '4',
		    'per_row' => '4'
		),
		'post_default_thumbnail'	=>	array(
			'url'		=>	wpui_url( 'images/wp-light.png' ),
			'width'		=>	'100',
			'height'	=>	'100'
		),
		'post_widget_number'		=>	'3',
		'jquery_disabled'			=>	'off',
		'docwrite_fix'				=>	'on',
		'bleeding_edge'				=>	'off',
		'version'					=>	WPUI_VER
	);
	if ( ! wpui_less_33() ) $defaults[ 'tour' ] = 'on';
	return $defaults;
}

?>