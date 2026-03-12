<?php

namespace Service\Logger;

interface LoggerServiceInterface
{
    public function error(\Throwable $exception);

    public function info(\Throwable $exception);

    public function warning(\Throwable $exception);
}