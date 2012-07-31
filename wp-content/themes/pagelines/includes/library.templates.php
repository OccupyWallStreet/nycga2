<?php
/**
 * This file contains a library of common templates accessed by functions
 *
 * @package PageLines Framework
 *
 **/


/**
 * Special content wrap is for plugins that operate outside of pagelines
 * We started doing things manually, so there are legacy extensions still using manual methodology
 * 
 * @uses $pagelines_render // this is set in the main pagelines setup_pagelines_template(); function
 **/
function do_special_content_wrap(){
	global $pagelines_render;
	if(
		isset($pagelines_render)
		|| class_exists('SkinPageLinesWiki')
		|| function_exists('vanilla_dcss')
	//	|| (function_exists('is_jigoshop') && is_jigoshop() && class_exists('PageLinesJigoShop'))
	)
		return false; 
	else 
		return true;
}

function pagelines_special_content_wrap_top(){

	if(do_special_content_wrap()):
		add_action('pagelines_after_sidebar_wrap', 'pagelines_special_content_wrap_finish_after_sidebar');
		add_action('pagelines_before_sidebar_wrap', 'pagelines_special_content_wrap_finish_before_sidebar');
		add_action('pagelines_start_footer', 'pagelines_special_content_wrap_finish_after_content');
	?>	
		<section id="content" class="container fix">
				<div class="content">
					<div class="content-pad">
						<div id="pagelines_content" class="fix">
							
							<div id="column-wrap" class="fix">
								<div id="column-main" class="mcolumn fix">
									<div class="mcolumn-pad">
	<?php endif;
	
	
}

/**
 * If the extension runs the sidebar, close down some markup before
 * 
 **/
function pagelines_special_content_wrap_finish_before_sidebar(){

	?>	
								</div>
							</div>
						</div>		
	<?php 
}

/**
 * If the extension runs the sidebar, close down some markup after
 * 
 **/
function pagelines_special_content_wrap_finish_after_sidebar(){
	?>
				</div>
			</div>
		</div>
	</section>
	<?php
}
/**
 * If the sidebar wasn't run, then finish the markup
 *
 */
function pagelines_special_content_wrap_finish_after_content(){
	global $sidebar_was_run;
	
	if(!isset($sidebar_was_run)):?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
	<?php endif;
}




// ======================================
// = Sidebar Setup & Template Functions =
// ======================================

/**
 * PageLines Draw Sidebar
 *
 * Writes sidebar markup.
 * If no dynamic sidebar (widget) exists it calls the default widget
 *
 * @since   ...
 *
 * @param   $id - Sidebar ID
 * @param   $name - Sidebar name
 * @param   null $default
 * @param   string $element - CSS wrapper element, default is `ul`
 *
 * @uses    pagelines_default_widget
 */
function pagelines_draw_sidebar($id, $name, $default = null, $element = 'ul'){
	
	printf('<%s id="%s" class="sidebar_widgets fix">', $element, 'list_'.$id);
	
	if (!dynamic_sidebar($name))
		pagelines_default_widget( $id, $name, $default); 
	
	printf('</%s>', $element);

}

/**
 * PageLines Default Widget
 *
 * Calls default sidebar widget, or allows user with 'edit_themes' capability to adds widgets
 *
 * @since   ...
 *
 * @param   $id - widget area ID
 * @param   $name - name of sidebar widget area
 * @param   $default - ...
 *
 * @uses    pagelines
 * @todo Finish paramater definitions
 */
