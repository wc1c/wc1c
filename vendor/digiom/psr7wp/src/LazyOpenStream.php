<?php
/**
 * Namespace
 */
namespace Digiom\Psr7wp;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Psr\Http\Message\StreamInterface;

/**
 * Class LazyOpenStream
 *
 * Lazily reads or writes to a file that is opened only after an IO operation take place on the stream.
 *
 * @package Digiom\Psr7wp
 */
final class LazyOpenStream implements StreamInterface
{
	use StreamDecoratorTrait;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @var string
	 */
	private $mode;

	/**
	 * LazyOpenStream constructor.
	 *
	 * @param string $filename File to lazily open
	 * @param string $mode fopen mode to use when opening the stream
	 */
	public function __construct($filename, $mode)
	{
		$this->filename = $filename;
		$this->mode = $mode;
	}

	/**
	 * Creates the underlying stream lazily when required.
	 */
	protected function createStream()
	{
		return Utils::streamFor(Utils::tryFopen($this->filename, $this->mode));
	}
}
