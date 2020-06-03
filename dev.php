<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'reload_async' => true,
            'max_wait_time'=>3
        ],
        'TASK'=>[
            'workerNum'=>4,
            'maxRunningNum'=>128,
            'timeout'=>15
        ],
        'MYSQL' => [
            'host'          => '127.0.0.1',
            'port'          => 8889,
            'user'          => 'root',
            'password'      => 'root',
            'database'      => 'beautiful_farm',
            'timeout'       => 5,
            'charset'       => 'utf8mb4',
        ],
        'WECHAT' => [
            'appid' => 'xxxxxxxxx',
            'secret' => 'xxxxxxxxx',
            'grant_type' => 'authorization_code'
        ],
        'REDIS' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => null,
            'db' => null
        ],
        'MAP' => [
            'key' => 'xxxxx',
            'url' => 'https://apis.map.qq.com/ws/'
        ],
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null
];
