<?php

namespace EasyBib\Api\Client;

use EasyBib\Api\Client\Resource\Reference;
use Guzzle\Http\Message\Response;

class ResponseDataContainer
{
    /**
     * @var \stdClass
     */
    private $rawData;

    /**
     * @param \stdClass $rawData
     */
    public function __construct(\stdClass $rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return array|\stdClass
     */
    public function getData()
    {
        return $this->rawData->data;
    }

    /**
     * @return array Reference[]
     */
    public function getReferences()
    {
        return array_map(
            function ($reference) {
                return new Reference($reference);
            },
            $this->rawData->links
        );
    }

    /**
     * Whether the data contained is an indexed array, as opposed to key-value
     * pairs, a.k.a. associative array. This mirrors an ambiguity in the API
     * payloads. The `data` section can contain either a set of key-value
     * pairs, *or* an array of "child" items.
     *
     * @return bool
     */
    public function isList()
    {
        return is_array($this->getData());
    }

    /**
     * @param Response $response
     * @return ResponseDataContainer
     */
    public static function fromResponse(Response $response)
    {
        $data = json_decode($response->getBody(true)) ?: (object) [];

        return new ResponseDataContainer($data);
    }
}
