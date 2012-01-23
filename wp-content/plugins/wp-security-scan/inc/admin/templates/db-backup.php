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
<h2 class="wpss_icon"><?php echo __("Backup your database"); ?></h2>
<?php /*[ DATABASE BACKUP ]*/ ?>
<div class="metabox-holder">

    <?php
    /*
     * DATABASE BACKUP TOOL
     * ================================================================
     */
    ?>
    <div id="bckdb" style="float:left; width:48%;" class="inner-sidebar1 postbox">
        <h3 class="hndle"><span><?php echo __('Backup Database');?></span></h3>
        <div class="inside">
            <div class="">
                <blockquote>
                    <p><?php echo __('Your WordPress database contains every post, every comment and every link you have on your blog. If your database gets erased or corrupted, you stand to lose everything you have written. There are many reasons why this could happen and not all are things you can control. But what you can do is <strong>back up your data</strong>.'); ?></p>
                    <p style="text-align: center;"><?php echo __('<strong>Please backup your database before using this tool!</strong>');?></p>
                </blockquote>
            </div>
            <?php
            /*
             * Check if the backups directory is writable
             */
            $wsd_bckDirPath = ABSPATH.PLUGINDIR.'/wp-security-scan/backups/';
            if (is_dir($wsd_bckDirPath) && is_writable($wsd_bckDirPath)) :

                if (function_exists('wp_create_nonce')){
                    $wsdwpss_nonce_field = wp_create_nonce();
                }
                else {$wsdwpss_nonce_field = '';}

            ?>
                <div style="padding: 7px 7px; margin: 10px 10px;">
                    <form action="#bck" method="post">
                    <?php if (function_exists('wp_nonce_field')) {
                        echo '<input type="hidden" name="_wsdwpss_dbb_wpnonce" value="'.$wsdwpss_nonce_field.'" />';
                        wp_nonce_field('wsdwpss-do-db-backup');
                        }
                        ?>
                        <input type="hidden" name="wsd_db_backup"/>
                        <input type="submit" class="button-primary" name="backupDatabaseButton" value="<?php echo __('Backup now!');?>"/>
                    </form>
                </div>
            <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    if (isset($_POST['_wsdwpss_dbb_wpnonce']))
                    {
                        if (function_exists('check_admin_referer')) {
                            check_admin_referer('wsdwpss-do-db-backup');
                            $_nonce = $_POST['_wsdwpss_dbb_wpnonce'];
                            if (empty($_nonce) || ($_nonce <> $wsdwpss_nonce_field)){
                                wp_die("Invalid request!");
                            }
                        }

                        $tables = '*';
                        if (isset($_POST['tables'])) {
                            $tables = implode(',',$_POST['tables']);
                        }

                        if (($fname = wsd_backupDatabase($tables)) <> '') {
                            echo '<p id="bck" class="wsd_user_success">';
                            echo '<span style="color:#fff;">'.__('Database successfully backed up!').'</span>';
                            echo '<br/><span style="color:#fff;">'.__('Download backup file:').' </span>';
                            echo '<a href="',get_option('siteurl'),'/wp-content/plugins/wp-security-scan/backups/',$fname,'" style="color:#0f0">',$fname,'</a>';
                            echo '</p>';
                        }
                        else {
                            echo '<p id="bck" class="wsd_user_notify">';
                            echo __('The database could not be backed up!');
                            echo '<br/>'.__('A posible error might be that you didn\'t set up writing permissions for the backups directory!');
                            echo '</p>';
                        }
                    }
                }
            ?>
            <?php else :
                // The directory is not writable. Display info message
                echo wsd_eInfo('<strong>Important</strong>: The <strong title="'.$wsd_bckDirPath.'" class="wsd_cursor_help">backups</strong> directory must be writable in order to use this functionality!');
            endif; ?>

        </div>
    </div>

    <?php /*[ DATABASE BACKUPS ]*/ ?>
    <div style="float:right;width:48%;" class="inner-sidebar1 postbox">
        <h3 class="hndle"><span><?php echo __('Database Backup Files');?></span></h3>
        <div class="inside">
            <?php
            /*
             * DISPLAY AVAILABLE DOWNLOADS
             */
            ?>
            <?php
                echo '<div>';
                    $files = wsd_getAvailableBackupFiles();
                    if (empty($files)) {
                        echo '<p style="margin:5px 5px;">'.__('There are no backup files available for download yet!').'</p>';
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
            ?>
        </div>
    </div>
</div>
