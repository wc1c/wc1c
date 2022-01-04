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
use Exception;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use const PHP_VERSION_ID;

/**
 * Class PumpStream
 * Provides a read only stream that pumps data from a PHP callable.
 *
 * When invoking the provided callable, the PumpStream will pass the amount of
 * data requested to read to the callable. The callable can choose to ignore
 * this value and return fewer or more bytes than requested. Any extra data
 * returned by the provided callable is buffered internally until drained using
 * the read() function of the PumpStream. The provided callable MUST return
 * false when there is no more data to read.
 *
 * @package Digiom\Psr7wp
 */
final class PumpStream implements StreamInterface
{
	/**
	 * @var callable|null
	 */
	private $source;

	/**
	 * @var int|null
	 */
	private $size;

	/**
	 * @var int
	 */
	private $tellPos = 0;

	/**
	 * @var array
	 */
	private $metadata;

	/**
	 * @var BufferStream
	 */
	private $buffer;

	/**
	 * PumpStream constructor.
	 *
	 * @param callable(int): (string|null|false)  $source  Source of the stream data. The callable MAY
	 *                                                     accept an integer argument used to control the
	 *                                                     amount of data to return. The callable MUST
	 *                                                     return a string when called, or false|null on error
	 *                                                     or EOF.
	 * @param array{size?: int, metadata?: array} $options Stream options:
	 *                                                     - metadata: Hash of metadata to use with stream.
	 *                                                     - size: Size of the stream, if known.
	 */
	public function __construct(callable $source, array $options = [])
	{
		$this->source = $source;
		$this->size = isset($options['size']) ? $options['size'] : null;
		$this->metadata = isset($options['metadata']) ? $options['metadata'] : [];
		$this->buffer = new BufferStream();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		try
		{
			return Utils::copyToString($this);
		}
		catch (Exception $e)
		{
			if (PHP_VERSION_ID >= 70400)
			{
				throw $e;
			}

			trigger_error(sprintf('%s::__toString exception: %s', self::class, (string) $e), E_USER_ERROR);
		}
	}

	/**
	 * @return void
	 */
	public function close()
	{
		$this->detach();
	}

	/**
	 * @return null
	 */
	public function detach()
	{
		$this->tellPos = 0;
		$this->source = null;

		return null;
	}

	/**
	 * @return int|null
	 */
	public function getSize()
	{
		if(!isset($this->size))
		{
			return null;
		}

		return $this->size;
	}

	/**
	 * @return int
	 */
	public function tell()
	{
		return $this->tellPos;
	}

	/**
	 * @return bool
	 */
	public function eof()
	{
		return $this->source === null;
	}

	/**
	 * @return false
	 */
	public function isSeekable()
	{
		return false;
	}

	/**
	 *
	 */
	public function rewind()
	{
		$this->seek(0);
	}

	/**
	 * @param int $offset
	 * @param int $whence
	 */
	public function seek($offset, $whence = SEEK_SET)
	{
		throw new RuntimeException('Cannot seek a PumpStream');
	}

	/**
	 * @return false
	 */
	public function isWritable()
	{
		return false;
	}

	/**
	 * @param string $string
	 *
	 * @return void
	 */
	public function write($string)
	{
		throw new RuntimeException('Cannot write to a PumpStream');
	}

	/**
	 * @return bool
	 */
	public function isReadable()
	{
		return true;
	}

	/**
	 * @param int $length
	 *
	 * @return string
	 */
	public function read($length)
	{
		$data = $this->buffer->read($length);

		$readLen = strlen($data);
		$this->tellPos += $readLen;
		$remaining = $length - $readLen;

		if($remaining)
		{
			$this->pump($remaining);
			$data .= $this->buffer->read($remaining);
			$this->tellPos += strlen($data) - $readLen;
		}

		return $data;
	}

	/**
	 * @return string
	 */
	public function getContents()
	{
		$result = '';
		while (!$this->eof())
		{
			$result .= $this->read(1000000);
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return mixed
	 */
	public function getMetadata($key = null)
	{
		if (!$key) {
			return $this->metadata;
		}

		return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
	}

	/**
	 * @param int $length
	 */
	private function pump($length)
	{
		if ($this->source)
		{
			do
			{
				$data = call_user_func($this->source, $length);

				if($data === false || $data === null)
				{
					$this->source = null;
					return;
				}

				$this->buffer->write($data);
				$length -= strlen($data);
			}
			while ($length > 0);
		}
	}
}
