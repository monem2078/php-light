<?php

namespace Phplight\Sessions;

class Session {
    /**
     * Session constructor
     *
     */
    private function __construct() {}

    /**
     * Session start
     *
     * @return void
     */
    public static function start() {
        if (! session_id()) {
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    /**
     * Set new session
     *
     * @param  $key.
     * @param $value.
     *
     * @return string $value.
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;

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
        return isset($_SESSION[$key]);
    }

    /**
     * Get session by key.
     *
     * @param $key
     *
     * @return mixed
     */
    public static function get($key) {
        return static::hasKey($key) ? $_SESSION[$key] : null;
    }

    /**
     * Remove by key
     *
     * @param $key
     *
     * @return void
     */
    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    /**
     * Get all sessions
     *
     * @return array
     */
    public static function getAll() {
        return $_SESSION;
    }

    /**
     * Session destroy
     *
     * @return void
     */
    public static function destroy() {
        foreach (static::getAll() as $key => $value) {
            static::remove($key);
        }
    }


    /**
     * Session flash.
     *
     * @param $key
     * @return mixed|null
     */
    public static function flash($key) {
        $value = null;
        if (static::hasKey($key)) {
            $value = $_SESSION[$key];
            static::remove($key);
        }

        return $value;
    }

}
