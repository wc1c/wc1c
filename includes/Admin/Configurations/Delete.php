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
use Wc1c\Exceptions\Exception;
use Wc1c\Configuration;
use Wc1c\Traits\Singleton;

/**
 * Class Delete
 *
 * @package Wc1c\Admin\Configurations
 */
class Delete
{
	use Singleton;

	/**
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * Delete constructor.
	 * @throws Exception
	 */
	public function __construct()
	{
		$configuration_id = wc1c_get_var($_GET['configuration_id'], 0);
		$error = false;

		try
		{
			$configuration = new Configuration($configuration_id);

			if(!$configuration->getStorage()->isExistingById($configuration_id))
			{
				$error = true;
			}

			$this->setConfiguration($configuration);
		}
		catch(Exception $e)
		{
			$error = true;
		}

		if($error)
		{
			add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'output_error'], 10);
		}
		else
		{
			$this->process($this->getConfiguration());
		}
	}

	/**
	 * Delete processing
	 *
	 * @param $configuration
	 *
	 * @throws Exception
	 */
	public function process($configuration)
	{
		$delete = false;
		$redirect = true;
		$force_delete = false;
		$configuration_status = $configuration->getStatus();
		$notice_args['type'] = 'error';
		$notice_args['data'] = __('Error. The configuration to be deleted is active and cannot be deleted.', 'wc1c');

		/**
		 * Защита от удаления активных соединений
		 */
		if(!$configuration->isStatus('active') && !$configuration->isStatus('processing'))
		{
			/**
			 * Окончательное удаление черновиков без корзины
			 */
			if($configuration_status === 'draft' && 'yes' === wc1c()->settings()->get('configurations_draft_delete', 'yes'))
			{
				$delete = true;
				$force_delete = true;
			}

			/**
			 * Помещение в корзину без удаления
			 */
			if($configuration_status !== 'deleted' && $force_delete === false)
			{
				$delete = true;
			}

			/**
			 * Окончательное удаление из корзины - вывод формы для подтверждения удаления
			 */
			if($configuration_status === 'deleted')
			{
				$redirect = false;
				$delete_form = new DeleteForm();

				if(!$delete_form->save())
				{
					add_action(WC1C_ADMIN_PREFIX . 'configurations_form_delete_show', [$delete_form, 'output_form']);
					add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'output'], 10);
				}
				else
				{
					$delete = true;
					$force_delete = true;
					$redirect = true;
				}
			}

			/**
			 * Удаление с переносом в список всех учетных записей и выводом уведомления об удалении
			 */
			if($delete)
			{
				$notice_args =
				[
					'type' => 'update',
					'data' => __('The configuration has been marked as deleted.', 'wc1c')
				];

				if($force_delete)
				{
					$notice_args =
					[
						'type' => 'update',
						'data' => __('The configuration has been successfully deleted.', 'wc1c')
					];
				}

				if(!$configuration->delete($force_delete))
				{
					$notice_args['type'] = 'error';
					$notice_args['data'] = __('Deleting error. Please retry again.', 'wc1c');
				}
			}
		}

		if($redirect)
		{
			wc1c_admin()->notices()->create($notice_args);
			wp_safe_redirect(wc1c_admin_configurations_get_url());
			die;
		}
	}

	/**
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @param Configuration $configuration
	 */
	public function setConfiguration($configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * Output error
	 */
	public function output_error()
	{
		wc1c_get_template('configurations/delete_error.php');
	}

	/**
	 * Output permanent remove
	 *
	 * @return void
	 */
	public function output()
	{
		wc1c_get_template('configurations/delete.php');
	}
}