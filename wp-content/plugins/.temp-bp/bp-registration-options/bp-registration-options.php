<?php
//ADD BUDDYPRESS MENU ITEM FOR 'Registration Options'

function bprwg_admin_menu() {

	if ( !is_site_admin() )

		return false;

	add_submenu_page( 'bp-general-settings', __( 'Registration Options', 'bprwg'), __( 'Registration Options', 'bprwg' ), 'administrator', 'bp-registration-options', 'bprwg_admin_screen' );

}

add_action( 'admin_menu', 'bprwg_admin_menu' );



//ADMIN SETTINGS PAGE

function bprwg_admin_screen() {

	global $wpdb, $bp, $iprefix;
	
	switch_to_blog(1);

	echo"<h2>BuddyPress Registration Options</h2>";

	$get_groups=get_option('bprwg_groups');

	$bp_groups = explode(',', $get_groups);

	$get_blogs=get_option('bprwg_blogs');

	$bp_blogs = explode(',', $get_blogs);

	$bp_moderate=get_option('bprwg_moderate');

	if($_POST['save_privacy']!=""){

		check_admin_referer('cro_check');

		$privacy_network=$_POST['privacy_network'];

		update_option('bprwg_privacy_network', $privacy_network);

		if($privacy_network){

			$privacy_network_exceptions=$_POST['privacy_network_exceptions'];

		}

		update_option('bprwg_privacy_network_exceptions', $privacy_network_exceptions);

		$privacy_profiles_limit=$_POST['privacy_profiles_limit'];

		update_option('bprwg_privacy_profiles_limit', $privacy_profiles_limit);

		$privacy_profile_views=$_POST['privacy_profile_views'];

		update_option('bprwg_privacy_profile_views', $privacy_profile_views);

	}elseif($_POST['Save']!=""){



		//nonce WP security check

		check_admin_referer('cro_check');



		//Save Options

		$bp_groups=$_POST['bp_groups'];

		if($bp_groups){

			$bp_groups_str = implode(",", $bp_groups);

		}

		update_option('bprwg_groups', $bp_groups_str);

		$bp_blogs=$_POST['bp_blogs'];

		if($bp_blogs){

			$bp_blogs_str = implode(",", $bp_blogs);

		}

		update_option('bprwg_blogs', $bp_blogs_str);

		$bp_moderate=$_POST['bp_moderate'];

		update_option('bprwg_moderate', $bp_moderate);

		$activate_message=$_POST['activate_message'];

		update_option('bprwg_activate_message', $activate_message);

		$approved_message=$_POST['approved_message'];

		update_option('bprwg_approved_message', $approved_message);

		$denied_message=$_POST['denied_message'];

		update_option('bprwg_denied_message', $denied_message);

	}

	if($_POST['reset_messages']!=""){

		delete_option('bprwg_activate_message');

		delete_option('bprwg_approved_message');

		delete_option('bprwg_denied_message');

	}

	//Menu

	$view=$_GET['view'];?>

	<a <?php if ($view==""){?>style="font-weight:bold;"<?php } ?> href="<?php echo add_query_arg ('view', '');?>">Registration Settings</a> |

    <a <?php if ($view=="privacy"){?>style="font-weight:bold;"<?php } ?> href="<?php echo add_query_arg ('view', 'privacy');?>">Privacy Settings</a> <!-- |

    <a <?php if ($view=="spam"){?>style="font-weight:bold;"<?php } ?> href="<?php echo add_query_arg ('view', 'spam');?>">Spam Settings</a> |

        <a <?php if ($view=="mapping"){?>style="font-weight:bold;"<?php } ?> href="<?php echo add_query_arg ('view', 'mapping');?>">WP Site/BP Group Mapping</a>-->

    <?php 

	//ADMIN MODERATION ACTION*********************************************

	if($bp_moderate=="yes"){

		//Moderation Actions*******************************************

		if ($view=="members" && $_POST['Moderate']!=""){

			$moderate_action=$_POST['Moderate'];

			$bp_member_check=$_POST['bp_member_check'];

			if($moderate_action=="Approve" && $bp_member_check!=""){

				$groups="";

				foreach ($bp_groups as $value) {

    				$groups=$groups.",".$value;

				}

				for ($i = 0; $i < count($bp_member_check); ++$i) {

					$userid=$bp_member_check[$i];

					$user_info = get_userdata($userid);

					$username=$user_info->user_login;

					$useremail=$user_info->user_email;
					
					//update any requested private groups

					if ($groups!=""){

						$group_email="";

						$groups="00".$groups;



						$db_result = $wpdb->get_results( "select a.id,a.name,b.slug from ".$iprefix."bp_groups a, ".$iprefix."bp_groups_members b where a.id=b.group_id and b.user_id=$userid and a.id in (".$groups.") and a.status in ('semi','private') and b.is_confirmed=0" );

						if ( count( $db_result ) > 0 ) {

							foreach( $db_result as $the_db_result ) {								

								$group_id = $the_db_result->id;

								$group_name = $the_db_result->name;

								$group_slug = $the_db_result->slug;

								$group_radio=$_POST["usergroup_".$userid."_".$group_id];

								if ($group_radio=="approve"){

									$sql="update ".$iprefix."bp_groups_members set is_confirmed=1 where group_id=$group_id and user_id=$userid";

									$wpdb->query($wpdb->prepare($sql));

									$group_email.="You have been accepted to the group [".$group_name."] - ".get_bloginfo("url")."/groups/".$slug."/.\n\n";

								}elseif ($group_radio=="deny"){

									$sql="delete from ".$iprefix."bp_groups_members where group_id=$group_id and user_id=$userid";

									$wpdb->query($wpdb->prepare($sql));

									$group_email.="Sorry but you were not accepted to the group [".$group_name."] - ".get_bloginfo("url")."/groups/".$slug."/.\n\n";

								}elseif ($group_radio=="ban"){

									$sql="update ".$iprefix."bp_groups_members set is_banned=1 where group_id=$group_id and user_id=$userid";

									$wpdb->query($wpdb->prepare($sql));

									$group_email.="Sorry but you were not accepted to the group [".$group_name."] - ".get_bloginfo("url")."/groups/".$slug."/.\n\n";

								}

							}

						}

					}

					update_usermeta($userid, 'bprwg_status', 'approved');

					$sql="update ".$iprefix."users set deleted=0 where ID=$userid";

					$wpdb->query($wpdb->prepare($sql));

					//update bp activity

					$sql="update ".$iprefix."bp_activity set hide_sitewide=0 where user_id=$userid";

					$wpdb->query($wpdb->prepare($sql));

					//email member with custom message

					$approved_message=get_option('bprwg_approved_message');

					$the_email=$approved_message;

					$the_email=str_replace("[username]",$username,$the_email);

					//$the_email="Hi ".$username.",\n\nYour member account on ".get_bloginfo("url")." has been approved! You can now login and start interacting with the rest of the community...";

					if($group_email!=""){

						$the_email.="\n\nThe following information pertains to each group you requested to join:\n\n".$group_email;

					}

					wp_mail($useremail, 'Membership Approved', $the_email);

				}

			//DENY OR BAN MEMBERS

			}elseif($moderate_action=="Deny" && $bp_member_check!="" || $moderate_action=="Ban" && $bp_member_check!=""){

				for ($i = 0; $i < count($bp_member_check); ++$i) {

					$userid=(int)$bp_member_check[$i];

					$user_info = get_userdata($userid);

					$username=$user_info->user_login;

					$useremail=$user_info->user_email;
					
					$wpdb->query( $wpdb->prepare("DELETE FROM ".$iprefix."usermeta WHERE user_id = %d", $userid) );

					$wpdb->query( $wpdb->prepare("DELETE FROM ".$iprefix."users WHERE ID = %d", $userid) );

					$wpdb->query( $wpdb->prepare("DELETE FROM ".$iprefix."bp_activity WHERE user_id = %d", $userid) );
					
					$wpdb->query( $wpdb->prepare("DELETE FROM ".$iprefix."bp_groups_members WHERE user_id = %d", $userid) );
					
					if($moderate_action=="Deny" && $bp_member_check!=""){
						//email member with custom message
						
						$denied_message=get_option('bprwg_denied_message');
	
						$the_email=$denied_message;
	
						$the_email=str_replace("[username]",$username,$the_email);
	
						wp_mail($useremail, 'Membership Denied', $the_email);
					}
				
				}

			}

		}



		$db_result_u = $wpdb->get_results( "Select a.* from ".$iprefix."users a LEFT OUTER JOIN ".$iprefix."usermeta b on a.ID=b.user_id where b.meta_key='bprwg_status' and meta_value<>'approved' and meta_value<>'denied' order by a.ID" );

		$members_count = count( $db_result_u );

?>

    	 |

        <a <?php if ($view=="members"){?>style="font-weight:bold;"<?php } ?> href="<?php echo add_query_arg ('view', 'members');?>">New Member Requests (<?php echo $members_count;?>)</a>

    <?php }

	

//ADMIN SETTINGS PAGE FORM*********************************************?>

    <hr>

    <?php 

	if($moderate_action=="Approve" && $bp_member_check!=""){

		echo "<div id=message class=updated fade>Checked Members Approved!</div>";

	}elseif($moderate_action=="Deny" && $bp_member_check!=""){

		echo "<div id=message class=updated fade>Checked Members Denied and Deleted!</div>";

	}elseif($_POST['Moderate']!=""){

		echo "<div id=message class=updated fade>Please check at least 1 checkbox before pressing an action button!</div>";

	}elseif($_POST['Save']!="" || $_POST['save_privacy']!=""){

		echo "<div id=message class=updated fade>Settings Saved!</div>";

	}

	?>

    <form name="bprwg" method="post">

    <?php

    if ( function_exists('wp_nonce_field') ) wp_nonce_field('cro_check');



    if ($view==""){

		$activate_message=get_option('bprwg_activate_message');

		if ($activate_message==""){

			$activate_message="<strong>Your membership account is awaiting approval by the site administrator.</strong> You will not be able to fully interact with the social aspects of this website until your account is approved. Once approved or denied you will receive an email notice.";

		}

		$approved_message=get_option('bprwg_approved_message');

		if ($approved_message==""){

			$approved_message="Hi [username],\n\nYour member account on ".get_bloginfo("url")." has been approved! You can now login and start interacting with the rest of the community...";

		}

		$denied_message=get_option('bprwg_denied_message');

		if ($denied_message==""){

			$denied_message="Hi [username],\n\nWe regret to inform you that your member account on ".get_bloginfo("url")." has been denied...";

		}

		?>

       	&nbsp;<input type="checkbox" id="bp_moderate" name="bp_moderate" onclick="show_messages();" value="yes"  <?php if($bp_moderate=="yes"){?>checked<?php }?>/>&nbsp;<strong>Moderate New Members</strong> (Every new member will have to be approved by an administrator.)<br />

       	<div id="bp_messages" style="display:none;">

        	<table>

            <tr>

           		<td align="right" valign="top">Activate & Profile Alert Message:</td>

            	<td><textarea name="activate_message" style="width:500px;height:100px;"><?php echo $activate_message;?></textarea></td>

            </tr>

            <tr>

           		<td align="right" valign="top">Account Approved Email:</td>

            	<td><textarea name="approved_message" style="width:500px;height:100px;"><?php echo $approved_message;?></textarea></td>

            </tr>

            <tr>

           		<td align="right" valign="top">Account Denied Email:</td>

            	<td><textarea name="denied_message" style="width:500px;height:100px;"><?php echo $denied_message;?></textarea></td>

            </tr>

            <tr>

            	<td></td>

                <td align="right">

                	<table width="100%">

                    <tr>

                    	<td>Short Code Key: [username]</td>

                        <td align="right"><input type="submit" name="reset_messages" value="Reset Messages" onclick="return confirm('Are you sure you want to reset to the default messages?');" /></td>

                    </tr>

                    </table>

                </td>

            </tr>

            </table>

        </div>

        <script>

	   function show_messages(){

	   		if(document.getElementById('bp_moderate').checked == true){

	   			document.getElementById('bp_messages').style.display='';

  			}else{

				document.getElementById('bp_messages').style.display='none';

			}

		}

		<?php if($bp_moderate=="yes"){

			echo "document.getElementById('bp_messages').style.display='';";

		}?>

	   </script>

<?php //**************************************************

//MANAGE GROUPS AND BLOGS?>

        <hr>

       	<strong>Check groups and/or sites members can join at registration</strong><br />

        <table>

        <tr>

        <td valign="top">

            <table>

            <tr>

                <td><strong>BuddyPress Groups:</strong></td>

            </tr>

            <?php 

			if(!is_array($bp_groups)){

				$bp_groups=array(0);

			}

			$db_result = $wpdb->get_results( "SELECT id,name FROM ".$iprefix."bp_groups where name <>'' order by name" );

            if ( count( $db_result ) > 0 ) {

				foreach( $db_result as $the_db_result ) {

?>

                    <tr>

                        <td>

                            <input type="checkbox" name="bp_groups[]" value="<?php echo $the_db_result->id;?>"  <?php if(in_array($the_db_result->id, $bp_groups)){?>checked<?php }?>/>&nbsp;<?php echo $the_db_result->name;?>

                        </td>

                    </tr>

<?php

				}

			}



            ?>

            </table>

        </td>

        <td width="100px"></td>

        <td valign="top">

            <table>

            <tr>

                <td><strong>WP Multi-Sites:</strong></td>

            </tr>

            <?php

			if(WP_ALLOW_MULTISITE==1){

				if(!is_array($bp_blogs)){

					$bp_blogs=array(0);

				}

				$db_result = $wpdb->get_results( "SELECT blog_id,path FROM ".$iprefix."blogs order by path" );

				if ( count( $db_result ) > 0 ) {

					foreach( $db_result as $the_db_result ) {

											

						$db_result_n = $wpdb->get_results( "SELECT option_value FROM ". $iprefix . $the_db_result->blog_id . "_options where option_name='blogname'" );

						if ( count( $db_result_n ) > 0 ) {

							foreach( $db_result_n as $the_db_result_n ) {

	?>

						<tr>

							 <td>

								<input type="checkbox" name="bp_blogs[]" value="<?php echo $the_db_result->blog_id;?>" <?php if($bp_blogs!=""){if( in_array( $the_db_result->blog_id, $bp_blogs) ) {?>checked<?php }} ?>/>&nbsp;<?php echo $the_db_result_n->option_value;?>

							</td>

						</tr>

	<?php	

							}

						}                 

					}

				

				} 

			}else{ ?>

				<tr><td>You do not have Multi-Site enabled. Refer to <a href="http://codex.wordpress.org/Create_A_Network">http://codex.wordpress.org/Create_A_Network</a> if you would like to set it up.</td></tr>

			<?php }?>

            </table>

        </td>

        </tr>

        </table>

        <br />

        <input type="submit" name="Save" value="Save Options" />

    <?php

//Spam**********************************

	}elseif($view=="spam"){ 

		?>

        <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> <strong>Require members upload a real photo.</strong><br />

        <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> <strong>Use a honey pot system.</strong><br />

        <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> Ban honey pot violators automatically.<br />

        <strong>When a member is banned:</strong><br />

        <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> Ban them with a cookie.<br />

        <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> Ban them by their IP address.<br />

        <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> Ban them by their email address (person@whatever.com).<br />

         <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> Ban them from viewing the website, violators will be redirected to a 404 page.<br />

        <input type="submit" name="save_spam" value="Save" />

        <?php 

//Privacy**********************************


	}elseif($view=="privacy"){ 

		$privacy_network=get_option('bprwg_privacy_network');

		if ($privacy_network){

			$privacy_network="checked";

		}

		$privacy_network_exceptions=get_option('bprwg_privacy_network_exceptions');

		$privacy_profiles_limit=get_option('bprwg_privacy_profiles_limit');

		if ($privacy_profiles_limit){

			$privacy_profiles_limit="checked";

		}

		$privacy_profile_views=get_option('bprwg_privacy_profile_views');

		if($privacy_profile_views==""){

			$privacy_profile_views=5;

		}

		$private_profiles=get_option('private_profiles');

		if ($private_profiles){

			$private_profiles="checked";

		}

		?>

        <input type="checkbox" name="privacy_network" value="1" <?php echo $privacy_network;?>/> Only registered or approved members can view BuddyPress pages (Private Network).

        <table>

        	<tr>

            <td style="width:25px !important;"></td>

            <td>

            	The following BP pages are an exception to above:<br />

				<?php $is_bp_dir = $bp->root_components;

				foreach ($is_bp_dir as $value) {

					if($value!="register" && $value!="search"  && $value!="activate"){

						?>

                            <input type="checkbox" name="privacy_network_exceptions[]" value="<?php echo $value;?>" <?php if(is_array($privacy_network_exceptions)){if(in_array($value,$privacy_network_exceptions)){echo"checked";}}?>/> <?php echo ucfirst($value);?><br />

                        <?php

					}

				}

			

				?>

            </td>

            </tr>

        </table>

        <!--<input type="checkbox" name="privacy_profiles_limit" value="1" <?php echo $privacy_profiles_limit;?> /> Public can only view BuddyPress profiles <input type="text" name="privacy_profile_views" value="<?php echo $privacy_profile_views;?>" style="width:20px;" /> times before being forced to register.<br />

        <input type="checkbox" name="private_profiles" value="1" <?php echo $private_profiles;?>/> Give members an option to make their profile viewable by their friends only.<br />-->

        <input type="submit" name="save_privacy" value="Save" />

        <?php

//MAPPING

	}elseif($view=="mapping"){ ?>

		<!--You can map WP Blogs to BP Groups so when a new member joins one they are automatically tied to the other. An example would be if you map Group A to Blog A, when a user joins Group A at registration they will automatically join Blog A or if a user joins Blog A they will automatically join Group A.-->

        Map blogs to blogs and/or groups and map groups to groups and/or blogs.<br /><br />

        <table>

        <tr>

        	<td><strong>If some one joins:</strong></td>

            <td></td>

            <td><strong>They will automatically join:</strong></td>

        </tr>

        <tr>

        	<td>

                <select name="user_join">

                	<?php //groups

					$db_result = $wpdb->get_results( "SELECT id,name FROM ".$iprefix."bp_groups order by name" );

					if ( count( $db_result ) > 0 ) {

						foreach( $db_result as $the_db_result ) {?>

							<option value="<?php echo $the_db_result->id;?>">BP Group - <?php echo $the_db_result->name;?>

						<?php

						}

					}

					//blogs

					$db_result = $wpdb->get_results( "SELECT blog_id,path FROM ".$iprefix."blogs order by path" );

					if ( count( $db_result ) > 0 ) {

						foreach( $db_result as $the_db_result ) {

							$db_result_n = $wpdb->get_results( "SELECT option_value FROM ". $iprefix . $the_db_result->blog_id . "_options where option_name='blogname'" );

							if ( count( $db_result_n ) > 0 ) {

								foreach( $db_result_n as $the_db_result_n ) {?>

									<option type="checkbox" value="<?php echo $the_db_result->blog_id;?>">WP Blog - <?php echo $the_db_result_n->option_value;?>

								<?php	

								}

							}                 

						}

					

					}?>

                </select>

            </td>

            <td>

            	<select name="code_join">

            

            	</select>

            </td>

        </tr>

        </table>

        

        

	<?php }else{

		///New members requests*********************************************************

		if ($members_count > 0) { ?>

            Please approve or deny the following new members:

            <SCRIPT LANGUAGE="JavaScript">

			function bprwg_checkall(field){

				if(document.getElementById('bp_checkall').checked == true){

					checkAll(field)

				}else{

					uncheckAll(field)

				}

			}

			function checkAll(field)

			{

			for (i = 0; i < field.length; i++)

				field[i].checked = true ;

			}

			

			function uncheckAll(field)

			{

			for (i = 0; i < field.length; i++)

				field[i].checked = false ;

			}



			</script>

            <table cellpadding="3" cellspacing="3">

            <tr>

            	<td><input type="checkbox" id="bp_checkall" onclick="bprwg_checkall(document.bprwg.bp_member_check);" name="checkall" /></td>

                <td><strong>Photo</strong></td>

                <td><strong>Name</strong></td>

                <?php

				$groups="";

				foreach ($bp_groups as $value) {

    				$groups=$groups.",".$value;

				}

				if ($groups!=""){

					$groups="00".$groups;?>

                	<td><strong>Requested Private Groups</strong></td>

                <?php } ?>

            	<td><strong>Email</strong></td>

                <td><strong>Created</strong></td>

                <td><strong>IP Data</strong></td>

            </tr>

<?php



			// We reuse the query from line 138

			foreach( $db_result_u as $the_db_result ) {	

				$user_id=$the_db_result->ID;

				$author = new BP_Core_User( $user_id );

				//$userpic=$author->avatar;

				$userpic=$author->avatar_mini;

				$userlink=$author->user_url;

				$username=$author->fullname;

				//$userpic=bp_core_get_avatar( $user_id, 1 );

				$useremail=$the_db_result->user_email;

				$userregistered=$the_db_result->user_registered;

				$userip = get_user_meta( $user_id, 'bprwg_ip_address', true); 

				if($bgc==""){

					$bgc="#ffffff";

				}else{

					$bgc="";

				}

?>

				<tr style="background:<?php echo $bgc;?> !important;">

                    <td valign="top"><?php //echo $user_id; ?><input type="checkbox" id="bp_member_check" name="bp_member_check[]" value="<?php echo $user_id; ?>"  /></td>

                    <td valign="top"><a target="_blank" href="<?php echo $userlink; ?>"><?php echo $userpic?></a></td>

                    <td valign="top"><strong><a target="_blank" href="<?php echo $userlink; ?>"><?php echo $username?></a></strong></td>

                    <?php if ($groups!=""){ ?>

                    <td valign="top">

<?php 



						$db_result = $wpdb->get_results( "select a.id,a.name from ".$iprefix."bp_groups a, ".$iprefix."bp_groups_members b where a.id=b.group_id and b.user_id=$user_id and a.id in (".$groups.") and a.status in ('semi','private') and b.is_confirmed=0" );

						if ( count( $db_result ) > 0 ) {

							foreach( $db_result as $the_db_result ) {

								$group_id = $the_db_result->id;

								$group_name = $the_db_result->name;							

?>

                    			[<input checked="checked" type="radio" name="usergroup_<?php echo $user_id; ?>_<?php echo $group_id; ?>" value="approve" />Approve<input type="radio" name="usergroup_<?php echo $user_id; ?>_<?php echo $group_id; ?>" value="deny" />Deny<input type="radio" name="usergroup_<?php echo $user_id; ?>_<?php echo $group_id; ?>" value="ban" />Ban] <strong><?php echo $group_name; ?></strong><br />

<?php							

							}

						} else {

							echo "N/A";

						}



?>

                    </td>

                    <?php } ?>

                    <td valign="top"><a href="mailto:<?php echo $useremail;?>"><?php echo $useremail;?></a></td>

                    <td valign="top"><?php echo $userregistered;?></td>

                    <td valign="top">
						<table>
                        <tr>
                        <td valign="top">
						<?php echo '<img src="http://api.hostip.info/flag.php?ip=' . $userip . '" / >' ?>
                        </td>
                        <td valign="top">
							<?php 
                            $response = wp_remote_get( 'http://api.hostip.info/get_html.php?ip=' . $userip );
                            if(!is_wp_error( $response ) ) {
                                 $data = $response['body'];
								 $data = str_replace("City:","<br>City:",$data);
								 $data = str_replace("IP:","<br>IP:",$data);
								 echo $data;
                            }
                            ?>
                        </td>
                        </tr>
                        </table>
                    </td>

				</tr>

		<?php } ?>

            </table>

            

			<br />

            <input type="submit" name="Moderate" value="Approve" />

            <input type="submit" name="Moderate" value="Deny" onclick="return confirm('Are you sure you want to deny and delete the checked memeber(s)?');" />

            <input type="submit" name="Moderate" value="Ban" onclick="return confirm('Are you sure you want to ban and delete the checked memeber(s)?');" /><br /><br />

            *If you Ban a member they will not receive an email.

         <?php }else{

		 	echo "No new members to approve.";

		 }

    } ?>

    </form>

    <br />

    For support please visit the <a target="_blank" href="http://webdevstudios.com/support/forum/buddypress-registration-options/">BP-Registration-Options Plugin Support Forum</a> | Version by <a href="http://webdevstudios.com">WebDevStudios.com</a><br />

    <a target="_blank" href="http://webdevstudios.com/support/wordpress-plugins/">Check out our other plugins</a> and follow <a target="_blank" href="http://twitter.com/webdevstudios">@WebDevStudios</a> and <a target="_blank" href="http://twitter.com/bmess">@bmess</a> on Twitter

<?php }

