<?php namespace Wc1c\Schemas\DefaultCML;

defined('ABSPATH') || exit;

use Wc1c\Abstracts\SchemaAbstract;
use Wc1c\Log\Logger;

/**
 * Core
 *
 * @package Wc1c\Schemas\DefaultCML
 */
final class Core extends SchemaAbstract
{
	/**
	 * Core constructor.
	 */
	public function __construct()
	{
		$this->setId('DefaultCML');
		$this->setVersion('0.1.0');
		$this->setName(__('Default schema based on CML', 'wc1c'));
		$this->setDescription(__('Standard data exchange using the standard exchange algorithm from 1C via CommerceML. Exchanges only contains products data.', 'wc1c'));
	}

	/**
	 * Initialize
	 *
	 * @return bool
	 */
	public function init()
	{
		$this->setOptions($this->configuration()->getOptions());

		if(true === wc1c()->context()->isWc1cAdmin())
		{
			$admin = Admin::instance();
			$admin->setCore($this);
			$admin->initConfigurations();
		}

		if(true === wc1c()->context()->isReceiver())
		{
			$receiver = Receiver::instance();
			$receiver->setCore($this);
			$receiver->initHandler();
		}

		return true;
	}

	/**
	 * Logger
	 *
	 * @return Logger
	 */
	public function log($channel = 'configurations')
	{
		if($channel === 'configurations')
		{
			$name = $this->configuration()->getUploadDirectory('logs') . DIRECTORY_SEPARATOR . 'main';
			return wc1c()->log($channel, $name);
		}

		if($channel === 'schemas')
		{
			$name = $this->configuration()->getSchema();
			return wc1c()->log($channel, $name);
		}

		$name = $this->configuration()->getUploadDirectory('logs') . DIRECTORY_SEPARATOR . $channel;
		return wc1c()->log('configurations', $name);
	}
}