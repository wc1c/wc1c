<?php defined('ABSPATH') || exit; ?>

<div class="row">
    <div class="col-24 col-lg-16">

        <h2><?php _e('Create new configuration', 'wc1c'); ?></h2>

        <p><?php _e('The established exchange schemes are used to create the configuration.', 'wc1c'); ?></p>

        <?php do_action('wc1c_admin_configurations_create_show'); ?>
    </div>
    <div class="col-24 col-lg-7">
        <div class="card m-0 p-1" style="width: 100%;max-width: 100%;">
            <ul class="list-group m-0 list-group-flush">
                <li class="list-group-item"><?php _e('1. Enter a convenient name.', 'wc1c'); ?></li>
                <li class="list-group-item"><?php _e('2. Select the appropriate schema.', 'wc1c'); ?></li>
                <li class="list-group-item"><?php _e('3. Click create configuration.', 'wc1c'); ?></li>
                <li class="list-group-item"><?php _e('4. Go to edit the newly created configuration, or create another one.', 'wc1c'); ?></li>
            </ul>
        </div>
    </div>
</div>