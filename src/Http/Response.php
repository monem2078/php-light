<?php


namespace Phplight\Http;


class Response {

    /**
     * Response constructor.
     *
     * @return void.
     */
    private function __construct() {}

    /**
     * get data.
     *
     * @param $data
     */
    public static function output($data) {
         if (! $data) {return;}

         if (!is_string($data)) {
             $data = static::json($data);
         }
         echo $data;
    }

    /**
     * convert data to json.
     *
     * @param $data
     * @return false|string
     */
    public static function json($data) {
        return json_encode($data);
    }

}