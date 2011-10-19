<?php
/*
 * Change Database table Prefix
 */
?>
<br/><br/>
<h2 class="wpss_icon">Change database table prefix</h2>

<?php
    // Holds the error/info messages generated on form postback
    $infoMessage = '';

    // Check if user has enough rights to alter the Table structure
    $wsd_userDbRights = wsd_getDbUserRights();
    $showPage = false; // assume we don't have ALTER rights
    if ($wsd_userDbRights['rightsEnough']) {
        $showPage = true;
        $canAlter = '<span style="color: #060; font-weight: 900;">(Yes)</span>';
    }
    else { $canAlter = '<span style="color: #f00; font-weight: 900;">(No)</span>'; }
?>
<p>Change your database table prefix to mitigate zero-day SQL Injection attacks.</p>
<p><strong>Before running this script:</strong>
<ul class="wsd_info_list">
    <li>The <strong title="<?php echo ABSPATH.'wp-config.php'; ?>" class="wsd_cursor_help">wp-config.php</strong> file must be set to writable before running this script. <span style="color: #060; font-weight: 900;">(Yes)</span></li>
    <li>The database user you're using with WordPress must have <strong>ALTER</strong> rights. <?php echo $canAlter;?></li>
</ul>
<?php
/*
 * If the user doesn't have ALTER rights
 */
if ( ! $showPage )
{
    echo wsd_eInfo('The User: <strong>'.DB_USER.'</strong> used to access the database server must have <strong>ALTER</strong> rights in order to perform this action!');

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
/*
 * VALIDATE FORM
 */
    if (!empty($_POST['newPrefixInput']) && isset($_POST['changePrefixButton']))
    {
        $wsd_isPostBack = true;

        check_admin_referer('prefix-changer-change_prefix');

        $wpdb =& $GLOBALS['wpdb'];
        $new_prefix = preg_replace("[^0-9a-zA-Z_]", "", $_POST['newPrefixInput']);
        if (empty($wsd_userDbRights['rightsEnough'])) {
            $wsd_Message .= wsd_eInfo('The User which is used to access your Wordpress Database, hasn\'t enough rights (is missing the ALTER right) to alter the Table structure.
                <br/>Please visit the <a href="http://www.websitedefender.com/category/faq/" target=_blank">WebsiteDefender WP Security Scan WordPress plugin documentation</a> website for more information.
                <br/>If the user <code>has ALTER rights</code> and the tool is still not working, please <a href="http://www.websitedefender.com/support/" target="_blank">contact us</a> for assistance!');
        }
        if (!empty($wsd_userDbRights['rightsTooMuch'])) {
            $wsd_Message .= wsd_eInfo('Your currently used User to access the Wordpress Database, holds too many rights.'.
                '<br/>We suggest that you limit his rights or to use another User with more limited rights instead, to increase your Security.','information');
        }
        if (strlen($new_prefix) < strlen($_POST['newPrefixInput'])){
            $wsd_Message .= wsd_eInfo('You used some characters disallowed in Table names. The sanitized prefix will be used instead: '. $new_prefix,'information');
        }
        if ($new_prefix == $old_prefix) {
            $wsd_Message .= wsd_eInfo('No change! Please select a new table prefix value.');
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
    <?php
    if (function_exists('wp_nonce_field')) {
        wp_nonce_field('prefix-changer-change_prefix');
    }
    ?>
     <p>Change the current:
         <input type="text" name="newPrefixInput" value="<?php echo $new_prefix;?>" size="20" maxlength="15"/>
         table prefix to something different.</p>
     <p>Allowed characters: all latin alphanumeric as well as the <strong>_</strong> (underscore).</p>
    <input type="submit" name="changePrefixButton" value="Start Renaming" />
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
