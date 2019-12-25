<?php

namespace Phplight\Http;

class Server {
    /**
     * Server constructor
     */
    private function __construct() {}

    /**
     * return all server data;
     *
     * @return mixed
     */
    public static function all() {
        return $_SERVER;
    }

    /**
     * Get value by key.
     *
     * @param $key
     * @return mixed|null
     */
    public static function get($key) {
        return static::hasKey($key) ? $_SERVER[$key] : null;
    }

    /**
     * Check if a specific key exist.
     *
     * @param $key
     * @return bool
     */
    public static function hasKey($key) {
        return isset($_SERVER[$key]);
    }

    /**
     * Get path info
     *
     * @param $path
     * @return mixed
     */
    public static function getPathInfo($path) {
        return pathinfo($path);
    }

}