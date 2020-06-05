<?php
namespace App\HttpController\Api;

use App\HttpController\BaseController;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\HttpAnnotation\Exception\Annotation\ParamValidateError;
use EasySwoole\Http\Message\Status;
abstract class ApiBase extends BaseController
{
    protected $login_user;

    public function index()
    {
        $this->actionNotFound('index');
    }

    protected function actionNotFound(?string $action): void
    {
        $this->writeJson(Status::CODE_NOT_FOUND);
    }

    public function onRequest(?string $action): ?bool
    {
        if (!parent::onRequest($action)) {
            return false;
        }

        if ($this->request()->getHeader('authorization')) {
            $token_array = explode(' ', $this->request()->getHeader('authorization')[0]);

            $this->login_user = $this->decodeToken($token_array[1]);

        }

        return true;
    }

    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ParamValidateError) {
            $msg = $throwable->getValidate()->getError()->getErrorRuleMsg();
            $this->writeJson(400, null, "{$msg}", false);
        } else {
            if (Core::getInstance()->isDev()) {
                $this->writeJson(500, null, $throwable->getMessage(), false);
            } else {
                Trigger::getInstance()->throwable($throwable);
                $this->writeJson(500, null, '系统内部错误，请稍后重试', false);
            }
        }
    }
}
