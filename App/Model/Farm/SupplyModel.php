<?php

namespace App\Model\Farm;

use EasySwoole\ORM\Collection\Collection;

/**
 * Class SupplyModel
 * @property $id
 * @property $account_id
 * @property $content
 * @property $status
 * @property $views
 * @property $place
 * @property $phone
 * @property $subject
 * @property $picture
 * @property $tags
 * @property $is_owner
 * @package App\Model\Farm
 */
class SupplyModel extends BaseModel
{
    protected $tableName = 'supply';

    protected $primaryKey = 'id';


    public function accounts()
    {
        return $this->hasOne(AccountModel::class, null, 'account_id', 'id');
    }

    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10, $account_no=null)
    {
        if (!empty($keyword))
        {
            $where['subject'] = ['%' . $keyword . '%','like'];
            $where['tags'] = ['%' . $keyword . '%','like'];
        }

        if (!empty($account_no))
        {
            $where['account_id'] = $account_no;
        }
        $where['status'] = 1;

        $list  = $this
            ->with(['accounts'])
            ->where(['supply.status'=>1])
            ->limit($pageSize * ($page - 1), $pageSize)
            ->order('supply.id', 'DESC')->withTotalCount()
            ->all($where);

        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }

    public function getInfo(int $supply_id):SupplyModel
    {
        $find =  $this->with(['accounts'])->get($supply_id);
        $views = $find->views + 1;

        $this->where(['id'=>$supply_id])->update(['views'=>$views]);

        return  $find;
    }

    public function createSupply(array $data):SupplyModel
    {
        $this->account_id = $data['account_id'];
        $this->subject = $data['subject'];
        $this->status = 1;
        $this->views = rand(1, 300);
        $this->place = $data['place'];
        $this->content = $data['content'];
        $this->phone = $data['phone'];
        $this->picture = $data['picture'];
        $this->tag = $data['tag'];

        $this->id = $this->save();

        return $this;
    }

    public function updateSupply(array $data):SupplyModel
    {
        $supply_id = $data['supply_id'];
        unset($data['supply_id']);

        $this->where(['id'=>$supply_id])->update($data);

        return $this->getInfo($supply_id);
    }
}
