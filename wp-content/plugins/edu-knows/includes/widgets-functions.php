<?php
///////////////////////////////////////////////////////////////////////////////
// multi instance twitter widget
///////////////////////////////////////////////////////////////////////////////
function wpmudev_twitter_js_handler($unique_id,$twitter_username,$twitter_count) {
	?>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--

	    function twitterCallback2(twitters) {
	      var statusHTML = [];
	      for (var i=0; i<twitters.length; i++){
	        var username = twitters[i].user.screen_name;
	        var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
	          return '<a href="'+url+'">'+url+'</a>';
	        }).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
	          return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
	        });
	        statusHTML.push('<li><span class="">'+status+'</span> <a style="font-size:85%" class="time" href="http://twitter.com/'+username+'/statuses/'+twitters[i].id+'">'+relative_time(twitters[i].created_at)+'</a></li>');
	      }
	      document.getElementById('twitter_update_list_<?php echo $unique_id; ?>').innerHTML = statusHTML.join('');
	    }

	    function relative_time(time_value) {
	      var values = time_value.split(" ");
	      time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
	      var parsed_date = Date.parse(time_value);
	      var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
	      var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
	      delta = delta + (relative_to.getTimezoneOffset() * 60);

	      if (delta < 60) {
	        return 'less than a minute ago';
	      } else if(delta < 120) {
	        return 'about a minute ago';
	      } else if(delta < (60*60)) {
	        return (parseInt(delta / 60)).toString() + ' minutes ago';
	      } else if(delta < (120*60)) {
	        return 'about an hour ago';
	      } else if(delta < (24*60*60)) {
	        return 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
	      } else if(delta < (48*60*60)) {
	        return '1 day ago';
	      } else {
	        return (parseInt(delta / 86400)).toString() + ' days ago';
	      }
	    }
	//-->!]]>
	</script>
<script type="text/javascript" src="http://api.twitter.com/1/statuses/user_timeline/<?php echo $twitter_username; ?>.json?callback=twitterCallback2&amp;count=<?php echo $twitter_count; ?>&amp;include_rts=t"></script>
	<?php
	}

class My_WPMUDEV_Twitter_Widget extends WP_Widget {
function My_WPMUDEV_Twitter_Widget() {
//Constructor
parent::WP_Widget(false, $name = TEMPLATE_DOMAIN . ' | Twitter', array(
'description' => __('Display your latest twitter.', TEMPLATE_DOMAIN)
));
}

function widget($args, $instance) {
// outputs the content of the widget
extract($args); // Make before_widget, etc available.

$twitter_username = $instance['twitter_username'];
$twitter_count = $instance['twitter_count'];

$twitter_title = empty($instance['title']) ? __('Twitter', TEMPLATE_DOMAIN) : apply_filters('widget_title', $instance['title']);
$unique_id = $args['widget_id'];

echo $before_widget;
echo $before_title . $twitter_title . ' - <a style="font-weight: normal; letter-spacing: normal; font-size: 11px;" href="http://twitter.com/' . $twitter_username . '">' . __("Follow me",TEMPLATE_DOMAIN) .'</a>' . $after_title; ?>
<ul id="twitter_update_list_<?php echo $unique_id; ?>">
<?php echo wpmudev_twitter_js_handler($unique_id,$twitter_username,$twitter_count); //Javascript output function ?>
</ul>
<?php echo $after_widget;
}

function update($new_instance, $old_instance) {
//update and save the widget
return $new_instance;
}

function form($instance) {
// Get the options into variables, escaping html characters on the way
$twitter_username = $instance['twitter_username'];
$twitter_title = $instance['title'];
$twitter_count = $instance['twitter_count'];
?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Twitter Title:",TEMPLATE_DOMAIN); ?></label>
<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $twitter_title; ?>" />
</p>

<p><label for="<?php echo $this->get_field_id('twitter_username'); ?>">
<?php echo __('Twitter ID:', TEMPLATE_DOMAIN)?></label>
<input class="widefat" type="text" id="<?php echo $this->get_field_id('twitter_username'); ?>" name="<?php echo $this->get_field_name('twitter_username'); ?>" value="<?php echo $twitter_username; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('twitter_count'); ?>"><?php echo __('Number Of Tweets:', TEMPLATE_DOMAIN)?></label>
<input class="widefat" type="text" id="<?php echo $this->get_field_id('twitter_count'); ?>" name="<?php echo $this->get_field_name('twitter_count'); ?>" value="<?php echo $twitter_count; ?>" />
</p>

<?php
}
}
register_widget('My_WPMUDEV_Twitter_Widget');



