<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<div class="post">

<div class="postcontent">
<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'toolbox' ) ); ?>

</div>

<div class="postdetails">
<p class="postedby"><?php
				printf( __( '<span class="sep">Posted on </span><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s" pubdate>%3$s</time></a> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%4$s" title="%5$s">%6$s</a></span>', 'toolbox' ),
					get_permalink(),
					get_the_date( 'c' ),
					get_the_date(),
					get_author_posts_url( get_the_author_meta( 'ID' ) ),
					sprintf( esc_attr__( 'View all posts by %s', 'toolbox' ), get_the_author() ),
					get_the_author()
				);
			?></p><p class="postcomments"><?php comments_popup_link( __( 'Leave a comment', 'toolbox' ), __( '1 Comment', 'toolbox' ), __( '% Comments', 'toolbox' ) ); ?></div>
</div>

</article><!-- #post-<?php the_ID(); ?> -->