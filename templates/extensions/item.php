<?php defined('ABSPATH') || exit; ?>

<div class="card col-24 mb-2 mt-2 pt-0 pb-0 pl-0 pr-0 border-0" style="border-radius: 0!important;">
    <div class="card-header p-2">
        <span class="badge badge-info p-2" style="font-size: 1em;">
            <?php _e('Version:', 'wc1c'); ?> <?php echo $args['object']->get_version(); ?>
        </span>
        <span class="badge badge-info p-2" style="font-size: 1em;">
            <?php _e('Extension ID:', 'wc1c'); ?> <?php echo $args['id']; ?>
        </span>
    </div>
    <div class="card-body p-3">
        <h5 class="card-title mt-0">
            <?php echo $args['object']->get_name(); ?>
        </h5>
        <p class="card-text">
            <?php echo $args['object']->get_description(); ?>
        </p>
    </div>
    <div class="card-footer p-2">
        <?php _e('Author:', 'wc1c'); ?> <?php echo $args['object']->get_author(); ?>
    </div>
</div>