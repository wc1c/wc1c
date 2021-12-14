<?php defined('ABSPATH') || exit;?>

<div class="row">
	<div class="col pt-4 pb-2">
		<?php _e('Use the forms to delete the configuration from WooCommerce.', 'wc1c'); ?>
	</div>
</div>

<div class="">
	<?php do_action(WC1C_ADMIN_PREFIX . 'configurations_form_delete_show'); ?>
</div>
