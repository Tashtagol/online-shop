<?php

namespace Core;

class Autoload
{
    public static function register ($rootPath) {
        $autoload = function (string $className) use ($rootPath) {

            $handlerPath =  str_replace('\\', '/', $className);
            $path = $rootPath . '/' . $handlerPath . '.php';

            if (file_exists($path)) {
                require_once $path;

                return true;
            }
            return false;
        };

        spl_autoload_register($autoload);
    }
}