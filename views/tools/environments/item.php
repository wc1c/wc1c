<?php defined('ABSPATH') || exit; ?>

<div class="mb-3 mt-2 pt-0 pb-0 pl-0 pr-0" style=" border: 1px solid rgba(0,0,0,.125);">
    <div class="card-header p-2">
        <h2 class="m-0 p-0 pb-1"><?php _e($args['title']); ?></h2>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped m-0 table-bordered">
            <tbody>
            <?php
                foreach($args['data'] as $data_key => $data_value)
                {
                    wc1c()->views()->getView('tools/environments/item_row.php', $data_value);
                }
            ?>
            </tbody>
        </table>
    </div>
</div>