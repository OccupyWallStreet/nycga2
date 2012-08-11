<?php
/*
 * Limit and offset filter.
 */

function wpv_filter_limit_admin_summary($view_settings, $query_type = 'post') {
    $view_settings = wpv_limit_default_settings($view_settings);
    $output = '';
    if ($query_type != 'taxonomy') {
        if (intval($view_settings['limit']) != -1) {
            if (intval($view_settings['limit']) == 1) {
                $output .= __(', limit to 1 item', 'wpv-views');
            } else {
                $output .= sprintf(__(', limit to %d items', 'wpv-views'),
                        intval($view_settings['limit']));
            }
        }
        if (intval($view_settings['offset']) != 0) {
            if (intval($view_settings['limit']) == 1) {
                $output .= __(', skip first item', 'wpv-views');
            } else {
                $output .= sprintf(__(', skip %d items', 'wpv-views'),
                        intval($view_settings['offset']));
            }
        }
    } else {
        if (intval($view_settings['taxonomy_limit']) != -1) {
            if (intval($view_settings['taxonomy_limit']) == 1) {
                $output .= __(', limit to 1 item', 'wpv-views');
            } else {
                $output .= sprintf(__(', limit to %d items', 'wpv-views'),
                        intval($view_settings['taxonomy_limit']));
            }
        }
        if (intval($view_settings['taxonomy_offset']) != 0) {
            if (intval($view_settings['taxonomy_limit']) == 1) {
                $output .= __(', skip first item', 'wpv-views');
            } else {
                $output .= sprintf(__(', skip %d items', 'wpv-views'),
                        intval($view_settings['taxonomy_offset']));
            }
        }
    }
    echo $output;
}

function wpv_filter_limit_admin($view_settings, $query_type = 'post') {

    ?>
    <fieldset>
        <legend><strong><?php _e('Offset and Limit:', 'wpv-views') ?></strong></legend>            
        <ul style="padding-left:30px;">
            <li>
                <label><?php
    _e('Limit the number of items returned to', 'wpv-views');
    if ($query_type != 'taxonomy') {

        ?>
                        <select name="_wpv_settings[limit]">
                            <option value="-1"><?php
        _e('No limit', 'wpv-views');

        ?></option>
                            <?php
                            for ($index = 1; $index < 51; $index++) {
                                echo '<option value="' . $index . '"';
                                if ($view_settings['limit'] == $index) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $index . '</option>';
                            }
                        } else {

                            ?>
                            <select name="_wpv_settings[taxonomy_limit]">
                                <option value="-1"><?php
                            _e('No limit', 'wpv-views');

                            ?></option>
                                <?php
                                for ($index = 1; $index < 51; $index++) {
                                    echo '<option value="' . $index . '"';
                                    if ($view_settings['taxonomy_limit'] == $index) {
                                        echo ' selected="selected"';
                                    }
                                    echo '>' . $index . '</option>';
                                }
                            }

                            ?>
                        </select></label>
            </li>
            <li>
                <label><?php
                            _e('Skip these number of items', 'wpv-views');
                            if ($query_type != 'taxonomy') {

                                ?>
                        <select name="_wpv_settings[offset]">
                            <option value="0"><?php
                                _e('None', 'wpv-views');

                                ?></option>
                            <?php
                            for ($index = 1; $index < 51; $index++) {
                                echo '<option value="' . $index . '"';
                                if ($view_settings['offset'] == $index) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $index . '</option>';
                            }
                        } else {

                            ?>
                            <select name="_wpv_settings[taxonomy_offset]">
                                <option value="0"><?php
                            _e('None', 'wpv-views');

                            ?></option>
                                <?php
                                for ($index = 1; $index < 51; $index++) {
                                    echo '<option value="' . $index . '"';
                                    if ($view_settings['taxonomy_offset'] == $index) {
                                        echo ' selected="selected"';
                                    }
                                    echo '>' . $index . '</option>';
                                }
                            }

                            ?>
                        </select></label>
            </li>
        </ul>

    </fieldset>

    <?php
}

add_filter('wpv-view-get-content-summary', 'wpv_limit_summary_filter', 5, 3);

function wpv_limit_summary_filter($summary, $post_id, $view_settings) {
    ob_start();
    wpv_filter_limit_admin_summary($view_settings);
    $summary .= ob_get_contents();
    ob_end_clean();
    return $summary;
}