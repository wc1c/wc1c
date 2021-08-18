<?php defined('ABSPATH') || exit; ?>

<tr class="">
    <td class="" style="width: 40%;">
	    <?php
	    esc_html_e($args['title']);
	    ?>
    </td>
    <td class="">
	    <?php
	    esc_html_e(apply_filters('wc1c_admin_environments_data_row_print', $args['data']));
	    ?>
    </td>
</tr>