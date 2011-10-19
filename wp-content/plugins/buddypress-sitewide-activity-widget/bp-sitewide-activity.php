<?php
/*
Plugin name: Buddypress Sitewide activity widget
Description: Sitewide Activity Widget for Buddypress 1.2+
Author:Brajesh Singh
Author URI: http://buddydev.com
Plugin URI: http://buddydev.com/plugins/buddypress-sitewide-activity-widget/
Version: 1.1.3.3
Last Updated: September 01, 2011
*/
 $bp_swa_dir =str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
 define("BP_SWA_DIR_NAME",$bp_swa_dir);//the directory name of swa widget
 define("SWA_PLUGIN_DIR",WP_PLUGIN_DIR."/".BP_SWA_DIR_NAME);
 define("BP_SWA_PLUGIN_URL",WP_PLUGIN_URL."/".BP_SWA_DIR_NAME);
 define("SWA_PLUGIN_NAME","swa");

//for enqueuing javascript
add_action("wp_print_scripts","swa_load_js");

function swa_load_js(){
    if(!is_admin())//load only on front end
        wp_enqueue_script("swa-js",BP_SWA_PLUGIN_URL."swa.js",array("jquery"));
}
//load css
add_action("wp_print_styles","swa_load_css");

function swa_load_css(){
    if(apply_filters("swa_load_css",true))//allow theme developers to override it
        wp_enqueue_style ("swa-css", BP_SWA_PLUGIN_URL."swa.css");
}

//localization
function swa_load_textdomain() {
        $locale = apply_filters( 'swa_load_textdomain_get_locale', get_locale() );
	// if load .mo file
	if ( !empty( $locale ) ) {
		$mofile_default = sprintf( '%s/languages/%s-%s.mo', SWA_PLUGIN_DIR,SWA_PLUGIN_NAME, $locale );
		$mofile = apply_filters( 'swa_load_textdomain_mofile', $mofile_default );
		// make sure file exists, and load it
		if ( file_exists( $mofile ) ) {
			load_textdomain( SWA_PLUGIN_NAME, $mofile );
		}
	}
}
add_action ( 'bp_init', 'swa_load_textdomain', 2 );

//post form
function swa_show_post_form(){
    include WP_PLUGIN_DIR."/".BP_SWA_DIR_NAME.'post-form.php';//no inc_once because we may need form multiple times
}

/* widget*/
class BP_SWA_Widget extends WP_Widget {
	function bp_swa_widget() {
		parent::WP_Widget( false, $name = __( 'Site Wide Activity', 'swa' ) );
	}

