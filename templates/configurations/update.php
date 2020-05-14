<?php defined('ABSPATH') || exit;?>

<?php do_action('wc1c_admin_before_configurations_update_show'); ?>

<div class="row m-0">
	<div class="col-17 p-0">
		<?php do_action('wc1c_admin_configurations_update_show'); ?>
	</div>
	<div class="col-7 p-0">
		<?php do_action('wc1c_admin_configurations_update_sidebar_show'); ?>
	</div>
</div>

<?php do_action('wc1c_admin_after_configurations_update_show'); ?>