///////////////////////////////////////////////////////////////////////////////////
////custom most commented post widget
///////////////////////////////////////////////////////////////////////////////////
class My_WPMUDEV_Most_Commented_Widget extends WP_Widget {
function My_WPMUDEV_Most_Commented_Widget() {
//Constructor
parent::WP_Widget(false, $name = TEMPLATE_DOMAIN . ' | Most Comments', array(
'description' => __('Display your most commented posts.', TEMPLATE_DOMAIN)
));
}
function widget($args, $instance) {
// outputs the content of the widget
extract($args); // Make before_widget, etc available.
$mc_name = empty($instance['title']) ? __('Most Comments', TEMPLATE_DOMAIN) : apply_filters('widget_title', $instance['title']);

$mc_number = $instance['number'];
$mc_comment_count = $instance['commentcount'];

$unique_id = $args['widget_id'];

global $wpdb, $post;
$mostcommenteds = $wpdb->get_results("SELECT $wpdb->posts.ID, post_title, post_name, post_date, COUNT($wpdb->comments.comment_post_ID) AS 'comment_total' FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID WHERE comment_approved = '1' AND post_date_gmt < '" . gmdate("Y-m-d H:i:s") . "' AND post_status = 'publish' AND post_password = '' GROUP BY $wpdb->comments.comment_post_ID ORDER  BY comment_total DESC LIMIT $mc_number");
  echo $before_widget;
  echo $before_title . $mc_name . $after_title;
  echo "<ul class='item-list' id='most-comments'> ";
  foreach ($mostcommenteds as $post) {
    $post_title = htmlspecialchars(stripslashes($post->post_title));
    $comment_total = (int) $post->comment_total;
    echo "<li><a href=\"" . get_permalink() . "\">$post_title";
    if($mc_comment_count == 'yes') {
    echo "&nbsp;<strong>($comment_total)</strong>";
    }
    echo "</a></li>";
  }
  echo "</ul> ";
  echo $after_widget;
}
function update($new_instance, $old_instance) {
//update and save the widget
return $new_instance;
}
function form($instance) {
// Get the options into variables, escaping html characters on the way
$mc_name = $instance['title'];
$mc_number = $instance['number'];
$mc_comment_count = $instance['commentcount'];
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Name for most comment(optional):', TEMPLATE_DOMAIN);?>
<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" class="widefat" value="<?php echo $mc_name;?>" /></label></p>

<p>
<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Total to show: ', TEMPLATE_DOMAIN);?>
<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" class="widefat" value="<?php echo $mc_number;?>" /></label>
</p>

<p>
<label for="<?php echo $this->get_field_id('commentcount'); ?>"><?php _e('Show comments count:', TEMPLATE_DOMAIN); ?></label>
<select id="<?php echo $this->get_field_id('commentcount'); ?>" name="<?php echo $this->get_field_name('commentcount'); ?>">
<option<?php if($mc_comment_count == 'yes') { echo " selected='selected'"; } ?> name="<?php echo $this->get_field_name('commentcount'); ?>" value="yes"><?php _e('yes', TEMPLATE_DOMAIN); ?></option>
<option<?php if($mc_comment_count == 'no') { echo " selected='selected'"; } ?> name="<?php echo $this->get_field_name('commentcount'); ?>" value="no"><?php _e('no', TEMPLATE_DOMAIN); ?></option>
</select>
</p>

<?php
}
}
register_widget('My_WPMUDEV_Most_Commented_Widget');


