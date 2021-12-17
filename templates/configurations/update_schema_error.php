<?php defined('ABSPATH') || exit; ?>

<?php
$title = __('Warning', 'wc1c');
$title = apply_filters(WC1C_ADMIN_PREFIX . 'configurations_update_schema_error_title', $title);
$text = __('Update is not available.', 'wc1c');
$text = apply_filters(WC1C_ADMIN_PREFIX . 'configurations_update_schema_error_text', $text);
?>

<div class="wc1c-configurations-alert mb-2">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $text; ?></p>
</div>