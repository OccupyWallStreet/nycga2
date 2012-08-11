<?php get_header();?>
<div id="main">
	<div id="content">
	        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <p >
                <?php the_time('M d Y'); ?>
            </p>
            <h2 class="title"><?php _e("404 - The Server can not find it !", "voidy"); ?></h2>
            <div class="entry">
              <p>			  
				<?php _e("The post or the page that you are looking for, is not available at this time. It could have been moved / deleted.", "voidy" ); ?>
			  </p>
              <p>
			  <?php _e("Please browse through the archives / search through the site.", "voidy") ; ?>
			  </p>
      			</div>
            <p class="comments">
              <?php _e("Posted as \"Not Found\"", "voidy") ; ?>
            </p>	          
	        </div>      
	</div>
  <?php get_sidebar();?>
  <?php get_footer();?>