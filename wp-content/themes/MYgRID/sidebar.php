<div id="sidebar">


<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Sidebar") ) : ?>

<h3>Recent posts</h3>
<ul>			
<?php wp_get_archives('title_li=&type=postbypost&limit=5'); ?>
</ul>

<?php endif; ?>		

</div>

