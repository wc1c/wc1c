<?php defined('ABSPATH') || exit; ?>

<div class="mb-2 mt-2 rounded-3 bg-white">
    <div class="card-header p-2">
        <span class="card-title mt-0 mb-0 float-start">
	        <?php _e('Extension ID:', 'wc1c'); ?> <b><?php echo $args['id']; ?></b>
        </span>
        <span class="float-end">
	        <?php _e('Version:', 'wc1c'); ?>
            <span class="badge bg-success">
             <?php echo $args['object']->getMeta('version', 'none'); ?>
            </span>
        </span>
        <div class="clearfix"></div>
    </div>
    <div class="card-body p-3 pt-4 pb-4">
        <h2 class="card-title mt-0 mb-2">
		    <?php echo $args['object']->getMeta('name', 'none'); ?>
        </h2>
        <p class="card-text mt-0">
            <?php echo $args['object']->getMeta('description', 'none'); ?>
        </p>

    </div>
    <div class="card-footer p-0">

    </div>
</div>