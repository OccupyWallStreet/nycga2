<?php
    query_posts( array(
        'post_type' => 'post',
        'posts_per_page' => get_option('workspace_home_blog_posts_num')
		)
	);
?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

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
        
        <?php if(is_array($src)){?>

            <a class="fadeThis" href="<?php the_permalink(); ?>" rel="bookmark" >
                <?php the_post_thumbnail('home-thumb', array('class' => 'entry-thumb')); ?>
                <div class="overlay">
                    <span class="icon"></span>
                </div>
            </a>
            <div class="image-shadow-bottom"></div>
        <?php
            for($i=0;$i<count($array);$i++){
        ?>
            <a class="fadeThis" href="<?php the_permalink(); ?>" rel="bookmark" ></a>
        <?php
            }
        ?>

            <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <div class="entry-meta">
                <?php the_time('M d, Y'); ?> &middot; <?php comments_popup_link( 'No comments yet', '1 comment', '% comments', 'comments-link', 'Comments off'); ?>
		    </div>
		    <div class="entry-except"><p><?php tj_content_limit('80'); ?></p></div>

        <?php }else{?>
            <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <div class="entry-meta">
                <?php the_time('M d, Y'); ?> &middot; <?php comments_popup_link( 'No comments yet', '1 comment', '% comments', 'comments-link', 'Comments off'); ?>
		    </div>
		    <div class="entry-excerpt"><p><?php tj_content_limit('80'); ?></p></div>
        <?php }?>
	</li>
    
<?php endwhile; else: ?>
<?php endif; ?>
<?php wp_reset_postdata();?>
 
