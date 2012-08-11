<?php 
	$tabone_title = get_option('dev_buddydaily_tabone_title');
	$tabtwo_title = get_option('dev_buddydaily_tabtwo_title');
	$tabthree_title = get_option('dev_buddydaily_tabthree_title');
	$tabcat_one = get_option('dev_buddydaily_tabcat_one');
	$tabcat_two = get_option('dev_buddydaily_tabcat_two');
	$tabcat_three = get_option('dev_buddydaily_tabcat_three');
	$tab_image_display = get_option('dev_buddydaily_featuretabs_image_size');
?>
<script type="text/javascript">
   jQuery(document).ready(function() {
	   jQuery.noConflict();
		  	jQuery(document).ready(function($){
                    var tabContainers = $('div.tabs > div');
                    tabContainers.hide().filter(':first').show();

                    $('div.tabs ul.tabNavigation a').click(function () {
                            tabContainers.hide();
                            tabContainers.filter(this.hash).show();
                            $('div.tabs ul.tabNavigation a').removeClass('selected');
                            $(this).addClass('selected');
                            return false;
                    }).filter(':first').click();
            });	
   });
</script>
		<div id="content-tabs">
		 <div class="tabs">
		        <ul class="tabNavigation">
		            <li><a href="#first"><?php echo stripslashes($tabone_title); ?></a></li>
		            <li><a href="#second"><?php echo stripslashes($tabtwo_title); ?></a></li>
		            <li><a href="#third"><?php echo stripslashes($tabthree_title); ?></a></li>
		        </ul>
					<div class="clear"></div>
						<?php query_posts('category_name='. $tabcat_one . '&posts_per_page=1'); ?>
										  <?php while (have_posts()) : the_post(); ?>
		        <div id="first">
		            <h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>	
		           			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image alignleft" style="height:200px;width:300px;display:block;background:url('<?php the_post_image_url($tab_image_display); ?>') center center no-repeat">&nbsp;</span></a>
			<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
										<?php the_excerpt(); ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
							<?php endwhile; ?>
				<div class="clear"></div>
		        </div>
			  <div id="second">
						<?php query_posts('category_name='. $tabcat_two . '&posts_per_page=1'); ?>
										  <?php while (have_posts()) : the_post(); ?>
		      
					 <h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>	
			           			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image alignleft" style="height:200px;width:300px;display:block;background:url('<?php the_post_image_url($tab_image_display); ?>') center center no-repeat">&nbsp;</span></a>
				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
											<?php the_excerpt(); ?>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
								<?php endwhile; ?>
						<div class="clear"></div>
		        </div>
		      <div id="third">
					<?php query_posts('category_name='. $tabcat_three . '&posts_per_page=1'); ?>
									  <?php while (have_posts()) : the_post(); ?>
		  
		        	 <h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>	
			           			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image alignleft" style="height:200px;width:300px;display:block;background:url('<?php the_post_image_url($tab_image_display); ?>') center center no-repeat">&nbsp;</span></a>
				<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
											<?php the_excerpt(); ?>
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
								<?php endwhile; ?>
						<div class="clear"></div>
		        </div>
		    </div>
			<div class="clear"></div>
		</div>