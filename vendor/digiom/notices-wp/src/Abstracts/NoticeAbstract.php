<?php
/**
 * Namespace
 */
namespace Digiom\WordPress\Notices\Abstracts;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Class NoticeAbstract
 *
 * @package Digiom\WordPress\Notices
 */
abstract class NoticeAbstract
{
	/**
	 * @var string|int Max 255
	 */
	protected $id = '';

	/**
	 * @var string Max 255
	 */
	protected $type = '';

	/**
	 * @var string Max 255, html text available
	 */
	protected $data = '';

	/**
	 * @var string Html text available
	 */
	protected $extra_data = '';

	/**
	 * Маркер возможности сокрытия уведомления
	 *
	 * @var bool true - разрешить, false - запретить
	 */
	protected $dismissible = false;

	/**
	 * @var array
	 */
	protected $args = [];

	/**
	 * NoticeAbstract constructor.
	 *
	 * @param array $args
	 */
	public function __construct($args)
	{
		$this->setArgs($args);

		$this->setType($args['type']);
		$this->setId($args['id']);
		$this->setData($args['data']);
		$this->setExtraData($args['extra_data']);

		if(isset($args['dismissible']) && $args['dismissible'])
		{
			$this->setDismissible(true);
		}
	}

	/**
	 * Вывод уведомления, либо на экран, либо в виде значения
	 *
	 * @param boolean $display
	 *
	 * @return mixed
	 */
	abstract public function output($display);

	/**
	 * @return array
	 */
	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * @param $args
	 *
	 * @return $this
	 */
	public function setArgs($args)
	{
		$this->args = $args;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param string $data
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getExtraData()
	{
		return $this->extra_data;
	}

	/**
	 * @param string $extra_data
	 */
	public function setExtraData($extra_data)
	{
		$this->extra_data = $extra_data;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDismissible()
	{
		return $this->dismissible;
	}

	/**
	 * @param bool $dismissible
	 */
	public function setDismissible($dismissible)
	{
		$this->dismissible = $dismissible;
		return $this;
	}
}