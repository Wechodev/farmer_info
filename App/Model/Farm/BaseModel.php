<?php
namespace App\Model\Farm;


use EasySwoole\ORM\AbstractModel;

class BaseModel extends AbstractModel
{
    protected $autoTimeStamp = true;
    protected $createTime = 'created';
    protected $updateTime = 'modified';

}
