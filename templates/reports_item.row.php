<?php defined('ABSPATH') || exit; ?>

<tr class="">
    <td class="" style="width: 40%;">
	    <?php
	    echo esc_html__($args['title']);
	    ?>
    </td>
    <td class="">
	    <?php
	    echo esc_html__(apply_filters('wc1c_admin_report_data_row_print', $args['data']));
	    ?>
    </td>
</tr>