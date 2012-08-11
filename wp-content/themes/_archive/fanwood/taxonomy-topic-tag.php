<?php

/**
 * Topic Tag
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

		<div class="hfeed">
			
			<?php bbp_breadcrumb( array( 'before' => '<div class="breadcrumb">', 'after' => '</div>', 'sep' => '<span class="sep">&raquo</span>' ) ); ?>
			
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

			<?php do_action( 'bbp_template_notices' ); ?>

			<div id="topic-tag" class="bbp-topic-tag">
				
				<div class="loop-meta">
				
					<h1 class="loop-title"><?php printf( __( 'Topic Tag: %s', 'bbpress' ), '<span>' . bbp_get_topic_tag_name() . '</span>' ); ?></h1>
					
					<div class="loop-description"><?php bbp_topic_tag_description(); ?></div>

				</div>

				<?php do_action( 'bbp_template_before_topic_tag' ); ?>

				<?php if ( bbp_has_topics() ) : ?>

					<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>

					<?php bbp_get_template_part( 'bbpress/loop',       'topics'    ); ?>

					<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>

				<?php else : ?>

					<?php bbp_get_template_part( 'bbpress/feedback',   'no-topics' ); ?>

				<?php endif; ?>

				<?php do_action( 'bbp_template_after_topic_tag' ); ?>
				
			</div>
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
