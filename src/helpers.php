<?php


if (!function_exists('hootsuite')) {

    /**
     *  Get hootsuite instance
     *
     * @param  string|null $message
     * @return \Guysolamour\Hootsuite\Hootsuite
     */
    function hootsuite()
    {
        return  app('hootsuite');
    }
}


if (!function_exists('hootsuite_settings')) {

    /**
     * Get settings
     *
     * @param  string|null $message
     * @return \Guysolamour\Hootsuite\Settings\HootsuiteSettings
     */
    function hootsuite_settings()
    {
        return app(Guysolamour\Hootsuite\Settings\HootsuiteSettings::class);
    }
}