function pagelines_default_widget($id, $name, $default){
	if(isset($default) && !pagelines('sidebar_no_default')):
	
		get_template_part( $default ); 
		
	elseif( current_user_can('edit_themes') ):
	?>	

	<li class="widget widget-default setup_area no_<?php echo $id;?>">
		<div class="widget-pad">
			<h3 class="widget-title">Add Widgets (<?php echo $name;?>)</h3>
			<p class="fix">This is your <?php echo $name;?> but it needs some widgets!<br/> Easy! Just add some content to it in your <a href="<?php echo admin_url('widgets.php');?>">widgets panel</a>.	
			</p>
			<p>
				<a href="<?php echo admin_url('widgets.php');?>" class="button"><?php _e('Add Widgets &rarr;', 'pagelines');?></a>
			</p>
		
		</div>
	</li>

<?php endif;
	}

/**
 * PageLines Standard Sidebar
 *
 * Defines standard sidebar parameters
 *
 * @since   ...
 *
 * @param   string $name - Name of sidebar area
 * @param   string $description - Description of sidebar area
 *
 * @internal    @param  before_widget - markup that wraps the widget area
 * @internal    @param  after_widget - closing tags of markup added in `before_widget`
 * @internal    @param  before_title - markup that wraps the widget title text
 * @internal    @param  after_title - closing tags of markup added in `before_title`
 *
 * @return  array - all sidebar parameters
 */
function pagelines_standard_sidebar($name, $description){
	return array(
		'name'=> $name,
		'description' => $description,
	    'before_widget' => '<li id="%1$s" class="%2$s widget fix"><div class="widget-pad">',
	    'after_widget' => '</div></li>',
	    'before_title' => '<h3 class="widget-title">',
	    'after_title' => '</h3>'
	);
}


/**
 * Javascript Confirmation
 *
 * @param string $name Function name, to be used in the input
 * @param string $text The text of the confirmation
 */
function pl_action_confirm($name, $text){ 
	?>
	<script language="jscript" type="text/javascript"> function <?php echo $name;?>(){	
			var a = confirm ("<?php echo esc_js( $text );?>");
			if(a) {
				jQuery("#input-full-submit").val(1);
				return true;
			} else return false;
		}
	</script>
<?php }

/**
 * PageLines Search Form
 *
 * Writes the default "Search" text to the search form's input field.
 * Allows the $searchform to be filtered via the pagelines_search_form hook
 *
 * @since   ...
 *
 * @param   bool $echo - defaults to true, outputs $searchform
 *
 * @return  mixed|void - if $echo is false, returns $searchform
 */
function pagelines_search_form( $echo = true ){ 

	$searchfield = sprintf('<input type="text" value="" name="s" class="searchfield" placeholder="%s" />', __('Search', 'pagelines'));	
	
	$searchform = sprintf('<form method="get" class="searchform" onsubmit="this.submit();return false;" action="%s/" ><fieldset>%s</fieldset></form>', home_url(), $searchfield);
	
	if ( $echo )
		echo apply_filters('pagelines_search_form', $searchform);
	else
		return apply_filters('pagelines_search_form', $searchform);
}


/**
 * PageLines <head> Includes
 *
 */
