<?php defined('ABSPATH') || exit; ?>

<h1 class="wp-heading-inline"><?php _e('Integration with 1C', 'wc1c'); ?></h1>

<a href="<?php echo $args['url_create']; ?>" class="page-title-action">
	<?php _e('New configuration', 'wc1c'); ?>
</a>

<?php
    $settings = wc1c()->settings('connection');

    if($settings->get('login', false))
    {
        wc1c()->admin()->connectBox(__($settings->get('login', 'Undefined'), 'wc1c'), true);
    }
    else
    {
        wc1c()->admin()->connectBox(__( 'Connection to the WC1C', 'wc1c'));
    }
?>
<hr class="wp-header-end">

<?php wc1c()->admin()->notices()->output(); ?>