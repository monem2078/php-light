<?php

namespace Phplight\Cookies;

class Cookie {
    /**
     * Session constructor
     *
     */
    private function __construct() {}


    /**
     * Set new cookie
     *
     * @param  $key.
     * @param $value.
     *
     * @return string $value.
     */
    public static function set($key, $value) {
        $expired = time() + (1 * 365 * 24 * 60 * 60);
        setcookie($key, $value, $expired, '/', '', false, true);

        return $value;
    }

    /**
     * Check key exists.
     *
     * @param $key.
     *
     * @return bool.
     */
    public static function hasKey($key) {
        return isset($_COOKIE[$key]);
    }

    /**
     * Get cookie by key.
     *
     * @param $key
     *
     * @return mixed
     */
    public static function get($key) {
        return static::hasKey($key) ? $_COOKIE[$key] : null;
    }

    /**
     * Remove by key
     *
     * @param $key
     *
     * @return void
     */
    public static function remove($key) {
        unset($_COOKIE[$key]);
        setcookie($key, null, -1, '/');
    }

    /**
     * Get all cookies
     *
     * @return array
     */
    public static function getAll() {
        return $_COOKIE;
    }

    /**
     * Cookie destroy
     *
     * @return void
     */
    public static function destroy() {
        foreach (static::getAll() as $key => $value) {
            static::remove($key);
        }
    }


}
