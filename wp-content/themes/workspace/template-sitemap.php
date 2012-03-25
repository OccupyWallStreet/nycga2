<?php
/*
Template Name: Sitemap
*/
?>
<?php get_header(); ?>

    <h1 class="page-title"><?php the_title(); ?></h1>

    <div id="content">

    <div class="entry-content">

        <?php if ( have_posts() ) { the_post(); ?>
          <?php the_content(); ?>
    <?php } ?>

      <!--Pages-->
      <div class="left" style="width:50%">  
        <h3><?php _e( 'Pages', 'themejunkie' ); ?>:</h3>
        <ul>
            <?php wp_list_pages( 'depth=0&sort_column=menu_order&title_li=' ); ?>
        </ul>
        </div>

        <!--Categories-->
        <div class="left" style="width:50%">  
        <h3><?php _e('Categories', 'themejunkie'); ?>:</h3>
        <ul>
            <?php wp_list_categories('title_li=&show_count=true'); ?>
        </ul>
        </div>
        
        <div class="clear"></div>

        <!--Posts per category-->
        <h3><?php _e( 'Posts per category', 'themejunkie' ); ?>:</h3>
        <?php

        $cats = get_categories();

      foreach ( $cats as $cat ) {

          query_posts( 'cat=' . $cat->cat_ID );

    ?>
            <h3><?php echo $cat->cat_name; ?></h3>

            <ul>
                <?php while ( have_posts() ) { the_post(); ?>
              <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> - <?php _e( 'Comments', 'themejunkie' ); ?> (<?php echo $post->comment_count; ?>)</li>
              <?php }  ?>
            </ul>

            <?php } ?>
    </div> <!-- .entry-content -->

    
    </div> <!-- #content -->


    <?php get_sidebar(); ?>

<?php get_footer(); ?>