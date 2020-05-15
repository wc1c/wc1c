<?php defined('ABSPATH') || exit;?>

<?php do_action('wc1c_admin_before_configurations_update_show'); ?>

<div class="row m-0">
	<div class="col-24 col-lg-17 p-0 order-2 order-lg-1">
		<?php do_action('wc1c_admin_configurations_update_show'); ?>
	</div>
	<div class="col-24 col-lg-7 p-0 order-1 order-lg-2">
		<?php do_action('wc1c_admin_configurations_update_sidebar_show'); ?>
	</div>
</div>

<?php do_action('wc1c_admin_after_configurations_update_show'); ?>