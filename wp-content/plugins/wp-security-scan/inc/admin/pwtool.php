<?php
function mrt_sub1(){
	
	mrt_wpss_menu_head('WP - Password Tools');
	
	?>
     
          <div class="metabox-holder">
              <div class="postbox" style="width: 60%;">
                  <h3 class="hndle"><span><?php echo __('Password Strength Tool');?></span></h3>
                  <div class="inside">
                      <p></p>
<table id="wsd_pwdtool">
    <tr valign="top">
        <td>
            <form name="commandForm">
                Type password: <input type="password" size="30" maxlength="50" name="password" onkeyup="testPassword(this.value);" value="" />
                <br/>
                <span style="color:#808080">Minimum 6 Characters</span>
            </form>
        </td>
        <td style="padding-left: 6px;">
            <span>Password Strength:</span>
            <div id="Words">
                <p class="indicator"></p>
                <p><strong>Begin Typing</strong></p>
            </div>
        </td>
    </tr>
</table>
    <p></p>
                  </div>
              </div>
          </div>

<div>
    <?php
    echo "<br /><strong>Strong Password Generator</strong><br />";
    echo "Strong Password: " . '<span style="color:#f00;">' . make_password(15) . "</span>";
    ?>
 </div>
<br/><br/>
<p style="margin-top: 75px;"></p>
<hr align="left" size="2" width="612px" />
 
<?php 
mrt_wpss_menu_footer();

} ?>
