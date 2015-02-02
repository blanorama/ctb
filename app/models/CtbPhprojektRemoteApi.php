<?php

use PhprojektRemoteApi\PhprojektRemoteApi;

class CtbPhprojektRemoteApi extends PhprojektRemoteApi
{
    /**
     * Little enhancement to use client by myself until api is further implemented
     *
     * @return \Goutte\Client
     */
    public function getClient()
    {
        return $this->httpClient;
    }

    public function getProjectUrl()
    {
        return $this->phprojektUrl;
    }
}