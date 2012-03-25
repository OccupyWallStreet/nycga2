<div id="post-<?php the_ID(); ?>" class="entry-box">
    <?php $embed = get_post_meta(get_the_ID(), 'tj_video_embed', TRUE); ?>
    <?php if($embed && (get_option('videoplus_fancybox_enable') == 'on')){
        $embed_url = '';
        $mode = '~http://[\d\w\s:/.-]*~iS';
        preg_match($mode,$embed,$embed_url);
    ?>
    <a href="<?php echo $embed_url[0];?>" rel="bookmark" class="various fancybox.iframe">
    <?php } else { ?>
        <a href="<?php the_permalink(); ?>" rel="bookmark">
    <?php } ?>
        <?php if(has_post_thumbnail()){?>
        	<?php the_post_thumbnail('entry-thumb', array('class' => 'entry-thumb')); ?>
        <?php } else { ?>
        <?php $img_url = get_post_meta(get_the_ID(), 'tj_video_img_url', TRUE); ?>
            <?php if($img_url != null) { ?><img src="<?php echo $img_url; ?>" alt="<?php the_title(); ?>" class="entry-thumb"/><?php } ?>
        <?php } ?>

        <?php if($embed) { ?>
        	<div class="video-flag"></div>
        <?php }?>
    </a>
    <div class="entry-meta">
  		<?php the_time('M j, Y'); ?> &middot; by <?php the_author_posts_link(); ?>
        <span class="entry-comment">
            <?php comments_popup_link( __( '0', 'theme junkie' ), __( '1', 'theme junkie' ), __( '%', 'theme junkie' ) ); ?>
        </span>
    </div><!-- .entry-meta -->
	<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
    <div class="entry-content">
        <p><?php the_excerpt(); ?></p>
	</div><!-- .entry-content -->
</div><!-- #post-<?php the_ID(); ?> .entry-box -->