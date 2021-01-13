<?php

namespace Guysolamour\Hootsuite\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Guysolamour\Hootsuite\Settings\HootsuiteSettings;

class HootsuiteController
{
    public function saveTokens(Request $request, HootsuiteSettings $settings)
    {

        $settings->saveTokens([
            'hootsuite_access_token'  => $request->get('access_token'),
            'hootsuite_refresh_token' => $request->get('refresh_token'),
            'hootsuite_token_expires' => Carbon::now()->addSeconds($request->get('expires_in'))->toDateTimeString(),
        ]);

        return redirect('/');

    }
}
