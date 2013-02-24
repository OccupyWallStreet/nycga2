<?php
/* action functions */
function bp_wpmu_blogpageloop() {
		do_action('bp_wpmu_blogpageloop');
}

function wpmu_blogpageloop() {
		do_action('wpmu_blogpageloop');
}

function bp_wpmu_excerptloop() {
		do_action('bp_wpmu_excerptloop');
}

function wpmu_excerptloop() {
		do_action('wpmu_excerptloop');
}

function bp_wpmu_singleloop() {
		do_action('bp_wpmu_singleloop');
}

function wpmu_singleloop() {
		do_action('wpmu_singleloop');
}

function bp_wpmu_pageloop() {
		do_action('bp_wpmu_pageloop');
}

function wpmu_pageloop() {
		do_action('wpmu_pageloop');
}

function wpmu_attachmentloop() {
		do_action('wpmu_attachmentloop');
}

/* buddypress loop functions */

/* blog / news template function */
/* buddypress function */
function bp_wpmu_blogpage_loop(){
		rewind_posts();
		$page = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=&showposts=5&paged=$page"); while ( have_posts() ) : the_post();?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="author-box">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
				<p><?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></p>
			</div>
			<div class="post-content">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time('M j Y') ?> <em><?php _e( 'in', 'network' ) ?> <?php the_category(', ') ?> <?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></em></p>
				<div class="entry">
							<a href="<?php the_permalink() ?>">
								<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
								<?php the_post_thumbnail(); ?></div><?php } } ?>
							</a>
					<?php the_excerpt(); ?>
					<p><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php _e( 'Read more', 'network' ) ?></a></p>
					<div class="clear"></div>
				</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'network' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'network' ), __( '1 Comment &#187;', 'network' ), __( '% Comments &#187;', 'network' ) ); ?></span></p>
			</div>
		</div>
			<?php endwhile;
}
add_action('bp_wpmu_blogpageloop', 'bp_wpmu_blogpage_loop');

/* non buddypress loop functions */
function wpmu_blogpage_loop(){
		rewind_posts();
		$page = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=&showposts=5&paged=$page"); while ( have_posts() ) : the_post();?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-content-wp">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', 'network' ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></em></span></p>
						<div class="entry">
									<a href="<?php the_permalink() ?>">
										<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
										<?php the_post_thumbnail(); ?></div><?php } } ?>
									</a>
							<?php the_excerpt(); ?>
							<p><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php _e( 'Read more', 'network' ) ?></a></p>
							<div class="clear"></div>
						</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'network' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'network' ), __( '1 Comment &#187;', 'network' ), __( '% Comments &#187;', 'network' ) ); ?></span></p>
			</div>
		</div>
			<?php endwhile;
}
add_action('wpmu_blogpageloop', 'wpmu_blogpage_loop');

/* excerpt function */
/* buddypress function */
function bp_wpmu_excerpt_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="author-box">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
				<p><?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></p>
			</div>
			<div class="post-content">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php the_time('M j Y') ?> <em><?php _e( 'in', 'network' ) ?> <?php the_category(', ') ?> <?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></em></p>
				<div class="entry">
							<a href="<?php the_permalink() ?>">
								<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
								<?php the_post_thumbnail(); ?></div><?php } } ?>
							</a>
					<?php the_excerpt(); ?>		
					<p><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php _e( 'Read more', 'network' ) ?></a></p>
							<div class="clear"></div>
				</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'network' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'network' ), __( '1 Comment &#187;', 'network' ), __( '% Comments &#187;', 'network' ) ); ?></span></p>
			</div>
		</div>
			<?php endwhile;
}
add_action('bp_wpmu_excerptloop', 'bp_wpmu_excerpt_loop');

/* non buddypress loop functions */
function wpmu_excerpt_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-content-wp">
					<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', 'network' ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></em></span></p>
						<div class="entry">
									<a href="<?php the_permalink() ?>">
										<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
										<?php the_post_thumbnail(); ?></div><?php } } ?>
									</a>
							<?php the_excerpt(); ?>
							<p><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php _e( 'Read more', 'network' ) ?></a></p>
									<div class="clear"></div>
						</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'network' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'network' ), __( '1 Comment &#187;', 'network' ), __( '% Comments &#187;', 'network' ) ); ?></span></p>
			</div>
		</div>
			<?php endwhile;
}
add_action('wpmu_excerptloop', 'wpmu_excerpt_loop');

/* single function */
/* buddypress function */
function bp_wpmu_single_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="author-box">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
					<p><?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></p>
				</div>
				<div class="post-content">
					<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><?php the_time('M j Y') ?> <em><?php _e( 'in', 'network' ) ?> <?php the_category(', ') ?> <?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></em></p>
					<div class="entry">
						<?php the_content( __( 'Read the rest of this entry &rarr;', 'network' ) ); ?>
					</div>
					<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'network' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'network' ), __( '1 Comment &#187;', 'network' ), __( '% Comments &#187;', 'network' ) ); ?></span></p>
				</div>
			</div>
			
			<?php comments_template('', true); 
			?>
			<?php endwhile;
}
add_action('bp_wpmu_singleloop', 'bp_wpmu_single_loop');

/* non buddypress loop functions */
function wpmu_single_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="post-content-wp">
					<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'network' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', 'network' ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', 'network' ) ?><?php the_author_link();  ?></em></span></p>
					<div class="entry">
						<?php the_content(); ?>
					</div>
					<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'network' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'network' ), __( '1 Comment &#187;', 'network' ), __( '% Comments &#187;', 'network' ) ); ?></span></p>
				</div>
			</div>	
			
			<?php comments_template('', true); 
			?>
			<?php endwhile;
		
}
add_action('wpmu_singleloop', 'wpmu_single_loop');

/* single function */
/* buddypress function */
function bp_wpmu_page_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
			<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="entry">
					<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'network' ) ); ?>
					<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'network' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link( __( 'Edit this entry.', 'network' ), '<p>', '</p>'); ?>
				</div>
			</div>
			
			<?php comments_template('', true); ?>
			<?php endwhile;
}
add_action('bp_wpmu_pageloop', 'bp_wpmu_page_loop');

/* non buddypress loop functions */
function wpmu_page_loop(){
	rewind_posts();
	while (have_posts()) : the_post(); ?>
			<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="entry">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'network' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link( __( 'Edit this entry.', 'network' ), '<p>', '</p>'); ?>
				</div>
			</div>
			
			<?php comments_template('', true); ?>
		<?php endwhile;
}
add_action('wpmu_pageloop', 'wpmu_page_loop');

function wpmu_attachment_loop(){
		rewind_posts();
			while (have_posts()) : the_post(); 
	$attachment_link = get_the_attachment_link($post->ID, true, array(450, 800)); // This also populates the iconsize for the next line 
			$_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; // This lets us style narrow icons specially ?>

				<div class="post" id="post-<?php the_ID(); ?>">

					<h2 class="posttitle"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &rarr; <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>

					<div class="entry">
						<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename($post->guid); ?></p>

						<?php the_content( __('<p class="serif">Read the rest of this entry &rarr;</p>', 'network' ) ); ?>

						<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'network' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					</div>

				</div>
			<?php comments_template(); ?>
				<?php endwhile;
}
add_action('wpmu_attachmentloop', 'wpmu_attachment_loop');
?>