<?php

namespace WuJunze\HttpClient;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

interface HttpInterface
{

    /**
     * 设置 HTTP 选项.
     *
     * @param array $options
     */
    public static function setOptions(array $options);

    /**
     * Set the api gateway.
     *
     * @param string $gateway
     *
     * @return $this
     */
    public function setGateway($gateway);


    /**
     * @return string
     */
    public function getGateway();


    /**
     * @param $method
     * @param $uri
     * @param array $options
     * @return Response
     */
    public function request($method, $uri, $options = []);

    /**
     * @param $uri
     * @param array $options
     * @return Response
     */
    public function get($uri, $options = []);

    /**
     * @param $uri
     * @param array $options
     * @return Response
     */
    public function post($uri, $options = []);

    /**
     * @return HttpInterface
     */
    public function getHttpClient();

    /**
     * @param ClientInterface $httpClient
     * @return ClientInterface
     */
    public function setHttpClient(ClientInterface $httpClient);

    /**
     * @return HandlerStack
     */
    public function getHttpHandler();

}