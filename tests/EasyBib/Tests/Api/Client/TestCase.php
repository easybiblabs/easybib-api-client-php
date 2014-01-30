<?php

namespace EasyBib\Tests\Api\Client;

use EasyBib\Api\Client\Session\ApiConfig;
use EasyBib\Api\Client\Session\AuthorizationResponse;
use EasyBib\Tests\Mocks\Api\Client\Session\MockTokenStore;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Given
     */
    protected $given;

    /**
     * @var string
     */
    protected $apiBaseUrl = 'http://data.easybib.example.com';

    /**
     * @var HistoryPlugin
     */
    protected $history;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var MockTokenStore
     */
    protected $tokenStore;

    /**
     * @var ApiConfig
     */
    protected $config;

    /**
     * @var AuthorizationResponse
     */
    protected $authorization;

    public function __construct()
    {
        parent::__construct();

        $this->given = new Given();
    }

    public function setUp()
    {
        parent::setUp();

        $this->httpClient = new Client($this->apiBaseUrl);

        $mockResponses = new MockPlugin([
            new Response(200, [], '{}'),
        ]);

        $this->history = new HistoryPlugin();

        $this->config = new ApiConfig([
            'client_id' => 'client_123',
            'redirect_url' => 'http://myapp.example.com/',
        ]);

        $this->httpClient->addSubscriber($mockResponses);
        $this->httpClient->addSubscriber($this->history);
        $this->tokenStore = new MockTokenStore();
        $this->authorization = new AuthorizationResponse(['code' => 'ABC123']);
    }

    public function shouldHaveMadeATokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();

        $expectedParams = [
            'grant_type' => 'authorization_code',
            'code' => $this->authorization->getCode(),
            'redirect_uri' => $this->config->getParams()['redirect_url'],
            'client_id' => $this->config->getParams()['client_id'],
        ];

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($expectedParams, $lastRequest->getPostFields()->toArray());
        $this->assertEquals($this->apiBaseUrl . '/oauth/token', $lastRequest->getUrl());
        // TODO assert return value
    }
}