//ACCOUNT ACTIVATION ACTIONS*******************************************************************

//ACCOUNT ACTIVATION ACTIONS*******************************************************************

//ACCOUNT ACTIVATION ACTIONS*******************************************************************

//ACCOUNT ACTIVATION ACTIONS*******************************************************************

//ACCOUNT ACTIVATION ACTIONS*******************************************************************

function bprwg_update_profile(){

	global $wpdb, $bp, $user_id, $blog_id, $iprefix;

	switch_to_blog(1);
	
	//Get Current User_ID

	//$userid = $bp->loggedin_user->id;

	//If not logged in they came from activation page

	if ($userid=="") {

		$key=$_GET['key'];

		if ($key!=""){

			if ( bp_account_was_activated() ) :

				$db_result = $wpdb->get_results( "select ID from ".$iprefix."users where user_activation_key='$key'" );

				if ( count( $db_result ) > 0 ) {

					foreach( $db_result as $the_db_result ) {

						$userid = $the_db_result->ID;

						$from_reg = 'yes';					

					}

				}

			endif;

		}

	}

	//Can only pass if user_id exists

	if ($userid!="") {

		$user_info = get_userdata($userid);

		$username=$user_info->user_login;

		$useremail=$user_info->user_email;

		//BLOGS

		$bp_blogs=get_option('bprwg_blogs');

		if($bp_blogs!=""){

			$userblogs=get_option('bprwg_newmember_blogs_'.$useremail);

			if($userblogs!=""){

				$arr_userblogs = explode(",", $userblogs);

				for($i = 0; $i < count($arr_userblogs); $i++){

					$blog_id = $arr_userblogs[$i];

					

					$db_result = $wpdb->get_results( "SELECT option_value FROM ".$iprefix.$blog_id."_options where option_name='default_role'" );

					if ( count( $db_result ) > 0 ) {

						foreach( $db_result as $the_db_result ) {

							$default_role = $the_db_result->option_value;

							add_user_to_blog($blog_id, $userid, $default_role);						

						}

					}

					

				}

			}

			delete_option('bprwg_newmember_blogs_'.$useremail);

		}

		//GROUPS

		$bp_groups=get_option('bprwg_groups');

		if($bp_groups!=""){

			$usergroups=get_option('bprwg_newmember_groups_'.$useremail);



            $db_result = $wpdb->get_results( "select id,status,name,slug from ".$iprefix."bp_groups where id in ($usergroups) order by name" );

            if ( count( $db_result ) > 0 ) {

				foreach( $db_result as $the_db_result ) {

					

					if( $the_db_result->status == 'semi' || $the_db_result->status == 'private' ) {

						$is_confirmed=0;

					} else {

						$is_confirmed=1;

					}

					//if not already in group then add to group





					$db_result_n = $wpdb->get_results( 'select id from ' . $iprefix . 'bp_groups_members where group_id=' . $the_db_result->id . ' and user_id=' . $userid );

					if ( count( $db_result_n ) == 0 ) {

						//add memebr to group and send group confirmation if need be

						$wpdb->query( $wpdb->prepare( 

							'insert into '. $iprefix . "bp_groups_members( group_id, user_id, inviter_id, user_title, date_modified, comments, is_confirmed ) values ( $the_db_result->id, $userid, 0, '', now(), '', $is_confirmed )"

						) );

						if ($is_confirmed==0){

							$group_email=$group_email.$username." wants to join the group [".$the_db_result->name."] - ".get_bloginfo("url")."/groups/".$the_db_result->slug."/admin/membership-requests.\n\n";

						}

					}



				

				}

			}

			

			delete_option('bprwg_newmember_groups_'.$useremail);

		}

		//for member moderation after member activation...

		if ($from_reg="yes"){

			$bp_moderate=get_option('bprwg_moderate');

			if ($bp_moderate=="yes"){

				//add/update usermeta status to activated

				update_usermeta($userid, 'bprwg_status', 'activated');

				update_usermeta($userid, 'bprwg_ip_address', $_SERVER['REMOTE_ADDR']);

	

				//update wp_users to deleted=1, this will prevent member from being listed in member directory but not actually delete them. once appoved will be updated back to 0, if denied will fully delete account

				$sql="update ".$iprefix."users set deleted=1 where ID=$userid";

				$wpdb->query($wpdb->prepare($sql));

				//fire off email to admin about new memebr with links to accept or reject

				$mod_email=$username." would like to become a member of your website, to accept or reject their request please go to ".get_bloginfo("url")."/wp-admin/admin.php?page=bp-registration-options&view=members \n\n";

			}

			//delete user_activation_key after activation

			$sql="update ".$iprefix."users set user_activation_key='' where ID=$userid";

			$wpdb->query($wpdb->prepare($sql));

		}

		//Send Emails for new member or request access to goups

		if($group_email!="" || $mod_email!=""){

			$the_email="";

			if($mod_email!=""){

				$the_email.=$mod_email;

			}

			if($mod_email!=""){

				$the_email.=$group_email;

			}

			$admin_email = get_option('admin_email');

			wp_mail($admin_email, 'New Member Request', $the_email);

		}

	}

}



