<?php get_header(); ?>
<div id="mid" class="fix">
<div id="single3" class="fix"><a name="main"></a>
<div id="content">

<div class="activity no-ajax" role="main">
	<?php if ( bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action() ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">
		<?php while ( bp_activities() ) : bp_the_activity(); ?>

			<?php locate_template( array( 'activity/entry.php' ), true ) ?>

		<?php endwhile; ?>
		</ul>

	<?php endif; ?>
	
</div>

</div>
</div>
<?php get_template_part('bar'); ?>
</div>
<?php get_footer(); ?>