<?php defined('ABSPATH') || exit;?>

<div class="configurations-all">
    <form method="post" action="">
		<?php
		    $list_table = $args['object'];
		    $list_table->prepareItems();
		?>

        <div class="p-1 bg-white rounded-3 mt-2 clearfix">
		    <?php $list_table->views(); ?>
        </div>

		<?php $list_table->display(); ?>
    </form>
</div>