function pagelines_head_common(){
	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);
	
	pagelines_register_hook('pagelines_code_before_head'); // Hook 

	printf('<meta http-equiv="Content-Type" content="%s; charset=%s" />',  get_bloginfo('html_type'),  get_bloginfo('charset'));

	pagelines_source_attribution();

	echo pl_source_comment('Title');

	// Draw Page <title> Tag. We use a filter to apply the actual titles.
	
	printf( '<title>%s</title>', wp_title( '',false ) );
		
	// Allow for extension deactivation of all css
	if(!has_action('override_pagelines_css_output')){	

		// Get CSS Objects & Grids
//		pagelines_load_css_relative('css/objects.css', 'pagelines-objects');
		
		// CSS Animations
//		wp_enqueue_style('animate', PL_CSS.'/animate.css'); TODO do we need this?
		
		// Multisite CSS
		if(is_multisite())
			pagelines_load_css_relative('css/multisite.css', 'pagelines-multisite');
		
		// Allow for PHP include of Framework CSS
		if( !apply_filters( 'disable_pl_framework_css', '' ) )
			pagelines_load_css(  PARENT_URL.'/style.css', 'pagelines-framework', pagelines_get_style_ver( true ));
	
		// RTL Language Support
		if(is_rtl()) 
			pagelines_load_css_relative( 'rtl.css', 'pagelines-rtl');
	}
		
	if ( ploption( 'facebook_headers' ) && ! has_action( 'disable_facebook_headers' ) && VPRO )
		pagelines_facebook_header();
		
	// Fix IE and special handling
	if ( pl_detect_ie() )
		pagelines_fix_ie();
	
	// Cufon replacement 
	pagelines_font_replacement();
	
	if(ploption('load_prettify_libs'))
		load_prettify();
	
	add_action( 'wp_head', create_function( '',  'echo pl_source_comment("Start >> Meta Tags and Inline Scripts", 2);' ), 0 );
	
	add_action( 'wp_print_styles', create_function( '',  'echo pl_source_comment("Styles");' ), 0 );
	
	add_action( 'wp_print_scripts', create_function( '',  'echo pl_source_comment("Scripts");' ), 0 );
	
	add_action( 'wp_print_footer_scripts', create_function( '',  'echo pl_source_comment("Footer Scripts");' ), 0 );
	
	add_action( 'admin_bar_menu', create_function( '',  'echo pl_source_comment("WordPress Admin Bar");' ), 0 );
	
	add_action( 'wp_head', 'pagelines_meta_tags', 9 );
	
	// Headerscripts option > custom code
	if ( ploption( 'headerscripts' ) )
		add_action( 'wp_head', create_function( '',  'print_pagelines_option("headerscripts");' ), 25 );

	if( ploption('asynch_analytics'))
		add_action( 'pagelines_head_last', create_function( '',  'echo ploption("asynch_analytics");' ), 25 );		
}

function load_prettify(){
	//add_action( 'pl_body_attributes', create_function( '',  'echo "onload="prettyprint();"' ) );
	
	wp_enqueue_script( 'prettify', PL_JS . '/prettify/prettify.js' );
	wp_enqueue_style( 'prettify', PL_JS . '/prettify/prettify.css' );
	add_action( 'wp_head', create_function( '',  'echo pl_js_wrap("prettyPrint()");' ), 14 );
	

}


function pagelines_meta_tags(){
	
	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);
	
	// Meta Images
	if(ploption('pagelines_favicon') && VPRO)
		printf('<link rel="shortcut icon" href="%s" type="image/x-icon" />%s', ploption('pagelines_favicon'), "\n");
	
	if(ploption('pagelines_touchicon'))
		printf('<link rel="apple-touch-icon" href="%s" />%s', ploption('pagelines_touchicon'), "\n");

	// Meta Data Profiles
	if(!apply_filters( 'pagelines_xfn', '' ))
		echo '<link rel="profile" href="http://gmpg.org/xfn/11" />'."\n";

	// Removes viewport scaling on Phones, Tablets, etc.
	if(!ploption('disable_mobile_view', $oset) && !apply_filters( 'disable_mobile_view', '' ))
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
		
}

function pagelines_source_attribution() {
	
	echo "\n\n<!-- "; 
	printf ( "Site Crafted Using PageLines - WordPress - HTML5 ( %s ) - www.PageLines.com ", get_pagelines_credentials( 'licence' ) );

	echo "-->\n";
}

function pl_source_comment( $text, $spacing = 1 ) {

	$newline = ($spacing) ? "\n" : '';

	$double = ($spacing == 2) ? "\n\n" : $newline;
	
	return sprintf( '%s<!-- %s -->%s', $double, $text, $newline);

}


