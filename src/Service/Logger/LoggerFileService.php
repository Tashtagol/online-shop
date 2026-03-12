<?php

namespace Service\Logger;

class LoggerFileService implements LoggerServiceInterface
{
    public function error(\Throwable $exception)
    {
        $filename = './../Storage/Log/errors.txt';
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();

        $datetime = date('Y-m-d H:i:s');

        $errormessage ="Ошибка: $message\nФайл: $file\nСрока: $line\nВремя: $datetime\n\n";

        file_put_contents($filename, $errormessage, FILE_APPEND);
    }
    public function info (\Throwable $exception)
{
    $filename = './../Storage/Log/info.txt';
    $message = $exception->getMessage();
    $file = $exception->getFile();
    $line = $exception->getLine();

    $datetime = date('Y-m-d H:i:s');

    $errormessage ="Ошибка: $message\nФайл: $file\nСрока: $line\nВремя: $datetime\n\n";

    file_put_contents($filename, $errormessage, FILE_APPEND);
}
    public function warning (\Throwable $exception)
{
    $filename = './../Storage/Log/warning.txt';
    $message = $exception->getMessage();
    $file = $exception->getFile();
    $line = $exception->getLine();

    $datetime = date('Y-m-d H:i:s');

    $errormessage ="Ошибка: $message\nФайл: $file\nСрока: $line\nВремя: $datetime\n\n";

    file_put_contents($filename, $errormessage, FILE_APPEND);;
}

}