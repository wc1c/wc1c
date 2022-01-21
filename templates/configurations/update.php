<?php defined('ABSPATH') || exit;?>

<div class="row g-0">
    <div class="col-24">
        <?php
            $label = __('Back to configurations list', 'wc1c');
            wc1c()->templates()->adminBackLink($label, $args['back_url']);
        ?>

        <div class="p-2 bg-white rounded-3 mb-3">
            <?php do_action(WC1C_ADMIN_PREFIX . 'configurations_update_header_show'); ?>
        </div>
    </div>
</div>

<?php do_action(WC1C_ADMIN_PREFIX . 'before_configurations_update_show'); ?>

<div class="row g-0">
	<div class="col-24 col-lg-17">
        <div class="pe-0 pe-lg-2">
	        <?php do_action(WC1C_ADMIN_PREFIX . 'configurations_update_show'); ?>
        </div>
	</div>
	<div class="col-24 col-lg-7">
		<?php do_action(WC1C_ADMIN_PREFIX . 'configurations_update_sidebar_show'); ?>
	</div>
</div>

<?php do_action(WC1C_ADMIN_PREFIX . 'after_configurations_update_show'); ?>