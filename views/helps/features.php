<?php defined('ABSPATH') || exit; ?>

<h2><?php _e( 'Not a feature?', 'wc1c' ); ?></h2>

<p>
	<?php _e('First of all, you need to make sure - whether the necessary opportunity is really missing.', 'wc1c'); ?>
	<?php _e('It may be worth looking at the available settings or reading the documentation.', 'wc1c'); ?>
</p>

<p>
	<?php _e('Also, before requesting an opportunity, you need to make sure that:', 'wc1c'); ?>
</p>

<ul>
    <li><?php _e('Is the required feature added in WC1C updates.', 'wc1c'); ?></li>
    <li><?php _e('Whether the possibility is implemented by an additional extension to WC1C.', 'wc1c'); ?></li>
    <li><?php _e('Whether the desired opportunity is waiting for its implementation.', 'wc1c'); ?></li>
</ul>

<p>
	<?php _e('If the feature is added in WC1C updates, you just need to install the updated version.', 'wc1c'); ?>
</p>

<p>
	<?php _e('But if the feature is implemented in an extension to WC1C, then this feature should not be expected as part of WC1C and you need to install the extension.', 'wc1c'); ?>
	<?php _e('Because the feature implemented in the extension is so significant that it needed to create an extension for it.', 'wc1c'); ?>
</p>

<p>
	<a href="https://wc1c.info/features" class="button" target="_blank">
		<?php _e('Features', 'wc1c'); ?>
	</a>
    <a href="https://wc1c.info/extensions" class="button" target="_blank">
		<?php _e('Extensions', 'wc1c'); ?>
    </a>
</p>