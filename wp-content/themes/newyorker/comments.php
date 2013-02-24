<?php if ( comments_open() ) : ?>
<div class="comments" id="comments">
    <?php if ( post_password_required() ) : ?>
                    <p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'Detox'); ?></p>
</div>
    <?php
            return;
        endif;
    ?>

    <div id="comments">
    <?php if (have_comments()) : ?>
        <h3><?php printf(_n('1 comment', '%1$s comments', get_comments_number()), number_format_i18n( get_comments_number() ), '' ); ?></h3>
        <div class="comment_list">

            <!-- <div class="navigation">
                <div class="alignleft"><?php previous_comments_link() ?></div>
                <div class="alignright"><?php next_comments_link() ?></div>
            </div> -->

            <ol>
                <?php wp_list_comments(array('callback' => 'commentslist')); ?>
            </ol>

            <!-- <div class="navigation">
                <div class="alignleft"><?php previous_comments_link() ?></div>
                <div class="alignright"><?php next_comments_link() ?></div>
            </div> -->

        </div>
    <?php endif; // end have_comments() ?>
    </div>

    <?php if ('open' == $post->comment_status) : ?>

    <div id="respond">
        <h3><?php _e('What do you think?', 'Detox') ?></h3>
        <div class="comment_form">

        <?php if ( get_option('comment_registration') && !$user_ID ) : ?>
            <p class="comment_message"><?php _e('You must be', 'Detox') ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">
            <?php _e('logged in', 'Detox') ?></a> <?php _e('to post a comment.', 'Detox') ?></p>
        <?php else : ?>

            	<?php comment_form(); ?>

        <?php endif; // If registration required and not logged in ?>

        </div>

        <?php endif; // if you delete this the sky will fall on your head ?>

    </div>

<?php endif; // end ! comments_open() ?>