<ul class="psp-page-list">
<?php foreach ( $pspPageList as $page ): ?>
<li class="psp-page-list-item"><a href="<?php echo get_permalink($page->ID); ?>"><?php echo $page->post_title; ?></a></li>
<?php endforeach; ?>
</ul>
