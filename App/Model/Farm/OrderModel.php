<?php

namespace App\Model\Farm;

use EasySwoole\Mysqli\Exception\Exception;

/**
 * 订单详情
 * Class OrderModel
 * @property $id
 * @property $account_id
 * @property $status
 * @property $address
 * @property $order_amount
 * @property $pay_amount
 * @property $out_trade_no
 * @property $orderInfo
 * @package App\Model\Farm
 */
class OrderModel extends BaseModel
{
    protected $tableName = 'orders';

    protected $primaryKey = 'id';

    public function accounts()
    {
        return $this->hasOne(AccountModel::class, null, 'account_id', 'id');
    }

    public function orderInfo()
    {
        return $this->hasMany(OrderInfoModel::class, null, 'out_trade_no', 'order_no');
    }

    public function getAll(int $page = 1, int $pageSize = 10, $account_no=null)
    {
        if (!empty($account_no))
        {
            $where['account_id'] = $account_no;
        }

        $where['status'] = 1;
        $list  = $this->limit($pageSize * ($page - 1), $pageSize)->order('created', 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }

    public function getInfo(string $out_trade_no, string $account_id):OrderModel
    {
        return $this->with(['orderInfo'])->get(['out_trade_no' => $out_trade_no, 'account_id' => $account_id]);
    }

    public function createOrder(array $data):OrderModel
    {
        $this->order_amount = $data['order_amount'];
        $this->pay_amount = $data['pay_amount'];
        $this->out_trade_no = $data['out_trade_no'];
        $this->address = $data['address'];
        $this->account_id = $data['account_id'];
        $this->status = 1;

        $this->id = $this->save();

        return $this->where(['out_trade_no'=>$data['out_trade_no']])->get();
    }

    public function updateOrder(array $data):OrderModel
    {
        $out_trade_no = $data['out_trade_no'];
        unset($data['out_trade_no']);

        $this->where(['out_trade_no'=>$out_trade_no])->update($data);

        return $this->where(['out_trade_no'=>$data['out_trade_no']])->get();
    }
}