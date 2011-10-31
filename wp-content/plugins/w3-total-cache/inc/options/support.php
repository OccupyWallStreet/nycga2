<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>
<p>
    Request professional services, suggest a feature or submit a bug using the form below:
</p>

<div id="support_container">
    <?php
    if (!$request_type || !isset($this->_request_types[$request_type])) {
        $this->action_support_select();
    } else {
        if (isset($this->_request_prices[$request_type]) && !$payment) {
            $this->action_support_payment();
        } else {
            $this->action_support_form();
        }
    }
    ?>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
