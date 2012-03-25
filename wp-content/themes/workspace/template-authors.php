<?php
/*
Template Name: Authors
*/
?>

<?php get_header(); ?>

	<h1 class="page-title"><?php the_title(); ?></h1>
    <div id="content">
	    <div class="entry-content">
	        <div class="author-box">
	            <?php
	                if (get_query_var('paged')){
	                    $paged = get_query_var('paged');
	                } elseif (get_query_var('page')) {
	                    $paged = get_query_var('page');
	                } else {
	                    $paged = 1;
	                }
	            ?>
	                <?php
	                    $user_list = tj_get_users( 10, $paged );
		                    foreach($user_list as $author) {
								if (get_the_author_meta('exclude',$author->ID) != 1) {
	
								$author_id = $author->ID;
								$author_name = $author->display_name;
								/* custom profile fields */
								if ( isset($author->twitter) ) { $author_twitter = $author->twitter; } else { $author_twitter = ''; }
								if ( isset($author->facebook) ) { $author_facebook = $author->facebook; } else { $author_facebook = ''; }
								$author_page_url = get_author_posts_url($author_id);
	
								$count++;
	
							?>
							<div class="post-author-box">
							    <h3 class="title"><?php printf( $author_name ); ?></h3>
							    <?php echo get_avatar( $author_id, '70' ); ?>
							    <div class="profile-description">
							        <?php the_author_meta( 'description', $author_id ); ?>
							        <p><a href="<?php echo $author_page_url; ?>"><?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'themejunkie' ), $author_name ); ?></a></p>
							        <div class="profile-social">
								    	<ul>
								    		<?php if ($author_twitter != '') { ?><li class="twitter"><a href="http://www.twitter.com/<?php echo $author_twitter; ?>"><?php _e( 'Twitter', 'themejunkie' ); ?></a></li><?php } ?>
								    		<?php if ($author_facebook != '') { ?><li class="facebook"><a href="<?php echo $author_facebook; ?>"><?php _e( 'Facebook', 'themejunkie' ); ?></a></li><?php } ?>
								    	</ul>
								    </div><!-- .profile-social -->
								</div><!-- .profile-description	-->
							</div><!-- .post-author-box -->
							<div class="clear"></div>
							<?php
							}
						}
	                ?>
	        </div><!-- .author-box -->
	    </div><!-- .entry-content -->
    </div><!-- #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

 
