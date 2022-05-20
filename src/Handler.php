<?php

namespace CGVSeat;

use Curl\Curl;

/**
 * 
 * @package CGVSeat
 * @author Yoppy Dimas <anggaraputra456@gmail.com|yoppy@sgbteam.id> 
 * */
class Handler
{
    const URL = 'https://m.cgv.id/';

    /**
     * @var mixed $token
     */
    private $token;

    /**
     * getting the token or checking exist token
     * 
     * @return array
     */
    protected function getToken()
    {
        if (file_exists('token.txt')) {
            return isset($this->token) ? $this->token : json_decode(@file_get_contents('token.txt'), true);
        }

        $curl = new Curl;
        $curl->get(self::URL);
        $token = $curl->getResponseCookies();
        $this->setToken($token);

        $json = json_encode($token);
        @file_put_contents('token.txt', $json);

        return $token;
    }

    /**
     * build token
     * 
     * @param string $method
     * @param string $uri
     * @param array|null $data
     * 
     * @return object Curl
     */
    protected function fetch(string $method, string $uri, array $data = null)
    {
        $curl = new Curl;
        $token = $this->getToken();
        $method = strtolower($method);
        $headers = [
            'referer' => 'https://m.cgv.id/',
            'accept-language' => 'en-US,en;q=0.9',
            'authority' => 'm.cgv.id',
            'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
            'dnt' => '1',
            'x-xsrf-token' => urldecode($token['XSRF-TOKEN']),
            'sec-ch-ua-mobile' => '?1',
            'user-agent' => 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.74 Mobile Safari/537.36',
            'accept' => 'application/json, text/plain, */*',
            'x-requested-with' => 'XMLHttpRequest',
            'sec-ch-ua-platform' => '"Android"',
            'origin' => 'https://m.cgv.id',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-dest' => 'empty',
            'referer' => 'https://m.cgv.id/',
            'accept-language' => 'en-US,en;q=0.9',
        ];
        $curl->setHeaders($headers);
        $curl->setCookies($token);
        if (isset($data) && ($method == 'post')) {
            $curl->$method(self::URL . $uri, $data);
        } else {
            $curl->$method(self::URL . $uri);
        }

        return $curl->response;
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
}
