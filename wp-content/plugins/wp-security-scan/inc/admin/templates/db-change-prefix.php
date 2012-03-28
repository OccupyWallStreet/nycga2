<?php
/*
 * Change Database table Prefix
 */
?>
<br/>
<h2 class="wpss_icon">Change database table prefix</h2>
<br/>
<div class="metabox-holder">
    <?php
    /*
     * CHANGE DATABASE PREFIX TOOL
     * ================================================================
     */
    ?>
    <div class="postbox">
        <h3 class="hndle"><span><?php echo __('Change Database Prefix');?></span></h3>
        <div class="inside">



<?php
    // Holds the error/info messages generated on form postback
    $infoMessage = '';

    // Check if user has enough rights to alter the Table structure
    $wsd_userDbRights = wsd_getDbUserRights();
    $showPage = false; // assume we don't have ALTER rights
    if (! empty($wsd_userDbRights['rightsEnough'])) {
        $showPage = true;
        $canAlter = '<span style="color: #060; font-weight: 900;">(Yes)</span>';
    }
    else { $canAlter = '<span style="color: #f00; font-weight: 900;">(No)</span>'; }
?>
<p>Change your database table prefix to mitigate zero-day SQL Injection attacks.</p>
<p><strong>Before running this script:</strong>
<ul class="wsd_info_list">
    <li>Make a backup of your database.</li>
    <li>The <strong title="<?php echo ABSPATH.'wp-config.php'; ?>" class="wsd_cursor_help">wp-config.php</strong> file must be set to writable before running this script. <span style="color: #060; font-weight: 900;">(Yes)</span></li>
    <li>The database user you're using with WordPress must have <strong>ALTER</strong> rights. <?php echo $canAlter;?></li>
</ul>
<?php
/*
 * If the user doesn't have ALTER rights
 */
if ( ! $showPage )
{
    echo wsd_eInfo('The User which is used to access your Wordpress Database must have <strong>ALTER</strong> rights in order to perform this action!');

    // Stop here, no need to load the rest of the page
    return;
}
?>

<?php
/*
 * Issue the file permissions warning
 */
$infoMessage = 'It\'s a security risk to have your files writable (0777)!
    Please make sure that after running this script, the <strong title="'.ABSPATH.'wp-config.php" class="wsd_cursor_help">wp-config.php</strong> file\'s permissions are set to 0644!
    <br/> See: <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">http://codex.wordpress.org/Changing_File_Permissions</a> for more information.';
echo wsd_eInfo($infoMessage,'information');
?>


<?php

    if (function_exists('wp_create_nonce')){
        $wsdwpssnonce = wp_create_nonce();
    }
    else {$wsdwpssnonce = '';}


/*
 * VALIDATE FORM
 */
    if (!empty($_POST['newPrefixInput']) && isset($_POST['changePrefixButton']))
    {
        $wsd_isPostBack = true;

        if (function_exists('check_admin_referer')) {
            check_admin_referer('wsdwpss-db-change-prefix');
            $_nonce = $_POST['_wsdwpss_chp_wpnonce'];
            if (empty($_nonce) || ($_nonce <> $wsdwpssnonce)){
                wp_die("Invalid request!");
            }
        }

        $wpdb =& $GLOBALS['wpdb'];
        $new_prefix = preg_replace("/[^0-9a-zA-Z_]/", "", $_POST['newPrefixInput']);
        if (empty($wsd_userDbRights['rightsEnough'])) {
            $wsd_Message .= wsd_eInfo('The User which is used to access your Wordpress Database, hasn\'t enough rights (is missing the ALTER right) to alter the Table structure.
                <br/>Please visit the <a href="http://www.websitedefender.com/category/faq/" target=_blank">WebsiteDefender WP Security Scan WordPress plugin documentation</a> website for more information.
                <br/>If the user <code>has ALTER rights</code> and the tool is still not working, please <a href="http://www.websitedefender.com/support/" target="_blank">contact us</a> for assistance!');
        }
//        if (!empty($wsd_userDbRights['rightsTooMuch'])) {
//            $wsd_Message .= wsd_eInfo('The database user used to access the WordPress Database has too many rights. Limit the user\'s rights to increase your Website\'s Security','information');
//        }
        if (strlen($new_prefix) < strlen($_POST['newPrefixInput'])){
            $wsd_Message .= wsd_eInfo('You used some characters disallowed for the table prefix. The sanitized version will be used instead: <strong>'. $new_prefix.'</strong>','information');
        }
        if ($new_prefix == $old_prefix) {
            $wsd_Message .= wsd_eInfo('No change! Please provide a new table prefix.');
        }
        else
        {
            // Get the list of tables to modify
            $tables = wsd_getTablesToAlter();
            if (empty($tables))
            {
                $wsd_Message .= wsd_eInfo('There are no tables to rename!');
            }
            else
            {
                $result = wsd_renameTables($tables, $old_prefix, $new_prefix);

                // check for errors
                if (!empty($result))
                {
                    $wsd_Message .= wsd_eInfo('All tables have been successfully updated!','success');

                    // try to rename the fields
                    $wsd_Message .= wsd_renameDbFields($old_prefix, $new_prefix);

                    if (wsd_updateWpConfigTablePrefix($wsd_wpConfigFile, $old_prefix, $new_prefix))
                    {
                        $wsd_Message .= wsd_eInfo('The wp-config file has been successfully updated!','success');
                    }
                    else {
                        $wsd_Message .= wsd_eInfo('The wp-config file could not be updated! You have to manually update the table_prefix variable
                            to the one you have specified: '.$new_prefix);
                    }
                }// End if tables successfully renamed
                else {
                    $wsd_Message .= wsd_eInfo('An error has occurred and the tables could not be updated!');
                }
            }// End if there are tables to rename
        }
    }// End if (!empty($_POST['newPrefixInput']))
    else {
        $new_prefix = $old_prefix;
    }
?>



<br/>
<form action="#cdtp" method="post" name="prefixchanging">
	<?php if (function_exists('wp_nonce_field')) {
        echo '<input type="hidden" name="_wsdwpss_chp_wpnonce" value="'.$wsdwpssnonce.'" />';
        wp_nonce_field('wsdwpss-db-change-prefix');
        }
        ?>
     <p><?php echo __('Change the current:');?>
         <input type="text" name="newPrefixInput" value="<?php echo $new_prefix;?>" size="20" maxlength="15"/>
         <?php echo __('table prefix to something different.');?></p>
     <p><?php echo __('Allowed characters: all latin alphanumeric as well as the <strong>_</strong> (underscore).');?></p>
    <input type="submit" class="button-primary" name="changePrefixButton" value="<?php echo __('Start Renaming');?>" />
</form>

<div id="cdtp">
    <?php
        // Display status information
        if ($isPostBack)
        {
            echo $wsd_Message;
        }
    ?>
</div>

        </div>
    </div>
</div>
