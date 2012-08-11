		<div class="navigation"><!-- start .navigation -->
				<?php if ( has_nav_menu( 'exhibitions' ) ) { ?>
						<?php wp_nav_menu( array('theme_location' => 'exhibitions', 'menu_class' => 'nav', 'container' => '', )); ?>			
				<?php } else {?>
					<ul class="nav">
				<?php
				$args = array( 'post_type' => 'exhibition');
				$loop = new WP_Query( $args );
				while ( $loop->have_posts() ) : $loop->the_post();
					echo '<li><a href="';
					the_permalink();
					echo '" title="';
					the_title_attribute();
					echo '">';
					the_title();
					echo '</li></a>';
				endwhile;
				?>
					</ul>
				<?php } ?>			
				<div class="clear"></div>
		</div><!-- end .navigation -->		
		<div class="clear"></div>