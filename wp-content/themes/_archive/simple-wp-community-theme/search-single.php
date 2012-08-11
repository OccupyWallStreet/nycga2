<?php get_header();?>

	<div id="content">
		<div class="padder">
			<?php do_action("advance-search");//this is the only line you need?>
			<!-- let the search put the content here -->		                   
    </div> <!-- Contents ends here... --> 
 </div><!-- Container ends here... -->
	<?php locate_template( array( 'sidebar.php' ), true ) ?>        
  <?php get_footer();?>