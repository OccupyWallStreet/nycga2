<?php 

/*

  FILE STRUCTURE:

- INCLUDE CORE THEME HOOK FILES
- ALL THEME HOOK DEFINITIONS:
  * lib_theme/hooks/single.php
  * lib_theme/hooks/archive.php
  * lib_theme/hooks/search.php
  * lib_theme/hooks/page.php
  * lib_theme/hooks/page-404.php
  * lib_theme/hooks/footer.php
  * lib_theme/hooks/navigation.php
  * lib_theme/hooks/logo.php
  * lib_theme/hooks/addinfo.php
  * lib_theme/hooks/widgets.php
  * lib_theme/hooks/custom-templates.php

*/

/* INCLUDE CORE THEME HOOK FILES */
/*------------------------------------------------------------------*/

	require_once (BIZZ_THEME_HOOKS . '/navigation.php');
	require_once (BIZZ_THEME_HOOKS . '/slider.php');
	require_once (BIZZ_THEME_HOOKS . '/aslider.php');
	require_once (BIZZ_THEME_HOOKS . '/front.php');
	require_once (BIZZ_THEME_HOOKS . '/logo.php');
	require_once (BIZZ_THEME_HOOKS . '/twitter.php');
	require_once (BIZZ_THEME_HOOKS . '/addinfo.php');
	require_once (BIZZ_THEME_HOOKS . '/footer.php');
	require_once (BIZZ_THEME_HOOKS . '/custom-templates.php');
	require_once (BIZZ_THEME_HOOKS . '/single.php');	
	require_once (BIZZ_THEME_HOOKS . '/archive.php');
	require_once (BIZZ_THEME_HOOKS . '/search.php');
	require_once (BIZZ_THEME_HOOKS . '/page.php');
	require_once (BIZZ_THEME_HOOKS . '/page-404.php');
	require_once (BIZZ_THEME_HOOKS . '/widgets.php');