///////////////////////////////////////////////////////////////////////////////////
////wordpress and buddypress recent comment widget
///////////////////////////////////////////////////////////////////////////////////
class My_WPMUDEV_Recent_Comments_Widget extends WP_Widget {
function My_WPMUDEV_Recent_Comments_Widget() {
//Constructor
parent::WP_Widget(false, $name = TEMPLATE_DOMAIN . ' | Recent Comments', array(
'description' => __('Display your recent comments with user avatar.', TEMPLATE_DOMAIN)
));
}
function widget($args, $instance) {
// outputs the content of the widget
extract($args); // Make before_widget, etc available.
$rc_name = empty($instance['title']) ? __('Recent Comments', TEMPLATE_DOMAIN) : apply_filters('widget_title', $instance['title']);

$rc_avatar = $instance['avatar_on'];
$rc_number = $instance['number'];

$unique_id = $args['widget_id'];

global $wpdb;

$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved,
comment_type,comment_author_url,
SUBSTRING(comment_content,1,80) AS com_excerpt
FROM $wpdb->comments
LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
$wpdb->posts.ID)
WHERE comment_approved = '1' AND comment_type = '' AND
post_password = ''
ORDER BY comment_date_gmt DESC LIMIT $rc_number";

$comments = $wpdb->get_results($sql);
$output = $pre_HTML;

echo $before_widget;

echo $before_title . $rc_name . $after_title;

echo "<ul class='item-list' id='recent-comments'> ";

foreach ($comments as $comment) {

$grav_email = $comment->comment_author_email;
$grav_name = $comment->comment_author_name;
$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email). "&amp;size=32";

?>
<li>
<?php if($rc_avatar == 'yes') {  ?>
<div class="item-avatar">
<?php if($bp_existed == 'true') { ?>
<?php echo bp_comment_author_avatar(); ?>
<?php } else { ?>
<?php echo get_avatar( $grav_email, '50'); ?>
<?php } ?>
</div>
<?php } ?>
<div<?php if($rc_avatar == 'no') {  ?> style="width: 100% !important;"<?php } ?> class="item">
<span class="activity"><?php echo strip_tags($comment->comment_author); ?> <?php _e("says",TEMPLATE_DOMAIN); ?></span> <br />
<a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php _e('on',TEMPLATE_DOMAIN); ?> <?php echo $comment->post_title; ?>">
<?php echo strip_tags($comment->com_excerpt); ?>...
</a>
</div>
</li>
<?php
}
echo "</ul> ";
echo $after_widget;
?>
<?php }

function update($new_instance, $old_instance) {
//update and save the widget
return $new_instance;
}
function form($instance) {
// Get the options into variables, escaping html characters on the way
$rc_name = $instance['title'];
$rc_number = $instance['number'];
$rc_avatar = $instance['avatar_on'];
?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Name for recent comment(optional):', TEMPLATE_DOMAIN); ?>
<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" class="widefat" value="<?php echo $rc_name; ?>" /></label></p>

<p>
<label for="<?php echo $this->get_field_id('avatar_on'); ?>"><?php _e('Enable avatar?:', TEMPLATE_DOMAIN); ?></label>
<select id="<?php echo $this->get_field_id('avatar_on'); ?>" name="<?php echo $this->get_field_name('avatar_on'); ?>">
<option<?php if($rc_avatar == 'yes') { echo " selected='selected'"; } ?> name="<?php echo $this->get_field_name('avatar_on'); ?>" value="yes"><?php _e('yes', TEMPLATE_DOMAIN); ?></option>
<option<?php if($rc_avatar == 'no') { echo " selected='selected'"; } ?> name="<?php echo $this->get_field_name('avatar_on'); ?>" value="no"><?php _e('no', TEMPLATE_DOMAIN); ?></option>
</select>
</p>

<p>
<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Total to show:', TEMPLATE_DOMAIN); ?>
<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" class="widefat" value="<?php echo $rc_number; ?>" /></label></p>

<?php
}
}
register_widget('My_WPMUDEV_Recent_Comments_Widget');




