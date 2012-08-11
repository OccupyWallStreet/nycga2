<?php 

add_action( 'bizz_single', 'bizz_single_area' );

add_action( 'bizz_headline_si_inside', 'bizz_headline' );
add_action( 'bizz_post_meta_si_inside', 'bizz_post_meta' );
add_action( 'bizz_breadcrumb_si_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_si_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_single_area() { 

?>

<?php bizz_single_before(); ?>

<div class="title-area clearfix">
<div class="container_12">
<div class="grid_12">
    <?php bizz_headline_si_inside(); ?>
</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.title-area -->

<?php if ( isset($GLOBALS['opt']['bizzthemes_breadcrumbs']) ) { ?>
<div class="breadcrumbs-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_breadcrumb_si_inside(); ?>
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
				
		<div class="single sing">
		    <div class="headline">
				<?php bizz_post_meta_si_inside(); ?>
			</div><!-- /.headline -->
			<?php if ( isset($GLOBALS['opt']['bizzthemes_thumb_show']) && isset($GLOBALS['opt']['bizzthemes_image_single']) ) {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_single_width'],$GLOBALS['opt']['bizzthemes_single_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_single_align']);
			} ?>
			<div class="format_text">
				<?php the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).'')); ?>
				<p class="meta">
				    <?php seo_post_tags(); ?>
					<?php seo_post_cats(); ?>
				</p>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
						
	<?php comments_template_si_inside(); ?>
		
	</div><!-- /.cbox -->	
	</div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->

<?php bizz_single_after(); ?>
		
<?php } ?>