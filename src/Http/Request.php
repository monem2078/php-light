<?php

namespace Phplight\Http;


class Request {

    private static $base_url;
    private static $script_name;
    private static $url;
    private static $full_url;
    private static $query_string;

    /**
     * Request constructor.
     */
    private function __construct() {}

    /**
     * Handle Request.
     *
     * @return void
     */
    public static function handle() {
        static::$script_name = str_replace('\\', '', dirname(Server::get('SCRIPT_NAME')));
        static::setBaseUrl();
        static::setUrl();
    }

    /**
     * Set base url.
     *
     * @return void
     */
    private static function setBaseUrl() {
        $protocol = Server::get('REQUEST_SCHEME') . '://';
        $host = Server::get("HTTP_HOST");
        $script_name = static::$script_name;
        static::$base_url = $protocol.$host.$script_name;
    }

    private static function setUrl() {
        $request_uri = urldecode(Server::get('REQUEST_URI'));
        $request_uri = rtrim(preg_replace("#^" . static::$script_name .'#', "", $request_uri), '/');

        $queryString = '';
        static::$full_url = $request_uri;
        if (strpos($request_uri, '?')) {
            list($request_uri, $queryString) = explode('?', $request_uri);
        }
        static::$url = $request_uri?: '/';
        static::$query_string = $queryString;
    }

    /**
     * get base url.
     *
     * @return mixed
     */
    public static function getBaseUrl() {
        return static::$base_url;
    }

    /**
     * get url.
     *
     * @return mixed
     */
    public static function getUrl() {
        return static::$url;
    }

    /**
     * get query string.
     *
     * @return mixed
     */
    public static function getQueryString() {
        return static::$query_string;
    }

    /**
     * get full url.
     *
     * @return mixed
     */
    public static function getFullUrl() {
        return static::$full_url;
    }

    /**
     * Get request method.
     *
     * @return mixed|null
     */
    public static function method() {
        return Server::get('REQUEST_METHOD');
    }

    /**
     * check if the request has the key.
     *
     * @param $type
     * @param $key
     * @return bool
     */
    public static function has($type, $key) {
        return array_key_exists($key, $type);
    }

    /**
     * get value from request
     *
     * @param $key
     * @param array|null $type
     * @return mixed|null
     */
    private static function value($key, array $type = null) {
        $type = isset($type) ? $type : $_REQUEST;
        return static::has($type, $key) ? $type[$key] : null;
    }

    /**
     * get value from get request.
     *
     * @param $key
     * @return mixed
     */
    public static function get($key) {
        return static::value($key, $_GET);
    }

    /**
     * get value from post request.
     *
     * @param $key
     * @return mixed
     */
    public static function post($key) {
        return static::value($key, $_POST);
    }

    public static function set($key, $value) {
        $_REQUEST[$key] = $value;
        $_GET[$key] = $value;
        $_POST[$key] = $value;
        return $value;
    }

    /**
     * return the previous url.
     *
     * @return mixed|null
     */
    public static function previous() {
        return Server::get("HTTP_REFERER");
    }

    /**
     * return all request.
     *
     * @return array
     */
    public static function all() {
        return $_REQUEST;
    }

}