//called from activation hook

function bprwg_activate($userid){

	global $wpdb, $bp, $blog_id, $iprefix;

	//get key from querystring and update user_activation_key in wp_users (has already been deleted on activation, put back in so we can grab it after bp activation stuff runs then delete it again)

	$key=$_GET['key'];

	$sql="update ".$iprefix."users set user_activation_key='$key' where ID=$userid";

	$wpdb->query($wpdb->prepare($sql));

	//Hide any by activity

	$sql="update ".$iprefix."bp_activity set hide_sitewide=1 where user_id=$userid";

	$wpdb->query($wpdb->prepare($sql));

}

//add_action( 'wpmu_activate_user', 'bprwg_activate');//Only works on MS



add_action( 'bp_core_activate_account', 'bprwg_activate');

add_filter('bp_before_activate_content', 'bprwg_update_profile');





//MODERATION - New member alert message and redirect*****************************

function bprwg_approve_message(){

	global $wpdb;

	//check if moderation is on

	$bp_moderate=get_option('bprwg_moderate');

	if ($bp_moderate=="yes"){

		$showit="yes";

		if(strrpos($_SERVER['REQUEST_URI'],"/activate")!== false){

			$showit="no";

			global $bp;

			$userid =  $bp->loggedin_user->id ;

			if ( bp_account_was_activated() ) :

				$showit="yes";

				$hidelogout="yes";

			endif;

		}

		if($showit=="yes"){

			$activate_message=get_option('bprwg_activate_message');

			echo '<div id="message" class="error"><p>'.$activate_message.'<br>';

			if($hidelogout!="yes"){

				echo '<a href="'.wp_logout_url( get_bloginfo("url") ).'" title="Logout">Logout</a>';

			}

			echo '</p></div>';

		}

	}

}



