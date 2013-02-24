<?php do_action( 'bp_before_sidebar' ) ?>
<div class="v_line v_line_right visible-desktop"></div>
<div id="sidebar" class="span4 widgetarea">
	<div class="paddersidebar right-sidebar-padder">

	<?php do_action( 'bp_before_after_sidebar' ) ?>
	<?php if( ! dynamic_sidebar( 'sidebar' )): ?>    
		
	<?php if ( is_singular() ) { ?>
		<div class="widget">
			<h3 class="widgettitle" ><?php _e('Recent Posts', 'cc'); ?></h3>
			<ul>
				<?php
				$myposts = get_posts('numberposts=5&offset=0&category=0');
				foreach($myposts as $post) : setup_postdata($post);
				?>
				<li><span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php } else { ?>
		<div class="widget">
			<h3 class="widgettitle" ><?php _e('Random Posts', 'cc'); ?></h3>
			<ul>
				<?php
				$rand_posts = get_posts('numberposts=5&orderby=rand');
				foreach( $rand_posts as $post ) :
				?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php } ?>
	<div class="widget tags">
		<h3 class="widgettitle" ><?php _e('Search by Tags!', 'cc'); ?></h3>
		<div><?php wp_tag_cloud('smallest=9&largest=18'); ?></div>
	</div>	
	<div class="widget">
		<h3 class="widgettitle" ><?php _e('Archives', 'cc'); ?></h3>
		<ul>
			<?php wp_get_archives( 'type=monthly' ); ?>
		</ul>
	</div>
	<div class="widget">
		<h3 class="widgettitle" ><?php _e('Links', 'cc'); ?></h3>
		<ul>
			<?php wp_list_bookmarks('title_li=&categorize=0&orderby=id'); ?>
		</ul>
	</div>
	<div class="widget">
		<h3 class="widgettitle" ><?php _e('Meta', 'cc'); ?></h3>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<?php wp_meta(); ?>
		</ul>
	</div>

	<?php endif; // end primary widget area ?>
	
	
	<?php do_action( 'bp_inside_after_sidebar' ) ?>

	</div><!-- .padder -->
</div><!-- #sidebar -->


<?php do_action( 'bp_after_sidebar' ) ?>