<?php defined('ABSPATH') || exit; ?>

<h1 class="wp-heading-inline"><?php _e('Integration with 1C', 'wc1c'); ?></h1>

<a href="<?php echo wc1c_admin_configurations_get_url('create'); ?>" class="page-title-action">
	<?php _e('New configuration', 'wc1c'); ?>
</a>

<hr class="wp-header-end">

<?php wc1c()->admin()->notices()->output(); ?>