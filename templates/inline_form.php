<?php defined('ABSPATH') || exit;?>

<form method="post" action="<?php echo esc_url(add_query_arg('form', $args['object']->get_id())); ?>">
	<?php wp_nonce_field('wc1c-admin-'.$args['object']->get_id().'-save', '_wc1c-admin-nonce-' . $args['object']->get_id()); ?>

    <?php $args['object']->generate_html($args['object']->get_fields(), true); ?>
</form>