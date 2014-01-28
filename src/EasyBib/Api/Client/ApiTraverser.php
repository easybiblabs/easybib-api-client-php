<?php

namespace EasyBib\Api\Client;

use EasyBib\Api\Client\Resource\Collection;
use EasyBib\Api\Client\Resource\Resource;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

class ApiTraverser
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->httpClient->setDefaultOption('exceptions', false);
    }

    /**
     * @param $url
     * @return \Guzzle\Http\Message\Response
     */
    public function get($url = null)
    {
        $request = $this->httpClient->get($url);
        $request->setHeader('Accept', 'application/vnd.com.easybib.data+json');

        $dataContainer = ResponseDataContainer::fromResponse($this->send($request));

        if ($dataContainer->isList()) {
            return new Collection($dataContainer, $this);
        }

        return new Resource($dataContainer, $this);
    }

    /**
     * This bootstraps the session by returning the user's "root" Resource
     *
     * @return Resource
     */
    public function getUser()
    {
        return $this->get('/user/');
    }

    /**
     * @param RequestInterface $request
     * @throws ExpiredTokenException
     * @return \Guzzle\Http\Message\Response
     */
    private function send(RequestInterface $request)
    {
        $response = $request->send();

        if ($this->isTokenExpired($response)) {
            throw new ExpiredTokenException();
        }

        return $response;
    }

    /**
     * @param Response $response
     * @return bool
     */
    private function isTokenExpired(Response $response)
    {
        if ($response->getStatusCode() != 400) {
            return false;
        }

        return json_decode($response->getBody(true))->error == 'invalid_grant';
    }
}
