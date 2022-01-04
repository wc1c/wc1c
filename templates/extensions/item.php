<?php defined('ABSPATH') || exit; ?>

<div class="mb-2 mt-2 border-0" style="border-radius: 0!important;">
    <div class="card-header p-2">
        <h2 class="card-title mt-0 mb-0">
	        <?php echo $args['object']->getMeta('name', 'none'); ?>
        </h2>
    </div>
    <div class="card-body p-2 pt-3 pb-3">
        <p class="card-text mt-0">
            <?php echo $args['object']->getMeta('description', 'none'); ?>
        </p>
    </div>
    <div class="card-footer p-2">
        <?php _e('Author:', 'wc1c'); ?>
        <span class="badge">
              <?php echo $args['object']->getMeta('author', 'none'); ?>
        </span>
	    <?php _e('Version:', 'wc1c'); ?>
        <span class="badge">
             <?php echo $args['object']->getMeta('version', 'none'); ?>
        </span>
        <?php _e('Extension ID:', 'wc1c'); ?>
        <span class="badge">
             <?php echo $args['id']; ?>
        </span>
    </div>
</div>