/**
*
* @TODO do
*
*/
function pagelines_facebook_header() {

	if ( is_home() || is_archive() )
		return;

	if ( function_exists( 'is_bbpress' ) && is_bbpress() )
		return;

	global $pagelines_ID;
	
	if ( ! $pagelines_ID )
		return;

	$fb_img = apply_filters('pl_opengraph_image', pl_the_thumbnail_url( $pagelines_ID, 'full' ) );
		
	echo pl_source_comment('Facebook Open Graph');	
		
	printf( "<meta property='og:title' content='%s' />\n", get_the_title($pagelines_ID));
	printf( "<meta property='og:url' content='%s' />\n", get_permalink($pagelines_ID));
	printf( "<meta property='og:site_name' content='%s' />\n", get_bloginfo( 'name' ));
	$fb_content = get_post( $pagelines_ID );
	if ( ! function_exists( 'sharing_plugin_settings' ) )
		printf( "<meta property='og:description' content='%s' />\n", pl_short_excerpt( $fb_content, 15 ) );
	printf( "<meta property='og:type' content='%s' />", (is_home()) ? 'website' : 'article');
	if($fb_img)
		printf( "\n<meta property='og:image' content='%s' />", $fb_img);
}

/**
*
* @TODO do
*
*/
function pagelines_supersize_bg(){
	
	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);
	$url = ploption('page_background_image_url', $oset);

	if(ploption('supersize_bg') && $url && !pl_is_disabled('color_control')){ 
	
		wp_enqueue_script('pagelines-supersize' );			
		add_action('wp_head', 'pagelines_runtime_supersize', 20);
	}	
}

/**
*
* @TODO do
*
*/
function pagelines_runtime_supersize(){

	if ( has_action( 'pl_no_supersize' ) )
    return;
	
	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);
	$url = ploption('page_background_image_url', $oset);
	?>
	
	<script type="text/javascript"> /* <![CDATA[ */
	jQuery(document).ready(function(){
		jQuery.supersized({ slides  :  	[ { image : '<?php echo $url; ?>' } ] });
	});/* ]]> */
	</script>
	
<?php
}


	
/**
 * PageLines Title Tag ( deprecated )
 *
 * Checks for AIO or WPSEO functionality, if they both do not exist then this will define the HTML <title> tag for the theme.
 *
 * @TODO deleteme
 *
 * @internal filter pagelines_meta_title provided for over-writing the default title text.
 */
function pagelines_title_tag(){
	echo "<title>";

	if ( !function_exists( 'aiosp_meta' ) && !function_exists( 'wpseo_get_value' ) ) {
	// Pagelines seo titles.
		global $page, $paged;
		$title = wp_title( '|', false, 'right' );

		// Add the blog name.
		$title .= get_bloginfo( 'name' );

		// Add the blog description for the home/front page.
		$title .= ( ( is_home() || is_front_page() ) && get_bloginfo( 'description', 'display' ) ) ? ' | ' . get_bloginfo( 'description', 'display' ) : '';

		// Add a page number if necessary:
		$title .= ( $paged >= 2 || $page >= 2 ) ? ' | ' . sprintf( __( 'Page %s', 'pagelines' ), max( $paged, $page ) ) : '';
	} else
		$title = trim( wp_title( '', false ) );
	
	// Print the title.
	echo apply_filters( 'pagelines_meta_title', $title );
	
	echo "</title>";
}

/**
 * PageLines Title Tag Filter
 *
 * Filters wp_title so SEO plugins can override.
 *
 * @since 2.2.2
 *
 * @internal filter pagelines_meta_title provided for over-writing the default title text.
 */
