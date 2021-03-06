<?php

namespace Guysolamour\Hootsuite\Commands;

use Illuminate\Console\Command;


class GetOauthCodeUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hootsuite:oauth:url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get oauth link to retrieve code';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line("------------------------------------------------------------------------------------------------------------------------");
        $this->line("Hootsuite oauth url");
        $this->line("------------------------------------------------------------------------------------------------------------------------");

        $this->info($this->getUrl());

        $this->line("-----------------------------------------------------------------------------------------------------------------------------");
        $this->line("Copy and paste this url in your browser and give authorizations");
    }

    /**
     * @return string
     */
    private function getUrl() :string
    {
        $args = [
            'client_id'     => config('hootsuite.client_id'),
            'response_type' => 'code',
            'scope'         => 'offline',
            'redirect_uri'  => config('hootsuite.redirect_uri'),
            'state'         => route('hootsuite.redirect.uri'),
        ];
        return config('hootsuite.api_endpoint') . 'oauth2/auth/?' . http_build_query($args);
    }
}
