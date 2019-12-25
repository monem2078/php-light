<?php

namespace Phplight\Router;

use Phplight\Http\Request;
use Phplight\View\View;

class Route {

    private static $routes = [];
    private static $middleware;
    private static $prefix;

    /**
     * route constructor.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * add new route.
     *
     * @param string $method
     * @param string $uri
     * @param string $callback
     */
    public static function add(string $method, string $uri, $callback) {
        $uri = rtrim(static::$prefix . '/' . trim($uri, '/'), '/');
        $uri = $uri ?: '/';
        foreach (explode('|', $method) as $method) {
            static::$routes[] = [
                'uri' => $uri,
                'callback' => $callback,
                'method' => $method,
                'middleware' => static::$middleware
            ];
        }
    }

    /**
     * add new get route.
     *
     * @param string $uri
     * @param $callback
     */
    public static function get(string $uri, $callback) {
        static::add("GET", $uri, $callback);
    }

    /**
     * add new post route.
     *
     * @param string $uri
     * @param $callback
     */
    public static function post(string $uri, $callback) {
        static::add("POST", $uri, $callback);
    }

    /**
     * add new any route.
     *
     * @param string $uri
     * @param $callback
     */
    public static function any(string $uri, $callback) {
        static::add("POST|GET", $uri, $callback);
    }

    /**
     * add prefix.
     *
     * @param string $prefix
     * @param $callback
     * @throws \Exception
     */
    public static function prefix(string $prefix, $callback) {
        $parentPrefix = static::$prefix;
        static::$prefix .= '/' . trim($prefix, '/');
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \Exception("Use a callback");
        }
        static::$prefix = $parentPrefix;
    }

    /**
     * handle route.
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public static function handle() {
        $uri = Request::getUrl();

        foreach (static::$routes as $route) {
            $matched = true;
            $route['uri'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['uri']);
            $route['uri'] = '#^' . $route['uri'] . '$#';
            if (preg_match($route['uri'], $uri, $matches)) {
                array_shift($matches);
                $params = array_values($matches);
                foreach ($params as $param) {
                    if (strpos($param, '/')) {
                        $matched = false;
                    }
                }
                if ($route['method'] != Request::method()) {
                    $matched = false;
                }
                if ($matched == true) {
                    return static::invoke($route, $params);
                }
            }
            return View::render("errors.404");
        }
    }

    /**
     * invoke route.
     *
     * @param $route
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    public static function invoke($route, $params = []) {
        static::executeMiddleware($route);
        $callback = $route['callback'];
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } elseif (strpos($callback, '@') != false) {
            list($controller, $method) = explode('@', $callback);
            $controller = 'App\Controllers\\' . $controller;
            if (class_exists($controller)) {
                $object = new $controller;
                if (method_exists($object, $method)) {
                     return call_user_func_array([$object, $method], $params);
                } else {
                    throw new \BadFunctionCallException("the method ". $method . "not found in controller ". $controller);
                }
            } else {
                throw new \ReflectionException("provide a valid callback function");
            }
        }
    }

    /**
     * execute middleware.
     *
     * @param $route
     * @return mixed
     * @throws \ReflectionException
     */
    public static function executeMiddleware($route) {
        foreach (explode('|', $route['middleware']) as $middleware) {
            if ($middleware != '') {
                $middleware = 'App\Middleware\\'. $middleware;
                if (class_exists($middleware)) {
                    $object = new $middleware;
                    return call_user_func_array([$object, 'handle'], []);
                } else {
                    throw new \ReflectionException( "class not found ". $middleware);
                }
            }
        }
    }

}