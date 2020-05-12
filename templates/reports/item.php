<?php defined('ABSPATH') || exit; ?>

<div class="card col-24 mb-2 mt-2 pt-0 pb-0 pl-0 pr-0 border-0" style="border-radius: 0!important;">
    <div class="card-header p-2">
        <h4 class="m-0 p-0 pb-1"><?php _e($args['title']); ?></h4>
    </div>
    <div class="card-body p-0">
        <table class="table  m-0 table-bordered">
            <tbody>
            <?php
            foreach($args['data'] as $data_key => $data_value)
            {
                wc1c_get_template('reports_item.row.php', $data_value);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>