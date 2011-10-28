<?php
/*
Simple:Press
RPX Support
$LastChangedDate: 2011-03-25 17:13:23 -0700 (Fri, 25 Mar 2011) $
$Rev: 5723 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function spf_rpx_loginform($id, $width='373px', $fieldset=false)
{
    $out = '';

    $sfrpx = sf_get_option('sfrpx');
    $realm = $sfrpx['sfrpxrealm'];
    $turl = spf_rpx_token_url();
    if (isset($_REQUEST['return_to']))
    {
        $turl .= '&goback='.$_REQUEST['return_to'];
    }
    $params = '&flags=hide_sign_in_with&language_preference='.WPLANG;
    $iframe_src = 'https://'.$realm.'/openid/embed?token_url='.urlencode($turl).$params;

    $out .= '
    <script type="text/javascript">
    <!--
    window.onload = function()
    {
        var rpx_lf = document.getElementById("'.$id.'");
        rpx_lf.style.width = "'.$width.'";
        rpx_lf.style.backgroundColor = "#ffffff";
        rpx_lf.style.margin = "0 auto";

        var rpx_up = document.createElement("DIV");
        rpx_up.style.paddingBottom = "10px";
        if (document.getElementById("sforum"))
        {
            if (document.getElementById("sforum").offsetWidth < 625)
            {
                rpx_up.style.clear = "both";
                rpx_up.style.paddingTop = "20px";
            }
        }
        rpx_up.innerHTML = "<span id=\"spf_rpx_password\"><h2 style=\"text-align:center\">'.__("Sign in with Site Password", "sforum").'</h2></span>";
        rpx_lf.insertBefore(rpx_up, rpx_lf.firstChild );

        var rpx_wrap = document.createElement("DIV");
        rpx_wrap.id = "spf_rpx_wrap";
        rpx_lf.insertBefore(rpx_wrap, rpx_lf.firstChild);

        var sign_in = document.createElement("H2");
        sign_in.id = "spf_rpx_signin";
        sign_in.innerHTML = "'.__("Sign in with 3rd Party Account", "sforum").'";
        sign_in.style.paddingBottom = "10px";
        sign_in.style.textAlign = "center";
        rpx_wrap.appendChild(sign_in);
    ';
    if ($fieldset)
    {
        $out.= '
            if (document.getElementById("sforum"))
            {
                if (document.getElementById("sforum").offsetWidth >= 625)
                {
                    rpx_wrap.style.styleFloat = "left";
                    rpx_wrap.style.cssFloat = "left";
                    rpx_wrap.style.marginRight = "20px";
                } else {
                    rpx_wrap.style.textAlign = "center";
                }
            }

            var rpx_fieldset = document.createElement("FIELDSET");
            rpx_fieldset.id = "spf_rpx_fieldset";
            rpx_fieldset.style.height = "240px";
            rpx_fieldset.style.margin = "0";
            rpx_fieldset.style.padding = "0";
            rpx_wrap.appendChild(rpx_fieldset);
        ';
    } else {
        $out.= '
            var rpx_fieldset = rpx_wrap;
        ';
    }
    $out.= '
        var rpx_iframe = document.createElement("IFRAME");
        rpx_iframe.id = "spf_rpx_iframe";
        rpx_iframe.src = "'.$iframe_src.'";
        rpx_iframe.style.width = "373px";
        rpx_iframe.style.height = "240px";
        rpx_iframe.scrolling = "no";
        rpx_iframe.frameBorder = "no";
        rpx_fieldset.appendChild(rpx_iframe);
    }
    //-->
    </script>
    ';

    return $out;
}

function spf_rpx_login_head($arg='')
{
    # dont do rpx stuff when we fire the login head action
    if ($arg == 'sploginform') return;

    # dont do rpx stuff for register or lost password
    if (isset($_GET['action']) && $_GET['action'] != 'login') return;

    echo spf_rpx_loginform('loginform');
}

function spf_rpx_iframe($style='', $token_url_params='')
{
  $sfrpx = sf_get_option('sfrpx');
  $realm = $sfrpx['sfrpxrealm'];
  $params .= '&flags=hide_sign_in_with&language_preference='.WPLANG;
  $turl = spf_rpx_token_url().$token_url_params.$params;
  $iframe_src = 'https://'.$realm.'/openid/embed?token_url='.urlencode($turl);
  echo '<iframe src="'.$iframe_src.'" scrolling="no" frameBorder="no" style="width:310px;height:240px;'.$style.'"></iframe>';
}

function spf_rpx_token_url()
{
    $url = SFURL;
    $token_url = sf_get_sfqurl($url).'rpx_response=1';

    $sfrpx = sf_get_option('sfrpx');
    if (!empty($sfrpx['sfrpxredirect']))
    {
        $goback = $sfrpx['sfrpxredirect'];
    } else {
        if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') == false)
        {
            # if we're not at the login page, define a goback to the page we are on
            $goback = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        } else {
            # otherwise, get the redirect or go to the admin page
            if ($_GET['redirect_to'])
            {
                $goback = $_GET['redirect_to'];
            } else
            {
                $goback = $url;
            }
        }
    }
    return $token_url.'&goback='.$goback;
}

function spf_rpx_process_token()
{
  $sfrpx = sf_get_option('sfrpx');

  if (empty($_REQUEST['rpx_response']) || empty($_REQUEST['token']))
  {
    return;
  }

  $post_data = array('token' => $_REQUEST['token'], 'apiKey' => $sfrpx['sfrpxkey'], 'format' => 'json');
  $raw_response = spf_rpx_http_post('https://rpxnow.com/api/v2/auth_info', $post_data);

  # parse the json or xml response into an associative array
  $auth_info = spf_rpx_parse_auth_info($raw_response);

  # process the auth_info response
  if ($auth_info['stat'] == 'ok')
  {
    spf_rpx_process_auth_info($auth_info);
  } else {
    echo __('An error occured', "sforum");
  }
}

function spf_rpx_process_auth_info($auth_info)
{
  # a user is already signed in and is changing their OpenID
  if ($_REQUEST['attach_to'])
  {
    $wpuid = $_REQUEST['attach_to'];

    # make sure the actually initiated the sign-in request
    $wpuser = wp_get_current_user();
    if ($wpuser && $wpuid == $wpuser->ID)
    {
      update_user_meta($wpuid, 'rpx_identifier', $auth_info['profile']['identifier']);
    }
  } else {
    # a user is not signed-in, so we sign them in
    spf_rpx_signin_user($auth_info);
  }
}

function spf_rpx_signin_user($auth_info)
{
  $identifier = $auth_info['profile']['identifier'];
  $wpuid = spf_rpx_get_wpuid_by_identifier($identifier);

  # if we don't have the identifier mapped to wp user, create a new one
  if (!$wpuid)
  {
    $wpuid = spf_rpx_create_wp_user($auth_info);
   	if (is_wp_error($wpuid))
    {
		update_sfnotice('sfmessage', '1@'.__('Sorry, Cannot Create Account as the Username or Email Address Already Exists', "sforum"));
		wp_redirect(SFURL);
        die();
    }
  }

  # sign the user in
  wp_set_auth_cookie($wpuid, true, false);
  wp_set_current_user($wpuid);

  # redirect them back to the page they were originally on
  wp_redirect($_GET['goback']);
  die();
}

function spf_rpx_get_wpuid_by_identifier($identifier)
{
  global $wpdb;
  $sql = "SELECT user_id FROM ".SFUSERMETA." WHERE meta_key = 'rpx_identifier' AND meta_value = %s";
  $r = $wpdb->get_var($wpdb->prepare($sql, $identifier));

  if ($r)
  {
    return $r;
  } else {
    return null;
  }
}

function spf_rpx_get_identifier_by_wpuid($wpuid)
{
  return get_user_meta($wpuid, 'rpx_identifier', true);
}

function spf_rpx_get_user_login_name($identifier)
{
  return 'rpx'.md5($identifier);
}

function spf_rpx_username_taken($username)
{
  $user = get_userdatabylogin($username);
  return $user != false;
}

# create a new user based on the
function spf_rpx_create_wp_user($auth_info)
{
    global $wpdb;

    $p = $auth_info['profile'];
    $rid = $p['identifier'];
    $provider_name = $p['providerName'];

    $username = $p['preferredUsername'];
    if (!$username or spf_rpx_username_taken($username))
    {
        $username = spf_rpx_get_user_login_name($rid);
    }

    $last_name = null;
    $first_name = null;
    if(!empty($p['name']))
    {
        $first_name = $p['name']['givenName'];
        $last_name = $p['name']['familyName'];
    }

    $email ='dummy@simple-press.com';
    if(!empty($p['email']))
    {
        $email = sf_filter_email_save($p['email']);
    }

    $userdata = array(
         'user_pass' => wp_generate_password(),
         'user_login' => $username,
         'display_name' => sf_filter_name_save($p['displayName']),
         'user_url' => $p['url'],
         'user_email' => $email,
         'first_name' => $first_name,
         'last_name' =>  $last_name,
         'nickname' => $p['displayName']);

    # try to create new user
    $wpuid = wp_insert_user($userdata);
    if ($wpuid && !is_wp_error($wpuid))
    {
        update_user_meta($wpuid, 'rpx_identifier', $rid);

        # remove temp email?
        if ($email == 'dummy@simple-press.com')
        {
            $wpdb->query("UPDATE ".SFUSERS." SET user_email='' WHERE ID=".$wpuid);
        }
    }

    return $wpuid;
}

function spf_rpx_edit_user_page()
{
  $user = wp_get_current_user();
  $rpx_identifier = $user->rpx_identifier;
  $login_provider = $user->rpx_provider;

  echo '<h3 id="rpx">'.__('Sign-in Provider','sforum').'</h3>';

  if ($rpx_identifier)
  {
    # extract the provider domain
    $pieces = explode('/', $rpx_identifier);
    $host = $pieces[2];

    echo '<p>'.__('You are currently using', 'sforum').' <b>'.$host.'</b> '.__('as your sign-in provider.  You may change this by choosing a different provider or OpenID below and clicking Sign-In.', 'sforum').'</p>';
  } else {
    echo '<p>'.__('You can sign in to this blog without a password by choosing a provider below.', 'sforum').'</p>';
  }

  $token_url_params = '&attach_to=' . $user->ID;

  spf_rpx_iframe('border:1px solid #aaa;padding:2em;background-color:white;', $token_url_params);
}

function spf_has_curl()
{
  return function_exists('curl_init');
}

function spf_rpx_http_post($url, $post_data)
{
  if (spf_has_curl())
  {
    return spf_curl_http_post($url, $post_data);
  } else {
    return spf_builtin_http_post($url, $post_data);
  }
}

function spf_curl_http_post($url, $post_data)
{
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $raw_data = curl_exec($curl);
  curl_close($curl);
  return $raw_data;
}

function spf_builtin_http_post($url, $post_data)
{
  $content = http_build_query($post_data);
  $opts = array('http'=>array('method'=>"POST", 'content'=>$content));
  $context = stream_context_create($opts);
  $raw_data = file_get_contents($url, 0, $context);
  return $raw_data;
}

function spf_get_json_coder()
{
  include_once ABSPATH.'wp-includes/class-json.php';
  return new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
}

function spf_rpx_parse_auth_info($raw)
{
  $json = spf_get_json_coder();
  return $json->decode($raw);
}

function spf_rpx_parse_lookup_rp($raw)
{
  $json = spf_get_json_coder();
  return $json->decode($raw);
}

?>