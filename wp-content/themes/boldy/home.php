<?php get_header(); ?>
	<!-- BEGIN SLIDER -->
	  <div id="slider">
	  		<?php if(get_option('boldy_slider')!=''){
			$page_data = get_page(get_option('boldy_slider'));
					$content = $page_data->post_content;
					echo $page_data->post_content;
			}else{?>
				<div style="border:1px solid #ddd; text-align:center; padding:150px 100px 0; height:219px; font-size:14px;">
					This is the slider. In order to have items here you need to create a page in which to insert the images, simply one after another, setting up the link to point at ( if needed ) and text captions in the Title field. Then select the page as the "slider page" in the Theme Options Page. Make sure your images are 960px x 370px.
				</div>
			<?php }?>
		</div>
	   <div style="width:960px; margin:0 auto; background:url(<?php bloginfo('template_directory'); ?>/images/bk_shadow_slider.png) 0 0 no-repeat; height:50px;"></div>
	   <!-- END SLIDER -->
	   <!-- BEGIN BLURB -->
	   <?php if(get_option('boldy_blurb_enable')=="yes" && get_option('boldy_blurb_text')!=""){ ?>
	   <div id="blurb">
			<p>
			<a href="<?php 
			if(get_option('boldy_blurb_page')!=""){
				echo get_permalink(get_option('boldy_blurb_page'));
			}elseif(get_option('boldy_blurb_link')!=""){
				echo get_option('boldy_blurb_link');
			} ?>"><img src="<?php bloginfo('template_directory'); ?>/images/but_blurb.png" alt="" /></a>
			<?php echo get_option('boldy_blurb_text'); ?> 
			</p>
	   </div>
	   <?php }?>
	   <!-- END BLURB -->
	   <!-- BEGIN HOME CONTENT -->
	   <!-- begin home boxes -->
		<?php $box1=get_post(get_option('boldy_home_box1'));
				  $box2=get_post(get_option('boldy_home_box2'));
				  $box3=get_post(get_option('boldy_home_box3')); 
				  if(get_option('boldy_home_box1')!= null && get_option('boldy_home_box2')!= null && get_option('boldy_home_box3')!= null){?>
		<div id="homeBoxes" class="clearfix">
			<div class="homeBox">
				<h2><?php echo $box1->post_title?></h2>
				<?php echo apply_filters('the_content', $box1->post_content);?>
				<a href="<?php echo get_option('boldy_home_box1_link')?>"><strong>Read more &raquo;</strong></a>
			</div>
			<div class="homeBox">
				<h2><?php echo $box2->post_title?></h2>
				<?php echo apply_filters('the_content', $box2->post_content);?>
				<a href="<?php echo get_option('boldy_home_box2_link')?>"><strong>Read more &raquo;</strong></a>
			</div>
			<div class="homeBox last">
				<h2><?php echo $box3->post_title?></h2>
				<?php echo apply_filters('the_content', $box3->post_content);?>
				<a href="<?php echo get_option('boldy_home_box3_link')?>"><strong>Read more &raquo;</strong></a>
			</div>
		</div>
		<?php }?>
		<!-- end home boxes -->
	   <!-- END HOME CONTENT -->
	   
	   <!-- SLIDER SETTINGS -->
	   <script type="text/javascript">
			$(window).load(function() {
				$('#slider').nivoSlider({
					effect:'random', //Specify sets like: 'fold,fade,sliceDown'
					slices:15,
					animSpeed:500,
					pauseTime:3000,
					startSlide:0, //Set starting Slide (0 index)
					directionNav:true, //Next & Prev
					directionNavHide:true, //Only show on hover
					controlNav:true, //1,2,3...
					controlNavThumbs:false, //Use thumbnails for Control Nav
					controlNavThumbsFromRel:false, //Use image rel for thumbs
					controlNavThumbsSearch: '.jpg', //Replace this with...
					controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
					keyboardNav:true, //Use left & right arrows
					pauseOnHover:true, //Stop animation while hovering
					manualAdvance:false, //Force manual transitions
					captionOpacity:0.8, //Universal caption opacity
					beforeChange: function(){},
					afterChange: function(){},
					slideshowEnd: function(){} //Triggers after all slides have been shown
				});
			});
			</script>

<?php get_footer(); ?>