<?php

namespace Guysolamour\Hootsuite\Clients;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Guysolamour\Hootsuite\Traits\HttpTrait;

class HootsuiteClient
{
    use HttpTrait;

    /**
     * Holds the Hootsuite Application's Client ID
     * @var     string
     */
    private $client_id = '';


    /**
     * Holds the Hootsuite Application's Client Secret
     * @var     string
     */
    private $client_secret = '';

    /**
     * Holds the oAuth Gateway endpoint, used to exchange a code for an access token
     * @var     string
     */
    private $oauth_gateway_endpoint = '';

    /**
     * Holds the API endpoint
     * @var     string
     */
    private $api_endpoint = '';


    /**
     * Holds the API version
     * @var     string
     */
    private $api_version = 'v1';



    /**
     * Access Code Token
     * @var     string
     */
    private $access_token = '';

    /**
     * Refresh Token
     * @var     string
     */
    private $refresh_token = '';

    /**
     * Token Expiry data
     * @var     Carbon
     */
    private $token_expires;

    /**
     *
     * @var \Guysolamour\Hootsuite\Settings\HootsuiteSettings;
     */
    private $settings;


    public function __construct()
    {
        $this->client_id              = config('hootsuite.client_id');
        $this->oauth_gateway_endpoint = config('hootsuite.redirect_uri');
        $this->api_endpoint           = config('hootsuite.api_endpoint');


        $this->settings               = hootsuite_settings();

        $this->setup();
    }



    /**
     * Get authenticated user
     *
     * @return mixed
     */
    public function user()
    {
        return $this->get('me')->json();
    }

    /**
     * @param string|null $networks
     * @return array
     */
    public function profiles(?string $networks = null)
    {
        $profiles =  $this->get('socialProfiles')['data'];

        if (!is_null($networks)) {
            $networks = array_map(function ($network) {
                $network = strtoupper(trim($network));

                switch ($network) {
                    case 'FACEBOOK':
                        $network = 'FACEBOOKPAGE';
                        break;
                    case 'TWITTER':
                        $network = 'TWITTER';
                        break;
                    case 'LINKEDIN':
                        $network = 'LINKEDINCOMPANY';
                        break;
                }

                return ($network);
            }, array_filter(explode(',', $networks)));

            $profiles = array_filter($profiles, function ($profile) use ($networks) {
                if (in_array($profile['type'], $networks)) {
                    return $profile;
                }
                return false;
            });
        }


        return $profiles;
    }

    public function getProfileName($profileId): string
    {
        $response =  $this->get("socialProfiles/{$profileId}");

        switch ($response['data']['type']) {
            case 'FACEBOOKPAGE':
                $profile = 'Facebook';
                break;
            case 'TWITTER':
                $profile = 'Twitter';
                break;
            case 'LINKEDINCOMPANY':
                $profile = 'Linkedin';
                break;
        }

        return $profile;
    }

    /**
     * @param string|null $networks
     * @return array
     */
    public function formatedProfiles(?string $networks = null)
    {
        $profiles =  Arr::pluck($this->profiles($networks), 'type', 'id');

        $newProfiles = [];

        foreach ($profiles as $key => $value) {
            if (Str::contains($value, 'FACEBOOK')) {
                $value = 'FACEBOOK';
            } else if (Str::contains($value, 'TWITTER')) {
                $value = 'TWITTER';
            } else if (Str::contains($value, 'LINKEDIN')) {
                $value = 'LINKEDIN';
            }
            $newProfiles[] = ['id' => $key, 'name' => Str::ucfirst(Str::lower($value))];
        }

        return $newProfiles;
    }

    /**
     * Add a message
     *
     * @param array $args
     * @return mixed
     */
    public function message(array $args)
    {
        return $this->schedule($args);
    }

    /**
     *
     * @param int $messageId
     * @return array
     */
    public function getMessage($messageId)
    {
        $response =  $this->get("messages/{$messageId}")->json();

        return Arr::get($response, 'data');
    }

