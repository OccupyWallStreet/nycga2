<?php
// sidebar for front page 	
if (is_home()) { ?>
	<div class="sidebar blog_sidebar noprint">
		<div class="padding">
		
			<ul class="sidebarTabs">
				<li><a><span><?php _e('Categories','smashingMultiMedia');?></span></a></li>
				<li><a><span><?php _e('Tags','smashingMultiMedia');?></span></a></li>
			</ul>
			
			<div class="sidebarPanes" >
				<div class="widget widget_categories">
					<ul>
						<?php 
							$orderBy 	= get_option('wps_catNavi_orderbyOption');
							$order 		= get_option('wps_catNavi_orderOption');
							$include	= get_option('wps_catNavi_inclOption');
							$exclude	= get_option('wps_catNavi_exclOption');
							$titleLi	='';
							$catMenuArg = array(
								'include'   =>$include,
								'exclude'	=>$exclude,
								'orderby'	=>$orderBy,
								'order'		=>$order,
								'title_li'	=>$titleLi,
								'show_count'=>0,
							);
								
							wp_list_categories($catMenuArg); 
						?>
					</ul>
				</div><!-- widget -->
			 
				<div class="widget widget_tags">
					
					<span class="tags">
						<?php wp_tag_cloud('number=0&smallest=8&largest=18'); ?>
					</span>
				</div><!-- widget -->
			</div><!-- sidebarPanes -->
			
			<?php switch(get_option('wps_latestTweet_option')){
				case 'lt_no':
								      					
				break;
											
				case 'lt_yes': ?>
					<h3 class="latestTweet"><?php _e('Latest Tweet','smashingMultiMedia');?></h3>		
					<div class="widget latestTweet_widget">
						<p class="widgetPadding">
							
							<?php 
								$username = get_option('wps_twitter');
								wp_echoTwitter($username); 
							?>
						</p><!-- widgetPadding -->
						<span class="arrow arrow1"></span>
						<span class="arrow arrow2"></span>
						<span class="arrow arrow3"></span>
					</div><!-- widget -->
				<?php break;
			} ?>
			
			<?php if ( is_sidebar_active('blog_widget_area') ) : dynamic_sidebar('blog_widget_area'); endif; ?>
			
		</div><!-- padding -->
	</div><!-- sidebar -->	

<?php } 
//when in a category
elseif (is_category() || is_archive() || is_search() || is_404()) { ?>
	<div class="sidebar category_sidebar noprint">
		<div class="padding">
		
			<ul class="sidebarTabs">
				<li><a><span><?php _e('Categories','smashingMultiMedia');?></span></a></li>
				<li><a><span><?php _e('Tags','smashingMultiMedia');?></span></a></li>
			</ul>
			
			<div class="sidebarPanes" >
				<div class="widget widget_categories">
					<ul>
						<?php 
							$orderBy 	= get_option('wps_catNavi_orderbyOption');
							$order 		= get_option('wps_catNavi_orderOption');
							$include	= get_option('wps_catNavi_inclOption');
							$exclude	= get_option('wps_catNavi_exclOption');
							$titleLi	='';
							$catMenuArg = array(
								'include'   =>$include,
								'exclude'	=>$exclude,
								'orderby'	=>$orderBy,
								'order'		=>$order,
								'title_li'	=>$titleLi,
								'show_count'=>0,
							);
								
							wp_list_categories($catMenuArg); 
						?>
					</ul>
				</div><!-- widget -->
			 
				<div class="widget widget_tags">
					
					<span class="tags">
						<?php wp_tag_cloud('number=0&smallest=8&largest=18'); ?>
					</span>
				</div><!-- widget -->
			</div><!-- sidebarPanes -->
			
			<?php 	
				if (is_category()) { if ( is_sidebar_active('category_widget_area') ) : dynamic_sidebar('category_widget_area'); endif; } 
				elseif (is_archive()) { if ( is_sidebar_active('archive_widget_area') ) : dynamic_sidebar('archive_widget_area'); endif;}
				elseif (is_search()) { if ( is_sidebar_active('search_widget_area') ) : dynamic_sidebar('search_widget_area'); endif;}
				elseif (is_404()) { if ( is_sidebar_active('page404_widget_area') ) : dynamic_sidebar('page404_widget_area'); endif;}
			?>
			
			    
		</div><!-- padding -->
	</div><!-- category_sidebar -->
	
<?php } 
// sidebar for Single Posts
elseif (is_single()) { ?>
	<div class="sidebar single_sidebar noprint">
		<div class="padding">
		
			<ul class="sidebarTabs">
				<li><a><span><?php _e('Categories','smashingMultiMedia');?></span></a></li>
				<li><a><span><?php _e('Tags','smashingMultiMedia');?></span></a></li>
			</ul>
			
			<div class="sidebarPanes" >
				<div class="widget widget_categories">
					<ul>
						<?php 
							$catsy = get_the_category();
							$myCat = $catsy[0]->cat_ID;
							$orderBy 	= get_option('wps_catNavi_orderbyOption');
							$order 		= get_option('wps_catNavi_orderOption');
							$include	= get_option('wps_catNavi_inclOption');
							$exclude	= get_option('wps_catNavi_exclOption');
							$titleLi	='';
							$catMenuArg = array(
								'include'   		=>$include,
								'exclude'			=>$exclude,
								'orderby'			=>$orderBy,
								'order'				=>$order,
								'title_li'			=>$titleLi,
								'show_count'		=>0,
								'current_category' 	=>$myCat,
							);
								
							wp_list_categories($catMenuArg); 
						?>
					</ul>
				</div><!-- widget -->
			 
				<div class="widget widget_tags">
					
					<span class="tags">
						<?php wp_tag_cloud('number=0&smallest=8&largest=18'); ?>
					</span>
				</div><!-- widget -->
			</div><!-- sidebarPanes -->
						
			<!--  Now lets display related posts from the same category -->
			<?php $categories 	= get_the_category($post->ID);
			if ($categories) {
				$category_ids 	= array();
				foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
					$showPosts 	= get_option('wps_related_showposts');
					
					$param		= array(
						'category__in' 		=> $category_ids,
						'post__not_in' 		=> array($post->ID),
						'showposts'			=> $showPosts, 
						'caller_get_posts'	=> 1
					);
					$my_query = new wp_query($param);
					if( $my_query->have_posts() ) {?>
						<div class="widget widget_related">
							<div class="widgetPadding">
								<h2 class="title"><span><?php _e('Related','smashingMultiMedia'); ?></span></h2>
								<ul class="related">
								<?php while ($my_query->have_posts()) {
									$my_query->the_post();
								?>
									<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
								<?php
								}
						echo '</ul></div></div>';
					}
			} 
			
			if ( is_sidebar_active('single_widget_area') ) : dynamic_sidebar('single_widget_area'); endif; ?>
			
		</div><!-- padding -->
	</div><!-- single_sidebar -->
	
<?php	
// sidebar for pages 	
} elseif (is_page()){ ?>
	<div class="sidebar page_sidebar noprint">
		<div class="padding">
			
			<?php
			if($post->post_parent) {
				$children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0"); 
			} else {
				$children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
			}
			if ($children) { ?>
				
				<div class="widget widget_subpages">
					<div class="widgetPadding">
						<h3 class="widget-title"><?php _e('In this section:','smashingMultiMedia');?></h3>
						<ul><?php echo $children; ?></ul>
					</div><!-- widgetPadding -->
				</div><!-- widget -->
				
			<?php } 
			 	
			if ( is_sidebar_active('page_widget_area') ) : dynamic_sidebar('page_widget_area'); endif;?>
			
		</div><!-- padding -->
	</div><!-- page_sidebar -->	

<?php //in all other cases
} else { ?>
	
<?php } ?>