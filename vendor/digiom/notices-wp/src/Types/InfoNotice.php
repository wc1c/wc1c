<?php
/**
 * Namespace
 */
namespace Digiom\WordPress\Notices\Types;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Digiom\WordPress\Notices\Abstracts\NoticeAbstract;

/**
 * Class InfoNotice
 *
 * @package Digiom\WordPress\Notices\Types
 */
class InfoNotice extends NoticeAbstract
{
	/**
	 * InfoNotice constructor.
	 *
	 * @param $args
	 */
	public function __construct($args)
	{
		$this->setType('info');

		parent::__construct($args);
	}

	/**
	 * @return string|void
	 */
	public function output($display)
	{
		$classes = ['info', 'updated', 'notice'];
		$dismiss = '';

		if($this->isDismissible())
		{
			$classes[] = 'is-dismissible';
			$dismiss = '<button id="notice-' . esc_attr($this->getId()) . '" class="notice-dismiss" type="button"><span class="screen-reader-text">' . __('Close', 'wc1c' ) . '</span></button>';
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