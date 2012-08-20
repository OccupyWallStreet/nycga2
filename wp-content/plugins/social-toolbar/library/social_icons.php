<script type="text/javascript">
  // When the document is ready set up our sortable with it's inherant function(s)
 jQuery(document).ready(function($) {
   $(":checkbox").click(function(){
		 var n = $("input:checked").length;
		if(n>14)
		{
		  alert('Only 14 icons can be displayed');
		  $(this).attr('checked', false);
		}
	
	});
    $("#social_toolbar_icon_list").sortable({
      handle : '.handle',
      update : function () {
		  var order = $('#social_toolbar_icon_list').sortable('serialize');
  		$("#info").load("<?php echo DD_SOCIAL_TOOLBAR_PATH;?>/library/social_icons_values.php?"+order);
      }
    });
});
</script>
<table cellspacing="5" cellpadding="5" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col" colspan="2"><?php _e('Social Account Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
<tr>
<td colspan="2">
			<input type="hidden" name="wpst_hidden_icon_profiles" value="UPDATED" />
			
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
</td>
</tr>
<style type="text/css">
#social_toolbar_icon_list,.pro_social_list { list-style: none; }
#social_toolbar_icon_list li,.pro_social_list li { display: block; padding: 10px 10px; margin-bottom: 3px; background-color: #efefef; border:1px solid #cccccc; }
#social_toolbar_icon_list li span,.pro_social_list li span { width:120px; padding:0px 20px 0px 0px; font-weight:bold; float:left; text-transform:capitalize; }
#social_toolbar_icon_list li img.handle ,.pro_social_list li img.handle { margin-right: 20px; cursor: move; float:left; }
.pro_social_list li { background:#e2e2e2; border-color:#bbbbbb;}
</style>
<tr>
<td colspan="2">
<?php
$social_icons=get_option('SOCIALTOOLBARICONS');
global $DDST_Profiles;
/* This code only runs when new icons added to the plugin */
if(count($social_icons)<count($DDST_Profiles))
{
	for($i=count($social_icons);$i<count($DDST_Profiles);$i++)
	{
		$social_icons[$i]=$DDST_Profiles[$i];
	}
	update_option('SOCIALTOOLBARICONS', $social_icons);
}

$DDOptions=get_option('SOCIALTOOLBAROPTIONS');
$social_icons=DDST_aasorting($social_icons,"order");
?>
			<ul id="social_toolbar_icon_list">
			<?php 
			while (list($key, $value) = each($social_icons)) 
			{
			$checked = $value['enable'] ? "checked" : ""; 
			if(strtolower($value['name'])=='googleplus')
			{
				$value['name']='google+';
			}
			if(strtolower($value['name'])== 'google+')
			{
				$bgimage=DD_SOCIAL_TOOLBAR_PATH.'images/'.$DDOptions['icon_type'].'/googleplus.png';
			}
			else
			{
				$bgimage=DD_SOCIAL_TOOLBAR_PATH.'images/'.$DDOptions['icon_type'].'/'.strtolower($value['name']).'.png';
			}
			?>
			<li id="listItem_<?php echo $key; ?>" style="background-image:url(<?php echo $bgimage;?>); background-repeat:no-repeat; background-position:right  5px;"><img src="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>images/arrow.png" alt="move" width="16" height="16" class="handle" />
			<span><?php _e($value['name'].': ','WPSOCIALTOOLBAR'); ?></span><input type="text"  name="social_profile[<?php echo $key;?>]" value="<?php echo $value['url']; ?>" size="40" />
			<input type="checkbox" name="social_toolbar_enable[]" value="<?php echo $key;?>" <?php echo $checked;?> />
			<?php 
			$social_website=strtolower($value['name']);	
			if($social_website=='twitter' || $social_website=='skype'):?>
			<p style="text-align:left;padding-left:40px;"><span>&nbsp;</span><small><?php _e('( Just Enter '.$value['name'].' Username )','WPSOCIALTOOLBAR'); ?></small></p>
			<?php endif; ?>
			<?php 
			if($social_website=='email'):?>
			<p style="text-align:left;padding-left:40px;"><span>&nbsp;</span><small><?php _e('( e.g. mailto:yourname@example.com )','WPSOCIALTOOLBAR'); ?></small></p>
			<?php endif; ?>
			</li>
			<?php
			}
		
			?>
			
</ul>
<?php
$pro_profiles=array(
'apple','bebo','Dribble','foursquare','hi5','iLike','ning','ping','reverbnation','Skype','Lastfm','MeetUp','Orkut','StumbleUpon','Digg','Tumblr','Xing','Beatport','SoundCloud','Spotify','Behance','BlinkList','Current','Delicious','DesignFloat','Designmoo','DeviantArt','Diigo','DZone','Email','Fark','Formspring','FriendFeed','Google+','GrooveShark','Klout','LoveDSGN','MisterWong','Yahoo','Netvouz','Newsvine','PingFM','PlayStationNetwork','Posterous','Reddit','ShareThis','Technorati','Tout','Wanttt','XBoxLive','Yelp','Zootool','Aim','Flout','Forrst','GitHub','Gowalla','Rdio','allrecipes','designersmx','pinterest','punchfork','instagram','thewebblend');

?>
<ul class="pro_social_list social_basic">
<?php foreach($pro_profiles as $pro ): ?>
<?php 
if($pro=='Google+')
{
	$pro_img="googleplus";
	$pro_name='google+';
}
else
{
	$pro_img=strtolower($pro); 
	$pro_name=$pro_img;
}

$pro_image_path=DD_SOCIAL_TOOLBAR_PATH.'images/'.$DDOptions['icon_type'].'/'.$pro_img.'.png';
?>
<li id="listItem_<?php echo $pro_img; ?>" style="background-image:url(<?php echo $pro_image_path;?>); background-repeat:no-repeat; background-position:right  5px;"><img src="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>images/arrow.png" alt="move" width="16" height="16" class="handle" />
			<span><?php _e($pro_name.' : ','WPSOCIALTOOLBAR'); ?></span><input type="text" class="social_basic"  name="socialtoolbar_profile[<?php echo $pro_img;?>]" value="" size="40" />
			<input type="checkbox" name="social_toolbar_pro[]" class="social_basic" value="<?php echo $pro_img;?>"  />
			<small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small>
</li>
<?php endforeach;?>
</ul>
</td>
</tr>
<tr>
<td colspan="2">
			<input type="hidden" name="wpst_hidden_icon_profiles" value="UPDATED" />
			
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
</td>
</tr>
</table>