    /**
     * @param int $messageId
     * @return bool
     */
    public function deleteMessage($messageId)
    {
        return $this->delete("messages/{$messageId}")->ok();
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getApiUrl(string $url = ''): string
    {
        return "{$this->api_endpoint}{$this->api_version}/{$url}";
    }

    /**
     * Check if a message is still scheduled
     *
     * @param int $messageId
     * @return boolean
     */
    public function messageIsStillScheduled($messageId): bool
    {
        $message = Arr::first($this->getMessage($messageId));

        return Arr::get($message, 'state') === 'SCHEDULED';
    }

    /**
     * Shortend url via bitly api
     *
     * @param string $url
     * @return string
     */
    private function bitlyfyUrl(string $url) :string
    {
        return app('bitly')->getUrl($url);
    }

    /**
     * Replace and shortened all links in a given url
     *
     * @param string $url
     * @return string
     */
    private function bitlyfyAllUrls(string $url) :string
    {
        $pattern = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';

        return preg_replace_callback($pattern, function($matches){
            return $this->bitlyfyUrl($matches[0]);
        }, $url);

    }

    /**
     * Schedule a message
     *
     * @param array $args
     * @return Response
     */
    public function schedule(array $args)
    {
        $data = [];

        $link = Arr::get($args, 'link', '');
        if ($link && config('hootsuite.bitly_text_link', false)) {
            $link = $this->bitlyfyUrl($link);
        }

        $text = $args['text'];
        if (config('hootsuite.bity_all_links', false)){
            $text = $this->bitlyfyAllUrls($args['text']);
        }

        if ($args['text']) {
            $data['text'] = <<<HTML
            {$args['hashtags']}
            {$text}

            {$link}
            HTML;
        }


        $data['socialProfileIds'] = $this->getNetworks($args['networks']);
        $data['emailNotification'] = $args['notify'];

        $image = Arr::get($args, 'image', false);
        if ($image) {
            $data['mediaUrls'] = [
                ['url' => $this->uploadImage($image)]
            ];
        }

        $schedule = Arr::get($args, 'publish_at', false);

        if ($schedule) {
            $data['scheduledSendTime'] = Carbon::parse($schedule)->toIso8601ZuluString();
        }

        $response = $this->post('messages', $data);

        return $response;
    }


    /**
     * Upload image and get url
     *
     * @param string $image_url
     * @return void
     */
    private function uploadImage(string $image_url)
    {
        $request = $this->http()
                        ->post($this->oauth_gateway_endpoint. '/photo/upload', [
                            'image_url' => $image_url
                        ]);

        if ($request->ok()){
            return $request['data'];
        }
    }

    /**
     * @param string|array|null $networks
     * @return array
     */
    private function getNetworks($networks = null): array
    {
        if (is_array($networks)) {
            return  $networks;
        }

        return Arr::pluck($this->profiles($networks), 'id');
    }


    /**
     * Refresh token
     *
     * @return void
     */
    private function refreshToken(): void
    {
        $request = Http::post($this->oauth_gateway_endpoint. '/refresh/token', [
            'refresh_token' => $this->refresh_token,
        ]);

        if ($request->ok()){
            $this->setTokens($request->json());
        }else {
            throw new \Exception("An error occured when refreshing token");
        }
    }

    /**
     * Check if the token has expired
     *
     * @param Carbon|null $token
     * @return boolean
     */
    private function isExpiredToken(?Carbon $token = null): bool
    {
        $token = $token ?: $this->token_expires;

        return !$token->isFuture();
    }


    /**
     * @param array $credentials
     * @param boolean $save
     * @return void
     */
    private function setTokens(array $credentials): void
    {
        $this->access_token   = Arr::get($credentials, 'access_token');
        $this->refresh_token  = Arr::get($credentials, 'refresh_token');
        $this->token_expires  = Carbon::now()->addSeconds(Arr::get($credentials, 'expires_in'));

        $this->saveTokens();
    }

    /**
     * Save tokens in database
     *
     * @return void
     */
    private function saveTokens(): void
    {
        if ($this->access_token) {
            $this->settings->hootsuite_access_token = $this->access_token;
        }

        if ($this->refresh_token) {
            $this->settings->hootsuite_refresh_token = $this->refresh_token;
        }

        if ($this->token_expires) {
            $this->settings->hootsuite_token_expires = $this->token_expires->toDateTimeString();
        }

        $this->settings->save();
    }

    /**
     * @return void
     */
    private function setup(): void
    {
        $this->access_token  = $this->settings->get('hootsuite_access_token');
        $this->refresh_token = $this->settings->get('hootsuite_refresh_token');
        $this->token_expires = Carbon::parse($this->settings->get('hootsuite_token_expires'));

        if (
            !($this->access_token) || !($this->refresh_token) || !($this->token_expires)
        ) {
            throw new \Exception("Access tokens invalid. Do not forget to give permission to your hootsuite account");
        }

        if ($this->isExpiredToken()) {
            $this->refreshToken();
        }
    }
}