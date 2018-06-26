<?php

namespace WuJunze\HttpClient\Tests;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use WuJunze\HttpClient\Http;

class HttpClientTest extends TestCase
{
    public function createHttpClient()
    {
        return new Http();
    }

    /**
     * @test
     */
    public function client()
    {
        $client = $this->createHttpClient();

        $this->assertNotNull(ClientInterface::class, $client);
    }


    /**
     * @test
     */
    public function setOptions()
    {
        $options = [
            'allow_redirects' => [
                'max' => 10,        // allow at most 10 redirects.
                'strict' => true,      // use "strict" RFC compliant redirects.
                'referer' => true,      // add a Referer header
                'protocols' => ['https'], // only allow https URLs
                'track_redirects' => true,
            ],
        ];
        $client = $this->createHttpClient()::setOptions($options);

        $this->assertNull($client);
    }


    public function setGateway($gateway)
    {

    }


    /**
     * @return string
     */
    public function getGateway()
    {

    }


    /**
     * @test
     */
    public function request()
    {
        $result = $this->createHttpClient()->request('get' ,'http://baidu.com/');
        $this->assertNotNull($result);
    }

    /**
     * @test
     */
    public function get()
    {
        $result = $this->createHttpClient()->get('http://baidu.com/');
        $this->assertNotNull($result);
    }

    /**
     * @test
     */
    public function post()
    {
        $result = $this->createHttpClient()->post('http://baidu.com/');
        $this->assertNotNull($result);
    }

    /**
     * @test
     */
    public function getAndSettHttpClient()
    {
        $client = $this->createHttpClient()->setHttpClient((new Client(['base_uri' => 'http://httpbin.org'])));
        $httpClient = $client->getHttpClient();
        $this->assertInstanceOf(Client::class, $httpClient);
    }

    /**
     * @test
     */
    public function getHttpHandler()
    {
        $stack = $this->createHttpClient()->getHttpHandler();
        $this->assertNotNull($stack);

    }

}