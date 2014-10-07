<?php

namespace Kaltura;

class KalturaAutoloader
{

    static $isRegistered = false;

    static public function autoload($class)
    {
        if (strpos($class, 'Kaltura') === 0) {
            $classPath = __DIR__.'/../'.str_replace('_', '/', $class).'.php';
            require_once($classPath);
        }
    }

    static public function register()
    {
        if (!self::$isRegistered) {
            self::$isRegistered = true;
            spl_autoload_register(array("\\Kaltura\\KalturaAutoloader", "autoload"));
        }
    }

}

KalturaAutoloader::register();
