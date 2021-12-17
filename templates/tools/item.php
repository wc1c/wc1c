<?php defined('ABSPATH') || exit; ?>

<div class="mb-2 mt-2" style="border-radius: 0!important; border: 1px solid white; width: 100%;">
    <div class="card-body p-3">
        <h2 class="card-title mt-0">
            <?php echo $args['object']->getName(); ?>
        </h2>
        <p class="card-text">
            <?php echo $args['object']->getDescription(); ?>
        </p>
    </div>
    <div class="card-footer p-3">
       <a class="text-decoration-none button button-primary" href="<?php echo wc1c_admin_tools_get_url($args['object']->getId())?>">
	       <?php _e('Open', 'wc1c'); ?>
       </a>
    </div>
</div>