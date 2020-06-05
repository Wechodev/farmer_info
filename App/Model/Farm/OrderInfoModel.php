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
        $this->hasOne(ProductModel::class, null, 'good_id', 'id');
    }

    public function orders()
    {
        $this->hasOne(OrderInfoModel::class, null, 'order_no', 'out_trade_no');
    }

    public function getInfo(int $order_id):OrderInfoModel
    {
       return $this->get(['id'=>$order_id])->with(['products']);
    }
}