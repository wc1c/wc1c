<?php defined('ABSPATH') || exit; ?>

<div class="card col-24 mb-3 pt-0 pb-0 pl-0 pr-0">
    <div class="card-header"><span class="badge badge-info p-2">ID: <?php echo $args['id']; ?></span>
        <span class="badge badge-info p-2"> Version: <?php echo $args['object']->get_version(); ?></span></div>
    <div class="card-body">
        <h5 class="card-title mt-0"><?php echo $args['object']->get_name(); ?></h5>
        <p class="card-text"><?php echo $args['object']->get_description(); ?></p>
    </div>
    <div class="card-footer">
        <small class="text-muted"></small>
    </div>
</div>