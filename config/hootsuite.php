<?php

return [
    /**
     * App client id
     */
    'client_id' => env('HOOTSUITE_CLIENT_ID'),
    /**
     * App client secret
     */
    'client_secret' => env('HOOTSUITE_CLIENT_SECRET'),
     /**
     * The link used to generate and refresh the tokens
     */
    'redirect_uri' => env('HOOTSUITE_REDIRECT_URI'),
    /**
     * Shorten the url of the message link
     */
    'bitly_text_link' => false,
    /**
     * Shorten all links in the message
     */
    'bity_all_links' => false,
    /**
     * This configuration is related to the laravel-setting package of spatie
     */
    'settings'  => [
        'group'     => 'hootsuite',
        'fields'    => [
            'hootsuite' => [
                'access_token'  => [
                    /**
                     * The name can not be changed
                     */
                    'name'          => 'hootsuite_access_token',
                    'default_value' => null,
                    'encrypted'     => true
                ],
                'refresh_token'  => [
                    /**
                     * The name can not be changed
                     */
                    'name'          => 'hootsuite_refresh_token',
                    'default_value' => null,
                    'encrypted'     => true
                ],
                'token_expires'  => [
                    /**
                     * The name can not be changed
                     */
                    'name'          => 'hootsuite_token_expires',
                    'default_value' => null,
                    'encrypted'     => false
                ],
            ],
            'bitly'     => [
                'api_token'  => [
                    /**
                     * The name can not be changed
                     */
                    'name'          => 'bitly_api_token',
                    'default_value' => null,
                    'encrypted'     => true
                ],
            ]
        ]
    ],
];
