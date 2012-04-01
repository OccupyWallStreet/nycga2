<?php
/*
Template Name: Image Gallery
*/
?>
<?php get_header(); ?>

      <h1 class="page-title"><?php the_title(); ?></h1>

    <div id="content" class="one-col">

    <div class="entry-content">

        <ul id="imagegallery">


<?php

    $num = get_option('workspace_gallery_num');
    $count = 0;


    query_posts( array(
        'post_type' => 'post',
        'posts_per_page' => 999
    )
  );

?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



        <?php
            $array=array();
            $text = $post->post_content;
            $mode ="/(<img(.*)\/>)/";
            $text = preg_replace($mode,"",$text);
        ?>
        <?php
            $SoImages  = '~<img [^\>]*\ />~';
            preg_match_all($SoImages,$post->post_content,$Images);
            $PictureAmount=count($Images[0]); //get the img's number
            if($PictureAmount>0){
                for($i=0;$i<$PictureAmount;$i++){
                    $ImgUrl = $Images[0][$i];
                    $SoImgAddress="/\<img.*?src\=\"(.*?)\"[^>]*>/i";  //get the url
                    preg_match($SoImgAddress,$ImgUrl,$imagesurl);
                    $array[]=$imagesurl[1];
                }
            }
        ?>
        <?php
            $src = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), array( '9999','9999' ), false, '' );
        ?>


            <?php if(is_array($src)&&$num>0){?>

            <?php $count++; ?>

            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <a class="fadeThis" href="<?php the_permalink(); ?>" rel="bookmark" >
                    <?php the_post_thumbnail('home-thumb', array('class' => 'entry-thumb')); ?>
                    <div class="overlay">
                        <span class="icon"></span>
                    </div>
                </a>
                <div class="image-shadow-bottom"></div>

            </li>

            <?php }?>

            <?php if($count>=$num){break;}?>

            <?php endwhile; else: ?>
        <?php endif; ?>
    <?php wp_reset_postdata();?>



        </ul>

    </div> <!-- .entry-content -->

    </div>

<?php get_footer(); ?>
 