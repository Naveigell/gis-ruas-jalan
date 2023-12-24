<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class BaseHttp
{
    /**
     * Base HTTP post request
     *
     * @param $endpoint
     * @param array $payload
     * @param array $headers
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function post($endpoint, $payload = [], $headers = [])
    {
        return $this->http($headers)->post($endpoint, $payload);
    }

    /**
     * Base HTTP get request
     *
     * @param $endpoint
     * @param array $query
     * @param array $headers
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function get($endpoint, $query = [], $headers = [])
    {
        return $this->http($headers)->get($endpoint, $query);
    }

    /**
     * Base HTTP get request
     *
     * @param $endpoint
     * @param array $query
     * @param array $headers
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function put($endpoint, $query = [], $headers = [])
    {
        return $this->http($headers)->put($endpoint, $query);
    }

    /**
     * Base HTTP delete request
     *
     * @param $endpoint
     * @param array $query
     * @param array $headers
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function delete($endpoint, $query = [], $headers = [])
    {
        return $this->http($headers)->delete($endpoint, $query);
    }

    /**
     * Create HTTP client
     *
     * @param $headers
     * @return \Illuminate\Http\Client\PendingRequest
     */
    private function http($headers = [])
    {
        return Http::withHeaders($headers);
    }
}
