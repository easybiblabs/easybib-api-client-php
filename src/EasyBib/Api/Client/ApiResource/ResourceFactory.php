<?php

namespace EasyBib\Api\Client\ApiResource;

use EasyBib\Api\Client\ApiTraverser;
use Psr\Http\Message\ResponseInterface;

class ResourceFactory
{
    const STATUS_ERROR = 'error';

    private $apiTraverser;

    /**
     * @param ApiTraverser $apiTraverser
     */
    public function __construct(ApiTraverser $apiTraverser)
    {
        $this->apiTraverser = $apiTraverser;
    }

    public function createFromData(\stdClass $data)
    {
        if (isset($data->status) && $data->status == self::STATUS_ERROR) {
            $message = isset($data->message) ? $data->message : 'Unspecified resource error';
            throw new ResourceErrorException($message);
        }

        if ($this->isList($data)) {
            return new Collection($data, $this->apiTraverser);
        }

        return new ApiResource($data, $this->apiTraverser);
    }

    /**
     * @param ResponseInterface $response
     * @return Resource
     */
    public function createFromResponse(ResponseInterface $response)
    {
        $data = json_decode($response->getBody());
        $resource = $this->createFromData($data);

        $locationHeaders = $response->getHeader('Location');
        if ($locationHeaders) {
            $resource->setLocation($locationHeaders[0]);
        }
        $totalRowsHeaders = $response->getHeader('X-EasyBib-TotalRows');
        if ($totalRowsHeaders && method_exists($resource, 'setTotalRows')) {
            $resource->setTotalRows($totalRowsHeaders[0]);
        }

        return $resource;
    }

    /**
     * Whether the data contained is an indexed array, as opposed to key-value
     * pairs, a.k.a. associative array. This mirrors an ambiguity in the API
     * payloads. The `data` section can contain either a set of key-value
     * pairs, *or* an array of "child" items.
     *
     * @param \stdClass $data
     * @return bool
     */
    private function isList(\stdClass $data)
    {
        return isset($data->data) && is_array($data->data);
    }
}
