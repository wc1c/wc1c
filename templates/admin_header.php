<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e('Integration with 1C', 'wc1c'); ?></h1>
    <a href="<?php echo wc1c_admin_get_configuration_url('create'); ?>" class="page-title-action">
	    <?php _e('New configuration', 'wc1c'); ?>
    </a>
    <hr class="wp-header-end">
	<?php WC1C_Admin()->print_messages(); ?>
	<?php echo WC1C_Admin()->page_tabs(); ?>
</div>