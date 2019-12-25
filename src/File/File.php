<?php

namespace Phplight\File;

class File {

    /**
     * File constructor.
     *
     * @return void;
     */
    private function __construct() {}

    /**
     * return root path.
     *
     * @return false|string
     */
    public static function root() {
        return ROOT;
    }

    /**
     * return ds.
     *
     * @return string
     */
    public static function ds() {
        return DS;
    }

    /**
     * get full path.
     *
     * @param $path
     * @return mixed|string
     */
    public static function getFullPath($path) {
        $path = static::root() . static::ds() . trim($path, '/');
        $path = str_replace(['\\', '/'], static::ds(), $path);
        return $path;
    }

    /**
     * check if file exists.
     *
     * @param $path
     * @return bool
     */
    public static function exists($path) {
        return file_exists(static::getFullPath($path));
    }

    /**
     * require file.
     *
     * @param $path
     * @return mixed
     */
    public static function require_file($path) {
        if (static::exists($path)) {
            return require_once static::getFullPath($path);
        }
    }

    /**
     * include file.
     *
     * @param $path
     * @return mixed
     */
    public static function include_file($path) {
        if (static::exists($path)) {
            return include static::getFullPath($path);
        }
    }

    /**
     * require dir.
     *
     * @param $path
     */
    public static function require_dir($path) {
        $files = array_diff(scandir(static::getFullPath($path)), ['.', '..']);

        foreach ($files as $file) {
            $file_path = $path . static::ds() . $file;
            if (static::exists($file_path)) {
                static::require_file($file_path);
            }
        }
    }
}