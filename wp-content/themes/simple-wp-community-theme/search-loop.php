			<?php /* this search loop shows your blog posts in the unified search 
			you may modify it as you want, It is a copy from my theme 
			
			*/
			
			
			?>
			<?php do_action( 'bp_before_blog_search' ) ?>
			<?php global $wp_query;
				$wp_query->is_search=true;
				$search_term=$_REQUEST['search-terms'];
				if(empty($search_term))
					$search_term=$_REQUEST['s'];
				$wp_query->query("s=".$search_term);?>
			<?php if ( have_posts() ) : ?>
            <?php while(have_posts()):the_post(); global $post;?>
			<?php do_action( 'bp_before_blog_post' ) ?>
                <div class="post"> <!-- Post goes here... --> 
                	<div class="post-content"> 
                    	<h3 class="post-title"><?php the_title();?></h3>
                        <div> 
                        	<?php the_excerpt();?>                           
                        </div>                       
                        <div class="clear"> </div>
                    </div>
                    <div class="postmetadata"> 
                    	<span><?php the_time('F j, Y') ?>  | <?php the_category(', ') ?> | <?php comments_popup_link( __( 'No Comments &#187;', 'bpmag' ), __( '1 Comment &#187;', 'bpmag' ), __( '% Comments &#187;', 'bpmag' ) ); ?></span>
                        <div class="readmore"><a href="<?php the_permalink();?>"><?php _e("Read more...","bpmag");?></a></div>
                    </div>
					
                </div><!-- Post ends here... -->
				<?php do_action( 'bp_after_blog_post' ) ?>
                <?php endwhile;?>
				<?php if(!bpmag_is_advance_search()):?>
				<div class="navigation">
					<?php if(function_exists("wp_pagenavi"))wp_pagenavi();else{ ?>
					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'bpmag' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'bpmag' ) ) ?></div>
				<?php }?>
				</div>
				<?php endif;?>
				<?php else : ?>
				<div class="post">
					<div class="post-content 404">
					<?php echo sprintf(__("We are sorry, but we could not find anything for the search term '%s'","bpmag"),$search_term);?>

				</div>
				</div>
				

			<?php endif; ?>
                 <?php do_action( 'bp_after_blog_search' ) ?>      
           