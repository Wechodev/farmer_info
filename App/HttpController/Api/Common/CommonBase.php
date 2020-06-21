<?php

namespace App\HttpController\Api\Common;

use App\HttpController\Api\ApiBase;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;

class CommonBase extends ApiBase
{
    /**
     * 上传文件
     * @Param(name="name", alias="name", required="", string="")
     * @Param(name="type", alias="type", required="", string="")
     */
    public function upload()
    {
        $file  = $this->request()->getUploadedFile('file');
        $type  = $this->request()->getParsedBody()['type'];
        $name  = $this->request()->getParsedBody()['name'];

        $file_name = date('YmdHis', time()).'-'.$name.'.'.explode('/', $file->getClientMediaType())[1];
        if (!is_dir('Public/file/'.$type.'/')) {
            mkdir('Public/file/'.$type.'/', 0777, true);
        }

        $file->moveTo('Public/file/'.$type.'/'.$file_name);

        $file_url =  '/Public/file/'.$type.'/'. $file_name;

        $this->writeJson(200, $file_url);
    }
}
