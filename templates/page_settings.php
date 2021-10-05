<?php defined('ABSPATH') || exit; ?>

<div class="wrap">

    <?php do_action('wc1c_admin_settings_form_before_show'); ?>

    <div class="row m-0">
        <div class="col-24 p-0 order-2 order-lg-1">
			<?php do_action('wc1c_admin_settings_show'); ?>
        </div>
    </div>

	<?php do_action('wc1c_admin_settings_form_after_show'); ?>

</div>