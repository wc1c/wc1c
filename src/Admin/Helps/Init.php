<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Helps;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Traits\SingletonTrait;

/**
 * Init
 *
 * @package Wc1c\Admin\Helps
 */
final class Init
{
	use SingletonTrait;

	/**
	 * Init constructor.
	 */
	public function __construct()
	{
		add_action('current_screen', [$this, 'add_tabs'], 50);
	}

	/**
	 * Add help tabs
	 */
	public function add_tabs()
	{
		$screen = get_current_screen();

		if(!$screen)
		{
			return;
		}

		$screen->add_help_tab
		(
			[
				'id' => WC1C_PREFIX . 'help_tab',
				'title' => __('Help', 'wc1c'),
				'content' => wc1c()->templates()->getTemplateHtml('/helps/main.php')
			]
		);

		$screen->add_help_tab
		(
			[
				'id' => WC1C_PREFIX . 'bugs_tab',
				'title' => __('Found a bug?', 'wc1c'),
				'content' => wc1c()->templates()->getTemplateHtml('/helps/bugs.php')
			]
		);

		$screen->add_help_tab
		(
			[
				'id' => WC1C_PREFIX . 'features_tab',
				'title' => __('Not a feature?', 'wc1c'),
				'content' => wc1c()->templates()->getTemplateHtml('/helps/features.php')
			]
		);
	}
}