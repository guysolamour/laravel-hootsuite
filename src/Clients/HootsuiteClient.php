<?php

namespace Guysolamour\Hootsuite\Clients;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Guysolamour\Hootsuite\Traits\HttpTrait;
use Guysolamour\Hootsuite\Exceptions\InvalidDataException;
use Guysolamour\Hootsuite\Exceptions\InvalidResponseException;

class HootsuiteClient
{
    use HttpTrait;

    /**
     * Holds the API endpoint
     * @var     string
     */
    public const API_ENDPOINT = 'https://platform.hootsuite.com/';

    /**
     * Holds the API endpoint
     * @var     string
     */
    public const API_VERSION = 'v1';


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
        $this->client_secret          = config('hootsuite.client_secret');

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
     *
     * @param int $messageId
     * @return array
     */
    public function getMessage(int $messageId)
    {
        $response =  $this->get("messages/{$messageId}")->json();

        return Arr::get($response, 'data');
    }

    /**
     * @param int $messageId
     * @return bool
     */
    public function destroy(int $messageId)
    {
        return $this->delete("messages/{$messageId}")->ok();
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getApiUrl(string $url = ''): string
    {
        return self::API_ENDPOINT . self::API_VERSION . DIRECTORY_SEPARATOR . $url;
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
    private function bitlyfyUrl(string $url): string
    {
        return app('bitly')->getUrl($url);
    }

    /**
     * Replace and shortened all links in a given url
     *
     * @param string $url
     * @return string
     */
    private function bitlyfyAllUrls(string $url): string
    {
        $pattern = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';

        return preg_replace_callback($pattern, function ($matches) {
            return $this->bitlyfyUrl($matches[0]);
        }, $url);
    }

    /**
     * Schedule a message
     *
     * @param array $args
     * @return Response
     */
    private function message(array $args, bool $schedule = false)
    {
        if (!Arr::exists($args, 'networks')) {
            throw new InvalidDataException("The [networks] is required. Please add it to the data array");
        }
        if (!Arr::exists($args, 'text')) {
            throw new InvalidDataException("The [text] is required. Please add it to the data array");
        }

        $data = [];

        $link = Arr::get($args, 'link', '');
        if ($link && config('hootsuite.bitly_text_link', false)) {
            $link = $this->bitlyfyUrl($link);
        }

        $hashtags = $this->getHashTags(Arr::get($args, 'hashtags', ''));

        $text = $args['text'];
        if (config('hootsuite.bity_all_links', false)) {
            $text = $this->bitlyfyAllUrls($args['text']);
        }

        if ($args['text']) {
            $data['text'] = <<<HTML
            {$hashtags}
            {$text}

            {$link}
            HTML;
        }

        $data['socialProfileIds'] = $this->getNetworks($args['networks']);
        $data['emailNotification'] = Arr::get($args, 'notify', false);

        $media = Arr::get($args, 'media', false);
        if ($media){
            $data['mediaUrls'] = $this->uploadMedia($media);
        }

        if ($schedule) {
            $schedule_date = Arr::get($args, 'schedule_at', false);

            if ($schedule_date) {
                if ($schedule_date instanceof \Carbon\Carbon) {
                    $data['scheduledSendTime'] = $schedule_date->toIso8601ZuluString();
                } else {
                    $data['scheduledSendTime'] = Carbon::parse($schedule_date)->toIso8601ZuluString();
                }
            }
        }

        $response = $this->post('messages', $data);


        return $response;
    }

    /**
     * Publish a message
     *
     * @param array $args
     * @return mixed
     */
    public function publish(array $args)
    {
        return $this->message($args);
    }

    /**
     * Add a message
     *
     * @param array $args
     * @return mixed
     */
    public function schedule(array $args)
    {
        if (!Arr::exists($args, 'schedule_at')) {
            throw new InvalidDataException("The [schedule_at] index is not present in the array data");
        }

        return $this->message($args, true);
    }


    /**
     * Upload image and get url
     *
     * @param string|string[] $image_url
     * @return array
     */
    private function uploadMedia($media): array
    {
        $media = Arr::wrap($media);

        $urls = [];

        foreach ($media as $item) {

            $response = Http::attach(
                'image_url', $item
            )->post('https://www.wpzinc.com/?api=owly&action=photo/upload')->throw();

            array_push($urls, ['url' => $response->json('data')]);
        }

        return $urls;
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
    private function refreshToken()
    {
        $args = [
            'grant_type'    => 'refresh_token',
            'redirect_uri'  =>  url(config('hootsuite.redirect_uri')),
            'scope'         => 'offline',
            'refresh_token' => $this->refresh_token
        ];

        $response =  $this->postAsForm(null, $args);

        if ($response->ok()) {
            $this->setTokens($response->json());
        } else {
            throw new InvalidResponseException("An error occured when refreshing token");
        }

        return $response;
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
            !$this->access_token || !$this->refresh_token || !$this->token_expires
        ) {
            throw new InvalidResponseException("Access tokens invalid. Do not forget to give permission to your hootsuite account");
        }

        if ($this->isExpiredToken()) {
            $this->refreshToken();
        }
    }

    /**
     * @param string|array $hashtags
     * @return string
     */
    private function getHashTags($hashtags): string
    {
        if (empty($hashtags)) {
            return '';
        }

        // ["this", "is", "a", "test"]
        if (is_array($hashtags)) {
            return  $this->convertHashtagsToString($hashtags);
        }

        // this|is|a|tag
        if (Str::contains($hashtags, '|')) {
            return $this->convertHashtagsToString(explode('|', $hashtags));
        }

        // this,is,a,tag
        if (Str::contains($hashtags, ',')) {
            return $this->convertHashtagsToString(explode('|', $hashtags));
        }

        return $hashtags;
    }

    private function convertHashtagsToString(array $hashtags): string
    {
        return collect($hashtags)->filter()->map(function ($hashtag) {
            return Str::startsWith($hashtag, '#') ? $hashtag : '#' . $hashtag;
        })->join(',');
    }

    /**
     * Send a post request to the Hootsuite Api
     *
     * @param string $url
     * @return Response
     */
    public static function postAsForm(?string $url = null, array $data = [])
    {
        return Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic ' .  base64_encode(config('hootsuite.client_id') . ':' . config('hootsuite.client_secret')),
            ])
            ->post($url ?? self::API_ENDPOINT . 'oauth2/token', $data)
            ->throw();
    }

}
