<?php defined('ABSPATH') || exit; ?>

<div class="bg-white p-2 rounded-3 mt-2">
	<?php do_action('wc1c_admin_tools_all_before_show'); ?>

	<?php

        foreach($args['object']->tools as $tool_id => $tool_object)
        {
            if(!class_exists($tool_object))
            {
                continue;
            }

            $tool = new $tool_object();

            $args =
                [
                    'id' => $tool_id,
                    'name' => $tool->getName(),
                    'description' => $tool->getDescription(),
                    'url' => $args['object']->utilityAdminToolsGetUrl($tool_id),
                    'object' => $tool,
                ];

            wc1c()->templates()->getTemplate('tools/item.php', $args);
        }

    ?>

	<?php do_action('wc1c_admin_tools_all_after_show'); ?>
</div>