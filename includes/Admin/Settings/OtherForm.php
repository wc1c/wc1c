<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Settings;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Exceptions\Exception;
use Wc1c\Settings\OtherSettings;

/**
 * Class OtherForm
 *
 * @package Wc1c\Admin\Settings
 */
class OtherForm extends Form
{
	/**
	 * OtherForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->set_id('settings-other');
		$this->setSettings(new OtherSettings());

		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_main'], 10);

		$this->init();
	}

	/**
	 * Main fields
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_main($fields)
	{
		$fields['tracking'] =
		[
			'title' => __('Usage data', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Allow sending usage data to WC1C servers?', 'wc1c'),
			'description' => __('If enabled, the site will send anonymous data about plugin usage to WC1C servers once a week in the background.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}
}