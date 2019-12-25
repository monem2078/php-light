<?php

/**
 * view render.
 *
 * @param $path
 * @param array $data
 * @return mixed
 */
if (!function_exists('view')) {
    function view($path, $data = [])
    {
        return \Phplight\View\View::render($path, $data);
    }
}

/**
 * request get.
 *
 * @param $key
 * @return mixed
 */
if (!function_exists('request')) {
    function request($key)
    {
        return \Phplight\Http\Request::get($key);
    }
}

/**
 * redirect.
 *
 * @param $path
 * @return mixed
 */
if (!function_exists('redirect')) {
    function redirect($path)
    {
        return \Phplight\Url\Url::redirect($path);
    }
}

/**
 * previous.
 *
 * @return mixed
 */
if (!function_exists('previous')) {
    function previous()
    {
        return \Phplight\Url\Url::previous();
    }
}

/**
 * url path.
 * @param $path .
 * @return mixed
 */
if (!function_exists('url')) {
    function url($path)
    {
        return \Phplight\Url\Url::path($path);
    }
}

/**
 * asset.
 * @param $path .
 * @return mixed
 */
if (!function_exists('asset')) {
    function asset($path)
    {
        return \Phplight\Url\Url::path($path);
    }
}

/**
 * dump and die.
 * @param $data .
 * @return mixed
 */
if (!function_exists('dd')) {
    function dd($data)
    {
        echo "<pre>";
        if (is_string($data)) {
            echo $data;
        } else {
            print_r($data);
        }
        echo "</pre>";
        die();
    }
}

/**
 * get session data.
 * @param $key .
 * @return mixed
 */
if (!function_exists('session')) {
    function session($key)
    {
        return \Phplight\Sessions\Session::get($key);
    }
}

/**
 * get session flash.
 * @param $key .
 * @return mixed
 */
if (!function_exists('flash')) {
    function flash($key)
    {
        return \Phplight\Sessions\Session::flash($key);
    }
}

/**
 * links.
 * @param $current_page .
 * @param $pages .
 * @return mixed
 */
if (!function_exists('links')) {
    function links($current_page, $pages)
    {
        return \Phplight\Database\Database::links($current_page, $pages);
    }
}

/**
 * auth user.
 * @param $table .
 * @return mixed
 */
if (!function_exists('auth')) {
    function auth($table)
    {
        $auth = \Phplight\Sessions\Session::get($table) ?: \Phplight\Cookies\Cookie::get($table);
        return \Phplight\Database\Database::table($table)->where('id', '=', $auth)->first();
    }
}