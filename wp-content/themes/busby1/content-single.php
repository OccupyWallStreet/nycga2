<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<div class="post">
<header>
<h2 class="posttitle"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'toolbox' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
</header>

<div class="postdate"><p><span class="postdateno"><?php the_time('j') ?></span><br /><?php the_time('F') ?></p></div>
<div class="postcontent">
<?php the_content(); ?>
<?php wp_link_pages(); ?>
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

<div class="singlemeta"><p>Category: <?php the_category(', ') ?> <?php the_tags('Tags : ',' '); ?></p></div>

</article><!-- #post-<?php the_ID(); ?> -->
