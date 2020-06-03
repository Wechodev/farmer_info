<?php


namespace App\Model\Farm;


/**
 * @property $id
 * @property $account_id
 * @property $shop_id
 * @property $good_id
 * Class CollectModel
 * @package App\Model\Farm
 */
class CollectModel extends BaseModel
{
    protected $tableName = 'carts';

    protected $primaryKey = 'id';

    public function shops()
    {
        $this->hasOne(ShopModel::class, null, 'id', 'shop_id');
    }

    public function products()
    {
        $this->hasOne(ProductModel::class, null, 'id', 'good_id');
    }

    public function getAll($account_no, $mode='shop', int $page = 1, int $pageSize = 10)
    {
        $where['status'] = 1;
        $where['account_id'] = $account_no;
        if ($mode=='shop')
        {
            $list  = $this->where(['shops'])->limit($pageSize * ($page - 1), $pageSize)->order('created', 'DESC')->withTotalCount()->all($where);
        } else {
            $list  = $this->where(['products'])->limit($pageSize * ($page - 1), $pageSize)->order('created', 'DESC')->withTotalCount()->all($where);
        }

        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }


    public function insertData(array $params):CollectModel
    {
        if ($params['mode'] == 'shop') {
            $this->shop_id = $params['collect_id'];
        } else {
            $this->good_id = $params['good_id'];
        }
        $this->account_id = $params['account_id'];
        $this->id = $this->save();

        return $this;
    }
}