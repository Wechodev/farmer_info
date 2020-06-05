<?php


namespace App\Model\Farm;

/**
 * 购物车类
 * @property $id
 * @property $good_id
 * @property $quantity
 * @property $account_id
 * @property $products
 * @property $is_overdue
 * Class CartModel
 * @package App\Model\Farm
 */
class CartModel extends BaseModel
{
    protected $tableName = 'carts';

    protected $primaryKey = 'id';

    public function products()
    {
        return $this->hasOne(ProductModel::class, null, 'good_id', 'id');
    }

    public function getAll(int $account_id)
    {
        return  $this->with(['products'])->where(['account_id'=>$account_id])->order('id', 'DESC')->all();
    }

    public function getCartToOrder(array $cart_ids)
    {
        return $this->where('id', $cart_ids, 'IN')->all();
    }

    public function createInfo(array $data):CartModel
    {
        $old_info = $this->where(['good_id'=>$data['product_id']])->get();

        if ($old_info) {
            $old_info->quantity = $old_info->quantity + $data['quantity'];
            $old_info->update();

            return $old_info;
        } else {
            $this->account_id = $data['user_id'];
            $this->quantity = $data['quantity'];
            $this->good_id = $data['product_id'];
            $this->is_overdue = 0;

            $this->id = $this->save();
            return $this;
        }
    }

    public function getInfo(int $cat_id):CartModel
    {
       return $this->with(['products'])->get($cat_id);
    }

    public function updateInfo(array $data):CartModel
    {
        $cart_id = $data['cart_id'];

        $now_info = $this->getInfo($cart_id);
        if ($data['deal']==0) {
            $now_info->quantity = $now_info->quantity - ($data['quantity']??1);
            if ($now_info->quantity==0) {
                $this->destroy(['id' => $cart_id]);
            }
        } else {
            $now_info->quantity = $now_info->quantity + ($data['quantity']??1);
        }

        $now_info->update();

        return $now_info;
    }

}