///////////////////////////////////////////////////////////////////////////////////
//// Flickr Widget
///////////////////////////////////////////////////////////////////////////////////
class My_WPMUDEV_Flickr_Widget extends WP_Widget {
function My_WPMUDEV_Flickr_Widget() {
//Constructor
parent::WP_Widget(false, $name = TEMPLATE_DOMAIN . ' | Flickr', array(
'description' => __('Displays your latest Flickr feed.', TEMPLATE_DOMAIN)
));
}
function widget($args, $instance) {
// outputs the content of the widget
extract($args); // Make before_widget, etc available.

$fli_name = empty($instance['title']) ? __('Flickr', TEMPLATE_DOMAIN) : apply_filters('widget_title', $instance['title']);
$fli_id = $instance['id'];
$fli_number = $instance['number'];
$unique_id = $args['widget_id'];

echo $before_widget;
echo $before_title . $fli_name . $after_title; ?>
<ul id="flickr-widget">
<li>
<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $fli_number; ?>&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user=<?php echo $fli_id; ?>"></script>
</li>
<li><a href="http://www.flickr.com/photos/<?php echo "$fli_id"; ?>"><?php _e("view more showcase here", TEMPLATE_DOMAIN); ?></a></li>
</ul>
<?php echo $after_widget; ?>
<?php }


function update($new_instance, $old_instance) {
//update and save the widget
return $new_instance;
}
function form($instance) {
// Get the options into variables, escaping html characters on the way
$fli_name = $instance['title'];
$fli_id = $instance['id'];
$fli_number = $instance['number'];
?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php  _e('Flickr Name',TEMPLATE_DOMAIN); ?>:
<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" class="widefat" value="<?php echo $fli_name; ?>" /></label></p>

<p>
<label for="<?php echo $this->get_field_id('id'); ?>"><?php  _e('Flickr ID',TEMPLATE_DOMAIN); ?>(<a target="_blank" href="http://www.idgettr.com">idGettr</a>):
<input id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" class="widefat" value="<?php echo $fli_id; ?>" /></label></p>


<p>
<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of photos:',TEMPLATE_DOMAIN); ?>
<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" class="widefat" value="<?php echo $fli_number; ?>" /></label></p>

<?php
}
}
register_widget('My_WPMUDEV_Flickr_Widget');





