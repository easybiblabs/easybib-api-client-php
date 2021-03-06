<?php

namespace EasyBib\Api\Client\ApiResource;

use EasyBib\Api\Client\LinkTransformer\LinkTransformerInterface;
use EasyBib\Api\Client\LinkTransformer\NullLinkTransformer;

class Relation
{
    /**
     * @var \stdClass
     */
    private $rawData;

    /**
     * @var LinkTransformerInterface
     */
    private $linkTransformer;

    /**
     * @var array
     */
    private static $requiredKeys = [
        'href',
        'rel',
    ];

    /**
     * @param \stdClass $rawData
     */
    public function __construct(\stdClass $rawData)
    {
        self::validate($rawData);

        $this->rawData = $rawData;
        $this->linkTransformer = new NullLinkTransformer();
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->linkTransformer->transform($this->rawData->href);
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return trim($this->rawData->rel);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return isset($this->rawData->type) ? trim($this->rawData->type) : null;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return isset($this->rawData->title) ? trim($this->rawData->title) : null;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return [
            'href' => $this->getHref(),
            'rel' => $this->getRel(),
            'type' => $this->getType(),
            'title' => $this->getTitle(),
        ];
    }

    /**
     * @param LinkTransformerInterface $linkTransformer
     * @return self
     */
    public function setLinkTransformer(LinkTransformerInterface $linkTransformer)
    {
        $this->linkTransformer = $linkTransformer;
        return $this;
    }

    /**
     * @param \stdClass $rawData
     * @throws InvalidResourceLinkException
     */
    private static function validate(\stdClass $rawData)
    {
        foreach (self::$requiredKeys as $key) {
            if (!isset($rawData->$key)) {
                throw new InvalidResourceLinkException('Missing ' . $key);
            }
        }
    }
}
