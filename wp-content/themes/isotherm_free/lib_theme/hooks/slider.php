<?php 

add_action( 'bizz_slider_catnav', 'bizz_catnav_area' );

add_action( 'bizz_slider', 'bizz_slider_area' );

function bizz_slider_area() {

?>

<?php 
    // Which post is last in array and which before last one	
	    $counting = 0;
		global $post;
		$myposts = get_posts('numberposts=-1');
		foreach ( $myposts as $posts ) { $counting++; }
	    $last_item = $counting;
?>

<?php bizz_slider_before(); ?> 

<div class="slider-area clearfix">
<div class="container_12">
<div class="grid_12">
	<?php bizz_slider_catnav(); ?>
	<?php 
	    $sticky=get_option('sticky_posts');
	    $args=array( 'posts_per_page' => 6 );
		query_posts($args); 
	?>
	<?php if (have_posts()) : $count = 0; ?>
	<div class="top-featured">
	<?php while (have_posts()) : the_post(); $count++; ?>
	<?php 
	if ($count == 1) {
	    $fitem = 'box1'; 
		$fwidth = '250';
		$fheight = '302';
	} elseif ($count <= 5 && $count > 1) {
	    $fitem = 'box2345';
		$fwidth = '250';
		$fheight = '150';
	} elseif ($count == 6) {
	    $fitem = 'box6';
		$fwidth = '172';
		$fheight = '302';
	}
	if ($count <= 6) {
	?>
		    <div class="<?php echo $fitem; ?> feat_item">
			    <?php bizz_image('key=image&width='.$fwidth.'&height='.$fheight.'&class=thumbnail fl'); ?>
				<?php global $post;
				$featured_exists = bizz_image('key=image&width='.$fwidth.'&height='.$fheight.'&return=true');
				if ( !isset($featured_exists) && empty($featured_exists ) ) { ?>
				    <a title="<?php the_title(); ?>" href="<?php the_permalink() ?>">
					    <img src="<?php echo bloginfo('template_url'); ?>/lib_frame/thumb.php?src=<?php echo bloginfo('template_url'); ?>/lib_theme/images/post-bg.png&amp;h=<?php echo $fheight; ?>&amp;w=<?php echo $fwidth; ?>&amp;zc=1&amp;q=90" alt="<?php the_title(); ?>" class="thumbnail fl" />
					</a>
				<?php } ?>
				<?php if ( isset($GLOBALS['opt']['bizzthemes_slmeta_cat']) ) { ?>   				
                <span class="cat">
				    <?php 
					$category = get_the_category();
					$num_cats = count($category);
					$cat_count = 1;
					foreach ($category as $cat) {
						echo '<a title="'.$cat->cat_name.'" href="'.get_category_link( $cat->cat_ID ).'" class="cat">'.$cat->cat_name.'</a>';
						if ($cat_count < $num_cats)
							echo ', ';
						$cat_count++;
					}
					?>
				</span> 
				<?php } ?> 
				<?php if ( isset($GLOBALS['opt']['bizzthemes_slmeta_tit']) ) { ?>
				<h2 style="width:<?php echo ($fwidth-17); ?>px"><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<?php } ?>
			</div><!-- /.feat_item -->
	<?php } ?>
	<?php endwhile; ?>
	<div class="fix"><!----></div>
	</div><!-- /.top-featured -->	
	<?php endif; ?>	
	<?php wp_reset_query(); ?>

</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.slider-area -->
<?php bizz_slider_after(); ?>
<?php }

function bizz_catnav_area() {

if ( get_inc_categories("cat_exclude_") != '' ) {

?>
    <div class="cat-menu clearfix">
	<ul class="sf-menu">
	    <?php if (get_inc_categories("cat_exclude_") == '') { $exclude = '9999999'; } else { $exclude = get_inc_categories("cat_exclude_"); } ?>
		<?php wp_list_categories('title_li=&depth=0&include=' . $exclude .'&sort_column=menu_order'); ?>
		<?php if (isset($last_item) && $last_item > 6) { ?>
		    <li class="floading"><div class="floading-image"><!----></div></li>
		<?php } ?>
	</ul><!-- /.sf-menu -->
	</div><!-- /.cat-menu -->
<?php
}

}

?>