/* ALL THEME HOOK DEFINITIONS */
/*------------------------------------------------------------------*/
	
	// lib_theme/hooks/single.php
	function bizz_single() { do_action( 'bizz_single' ); }	
	function bizz_single_before() { do_action( 'bizz_single_before' ); }
	function bizz_headline_si_inside() { do_action( 'bizz_headline_si_inside' ); }
	function bizz_breadcrumb_si_inside() { do_action( 'bizz_breadcrumb_si_inside' ); }
	function bizz_post_meta_si_inside() { do_action( 'bizz_post_meta_si_inside' ); }
	function comments_template_si_inside() { do_action( 'comments_template_si_inside' ); }
	function bizz_single_after() { do_action( 'bizz_single_after' ); }
	
	// lib_theme/hooks/archive.php
	function bizz_archive() { do_action( 'bizz_archive' ); }	
	function bizz_archive_before() { do_action( 'bizz_archive_before' ); }
	function bizz_headline_a_inside() { do_action( 'bizz_headline_a_inside' ); }
	function bizz_breadcrumb_a_inside() { do_action( 'bizz_breadcrumb_a_inside' ); }
	function bizz_wp_pagenavi_a_top() { do_action( 'bizz_wp_pagenavi_a_top' ); }
	function bizz_wp_pagenavi_a_bottom() { do_action( 'bizz_wp_pagenavi_a_bottom' ); }
	function bizz_subheadline_a_inside() { do_action( 'bizz_subheadline_a_inside' ); }
	function bizz_post_meta_a_inside() { do_action( 'bizz_post_meta_a_inside' ); }
	function bizz_archive_after() { do_action( 'bizz_archive_after' ); }
	
	// lib_theme/hooks/search.php
	function bizz_search() { do_action( 'bizz_search' ); }
	function bizz_search_before() { do_action( 'bizz_search_before' ); }
	function bizz_headline_se_inside() { do_action( 'bizz_headline_se_inside' ); }
	function bizz_breadcrumb_se_inside() { do_action( 'bizz_breadcrumb_se_inside' ); }
	function bizz_wp_pagenavi_se_top() { do_action( 'bizz_wp_pagenavi_se_top' ); }
	function bizz_wp_pagenavi_se_bottom() { do_action( 'bizz_wp_pagenavi_se_bottom' ); }
	function bizz_subheadline_se_inside() { do_action( 'bizz_subheadline_se_inside' ); }
	function bizz_post_meta_se_inside() { do_action( 'bizz_post_meta_se_inside' ); }
	function bizz_search_after() { do_action( 'bizz_search_after' ); }	
	
	// lib_theme/hooks/page.php
	function bizz_page() { do_action( 'bizz_page' ); }	
	function bizz_page_before() { do_action( 'bizz_page_before' ); }
	function bizz_headline_p_inside() { do_action( 'bizz_headline_p_inside' ); }
	function bizz_breadcrumb_p_inside() { do_action( 'bizz_breadcrumb_p_inside' ); }
	function comments_template_p_inside() { do_action( 'comments_template_p_inside' ); }
	function bizz_page_after() { do_action( 'bizz_page_after' ); }
	
	// lib_theme/hooks/page-404.php
	function bizz_page_404() { do_action( 'bizz_page_404' ); }
	function bizz_page_404_before() { do_action( 'bizz_page_404_before' ); }
	function bizz_404_error_inside() { do_action( 'bizz_404_error_inside' ); }
	function bizz_page_404_after() { do_action( 'bizz_page_404_after' ); }
	
	// lib_theme/hooks/footer.php
	function bizz_footer() { do_action( 'bizz_footer' ); }	
	function bizz_footer_before() { do_action( 'bizz_footer_before' ); }
	function bizz_footer_branding_inside() { do_action( 'bizz_footer_branding_inside' ); }
	function bizz_footer_after() { do_action( 'bizz_footer_after' ); }
	
	// lib_theme/hooks/navigation.php
    function bizz_navigation() { do_action( 'bizz_navigation' ); }		
	function bizz_navigation_before() { do_action( 'bizz_navigation_before' ); }	
	function bizz_feed_spot_inside() { do_action( 'bizz_feed_spot_inside' ); }
	function bizz_navigation_after() { do_action( 'bizz_navigation_after' ); }	
	
	 // library/hooks/slider.php
	function bizz_slider() { do_action( 'bizz_slider' ); }
	function bizz_slider_before() { do_action( 'bizz_slider_before' ); }
	function bizz_slider_catnav() { do_action( 'bizz_slider_catnav' ); }
	function bizz_slider_after() { do_action( 'bizz_slider_after' ); }
	
	// library/hooks/aslider.php
	function bizz_aslider() { do_action( 'bizz_aslider' ); }
	function bizz_aslider_before() { do_action( 'bizz_aslider_before' ); }
	function bizz_slider_acatnav() { do_action( 'bizz_slider_acatnav' ); }
	function bizz_aslider_after() { do_action( 'bizz_aslider_after' ); }
	
	// lib_theme/hooks/front.php
	function bizz_front() { do_action( 'bizz_front' ); }	
	function bizz_front_before() { do_action( 'bizz_front_before' ); }
	function bizz_wp_pagenavi_fr_top() { do_action( 'bizz_wp_pagenavi_fr_top' ); }
	function bizz_wp_pagenavi_fr_bottom() { do_action( 'bizz_wp_pagenavi_fr_bottom' ); }
	function bizz_subheadline_fr_inside() { do_action( 'bizz_subheadline_fr_inside' ); }
	function bizz_post_meta_fr_inside() { do_action( 'bizz_post_meta_fr_inside' ); }
	function bizz_front_after() { do_action( 'bizz_front_after' ); }
	
	// lib_theme/hooks/logo.php
    function bizz_logo() { do_action( 'bizz_logo' ); }		
	function bizz_logo_before() { do_action( 'bizz_logo_before' ); }	
	function bizz_logo_inside() { do_action( 'bizz_logo_inside' ); }
	function bizz_search_form_logo_inside() { do_action( 'bizz_search_form_logo_inside' ); }
	function bizz_logo_after() { do_action( 'bizz_logo_after' ); }	

    // lib_theme/hooks/twitter.php
	function bizz_twitter() { do_action( 'bizz_twitter' ); }
	function bizz_twitter_before() { do_action( 'bizz_twitter_before' ); }
	function bizz_twitter_after() { do_action( 'bizz_twitter_after' ); }
	
	// lib_theme/hooks/addinfo.php
	function bizz_addinfo() { do_action( 'bizz_addinfo' ); }	
	function bizz_addinfo_before() { do_action( 'bizz_addinfo_before' ); }
	function bizz_addinfo_after() { do_action( 'bizz_addinfo_after' ); }
	
	// lib_theme/hooks/widgets.php
	function bizz_widgets() { do_action( 'bizz_widgets' ); }
	function bizz_widgets_before() { do_action( 'bizz_widgets_before' ); }
	function bizz_widgets_after() { do_action( 'bizz_widgets_after' ); }
	
	// lib_theme/hooks/custom-templates.php
	function bizz_no_sidebar() { do_action( 'bizz_no_sidebar' ); }	
	function bizz_no_sidebar_before() { do_action( 'bizz_no_sidebar_before' ); }	
	function bizz_headline_cn_inside() { do_action( 'bizz_headline_cn_inside' ); }
	function bizz_breadcrumb_cn_inside() { do_action( 'bizz_breadcrumb_cn_inside' ); }
	function comments_template_cn_inside() { do_action( 'comments_template_cn_inside' ); }
	function bizz_no_sidebar_after() { do_action( 'bizz_no_sidebar_after' ); }	
	
	// lib_theme/hooks/custom-templates.php
	function bizz_blog() { do_action( 'bizz_blog' ); }	
	function bizz_blog_before() { do_action( 'bizz_blog_before' ); }	
	function bizz_headline_cb_inside() { do_action( 'bizz_headline_cb_inside' ); }
	function bizz_breadcrumb_cb_inside() { do_action( 'bizz_breadcrumb_cb_inside' ); }
	function bizz_wp_pagenavi_cb_top() { do_action( 'bizz_wp_pagenavi_cb_top' ); }
	function bizz_wp_pagenavi_cb_bottom() { do_action( 'bizz_wp_pagenavi_cb_bottom' ); }
	function bizz_subheadline_cb_inside() { do_action( 'bizz_subheadline_cb_inside' ); }
	function bizz_post_meta_cb_inside() { do_action( 'bizz_post_meta_cb_inside' ); }
	function bizz_blog_after() { do_action( 'bizz_blog_after' ); }	
	
	// lib_theme/hooks/custom-templates.php
	function bizz_sitemap() { do_action( 'bizz_sitemap' ); }	
	function bizz_sitemap_before() { do_action( 'bizz_sitemap_before' ); }
	function bizz_headline_cs_inside() { do_action( 'bizz_headline_cs_inside' ); }
	function bizz_breadcrumb_cs_inside() { do_action( 'bizz_breadcrumb_cs_inside' ); }
	function comments_template_cs_inside() { do_action( 'comments_template_cs_inside' ); }
	function bizz_sitemap_after() { do_action( 'bizz_sitemap_after' ); }	
	
	// lib_theme/hooks/custom-templates.php
	function bizz_custom() { do_action( 'bizz_custom' ); }	
	function bizz_custom_before() { do_action( 'bizz_custom_before' ); }
	function bizz_headline_c_inside() { do_action( 'bizz_headline_c_inside' ); }
	function bizz_breadcrumb_c_inside() { do_action( 'bizz_breadcrumb_c_inside' ); }
	function bizz_custom_inside() { do_action( 'bizz_custom_inside' ); }
	function bizz_custom_after() { do_action( 'bizz_custom_after' ); }
	
	// lib_theme/hooks/custom-templates.php
	function bizz_portfolio() { do_action( 'bizz_portfolio' ); }	
	function bizz_portfolio_before() { do_action( 'bizz_portfolio_before' ); }
	function bizz_headline_cp_inside() { do_action( 'bizz_headline_cp_inside' ); }
	function bizz_breadcrumb_cp_inside() { do_action( 'bizz_breadcrumb_cp_inside' ); }
	function bizz_wp_pagenavi_cp_top() { do_action( 'bizz_wp_pagenavi_cp_top' ); }
	function bizz_wp_pagenavi_cp_bottom() { do_action( 'bizz_wp_pagenavi_cp_bottom' ); }
	function comments_template_cp_inside() { do_action( 'comments_template_cp_inside' ); }
	function bizz_portfolio_after() { do_action( 'bizz_portfolio_after' ); }
	
	// lib_theme/hooks/custom-templates.php
	function bizz_faqs() { do_action( 'bizz_faqs' ); }	
	function bizz_faqs_before() { do_action( 'bizz_faqs_before' ); }	
	function bizz_headline_cf_inside() { do_action( 'bizz_headline_cf_inside' ); }
	function bizz_breadcrumb_cf_inside() { do_action( 'bizz_breadcrumb_cf_inside' ); }
	function bizz_wp_pagenavi_cf_top() { do_action( 'bizz_wp_pagenavi_cf_top' ); }
	function bizz_wp_pagenavi_cf_bottom() { do_action( 'bizz_wp_pagenavi_cf_bottom' ); }
	function bizz_subheadline_cf_inside() { do_action( 'bizz_subheadline_cf_inside' ); }
	function bizz_post_meta_cf_inside() { do_action( 'bizz_post_meta_cf_inside' ); }
	function bizz_search_form_cf_inside() { do_action( 'bizz_search_form_cf_inside' ); }
	function bizz_faqs_after() { do_action( 'bizz_faqs_after' ); }	
	function bizz_faq_popular_inside() { do_action( 'bizz_faq_popular_inside' ); }	
	
?>