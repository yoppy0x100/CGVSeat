<?php

namespace CGVSeat;

use CGVSeat\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

/**
 * 
 * @package CGVSeat
 * @author Yoppy Dimas <anggaraputra456@gmail.com|yoppy@sgbteam.id> 
 * */
class Handler extends Config
{
    const URL = 'https://m.cgv.id/';

    /**
     * @var mixed $token
     */
    private $token;

    #Guzzle\\cookieJar
    private $cookies;

    /**
     * getting the token or checking exist token
     * 
     * @return array
     */
    protected function getToken()
    {

        $client = new Client();
        $response = $client->request('GET', self::URL, [
            'cookies' => $this->cookies,
        ]);

        return $response;
    }

    /**
     * build token
     * 
     * @param string $method
     * @param string $uri
     * @param null $data
     * 
     * @return object Curl
     */
    public function fetch($data = null)
    {
        $this->getToken();
        $client = new Client();
        $csrf = $this->cookies->getCookieByName('XSRF-TOKEN')->getValue();
        $headers = [
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Access-Control-Allow-Origin' => '*',
            'Content-Type' => 'application/json',
            'Dnt' => '1',
            'Origin' => 'https://m.cgv.id',
            'Priority' => 'u=1, i',
            'Referer' => 'https://m.cgv.id/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1 Edg/129.0.0.0',
            'X-XSRF-TOKEN' => urldecode($csrf),
        ];


        $response = $client->request('POST', 'https://m.cgv.id/mw/execute', [
            'cookies' => $this->cookies,
            'headers' => $headers,'json' => $data,
        ]);

        return json_decode($response->getBody());
    }

    private function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function __call($name, $arguments)
    {
        if ($obj = (new \ReflectionClass(__NAMESPACE__ . '\\' . $name))->newInstanceArgs($arguments)) {
            return $obj;
        }

        return $this;
    }

    public function __construct()
    {
        $this->cookies = new CookieJar();
    }
}
