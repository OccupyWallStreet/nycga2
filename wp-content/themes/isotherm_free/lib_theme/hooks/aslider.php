<?php 

add_action( 'bizz_slider_acatnav', 'bizz_acatnav_area' );

add_action( 'bizz_aslider', 'bizz_aslider_area' );

function bizz_aslider_area() {

?>

<?php 
global $wpdb;
if (is_tag()) {
    $tagname = get_query_var('tag');
	$tagid = $wpdb->get_row("SELECT * FROM $wpdb->terms WHERE slug = '$tagname'");
	$tag_id = $tagid->term_id;
	$numtags = $wpdb->get_row("SELECT * FROM $wpdb->term_taxonomy WHERE taxonomy = 'post_tag' AND term_id = '$tag_id'"); 
	$numcats = $numtags->count;
} elseif (is_category()) {
    $id = get_query_var('cat'); 
    $numcats = $wpdb->get_var("SELECT count FROM $wpdb->term_taxonomy WHERE (term_id = $id) "); 
} else {
	$numcats = '';
}

if ( is_tag() || is_category() ) {
?>

<?php bizz_aslider_before(); ?> 

<div class="slider-area clearfix">
<div class="container_12">
<div class="grid_12">

    <?php bizz_slider_acatnav(); ?>
	
	<?php if ($numcats > 6 && !is_paged()) { ?>
	
	<?php if (have_posts()) : $count = 0; ?>
	<div class="top-featured">
	<?php while (have_posts()) : the_post(); $count++; ?>
	<?php 
	if ($count <= 1) { 
	    $fitem = 'box1'; 
		$fwidth = '250';
		$fheight = '302';
	} elseif ($count <= 5 && $count > 1) {
	    $fitem = 'box2345';
		$fwidth = '250';
		$fheight = '150';
	} elseif ($count <= 6 && $count > 5) {
	    $fitem = 'box6';
		$fwidth = '172';
		$fheight = '302';
	}
	?>
	<?php if ( $count <= 6 && !is_paged() ) { ?>
		    <div class="<?php echo $fitem; ?> feat_item">
			    <?php bizz_image('key=image&width='.$fwidth.'&height='.$fheight.'&class=thumbnail fl'); ?>
				<?php global $post;
				$featured_exists = bizz_image('key=image&width='.$fwidth.'&height='.$fheight.'&return=true');
				if ( !isset($featured_exists) && empty($featured_exists ) ) { ?>
				    <a title="<?php the_title(); ?>" href="<?php the_permalink() ?>">
					    <img src="<?php echo bloginfo('template_url'); ?>/lib_frame/thumb.php?src=<?php echo bloginfo('template_url'); ?>/lib_theme/images/post-bg.png&amp;h=<?php echo $fheight; ?>&amp;w=<?php echo $fwidth; ?>&amp;zc=1&amp;q=90" alt="<?php the_title(); ?>" class="thumbnail fl" />
					</a>
				<?php } ?>				
				<?php if (( $GLOBALS['opt']['bizzthemes_slmeta_cat'] == 'true')) { ?>
                <span class="cat">
				    <?php the_time('F j, Y'); ?>
				</span>  
				<?php } ?>
				<?php if (( $GLOBALS['opt']['bizzthemes_slmeta_tit'] == 'true')) { ?>
				    <h2 style="width:<?php echo ($fwidth-17); ?>px"><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<?php } ?>
			</div><!-- /.feat_item -->
	<?php } ?>
	<?php endwhile; ?>
	<div class="fix"><!----></div>
	</div><!-- /.top-featured -->	
	<?php endif; ?>	
	
	<?php } ?>

</div><!-- /.grid_12 -->
</div><!-- /.container_12 -->
</div><!-- /.slider-area -->

<?php bizz_aslider_after(); ?>

<?php }} 

function bizz_acatnav_area() {
global $wpdb;

if (is_tag()) {
    $tagname = get_query_var('tag');
	$tagid = $wpdb->get_row("SELECT * FROM $wpdb->terms WHERE slug = '$tagname'");
	$tag_id = $tagid->term_id;
	$numtags = $wpdb->get_row("SELECT * FROM $wpdb->term_taxonomy WHERE taxonomy = 'post_tag' AND term_id = '$tag_id'"); 
	$numcats = $numtags->count;
} elseif (is_category()) {
    $id = get_query_var('cat'); 
    $numcats = $wpdb->get_var("SELECT count FROM $wpdb->term_taxonomy WHERE (term_id = $id) "); 
} else {
	$numcats = '';
}

if ( (is_tag() || is_category()) && get_inc_categories("cat_exclude_") != '' ) {
?>
    <div class="cat-menu clearfix">
	<ul class="sf-menu">
	    <?php if (get_inc_categories("cat_exclude_") == '') { $exclude = '9999999'; } else { $exclude = get_inc_categories("cat_exclude_"); } ?>
		<?php wp_list_categories('title_li=&depth=0&include=' . $exclude .'&sort_column=menu_order'); ?>
		<?php if ($numcats > 6 && !is_paged()) { ?>
		    <li class="floading"><div class="floading-image"><!----></div></li>
		<?php } ?>
	</ul><!-- /.sf-menu -->
	</div><!-- /.cat-menu -->
<?php
}}
?>