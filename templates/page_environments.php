<?php defined('ABSPATH') || exit; ?>

<div class="wrap">

    <?php wc1c_get_template('environments/header.php'); ?>
    
    <?php do_action('wc1c_admin_environment_before_show'); ?>

	<?php do_action('wc1c_admin_environment_show'); ?>

	<?php do_action('wc1c_admin_environment_after_show'); ?>

</div>