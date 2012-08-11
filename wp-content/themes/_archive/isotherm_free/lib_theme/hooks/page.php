<?php 

add_action( 'bizz_page', 'bizz_page_area' );

add_action( 'bizz_headline_p_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_p_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_p_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_page_area() { 

?>

<?php bizz_page_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_p_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_p_inside(); ?>
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
						
	<?php if (comments_open() && isset($GLOBALS['opt']['bizzthemes_comments_pag']) ) : ?>
		<?php comments_template_p_inside(); ?>
	<?php endif; ?>
	
	</div><!-- /.cbox -->	
	</div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->

<?php bizz_page_after(); ?>
		
<?php } ?>