<?php namespace Wc1c\Admin\Wizards;

use Wc1c\Admin\Wizards\Setup\Check;
use Wc1c\Admin\Wizards\Setup\Complete;
use Wc1c\Admin\Wizards\Setup\Database;
use Wc1c\Exceptions\Exception;
use Wc1c\Traits\SingletonTrait;

defined('ABSPATH') || exit;

/**
 * SetupWizard
 *
 * @package Wc1c\Admin\Wizards
 */
final class SetupWizard extends WizardAbstract
{
	use SingletonTrait;

	/**
	 * SetupWizard constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('setup');
		$this->setDefaultSteps();
		$this->setStep(isset($_GET[$this->getId()]) ? sanitize_key($_GET[$this->getId()]) : current(array_keys($this->getSteps())));

		$this->init();
	}

	/**
	 * Initialize
	 */
	public function init()
	{
		add_filter('wc1c_admin_init_sections', [$this, 'hideSections'], 20, 1);
		add_filter('wc1c_admin_init_sections_current', [$this, 'setSectionsCurrent'], 20, 1);
		add_action('wc1c_admin_show', [$this, 'route']);
	}

	/**
	 * @param $sections
	 *
	 * @return array
	 */
	public function hideSections($sections)
	{
		$default_sections[$this->getId()] =
		[
			'title' => __('Setup wizard', 'wc1c'),
			'visible' => true,
			'callback' => [__CLASS__, 'instance']
		];

		return $default_sections;
	}

	/**
	 * @param $section
	 *
	 * @return string
	 */
	public function setSectionsCurrent($section)
	{
		return $this->getId();
	}

	/**
	 * @return void
	 */
	private function setDefaultSteps()
	{
		$default_steps =
		[
			'check' =>
			[
				'name' => __('Compatibility', 'wc1c'),
				'callback' => [Check::class, 'instance'],
			],
			'database' =>
			[
				'name' => __('Database', 'wc1c'),
				'callback' => [Database::class, 'instance'],
			],
			'complete' =>
			[
				'name' => __('Completing', 'wc1c'),
				'callback' => [Complete::class, 'instance'],
			],
		];

		$this->setSteps($default_steps);
	}
}