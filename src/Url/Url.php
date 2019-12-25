<?php

namespace Phplight\Url;

use Phplight\Http\Request;
use Phplight\Http\Server;

class Url {

    /**
     * Url constructor.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * get full path.
     *
     * @param $path
     * @return string
     */
    public static function path($path) {
        return Request::getBaseUrl() . '/' .trim($path, '/');
    }

    /**
     * get previous url.
     *
     * @return mixed|null
     */
    public static function previous() {
        return Request::previous();
    }

    /**
     * redirect to a specific route.
     *
     * @param $path
     */
    public static function redirect($path) {
        header('location' . $path);
        exit();
    }
}