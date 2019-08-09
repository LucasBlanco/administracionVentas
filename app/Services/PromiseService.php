<?php
/**
 * Created by PhpStorm.
 * User: lblanco
 * Date: 08/03/19
 * Time: 17:03
 */

namespace App\services;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class PromiseService
{


    private $client;

    public function __construct($base_uri)
    {
        $this->client = new Client([
            'base_uri' => $base_uri,
            'timeout'  => 10.0,
            'json' => true
        ]);
    }

    public function get($url)
    {
       return $this->client->getAsync($url);
    }

    public function post($url, $data)
    {
        return $this->client->postAsync($url, ['body' => $data]);
    }

    public function put($url, $data)
    {
        return $this->client->putAsync($url, ['body' => $data]);
    }

    public function delete($url, $id)
    {
        return $this->client->deleteAsync($url . '/' . $id);
    }

    public function all($promises){
        return Promise\unwrap($$promises);
    }
}