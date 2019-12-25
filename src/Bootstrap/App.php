<?php

namespace Phplight\Bootstrap;

use Phplight\Exceptions\Whoops;
use Phplight\File\File;
use Phplight\Http\Request;
use Phplight\Http\Response;
use Phplight\Router\Route;
use Phplight\Sessions\Session;

class App {
    /**
     * App constructor
     *
     * @return void
     */
    private function __construct() {}


    /**
     * Run the application.
     *
     * @throws \Exception
     */
    public static function run() {
        // Register whoops.
        Whoops::handle();

        // Start session
        Session::start();

        // Handle request.
        Request::handle();

        // Require route file.
        File::require_dir('routes');

        // Handle route.
        $data = Route::handle();

        Response::output($data);
    }
}
