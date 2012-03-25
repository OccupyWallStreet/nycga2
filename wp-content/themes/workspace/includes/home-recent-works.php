<?php
	query_posts( array(
		'post_type' => 'portfolio',
		'posts_per_page' => get_option('workspace_home_portfolio_posts_num')
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

	<li id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
		
		<?php  if($video_embed){?>

			<a title="<?php the_title();?>" href="<?php the_permalink(); ?>" >
				<?php the_post_thumbnail('home-thumb', array('class' => 'entry-thumb')); ?>
				<div class="overlay">
					<span class="icon-video"></span>
				</div>
			</a>
			<div class="image-shadow-bottom"></div>
			<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			<div class="entry-excerpt"><?php echo stripslashes(htmlspecialchars_decode($brief_desc)); ?></div>
		
		<?php }elseif(is_array($src)){?>
			<div class="img-div">
			<?php
				if($have_img){
					for($i=0;$i<5;$i++){
						if($image[$i]!=''){ $count = $i;
			?>
							<a title="<?php the_title();?>" href="<?php echo $image[$i]; ?>" rel="prettyPhoto[<?php the_ID();?>]"><?php the_post_thumbnail('home-thumb', array('class' => 'entry-thumb')); ?>
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

				<a title="<?php the_title();?>" href="<?php the_permalink(); ?>" rel="<?php the_title();?>"><?php the_post_thumbnail('home-thumb', array('class' => 'entry-thumb')); ?>
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

<?php endwhile; else: ?>
<?php endif; ?>
<?php wp_reset_postdata();?>

