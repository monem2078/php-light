<?php

namespace Phplight\View;

use Phplight\File\File;
use Jenssegers\Blade\Blade;
use Phplight\Sessions\Session;

class View {

    /**
     * view constructor.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * render blade view.
     *
     * @param $path
     * @param $data
     * @return mixed
     */
    public static function render($path, $data = []) {
        $errors = Session::flash('errors');
        $old = Session::flash('old');
        $data = array_merge($data, ['errors' => $errors, 'old' => $old]);
        $blade = new Blade(File::getFullPath('views'), File::getFullPath('storage/cache'));

        return $blade->make($path, $data)->render();
    }

    /**
     * render view.
     *
     * @param $path
     * @param array $data
     * @return false|string
     * @throws \Exception
     */
    public static function renderView($path, $data = []) {
        $path = 'Views' . File::ds() . str_replace(['/', '\\', '.'], File::ds(), $path) . '.php';
        if (! File::exists($path)) {
            throw  new \Exception("The view file {$path} not found");
        }

        ob_start();
        extract($data);
        include File::getFullPath($path);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}