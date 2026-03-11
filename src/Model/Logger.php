<?php

namespace Model;


class Logger extends Model
{
    public function log (string $type, \Throwable $exception)
    {
        $stmt = self::getPDO()->prepare("INSERT INTO logs (type, message, line, file) VALUES (:type, :message, :line, :file)");

        $stmt->execute([':type' => $type, ':message' => $exception->getMessage(), ':line' => $exception->getLine(), ':file' => $exception->getFile()]);
    }

}