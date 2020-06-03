<?php

namespace App\HttpController\Api\Common;

use App\HttpController\Api\ApiBase;

class CommonBase extends ApiBase
{
    public function upload()
    {
        $file  = $this->request()->getUploadedFile('file');
        $type  = $this->request()->getBody()['type'];
        $name  = $this->request()->getBody()['name'];


        $file->clientFileName = date('Y-m-d H:i:s', time()).'-'.$name.'.'.$file->getClientMediaType();
        $file->moveTo('Temp/shop/product/');
        $file_url =  'Temp/shop/'.$type.'/'. $file->clientFileName;

        $this->writeJson(200, $file_url);
    }
}