//////////////////////////////////////////////////////////////////////////
// Multi Category Featured Posts Widget
///////////////////////////////////////////////////////////////////////////
class My_WPMUDEV_Featured_Multi_Category_Widget extends WP_Widget {
function My_WPMUDEV_Featured_Multi_Category_Widget() {
//Constructor
parent::WP_Widget(false, $name = TEMPLATE_DOMAIN . ' | Featured Categories', array(
'description' => __('Displays multi category posts with thumbnail.', TEMPLATE_DOMAIN)
));
}
function widget($args, $instance) {
global $bp_existed, $post;
// outputs the content of the widget
extract($args); // Make before_widget, etc available.

$feat_title = empty($instance['title']) ? __('Featured Categories', TEMPLATE_DOMAIN) : apply_filters('widget_title', $instance['title']);
$feat_name = $instance['featcatname'];
$feat_thumb = $instance['featthumb'];
$feat_total = $instance['feattotal'];
$unique_id = $args['widget_id'];

echo $before_widget;

echo $before_title . $feat_title . $after_title;

echo "<ul class='item-list' id='recent-postcat'>";
$my_query = new WP_Query('cat='. $feat_name . '&' . 'showposts=' . $feat_total);
while ($my_query->have_posts()) : $my_query->the_post();
$do_not_duplicate = $post->ID;
$the_post_ids = get_the_ID();
?>

<li>
<?php if($feat_thumb == 'yes') { ?>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(),$fetch_size='thumbnail',$fetch_w='', $fetch_h='',$alt_class=''); ?>
<?php } ?>
<div class="feat-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
<small><?php _e('by', TEMPLATE_DOMAIN); ?>&nbsp;<?php if( $bp_existed == 'true' ) { ?><?php printf( __('%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?><?php the_author_posts_link(); ?><?php } ?>&nbsp;<?php _e('on', TEMPLATE_DOMAIN); ?>&nbsp;<?php the_time('F jS Y') ?></small>
</li>
<?php endwhile; ?>
<?php
echo "</ul>";
echo $after_widget;
// end echo result
}


function update($new_instance, $old_instance) {
//update and save the widget
return $new_instance;
}
function form($instance) {
// Get the options into variables, escaping html characters on the way
$feat_title = $instance['title'];
$feat_name = $instance['featcatname'];
$feat_thumb = $instance['featthumb'];
$feat_total = $instance['feattotal'];
?>


<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:",TEMPLATE_DOMAIN); ?> <em><?php _e("*required",TEMPLATE_DOMAIN); ?></em></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $feat_title; ?>" />
</p>

<p><label for="<?php echo $this->get_field_id('featcatname'); ?>"><?php _e("Category ID:",TEMPLATE_DOMAIN); ?><br /><em><?php _e("*separate by commas [,]",TEMPLATE_DOMAIN); ?></em> </label>
<input type="text" class="widefat" id="<?php echo $this->get_field_id('featcatname'); ?>" name="<?php echo $this->get_field_name('featcatname'); ?>" value="<?php echo $feat_name; ?>" />
</p>

<p><label for="<?php echo $this->get_field_id('featthumb'); ?>"><?php _e('Enable Thumbnails?:<br /><em>*post featured images</em>', TEMPLATE_DOMAIN); ?>    </label>
<select class="widefat" id="<?php echo $this->get_field_id('featthumb'); ?>" name="<?php echo $this->get_field_name('featthumb'); ?>">
<option<?php if($feat_thumb == 'yes') { echo " selected='selected'"; } ?> name="<?php echo $this->get_field_name('featthumb'); ?>" value="yes"><?php _e('yes', TEMPLATE_DOMAIN); ?></option>
<option<?php if($feat_thumb== 'no') { echo " selected='selected'"; } ?> name="<?php echo $this->get_field_name('featthumb'); ?>" value="no"><?php _e('no', TEMPLATE_DOMAIN); ?></option>
</select>
</p>


<p><label for="<?php echo $this->get_field_id('feattotal'); ?>"><?php _e("Total:",TEMPLATE_DOMAIN); ?></label> <br />
<input class="widefat" id="<?php echo $this->get_field_id('feattotal'); ?>" name="<?php echo $this->get_field_name('feattotal'); ?>" type="text" value="<?php echo $feat_total; ?>" />
</p>

<?php
}
}
register_widget('My_WPMUDEV_Featured_Multi_Category_Widget');



function wp_add_widget_style_head() {
print "<style type='text/css' media='screen'>"; ?>
ul#recent-postcat li img {
margin: 0px 10px 0px 0px!important;
border: 1px solid #ddd;
padding: 3px;
float:left;
max-width: 80px;
width: auto;
height:auto;
background: #fff;
}
#recent-postcat li div.feat-title {
  font-size: 1em; padding-bottom: 0px; margin: 6px 0px 0px 0px;
}
#recent-postcat li small {
  font-size: 0.85em;
}
ul#flickr-widget li img {
  float: left;
  margin: 0px 7px 8px 0px;
  background-color: #fff;
  border: 1px solid #eee;
  padding: 5px;
}
<?php print "</style>";
}
add_action('wp_head','wp_add_widget_style_head');


?>