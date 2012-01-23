<?php if (!defined('W3TC')) die(); ?>
<form action="<?php echo W3TC_PAYPAL_URL; ?>" method="get">
    <div class="metabox-holder">
        <?php echo $this->postbox_header('Request payment'); ?>

        <p><?php echo htmlspecialchars($this->_request_types[$request_type]); ?></p>

        <p><strong>Price: <?php echo sprintf('%.2f', $this->_request_prices[$request_type]); ?> USD</strong></p>

        <p>
            <input type="hidden" name="cmd" value="_xclick" />
            <input type="hidden" name="business" value="<?php echo W3TC_PAYPAL_BUSINESS; ?>" />
            <input type="hidden" name="item_name" value="<?php echo htmlspecialchars(sprintf('%s: %s (#%s)', ucfirst(w3_get_host()), $this->_request_types[$request_type], $request_id)); ?>" />
            <input type="hidden" name="amount" value="<?php echo sprintf('%.2f', $this->_request_prices[$request_type]); ?>" />
            <input type="hidden" name="currency_code" value="USD" />
            <input type="hidden" name="no_shipping" value="1" />
            <input type="hidden" name="rm" value="2" />
            <input type="hidden" name="return" value="<?php echo htmlspecialchars($return_url); ?>" />
            <input type="hidden" name="cancel_return" value="<?php echo htmlspecialchars($cancel_url); ?>" />
            <input type="submit" class="button-primary" value="Buy now" />
            <input id="support_cancel" class="{nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Cancel" class="button-primary" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>
