<?php get_header(); ?>

<?php do_action( 'bp_before_blog_home' ) ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php locate_template ( array('lib/templates/wp-template/headline.php'), true ); ?>

<?php if( is_author() ) { ?>
<?php if(isset($_GET['author_name'])) : $curauth = get_userdatabylogin($author_name); else : $curauth = get_userdata(intval($author)); endif; ?>
<div id="author-bio" class="post-content">
<div class="avatar alignleft"><?php echo get_avatar($curauth->user_email, '128', $avatar); ?></div>
<div id="author-profile">
<h1><?php echo $curauth->display_name; ?></h1>
<p>
<?php if($curauth->user_description<>''): echo $curauth->user_description; else: _e("This user hasn't shared any biographical information", TEMPLATE_DOMAIN); endif; ?>
</p>
<?php
if(($curauth->user_url<>'http://') && ($curauth->user_url<>'')) echo '<p class="im">'.__('Homepage:',TEMPLATE_DOMAIN).' <a href="'.$curauth->user_url.'">'.$curauth->user_url.'</a></p>';
if($curauth->yim<>'') echo '<p class="im">'.__('Yahoo Messenger:',TEMPLATE_DOMAIN).' <a class="im_yahoo" href="ymsgr:sendIM?'.$curauth->yim.'">'.$curauth->yim.'</a></p>';
if($curauth->jabber<>'') echo '<p class="im">'.__('Jabber/GTalk:',TEMPLATE_DOMAIN).' <a class="im_jabber" href="gtalk:chat?jid='.$curauth->jabber.'">'.$curauth->jabber.'</a></p>';
if($curauth->aim<>'') echo '<p class="im">'.__('AIM:',TEMPLATE_DOMAIN).' <a class="im_aim" href="aim:goIM?screenname='.$curauth->aim.'">'.$curauth->aim.'</a></p>';
?>
</div>
</div>
<?php } ?>

<?php while (have_posts()) : the_post(); ?>

<?php do_action( 'bp_before_blog_post' ) ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

<?php $post_meta_status = get_option('tn_buddyfun_post_meta_status'); if($post_meta_status != 'disable') { ?>
<div class="post-author"><?php the_time('jS F Y') ?> <?php _e('by', TEMPLATE_DOMAIN) ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( '%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?> <?php the_author_posts_link(); ?><?php } ?> <?php _e('under', TEMPLATE_DOMAIN) ?> <?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit', TEMPLATE_DOMAIN)); ?></div>
<?php } ?>


<div class="post-content">
<?php
$post_style = get_option('tn_buddyfun_blog_post_style');
$post_meta_status = get_option('tn_buddyfun_post_meta_status');
if($post_style == '' || $post_style == 'full post') { ?>
<?php the_content(__('...click here to read more', TEMPLATE_DOMAIN)); ?>
<?php } elseif($post_style == 'excerpt post') { ?>
<?php echo custom_the_content(70); ?>
<?php } elseif($post_style == 'featured thumbnail with excerpt post') { ?>
<?php wp_custom_post_thumbnail($the_post_id=get_the_ID(), $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='medium', $fetch_w='200', $fetch_h='200', $alt_class='alignleft feat-thumb'); ?>
<?php echo the_excerpt(); ?>
<?php } ?>

<?php $facebook_like_status = get_option('tn_buddyfun_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="margin-top: 10px; border:none; overflow:hidden; width:450px; height:30px"></iframe>
<?php } ?>
</div>


<?php $post_meta_status = get_option('tn_buddyfun_post_meta_status'); if($post_meta_status != 'disable') { ?>
<div class="post-tagged">
<?php if(has_tag()) { ?>
<p class="tags">
<?php if(function_exists("the_tags")) : ?>
<?php the_tags(__('tags:&nbsp;', TEMPLATE_DOMAIN), ', ', ''); ?>
<?php endif; ?>
</p>
<?php } ?>
<?php if ( comments_open() ) { ?>
<p class="com"><?php comments_popup_link(__('Leave Comments &rarr;', TEMPLATE_DOMAIN), __('One Comment &rarr;', TEMPLATE_DOMAIN), __('% Comments &rarr;', TEMPLATE_DOMAIN)); ?></p>
<?php } ?>
</div>
<?php } ?>

</div>

<?php do_action( 'bp_after_blog_post' ) ?>

<?php endwhile; ?>

<?php if ( comments_open() ) { ?><?php comments_template('', true); ?><?php } ?>


<?php locate_template ( array('lib/templates/wp-template/paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php do_action( 'bp_after_blog_home' ) ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>