<?php get_header();?>

<div id="content" class="span8">
	<div class="padder">
		<?php do_action("advance-search");//this is the only line you need ?>
		<!-- let the search put the content here -->		                   
    </div> <!-- Contents ends here... --> 
</div><!-- Container ends here... -->

<?php get_footer();?>