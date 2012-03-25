<?php
/*
Template Name: Portfolio
*/
?>

<?php get_header(); ?>

	<div id="portfolio">
	    <div id="portfolio-head">
	        <h1><?php echo get_option('workspace_portfolio_page_title');?></h1>
	        <ul id="filter">
	            <?php wp_list_categories('taxonomy=portfolio-type&orderby=name&order=DESC&hide_empty=0&hierarchical=1&title_li='); ?>
	        </ul>
	    </div><!-- #portfolio-head -->
	    <div id="portfolio-content">
	        <ul class="ourHolder grid">
	        <?php
	            query_posts( array(
		            'post_type' => 'portfolio',
				    'posts_per_page' => 48
				    )
			    );
	        ?>
	
	        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
	        <?php
	
	            $have_img = FALSE;
	            $have_video = FALSE;
	            $image[]=array();
	            $image[0]  = get_post_meta(get_the_ID(), 'tj_portfolio_image1', TRUE);
	            $image[1] = get_post_meta(get_the_ID(), 'tj_portfolio_image2', TRUE);
	            $image[2] = get_post_meta(get_the_ID(), 'tj_portfolio_image3', TRUE);
	            $image[3] = get_post_meta(get_the_ID(), 'tj_portfolio_image4', TRUE);
	            $image[4] = get_post_meta(get_the_ID(), 'tj_portfolio_image5', TRUE);
	
	            $src = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), array( '9999','9999' ), false, '' );
	
	            $video_embed = get_post_meta(get_the_ID(), 'tj_video_embed_portfolio', TRUE);
	            $brief_desc = get_post_meta(get_the_ID(), 'tj_portfolio_brief_desc', TRUE);
	            $extended_desc = get_post_meta(get_the_ID(), 'tj_portfolio_extended_desc', TRUE);
	
	            for($i=0;$i<5;$i++){
	                if($image[$i]!=''){
	                    $have_img = TRUE;
	                }
	            }
	
	            if($video_embed){
	                $have_video = TRUE;
	            }
	
	            $count = 0;
	        ?>
	
	                <li class="item" data-type="<?php
	                                $terms = get_the_terms( get_the_ID(), 'portfolio-type' );
		                            if(is_array($terms)){
	                                    foreach ($terms as $term){
	                                        echo 'cat-item-'.$term->term_id.' ';
	                                    }
	                                }
	                            ?>" data-id="id-<?php the_ID();?>" >
	
	                        <?php if($video_embed){?>
	
	                        <div class="img-div">
	                            <a title="<?php the_title();?>" href="<?php the_permalink(); ?>" >
	                                <?php the_post_thumbnail('portfolio-thumb', array('class' => 'entry-thumb')); ?>
	                                <div class="overlay">
	                                    <span class="icon-video"></span>
	                                </div>
	                            </a>
	
	                            <div class="image-shadow-bottom"></div>
	
	                            <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
								<div class="entry-excerpt"><?php echo stripslashes(htmlspecialchars_decode($brief_desc)); ?></div>
	                        </div>
	                        <?php }elseif(is_array($src)){?>
	                            <div class="img-div">
	                                <?php
	                                    if($have_img){
	                                        for($i=0;$i<5;$i++){
	                                            if($image[$i]!=''){ $count = $i;
	                                ?>
	    		                    <a title="<?php the_title();?>" href="<?php echo $image[$i]; ?>" rel="prettyPhoto[<?php the_ID();?>]"><?php the_post_thumbnail('portfolio-thumb', array('class' => 'entry-thumb')); ?>
	                                    <div class="overlay">
	                                        <span class="icon"></span>
	                                    </div>
	                                </a>
	                                <div class="image-shadow-bottom"></div>
	                                <?php
	                                                break;
	                                            }
	                                        }
	                                    }else{
	                                ?>
	
	                                <a title="<?php the_title();?>" href="<?php the_permalink(); ?>" rel="<?php the_title();?>"><?php the_post_thumbnail('portfolio-thumb', array('class' => 'entry-thumb')); ?>
	                                    <div class="overlay">
	                                        <span class="icon"></span>
	                                    </div>
	                                </a>
	                                <div class="image-shadow-bottom"></div>
	                                <?php }?>
	                            </div>
				            <?php
	                            for($i=$count+1;$i<5;$i++){
	                                if($image[$i]!=''){
	                        ?>
	                            <a title="<?php the_title();?>" href="<?php echo $image[$i]; ?>" rel="prettyPhoto[<?php the_ID();?>]"></a>
	                        <?php
	                                }
	                            }
	                        ?>
	
	                        <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				            <div class="entry-excerpt"><?php echo stripslashes(htmlspecialchars_decode($brief_desc)); ?></div>
				            
	                    <?php }else{?>
	
	                        
	                        <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				            <div class="entry-excerpt"><?php echo stripslashes(htmlspecialchars_decode($brief_desc)); ?></div>
	
	                    <?php }?>
	                    
	                </li>
	                
	               <?php $item_count ++; ?> 
	
	        <?php endwhile; else: ?>
		    <?php endif; ?>
	    		    
	        <?php wp_reset_postdata();?>
	
	        </ul><!-- .ourHolder .grid -->
	    </div><!-- #portfolio-content -->
	</div><!-- #portfolio -->

<?php get_footer(); ?>