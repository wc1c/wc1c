<?php namespace Digiom\Wotices\Types;

defined('ABSPATH') || exit;

use Digiom\Wotices\Abstracts\NoticeAbstract;

/**
 * Class ErrorNotice
 *
 * @package Digiom\Wotices\Types
 */
class ErrorNotice extends NoticeAbstract
{
	/**
	 * ErrorNotice constructor.
	 *
	 * @param array $args
	 */
	public function __construct($args)
	{
		$this->setType('error');

		parent::__construct($args);
	}

	/**
	 * @param boolean $display
	 *
	 * @return string|void
	 */
	public function output($display)
	{
		$classes = ['error', 'updated', 'notice'];
		$dismiss = '';

		if($this->isDismissible())
		{
			$classes[] = 'is-dismissible';
			$dismiss = '<button id="notice-' . esc_attr($this->getId()) . '" class="notice-dismiss" type="button"><span class="screen-reader-text">' . __('Close', 'wsklad' ) . '</span></button>';
		}

		$content = sprintf
		(
			'<div id="%1$s" class="%2$s"><p><strong>%3$s</strong></p>%4$s <div>%5$s</div></div>',
			esc_attr($this->getId()),
			esc_attr(implode(' ', $classes)),
			$this->getData(),
			$dismiss,
			$this->getExtraData()
		);

		if($display === false)
		{
			return $content;
		}

		echo $content;
	}
}