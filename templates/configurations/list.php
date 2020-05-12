<?php defined('ABSPATH') || exit;?>

<?php do_action('wc1c_admin_before_configurations_list_show') ?>

<form id="outbox-filter" method="post" action="">
    <?php do_action('wc1c_admin_configurations_list_show') ?>
</form>

<?php do_action('wc1c_admin_after_configurations_list_show') ?>