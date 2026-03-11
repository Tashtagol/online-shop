<?php

namespace Service\Logger;
use Model\Logger;
class LoggerDbService implements LoggerServiceInterface

{
    private Logger $logger;
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
    public function error (\Throwable $exception)
    {
        $this->logger->log('ERROR', $exception);
    }
    public function warning (\Throwable $exception)
    {
        $this->logger->log('WARNING', $exception);
    }
    public function info (\Throwable $exception)
    {
        $this->logger->log ('INFO', $exception);
    }

}