<?php 

global $bp;

if( have_posts() ) : ?>
	<div class = 'pagination'>
		<div class = 'pag_count' id = '<?php echo $bp->current_component; ?>-dir-count'>
		</div>
		<div class = 'pagination_links' id = '<?php echo $bp->current_component; ?>-dir-page'>
		</div>
	</div>

	<ul id = '<?php echo $bp->current_component; ?>-list' class = 'item-list'>
	<?php while( have_posts() ) : the_post(); ?>
		<li>
			<div class = 'item-avatar'>
				<a href = '<?php the_permalink(); ?>'><?php bpcp_type_avatar( Array( 'width'=>50, 'height'=>50, 'type'=>'thumbnail' ) ); ?></a>
			</div>

			<div class = 'item'>
				<div class = 'item-title'><a href = '<?php the_permalink(); ?>'><?php the_title(); ?></a></div>
				<div class = 'item-meta'>
					<span class = 'activity'><?php bpcp_last_active(); ?></span>
				<?php 
					do_action( 'bpcp_loop_meta', $bp->active_components[ $bp->current_component ] ); 
					do_action( 'bpcp_' . $bp->active_components[ $bp->current_component ] . '_loop_meta' ); 
				?>
				</div>
				<div class = 'item-desc'><?php the_excerpt(); ?></div>
			</div>

			<div class = 'action'>
				<div class = 'meta'>
				<?php 
					do_action( 'bpcp_loop_action_meta', $bp->active_components[ $bp->current_component ] ); 
					do_action( 'bpcp_' . $bp->active_components[ $bp->current_component ] . '_loop_action_meta' ); 
				?>
				</div>
			</div>

			<div class = 'clear'></div>
		</li>
	<?php endwhile; ?>
	</ul>
<?php endif;
