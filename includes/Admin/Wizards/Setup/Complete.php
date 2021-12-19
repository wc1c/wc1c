<?php namespace Wc1c\Admin\Wizards\Setup;

use Wc1c\Admin\Wizards\StepAbstract;
use Wc1c\Traits\SingletonTrait;

defined('ABSPATH') || exit;

/**
 * Complete
 *
 * @package Wc1c\Admin\Wizards
 */
class Complete extends StepAbstract
{
	use SingletonTrait;

	/**
	 * Complete constructor.
	 */
	public function __construct()
	{
		$this->setId('complete');
	}

	/**
	 * Precessing step
	 */
	public function process()
	{
		delete_option('wc1c_wizard');

		add_action(WC1C_PREFIX . 'wizard_content_output', [$this, 'output'], 10);
	}

	/**
	 * Output wizard content
	 *
	 * @return void
	 */
	public function output()
	{
		$args =
		[
			'step' => $this
		];

		wc1c()->templates()->getTemplate('wizards/steps/complete.php', $args);
	}
}