//INIT***************************************

function bprwg_redirect(){

	global $wpdb, $bp;

	//add/remove users to/from blogs

	if($_POST['Update_Blog']!=""){

		global $bp, $blog_id, $user_id, $iprefix;

		$userid =  $bp->loggedin_user->id ;

		if($userid!=""){

			$bp_blogs=get_option('bprwg_blogs');

			if($bp_blogs!=""){

				$bp_blogs_form=$_POST['bprwg_blogs'];

				//echo "bp_blogs_form:".$bp_blogs_form;

				$arr_bp_blogs = explode(",", $bp_blogs);

				for($i = 0; $i < count($arr_bp_blogs); $i++){

					$iblog_id = $arr_bp_blogs[$i];

					//echo $iblog_id."<br>";

					if($bp_blogs_form!=""){

						//if checked

						if(in_array($iblog_id, $bp_blogs_form)){

							//echo $iblog_id." checked<br>";

							//if already a member skip, else add

							$sql="SELECT meta_value FROM ".$iprefix."usermeta where meta_key='wp_".$iblog_id."_capabilities' and user_id=".$userid."";

							$db_result = $wpdb->get_results($sql);

							if ( count( $db_result ) > 0 ) {

								//echo "already joined";

							}else{

								$sql="SELECT option_value FROM ".$iprefix.$iblog_id."_options where option_name='default_role'";

								//echo $sql."<br>";

								$db_result = $wpdb->get_results($sql);

								if ( count( $db_result ) > 0 ) {

									foreach( $db_result as $the_db_result ) {

										$default_role = $the_db_result->option_value;

										//echo "default_role: ".$default_role."<br>";

										//echo "blog_id: ".$iblog_id."<br>";

										//echo "userid: ".$userid."<br>";

										add_user_to_blog($iblog_id, $userid, $default_role);						

									}

								}

							}

						//if not checked

						}else{

							$removethem="yes";

						}

					}else{

						$removethem="yes";

					}

					if ($removethem=="yes"){

						//echo $iblog_id." not checked<br>";

						$sql="SELECT meta_value FROM ".$iprefix."usermeta where meta_key='wp_".$iblog_id."_capabilities' and user_id=".$userid."";

						$db_result = $wpdb->get_results($sql);

						if ( count( $db_result ) > 0 ) {

							foreach( $db_result as $the_db_result ) {

								$default_role = $the_db_result->meta_value;

								//echo $default_role."<br>";

								if($default_role > 9){

									//$bprwg_error="?m=10";

									//echo $bprwg_error."<br>";

								}else{

									//echo "Remove blog<br>";

									//echo "userid:".$userid."<br>";

									//echo "blog_id:".$iblog_id."<br>";

									remove_user_from_blog($userid, $iblog_id);

								}

							}

						}

						$removethem="no";

					}

				}

				echo '<meta http-equiv="refresh" content="0;url='.$bprwg_error.'" />';

				exit();

			}

		}

	}

	

	//redirect from wp-signup.php

	if(strrpos($_SERVER['REQUEST_URI'],"/wp-signup.php")!== false ){

		$url=get_option('siteurl')."/register";

		wp_redirect($url, 301);

		//if for some reason wp_redirect isn't working.(maybe other plugins runing before with output)

		echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';

		exit();

	}

	

	$url = $_SERVER['REQUEST_URI'];

	$is_bp=$url;

	$is_bp=str_replace("?","/",$is_bp);

	$is_bp=substr($is_bp,0,strpos($is_bp,'/',1));

	if($is_bp==""){

		$is_bp=$url;

	}

	$is_bp=str_replace("/","",$is_bp);

	

	//redirect profile viewer X times to registation

	$privacy_profile=get_option('bprwg_privacy_profile_views');

	$profile_id=$bp->displayed_user->id;

	//echo "<hr>here: ".$profile_id."<hr>";

	if($privacy_profile!="" && $profile_id!="" && $bp->loggedin_user->id=="0"){

		$privacy_profiles_limit=get_option('bprwg_privacy_profiles_limit');

		$my_profile_view=6;

		if($my_profile_view >= $privacy_profiles_limit){

			$url=get_option('siteurl')."/register/?profile_views=true";

			wp_redirect($url);

			echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';

			exit();

		}

	}

	

	//Show Private network message on registration page

	$privacy_network=get_option('bprwg_privacy_network');

	if($privacy_network){

		add_filter('bp_before_container', 'bprwg_message_private_network');

	}

	$bp_moderate=get_option('bprwg_moderate');

	//check if moderation or private network is on

	if ($bp_moderate=="yes" || $privacy_network!=""){

		//only restrict buddypress pages

		$is_bp_dir = $bp->root_components;

		if (in_array($is_bp, $is_bp_dir) && $is_bp!="register" && $is_bp!="activate") {

			//check if logged in

			$userid =  $bp->loggedin_user->id;

			if ($userid!="" || $privacy_network!=""){

				if ($userid!=""){

					$user_info = get_userdata($userid);

					$username=$user_info->user_login;

					$url="/members/".$username."/profile/";

					//check if approved or grandfathered in (already had account when plugin activated)

					$status = get_usermeta($userid, 'bprwg_status');

					if($status!="approved" && $status!=""){

					//check if allowed buddypress pages

						if($url==$_SERVER['REQUEST_URI'] || strrpos($_SERVER['REQUEST_URI'],$url."change-avatar")!== false || strrpos($_SERVER['REQUEST_URI'],$url."edit")!== false || strrpos($_SERVER['REQUEST_URI'],"wp-login.php?action=logout")!== false){

							//add_filter('bp_before_profile_menu', 'bprwg_approve_message');

							add_filter('bp_before_container', 'bprwg_approve_message');

						}else{

							$url=get_option('siteurl').$url;

							wp_redirect($url);

							echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';//if for some reason wp_redirect isn't working.(maybe other plugins runing before with output)

							exit();

						}

					}

				}else{

					$privacy_network_exceptions=get_option('bprwg_privacy_network_exceptions');

					if(!is_array($privacy_network_exceptions)){

						$privacy_network_exceptions=array();	

					}

					if(!in_array($is_bp, $privacy_network_exceptions)){

						$url=get_option('siteurl')."/register/?private=true";

						wp_redirect($url);

						echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';

						exit();

					}

				}

			}

		}

	}

}

