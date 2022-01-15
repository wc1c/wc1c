<?php defined('ABSPATH') || exit; ?>

<div class="mb-1 mt-1 w-100" style="border-radius: 0!important; border: 1px solid #c3c4c7;">
    <div class="card-body p-3">
        <h2 class="card-title mt-0">
            <?php echo $args['name']; ?>
        </h2>
        <p class="card-text">
            <?php echo $args['description']; ?>
        </p>
    </div>
    <div class="card-footer p-3">
       <a class="text-decoration-none button button-primary" href="<?php echo $args['url']; ?>">
	       <?php _e('Open', 'wc1c'); ?>
       </a>
    </div>
</div>