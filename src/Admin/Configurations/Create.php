<?php namespace Wc1c\Admin\Configurations;

defined('ABSPATH') || exit;

use Wc1c\Abstracts\ScreenAbstract;
use Wc1c\Exceptions\Exception;
use Wc1c\Traits\SingletonTrait;
use Wc1c\Traits\UtilityTrait;

/**
 * Create
 *
 * @package Wc1c\Admin\Configurations
 */
class Create extends ScreenAbstract
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * @throws Exception
	 */
	public function __construct()
	{
		$form = new CreateForm();
		$form->load_fields();
		$form->save();

		add_action('wc1c_admin_configurations_form_create_show', [$form, 'outputForm'], 10);

		parent::__construct();
	}

	/**
	 * Error
	 */
	public function wrapError()
	{
		wc1c()->views()->getView('configurations/error.php');
	}

	/**
	 * Show page
	 *
	 * @return void
	 */
	public function output()
	{
		$args['back_url'] = $this->utilityAdminConfigurationsGetUrl('all');

		wc1c()->views()->getView('configurations/create.php', $args);
	}
}