add_action('init', 'bprwg_redirect', -1);

add_filter('bp_after_activate_content', 'bprwg_approve_message');





function bprwg_message_private_network() {

    if($_GET['private']=='true'){

		echo '<div id="message" class="error"><p>This is a private network. You must register first before fully interacting with the rest of the community.';

    	echo '</p></div>';

	}

}



//ADMIN DASHBOARD MESSAGE******************************************************

function bprwg_admin_msg() {

	global $wpdb, $iprefix;
	
	switch_to_blog(1);
	//Delete any un-activated accounts over 7 days old
	$thedate=date('Y-m-d G:i:s');
	$oneWeekAgo = strtotime ( '-1 week' , strtotime ( $thedate ) ) ;
	$thedate=date ( 'Y-m-j G:i:s' , $oneWeekAgo );
	$db_result = $wpdb->get_results("Select user_email from ".$iprefix."signups where active=0 and registered < '".$thedate."'");
	if ( count( $db_result ) > 0 ) {
		foreach( $db_result as $the_db_result ) {
			$email=$the_db_result->user_email;
			$wpdb->get_results( "delete from ".$wpdb->prefix."options where option_name = 'bprwg_newmember_groups_".$email."' or option_name = 'bprwg_newmember_blogs_".$email."'" );
			$wpdb->get_results( "delete from ".$iprefix."signups where active=0 and user_email='".$email."'" );
		}
	}
	if (current_user_can('manage_options')){

		$bp_moderate=get_option('bprwg_moderate');

		if ($bp_moderate=="yes"){

			$db_result = $wpdb->get_results( "Select a.* from ".$iprefix."users a LEFT OUTER JOIN ".$iprefix."usermeta b on a.ID=b.user_id where b.meta_key='bprwg_status' and meta_value<>'approved' and meta_value<>'denied' order by a.ID" );

            if ( count( $db_result ) > 0 ) {

				

				if( count( $db_result ) != 1 ){

					$s = 's';

				}

				echo '<div class="error"><p>You have <a href="'.get_bloginfo("url").'/wp-admin/admin.php?page=bp-registration-options&view=members"><strong>'.count( $db_result ).' new member request'.$s.'</strong></a> that you need to approve or deny. Please <a href="'.get_bloginfo("url").'/wp-admin/admin.php?page=bp-registration-options&view=members">click here</a> to take action.</p></div>';				

            }

            

		}

	}

}

