<?php get_header(); ?>
<div id="main" class="group">
	<section id="posts">
	    <article class="post group">
			<h2><?php _e( 'Not Found', CI_DOMAIN ); ?></h2>
			<p><?php _e( 'Oh, no! The page you requested could not be found. Perhaps searching will help...', CI_DOMAIN ); ?></p>
			<?php get_search_form(); ?>
			<script type="text/javascript">
				// focus on search field after it has loaded
				document.getElementById('s') && document.getElementById('s').focus();
			</script>
			<p></p>
	    </article>
	</section><!-- #posts -->

    <aside id="sidebar">
		<?php dynamic_sidebar('sidebar-right'); ?>
    </aside><!-- #sidebar -->
</div><!-- #main -->


<?php get_footer(); ?>
