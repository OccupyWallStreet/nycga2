	<?php 
		$cat_rows = get_option('dev_buddydaily_featurecat_row_num');
		$cat_one = get_option('dev_buddydaily_featurecat_one');
		$cat_two = get_option('dev_buddydaily_featurecat_two');
		$cat_three = get_option('dev_buddydaily_featurecat_three');
		$cat_four = get_option('dev_buddydaily_featurecat_four');
		$cat_five = get_option('dev_buddydaily_featurecat_five');
		$cat_six = get_option('dev_buddydaily_featurecat_six');
		$cat_seven = get_option('dev_buddydaily_featurecat_seven');
		$cat_eight = get_option('dev_buddydaily_featurecat_eight');
		$cat_nine = get_option('dev_buddydaily_featurecat_nine');
		$cat_image_display = get_option('dev_buddydaily_featurecat_image_size');
	?>

	<?php
	
	if (($cat_rows == "")||($cat_rows == "1")):
		?>
			<div class="column-content">
				<?php if ($cat_one != ""):?>
				<div class="content-block">				
					<?php $my_query = new WP_Query('category_name='. $cat_one . '&posts_per_page=1');
				  while ($my_query->have_posts()) : $my_query->the_post();
				  $do_not_duplicate = $post->ID;?>
					<div class="feature-wrap">													
				    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
							<?php the_excerpt(); ?>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
					</div>
					<h3><?php the_category() ?></h3>
				  <?php endwhile; ?>
				<ul class="category-preview">
				  <?php $my_query = new WP_Query('category_name='. $cat_one . '&posts_per_page=5');
					  while ($my_query->have_posts()) : $my_query->the_post();
				  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
						<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ($cat_two != ""):?>
				<div class="content-block">				
						<?php $my_query = new WP_Query('category_name='. $cat_two . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_two . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ($cat_three != ""):?>
				<div class="content-block-end">				
						<?php $my_query = new WP_Query('category_name='. $cat_three . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_three . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>
	<?php
	if ($cat_rows == "2"):
		?>
			<div class="column-content">
				<?php if ($cat_one != ""):?>
				<div class="content-block">				
					<?php $my_query = new WP_Query('category_name='. $cat_one . '&posts_per_page=1');
				  while ($my_query->have_posts()) : $my_query->the_post();
				  $do_not_duplicate = $post->ID;?>
					<div class="feature-wrap">													
				    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
							<?php the_excerpt(); ?>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
					</div>
					<h3><?php the_category() ?></h3>
				  <?php endwhile; ?>
				<ul class="category-preview">
				  <?php $my_query = new WP_Query('category_name='. $cat_one . '&posts_per_page=5');
					  while ($my_query->have_posts()) : $my_query->the_post();
				  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
						<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ($cat_two != ""):?>
				<div class="content-block">				
						<?php $my_query = new WP_Query('category_name='. $cat_two . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_two . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ($cat_three != ""):?>
				<div class="content-block-end">				
						<?php $my_query = new WP_Query('category_name='. $cat_three . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_three . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>
				<div class="clear"></div>
			</div>
			<div class="column-content">
				<?php if ($cat_four != ""):?>
				<div class="content-block">				
					<?php $my_query = new WP_Query('category_name='. $cat_four . '&posts_per_page=1');
				  while ($my_query->have_posts()) : $my_query->the_post();
				  $do_not_duplicate = $post->ID;?>
					<div class="feature-wrap">													
				    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>			<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
							<?php the_excerpt(); ?>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
					</div>
					<h3><?php the_category() ?></h3>
				  <?php endwhile; ?>
				<ul class="category-preview">
				  <?php $my_query = new WP_Query('category_name='. $cat_four . '&posts_per_page=5');
					  while ($my_query->have_posts()) : $my_query->the_post();
				  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
						<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ($cat_five != ""):?>
				<div class="content-block">				
						<?php $my_query = new WP_Query('category_name='. $cat_five . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_five . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ($cat_six != ""):?>
				<div class="content-block-end">				
						<?php $my_query = new WP_Query('category_name='. $cat_six . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_six . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>
				<div class="clear"></div>
			</div>
	<?php endif; ?>
	<?php
	if ($cat_rows == "3"):
		?>
			<div class="column-content">
				<?php if ($cat_one != ""):?>
				<div class="content-block">				
					<?php $my_query = new WP_Query('category_name='. $cat_one . '&posts_per_page=1');
				  while ($my_query->have_posts()) : $my_query->the_post();
				  $do_not_duplicate = $post->ID;?>
					<div class="feature-wrap">													
				    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>			<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
							<?php the_excerpt(); ?>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
					</div>
					<h3><?php the_category() ?></h3>
				  <?php endwhile; ?>
				<ul class="category-preview">
				  <?php $my_query = new WP_Query('category_name='. $cat_one . '&posts_per_page=5');
					  while ($my_query->have_posts()) : $my_query->the_post();
				  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
						<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ($cat_two != ""):?>
				<div class="content-block">				
						<?php $my_query = new WP_Query('category_name='. $cat_two . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_two . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ($cat_three != ""):?>
				<div class="content-block-end">				
						<?php $my_query = new WP_Query('category_name='. $cat_three . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_three . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>
				<div class="clear"></div>
			</div>
			<div class="column-content">
				<?php if ($cat_four != ""):?>
				<div class="content-block">				
					<?php $my_query = new WP_Query('category_name='. $cat_four . '&posts_per_page=1');
				  while ($my_query->have_posts()) : $my_query->the_post();
				  $do_not_duplicate = $post->ID;?>
					<div class="feature-wrap">													
				    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>			<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
							<?php the_excerpt(); ?>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
					</div>
					<h3><?php the_category() ?></h3>
				  <?php endwhile; ?>
				<ul class="category-preview">
				  <?php $my_query = new WP_Query('category_name='. $cat_four . '&posts_per_page=5');
					  while ($my_query->have_posts()) : $my_query->the_post();
				  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
						<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ($cat_five != ""):?>
				<div class="content-block">				
						<?php $my_query = new WP_Query('category_name='. $cat_five . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>			<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_five . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ($cat_six != ""):?>
				<div class="content-block-end">				
						<?php $my_query = new WP_Query('category_name='. $cat_six . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
							<div class="feature-wrap">													
						    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j, Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
										<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
									<?php the_excerpt(); ?>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
							</div>
							<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_six . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>
				<div class="clear"></div>
			</div>
			<div class="column-content">
				<?php if ($cat_seven != ""):?>
				<div class="content-block">				
					<?php $my_query = new WP_Query('category_name='. $cat_seven . '&posts_per_page=1');
				  while ($my_query->have_posts()) : $my_query->the_post();
				  $do_not_duplicate = $post->ID;?>
					<div class="feature-wrap">													
				    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
							<?php the_excerpt(); ?>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
					</div>
					<h3><?php the_category() ?></h3>
				  <?php endwhile; ?>
				<ul class="category-preview">
				  <?php $my_query = new WP_Query('category_name='. $cat_seven . '&posts_per_page=5');
					  while ($my_query->have_posts()) : $my_query->the_post();
				  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
						<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ($cat_eight != ""):?>
				<div class="content-block">				
						<?php $my_query = new WP_Query('category_name='. $cat_eight . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_eight . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ($cat_nine != ""):?>
				<div class="content-block-end">				
						<?php $my_query = new WP_Query('category_name='. $cat_nine . '&posts_per_page=1');
					  while ($my_query->have_posts()) : $my_query->the_post();
					  $do_not_duplicate = $post->ID;?>
						<div class="feature-wrap">													
					    	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>			<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image" style="height:100px;width:190px;display:block;background:url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
								<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
						</div>
						<h3><?php the_category() ?></h3>
					  <?php endwhile; ?>
					<ul class="category-preview">
					  <?php $my_query = new WP_Query('category_name='. $cat_nine . '&posts_per_page=5');
						  while ($my_query->have_posts()) : $my_query->the_post();
					  if( $post->ID == $do_not_duplicate ) continue; update_post_caches($posts); ?>
							<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>
				<div class="clear"></div>
			</div>
	<?php endif; ?>