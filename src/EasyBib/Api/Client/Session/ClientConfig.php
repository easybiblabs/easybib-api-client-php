<?php

namespace EasyBib\Api\Client\Session;


class ClientConfig
{
    /**
     * @var array
     */
    private $params;

    private static $requiredParams = [
        'client_id',
    ];

    private static $permittedParams = [
        'client_id',
        'redirect_url',
        // 'state',  // not yet supported
    ];

    public function __construct(array $params)
    {
        self::validate($params);
        $this->params = $params;
    }

    public function getParams()
    {
        $params = $this->params;

        return $params;
    }

    private static function validate(array $params)
    {
        $validator = new ArrayValidator(self::$requiredParams, self::$permittedParams);
        $validator->validate($params);
    }
}
