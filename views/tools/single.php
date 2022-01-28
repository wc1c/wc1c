<?php defined('ABSPATH') || exit; ?>

<?php do_action(WC1C_ADMIN_PREFIX . 'before_tools_single_show'); ?>

<div class="row g-0">
    <div class="col">

        <?php
            $label = __('Back to all tools', 'wc1c');
            wc1c()->views()->adminBackLink($label, $args['back_url']);
        ?>

        <div class="bg-white p-2 rounded-3">
            <?php do_action(WC1C_ADMIN_PREFIX . 'tools_single_show'); ?>
        </div>
    </div>
</div>

<?php do_action(WC1C_ADMIN_PREFIX . 'after_tools_single_show'); ?>