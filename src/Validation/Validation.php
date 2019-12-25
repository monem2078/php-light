<?php

namespace Phplight\Validation;

use Phplight\Http\Request;
use Phplight\Sessions\Session;
use Phplight\Url\Url;
use Rakit\Validation\Validator;

class Validation {

    /**
     * Validation constructor.
     *
     * @return void
     */
    private function __construct() {}


    public static function validate(array $rules, $json) {
        $validator = new Validator;

        $validation = $validator->make($_POST + $_FILES, $rules);

        $errors = $validation->errors();

        if ($validation->fails()) {
            if ($json) {
                return ['errors' => $errors->firstOfAll()];
            } else {
                Session::set('errors', $errors);
                Session::set('old', Request::all());
                return Url::redirect(Url::previous());
            }
        }
    }
}