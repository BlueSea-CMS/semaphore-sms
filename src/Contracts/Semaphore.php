<?php

namespace BlueSea\Semaphore\Contracts;

use BlueSea\Semaphore\Exceptions\ApiConfigurationException;

class Semaphore extends HttpConnector
{
    public function __construct()
    {
        $this->checkConfig();

        parent::__construct(config('semaphore'));
    }

    public function checkConfig()
    {
        if(!file_exists(config_path('semaphore.php')))
        {
            throw new ApiConfigurationException('Configuration File Not Found');
        }
    }
}
