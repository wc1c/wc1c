<?php defined('ABSPATH') || exit; ?>

<div class="card col-24 mb-3 pt-0 pb-0 pl-0 pr-0 border-0" style="border-radius: 0!important;">
    <div class="card-header p-2">
        <h4 class="m-0 p-0"><?php _e($args['title']); ?></h4>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped m-0 table-borderless">
            <tbody>
            <?php
            foreach($args['data'] as $data_key => $data_value)
            {
                wc1c_get_template('report_item.row.php', $data_value);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>