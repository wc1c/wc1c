<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Traits;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Configuration;
use Wc1c\Exceptions\Exception;

/**
 * ProcessConfigurationTrait
 *
 * @package Wc1c\Admin\Traits
 */
trait ProcessConfigurationTrait
{
	/**
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * @param $configuration_id
	 *
	 * @return bool
	 */
	public function setConfiguration($configuration_id)
	{
		$error = false;

		try
		{
			$configuration = new Configuration($configuration_id);

			if(!$configuration->getStorage()->isExistingById($configuration_id))
			{
				$error = true;
			}

			$this->configuration = $configuration;
		}
		catch(Exception $e)
		{
			$error = true;
		}

		return $error;
	}

	/**
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}
}