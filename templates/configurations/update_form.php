<?php defined('ABSPATH') || exit;?>

<form method="post" action="">
	<?php wp_nonce_field('wc1c-admin-configurations-update-save', '_wc1c-admin-nonce'); ?>
    <div class="bg-white p-1">
        <table class="form-table wc1c-admin-form-table">
            <?php $args['object']->generate_html($args['object']->get_fields(), true); ?>
        </table>
    </div>
    <p class="submit">
	    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save configuration', 'wc1c'); ?>">
    </p>
</form>