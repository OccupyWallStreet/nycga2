<?php
/**
 * @package WordPress
 * @subpackage Yoko
 */

get_header(); ?>

<div id="wrap">
<div id="main">

	<div id="content">
		<article id="page">
			<header class="page-entry-header">
				<h1 class="entry-title"><?php _e( 'Not Found', 'yoko' ); ?></h1>
			</header><!-- end page-entry-header -->
		
			<div class="single-entry-content">
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'yoko' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		
			<script type="text/javascript">
				// focus on search field after it has loaded
				document.getElementById('s') && document.getElementById('s').focus();
			</script>
		</article>
	</div><!-- end content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>