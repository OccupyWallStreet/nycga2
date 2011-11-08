<?php 

/* Blog Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_blog', 'bizz_blog_area' );

function bizz_blog_area() { 

add_action( 'bizz_headline_cb_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cb_inside', 'bizz_breadcrumb' );
add_action( 'bizz_wp_pagenavi_cb_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_cb_bottom', 'bizz_wp_pagenavi' );
add_action( 'bizz_subheadline_cb_inside', 'bizz_subheadline' );
add_action( 'bizz_post_meta_cb_inside', 'bizz_post_meta' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

?>

<?php if (is_paged()) $is_paged = true; ?>

<?php bizz_blog_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_cb_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_cb_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area clearfix">
<div class="container_12 containerbar">

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { bizz_widgets(); } ?>

    <div class="grid_8 mainbar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>mainbar_right<?php } ?> equalh">
	<div class="cbox">

    <?php 
	    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args=array( 'paged' => $paged );
		query_posts($args); 
	?>
	
	<?php if (function_exists('bizz_wp_pagenavi') && is_paged()) { ?>
		<div class="lpagination clearfix">
		    <?php bizz_wp_pagenavi_cb_top(); ?>
		</div>
    <?php } ?>
	
	<?php if (have_posts()) : $postcount = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
	
	    <?php if (($GLOBALS['opt']['bizzthemes_gbox_display'] == '2') && ($postcount % 2)) { $even = 'odd'; } else { $even = 'even'; } ?>
		<div class="single clearfix bsize_<?php echo stripslashes($GLOBALS['opt']['bizzthemes_gbox_display']); ?> <?php echo $even; ?>">
		    <div class="headline">
				<?php bizz_subheadline_cb_inside(); ?>
				<?php bizz_post_meta_cb_inside(); ?>
			</div><!-- /.headline -->
			<?php if ($GLOBALS['opt']['bizzthemes_thumb_show'] == 'true') {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_thumb_width'],$GLOBALS['opt']['bizzthemes_thumb_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_thumb_align']);
			} ?>
			<div class="format_text">
				<?php if ( isset($GLOBALS['opt']['bizzthemes_archive_full']) ) { ?>
				    <?php the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).'')); ?>
                <?php } else { ?>
					<?php the_excerpt(); ?>
					<?php if ( isset($GLOBALS['opt']['bizzthemes_readmore']) ) { ?>
						<span class="read-more"><a rel="nofollow" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php echo stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']); ?></a></span>
                    <?php } ?>
				<?php } ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
	
	<?php if (function_exists('bizz_wp_pagenavi')) { ?>
	    <div class="fix"><!----></div>
		<div class="lpagination fpagination clearfix">
		    <?php bizz_wp_pagenavi_cb_bottom(); ?>
		</div>
    <?php } ?>
			
	<?php wp_reset_query(); ?>
	
	</div><!-- /.cbox -->	
    </div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->	

<?php bizz_blog_after(); ?>
		
<?php }

/* FAQs Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_faqs', 'bizz_faqs_area' );

function bizz_faqs_area() { 

add_action( 'bizz_headline_cf_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cf_inside', 'bizz_breadcrumb' );
add_action( 'bizz_wp_pagenavi_cf_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_cf_bottom', 'bizz_wp_pagenavi' );
add_action( 'bizz_subheadline_cf_inside', 'bizz_subheadline' );
add_action( 'bizz_post_meta_cf_inside', 'bizz_post_meta' );
add_action( 'bizz_search_form_cf_inside', 'bizz_search_form' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );
add_action( 'bizz_faq_popular_inside', 'bizz_faqs_popular_list' );

?>

<?php if (is_paged()) $is_paged = true; ?>

<?php bizz_faqs_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_cf_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_cf_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area clearfix">
<div class="container_12 containerbar">

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { bizz_widgets(); } ?>

    <div class="grid_8 mainbar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>mainbar_right<?php } ?> equalh">
	<div class="cbox faqs">
	
	<?php if (have_posts()) : $postcount = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<div class="single clearfix">
			<div class="format_text">
				<?php the_content(); ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>

	<?php bizz_search_form_cf_inside(); ?>
	
	<?php bizz_faq_popular_inside(); ?>
	
	<ul class="faq-section">	
	    <?php bizz_faqs_list(); ?>
	</ul>
	
	</div><!-- /.cbox -->	
    </div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->	

<?php bizz_faqs_after(); ?>
		
<?php } 

/* Custom Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_custom', 'bizz_custom_area' );

function bizz_custom_area() { 

add_action( 'bizz_headline_c_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_c_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_c_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

?>

<?php bizz_custom_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_c_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_c_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area clearfix">
<div class="container_12 containerbar">

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { bizz_widgets(); } ?>

    <div class="grid_8 mainbar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>mainbar_right<?php } ?> equalh">
	<div class="cbox">
		
    <?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<div class="single clearfix">
			<div class="format_text">
				<?php the_content(); ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
						
	<div class="single clearfix">
		<?php bizz_custom_inside(); ?>
	</div><!-- /.single -->

	</div><!-- /.cbox -->	
    </div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->	

<?php bizz_custom_after(); ?>
		
<?php } 

/* No Sidebar Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_no_sidebar', 'bizz_no_sidebar_area' );

function bizz_no_sidebar_area() { 

add_action( 'bizz_headline_cn_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cn_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_cn_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

?>

<?php bizz_no_sidebar_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_cn_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_cn_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area clearfix">
<div class="container_12">

    <div class="grid_12">
	<div class="cbox">
		
    <?php if (have_posts()) : $postcount = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<div class="single clearfix">
			<?php if ( isset($GLOBALS['opt']['bizzthemes_thumb_show']) && isset($GLOBALS['opt']['bizzthemes_image_single']) ) {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_single_width'],$GLOBALS['opt']['bizzthemes_single_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_single_align']);
			} ?> 
			<div class="format_text">
				<?php the_content(); ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
						
	<?php if (comments_open() && isset($GLOBALS['opt']['bizzthemes_comments_pag']) ) : ?>
		<?php comments_template_cn_inside(); ?>
	<?php endif; ?>
	
	</div><!-- /.cbox -->	
	</div><!-- /.grid_12 -->

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->

<?php bizz_no_sidebar_after(); ?>
		
<?php } 

/* Sitemap Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_sitemap', 'bizz_sitemap_area' );

function bizz_sitemap_area() {

add_action( 'bizz_headline_cs_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cs_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_cs_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

?>

<?php bizz_sitemap_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_cs_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_cs_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area clearfix">
<div class="container_12 containerbar">

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { bizz_widgets(); } ?>

    <div class="grid_8 mainbar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>mainbar_right<?php } ?> equalh">
	<div class="cbox">

	<?php if (have_posts()) : $postcount = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
	
		<?php 
		$pages_name = (isset($GLOBALS['opt']['bizzthemes_pages_name'])) ? $GLOBALS['opt']['bizzthemes_pages_name'] : '';
		$categories_name = (isset($GLOBALS['opt']['bizzthemes_categories_name'])) ? $GLOBALS['opt']['bizzthemes_categories_name'] : '';
		?>
				
		<div class="single sing clearfix">
			<div class="format_text">
				<h3><?php echo stripslashes($pages_name); ?></h3>
                <ul><?php wp_list_pages('sort_column=menu_order&title_li=' ); ?></ul>				
                <h3><?php echo stripslashes($categories_name); ?></h3>
                <ul><?php wp_list_categories('title_li=&show_count=1') ?></ul>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
						
	<?php if (comments_open() && isset($GLOBALS['opt']['bizzthemes_comments_pag'])) : ?>
		<?php comments_template_cs_inside(); ?>
	<?php endif; ?>
	
	</div><!-- /.cbox -->	
    </div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->	

<?php bizz_sitemap_after(); ?>
		
<?php }

/* Portfolio Template */
/*------------------------------------------------------------------*/
add_action( 'bizz_portfolio', 'bizz_portfolio_area' );

