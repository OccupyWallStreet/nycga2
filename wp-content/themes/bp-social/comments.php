<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die (__('Please do not load this page directly. Thanks!',TEMPLATE_DOMAIN));

if ( post_password_required() ) { ?>
<h2 id="headline"><?php _e('This post is password protected. Enter the password to view comments.',TEMPLATE_DOMAIN); ?></h2>
<?php
return;
}
?>

<?php do_action('wp_comments_begin'); ?>

<div id="wp-comments">
<?php if ( have_comments() ) : ?><?php if ( ! empty($comments_by_type['comment']) ) : ?>

<h4><?php comments_number(__('No Comments',TEMPLATE_DOMAIN), __('1 Comment',TEMPLATE_DOMAIN), __('% Comments',TEMPLATE_DOMAIN)); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if (!is_user_logged_in() ) { ?><span><a href="<?php echo site_url(); ?>/wp-login.php"><?php _e('Login and add yours too!',TEMPLATE_DOMAIN); ?></a></span><?php } ?>
<span class="alignright"><?php paginate_comments_links(); ?></span>
</h4>


<ol id="comments" class="commentlist">
<?php wp_list_comments('type=comment&callback=list_comments'); ?>
</ol>


<?php endif; ?>

<?php if ( ! empty($comments_by_type['pings']) ) : ?>
<h4 id="ping"><?php _e('Trackback and Pingback',TEMPLATE_DOMAIN); ?></h4>

<ul class="pinglist">
<?php wp_list_comments('type=pings&callback=list_pings'); ?>
</ul>

<?php endif; ?>


<?php else : // this is displayed if there are no comments so far ?>
<?php if ('open' == $post->comment_status) : ?>
 <!-- If comments are open, but there are no comments. -->
<?php else : // comments are closed ?>
<!-- If comments are closed. -->
<h2 id="headline"><?php _e('Sorry, the comment form is closed at this time.',TEMPLATE_DOMAIN); ?></h2>
<?php endif; ?>
<?php endif; ?>

<?php if ('open' == $post->comment_status) : ?>

<div id="respond">


<h4><?php comment_form_title(__('Leave a Reply',TEMPLATE_DOMAIN), __('Leave a Reply to %s',TEMPLATE_DOMAIN) ); ?></h4>


<div class="cancel-comment-reply">
<?php cancel_comment_reply_link(); ?>
</div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>

<h5><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.',TEMPLATE_DOMAIN), get_site_url() ."/wp-login.php?redirect_to=".urlencode(get_permalink()));?><h5>

<?php else : ?>

<?php if(USE_NEW_COMMENT_FORM == 'yes') { ?>
<?php comment_form(); ?>
<?php } else { ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<label><?php printf(__('Logged in as %s.',TEMPLATE_DOMAIN), '<a href="'.get_site_url().'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url( get_site_url() ); ?>"><?php _e('Log out &raquo;',TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo site_url(); ?>/wp-login.php?action=logout" title="<?php __('Log out of this account',TEMPLATE_DOMAIN); ?>"><?php _e('Log out &raquo;',TEMPLATE_DOMAIN); ?></a> <?php } ?></label>


<?php else : ?>

<div class="labelbox">
<label for="author"><small><?php _e('Name',TEMPLATE_DOMAIN); ?> <?php if ($req) _e('(required)',TEMPLATE_DOMAIN); ?></small></label>
<p><input type="text" class="cfield" name="author" id="author" value="<?php echo $comment_author; ?>" <?php if ($req) echo "aria-required='true'"; ?> /></p>


<label for="email"><small><?php _e('Mail (will not be published)',TEMPLATE_DOMAIN);?> <?php if ($req) _e('(required)',TEMPLATE_DOMAIN); ?></small></label>
<p><input type="text" class="cfield" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> /></p>


<label for="url"><small><?php _e('Website',TEMPLATE_DOMAIN); ?></small></label>
<p><input type="text" class="cfield" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" /></p>
</div>

<?php endif; ?>

<div class="labelcom">
<p><textarea name="comment" id="comment" cols="50%" rows="8" class="carea"></textarea></p>

<p><input name="submit" type="submit" class="st" value="<?php echo esc_attr(__('Submit Comment',TEMPLATE_DOMAIN)); ?>" id="submit-comment" alt="submit" />

<?php if(function_exists("comment_id_fields")) { ?>
<?php comment_id_fields(); ?>
<?php } ?>
</p>

<p class="full"><?php _e('you can also leave a',TEMPLATE_DOMAIN); ?> <a href="<?php trackback_url() ?>" rel="trackback"><?php _e('trackback',TEMPLATE_DOMAIN); ?></a> <?php _e('to this post',TEMPLATE_DOMAIN); ?></p>

<?php do_action('comment_form', $post->ID); ?>
</div>

</form>
<?php } ?>

<?php endif; // If registration required and not logged in ?>

</div>

<?php endif; // if you delete this the sky will fall on your head ?>

</div>

<?php do_action('wp_comments_end'); ?>