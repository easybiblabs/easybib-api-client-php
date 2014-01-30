<?php

namespace EasyBib\Api\Client;

class ApiConfig
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
        // 'scope',  // not yet supported
        // 'state',  // not yet supported
    ];

    public function __construct(array $params)
    {
        self::validate($params);
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    private static function validate(array $params)
    {
        $validator = new ArrayValidator(self::$requiredParams, self::$permittedParams);
        $validator->validate($params);
    }
}
