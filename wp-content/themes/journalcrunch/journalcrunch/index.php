<?php get_header(); ?>
<!-- Begin #colleft -->
			<div id="colLeft">
			<!-- archive-title -->	
			<?php if(get_option('journal_box_model')!="normal"){?>			
						<?php if(is_month()) { ?>
						<h1>Archive from <?php the_time('F, Y') ?></h1>
						<?php } elseif(is_category()){ ?>
						<h1>Browsing "<?php $current_category = single_cat_title("", true); ?>"</h1>
						<?php } elseif(is_tag()) { ?>
						<h1>Tagged with "<?php wp_title('',true,''); ?>"</h1>
						<?php } elseif(is_author()) { ?>
						<h1>Articles by "<?php wp_title('',true,''); ?>"</h1>
						<?php }else{?>
						<!--<h1>Browsing All Articles</h1>-->
						<?php }
					}else{?>
						<?php if(is_month()) { ?>
						<div id="archive-title">
						Archive from <strong><?php the_time('F, Y') ?></strong>
						</div>
						<?php } ?>
						<?php if(is_category()) { ?>
						<div id="archive-title">
						Browsing"<strong><?php $current_category = single_cat_title("", true); ?></strong>"
						</div>
						<?php } ?>
						<?php if(is_tag()) { ?>
						<div id="archive-title">
						Tagged with "<strong><?php wp_title('',true,''); ?></strong>"
						</div>
						<?php } ?>
						<?php if(is_author()) { ?>
						<div id="archive-title">
						Articles by "<strong><?php wp_title('',true,''); ?></strong>"
						</div>
						<?php }
					} ?>
					<!-- /archive-title -->
		<?php $postindex = 1; ?>	
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>	
		<?php if(get_option('journal_box_model')!="normal"){?>
			<div class="postBox <?php if(($postindex % 2) == 0){ echo 'lastBox';}?>">
				<div class="postBoxInner">
					<?php
					if ( has_post_thumbnail()) {
						//the_post_thumbnail();?> 
						<img src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_image_path($post->ID); ?>&h=90&w=255&zc=1" alt="<?php the_title(); ?>">
					<?php } else {?>
						<img src="<?php bloginfo('template_directory'); ?>/images/nothumb.jpg" alt="No Thumbnail"  />
					<?php } ?>
					<h2><a href="<?php the_permalink() ?>" ><?php the_title(); ?></a></h2>
					<div class="excerpt"><?php  wpe_excerpt('wpe_excerptlength_index', 'wpe_excerptmore') ?></div>
					<div class="meta"> <?php the_time('M j, Y') ?> &nbsp;&nbsp;&nbsp;<img src="<?php bloginfo('template_directory'); ?>/images/ico_post_comments.png" alt="" /> <?php comments_popup_link('No Comments', '1 Comment ', '% Comments'); ?></div>
				</div>
				<a href="<?php the_permalink() ?>" class="readMore">Read More</a>
			</div>
			<?php ++$postindex; ?>
			<?php }else{?>
				<div id="singlePost" class="normal">
						<h1><a href="<?php the_permalink() ?>" ><?php the_title(); ?></a></h1>
						<div class="meta">
						 <?php the_time('M j, Y') ?> by <?php the_author_posts_link()?>&nbsp;&nbsp;&nbsp;<img src="<?php bloginfo('template_directory'); ?>/images/ico_post_comments.png" alt="" /> <?php comments_popup_link('No Comments', '1 Comment ', '% Comments'); ?>&nbsp;&nbsp;&nbsp;<img src="<?php bloginfo('template_directory'); ?>/images/ico_post_date.png" alt="" /> Posted under:  <?php the_category(', ') ?> 
						</div>
						<?php the_content(__('Read more &raquo;')); ?>
					</div>
			<?php }?>
			<?php endwhile; ?>

	<?php else : ?>

		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>
	<div style="clear:both;"></div>
			<?php if (function_exists("emm_paginate")) {
				emm_paginate();
			} ?>
	</div>
	<!-- End #colLeft -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
