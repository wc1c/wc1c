<?php namespace Wc1c\Admin\Settings;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
use Wc1c\Settings\MainSettings;

/**
 * MainForm
 *
 * @package Wc1c\Admin
 */
class MainForm extends Form
{
	/**
	 * MainForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->set_id('settings-main');
		$this->setSettings(new MainSettings());

		add_filter('wc1c_' . $this->get_id() . '_form_load_fields', [$this, 'init_fields_main'], 10);
		add_filter('wc1c_' . $this->get_id() . '_form_load_fields', [$this, 'init_form_fields_tecodes'], 10);

		add_filter('wc1c_' . $this->get_id() . '_form_load_fields', [$this, 'init_fields_configurations'], 20);
		add_filter('wc1c_' . $this->get_id() . '_form_load_fields', [$this, 'init_fields_technical'], 30);

		$this->init();
	}

	/**
	 * Add fields for Configurations
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_configurations($fields)
	{
		$fields['configurations_title'] =
		[
			'title' => __('Configurations', 'wc1c'),
			'type' => 'title',
			'description' => __('Some settings for the configurations.', 'wc1c'),
		];

		$fields['configurations_unique_name'] =
		[
			'title' => __('Unique names', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Require unique names for configurations?', 'wc1c'),
			'description' => __('If enabled, will need to provide unique names for the configurations.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['configurations_show_per_page'] =
		[
			'title' => __('Number in the list', 'wc1c'),
			'type' => 'text',
			'description' => __('The number of displayed configurations on one page.', 'wc1c'),
			'default' => 10,
			'css' => 'min-width: 20px;',
		];

		$fields['configurations_draft_delete'] =
		[
			'title' => __('Deleting drafts without trash', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable deleting drafts without placing them in the trash?', 'wc1c'),
			'description' => __('If enabled, configurations for connections in the draft status will be deleted without being added to the basket.', 'wc1c'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Add for Technical
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_technical($fields)
	{
		$fields['technical_title'] =
		[
			'title' => __('Technical settings', 'wc1c'),
			'type' => 'title',
			'description' => __('Used to set up the environment.', 'wc1c'),
		];

		$fields['php_max_execution_time'] =
		[
			'title' => __('Maximum time for execution PHP', 'wc1c'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s <br /> %s <b>%s</b> <br /> %s',
				__('Value is seconds. WC1C will run until a time limit is set.', 'wc1c'),
				__('Server value:', 'wc1c'),
				wc1c()->environment()->get('php_max_execution_time'),
				__('If specify 0, the time limit will be disabled. Specifying 0 is not recommended, it is recommended not to exceed the server limit.', 'wc1c')
			),
			'default' => wc1c()->environment()->get('php_max_execution_time'),
			'css' => 'min-width: 100px;',
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum request size', 'wc1c'),
			'type' => 'text',
			'description' => __('The setting must not take a size larger than specified in the server settings.', 'wc1c'),
			'default' => wc1c()->environment()->get('php_post_max_size'),
			'css' => 'min-width: 100px;',
		];

		return $fields;
	}

	/**
	 * Add for Main
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_main($fields)
	{
		$fields['receiver'] =
		[
			'title' => __('Receiver', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable data Receiver: background requests?', 'wc1c'),
			'description' => __('It is used to receive background requests from 1C in exchange schemes. Do not disable this option if you do not know what it is for.', 'wc1c'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Add fields for tecodes
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_form_fields_tecodes($fields)
	{
		$buy_url = esc_url('https://wc1c.info/market/code');

		$fields['tecodes'] =
		[
			'title' => __('Support', 'wc1c'),
			'type' => 'title',
			'description' => sprintf
            (
                '%s <a target="_blank" href="%s">%s</a>. %s',
                __('The code can be obtained from the plugin website:', 'wc1c'),
                $buy_url,
                $buy_url,
                __('Users with active codes participate in the development of integration with 1C, they have a connection with developers and other additional features.', 'wc1c')
            ),
        ];

		if(wc1c()->tecodes()->is_valid())
		{
			$fields['tecodes_status'] =
            [
                'title' => __('Status', 'wc1c'),
                'type' => 'tecodes_status',
                'class' => 'p-2',
                'description' => __('Support code activated. To activate another code, you can enter it again.', 'wc1c'),
                'default' => ''
            ];
		}

        $fields['tecodes_code'] =
        [
            'title' => __('Code for activation', 'wc1c'),
            'type' => 'tecodes_text',
            'class' => 'p-2',
            'description' => sprintf
            (
                '%s <br /> %s <b>%s</b>',
                __('If enter the correct code, the current environment will be activated. Enter the code only on the actual workstation.', 'wc1c'),
                __('Current license API status:', 'wc1c'),
                wc1c()->tecodes()->api_get_status()
            ),
            'default' => ''
        ];

		return $fields;
	}

	/**
	 * Generate Tecodes data HTML
	 *
	 * @param string $key Field key.
	 * @param array  $data Field data.
	 *
	 * @return string
	 */
	public function generate_tecodes_status_html($key, $data)
	{
		$field_key = $this->get_prefix_field_key($key);
		$defaults = array
		(
			'title' => '',
			'disabled' => false,
			'class' => '',
			'css' => '',
			'placeholder' => '',
			'type' => 'text',
			'desc_tip' => false,
			'description' => '',
			'custom_attributes' => [],
		);

		$data = wp_parse_args($data, $defaults);

		ob_start();
		?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); ?></label>
            </th>
            <td class="forminp">
				<?php echo $this->get_description_html($data); // WPCS: XSS ok.?>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Tecodes Text Input HTML
	 *
	 * @param string $key Field key.
	 * @param array  $data Field data.
	 *
	 * @return string
	 */
	public function generate_tecodes_text_html($key, $data)
	{
		$field_key = $this->get_prefix_field_key($key);
		$defaults = array
		(
			'title' => '',
			'disabled' => false,
			'class' => '',
			'css' => '',
			'placeholder' => '',
			'type' => 'text',
			'desc_tip' => false,
			'description' => '',
			'custom_attributes' => [],
		);

		$data = wp_parse_args($data, $defaults);

		ob_start();
		?>
		<tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); ?></label>
            </th>
			<td class="forminp">
                <div class="input-group">
                    <input class="form-control input-text regular-input <?php echo esc_attr($data['class']); ?>"
                    type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field_key); ?>"
                    id="<?php echo esc_attr($field_key); ?>" style="<?php echo esc_attr($data['css']); ?>"
                    value="<?php echo esc_attr($this->get_field_data($key)); ?>"
                    placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled($data['disabled'], true); ?> <?php echo $this->get_custom_attribute_html($data); // WPCS: XSS ok.
                    ?> />
                    <button name="save" class="btn btn-primary" type="submit" value="<?php _e('Activate', 'wc1c') ?>"><?php _e('Activate', 'wc1c') ?></button>
                </div>
                <?php echo $this->get_description_html($data); // WPCS: XSS ok.?>
            </td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Validate tecodes code
     *
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	public function validate_tecodes_code_field($key, $value)
	{
		if($value === '')
		{
			return '';
		}

		wc1c()->tecodes()->set_code($value);
		wc1c()->tecodes()->validate();

		if(!wc1c()->tecodes()->is_valid())
		{
			$errors = wc1c()->tecodes()->get_errors();

			if(is_array($errors))
			{
				foreach(wc1c()->tecodes()->get_errors() as $error_key => $error)
				{
					wc1c()->admin()->notices()->create
					(
						[
							'type' => 'error',
							'data' => $error
						]
					);
				}
			}
		}

		return '';
	}
}