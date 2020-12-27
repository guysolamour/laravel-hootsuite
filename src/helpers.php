<?php


if (!function_exists('hootsuite')) {

    /**
     * Arrange for a flash message.
     *
     * @param  string|null $message
     * @return \Guysolamour\Hootsuite\Hootsuite
     */
    function hootsuite()
    {
        $hootsuite = app('hootsuite');

        // if (!is_null($message)) {
        //     return $hootsuite->success($message, $link);
        // }

        return $hootsuite;
    }
}
