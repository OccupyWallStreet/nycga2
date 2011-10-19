<?php
function mrt_sub3()
{
    // Show header
    mrt_wpss_menu_head('WP - Database Security');
    
    $wsd_wpConfigFile = ABSPATH.'wp-config.php';

    // internal flag
    $canLoadPage = false;
    if (wsd_wpConfigCheckPermissions($wsd_wpConfigFile)) {
        $canLoadPage = true;
    }
?>
<p class="wsd_user_notify">
    <strong>Important</strong>: Make a backup of your database before using this tool!
</p>
<?php
    if (!$canLoadPage) {
        // Display the error message
        echo wsd_eInfo('
            The <strong>wp-config.php</strong> file MUST be writable in order to perform this action.
            You have to manually change permissions for this file.');
    }
?>


<?php /*[ BEGIN PAGE DATABASE ]*/ ?>
<div id="wsd_db_wrapper">
    <?php
        /* Display the Database backup page */
        echo wsd_getTemplate('db-backup');
    ?>
    
    <br/>
    <div style="clear:both;"></div>
    
    <?php
/* Stop here if the wp-config file is not writable or if we cannot change its permissions */ 
        if ($canLoadPage)
        {
            // Display the Change Database Table prefix page
            echo wsd_getTemplate('db-change-prefix',array(
                'wsd_wpConfigFile' => $wsd_wpConfigFile,
                'old_prefix' => $GLOBALS['table_prefix'],
                'new_prefix' => (empty($_POST['newPrefixInput']) ? '' : $_POST['newPrefixInput']),
                'isPostBack' => (($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false)
            ));
        }
    ?>
</div>
<?php /*[ END PAGE DATABASE ]*/ ?>

<p style="height:200px;"></p>

<?php
    // Show footer
    mrt_wpss_menu_footer();
}//function mrt_sub3
?>