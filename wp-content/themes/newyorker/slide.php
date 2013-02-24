<div id="mow">

<ul class="ca-menu">

                    <?php 
	$slidecat = get_option('NewYorker_slicer_category'); 
	$my_query = new WP_Query('showposts=1&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
                    <li class="one">
                        <a href="<?php the_permalink() ?>">
                           
                            <div class="ca-content">
                                <h2 class="ca-main"><?php the_title() ?></h2>
                                <h3 class="ca-sub"><?php the_title() ?></h3>
                            </div>
                             <span class="ca-icon"><?php
  $posttags = get_the_tags();
  if ($posttags) {
    foreach($posttags as $tag) {
      echo $tag->name . ' '; 
    }
  }
?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                    
                    <?php 
	$slidecat = get_option('NewYorker_slicer_category'); 
	$my_query = new WP_Query('showposts=1&offset=1');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
                    <li class="two">
                        <a href="<?php the_permalink() ?>">
                            
                            <div class="ca-content">
                                <h2 class="ca-main"><?php the_title() ?></h2>
                                <h3 class="ca-sub"><?php the_title() ?></h3>
                            </div>
                            <span class="ca-icon"><?php
  $posttags = get_the_tags();
  if ($posttags) {
    foreach($posttags as $tag) {
      echo $tag->name . ' '; 
    }
  }
?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                    
                    <?php 
	$slidecat = get_option('NewYorker_slicer_category'); 
	$my_query = new WP_Query('showposts=1&offset=2');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
                    <li class="three">
                        <a href="<?php the_permalink() ?>">
                            
                            <div class="ca-content">
                                <h2 class="ca-main"><?php the_title() ?></h2>
                                <h3 class="ca-sub"><?php the_title() ?></h3>
                            </div>
                            <span class="ca-icon"><?php
  $posttags = get_the_tags();
  if ($posttags) {
    foreach($posttags as $tag) {
      echo $tag->name . ' '; 
    }
  }
?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                    
                    <?php 
	$slidecat = get_option('NewYorker_slicer_category'); 
	$my_query = new WP_Query('showposts=1&offset=3');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
                    <li class="four">
                        <a href="<?php the_permalink() ?>">
                            
                            <div class="ca-content">
                                <h2 class="ca-main"><?php the_title() ?></h2>
                                <h3 class="ca-sub"><?php the_title() ?></h3>
                            </div>
                            <span class="ca-icon"><?php
  $posttags = get_the_tags();
  if ($posttags) {
    foreach($posttags as $tag) {
      echo $tag->name . ' '; 
    }
  }
?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                    
</ul>

</div>