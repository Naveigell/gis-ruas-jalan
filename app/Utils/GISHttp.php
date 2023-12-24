<?php

namespace App\Utils;

class GISHttp extends BaseHttp
{
    private const ENDPOINT = 'https://gisapis.manpits.xyz';
    private $token;

    /**
     * Create road
     *
     * @param $payload
     * @return array|mixed
     */
    public function createRoad($payload)
    {
        return $this->post(self::ENDPOINT . '/api/ruasjalan', $payload, ["Authorization" => "Bearer {$this->token}"])->json();
    }

    public function listRoad()
    {
        return $this->get(self::ENDPOINT . '/api/ruasjalan', [], ["Authorization" => "Bearer {$this->token}"])->json();
    }

    /**
     * Login method for GIS
     *
     * @param $payload
     * @return array|mixed
     */
    public function login($payload)
    {
        return $this->post(self::ENDPOINT . '/api/login', $payload)->json();
    }

    /**
     * Get list of province
     *
     * @return array|mixed
     */
    public function listProvince()
    {
        return $this->get(
            self::ENDPOINT . '/api/mregion',
            [],
            ["Authorization" => "Bearer {$this->token}"]
        )->json();
    }

    /**
     * Get list of existing road
     *
     * @return array|mixed
     */
    public function listExistingRoad()
    {
        return $this->get(
            self::ENDPOINT . '/api/meksisting',
            [],
            ["Authorization" => "Bearer {$this->token}"]
        )->json();
    }

    /**
     * Get list of road condition
     *
     * @return array|mixed
     */
    public function listRoadCondition()
    {
        return $this->get(
            self::ENDPOINT . '/api/mkondisi',
            [],
            ["Authorization" => "Bearer {$this->token}"]
        )->json();
    }

    /**
     * Get list of road type
     *
     * @return array|mixed
     */
    public function listRoadType()
    {
        return $this->get(
            self::ENDPOINT . '/api/mjenisjalan',
            [],
            ["Authorization" => "Bearer {$this->token}"]
        )->json();
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }
}
