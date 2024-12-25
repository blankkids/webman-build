<?php

return [
    'enable' => true,
    // 文件名后缀
    'file_name_format' => [
        'controller' => 'Controller',
        'model' => 'Model',
        'service' => 'Service',
        'logic' => 'Logic',
        'middleware' => 'Middleware',
        'error' => 'Error',
        'enum' => 'Enum',
        'mappers' => 'Mapper',
        'entity' => 'Entity',
        'response' => 'Response',
        'validate' => 'Validate',
        'repository' => 'Repository',
        'transform' => 'Transform',
    ],
    // 模块路径
    'domain_path' => 'app' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . '{module_name}',
    // 子目录路径
    'child_path' => [
        //presentation
        'controller' => 'presentation' . DIRECTORY_SEPARATOR . 'http',
        'console' => 'presentation' . DIRECTORY_SEPARATOR . 'console',
        'api' => 'presentation' . DIRECTORY_SEPARATOR . 'api',
        //application
        'logic' => 'application' . DIRECTORY_SEPARATOR . 'logic',
        'mappers' => 'application' . DIRECTORY_SEPARATOR . 'mappers',
        'transform' => 'application' . DIRECTORY_SEPARATOR . 'transform',
        'repository' => 'application' . DIRECTORY_SEPARATOR . 'repository',
        'validate' => 'application' . DIRECTORY_SEPARATOR . 'validate',
        'response' => 'application' . DIRECTORY_SEPARATOR . 'response',
        //domain
        'entity' => 'domain' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'entity',
        'value_objects' => 'domain' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'valueObjects',
        'event' => 'domain' . DIRECTORY_SEPARATOR . 'event',
        'factory' => 'domain' . DIRECTORY_SEPARATOR . 'factory',
        'policies' => 'domain' . DIRECTORY_SEPARATOR . 'policies',
        'service' => 'domain' . DIRECTORY_SEPARATOR . 'service',
        'error' => 'domain' . DIRECTORY_SEPARATOR . 'error',
        'enum' => 'domain' . DIRECTORY_SEPARATOR . 'enum',
        //infrastructure
        'model' => 'infrastructure' . DIRECTORY_SEPARATOR . 'model',
        'jobs' => 'infrastructure' . DIRECTORY_SEPARATOR . 'jobs',
    ],
];