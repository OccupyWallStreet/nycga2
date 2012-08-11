<?php if (is_single()) { ?>
<div id="post-navigator-single">
<div class="alignleft"><?php previous_post_link('&laquo;%link') ?></div>
<div class="alignright"><?php next_post_link('%link&raquo;') ?></div>
</div>

<?php } else if (is_page()) { ?>

<div id="post-navigator">
<?php wp_link_pages('<strong>Page</strong> ', '', 'number'); ?>
</div>

<?php } else if (is_tag()) { ?>

<div id="post-navigator">
<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Entries', 'bp-community' ) ) ?></div>
<div class="alignright"><?php previous_posts_link( __( 'Next Entries &raquo;', 'bp-community') ) ?></div>
</div>

<?php } else if ((is_category()) || (is_archive())) { ?>

<div id="post-navigator">
<?php if (function_exists('custom_wp_pagenavi')) : ?>
<?php custom_wp_pagenavi(); ?>
<?php else : ?>
<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Entries', 'bp-community') ) ?></div>
<div class="alignright"><?php previous_posts_link( __( 'Next Entries &raquo;', 'bp-community') ) ?></div>
<?php endif; ?>
</div>

<?php } else { ?>
<div id="post-navigator">
<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Entries', 'bp-community') ) ?></div>
<div class="alignright"><?php previous_posts_link( __( 'Next Entries &raquo;', 'bp-community') ) ?></div>
</div>
<?php } ?>
