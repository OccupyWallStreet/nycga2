			<div id="post-entry">
						<?php
				$newstitle = get_option('dev_businessblog_news_title');
				$featured_category = get_option('dev_businessblog_feature_cat');
						?>
							<?php
									if ($newstitle == ""){
										$newstitle = "Set this all up under theme options";
									}
									?>
			<h2 id="post-header"><?php echo stripslashes($newstitle); ?></h2><div class="clear"></div>
<?php
if ($featured_category != ""){
?>
			<?php
			// get category id
				$category_ID = $wpdb->get_var( "SELECT term_id FROM $wpdb->terms WHERE slug = '" . $featured_category . "'" );
			?>
			<?php 
			// exclude the featured category and the list of latest offset
			query_posts('cat=-'. $category_ID . '&showposts=1'); ?>
							  <?php while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-author">
			<?php echo get_avatar( get_the_author_meta('email'), '48' ); ?>
			<p><?php _e('by', 'business-blog'); ?> <?php the_author_posts_link(); ?></p>
			</div>
			<div class="post-meta">
			<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
			<div class="post-date">
			<div class="pdate"><?php the_time('F jS Y') ?> in <?php the_category(', ') ?></div>
			<div class="pcomment"><?php comments_popup_link(__('Leave Comment'), __('1 Comments'), __('% Comments')); ?></div>
			</div>
			<div class="post-content">
			<?php the_excerpt(); ?>
			</div>
			<div class="post-date">
			<div class="ptag"><?php the_tags(__('Tagged:&nbsp;'), ', ', ''); ?> </div>
			<div class="pmore"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php _e('read more &raquo;', 'business-blog'); ?></a></div>
			</div>
			</div>
			</div>
						<?php endwhile; 
						?>	
			<div class="post-meta-more">
			<h3><?php _e('More Articles;', 'business-blog'); ?></h3>
			<ul>
							<?php 
							// exclude the featured category and the list of latest offset
							query_posts('cat=-'. $category_ID . '&posts_per_page=10&offset=1'); ?>
											  <?php while (have_posts()) : the_post(); ?>
			<li><span class="moredate"><?php the_time('F jS') ?></span><span class="moretitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></span></li>
			<?php endwhile; 
			?>
			</ul>
			</div>
<?php
}
?>
			</div>