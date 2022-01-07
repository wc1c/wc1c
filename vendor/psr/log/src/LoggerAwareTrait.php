<?php

namespace Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected $log;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $log
     */
    public function setLog(LoggerInterface $log)
    {
        $this->log = $log;
    }
}
