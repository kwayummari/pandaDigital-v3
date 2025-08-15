<?php
// /path/to/secure/directory/config_loader.php
class ConfigLoader {
    private static $config = null;

    public static function getConfig() {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/config.php';
        }
        return self::$config;
    }

    public static function get($key) {
        $config = self::getConfig();
        return $config[$key] ?? null;
    }
}