<?php
/*
Template Name: Homepage
*/
?>
<?php get_header(); ?>

<!-- BEGIN SLIDER -->
<?php if(get_option('journal_slider')=='yes'){
	$slideshowloop = new WP_Query( array( 'post_type' => 'slideshow', 'order' => 'ASC' ) ); ?>
	<div id="slider">
	<?php 
			if($slideshowloop -> have_posts()){
			while ( $slideshowloop->have_posts() ) : $slideshowloop->the_post();
			$slideshow_meta = get_post_meta($post->ID,'_slideshow_meta',TRUE);
			?>
			<a href="<?php echo $slideshow_meta['linkto'];?>">
			<?php //the_post_thumbnail('slider-thumbnail',array("title" => ''.$slideshow_meta['caption'].''));?>
			<img src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_image_path($post->ID); ?>&h=370&w=940&zc=1" alt="<?php the_title(); ?>" title="<?php echo $slideshow_meta['caption'];?>">
			</a>
			<?php 
			endwhile;
			}else{?>
				<div style="border:1px solid #ddd; background:#000; opacity:0.5;text-align:center; padding:150px 100px 0; height:219px; font-size:14px; ">				<span style="opacity:1;color:#fff;text-shadow:none;">This is the slider. In order to have items here you need to create them in Admin > Slider Items section, on the left side menu. For proper display use images 940px x 370px.</span>
				</div>
			<?php }?>
			
	  </div>
	  <div style="width:940px; margin:0 auto 30px; background:url(<?php bloginfo('template_directory'); ?>/images/bk_shadow_slider.png) 0 -35px no-repeat; height:15px;"></div>
	   <!-- END SLIDER -->
	    <!-- SLIDER SETTINGS -->
	   <script type="text/javascript">
			$(window).load(function() {
				$('#slider').nivoSlider({
					effect:'<?php if(get_option('journal_slider_effect')==''): echo 'random';
						  else: echo get_option('journal_slider_effect');
						  endif;?>',
					slices:<?php if(get_option('journal_slider_slices')==''): echo '15';
						  else: echo get_option('journal_slider_slices');
						  endif;?>,
					animSpeed:<?php if(get_option('journal_slider_animation_speed')==''): echo '500';
						  else: echo get_option('journal_slider_animation_speed');
						  endif;?>,
					pauseTime:<?php if(get_option('journal_slider_pause_time')==''): echo '3000';
						  else: echo get_option('journal_slider_pause_time');
						  endif;?>,
					startSlide:0, //Set starting Slide (0 index)
					directionNav:true, //Next &amp; Prev
					directionNavHide:true, //Only show on hover
					controlNav:true, //1,2,3...
					controlNavThumbs:false, //Use thumbnails for Control Nav
					controlNavThumbsFromRel:false, //Use image rel for thumbs
					controlNavThumbsSearch: '.jpg', //Replace this with...
					controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
					keyboardNav:true, //Use left &amp; right arrows
					pauseOnHover:true, //Stop animation while hovering
					manualAdvance:false, //Force manual transitions
					captionOpacity:<?php if(get_option('journal_slider_caption_opacity')==''): echo '0.8';
						  else: echo get_option('journal_slider_caption_opacity');
						  endif;?>, //Universal caption opacity
					beforeChange: function(){},
					afterChange: function(){},
					slideshowEnd: function(){} //Triggers after all slides have been shown
				});
			});
			</script>
	<?php }else{?>
<!-- Begin #featuredPosts -->
	<?php
	 if(get_option('journal_featured_posts')!=''){
		 query_posts('tag=featured&showposts='.get_option('journal_featured_posts'));
		 }else{
		 query_posts('tag=featured&showposts=2');
	}
	 $featuredindex = 1; 
	 if (have_posts()) : ?>	
			<div id="featuredPosts">
		<?php while (have_posts()) : the_post(); ?>
				<div class="item <?php if(($featuredindex % 2) == 0){ echo 'lastItem';}?>">
					<h1><a href="<?php the_permalink() ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					<?php
					if ( has_post_thumbnail() ) {?>
						<a href="<?php the_permalink() ?>" title="Permanent Link to <?php the_title_attribute(); ?>">
						<?php //the_post_thumbnail('featured-post-thumbnail');?>
						<img src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_image_path($post->ID); ?>&h=290&w=430&zc=1" alt="<?php the_title(); ?>">
						</a>
					<?php } else {?>
						<img src="<?php bloginfo('template_directory'); ?>/images/nothumb_featured.jpg" alt="No Thumb"  />
					<?php } ?>
					<?php wpe_excerpt('wpe_excerptlength_featured', 'wpe_excerptmore'); ?>

					<a href="<?php the_permalink() ?>" class="readMore">Read More</a>
				</div>
		<?php ++$featuredindex; ?>
		<?php endwhile; ?>
		</div>
		<?php endif;
			wp_reset_query();?>
		<!-- End #featuredPosts -->
	<?php }?>
		<?php $postindex = 1; 
		 	if(!query_posts('showposts='.get_option('journal_home_posts').'&tag=homepost')){
				if(get_option('journal_home_posts')!=''){
			 		query_posts('showposts='.get_option('journal_home_posts'));
				}else{
					query_posts('showposts=6');
				}
			}else{
				query_posts('showposts='.get_option('journal_home_posts').'&tag=homepost');
				if(get_option('journal_home_posts')!=''){
			 		query_posts('showposts='.get_option('journal_home_posts').'&tag=homepost');
				}else{
					query_posts('showposts=6&tag=homepost');
				}
			}
		 
		 if (have_posts()) : while (have_posts()) : the_post(); ?>	
			<div class="postBox <?php if(($postindex % 3) == 0){ echo 'lastBox';}?>">
				<div class="postBoxInner">
				
					<?php
					if(has_post_thumbnail()) {
							//the_post_thumbnail();?>
							<img src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_image_path($post->ID); ?>&h=90&w=255&zc=1" alt="<?php the_title(); ?>">
						<?php } else {
							echo '<img src="'.get_bloginfo("template_url").'/images/nothumb.jpg"  alt="No Thumbnail"/>';
						}?>
					
					<h2><a href="<?php the_permalink() ?>" ><?php the_title(); ?></a></h2>
					<div class="excerpt"><?php  wpe_excerpt('wpe_excerptlength_index', 'wpe_excerptmore') ?></div>
					<div class="meta"> <?php the_time('M j, Y') ?> &nbsp;&nbsp;&nbsp;<img src="<?php bloginfo('template_directory'); ?>/images/ico_post_comments.png" alt="" /> <?php comments_popup_link('No Comments', '1 Comment ', '% Comments'); ?></div>
				</div>
				<a href="<?php the_permalink() ?>" class="readMore">Read More</a>
			</div>
			<?php ++$postindex; ?>
			<?php endwhile; ?>

	<?php else : ?>

		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; 
	wp_reset_query();?>
			
<?php get_footer(); ?>
