<?php namespace Wc1c\Extensions;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
use Wc1c\Extensions\Contracts\ExtensionContract;
use Wc1c\Traits\SingletonTrait;

/**
 * Core
 *
 * @package Wc1c\Extensions
 */
final class Core
{
	use SingletonTrait;

	/**
	 * @var array All loaded
	 */
	private $extensions = [];

	/**
	 * Set
	 *
	 * @param array $extensions
	 *
	 * @return void
	 * @throws Exception
	 */
	public function set($extensions)
	{
		if(!is_array($extensions))
		{
			throw new Exception(__('Set $extensions is not valid.', 'wc1c'));
		}

		$this->extensions = $extensions;
	}

	/**
	 * Initializing extensions
	 *
	 * @param string $extension_id If an extension ID is specified, only the specified extension is loaded
	 *
	 * @return void
	 * @throws Exception
	 */
	public function init($extension_id = '')
	{
		try
		{
			$extensions = $this->get();
		}
		catch(Exception $e)
		{
			throw $e;
		}

		/**
		 * Init specified extension
		 */
		if('' !== $extension_id)
		{
			if(!array_key_exists($extension_id, $extensions))
			{
				throw new Exception(__('Extension not found by id.', 'wc1c'));
			}

			if(!$extensions[$extension_id] instanceof ExtensionContract)
			{
				throw new Exception(__('Extension is not implementation ExtensionContract. Skipped init.', 'wc1c'));
			}

			if($extensions[$extension_id]->isInitialized())
			{
				return;
			}

			try
			{
				$extensions[$extension_id]->init();
				$extensions[$extension_id]->setInitialized(true);
			}
			catch(Exception $e)
			{
				throw new Exception(__('Init extension exception:', 'wc1c') . ' ' . $e->getMessage());
			}

			$this->set($extensions);

			return;
		}

		/**
		 * Init all extensions
		 */
		foreach($extensions as $extension => $extension_object)
		{
			try
			{
				$this->init($extension);
			}
			catch(Exception $e)
			{
				wc1c()->log()->warning($e->getMessage(), ['exception' => $e]);
			}
		}
	}

	/**
	 * Get initialized extensions
	 *
	 * @param string $extension_id
	 *
	 * @return array|ExtensionContract
	 * @throws Exception
	 */
	public function get($extension_id = '')
	{
		if('' !== $extension_id)
		{
			if(array_key_exists($extension_id, $this->extensions))
			{
				return $this->extensions[$extension_id];
			}

			throw new Exception(__('Get extension by id is unavailable.', 'wc1c'));
		}

		return $this->extensions;
	}

	/**
	 * Extensions load
	 *
	 * @return void
	 * @throws Exception
	 */
	public function load()
	{
		$extensions = [];

		if(has_filter('wc1c_extensions_loading') && 'yes' === wc1c()->settings('main')->get('extensions', 'yes'))
		{
			$extensions = apply_filters('wc1c_extensions_loading', $extensions);
		}

		try
		{
			$this->set($extensions);
		}
		catch(Exception $e)
		{
			throw new Exception(__('Extensions load exception:', 'wc1c') . ' ' . $e->getMessage());
		}
	}
}