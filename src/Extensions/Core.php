<?php namespace Wc1c\Extensions;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
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
	 * @return boolean
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

		if(!is_array($extensions))
		{
			throw new Exception(__('Init $extensions is not array.', 'wc1c'));
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

			$init_extension = $extensions[$extension_id];

			if(!is_object($init_extension))
			{
				throw new Exception(__('$extensions[$extension_id] is not object.', 'wc1c'));
			}

			if($init_extension->isInitialized())
			{
				throw new Exception(__('Old extension initialized.', 'wc1c'));
			}

			if(!method_exists($init_extension, 'init'))
			{
				throw new Exception(__('Method init is not found in extension.', 'wc1c'));
			}

			try
			{
				$init_extension->init();
			}
			catch(Exception $e)
			{
				throw new Exception(__('Init extension exception:', 'wc1c') . ' ' . $e->getMessage());
			}

			$init_extension->setInitialized(true);

			return true;
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
				wc1c()->log()->error($e->getMessage(), ['exception' => $e]);
				continue;
			}
		}

		return true;
	}

	/**
	 * Get initialized extensions
	 *
	 * @param string $extension_id
	 *
	 * @return array|object
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

			throw new Exception(__('$extension_id is unavailable.', 'wc1c'));
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