<?php defined('ABSPATH') || exit; ?>

<?php do_action(WC1C_ADMIN_PREFIX . 'before_tools_single_show'); ?>

<div class="row g-0">
    <div class="col">
        <?php
            $label = __('Back to all tools', 'wc1c');
            $url = wc1c_admin_tools_get_url();
            wc1c_admin_back_link($label, $url);
        ?>
        <?php do_action(WC1C_ADMIN_PREFIX . 'tools_single_show'); ?>
    </div>
</div>

<?php do_action(WC1C_ADMIN_PREFIX . 'after_tools_single_show'); ?>