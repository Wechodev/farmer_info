<?php

namespace App\HttpController;

use App\Model\Farm\AccountModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\Jwt\Jwt;


class BaseController extends AnnotationController
{
    protected $secret_key = 'farm-app-secret';
    function index()
    {
        $this->actionNotFound('index');
    }

    protected function clientRealIP($headerName = 'x-real-ip')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri = $this->request()->getHeader($headerName);
        $xff = $this->request()->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) $clientAddress = $list[0];
            }
        }
        return $clientAddress;
    }


    protected function input($name, $default = null)
    {
        $value = $this->request()->getRequestParam($name);
        return $value ?? $default;
    }

    public function createToken(AccountModel $model):string
    {
        $jwt_object = Jwt::getInstance()
            ->setSecretKey($this->secret_key) // 秘钥
            ->publish();

        $jwt_object->setAlg('HMACSHA256'); // 加密方式
        $jwt_object->setAud('user'); // 用户
        $jwt_object->setExp(time()+36000); // 过期时间 暂时时间长一点
        $jwt_object->setIat(time()); // 发布时间
        $jwt_object->setIss('farm_app'); // 发行人
        $jwt_object->setJti(md5(time())); // jwt id 用于标识该jwt
        $jwt_object->setNbf(time()+60*5); // 在此之前不可用
        $jwt_object->setSub('主题'); // 主题

        $jwt_object->setData([
            'nick_name' => $model->nick_name,
            'id' => $model->id,
            'open_id' =>$model->open_id,
            'nick_pic' => $model->nick_pic,
            'telephone' => $model->telephone,
        ]);

        return  $jwt_object->__toString();
    }

    public function decodeToken(string $token):?array
    {
        $data = [
            'account' => [],
            'is_status' => -1
        ];
        try {
            $jwt_object = Jwt::getInstance()->setSecretKey($this->secret_key)->decode($token);
            $status = $jwt_object->getStatus();

            switch ($status)
            {
                case  1:
                    echo '验证通过';
                    $data = [
                       'account' => $jwt_object->getData(),
                       'status' => 1
                    ];
                    break;
                case  -1:
                    echo '无效';
                    $data = [
                        'account' => [],
                        'status' => -1
                    ];
                    break;
                case  -2:
                    echo 'token过期';
                    $data = [
                        'account' => [],
                        'status' => -2
                    ];
                    break;
            }
        } catch (\EasySwoole\Jwt\Exception $e) {
            Trigger::getInstance()->error($e->getMessage());
        } finally {
            return $data;
        }
    }

    protected function writeJson($statusCode = 200, $result = null, $msg = null, $is_success=true, $bug_info=null)
    {
        if (!$this->response()->isEndResponse()) {
            $data = Array(
                'meta' => [
                    'code' => $statusCode,
                    'message' => $msg?$msg:'success',
                    'success' => $is_success?true:false
                ],
                'data' => $result,
            );

            if ($bug_info && $bug_info instanceof \Exception) {
                Trigger::getInstance()->error($bug_info->getMessage().':'.$bug_info->getFile());
            }
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }
}