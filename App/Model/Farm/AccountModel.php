<?php

namespace App\Model\Farm;


use EasySwoole\Mysqli\Exception\Exception;
use EasySwoole\ORM\AbstractModel;


/**
 * Class UserModel
 * Create With Automatic Generator
 * @property $id
 * @property $nick_name
 * @property $open_id
 * @property $nick_pic
 * @property $sex
 * @property $telephone
 * @property $status
 * @property $shop_id
 */
class AccountModel extends BaseModel
{
    protected $tableName = 'accounts';

    protected $primaryKey = 'id';

    public function getInfo(string $open_id, int $id = null):?AccountModel
    {
        $array = [
            'open_id' => $open_id
        ];
        if ($id) {
            array_push($array, ['id'=>$id]);
        }

        return $this->get($array);
    }

    public function getAllInfo(int $page = 1, string $keyword = null, int $pageSize = 10):array
    {

    }

    public function insertData(array $data):?AccountModel
    {
        $this->open_id = $data['open_id'];
        $this->status = 1;
        $this->shop_id = 0;

        $this->id = $this->save();

        return  $this;
    }

    public function updateData(array $data, string $open_id):AccountModel
    {
        /*$this->nick_name = $data['nick_name'];
        $this->nick_pic = $data['nick_pic'];
        $this->sex = $data['sex'];
        $this->telephone = $data['telephone'];
        $this->status = 1;
        $this->shop_id = 0;*/

        $this->where(['open_id' => $open_id])->update($data);
        $this->getInfo($open_id);

        return  $this;

    }

    public function deleteData(AccountModel $model):AccountModel
    {
        try {
            $this->update(['status' => 0], ['id' => $model->id]);
        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {
        }

        return  $this->getInfo($model->open_id);
    }
}