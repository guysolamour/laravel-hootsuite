<?php

return [
    /**
     * L'idée de l'app, ne doit pas changer
     */
    'client_id' => 'dee91d5b-7c0a-454e-9149-fc40f91bbb40',
     /**
     * Le lien utiliser pour générer et rafraichir les tokens
     */
    'redirect_uri' => 'https://aswebagency.com/api/laravel-hootsuite',
     /**
     * L'url de l'api de hootsuite
     */
    'api_endpoint' => 'https://platform.hootsuite.com/',
    /**
     * Raccourcir l'url qui est passe avec le message
     */
    'bitly_text_link' => false,
    /**
     * Bitly aussi tous les liens présents dans le message
     */
    'bity_all_links' => false,
    /**
     * Cette configuration est en rapport sur le package laravel-setiing de spatie
     * Vous pouvez consulter la documentation pour en savoir plus
     */
    'settings'  => [
        'group'     => 'hootsuite',
        'fields'    => [
            'hootsuite' => [
                'access_token'  => [
                    /**
                     * Le nom ne doit pas etre changé
                     */
                    'name'          => 'hootsuite_access_token',
                    'default_value' => null,
                    'encrypted'     => false
                ],
                'refresh_token'  => [
                    /**
                     * Le nom de doit pas etre change
                     */
                    'name'          => 'hootsuite_refresh_token',
                    'default_value' => null,
                    'encrypted'     => false
                ],
                'token_expires'  => [
                    'name'          => 'hootsuite_token_expires',
                    'default_value' => null,
                    'encrypted'     => false
                ],
            ],
            'bitly'     => [
                'api_token'  => [
                    'name'          => 'bitly_api_token',
                    'default_value' => null,
                    'encrypted'     => true
                ],
            ]
        ]
    ],
];