function bizz_portfolio_area() { 

add_action( 'bizz_headline_cp_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cp_inside', 'bizz_breadcrumb' );
add_action( 'bizz_wp_pagenavi_cp_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_cp_bottom', 'bizz_wp_pagenavi' );
add_action( 'comments_template_cp_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

?>

<?php if (is_paged()) $is_paged = true; ?>

<?php bizz_portfolio_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_cp_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_cp_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area clearfix">
<div class="container_12">

    <div class="grid_12">
	<div class="cbox">
	
	<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		  $custom_cat = ( isset($GLOBALS['opt']['bizzthemes_portfolio_cat']) ) ? $GLOBALS['opt']['bizzthemes_portfolio_cat'] : '';
		  $args = array( 'posts_per_page'=> $GLOBALS['opt']['bizzthemes_portfolio_number'], 'ignore_sticky_posts'=> '1', 'cat' => $custom_cat, 'paged'=> $paged );
		  query_posts($args);
	?>
	
	<?php if (function_exists('bizz_wp_pagenavi') && is_paged()) { ?>
		<div class="lpagination clearfix">
		    <?php bizz_wp_pagenavi_cp_top(); ?>
		</div>
    <?php } ?>
		
	<?php if (have_posts()) : $postcount = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<?php if ($postcount % 3) { $even = 'odd'; } else { $even = 'even'; } ?>
		<div class="portfolio <?php echo $even; ?>">
			<?php bizz_get_image('image',280,150,'alignleft','90','','src'); ?>
			<div class="headline">
				<?php if ( isset($GLOBALS['opt']['bizzthemes_portfolio_title']) ) { ?>
				    <h2 class="ptitle"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
				<?php } ?>
				<p class="meta">
				<?php if ( isset($GLOBALS['opt']['bizzthemes_portfolio_date']) ) { ?>
					<span class="date"><abbr class="published" title="<?php the_time('F j, Y'); ?>"><?php the_time('F j, Y'); ?></abbr></span>
				<?php } ?>
				<?php if ( isset($GLOBALS['opt']['bizzthemes_portfolio_cats']) ) { ?>
				    <span class="tag"><?php the_category(', ') ?></span>
				<?php } ?>
				</p>
			</div><!-- /.headline -->
		</div><!-- /.portfolio -->
		
		<?php if( ( $postcount%3 ) == 0 ) { // 0, 2, 4...  ?>
			<div class="fix"><!----></div>
		<?php } ?>
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
			
	<?php if (function_exists('bizz_wp_pagenavi')) { ?>
	    <div class="fix"><!----></div>
		<div class="lpagination fpagination clearfix">
		    <?php bizz_wp_pagenavi_cp_bottom(); ?>
		</div>
    <?php } ?>
			
	<?php wp_reset_query(); ?>
				        
    </div><!-- /.cbox -->	
	</div><!-- /.grid_12 -->

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->

<?php bizz_portfolio_after(); ?>
		
<?php } ?>