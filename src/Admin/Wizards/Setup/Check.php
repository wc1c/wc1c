<?php namespace Wc1c\Admin\Wizards\Setup;

use Wc1c\Admin\Wizards\StepAbstract;
use Wc1c\Traits\SingletonTrait;

defined('ABSPATH') || exit;

/**
 * Check
 *
 * @package Wc1c\Admin\Wizards
 */
class Check extends StepAbstract
{
	use SingletonTrait;

	/**
	 * Check constructor.
	 */
	public function __construct()
	{
		$this->setId('check');
	}

	/**
	 * Precessing step
	 */
	public function process()
	{
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

		wc1c()->views()->getView('wizards/steps/check.php', $args);
	}
}