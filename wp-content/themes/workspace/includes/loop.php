<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="left">
		<a href="<?php the_permalink(); ?>" rel="bookmark" class="fadeThis">
		    <?php the_post_thumbnail('blog-thumb', array('class' => 'entry-thumb')); ?>
		    <div class="overlay">
		        <span class="icon"></span>
		    </div>
		</a>
		<div class="image-shadow-bottom"></div>	
	</div><!-- .left -->
	<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
	<div class="entry-meta">
		<span class="entry-author"><?php the_author_posts_link(); ?></span>
		<span class="entry-date"><?php the_time('M jS, Y') ?></span> 
		<span class="entry-comment"><?php comments_popup_link( __( '0 Comment', 'themejunkie' ), __( '1 Comment', 'themejunkie' ), __( '% Comments', 'themejunkie' ) ); ?></span>
	</div><!-- .entry-meta -->
	<div class="entry">
		<?php tj_content_limit( '200' ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'themejunkie' ), 'after' => '</div>' ) ); ?>	    	
		<div class="clear"></div>
	</div><!-- end. entry -->
	<div class="clear"></div>
</div><!-- #post-<?php the_ID(); ?> -->	