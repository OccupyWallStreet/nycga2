<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<p>
    Remove objects from the CDN by specifying the relative path on individual lines below and clicking the "Purge" button when done. For example:
</p>
<p>
    <?php switch ($this->_config->get_string('cdn.engine')):
        case 'cotendo': ?>
        <ul>
            <li><em>wp-content/themes/twentyten/images/headers/</em> &mdash; the folder itself (only when accessed directly without any file).</li>
            <li><em>wp-content/themes/twentyten/images/headers/*.</em> &mdash; all files in the folder with no extension, with all parameter variations.</li>
            <li><em>wp-content/themes/twentyten/images/headers/*.jpg</em> &mdash; all files in the folder whose extension is "jpg".</li>
            <li><em>wp-content/themes/twentyten/images/headers/path</em> &mdash; the specific file (when the file does not have an extension), and without parameters.</li>
            <li><em>wp-content/themes/twentyten/images/headers/path.jpg</em> &mdash; the specific file with its extension, and without parameters.</li>
            <li><em>wp-content/themes/twentyten/images/headers/path.jpg?*</em> &mdash; the specific file with its extension, with all variation of parameters.</li>
            <li><em>wp-content/themes/twentyten/images/headers/path.jpg?key=value</em> &mdash; the specific file with its extension, with the specific parameters.</li>
        </ul>
        <?php break;

        default: ?>
        <em>wp-content/themes/twentyten/images/headers/path.jpg</em>
        <?php break;
    endswitch; ?>
</p>


<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>Files to purge:</p>
    <p>
        <textarea name="files" rows="10" cols="90"></textarea>
    </p>
    <p>
        <?php echo $this->nonce_field('w3tc'); ?>
        <input class="button-primary" type="submit" name="w3tc_cdn_purge_post" value="Purge" />
    </p>
</form>

<div class="log">
    <?php foreach ($results as $result): ?>
        <div class="log-<?php echo ($result['result'] == W3TC_CDN_RESULT_OK ? 'success' : 'error') ?>">
            <?php echo htmlspecialchars($result['remote_path']); ?>
            <strong><?php echo htmlspecialchars($result['error']); ?></strong>
        </div>
    <?php endforeach; ?>
</div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>