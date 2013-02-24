				<div id="sidebar2" class="sidebar four columns" role="complementary">

					<div class="panel">
				
						<?php if ( is_active_sidebar( 'sidebar2' ) ) : ?>

							<?php dynamic_sidebar( 'sidebar2' ); ?>

						<?php else : ?>

							<!-- This content shows up if there are no widgets defined in the backend. -->
							
							<div class="alert-box">Please activate some Widgets.</div>

						<?php endif; ?>

					</div>

				</div>