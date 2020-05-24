<?php defined('ABSPATH') || exit; ?>

<div class="card col-24 mb-2 mt-2 pt-0 pb-0 pl-0 pr-0 border-0" style="border-radius: 0!important;">
    <div class="card-header p-2">
        <h5 class="card-title mt-0 mb-0">
	        <?php echo $args['object']->get_meta('name', 'none'); ?>
        </h5>
    </div>
    <div class="card-body p-2 pt-3 pb-3">
        <p class="card-text mt-0">
            <?php echo $args['object']->get_meta('description', 'none'); ?>
        </p>
    </div>
    <div class="card-footer p-2">
        <?php _e('Author:', 'wc1c'); ?>
        <span class="badge badge-info p-1" style="font-size: 1em;">
              <?php echo $args['object']->get_meta('author', 'none'); ?>
        </span>
	    <?php _e('Version:', 'wc1c'); ?>
        <span class="badge badge-info p-1" style="font-size: 1em;">
             <?php echo $args['object']->get_meta('version', 'none'); ?>
        </span>
        <?php _e('Extension ID:', 'wc1c'); ?>
        <span class="badge badge-info p-1" style="font-size: 1em;">
             <?php echo $args['id']; ?>
        </span>
    </div>
</div>