function pagelines_filter_wp_title( $title ) {
	global $wp_query, $s, $paged, $page;
	$sep = __( '|','pagelines' );
	$new_title = get_bloginfo( 'name' ) . ' ';
	$bloginfo_description = get_bloginfo( 'description' );
	if( is_feed() ) {
		$new_title = $title;
	} elseif ( ( is_home () || is_front_page() ) && ! empty( $bloginfo_description ) && ! $paged && ! $page ) {
		$new_title .= $sep . ' ' . $bloginfo_description;
	} elseif ( is_category() ) {
		$new_title .= $sep . ' ' . single_cat_title( '', false );
	} elseif ( is_single() || is_page() ) { 
		$new_title .= $sep . ' ' . single_post_title( '', false );
	} elseif ( is_search() ) { 
		$new_title .= $sep . ' ' . sprintf( __( 'Search Results: %s','pagelines' ), esc_html( $s ) );
	} else
		$new_title .= $sep . ' ' . $title;
	if ( $paged || $page ) {
		$new_title .= ' ' . $sep . ' ' . sprintf( __( 'Page: %s', 'pagelines' ), max( $paged, $page ) );
	}
    return apply_filters( 'pagelines_meta_title', $new_title );
}
add_filter( 'wp_title', 'pagelines_filter_wp_title' );
	
/**
 * 
 *  Fix IE to the extent possible
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_fix_ie( ){
	
	$ie_ver = pl_detect_ie();
	if( ploption('google_ie') && ( $ie_ver < 8 ) )
		echo '<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script>'."\n";
	
	if ( $ie_ver < 9 ){
		printf(
			'%2$s<script src="%1$s"></script>%2$s', 
			'http://html5shim.googlecode.com/svn/trunk/html5.js', 
			"\n"
		);
	}

	// If IE7 add the Internet Explorer 7 specific stylesheet
	if ( $ie_ver == 7 )
		wp_enqueue_style('ie7-style', PL_CSS  . '/ie7.css', array(), CORE_VERSION);
} 

/**
 * 
 *  Cufon Font Replacement
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_font_replacement( $default_font = ''){
	
	if(ploption('typekit_script')){
		echo pagelines_option('typekit_script');
	}
	
	if(ploption('fontreplacement')){
		global $cufon_font_path;
		
		if(ploption('font_file')) $cufon_font_path = ploption('font_file');
		elseif($default_font) $cufon_font_path = PL_JS.'/'.$default_font;
		else $cufon_font_path = null;
		
		// ===============================
		// = Hook JS Libraries to Footer =
		// ===============================
		add_action('wp_footer', 'font_replacement_scripts');

		/**
		*
		* @TODO document
		*
		*/
		function font_replacement_scripts(){
			
			global $cufon_font_path;

			wp_register_script('cufon', PL_ADMIN_JS.'/type.cufon.js', 'jquery', '1.09i', true);
			wp_print_scripts('cufon');
			
			if(isset($cufon_font_path)){
				wp_register_script('cufon_font', $cufon_font_path, 'cufon');
				wp_print_scripts('cufon_font');
			}
		
		}
		
		add_action('wp_head', 'cufon_inline_script');

		/**
		*
		* @TODO document
		*
		*/
		function cufon_inline_script(){
			?><script type="text/javascript"><?php 
			if(pagelines('replace_font')): 
				?>jQuery(document).ready(function () { Cufon.replace('<?php echo ploption("replace_font"); ?>', {hover: true}); });<?php 
			endif;
			?></script><?php
		 }
 	}
}

/**
 * 
 *  Pagination Function
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 2.0.b12 moved
 *
 */
function pagelines_pagination() {
	if(function_exists('wp_pagenavi') && show_posts_nav() && VPRO):
		wp_pagenavi(); 
	elseif (show_posts_nav()) : ?>
		<div class="page-nav-default fix">
			<span class="previous-entries"><?php next_posts_link(__('&larr; Previous Entries','pagelines')) ?></span>
			<span class="next-entries"><?php previous_posts_link(__('Next Entries &rarr;','pagelines')) ?></span>
		</div>
<?php endif;
}

/**
 * 
 *  Fallback for navigation, if it isn't set up
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_nav_fallback() {
	global $post; ?>
	
	<ul id="menu-nav" class="main-nav<?php echo pagelines_nav_classes();?>">
		<?php wp_list_pages( 'title_li=&sort_column=menu_order&depth=3'); ?>
	</ul><?php
}


/**
 * 
 *  Blank Nav Fallback
 *
 */
