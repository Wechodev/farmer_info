<?php
namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Pool\Exception\Exception;
use EasySwoole\RedisPool\RedisPoolException;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        $config = new \EasySwoole\ORM\Db\Config(Config::getInstance()->getConf('MAIN_SERVER.MYSQL'));
        try {
            $config->setMaxObjectNum(20);
        } catch (Exception $e) {
            \EasySwoole\EasySwoole\Trigger::getInstance()->error($e->getMessage());
        }//配置连接池最大数量
        DbManager::getInstance()->addConnection(new Connection($config));

        try {
            $redisPoolConfig = \EasySwoole\RedisPool\Redis::getInstance()->register('redis', new \EasySwoole\Redis\Config\RedisConfig(Config::getInstance()->getConf('MAIN_SERVER.REDIS')));
            $redisPoolConfig->setMinObjectNum(5);
            $redisPoolConfig->setMaxObjectNum(20);
        } catch (\Exception $e) {
            \EasySwoole\EasySwoole\Trigger::getInstance()->error($e->getMessage());
        }

    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}