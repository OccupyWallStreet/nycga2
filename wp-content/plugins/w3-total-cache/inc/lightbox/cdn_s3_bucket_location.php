<?php if (!defined('W3TC')) die(); ?>
<h3>Select bucket location</h3>

<p>
    <label>Location:
        <select id="cdn_<?php echo $type; ?>_bucket_location">
            <?php foreach ($locations as $location => $name): ?>
            <option value="<?php echo $location; ?>"><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</p>
<p>
    <input id="cdn_create_container" class="button-primary {type: '<?php echo $type; ?>', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Create bucket" />
    <span id="cdn_create_container_status" class="w3tc-status w3tc-process"></span>
</p>
<p style="text-align: center;">
    <input class="button" type="button" value="Close" />
</p>
