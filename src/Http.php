<?php

namespace WuJunze\HttpClient;

use GuzzleHttp\ClientInterface as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Http implements HttpInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    protected static $HTTP_OPTIONS = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    /**
     * Http instance.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var HandlerStack
     */
    protected $httpHandler;

    /**
     * @var string
     */
    protected $gateway;

    /**
     * 设置 HTTP 选项.
     *
     * @param array $options
     */
    public static function setOptions(array $options)
    {
        self::$HTTP_OPTIONS = array_merge(self::$HTTP_OPTIONS, $options);
    }

    /**
     * Set the api gateway.
     *
     * @param string $gateway
     *
     * @return $this
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function getGateway()
    {
        return $this->gateway;
    }

    public function request($method, $uri, $options = [])
    {
        $options['handler'] = $this->getHttpHandler();

        return $this->getHttpClient()->request($method, $this->gateway.ltrim($uri, '/'), $options);
    }

    public function get($uri, $options = [])
    {
        return $this->request('GET', $uri, $options);
    }

    public function post($uri, $options = [])
    {
        return $this->request('POST', $uri, $options);
    }

    public function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->setHttpClient(new \GuzzleHttp\Client(self::$HTTP_OPTIONS));
        }

        return $this->httpClient;
    }

    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    public function getHttpHandler()
    {
        if ($this->httpHandler === null) {
            $this->httpHandler = HandlerStack::create();
            $this->registerMiddlewares($this->httpHandler);
        }

        return $this->httpHandler;
    }

    protected function registerMiddlewares($handler)
    {
        $handler->push($this->retryMiddleware());
        $handler->push($this->logMiddleware());
    }

    protected function retryMiddleware()
    {
        return Middleware::retry(function ($retries, RequestInterface $request, ResponseInterface $response = null) {
            // Limit the number of retries to 2
            if ($retries <= 2 && $response && $body = $response->getBody()) {
                // Retry on server errors
                if (stripos($body, 'errcode') && (stripos($body, '40001') || stripos($body, '42001'))) {
                    $field = $this->accessToken->getQueryName();
                    $token = $this->accessToken->getToken(true);

                    $request = $request->withUri($newUri = Uri::withQueryValue($request->getUri(), $field, $token));

                    return true;
                }
            }

            return false;
        });
    }

    protected function logMiddleware()
    {
        if ($this->logger) {
            return Middleware::log($this->logger, new \GuzzleHttp\MessageFormatter(\GuzzleHttp\MessageFormatter::DEBUG));
        } else {
            return Middleware::tap();
        }
    }
}
