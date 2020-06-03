<?php


namespace App\Model\Farm;

/**
 * 订单详情表
 * @property $id
 * @property $order_no
 * @property $good_id
 * @property $pay_amount
 * @property $quantity
 * @property $order_amount
 * Class OrderInfoModel
 * @package App\Model\Farm
 */
class OrderInfoModel extends BaseModel
{
    protected $tableName = 'order_info';

    protected $primaryKey = 'id';


    public function products()
    {
        $this->hasOne(ProductModel::class, null, 'id', 'good_id');
    }


    public function orders()
    {
        $this->hasOne(OrderInfoModel::class, null, 'out_trade_no', 'order_no');
    }


    public function getInfo(int $order_id):OrderInfoModel
    {
       return $this->get(['id'=>$order_id])->with(['products']);
    }


    public function createInfo(array $info):OrderInfoModel
    {
        $this->order_no = $info['out_trade_no'];
        $this->good_id = $info['good_id'];
        $this->order_amount = $info['order_amount'];
        $this->pay_amount = $info['pay_amount'];
        $this->quantity = $info['quantity'];

        $this->id = $this->save();

        return $this->getInfo($this->id);
    }
}