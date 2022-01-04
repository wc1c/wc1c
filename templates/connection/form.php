<?php defined('ABSPATH') || exit;?>

<form method="post" action="">
	<?php wp_nonce_field('wc1c-admin-settings-save', '_wc1c-admin-nonce'); ?>
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

        <?php if(false === $connection_state) : ?>

            <?php
	            $current_url = home_url(add_query_arg($_GET));
            ?>

            <a href="<?php echo $args['object']->connection->buildUrl($current_url); ?>" class="button button-secondary"><?php _e('Connect by WC1C site', 'wc1c'); ?></a>
        <?php endif; ?>
    </p>
</form>