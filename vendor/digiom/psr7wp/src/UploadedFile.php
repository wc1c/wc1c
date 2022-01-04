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
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * Class UploadedFile
 *
 * @package Digiom\Psr7wp
 */
class UploadedFile implements UploadedFileInterface
{
	const ERRORS =
	[
		UPLOAD_ERR_OK,
		UPLOAD_ERR_INI_SIZE,
		UPLOAD_ERR_FORM_SIZE,
		UPLOAD_ERR_PARTIAL,
		UPLOAD_ERR_NO_FILE,
		UPLOAD_ERR_NO_TMP_DIR,
		UPLOAD_ERR_CANT_WRITE,
		UPLOAD_ERR_EXTENSION,
	];

	/**
	 * @var string|null
	 */
	private $clientFilename;

	/**
	 * @var string|null
	 */
	private $clientMediaType;

	/**
	 * @var int
	 */
	private $error;

	/**
	 * @var string|null
	 */
	private $file;

	/**
	 * @var bool
	 */
	private $moved = false;

	/**
	 * @var int|null
	 */
	private $size;

	/**
	 * @var StreamInterface|null
	 */
	private $stream;

	/**
	 * UploadedFile constructor.
	 *
	 * @param StreamInterface|string|resource $streamOrFile
	 * @param int|null $size
	 * @param int $errorStatus
	 * @param string|null $clientFilename
	 * @param string|null $clientMediaType
	 */
	public function __construct($streamOrFile, $size, $errorStatus, $clientFilename = null, $clientMediaType = null)
	{
		$this->setError($errorStatus);
		$this->size = $size;
		$this->clientFilename = $clientFilename;
		$this->clientMediaType = $clientMediaType;

		if($this->isOk())
		{
			$this->setStreamOrFile($streamOrFile);
		}
	}

	/**
	 * Depending on the value set file or stream variable
	 *
	 * @param StreamInterface|string|resource $streamOrFile
	 *
	 * @throws InvalidArgumentException
	 */
	private function setStreamOrFile($streamOrFile)
	{
		if(is_string($streamOrFile))
		{
			$this->file = $streamOrFile;
		}
		elseif (is_resource($streamOrFile))
		{
			$this->stream = new Stream($streamOrFile);
		}
		elseif ($streamOrFile instanceof StreamInterface)
		{
			$this->stream = $streamOrFile;
		}
		else
		{
			throw new InvalidArgumentException('Invalid stream or file provided for UploadedFile');
		}
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function setError($error)
	{
		if(false === in_array($error, self::ERRORS, true))
		{
			throw new InvalidArgumentException('Invalid error status for UploadedFile');
		}

		$this->error = $error;
	}

	private function isStringNotEmpty($param)
	{
		return is_string($param) && false === empty($param);
	}

	/**
	 * Return true if there is no upload error
	 */
	private function isOk()
	{
		return $this->error === UPLOAD_ERR_OK;
	}

	public function isMoved()
	{
		return $this->moved;
	}

	/**
	 * @throws RuntimeException if is moved or not ok
	 */
	private function validateActive()
	{
		if(false === $this->isOk())
		{
			throw new RuntimeException('Cannot retrieve stream due to upload error');
		}

		if($this->isMoved())
		{
			throw new RuntimeException('Cannot retrieve stream after it has already been moved');
		}
	}

	public function getStream()
	{
		$this->validateActive();

		if ($this->stream instanceof StreamInterface)
		{
			return $this->stream;
		}

		/**
		 * @var string $file
		 */
		$file = $this->file;

		return new LazyOpenStream($file, 'r+');
	}

	public function moveTo($targetPath)
	{
		$this->validateActive();

		if(false === $this->isStringNotEmpty($targetPath))
		{
			throw new InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
		}

		if($this->file)
		{
			$this->moved = PHP_SAPI === 'cli' ? rename($this->file, $targetPath) : move_uploaded_file($this->file, $targetPath);
		}
		else
		{
			Utils::copyToStream($this->getStream(), new LazyOpenStream($targetPath, 'w'));

			$this->moved = true;
		}

		if(false === $this->moved)
		{
			throw new RuntimeException(sprintf('Uploaded file could not be moved to %s', $targetPath));
		}
	}

	/**
	 * @return int|null
	 */
	public function getSize()
	{
		if(!$this->size)
		{
			return null;
		}

		return $this->size;
	}

	/**
	 * @return int
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return string|null
	 */
	public function getClientFilename()
	{
		if(!$this->clientFilename)
		{
			return null;
		}

		return $this->clientFilename;
	}

	/**
	 * @return string|null
	 */
	public function getClientMediaType()
	{
		if(!$this->clientMediaType)
		{
			return null;
		}

		return $this->clientMediaType;
	}
}
