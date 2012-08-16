<?php if (is_category()) { ?>
<h2 id="post-header">
<?php _e("Sorry, we can't find the category you're looking for at this URL. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", TEMPLATE_DOMAIN); ?></h2>
<?php } else if (is_archive()) { ?>
<h2 id="post-header">
<?php _e("Sorry, we can't find the archive you're looking for at this URL. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", TEMPLATE_DOMAIN); ?></h2>
<?php } else if (is_search()) { ?>
<h2 id="post-header">
<?php _e("Sorry, we can't find the search keyword you're looking for. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", TEMPLATE_DOMAIN); ?>
</h2>
<?php } else if (is_author()) { ?>
<h2 id="post-header">
<?php _e("Sorry, we can't find the author you're looking for at this URL. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", TEMPLATE_DOMAIN); ?></h2>
<?php } else if (is_single()) { ?>
<h2 id="post-header">
<?php _e("Sorry, we can't find the post you're looking for at this URL. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", TEMPLATE_DOMAIN); ?></h2>
<?php } else if (is_home()) { ?>
<h2 id="post-header">
<?php _e("Sorry, we can't find the content you're looking for at this URL. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", TEMPLATE_DOMAIN); ?></h2>
<?php } else if (is_404()) { ?>
<h2 id="post-header">
<?php _e("Sorry, we can't find the content you're looking for at this URL. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", TEMPLATE_DOMAIN); ?></h2>
<?php } ?>