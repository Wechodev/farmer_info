<?php

namespace App\Model\Farm;

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
 * @property $is_owner
 * @property $account
 * @package App\Model\Farm
 */
class SupplyModel extends BaseModel
{
    protected $tableName = 'supply';

    protected $primaryKey = 'id';

    public function accounts()
    {
        return $this->hasOne(AccountModel::class, null, 'id', 'account_id');
    }

    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10, $account_no=null)
    {
        if (!empty($keyword))
        {
            $where['subject'] = ['%' . $keyword . '%','like'];
        }
        if (!empty($account_no))
        {
            $where['account_id'] = $account_no;
        }
        $where['status'] = 1;
        $list  = $this->with(['styles', 'shops'])->limit($pageSize * ($page - 1), $pageSize)->order('distant', 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }

    public function getInfo(int $supply_id):SupplyModel
    {
        $this->get(['id'=>$supply_id])->with(['accounts']);

        return  $this;
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
