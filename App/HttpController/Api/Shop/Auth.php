<?php

namespace App\HttpController\Api\Shop;

use App\Model\Farm\AccountModel;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\WeChat\Exception\MiniProgramError;
use EasySwoole\WeChat\Exception\RequestError;
use EasySwoole\WeChat\MiniProgram\MiniProgram;
class Auth extends ShopBase
{
    public $whiteList=['login'];

    /**
     * 登陆方法
     * @Param (name="js_code", alias="js_code", required="必须提交js_code")
     */
    public function login()
    {
        $param = $this->request()->getParsedBody();
        $js_code = $param['js_code'];

        $session = '';

       /* if ($this->chat_handler instanceof MiniProgram) {
            try {
                $session = $this->chat_handler->auth()->session($js_code);
            } catch (InvalidUrl $e) {
                $this->writeJson(503, null, '获取token错误', false, $e->getMessage());
                return;
            } catch (MiniProgramError $e) {
                $this->writeJson(503, null, '获取token错误', false, $e->getMessage());
                return;
            } catch (RequestError $e) {
                $this->writeJson(503, null, '请求失败', false, $e->getMessage());
                return;
            }
        }

        if (isset($session['errcode']) && $session['errcode']!=0) {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登陆失败', false);
            return;
        }*/

        try {
            //$info = $this->findAccountInfo($session['open_id']);
            $info = $this->findAccountInfo('ceshiceshiceshi001');
            //创建token
            $token = $this->createToken($info);

            $data = [
                'is_need_update' => $info->status==1,
                'token' => $token,
                'session_key' => 'ceshi_session'//$session['session_key']
            ];

            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(503, null, '请求失败', false, $e->getMessage());
            return;
        }
    }

    /**
     * 获取用户信息
     * @param string $open_id
     * @return AccountModel
     */
    public function findAccountInfo(string $open_id):AccountModel
    {
        $model = new AccountModel();

        $account_info = $model->getInfo($open_id);

        if (!$account_info->id) {
            try {
                $data =  [
                    'open_id' => $open_id,
                ];
                $model->insertData($data);
            }catch (Exception $e) {
                Trigger::getInstance()->error($e->getMessage(). $open_id);
            }
        }

        return $model;
    }

    /**
     * 更新用户信息方法
     * @Param (name="nick_name", alias="nick_name", required="微信昵称必须提交")
     * @Param (name="nick_pic", alias="nick_pic", required="微信头像必须提交")
     * @Param (name="country", alias="country")
     * @Param (name="province", alias="province")
     * @Param (name="city", alias="city")
     * @Param (name="sex", alias="gender")
     * @Param (name="iv", alias="iv")
     * @Param (name="encryptedData", alias="encryptedData")
     * @Param (name="session_key", alias="session_key")
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws Exception
     * @throws \Throwable
     */
    public function updateAccountInfo()
    {
        $open_id = $this->who['open_id'];

        $param = $this->request()->getParsedBody();

        $update_data = [
            'telephone' => $param['telephone'],
            'sex' => $param['sex'],
            'province' => $param['province'],
            'country' => $param['country'],
            'nick_pic' => $param['nick_pic'],
            'nick_name' => $param['nick_name']
        ];

        //解析电话号
     /*   $encrypted_data = $param['encryptedData']??null;
        $session_key = $param['session_key']??null;
        $iv = $param['iv']??null;

        if ($this->chat_handler instanceof MiniProgram) {
            if ($encrypted_data && $session_key && $iv) {
                $telephone_array = $this->chat_handler->encryptor()->decryptData($session_key, $iv, $encrypted_data);
                $update_data['telephone'] = $telephone_array['phoneNumber'];
            }
        }*/

        $model = new AccountModel();
        $result = $model->updateData($update_data , $open_id);
        if (!$result) {
            Trigger::getInstance()->error("用户信息更新失败". $open_id);
        }

        $this->writeJson(200, ['user_info'=>$result]);
    }

}