function blank_nav_fallback() {
	
	if(current_user_can('edit_themes'))
		printf( __( "<ul class='inline-list'>Please select a nav menu for this area in the <a href='%s'>WordPress menu admin</a>.</ul>", 'pagelines' ), admin_url('nav-menus.php') );
}

/**
 * 
 *  Returns child pages for subnav, setup in hierarchy
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_page_subnav(){ 
	global $post; 
	if(!is_404() && isset($post) && is_object($post) && !pagelines_option('hide_sub_header') && ($post->post_parent || wp_list_pages('title_li=&child_of='.$post->ID.'&echo=0'))):?>
	<ul class="secondnav_menu lcolor3">
		<?php 
			if(count($post->ancestors)>=2){
				$reverse_ancestors = array_reverse($post->ancestors);
				$children = wp_list_pages('title_li=&depth=1&child_of='.$reverse_ancestors[0].'&echo=0&sort_column=menu_order');	
			}elseif($post->post_parent){ $children = wp_list_pages('title_li=&depth=1&child_of='.$post->post_parent.'&echo=0&sort_column=menu_order');
			}else{	$children = wp_list_pages('title_li=&depth=1&child_of='.$post->ID.'&echo=0&sort_column=menu_order');}

			if ($children) { echo $children;}
		?>
	</ul>
	<?php endif;
}

/**
 * PageLines Main Logo
 *
 * The main site logo template
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 *
 * @since       1.1.0
 *
 * @param       null $location
 *
 * @uses        (global) $pagelines_ID
 * @uses        ploption - pagelines_custom_logo, pagelines_custom_logo_url
 * @uses        (filters) pagelines_logo_url, pagelines_site_logo, pagelines_custom_logo_url, pagelines_site_title
 *
 */
function pagelines_main_logo( $location = null ){ 
	
	global $pagelines_ID; 
	
	if ( is_pagelines_special() )
		$pagelines_ID = false;
	
	$oset = array( 'post_id' => $pagelines_ID );
	
	if(ploption('pagelines_custom_logo', $oset) || apply_filters('pagelines_site_logo', '') || apply_filters('pagelines_logo_url', '')){
		

		$logo = apply_filters('pagelines_logo_url', esc_url(ploption('pagelines_custom_logo', $oset) ), $location);


		$logo_url = ( esc_url(ploption('pagelines_custom_logo_url', $oset) ) ) ? esc_url(ploption('pagelines_custom_logo_url', $oset) ) : home_url();
		
		$site_logo = sprintf( '<a class="plbrand mainlogo-link" href="%s" title="%s"><img class="mainlogo-img" src="%s" alt="%s" /></a>', $logo_url, get_bloginfo('name'), $logo, get_bloginfo('name'));
		
		echo apply_filters('pagelines_site_logo', $site_logo, $location);
		
	} else {
		
		$site_title = sprintf( '<div class="title-container"><a class="home site-title" href="%s" title="%s">%s</a><h6 class="site-description subhead">%s</h6></div>', esc_url(home_url()), __('Home','pagelines'), get_bloginfo('name'), get_bloginfo('description'));
		
		echo apply_filters('pagelines_site_title', $site_title, $location);	
	}		
}

/**
 * 
 * Wraps in standard js on ready format
 *
 * @since 2.0.0
 */
function pl_js_wrap( $js ){
	
	return sprintf('<script type="text/javascript">/*<![CDATA[*/ jQuery(document).ready(function(){ %s }); /*]]>*/</script>', $js);
	
}

/**
 * 
 *  Adds PageLines to Admin Bar
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.3.0
 *
 */
