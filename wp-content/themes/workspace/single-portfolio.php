<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<?php
	        $image = array();
	        $video_embed = get_post_meta(get_the_ID(), 'tj_video_embed_portfolio', TRUE);
	        $m4v_url = get_post_meta(get_the_ID(), 'tj_video_m4v', TRUE);
	        $ogv_url = get_post_meta(get_the_ID(), 'tj_video_ogv', TRUE);
            $have_embed = FALSE;
	        $have_img = FALSE;

            if($video_embed != ''){
                $have_embed = TRUE;
            }
			$image[0] = get_post_meta(get_the_ID(), 'tj_portfolio_image1', TRUE);
			$image[1] = get_post_meta(get_the_ID(), 'tj_portfolio_image2', TRUE);
			$image[2] = get_post_meta(get_the_ID(), 'tj_portfolio_image3', TRUE);
			$image[3] = get_post_meta(get_the_ID(), 'tj_portfolio_image4', TRUE);
			$image[4] = get_post_meta(get_the_ID(), 'tj_portfolio_image5', TRUE);
	        $src = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), array( '9999','9999' ), false, '' );
	        for($i=0;$i<5;$i++){
	                if($image[$i]!=''){
	                    $have_img = TRUE;
	                }
	        }
	
		?>
    	<div id="portfolio-head">		
			<h1><?php the_title(); ?> </h1>
			<div class="portfolio-nav">
				<span class="nav-previous"><?php next_post_link(__('%link', 'themejunkie'), '<span class="arrow">&larr; Previous</span>') ?> | </span>
				<span class="nav-next"><?php previous_post_link(__('%link', 'themejunkie'), '<span class="arrow">Next &rarr;</span>') ?></span>
			</div><!-- .portfolio-nav -->
		</div>		
        <div id="content">
		    <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<?php if($have_img):?>
					<div id="gallery">
						<div class="slides_container">
							<?php   for($i=0;$i<5;$i++):?>
							<?php       if($image[$i]):     ?>
								<img src="<?php echo $image[$i];?>"/>
							<?php endif; endfor;?>
						</div>
						<div class="gallery-flag">
							<div class="gallery-flag-prev"></div>
							<div class="gallery-flag-next"></div>
						</div>
					</div><!-- .gallery -->
				<?php endif;?>
				
				<?php if($video_embed!=''):?>
					<div class="clear"></div>
					<div class="video-portfolio">
						<?php echo stripslashes(htmlspecialchars_decode($video_embed)); ?>
					</div>
				<?php endif; ?>
		
				<?php if(get_the_content('')!=''):?>
					<div class="entry-content">
						<?php the_content(''); ?>
						<?php edit_post_link( __('[Edit]', 'themejunkie'), '<span class="edit-post">', '</span>' ); ?>						
					</div><!-- .entry-content -->
				<?php endif;?>
	        
		    </div><!-- #post-<?php the_ID(); ?> -->

            <?php if(get_option('workspace_show_portfolio_author') == 'on') { ?>
			    <div class="clear"></div>
			    <div class="entry-author">
				    <div class="author-avatar"><?php echo get_avatar( get_the_author_meta('email'), '48' ); ?></div>
				    <strong><?php the_author_posts_link(); ?></strong><br />
				    <p><?php the_author_meta( 'description' ); ?></p>
				    <div class="clear"></div>
			    </div><!-- .entry-author -->
		    <?php } ?>

    	    <?php if(get_option('workspace_show_portfolio_comments') == 'on') { ?>
	  		    <?php comments_template(); ?>
	  	    <?php } ?>
		</div><!-- #content -->
		<?php endwhile; else: ?>
	
		<div id="post-0" <?php post_class() ?>>
			<h1 class="entry-title"><?php _e('Error 404 - Not Found', 'themejunkie') ?></h1>
			<div class="entry-content">
				<p><?php _e("Sorry, but you are looking for something that isn't here.", "themejunkie") ?></p>
			</div><!-- .entry-content -->
		</div><!-- #post-0 -->
	
	<?php endif; ?>

	<div id="sidebar">
	    <div class="widget">
	    	<h3 class="widget-title"><span><?php _e('Overview', 'themejunkie') ?></span></h3>						
	        <?php
				$extended_desc = get_post_meta(get_the_ID(), 'tj_portfolio_extended_desc', TRUE);
				$link = get_post_meta(get_the_ID(), 'tj_portfolio_link', TRUE);
			?>
	        <div class="portfolio-meta">
				<?php if($extended_desc != '') : ?>
					<p class="extended-desc">
						<?php echo stripslashes(htmlspecialchars_decode($extended_desc)); ?>
					</p>
				<?php  else : echo '<p>Please write extended description for the portfolio.</p>'; endif; ?>	
				<?php if($link != '') : ?>
					<p class="portfolio-link"><a target="_blank" href="<?php echo $link; ?>"><?php _e('View Project &rarr;', 'themejunkie'); ?></a></p>
				<?php endif; ?>									
				<?php $terms = get_the_terms( get_the_ID(), 'portfolio-type' ); ?>
				<?php if(is_array($terms)){?>
					<h3 class="portfolio-type-title"><?php _e('Portfolio Types', 'themejunkie'); ?></h3>			
					<ul>
						<?php foreach ($terms as $term) :  ?>
							<li><?php echo $term->name; ?></li>
						<?php endforeach; ?>
					</ul>
				<?php } else { ?>
				<?php }?>		
				<div class="clear"></div>		
			</div><!-- .portfolio-meta -->
		</div><!-- .widget -->
	    <?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('portfolio-sidebar') ) ?>
	</div><!-- #sidebar -->
	
<?php get_footer(); ?>