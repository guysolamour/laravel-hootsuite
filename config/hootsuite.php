<?php

return [
    /**
     * App client id, can not be changed
     */
    'client_id' => 'dee91d5b-7c0a-454e-9149-fc40f91bbb40',
     /**
     * The link used to generate and refresh the tokens
     */
    'redirect_uri' => 'https://aswebagency.com/laravel-hootsuite',
     /**
     * Hootsuite api url
     */
    'api_endpoint' => 'https://platform.hootsuite.com/',
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
                    'encrypted'     => false
                ],
                'refresh_token'  => [
                    /**
                     * The name can not be changed
                     */
                    'name'          => 'hootsuite_refresh_token',
                    'default_value' => null,
                    'encrypted'     => false
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
