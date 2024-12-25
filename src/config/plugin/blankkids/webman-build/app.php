<?php

return [
    'enable' => true,
    'file_name_format' => [
        'controller' => 'Controller',
        'model' => 'Model',
        'service' => 'Service',
        'middleware' => 'Middleware',
        'request' => 'Request',
        'response' => 'Response',
        'validator' => 'Validator',
        'repository' => 'Repository',
    ],
    'domain_path' => 'app' . DIRECTORY_SEPARATOR . 'domain',
    'child_path' => [
        'console',
        'error',
        'enum',
        'job',
        'model',
        'entity',
        'service',
        'repository',
        'port' . DIRECTORY_SEPARATOR . 'controller',
        'port' . DIRECTORY_SEPARATOR . 'request',
        'port' . DIRECTORY_SEPARATOR . 'logic',
        'port' . DIRECTORY_SEPARATOR . 'transform',
    ],
];