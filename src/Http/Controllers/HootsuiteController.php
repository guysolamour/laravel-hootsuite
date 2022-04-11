<?php

namespace Guysolamour\Hootsuite\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Guysolamour\Hootsuite\Clients\HootsuiteClient;
use Guysolamour\Hootsuite\Settings\HootsuiteSettings;

class HootsuiteController
{
    public function redirectUri(Request $request)
    {
        $args = [
            'grant_type'    => 'authorization_code',
            'redirect_uri'  =>  url(config('hootsuite.redirect_uri')),
            // 'redirect_uri'  =>  "https://ivoirecourrier.com/laravel-hootsuite",
            'scope'         =>  'offline',
            'code'          =>  $request->get('code'),
        ];

        $response = HootsuiteClient::postAsForm(null, $args);

        return redirect()->to(route('hootsuite.redirect.uri.tokens') . '?' . http_build_query($response->json()));
    }

    public function saveTokens(Request $request, HootsuiteSettings $settings)
    {
        if (!$request->has('access_token') || !$request->get('refresh_token') || !$request->get('expires_in')){
            return;
        }

        $settings->saveTokens([
            'hootsuite_access_token'  => $request->get('access_token'),
            'hootsuite_refresh_token' => $request->get('refresh_token'),
            'hootsuite_token_expires' => Carbon::now()->addSeconds($request->get('expires_in'))->toDateTimeString(),
        ]);

        return view('hootsuite::index', compact('settings'));
    }
}
