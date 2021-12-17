<?php defined('ABSPATH') || exit;?>

<form method="post" action="">
	<?php wp_nonce_field('wsklad-admin-settings-save', '_wsklad-admin-nonce'); ?>
    <div class="wc1c-admin-settings wc1c-admin-connection">
        <table class="form-table wc1c-admin-form-table wc1c-admin-settings-form-table">
		    <?php $args['object']->generate_html($args['object']->get_fields(), true); ?>
        </table>
    </div>
    <p class="submit">
	    <?php
            $connection_state = $args['object']->getSettings()->isConnected();
            $button = __('Connect by Login & Password', 'wc1c');
            if($connection_state)
            {
                $button = __('Disconnect', 'wc1c');
            }
        ?>
	    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $button; ?>">
    </p>
</form>