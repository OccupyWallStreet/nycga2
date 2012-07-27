<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ( __('Please do not load this page directly. Thanks!', TEMPLATE_DOMAIN) );
if ( post_password_required() ) { ?>
<h2 id="post-header"><?php _e('This post is password protected. Enter the password to view comments.', TEMPLATE_DOMAIN); ?></h2>
<?php
return;
}
?>

<!-- You can start editing here. -->
<div id="commentpost">

<?php if ( have_comments() ) : ?>
<?php if ( ! empty($comments_by_type['comment']) ) : ?>
<h4 id="comments"><?php comments_number(__('No Responses', TEMPLATE_DOMAIN), __('One Response', TEMPLATE_DOMAIN), __('% Responses', TEMPLATE_DOMAIN) );?> to &#8220;<?php the_title(); ?>&#8221;</h4>


<div id="post-navigator">
<div class="alignleft"><?php previous_comments_link() ?></div>
<div class="alignright"><?php next_comments_link() ?></div>
</div>

<ol class="commentlist">
<?php wp_list_comments('type=comment'); ?>
</ol>


<div id="post-navigator">
<div class="alignleft"><?php previous_comments_link() ?></div>
<div class="alignright"><?php next_comments_link() ?></div>
</div>


<?php endif; ?>

<?php if ( ! empty($comments_by_type['pings']) ) : ?>
<h4><?php _e('Trackbacks/Pingbacks', TEMPLATE_DOMAIN); ?></h4>
<ol class="pinglist">
<?php wp_list_comments('type=pings&callback=list_pings'); ?>
</ol>
<?php endif; ?>


 <?php else : // this is displayed if there are no comments so far ?>

 <?php if ('open' == $post->comment_status) : ?>
 <!-- If comments are open, but there are no comments. -->

<?php else : // comments are closed ?>
<!-- If comments are closed. -->
<h2 id="post-header"><?php _e('Comments are closed.', TEMPLATE_DOMAIN); ?></h2>

<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<div id="respond">

<h4><?php comment_form_title( __('Leave a Reply', TEMPLATE_DOMAIN), __('Leave a Reply to %s', TEMPLATE_DOMAIN) ); ?></h4>

<div class="cancel-comment-reply">
<?php cancel_comment_reply_link(); ?>
</div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.',TEMPLATE_DOMAIN), get_option('siteurl')."/wp-login.php?redirect_to=".urlencode(get_permalink()));?></p>
<?php else : ?>

<?php if(USE_NEW_COMMENT_FORM == 'yes') { ?>
<?php comment_form(); ?>
<?php } else { ?>
<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="cf">

<?php if ( $user_ID ) : ?>

<p>
<?php printf(__('Logged in as %s.',TEMPLATE_DOMAIN), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>"><?php _e('Log out &raquo;',TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php __('Log out of this account',TEMPLATE_DOMAIN); ?>"><?php _e('Log out &raquo;',TEMPLATE_DOMAIN); ?></a> <?php } ?>
</p>

<?php else : ?>

<p>
<label for="author"><small><?php _e('Name',TEMPLATE_DOMAIN); ?> <?php if ($req) echo "(required)"; ?></small></label><br />
<input type="text" class="tf" name="author" id="author" value="<?php echo $comment_author; ?>" <?php if ($req) echo "aria-required='true'"; ?> />
</p>

<p>
<label for="email"><small><?php _e('Mail (will not be published)',TEMPLATE_DOMAIN); ?><?php if ($req) echo "(required)"; ?></small></label><br />
<input type="text" class="tf" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
</p>

<p>
<label for="url"><small><?php _e('Website',TEMPLATE_DOMAIN); ?></small></label><br />
<input type="text" class="tf" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
</p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->

<p>
<textarea name="comment" id="comment" cols="50%" rows="8" class="af"></textarea>
</p>

<p>
<input name="submit" type="submit" class="st" value="<?php _e('submit',TEMPLATE_DOMAIN); ?>" id="submit" alt="submit" />
<?php comment_id_fields(); ?>
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>
<?php } ?>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>
</div>