function pagelines_settings_menu_link(  ){ 
	global $wp_admin_bar;
	
	global $pagelines_template;

	
	if ( !current_user_can('edit_theme_options') )
		return;

	$wp_admin_bar->add_menu( array( 'id' => 'pl_settings', 'title' => __('PageLines', 'pagelines'), 'href' => admin_url( PL_DASH_URL ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_dashboard', 'parent' => 'pl_settings', 'title' => __( 'Dashboard', 'pagelines' ), 'href' => admin_url( PL_DASH_URL ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_main_settings', 'parent' => 'pl_settings', 'title' => __( 'Site Options', 'pagelines' ), 'href' => admin_url( PL_SETTINGS_URL ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_special', 'parent' => 'pl_settings', 'title' => __( 'Page Options', 'pagelines' ), 'href' => admin_url( PL_SPECIAL_OPTS_URL ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_templates', 'parent' => 'pl_settings', 'title' =>  __( 'Drag &amp; Drop', 'pagelines' ), 'href' => admin_url( PL_TEMPLATE_SETUP_URL ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_extend', 'parent' => 'pl_settings', 'title' => __('Store', 'pagelines'), 'href' => admin_url( PL_ADMIN_STORE_URL ) ) );

	$template_name = (isset($pagelines_template->template_name)) ? $pagelines_template->template_name : false;

	if( $template_name ){
		$page_type = __('Template: ', 'pagelines') . ucfirst($template_name );
		$wp_admin_bar->add_menu( array( 'id' => 'template_type', 'title' => $page_type, 'href' => admin_url( PL_TEMPLATE_SETUP_URL ) ) );
	}
	
	$spurl = pl_special_url( $template_name );
	
	if( $template_name && is_pagelines_special() && $spurl){
		$wp_admin_bar->add_menu( array( 'id' => 'special_settings', 'title' => __('Edit Meta', 'pagelines'), 'href' => $spurl ) );
	}
}

/**
*
* @TODO do
*
*/
function pl_special_url( $t ){
	
	$t = strtolower( trim($t) );
	
	if($t == 'blog')
		$slug = 'blog_page';
	elseif($t == 'category')
		$slug = 'category_page';
	elseif($t == 'archive')
		$slug = 'archive_page';
	elseif($t == 'search')
		$slug = 'search_results';
	elseif($t == 'tag')
		$slug = 'tag_listing';
	elseif($t == '404_error')
		$slug = '404_page';
	elseif($t == 'author')
		$slug = 'author_posts';
	else 
		return false;

	$rurl = sprintf(PL_SPECIAL_OPTS_URL.'%s', '#'.$slug);

	return admin_url( $rurl );

}

/**
 * 
 *  PageLines Attribution
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_cred(){ 
	
	if( !VPRO || (VPRO && !ploption('watermark_hide')) || has_action('show_pagelines_leaf') ) {
						
		$image = (VPRO && ploption('watermark_image')) ? ploption('watermark_image') : PL_IMAGES.'/pagelines.png';
		
		$alt = (VPRO) ? ploption( 'watermark_alt' ) : 'Build a website with PageLines';
		
		$url = (VPRO && ploption('watermark_link')) ? ploption('watermark_link') : 'http://www.pagelines.com';
			
		$img 	= sprintf('<img src="%s" alt="%s" />', $image, $alt);
		
		$link = (!apply_filters('no_leaf_link', '')) ? sprintf('<a class="plimage" target="_blank" href="%s" title="%s">%s</a>', $url, $alt, $img ) : $img;
		
		$cred = sprintf('<div id="cred" class="pagelines" style="display: block; visibility: visible;">%s</div><div class="clear"></div>', $link);
	
		echo apply_filters('pagelines_leaf', $cred);
		
	}

}

/**
*
* @TODO do
*
*/
function pagelines_get_childcss() {
	if ( ! is_admin() && is_child_theme() )
		pagelines_load_css(  get_bloginfo('stylesheet_url'), 'pagelines-child-stylesheet', pagelines_get_style_ver());
}
