<div id="category-navigation">
	<ul class="sf-menu">
			<?php 
			wp_list_categories('orderby=id&show_count=0&title_li=');
			?>
	</ul>
	<div class="clear"></div>
</div>
<div class="page-navigation">
	
		<?php wp_nav_menu( array('menu' => 'mymenu', 'menu_class' => 'sf-menu', 'container' => '', )); ?>
			</ul>
			<div class="clear"></div>
	</div>
