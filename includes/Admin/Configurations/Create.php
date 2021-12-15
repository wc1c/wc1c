<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Configurations;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Abstracts\ScreenAbstract;
use Wc1c\Exceptions\Exception;
use Wc1c\Traits\SingletonTrait;

/**
 * Class Create
 *
 * @package Wc1c\Admin\Configurations
 */
class Create extends ScreenAbstract
{
	use SingletonTrait;

	/**
	 * @throws Exception
	 */
	public function __construct()
	{
		$form = new CreateForm();
		$form->load_fields();
		$form->save();

		add_action(WC1C_ADMIN_PREFIX . 'configurations_form_create_show', [$form, 'output_form'], 10);

		parent::__construct();
	}

	/**
	 * Error
	 */
	public function wrapError()
	{
		wc1c_get_template('configurations/error.php');
	}

	/**
	 * Show page
	 *
	 * @return void
	 */
	public function output()
	{
		wc1c_get_template('configurations/create.php');
	}
}