<?php

class LikeDisablePage
{
	
	public function DisablePages()
	{
		
		if(function_exists('add_meta_box'))
		{
			add_meta_box('fb-like', 'Facebook Like Options', array('LikeDisablePage', 'LikeBoxLayout'), 'page', 'side', 'default', null );
			
			}else
			
			{
				    add_action('dbx_page_advanced', array('LikeDisablePage', 'LikeBoxLayout'));
				
				}
			
		if(function_exists('add_meta_box'))
		{
			add_meta_box('fb-like', 'Facebook Like Options', array('LikeDisablePage', 'LikeBoxLayout'), 'post', 'side', 'default', null );
			
			}else
			
			{
				    add_action('dbx_page_advanced', array('LikeDisablePage', 'LikeBoxLayout'));
				
				}
		
		
		
		
		}
	
	
		
	public function LikeBoxLayout()
	{

		$Disable_Status = get_option('disable_like_status_'.$_GET['post']);
		$PageType = get_option('disable_like_pagetype_'.$_GET['post']);
		$PageType = ($PageType != '') ? $PageType : 'chose';
		$Check_Status = ($Disable_Status == true) ? 'CHECKED' : '';
		echo '		
		<table>
		   <tr>
		   	<td>Check To Disable:</td>
		   	<td> <input type="checkbox" name="disable_like_button" id="disable_like_check" '.$Check_Status.'/></td>
		   </tr>
		</table>
		<table>
		 <tr>
		    <td>Page Type:</td>
		    <td>
		     <select name="fb_page_type" id="fb_page_type"><option value="chose">Chose a type</option><option value="activity">activity</option><option value="actor">actor</option><option value="album">album</option><option value="article">article</option><option value="athlete">athlete</option><option value="author">author</option><option value="band">band</option><option value="bar">bar</option><option value="blog">blog</option><option value="book">book</option><option value="cafe">cafe</option><option value="cause">cause</option><option value="city">city</option><option value="company">company</option><option value="country">country</option><option value="director">director</option><option value="drink">drink</option><option value="food">food</option><option value="game">game</option><option value="government">government</option><option value="hotel">hotel</option><option value="landmark">landmark</option><option value="movie">movie</option><option value="musician">musician</option><option value="non_profit">non_profit</option><option value="politician">politician</option><option value="product">product</option><option value="public_figure">public_figure</option><option value="restaurant">restaurant</option><option value="school">school</option><option value="song">song</option><option value="sport">sport</option><option value="sports_league">sports_league</option><option value="sports_team">sports_team</option><option value="state_province">state_province</option><option value="tv_show">tv_show</option><option value="university">university</option><option value="website">website</option></select>
		    </td>
		   </tr>
		</table>
		';
		
		}
	
	public function DoLikeDisable($page_id)
	{
		
		$Check_Status = $_POST['disable_like_button'];
		$PageType     = $_POST['fb_page_type'];
		add_option('disable_like_status_'.$page_id, '');
		add_option('disable_like_pagetype_'.$page_id, '');
		
		if($Check_Status == 'on')
		{
			update_option('disable_like_status_'.$page_id, true);
			
			}
		
		else
		{
			update_option('disable_like_status_'.$page_id, false);
			
			}

		if($PageType != "chose")
		{
			update_option('disable_like_pagetype_'.$page_id, $PageType);
		}
		else
			{
				update_option('disable_like_pagetype_'.$page_id, "article");
			}
		
		}
	
	}
	
	
add_action('admin_menu', array('LikeDisablePage', 'DisablePages'));
add_action('pre_post_update',  array('LikeDisablePage', 'DoLikeDisable'));


?>