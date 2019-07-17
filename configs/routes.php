<?php
return [
    'api' => [
        'index' => 'welcome/index',
        'index1' => 'welcomep/index1',
        'index2' => 'welcome/index2',
        'import' => 'welcome/importdata',
        'encrypt' => 'welcome/encrypt',
        'rsa' => 'welcome/rsa',
        'sign' => 'welcome/verifysign',
        'upload' => 'welcome/upload',
        'wxsign' => 'welcome/wx-sign',
    ],
    'web' => [
        '/' => 'welcome/index',
        'render' => 'welcome/render',
        'model' => 'welcome/model',
        'cache' => 'welcome/cache',
        'cache1' => 'welcome/cache1',
        'view' => 'welcome/view',
        'smarty' => 'welcome/smarty-view',
        'mail' => 'welcome/send-mail',
        'upload' => 'welcome/upload',
        'log' => 'welcome/log',
        'csrf' => 'welcome/apicsrf',
        'mq' => 'welcome/rabbitmq',
        'ms' => 'welcome/seckill',
        'import' => 'welcome/importdata',
    ],
    'backend' => []
];