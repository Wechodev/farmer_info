<?php


namespace App\Model\Farm;


/**
 * @property $id
 * @property $account_id
 * @property $shop_id
 * @property $good_id
 * @property $shops
 * @property $products
 * Class CollectModel
 * @package App\Model\Farm
 */
class CollectModel extends BaseModel
{
    protected $tableName = 'collect';

    protected $primaryKey = 'id';

    public function shops()
    {
        return $this->hasOne(ShopModel::class, null, 'shop_id', 'id');
    }

    public function products()
    {
        return $this->hasOne(ProductModel::class, null, 'good_id', 'id');
    }

    public function getAll($account_no, $mode, int $page = 1, int $pageSize = 10)
    {
        $where['account_id'] = $account_no;

        $list  = $this->with([$mode.'s'])
            ->where(($mode=='shop'?'shop':'good').'_id', 0, '>')
            ->limit($pageSize * ($page - 1), $pageSize)
            ->order('created', 'DESC')
            ->withTotalCount()
            ->all($where);

        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }

    public function getAllMan(int $account_id, string $mode):array
    {
        $list = $this->field([$mode.'_id'])->where($mode.'_id', 0, '>')->all(['account_id'=>$account_id]);

        $data = [];
        foreach ($list as $item) {
            $data[] = $item->{$mode.'_id'};
        }

        return $data;
    }

    public function insertData(array $params):CollectModel
    {
        if ($params['mode'] == 'shop') {
            $this->shop_id = $params['collect_id'];
        } else {
            $this->good_id = $params['collect_id'];
        }
        $this->account_id = $params['account_id'];
        $this->id = $this->save();

        return $this;
    }

}