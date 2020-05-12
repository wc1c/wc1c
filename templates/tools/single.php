<?php defined('ABSPATH') || exit; ?>

<div class="card col-24 mb-2 mt-2 pt-0 pb-0 pl-0 pr-0 border-0" style="border-radius: 0!important;">
    <div class="card-body p-3">
        <h5 class="card-title mt-0">
            <?php echo $args['object']->get_name(); ?>
        </h5>
        <p class="card-text">
            <?php echo $args['object']->get_description(); ?>
        </p>
    </div>
</div>

<?php do_action('wc1c_admin_tools_single_show'); ?>