	function widget($args, $instance) {
		global $bp;
		extract( $args );
                $included_components=$instance["included_components"];
                $excluded_components=$instance["excluded_components"];
                if(empty($included_components))
                    $included_components=BP_Activity_Activity::get_recorded_components();
                    $scope=$included_components;
                
                if(!empty($scope)&&is_array($excluded_components))
                    $scope=array_diff($scope,$excluded_components);
                if(!empty($scope))
                    $scope=join(",",$scope);
                

                if(!empty ($included_components)&&  is_array($included_components))
                    $included_components=join(",",$included_components);
                 if(!empty ($excluded_components)&&  is_array($excluded_components))
                    $excluded_components=join(",",$excluded_components);
                 
                 //find scope
                 

		echo $before_widget;
		echo $before_title
		   . $instance['title'] ;
                if($instance['show_feed_link']=="yes")
		echo	 ' <a class="swa-rss" href="' . bp_get_sitewide_activity_feed_link() . '" title="' . __( 'Site Wide Activity RSS Feed', 'swa' ) . '">' . __( '[RSS]', 'swa' ) . '</a>';
		echo    $after_title;
		 
                   bp_swa_list_activities($instance['per_page'],1,$scope,$instance['max_items'],$instance["show_avatar"],$instance["show_activity_filters"],$included_components,$excluded_components,$instance['is_personal'],$instance['is_blog_admin_activity'],$instance['show_post_form']);
		  ?>
		<input type='hidden' name='max' id='swa_max_items' value="<?php echo  $instance['max_items'];?>" />  
		<input type='hidden' name='max' id='swa_per_page' value="<?php echo  $instance['per_page'];?>" />  
		<input type='hidden' name='show_avatar' id='swa_show_avatar' value="<?php echo  $instance['show_avatar'];?>" />
		<input type='hidden' name='show_filters' id='swa_show_filters' value="<?php echo  $instance['show_activity_filters'];?>" />
		<input type='hidden' name='included_components' id='swa_included_components' value="<?php echo  $included_components;?>" />
		<input type='hidden' name='excluded_components' id='swa_excluded_components' value="<?php echo  $excluded_components;?>" />
		<input type='hidden' name='is_personal' id='swa_is_personal' value="<?php echo  $instance['is_personal'];?>" />
		<input type='hidden' name='is_blog_admin_activity' id='swa_is_blog_admin_activity' value="<?php echo  $instance['is_blog_admin_activity'];?>" />
		<input type='hidden' name='show_post_form' id='swa_show_post_form' value="<?php echo  $instance['show_post_form'];?>" />

	<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_items'] = strip_tags( $new_instance['max_items'] );
		$instance['per_page'] = strip_tags( $new_instance['per_page'] );
		$instance['show_avatar'] =  $new_instance['show_avatar']; //avatar should be visible or not
		$instance['allow_reply'] = $new_instance['allow_reply']; //allow reply inside widget or not
		$instance['show_post_form'] = $new_instance['show_post_form']; //should we show the post form or not
		$instance['show_activity_filters'] =$new_instance['show_activity_filters'] ; //activity filters should be visible or not
		$instance['show_feed_link'] =  $new_instance['show_feed_link'] ; //feed link should be visible or not

                $instance["included_components"]=$new_instance["included_components"];
                $instance["excluded_components"]=$new_instance["excluded_components"];
                $instance["is_blog_admin_activity"]=$new_instance["is_blog_admin_activity"];
                $instance["is_personal"]=$new_instance["is_personal"];
                  

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title'=>__('Site Wide Activities','swa'),'max_items' => 200, 'per_page' => 25,'is_personal'=>'no','is_blog_admin_activity'=>'no','show_avatar'=>'yes','show_feed_link'=>'yes','show_post_form'=>'no','allow_reply'=>'no','show_activity_filters'=>'yes','included_components'=>false,'excluded_components'=>false ) );
		$per_page = strip_tags( $instance['per_page'] );
		$max_items = strip_tags( $instance['max_items'] );
		$title = strip_tags( $instance['title'] );
                extract($instance);
              
		?>

                <p><label for="bp-swa-title"><strong><?php _e('Title:', 'swa'); ?> </strong><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>
		<p><label for="bp-swa-per-page"><?php _e('Number of Items Per Page:', 'swa'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'per_page' ); ?>" name="<?php echo $this->get_field_name( 'per_page' ); ?>" type="text" value="<?php echo esc_attr( $per_page ); ?>" style="width: 30%" /></label></p>
		<p><label for="bp-swa-max"><?php _e('Max items to show:', 'swa'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_items' ); ?>" name="<?php echo $this->get_field_name( 'max_items' ); ?>" type="text" value="<?php echo esc_attr( $max_items ); ?>" style="width: 30%" /></label></p>
		 <p><label for="bp-swa-is-personal"><strong><?php _e("Limit to Logged In user's activity:", 'swa'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'is_personal' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'is_personal' ); ?>_yes" name="<?php echo $this->get_field_name( 'is_personal' ); ?>" type="radio" <?php if($is_personal=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'is_personal' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'is_personal' ); ?>_no" name="<?php echo $this->get_field_name( 'is_personal' ); ?>" type="radio" <?php if($is_personal!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
                <p><label for="bp-swa-is-blog-admin-activity"><strong><?php _e("List My Activity Only:", 'swa'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_yes" name="<?php echo $this->get_field_name( 'is_blog_admin_activity' ); ?>" type="radio" <?php if($is_blog_admin_activity=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'is_blog_admin_activity' ); ?>_no" name="<?php echo $this->get_field_name( 'is_blog_admin_activity' ); ?>" type="radio" <?php if($is_blog_admin_activity!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
                <p><label for="bp-swa-show-avatar"><strong><?php _e('Show Avatar:', 'swa'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_avatar' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" type="radio" <?php if($show_avatar=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_avatar' ); ?>_no" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" type="radio" <?php if($show_avatar!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>
                    
                    </label>
               </p>
               <p><label for="bp-swa-show-feed-link"><?php _e('Show Feed Link:', 'swa'); ?>
                       <label for="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_feed_link' ); ?>" type="radio" <?php if($show_feed_link=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_feed_link' ); ?>_no" name="<?php echo $this->get_field_name( 'show_feed_link' ); ?>" type="radio" <?php if($show_feed_link!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
               <p><label for="bp-swa-show-post-form"><strong><?php _e('Show Post Form', 'swa'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'show_post_form' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_post_form' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_post_form' ); ?>" type="radio" <?php if($show_post_form=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_post_form' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_post_form' ); ?>_no" name="<?php echo $this->get_field_name( 'show_post_form' ); ?>" type="radio" <?php if($show_post_form!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
               <!-- <p><label for="bp-swa-show-reply-link"><?php _e('Allow reply to activity item:', 'swa'); ?>
                       <label for="<?php echo $this->get_field_id( 'allow_reply' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'allow_reply' ); ?>_yes" name="<?php echo $this->get_field_name( 'allow_reply' ); ?>" type="radio" <?php if($show_feed_link=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'allow_reply' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'allow_reply' ); ?>_no" name="<?php echo $this->get_field_name( 'allow_reply' ); ?>" type="radio" <?php if($show_feed_link!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>-->
               <p><label for="bp-swa-show-activity-filters"><strong><?php _e('Show Activity Filters:', 'swa'); ?></strong>
                       <label for="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_yes" > <input id="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_yes" name="<?php echo $this->get_field_name( 'show_activity_filters' ); ?>" type="radio" <?php if($show_activity_filters=='yes') echo "checked='checked'";?> value="yes" style="width: 10%" />Yes</label>
                       <label for="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_activity_filters' ); ?>_no" name="<?php echo $this->get_field_name( 'show_activity_filters' ); ?>" type="radio" <?php if($show_activity_filters!=='yes') echo "checked='checked'";?> value="no" style="width: 10%" />No</label>

                    </label>
               </p>
               <p><label for="bp-swa-included-filters"><strong><?php _e('Include only following Filters:', 'swa'); ?></strong></label></p>
                 <p>     <?php $recorded_components=BP_Activity_Activity::get_recorded_components();
                      foreach((array)$recorded_components as $component):?>
                         <label for="<?php echo $this->get_field_id( 'included_components' ).'_'.$component ?>" ><?php echo ucwords($component);?> <input id="<?php echo $this->get_field_id( 'included_components' ).'_'.$component ?>" name="<?php echo $this->get_field_name( 'included_components' ); ?>[]" type="checkbox" <?php if(is_array($included_components)&&in_array($component, $included_components)) echo "checked='checked'";?> value="<?php echo $component;?>" style="width: 10%" /></label>
                       <?php endforeach;?>
                   
               </p>

              <p><label for="bp-swa-included-filters"><strong><?php _e('Exclude following Components activity', 'swa'); ?></strong></label></p>
                 <p>     <?php $recorded_components=BP_Activity_Activity::get_recorded_components();
                      foreach((array)$recorded_components as $component):?>
                         <label for="<?php echo $this->get_field_id( 'excluded_components' ).'_'.$component ?>" ><?php echo ucwords($component);?> <input id="<?php echo $this->get_field_id( 'excluded_components' ).'_'.$component ?>" name="<?php echo $this->get_field_name( 'excluded_components' ); ?>[]" type="checkbox" <?php if(is_array($excluded_components)&&in_array($component, $excluded_components)) echo "checked='checked'";?> value="<?php echo $component;?>" style="width: 10%" /></label>
                       <?php endforeach;?>

               </p>
               
	<?php
	}
}

function swa_register_widgets(){
    add_action('widgets_init', create_function('', 'return register_widget("BP_SWA_Widget");') );
}
//register the widget
add_action( 'bp_loaded', 'swa_register_widgets' );



/** Fix error for implode issue*/
//compat with filter links, will remove when bp adds it

function swa_activity_filter_links( $args = false ) {//copy of bp_activity_filter_link
	echo swa_get_activity_filter_links( $args );
}
	function swa_get_activity_filter_links( $args = false ) {
		global $activities_template, $bp;
                
              
                $link='';
		$defaults = array(
			'style' => 'list'
		);
            //check scope, if not single entiry

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$components=swa_get_base_component_scope($include,$exclude);
                 
                if ( !$components )
			return false;
                 
		foreach ( (array) $components as $component ) {
			/* Skip the activity comment filter */
			if ( 'activity' == $component )
				continue;

			if ( isset( $_GET['afilter'] ) && $component == $_GET['afilter'] )
				$selected = ' class="selected"';
			else
				$selected='';

			$component = esc_attr( $component );

			switch ( $style ) {
				case 'list':
					$tag = 'li';
					$before = '<li id="afilter-' . $component . '"' . $selected . '>';
					$after = '</li>';
				break;
				case 'paragraph':
					$tag = 'p';
					$before = '<p id="afilter-' . $component . '"' . $selected . '>';
					$after = '</p>';
				break;
				case 'span':
					$tag = 'span';
					$before = '<span id="afilter-' . $component . '"' . $selected . '>';
					$after = '</span>';
				break;
			}

			$link = add_query_arg( 'afilter', $component );
			$link = remove_query_arg( 'acpage' , $link );

			$link = apply_filters( 'bp_get_activity_filter_link_href', $link, $component );

			/* Make sure all core internal component names are translatable */
			$translatable_components = array( __( 'profile', 'swa'), __( 'friends', 'swa' ), __( 'groups', 'swa' ), __( 'status', 'swa' ), __( 'blogs', 'swa' ) );

			$component_links[] = $before . '<a href="' . esc_attr( $link ) . '">' . ucwords( __( $component, 'swa' ) ) . '</a>' . $after;
		}

		$link = remove_query_arg( 'afilter' , $link );

		
                 
                     if ( !empty( $_REQUEST['scope'] ) ){
                        $link .= "?afilter=";
        			$component_links[] = '<' . $tag . ' id="afilter-clear"><a href="' . esc_attr( $link ) . '"">' . __( 'Clear Filter', 'swa' ) . '</a></' . $tag . '>';
                     }

                     if(!empty($component_links))
                        return apply_filters( 'swa_get_activity_filter_links', implode( "\n", $component_links ),$component_links );
               
                 return false;
	}

 function swa_get_base_component_scope($include,$exclude){
     /* Fetch the names of components that have activity recorded in the DB */
		$components = BP_Activity_Activity::get_recorded_components();

                if(!empty($include))
                    $components=explode(",",$include);//array of component names

                if(!empty($exclude)){  //exclude all the
                    $components=array_diff((array)$components, explode(",",$exclude));//diff of exclude/recorded components
                    }
       return $components;
 }







 //helper function, return the single admin of this blog ok ok ok

 function swa_get_blog_admin_id(){
     global $current_blog;
     $blog_id=$current_blog->blog_id;
     $users=  SWA_Helper::get_admin_users_for_blog($blog_id);
     if(!empty($users))
         $users=$users[0];//just the first user
     return $users;
 }

/**
 * template/output control functions
 */

//individual entry in the activity stream
function swa_activity_entry($show_avatar=false){
    ?>
 <?php do_action( 'bp_before_activity_entry' ) ?>
    <li class="<?php bp_activity_css_class() ?>" id="activity-<?php bp_activity_id() ?>">
            <?php if($show_avatar=="yes"):?>
                      <div class="swa-activity-avatar">
                            <a href="<?php bp_activity_user_link() ?>">
                                    <?php bp_activity_avatar( 'type=thumb&width=50&height=50' ) ?>
                            </a>

                    </div>
           <?php endif;?>
          <div class="swa-activity-content">
		<div class="swa-activity-header">
			<?php bp_activity_action() ?>
		</div>

		<?php if ( bp_activity_has_content() ) : ?>
			<div class="swa-activity-inner">
				<?php bp_activity_content_body() ?>
			</div>
		<?php endif; ?>

	<?php do_action( 'bp_activity_entry_content' ) ?>
	<div class="swa-activity-meta">
            <?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>
				<a href="<?php bp_activity_comment_link() ?>" class="acomment-reply" id="acomment-comment-<?php bp_activity_id() ?>"><?php _e( 'Reply', 'buddypress' ) ?> (<span><?php bp_activity_comment_count() ?></span>)</a>
			<?php endif; ?>
            <?php if ( is_user_logged_in() ) : ?>
		<?php if ( !bp_get_activity_is_favorite() ) : ?>
                    <a href="<?php bp_activity_favorite_link() ?>" class="fav" title="<?php _e( 'Mark as Favorite', 'swa' ) ?>"><?php _e( 'Favorite', 'swa' ) ?></a>
		<?php else : ?>
                    <a href="<?php bp_activity_unfavorite_link() ?>" class="unfav" title="<?php _e( 'Remove Favorite', 'swa' ) ?>"><?php _e( 'Remove Favorite', 'swa' ) ?></a>
		<?php endif; ?>
            <?php endif;?>
            <?php do_action( 'bp_activity_entry_meta' ) ?>
        </div>
	<div class="clear" ></div>
    </div>
    <?php if ( 'activity_comment' == bp_get_activity_type() ) : ?>
	<div class="swa-activity-inreplyto">
            <strong><?php _e( 'In reply to', 'swa' ) ?></strong> - <?php bp_activity_parent_content() ?> &middot;
            <a href="<?php bp_activity_thread_permalink() ?>" class="view" title="<?php _e( 'View Thread / Permalink', 'swa' ) ?>"><?php _e( 'View', 'swa' ) ?></a>
	</div>
    <?php endif; ?>
    <?php if ( bp_activity_can_comment() ) : ?>
        <div class="swa-activity-comments">
        	<?php bp_activity_comments() ?>
            <?php if ( is_user_logged_in() ) : ?>
			<form action="<?php bp_activity_comment_form_action() ?>" method="post" id="swa-ac-form-<?php bp_activity_id() ?>" class="swa-ac-form"<?php bp_activity_comment_form_nojs_display() ?>>
				<div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ) ?></div>
				<div class="ac-reply-content">
					<div class="ac-textarea">
						<textarea id="swa-ac-input-<?php bp_activity_id() ?>" class="ac-input" name="ac_input_<?php bp_activity_id() ?>"></textarea>
					</div>
					<input type="submit" name="swa_ac_form_submit" value="<?php _e( 'Post', 'buddypress' ) ?> &rarr;" /> &nbsp; <?php _e( 'or press esc to cancel.', 'buddypress' ) ?>
					<input type="hidden" name="comment_form_id" value="<?php bp_activity_id() ?>" />
				</div>
				<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ) ?>
			</form>
			<?php endif; ?>
	</div>
    <?php endif; ?>
</li>
<?php do_action( 'bp_after_swa_activity_entry' ) ?>

<?php
}


function bp_swa_list_activities($per_page=10,$page=1,$scope='',$max=200,$show_avatar="yes",$show_filters="yes",$included=false,$excluded=false,$is_personal="no",$is_blog_admin_activity="no",$show_post_form="no"){
//check for the scope of activity
//is it the activity of logged in user/blog admin
//logged in user over rides blog admin
     global $bp;
     $primary_id='';
     if(bp_is_group ())
         $primary_id=null;
     $user_id="";//for limiting to users

     if($is_personal=="yes")
        $user_id=$bp->loggedin_user->id;
    else if($is_blog_admin_activity=="yes")
        $user_id=swa_get_blog_admin_id();
    else if(bp_is_user())
        $user_id=null;
    
    $components_scope=swa_get_base_component_scope($included,$excluded);

    $components_base_scope="";

    if(!empty($components_scope))
        $components_base_scope=join(",",$components_scope);

   ?>
      <div class='swa-wrap'>
          <?php if(is_user_logged_in()&&$show_post_form=="yes")
                swa_show_post_form();?>
          <?php if($show_filters=="yes"):?>
			<ul id="activity-filter-links">
				<?php swa_activity_filter_links("scope=".$scope."&include=".$included."&exclude=".$excluded) ?>
			</ul>
          <div class="clear"></div>
                    <?php endif;?>
        <?php
 	if ( bp_has_activities( 'type=sitewide&max=' . $max . '&page='.$page.'&per_page=' .$per_page.'&object='.$scope."&user_id=".$user_id."&primary_id=".$primary_id ) ) : ?>

            <div class="swa-pagination ">
                    <div class="pag-count" id="activity-count">
                            <?php bp_activity_pagination_count() ?>
                    </div>

                    <div class="pagination-links" id="activity-pag">
                            &nbsp; <?php bp_activity_pagination_links() ?>
                    </div>
                <div class="clear" ></div>
            </div>


            <div class="clear" ></div>
                <ul  class="site-wide-stream swa-activity-list">
                    <?php while ( bp_activities() ) : bp_the_activity(); ?>
                        <?php swa_activity_entry($show_avatar);?>
                    <?php endwhile; ?>
               </ul>

	<?php else: ?>

        <div class="widget-error">
            <?php if($is_personal=="yes")
                $error=sprintf(__("You have no recent %s activity.","swa"),$scope);
                else
                    $error=__('There has been no recent site activity.', 'swa');
                ?>
                <?php echo $error; ?>
        </div>
	<?php endif;?>
     </div>
     
<?php
}

/*** helper functions*/
/*we don't want users to be able to reply from the swa  widget, so modify bp_activity_get_comments*/
	function bp_swa_activity_get_comments( $args = '' ) {
		global $activities_template, $bp;

		if ( !$activities_template->activity->children )
			return false;

		$comments_html = bp_swa_activity_recurse_comments( $activities_template->activity );

		return apply_filters( 'bp_swa_activity_get_comments', $comments_html );
	}
		function bp_swa_activity_recurse_comments( $comment ) {
			global $activities_template, $bp;

			if ( !$comment->children )
				return false;

			$content .= '<ul>';
			foreach ( (array)$comment->children as $comment ) {
				if ( !$comment->user_fullname )
					$comment->user_fullname = $comment->display_name;

				$content .= '<li id="swa-acomment-' . $comment->id . '">';
				$content .= '<div class="swa-acomment-avatar"><a href="' . bp_core_get_user_domain( $comment->user_id, $comment->user_nicename, $comment->user_login ) . '">' . bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => 25, 'height' => 25, 'email' => $comment->user_email ) ) . '</a></div>';
				$content .= '<div class="swa-acomment-meta"><a href="' . bp_core_get_user_domain( $comment->user_id, $comment->user_nicename, $comment->user_login ) . '">' . apply_filters( 'bp_get_member_name', $comment->user_fullname ) . '</a> &middot; ' . sprintf( __( '%s ago', 'swa' ), bp_core_time_since( strtotime( $comment->date_recorded ) ) );


				/* Delete link */
				if ( $bp->loggedin_user->is_site_admin || $bp->loggedin_user->id == $comment->user_id )
					$content .= ' &middot; <a href="' . wp_nonce_url( $bp->root_domain . '/' . $bp->activity->slug . '/delete/?cid=' . $comment->id, 'bp_activity_delete_link' ) . '" class="delete acomment-delete">' . __( 'Delete', 'swa' ) . '</a>';

				$content .= '</div>';
				$content .= '<div class="swa-acomment-content">' . apply_filters( 'bp_get_activity_content', $comment->content ) . '</div>';

				$content .= bp_activity_recurse_comments( $comment );
				$content .= '</li>';
			}
			$content .= '</ul>';

			return apply_filters( 'bp_swa_activity_recurse_comments', $content );
		}



                

 
 //ajax action handling	for the filters(blogs/profile/groups)
function swa_ajax_list_activity(){
	$page=$_POST["page"]?$_POST["page"]:1;
	$scope=$_POST['scope'];
	$per_page=$_POST['per_page']?$_POST['per_page']:10;
	$max=$_POST['max']?$_POST['max']:200;

        $show_avatar=$_POST['show_avatar']?$_POST['show_avatar']:"yes";
        $show_filters=$_POST['show_filters']?$_POST['show_filters']:"yes";
        $included=$_POST['included_components']?$_POST['included_components']:false;
        $excluded=$_POST['excluded_components']?$_POST['excluded_components']:false;
        $is_personal=$_POST['is_personal']?$_POST['is_personal']:"no";
        $is_blog_admin_activity=$_POST['is_blog_admin_activity']?$_POST['is_blog_admin_activity']:"no";
        $show_post_form=$_POST["show_post_form"]?$_POST["show_post_form"]:"no";
                //$show_filters=true,$included=false,$excluded=false
	bp_swa_list_activities($per_page,$page,$scope,$max,$show_avatar,$show_filters,$included,$excluded,$is_personal,$is_blog_admin_activity,$show_post_form);
}

add_action("wp_ajax_swa_fetch_content",	"swa_ajax_list_activity");

 //
 /* AJAX update posting */
function swa_post_update() {
	global $bp;

	/* Check the nonce */
	check_admin_referer( 'swa_post_update', '_wpnonce_swa_post_update' );

	if ( !is_user_logged_in() ) {
		echo '-1';
		return false;
	}

	if ( empty( $_POST['content'] ) ) {
		echo '-1<div id="message" class="error"><p>' . __( 'Please enter some content to post.', 'swa' ) . '</p></div>';
		return false;
	}

	if ( empty( $_POST['object'] ) && function_exists( 'bp_activity_post_update' ) ) {
		$activity_id = bp_activity_post_update( array( 'content' => $_POST['content'] ) );
	} elseif ( $_POST['object'] == 'groups' ) {
		if ( !empty( $_POST['item_id'] ) && function_exists( 'groups_post_update' ) )
			$activity_id = groups_post_update( array( 'content' => $_POST['content'], 'group_id' => $_POST['item_id'] ) );
	} else
		$activity_id = apply_filters( 'bp_activity_custom_update', $_POST['object'], $_POST['item_id'], $_POST['content'] );

	if ( !$activity_id ) {
		echo '-1<div id="message" class="error"><p>' . __( 'There was a problem posting your update, please try again.', 'swa' ) . '</p></div>';
		return false;
	}
$show_avatar=$_POST["show_avatar"]?$_POST["show_avatar"]:"no";
	if ( bp_has_activities ( 'include=' . $activity_id ) ) : ?>
		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<?php swa_activity_entry($show_avatar) ?>
		<?php endwhile; ?>
	 <?php endif;
}


add_action("wp_ajax_swa_post_update","swa_post_update");//hook to post update


//wraper class for finding admin users of a blog
class SWA_Helper{
    function get_admin_users_for_blog($blog_id) {
	global $wpdb,$current_blog;
        $meta_key="wp_".$blog_id."_capabilities";//.."_user_level";

	$role_sql="select user_id,meta_value from {$wpdb->usermeta} where meta_key='". $meta_key."'";

	$role=$wpdb->get_results($wpdb->prepare($role_sql),ARRAY_A);
	//clean the role
	$all_user=array_map("swa_serialize_role",$role);//we are unserializing the role to make that as an array

	foreach($all_user as $key=>$user_info)
		if($user_info['meta_value']['administrator']==1)//if the role is admin
			$admins[]=$user_info['user_id'];

	return $admins;

}

}
function swa_serialize_role($roles){
	$roles['meta_value']=maybe_unserialize($roles['meta_value']);
return $roles;
}
?>
