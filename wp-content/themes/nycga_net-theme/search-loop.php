			<?php /* this search loop shows your blog posts in the unified search 
			you may modify it as you want, It is a copy from my theme 
			
			*/
			
			
			?>
			<?php do_action( 'bp_before_blog_search' ) ?>
			<?php global $wp_query;
				$wp_query->is_search=true;
				
				if(!empty($_REQUEST['search-terms'])){
					$search_term=$_REQUEST['search-terms'];
				} elseif(!empty($_REQUEST['s'])) {
					$search_term=$_REQUEST['s'];
				}
				
			if(!empty($search_term)){
				$wp_query->query("s=".$search_term);

			if ( have_posts() ) : ?>
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
                    	<span><?php the_time('F j, Y') ?>  | <?php the_category(', ') ?> | <?php comments_popup_link( __( 'No Comments &#187;', 'cc' ), __( '1 Comment &#187;', 'cc' ), __( '% Comments &#187;', 'cc' ) ); ?></span>
                        <div class="readmore"><a href="<?php the_permalink();?>"><?php _e("Read more...","cc");?></a></div>
                    </div>
					
                </div><!-- Post ends here... -->
				<?php do_action( 'bp_after_blog_post' ) ?>
                <?php endwhile;?>
				<?php if(!cc_is_advance_search()):?>
				<div class="navigation">
					<?php if(function_exists("wp_pagenavi"))wp_pagenavi();else{ ?>
					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'cc' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'cc' ) ) ?></div>
				<?php }?>
				</div>
				<?php endif;?>
				<?php else : ?>
				<div class="post">
					<div class="post-content 404">
					<?php echo sprintf(__("We are sorry, but we could not find anything for the search term '%s'","cc"),$search_term);?>

				<?php locate_template( array( 'searchform.php' ), true ) ?>
				</div>
				</div>
				

				<?php endif; ?>
				<?php } ?>
			<?php do_action( 'bp_after_blog_search' ) ?>        