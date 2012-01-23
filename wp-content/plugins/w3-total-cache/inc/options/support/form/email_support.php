<?php if (!defined('W3TC')) die(); ?>
<?php echo $this->postbox_header('Required information', 'required'); ?>
<table class="form-table">
    <tr>
        <th>Request type:</th>
        <td><?php echo htmlspecialchars($this->_request_types[$request_type]); ?></td>
    </tr>
    <tr>
        <th><label for="support_url">Blog <acronym title="Uniform Resource Locator">URL</acronym>:</label></th>
        <td><input id="support_url" type="text" name="url" value="<?php echo htmlspecialchars($url); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_name">Name:</label></th>
        <td><input id="support_name" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_email">E-Mail:</label></th>
        <td><input id="support_email" type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_twitter">Twitter ID:</label></th>
        <td><input id="support_twitter" type="text" name="twitter" value="<?php echo htmlspecialchars($twitter); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_subject">Subject:</label></th>
        <td><input id="support_subject" type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_description">Issue description:</label></th>
        <td><textarea id="support_description" name="description" cols="70" rows="8"><?php echo htmlspecialchars($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label for="support_template">Attach template:</label></th>
        <td><select id="support_template" name="templates[]" multiple="multiple" size="10" style="height: auto;">
            <?php foreach ($template_files as $template_file): ?>
            <option value="<?php echo htmlspecialchars($template_file); ?>"<?php if (in_array($template_file, $templates)): ?> selected="selected"<?php endif; ?>><?php echo htmlspecialchars($template_file); ?></option>
            <?php endforeach; ?>
        </select></td>
    </tr>
    <tr>
        <th><label for="support_file">Attach file:</label></th>
        <td>
            <input id="support_file" type="file" name="files[]" value="" /><br />
            <a href="#" id="support_more_files">Attach more files</a>
        </td>
    </tr>
</table>
<?php echo $this->postbox_footer(); ?>

<?php echo $this->postbox_header('Additional information'); ?>
<table class="form-table">
    <tr>
        <th><label for="support_phone">Phone:</label></th>
        <td><input id="support_phone" type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_forum_url">Forum Topic URL:</label></th>
        <td><input id="support_forum_url" type="text" name="forum_url" value="<?php echo htmlspecialchars($forum_url); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_wp_login"><acronym title="WordPress">WP</acronym> Admin login:</label></th>
        <td><input id="support_wp_login" type="text" name="wp_login" value="<?php echo htmlspecialchars($wp_login); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_wp_password"><acronym title="WordPress">WP</acronym> Admin password:</label></th>
        <td><input id="support_wp_password" type="text" name="wp_password" value="<?php echo htmlspecialchars($wp_password); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_ftp_host"><acronym title="Secure Shell">SSH</acronym> / <acronym title="File Transfer Protocol">FTP</acronym> host:</label></th>
        <td><input id="support_ftp_host" type="text" name="ftp_host" value="<?php echo htmlspecialchars($ftp_host); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_ftp_login"><acronym title="Secure Shell">SSH</acronym> / <acronym title="File Transfer Protocol">FTP</acronym> login:</label></th>
        <td><input id="support_ftp_login" type="text" name="ftp_login" value="<?php echo htmlspecialchars($ftp_login); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_ftp_password"><acronym title="Secure Shell">SSH</acronym> / <acronym title="File Transfer Protocol">FTP</acronym> password:</label></th>
        <td><input id="support_ftp_password" type="text" name="ftp_password" value="<?php echo htmlspecialchars($ftp_password); ?>" size="80" /></td>
    </tr>
</table>
<?php echo $this->postbox_footer(); ?>