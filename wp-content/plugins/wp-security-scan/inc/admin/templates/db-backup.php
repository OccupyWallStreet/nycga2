<?php
/*
 * Backup Database
 */
?>
<?php
/*
 * BACKUP DATABASE SECTION
 */
?>
<br/><br/>
<h2 class="wpss_icon">Backup your database</h2>

<?php
/*
 * Check if the backups directory is writable
 */
$wsd_bckDirPath = ABSPATH.PLUGINDIR.'/wp-security-scan/backups/';
if (is_dir($wsd_bckDirPath) && is_writable($wsd_bckDirPath)) :
?>

<div style="padding: 7px 7px; margin: 10px 10px;">
    <form action="#bck" method="post">
        <input type="hidden" name="wsd_db_backup"/>
        <input type="submit" name="backupDatabaseButton" value="Backup now!"/>
    </form>
</div>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if (isset($_POST['wsd_db_backup']))
        {
            $tables = '*';
            if (isset($_POST['tables'])) {
                $tables = implode(',',$_POST['tables']);
            }

            if (($fname = wsd_backupDatabase($tables)) <> '') {
                echo '<p id="bck" class="wsd_user_success">';
                echo '<span style="color:#fff;">Database successfully backed up!</span>';
                echo '<br/><span style="color:#fff;">Download backup file: </span>';
                echo '<a href="',get_option('siteurl'),'/wp-content/plugins/wp-security-scan/backups/',$fname,'" style="color:#0f0">',$fname,'</a>';
                echo '</p>';
            }
            else {
                echo '<p id="bck" class="wsd_user_notify">';
                echo 'The database could not be backed up!';
                echo '<br/>A posible error might be that you didn\'t set up writing permissions for the backups directory!';
                echo '</p>';
            }
        }
    }
?>
<?php else :
    // The directory is not writable. Display info message
    echo wsd_eInfo('<strong>Important</strong>: The <strong title="'.$wsd_bckDirPath.'" class="wsd_cursor_help">backups</strong> directory must be writable in order to use this functionality!');
endif; ?>



<?php
/*
 * DISPLAY AVAILABLE DOWNLOADS
 */
?>
<?php
    function wsd_db_download_list()
    {
        echo '<div>';
            $files = wsd_getAvailableBackupFiles();
            if (empty($files)) {
                echo '<p style="margin:5px 5px;">There are no backup files available for download yet!</p>';
            }
            else {
                echo '<ul id="wsd-information-scan-list">';
                foreach($files as $fileName) {
                    echo '<li>';
                        echo '<a href="',get_option('siteurl'),'/wp-content/plugins/wp-security-scan/backups/',$fileName,'">',$fileName,'</a>';
                    echo '</li>';
                }
                echo '</ul>';
            }
        echo '</div>';
    }
    add_meta_box("wpss_mrt_1", 'Available database backups', "wsd_db_download_list", "wsd_db_bck_dwl");
    echo '<div style="float:left; width:50%;" class="inner-sidebar1">';
        echo '<div class="metabox-holder">';
            do_meta_boxes('wsd_db_bck_dwl','advanced',''); 	
    echo '</div></div>';
?>