function bprwg_admin_init() {

	add_action('admin_notices', 'bprwg_admin_msg');

}

add_action( 'admin_init', 'bprwg_admin_init' );



//REGISTRATION FORM*******************************************************************************

function bprwg_register_page(){

	global $wpdb, $bp, $user_id, $blog_id, $iprefix;

	//GROUPS

	$bp_groups=get_option('bprwg_groups');

	if($bp_groups!=""){



		$db_result = $wpdb->get_results( "SELECT id,name FROM ".$iprefix."bp_groups where id in ($bp_groups) order by name" );

		if ( count( $db_result ) > 0 ) {

?>

			<div id="bp_registration-options-groups" class="register-section">

            <strong>Group(s)</strong><br />

			<?php foreach( $db_result as $the_db_result ): ?>

                <input type="checkbox" name="bprwg_groups[]" value="<?php echo $the_db_result->id; ?>" />&nbsp;<?php echo $the_db_result->name; ?>&nbsp;&nbsp;

            <?php endforeach; ?>

			<br />Check one or more groups you would like to join.</div>

<?php			

		}

	

	}

	//BLOGS

	$bp_blogs=get_option('bprwg_blogs');

	if($bp_blogs!=""){ 

?>

    	<div id="bp_registration-options-blogs">

        <strong>Blog(s)</strong><br />

<?php

		$arr_bp_blogs = explode(",", $bp_blogs);

		for($i = 0; $i < count($arr_bp_blogs); $i++){

			$blog_id = $arr_bp_blogs[$i];



            $db_result = $wpdb->get_results( "SELECT option_value FROM ".$iprefix.$blog_id."_options where option_name='blogname'" );

            if ( count( $db_result ) > 0 ) {

				foreach( $db_result as $the_db_result ) {

?>

					<input type="checkbox" name="bprwg_blogs[]" value="<?php echo $blog_id; ?>" />&nbsp;<?php echo $the_db_result->option_value; ?>&nbsp;&nbsp;

<?php

				}

			}

		}

		echo "<br />Check one or more blogs you would like to join.</div>";

	}

	//captcha

}

