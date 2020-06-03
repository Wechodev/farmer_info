<?php


namespace App\Model\Farm;


use function GuzzleHttp\Promise\all;

/**
 * 购物车类
 * @property $id
 * @property $good_id
 * @property $quantity
 * @property $account_id
 * Class CartModel
 * @package App\Model\Farm
 */
class CartModel extends BaseModel
{
    protected $tableName = 'carts';

    protected $primaryKey = 'id';

    public function products()
    {
        $this->hasOne(ProductModel::class, null, 'id', 'good_id');
    }

    /**
     * 获取需要的购物车信息
     * @param int $account_id
     * @return array|bool|\EasySwoole\ORM\Collection\Collection|\EasySwoole\ORM\Db\Cursor|\EasySwoole\ORM\Db\CursorInterface
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function getAll(int $account_id)
    {
        return  $this->where(['account_id'=>$account_id])->order('id', 'DESC')->with(['products'])->all();
    }

    public function createInfo(array $data):CartModel
    {
        $this->account_id = $data['user_id'];
        $this->quantity = $data['quantity'];
        $this->good_id = $data['good_id'];

        $this->id = $this->save();

        return $this;
    }


    public function getInfo(int $cat_id):CartModel
    {
       return $this->get(['id'=>$cat_id])->with(['products']);
    }


    public function updateInfo(array $data):CartModel
    {
        $cart_id = $data['cat_id'];
        unset($data['out_trade_no']);

        $this->where(['id' => $cart_id])->update($data);

        $now_info = $this->getInfo($cart_id);
        if ($now_info->quantity==0) {
            $this->destroy(['id' => $cart_id]);
        }

        return $now_info;
    }

}