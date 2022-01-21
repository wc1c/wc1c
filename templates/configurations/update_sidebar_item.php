<?php defined('ABSPATH') || exit; ?>

<?php do_action(WC1C_ADMIN_PREFIX . 'configurations_update_before_sidebar_item_show'); ?>

<div class="card mb-2 mt-0 p-0" style="max-width: 100%; <?php if(isset($args['css'])) echo $args['css']; ?>">
    <?php if(isset($args['header'])): ?>
    <div class="card-header p-2">
        <?php echo $args['header']; ?>
    </div>
    <?php endif; ?>
    <?php if(isset($args['body'])): ?>
    <div class="card-body p-0">
	    <?php echo $args['body']; ?>
    </div>
    <?php endif; ?>
    <?php if(isset($args['footer'])): ?>
    <div class="card-footer p-2">
	    <?php echo $args['footer']; ?>
    </div>
	<?php endif; ?>
</div>

<?php do_action(WC1C_ADMIN_PREFIX . 'configurations_update_after_sidebar_item_show'); ?>