<?php

namespace WuJunze\HttpClient\Traits;

use WuJunze\HttpClient\Http;

trait HttpTrait
{
    /**
     * @var Http
     */
    private $http;

    public function getHttp()
    {
        if ($this->http === null) {
            $this->setHttp(new Http());
        }

        return $this->http;
    }

    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }
}
