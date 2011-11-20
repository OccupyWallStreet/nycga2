<?php 

add_action( 'bizz_front', 'bizz_front_area' );

add_action( 'bizz_wp_pagenavi_fr_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_fr_bottom', 'bizz_wp_pagenavi' );
add_action( 'bizz_subheadline_fr_inside', 'bizz_subheadline' );
add_action( 'bizz_post_meta_fr_inside', 'bizz_post_meta' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_front_area() { 

?>

<?php $is_paged = (is_paged()) ? true : false; ?>

<?php bizz_front_before(); ?>

<div class="cbox-area clearfix">
<div class="container_12 containerbar">

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { bizz_widgets(); } ?>

    <div class="grid_8 mainbar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>mainbar_right<?php } ?> equalh">
	<div class="cbox">

    <?php 
	if ( $GLOBALS['opt']['bizzthemes_front_number'] == '0' ) { $inumber = '-1'; } else { $inumber = $GLOBALS['opt']['bizzthemes_front_number']; }
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$sticky_count = count(get_option('sticky_posts')); 
	$args=array( 'posts_per_page' => $inumber+6, 'paged' => $paged );
	query_posts($args); 
	?>
	
	<?php if (function_exists('bizz_wp_pagenavi') && is_paged()) { ?>
		<div class="lpagination clearfix">
		    <?php bizz_wp_pagenavi_fr_top(); ?>
		</div>
    <?php } ?>
	
	<?php if (have_posts()) : $count = 0; $postcount = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++; ?>
	
	<!-- List of Remaining Entries: START -->
	<?php if ( isset($GLOBALS['opt']['bizzthemes_index_s_slider']) ) { ?>
		<?php if ( $postcount <= 6 && !$is_paged ) { continue; }  if ( $postcount >= 7 && !$is_paged ) ?>
	<?php } else { ?>
		<?php if ( $postcount == 0 && !$is_paged ) { continue; }  if ( $postcount >= 1 && !$is_paged ) ?>
	<?php } { ?>
		
	    <?php if (($GLOBALS['opt']['bizzthemes_box_display'] == '2') && ($postcount % 2)) { $even = 'odd'; } else { $even = 'even'; } ?>
		<div class="single clearfix bsize_<?php echo stripslashes($GLOBALS['opt']['bizzthemes_box_display']); ?> <?php echo $even; ?>">
		    <div class="headline">
				<?php bizz_subheadline_fr_inside(); ?>
				<?php bizz_post_meta_fr_inside(); ?>
			</div><!-- /.headline -->
			<?php if ( isset($GLOBALS['opt']['bizzthemes_thumb_show']) ) {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_fi_width'],$GLOBALS['opt']['bizzthemes_fi_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_fi_align']);
			} ?>
			<div class="format_text">
				<?php if ( isset($GLOBALS['opt']['bizzthemes_front_full']) ) { ?>
				    <?php the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).'')); ?>
                <?php } else { ?>
					<?php the_excerpt(); ?>
					<?php if ( isset($GLOBALS['opt']['bizzthemes_freadmore']) ) { ?>
						<span class="read-more"><a rel="nofollow" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php echo stripslashes($GLOBALS['opt']['bizzthemes_freadmore_text']); ?></a></span>
                    <?php } ?>
				<?php } ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
		
		<?php if ($GLOBALS['opt']['bizzthemes_box_display'] == '2') { 
		    $count++; if ($count == 2) { $count = 0; 
		?>
		    <div class="single-sep clearfix"><!----></div>
		<?php } } elseif ($GLOBALS['opt']['bizzthemes_box_display'] == '1') { 
		    $count++; if ($count == 1) { $count = 0; 
		?>
		    <div class="single-sep clearfix"><!----></div>
		<?php } } ?>
		
	<?php continue; } ?>
	<!-- List of Remaining Entries: END -->
	<!-- Rest of Entries: START -->
	
	    <?php if (($GLOBALS['opt']['bizzthemes_box_display'] == '2') && ($postcount % 2)) { $even = 'odd'; } else { $even = 'even'; } ?>
		<div class="single bsize_<?php echo stripslashes($GLOBALS['opt']['bizzthemes_box_display']); ?> <?php echo $even; ?>">
		    <div class="headline">
				<?php bizz_subheadline_fr_inside(); ?>
				<?php bizz_post_meta_fr_inside(); ?>
			</div><!-- /.headline -->
			<?php if ($GLOBALS['opt']['bizzthemes_thumb_show'] == 'true') {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_fi_width'],$GLOBALS['opt']['bizzthemes_fi_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_fi_align']);
			} ?>
			<div class="format_text">
				<?php if ( $GLOBALS['opt']['bizzthemes_front_full'] == 'true' ) { ?>
				    <?php the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).'')); ?>
                <?php } else { ?>
					<?php the_excerpt(); ?>
					<?php if ( $GLOBALS['opt']['bizzthemes_freadmore'] == 'true' ) { ?>
						<span class="read-more"><a rel="nofollow" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php echo stripslashes($GLOBALS['opt']['bizzthemes_freadmore_text']); ?></a></span>
                    <?php } ?>
				<?php } ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
		
		<?php if ($GLOBALS['opt']['bizzthemes_box_display'] == '2') { 
		    $count++; if ($count == 2) { $count = 0; 
		?>
		    <div class="single-sep clearfix"><!----></div>
		<?php } } elseif ($GLOBALS['opt']['bizzthemes_box_display'] == '1') { 
		    $count++; if ($count == 1) { $count = 0; 
		?>
		    <div class="single-sep clearfix"><!----></div>
		<?php } } ?>
		
	<!-- Rest of Entries: END -->
	
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
	
	<?php if (function_exists('bizz_wp_pagenavi')) { ?>
	    <div class="fix"><!----></div>
		<div class="lpagination fpagination clearfix">
		    <?php bizz_wp_pagenavi_fr_bottom(); ?>
		</div>
    <?php } ?>
			
	<?php wp_reset_query(); ?>

	</div><!-- /.cbox -->	
	</div><!-- /.grid_8 -->

    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_widgets(); } ?>

</div><!-- /.container_12 -->
</div><!-- /.cbox-area -->	

<?php bizz_front_after(); ?>

<?php } ?>