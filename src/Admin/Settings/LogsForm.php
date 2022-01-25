<?php namespace Wc1c\Admin\Settings;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
use Wc1c\Settings\LogsSettings;

/**
 * LogsForm
 *
 * @package Wc1c\Admin\Settings
 */
class LogsForm extends Form
{
	/**
	 * LogsForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->set_id('settings-logs');
		$this->setSettings(new LogsSettings());

		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_logger'], 10);

		$this->init();
	}

	/**
	 * Add settings for logger
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_logger($fields)
	{
		$fields['logger_level'] =
		[
			'title' => __('Level for main events', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => '300',
			'options' =>
				[
					'100' => __('DEBUG (100)', 'wc1c'),
					'200' => __('INFO (200)', 'wc1c'),
					'250' => __('NOTICE (250)', 'wc1c'),
					'300' => __('WARNING (300)', 'wc1c'),
					'400' => __('ERROR (400)', 'wc1c'),
				],
		];

		$fields['logger_files_max'] =
		[
			'title' => __('Maximum files', 'wc1c'),
			'type' => 'text',
			'description' => __('Log files created daily. This option on the maximum number of stored files. By default saved of the logs are for the last 30 days.', 'wc1c'),
			'default' => 30,
			'css' => 'min-width: 20px;',
		];

		$fields['logger_wc1c'] =
		[
			'title' => __('Access to technical events', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Allow the WC1C team to access technical events?', 'wc1c'),
			'description' => __('If allowed, the WC1C team will be able to access technical events and release the necessary updates based on them.', 'wc1c'),
			'default' => 'no'
		];

		$fields['interface_title'] =
		[
			'title' => __('Levels by context', 'wc1c'),
			'type' => 'title',
			'description' => __('Event log settings based on context.', 'wc1c'),
		];

		$fields['logger_receiver_level'] =
		[
			'title' => __('Receiver', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded the Receiver events in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => 'logger_level',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wc1c'),
				'100' => __('DEBUG (100)', 'wc1c'),
				'200' => __('INFO (200)', 'wc1c'),
				'250' => __('NOTICE (250)', 'wc1c'),
				'300' => __('WARNING (300)', 'wc1c'),
				'400' => __('ERROR (400)', 'wc1c'),
			],
		];

		$fields['logger_tools_level'] =
		[
			'title' => __('Tools', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded the tools events in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => 'logger_level',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wc1c'),
				'100' => __('DEBUG (100)', 'wc1c'),
				'200' => __('INFO (200)', 'wc1c'),
				'250' => __('NOTICE (250)', 'wc1c'),
				'300' => __('WARNING (300)', 'wc1c'),
				'400' => __('ERROR (400)', 'wc1c'),
			],
		];

		$fields['logger_schemas_level'] =
		[
			'title' => __('Schemas', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded the schemas events in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => 'logger_level',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wc1c'),
				'100' => __('DEBUG (100)', 'wc1c'),
				'200' => __('INFO (200)', 'wc1c'),
				'250' => __('NOTICE (250)', 'wc1c'),
				'300' => __('WARNING (300)', 'wc1c'),
				'400' => __('ERROR (400)', 'wc1c'),
			],
		];

		$fields['logger_configurations_level'] =
		[
			'title' => __('Configurations', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded the configurations events in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => 'logger_level',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wc1c'),
				'100' => __('DEBUG (100)', 'wc1c'),
				'200' => __('INFO (200)', 'wc1c'),
				'250' => __('NOTICE (250)', 'wc1c'),
				'300' => __('WARNING (300)', 'wc1c'),
				'400' => __('ERROR (400)', 'wc1c'),
			],
		];

		return $fields;
	}
}