add_filter('bp_before_registration_submit_buttons', 'bprwg_register_page');



//REGISTRATION ACTIONS*************************************************************

function bprwg_register_save(){

	global $wpdb;

	switch_to_blog(1);

	$iemail=$_POST['signup_email'];

	//echo $iemail;

	$bp_groups=$_POST['bprwg_groups'];

	if($bp_groups!=""){

		$bp_groups_str = implode(",", $bp_groups);

		update_option('bprwg_newmember_groups_'.$iemail, $bp_groups_str);

	}

	$bp_blogs=$_POST['bprwg_blogs'];

	if($bp_blogs!=""){

		$bp_blogs_str = implode(",", $bp_blogs);

		update_option('bprwg_newmember_blogs_'.$iemail, $bp_blogs_str);

	}

	//exit();

}

add_action( 'bp_complete_signup', 'bprwg_register_save' );





//Member Profile Page (ADD BLOGS)*******************************************

function bprwg_blog_menu(){ 

	global $wpdb, $bp, $iprefix;

	if ( bp_is_my_profile() ) :

		$bp_blogs=get_option('bprwg_blogs');

		if($bp_blogs!=""){?>

			<div id="bprwg_manage_group_blogs">

			<b>Manage Blog(s):</b>

			<?php if($_GET['m']!=""){

				echo "<div id='bprwg_manage_message'>";	

				if($_GET['m']=="10"){

					echo "Dude?!?! Your an admin why are you trying to remove yourself from your blogs??? If you really wanna do this use the backend... Ya heard??!?!";

				}else{

					echo "Blog(s) updated!";	

				}

				echo "</div>";

			}?>

            <p>*Check or uncheck the blog(s) you would like to be apart of:</p>

			<form action="" method="post">

			<?php //form posts to bprwg_redirect()

			$userid =  $bp->loggedin_user->id ;

			$arr_bp_blogs = explode(",", $bp_blogs);

			for($i = 0; $i < count($arr_bp_blogs); $i++){

				$blog_id = $arr_bp_blogs[$i];

				$checked="";

				$sql="SELECT meta_value FROM ".$iprefix."usermeta where meta_key='wp_".$blog_id."_capabilities' and user_id=".$userid."";

				//echo $sql."<br>";

				$db_result = $wpdb->get_results($sql);

				if ( count( $db_result ) > 0 ) {

					$checked="checked";

				}

				$sql="SELECT option_value FROM ".$iprefix.$blog_id."_options where option_name='blogname'";

				//echo $sql;

				$db_result = $wpdb->get_results( $sql );

				if ( count( $db_result ) > 0 ) {

					echo "<ul id='bprwg_manage_group_blogs_checks'>";

					foreach( $db_result as $the_db_result ) {?>

						<li><input type="checkbox" name="bprwg_blogs[]" value="<?php echo $blog_id; ?>" <?php echo $checked; ?>/>&nbsp;<?php echo $the_db_result->option_value; ?></li>

					<?php

					}

					echo "</ul>";

				}

			}

			?>

			<input type="submit" name="Update_Blog" value="Update" />

			</form><br />

			</div>

			<?php

		}

	endif;

}

add_filter( 'bp_before_member_blogs_content', 'bprwg_blog_menu');
?>