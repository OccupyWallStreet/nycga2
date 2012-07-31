<?php if (is_single()) { ?>
<div id="pagination">
<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
</div>

<?php } else if (is_page()) { ?>

<div id="pagination">
<?php link_pages('<strong>Pages:</strong> ', '', 'number'); ?>
</div>

<?php } else { ?>

<div id="pagination">
<?php if (function_exists('wp_pagenavi')) : ?>
<?php wp_pagenavi(); ?>
<?php else : ?>
<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;'); ?></div>
<div class="alignleft"><?php next_posts_link('&laquo; Older Entries'); ?></div>
<?php endif; ?>
</div>

<?php } ?>