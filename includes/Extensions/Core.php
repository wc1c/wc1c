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
	 * Core constructor.
	 */
	public function __construct()
	{

	}

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
			throw new Exception('$extensions is not valid');
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
			throw new Exception('Get extensions exception - ' . $e->getMessage());
		}

		if(!is_array($extensions))
		{
			throw new Exception('$extensions is not array');
		}

		/**
		 * Init specified extension
		 */
		if('' !== $extension_id)
		{
			if(!array_key_exists($extension_id, $extensions))
			{
				throw new Exception('extension not found by id');
			}

			$init_extension = $extensions[$extension_id];

			if(!is_object($init_extension))
			{
				throw new Exception('$extensions[$extension_id] is not object');
			}

			if($init_extension->isInitialized())
			{
				throw new Exception('Old extension initialized.');
			}

			if(!method_exists($init_extension, 'init'))
			{
				throw new Exception('Method init is not found');
			}

			try
			{
				$init_extension->init();
			}
			catch(Exception $e)
			{
				throw new Exception('Init extension exception - ' . $e->getMessage());
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
				wc1c()->log()->error($e->getMessage(), $e);
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

			throw new Exception('$extension_id is unavailable');
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

		if('yes' === wc1c()->settings('main')->get('extensions', 'yes'))
		{
			$extensions = apply_filters(WC1C_PREFIX . 'extensions_loading', $extensions);
		}

		try
		{
			$this->set($extensions);
		}
		catch(Exception $e)
		{
			throw new Exception('Extensions set exception - ' . $e->getMessage());
		}
	}
}