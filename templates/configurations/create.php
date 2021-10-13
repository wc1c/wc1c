<?php defined('ABSPATH') || exit; ?>

<?php do_action('wc1c_admin_configurations_create_show_before'); ?>

<div class="row m-0">
    <div class="col-24">
	    <?php
            $label = __('Back to configurations list', 'wc1c');
            $url = wc1c_admin_configurations_get_url('list');
            wc1c_admin_back_link($label, $url);
	    ?>

        <?php do_action('wc1c_admin_configurations_create_show'); ?>
    </div>
</div>

<?php do_action('wc1c_admin_configurations_create_show_after'); ?>