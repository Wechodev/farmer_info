<?php
namespace App\HttpController\Api\Shop;

use App\HttpController\Api\ApiBase;
use EasySwoole\Http\Message\Status;
use EasySwoole\WeChat\MiniProgram\MiniProgram;
class ShopBase extends ApiBase
{
    //public才会根据协程清除
    public $who;
    public $chat_handler;
    public $redis_handler;
    public $whiteList;

    /**
     * onRequest
     * @param null|string $action
     * @return bool|null
     * @throws \Throwable
     * Time: 13:49
     */
    public function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            $instance = \EasySwoole\EasySwoole\Config::getInstance();
            $app_id = $instance->getConf('MAIN_SERVER.WECHAT.appid');
            $app_secret = $instance->getConf('MAIN_SERVER.WECHAT.secret');

            $this->chat_handler = new MiniProgram();
            $this->chat_handler->getConfig()->setAppId($app_id)->setAppSecret($app_secret);

            //白名单判断
            if ($this->whiteList && in_array($action, $this->whiteList)) {
                return true;
            }

            //获取登入信息
            if (!$this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '无效的token', false);
                return false;
            }


            $this->redis_handler = \EasySwoole\RedisPool\Redis::defer('redis');

            if ($this->redis_handler->get('access_token')) {
                $token_array = [];

                if ($this->chat_handler instanceof MiniProgram) {
                    $token_array = $this->chat_handler->accessToken()->getToken(100);
                }

                if ($token_array['errcode'] != 0) {
                    $this->writeJson(Status::CODE_UNAUTHORIZED, '', '获取token失败', false);
                    return false;
                }
                $token = $token_array['access_token'];

                $this->redis_handler->set('access_token', $token, 105);
            }

            return true;
        }
        return false;
    }

    /**
     * getWho
     * @return bool
     */
    public function getWho(): bool
    {
        if ($this->login_user && $this->login_user['status'] === 1) {
            $this->who = $this->login_user['account'];
            return true;
        }

        return false;
    }

    public function createOrderNo($format = 'YmdHisu', $u_timestamp = null)
    {
        if (is_null($u_timestamp)){
            $u_timestamp = microtime(true);
        }
        $timestamp = floor($u_timestamp);
        $milliseconds = round(($u_timestamp - $timestamp) * 1000000);
        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp).mt_rand(10000, 99999);
    }
}