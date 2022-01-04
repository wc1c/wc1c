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
use BadMethodCallException;
use Exception;
use UnexpectedValueException;
use const PHP_VERSION_ID;

/**
 * Stream decorator trait
 *
 * @property StreamInterface $stream
 */
trait StreamDecoratorTrait
{
    /**
     * @param StreamInterface $stream Stream to decorate
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Magic method used to create a new stream if streams are not added in
     * the constructor of a decorator (e.g., LazyOpenStream).
     *
     * @return StreamInterface
     */
    public function __get($name)
    {
        if ($name === 'stream')
        {
            $this->stream = $this->createStream();
            return $this->stream;
        }

        throw new UnexpectedValueException("$name not found on class");
    }

	/**
	 * @throws Exception
	 */
	public function __toString()
    {
        try
        {
            if ($this->isSeekable())
            {
                $this->seek(0);
            }

            return $this->getContents();
        }
        catch (Exception $e)
        {
            if(PHP_VERSION_ID >= 70400)
            {
                throw $e;
            }

            trigger_error(sprintf('%s::__toString exception: %s', self::class, (string) $e), E_USER_ERROR);
        }
    }

	/**
	 * @return string
	 */
    public function getContents()
    {
        return Utils::copyToString($this);
    }

    /**
     * Allow decorators to implement custom methods
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        /**
         * @var callable $callable
         */
        $callable = [$this->stream, $method];
        $result = call_user_func_array($callable, $args);

        // Always return the wrapped object if the result is a return $this
        return $result === $this->stream ? $this : $result;
    }

    public function close()
    {
        $this->stream->close();
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        return $this->stream->getMetadata($key);
    }

    public function detach()
    {
        return $this->stream->detach();
    }

    public function getSize()
    {
    	$size = $this->stream->getSize();

    	if(!$size)
	    {
	    	return null;
	    }

        return $size;
    }

    public function eof()
    {
        return $this->stream->eof();
    }

    public function tell()
    {
        return $this->stream->tell();
    }

    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    public function isWritable()
    {
        return $this->stream->isWritable();
    }

    public function isSeekable()
    {
        return $this->stream->isSeekable();
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        $this->stream->seek($offset, $whence);
    }

    public function read($length)
    {
        return $this->stream->read($length);
    }

    public function write($string)
    {
        return $this->stream->write($string);
    }

    /**
     * Implement in subclasses to dynamically create streams when requested.
     *
     * @throws BadMethodCallException
     */
    protected function createStream()
    {
        throw new BadMethodCallException('Not implemented');
    }
}
