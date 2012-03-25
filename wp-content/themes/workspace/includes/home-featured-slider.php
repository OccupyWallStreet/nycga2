<!--theme slider -->


    <div id="featured-slider">
        <div id="slider-box">
            <a id="slider-prev"></a>
            <a id="slider-next"></a>
        <?php
            $num = get_option('workspace_slides_num');
            query_posts( array(
			'post_type' => 'slider',
			'posts_per_page' => $num
			)
		);
        ?>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="slides">

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
                $has_post_img = FALSE;
                if(count($array)>0){
                    $has_post_img = TRUE;
                }
            ?>

            <?php
                //get_template_part( 'includes/theme-slider' );
                $has_img = get_post_meta(get_the_ID(), 'tj_slider_image', TRUE);
                $has_video = get_post_meta(get_the_ID(), 'tj_slider_video', TRUE);
                $has_url = get_post_meta(get_the_ID(), 'tj_slider_url', TRUE);
                $has_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), array( '9999','9999' ), false, '' );

            ?>

            <?php
                if(is_array($has_thumb)){
            ?>
                <div class="image-slide-content">
                    <?php if($has_url){?>
                        <div class="entry-title"><a title="<?php the_title();?>" href="<?php echo $has_url;?>"><h2 class="title"><?php the_title();?></h2></a></div>
                    <?php }else{?>
                        <div class="entry-title"><h2 class="title"><?php the_title();?></h2></div>
                    <?php }?>
                    <div class="entry-except"><?php the_excerpt();?></div>
                </div>
                <div class="image-slide-thumb">
                    <?php the_post_thumbnail('slider-thumb')?>
                </div>
            <?php
                }elseif($has_img){
            ?>
                <div class="image-slide-content">
                    <?php if($has_url){?>
                        <a title="<?php the_title();?>" href="<?php echo $has_url;?>"><h2 class="entry-title"><?php the_title();?></h2></a>
                    <?php }else{?>
                        <h2 class="entry-title"><?php the_title();?></h2>
                    <?php }?>
                    <div class="entry-except"><?php the_excerpt();?></div>
                </div>
                <div class="image-slide-thumb">
                    <img src="<?php echo $has_img;?>" alt="<?php the_title();?>">
                </div>
            <?php
                } elseif ($has_video) {
            ?>

                <div class="video-slide">
                <div class="video-slide-embed">
                    <?php echo stripslashes(htmlspecialchars_decode($has_video));?>
                </div>                
                    <?php if($has_url){?>
                        <a title="<?php the_title();?>" href="<?php echo $has_url;?>"><h2 class="entry-title"><?php the_title();?></h2></a>
                    <?php }else{?>
                        <h2 class="entry-title"><?php the_title();?></h2>
                    <?php }?>
                    <div class="entry-content"><?php the_content();?></div>
                </div>
                <div class="clear"></div>
            <?php
                } elseif($has_post_img) {
            ?>
                <div class="post-slide entry-content">
                    <?php the_content('');?>
                </div>
            <?php
                }
            ?>

            </div>
        <?php endwhile; endif; ?>
        <?php wp_reset_postdata();?>
        </div>

	</div> <!-- end #featured-slider -->
