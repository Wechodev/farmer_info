<?php

namespace App\HttpController\Api\Common;

use App\HttpController\Api\ApiBase;

class CommonBase extends ApiBase
{
    public function upload()
    {
        $file  = $this->request()->getUploadedFile('file');
        $type  = $this->request()->getParsedBody()['type'];
        $name  = $this->request()->getParsedBody()['name'];

        $file_name = date('Y-m-d H:i:s', time()).'-'.$name.'.'.explode('/', $file->getClientMediaType())[1];
        if (!is_dir('Public/file/'.$type.'/')) {
            mkdir('Public/file/'.$type.'/', 0777, true);
        }

        $file->moveTo('Public/file/'.$type.'/'.$file_name);

        $file_url =  '/Public/file/'.$type.'/'. $file_name;

        $this->writeJson(200, $file_url);
    }
}
