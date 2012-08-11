<?php get_header(); ?>

<?php do_action( 'bp_before_blog_home' ) ?>

<?php get_sidebar('left'); 
global $bp_existed;?>

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

<?php while (have_posts()) : the_post(); $author_email = get_the_author_meta('email'); $the_post_ids = get_the_ID(); $the_post_title = get_the_title(); ?>

<?php do_action( 'bp_before_blog_post' ) ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<div class="post-meta">

<div class="post-avatar">
<?php if(function_exists("bp_post_author_avatar")) : ?>
<?php bp_post_author_avatar(); ?>
<?php else: ?>
<?php echo get_avatar($author_email,'32'); ?>
<?php endif; ?>
</div>

<div class="post-info">
<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('permalink to', TEMPLATE_DOMAIN); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h1>
<?php $post_meta_status = get_option('tn_buddysocial_post_meta_status'); if($post_meta_status != 'disable') { ?>
<p><?php the_time( 'j F, Y', TEMPLATE_DOMAIN ); ?>&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit', TEMPLATE_DOMAIN), '', ''); ?><br /><?php _e('Published by', TEMPLATE_DOMAIN); ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( '%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?>
<?php _e('by',TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?><?php } ?> <?php _e('in', TEMPLATE_DOMAIN); ?> <?php the_category(', ') ?></p>
<?php } ?>
</div>
</div>


<div class="post-content">
<?php
$post_style = get_option('tn_buddysocial_blog_post_style');
$post_meta_status = get_option('tn_buddysocial_post_meta_status');
if($post_style == '' || $post_style == 'full post') { ?>
<?php the_content( __('Read more &raquo;', TEMPLATE_DOMAIN) ); ?>
<?php } elseif($post_style == 'excerpt post') { ?>
<?php echo custom_the_content(70); ?>
<?php } elseif($post_style == 'featured thumbnail with excerpt post') { ?>
<?php wp_custom_post_thumbnail($the_post_id=get_the_ID(), $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='medium', $fetch_w='200', $fetch_h='200', $alt_class='alignleft feat-thumb'); ?>
<?php echo the_excerpt(); ?>
<?php } ?>
<?php $facebook_like_status = get_option('tn_buddysocial_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<div style="margin-top: 10px;">
<div class="fb-like" data-href="<?php echo urlencode(get_permalink($post->ID)); ?>" data-send="false" data-layout="standard" data-width="450" data-show-faces="false" data-font="arial"></div>
</div>
<?php } ?>
</div>

<div class="post-tag"><?php if(has_tag()) { ?><span class="tags"><?php the_tags(__('tags:&nbsp;', TEMPLATE_DOMAIN), ', ', ''); ?></span><?php } ?><?php if ( comments_open() ) { ?><span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', TEMPLATE_DOMAIN ), __( '1 Comment &#187;', TEMPLATE_DOMAIN ), __( '% Comments &#187;', TEMPLATE_DOMAIN ) ); ?></span><?php } ?></div>

</div>

<?php do_action( 'bp_after_blog_post' ) ?>

<?php endwhile; ?>

<?php if ( comments_open() ) { ?><?php comments_template('', true); ?><?php } ?>

<?php locate_template ( array('lib/templates/wp-template/paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>


</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>