<?php

namespace Guysolamour\Hootsuite\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait HttpTrait
{
    /**
     * @param string $url
     * @return string
     */
    abstract protected function getApiUrl(string $url = ''): string;


    /**
     * Get Http with bearer token
     *
     * @return PendingRequest
     */
    protected function http()
    {
        return Http::withToken($this->access_token);
    }


    /**
     * Send a get request to the hootsuite Api
     *
     * @param string $url
     * @return Response
     */
    public function get(string $url, array $data = [])
    {
        return $this->http()->get($this->getApiUrl($url), $data);
    }

    /**
     * Send a post request to the Hootsuite Api
     *
     * @param string $url
     * @return Response
     */
    public function post(string $url, array $data = [])
    {
        return $this->http()->post($this->getApiUrl($url), $data);
    }

    /**
     * Send a put request to the hootsuite Api
     *
     * @param string $url
     * @return Response
     */
    public function put(string $url, array $data = [])
    {
        return $this->http()->put($this->getApiUrl($url), $data);
    }

    /**
     * Send a delete request to the hootsuite Api
     *
     * @param string $url
     * @return Response
     */
    public function delete(string $url, array $data = [])
    {
        return $this->http()->delete($this->getApiUrl($url), $data);
    }
}
