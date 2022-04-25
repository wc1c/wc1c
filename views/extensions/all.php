<?php defined('ABSPATH') || exit; ?>

<?php do_action('wc1c_admin_extensions_before_show'); ?>

<div class="extensions-all bg-white p-1 px-2 rounded-3 mt-2">
	<?php
        foreach($args['extensions'] as $extension_id => $extension_object)
        {
            $args =
            [
                'id' => $extension_id,
                'object' => $extension_object
            ];

            wc1c()->views()->getView('extensions/item.php', $args);
        }
	?>
</div>

<?php do_action('wc1c_admin_extensions_after_show'); ?>