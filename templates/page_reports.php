<?php defined('ABSPATH') || exit; ?>

<div class="wrap">

    <?php wc1c_get_template('reports_header.php'); ?>
    
    <?php do_action('wc1c_admin_report_before_show'); ?>

	<?php do_action('wc1c_admin_report_show'); ?>

	<?php do_action('wc1c_admin_report_after_show'); ?>

</div>