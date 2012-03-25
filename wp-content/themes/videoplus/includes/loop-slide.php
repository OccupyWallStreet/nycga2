<div class="entry-slides">
    <div class="entry-embed">
        <?php
            $embed = get_post_meta(get_the_ID(), 'tj_video_embed', TRUE);
            echo stripslashes(htmlspecialchars_decode($embed));
        ?>
    </div>
	    <h2 class="entry-title">
            <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
                <?php the_title(); ?>
            </a>
        </h2><!-- .entry-title -->
        <div class="entry-meta">
            <abbr title="<?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?>"><?php the_time('F j, Y'); ?></abbr> &middot; <?php _e('by','themejunkie'); ?> <?php the_author_posts_link(); ?>
        </div><!-- .entry-meta -->        
        <div class="entry-content">
            <p><?php the_excerpt(); ?></p>
            <a href="<?php the_permalink(); ?>" rel="bookmark" class="read-more">Continue Reading &raquo;</a>
	    </div><!-- .entry-content -->
</div><!-- #post-<?php the_ID(); ?> -->