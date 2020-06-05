<?php


namespace App\HttpController\Api\Shop;


use App\Model\Farm\CartModel;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ORM\Exception\Exception;

class Cart extends ShopBase
{
    /**
     * 购物车列表
     */
    public function cartList()
    {
        $user_id = $this->who['id'];

        $model = new CartModel();

        try {
            $data = $model->getAll($user_id);

            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询购物车列表失败', false, $e);
            return;
        } catch (\Throwable $e) {
            $this->writeJson(404, null, '查询购物车列表失败', false, $e);
            return;
        }
    }

    /**
     * 加入购物车
     * @Param(name="product_id", alias="product_id", required="", integer="")
     * @Param(name="quantity", alias="quantity", required="", integer="")
     */
    public function createCart()
    {
        $data = $this->request()->getParsedBody();
        $data['user_id'] = $this->who['id'];

        $model = new CartModel();

        try {
            $new_model = $model->createInfo($data);
            $this->writeJson(200, $new_model);
        }catch (\Exception $e) {
            $this->writeJson(404, null, '查询购物车列表失败', false, $e);
            return;
        }
    }

    /**
     * 更新购物车
     * @Param(name="deal", alias="deal", required="", integer="")
     * @Param(name="cart_id", alias="cart_id", required="", integer="")
     * @Param(name="quantity", alias="quantity", optional="", integer="")
     */
    public function updateCart()
    {
        $data = $this->request()->getParsedBody();
        $data['user_id'] = $this->who['id'];

        $model = new CartModel();

        try {

            $new_model  = $model->updateInfo($data);
            $this->writeJson(200, $new_model);
        }catch (\Exception $e) {
            $this->writeJson(404, null, '购物车列表更新失败', false, $e);
            return;
        }
    }

    /**
     * 删除购物车
     * @Param(name="cart_id", alias="cart_id", required="", integer="")
     */
    public function deleteCart()
    {
        $cart_id = $this->input('cart_id');

        $model = new CartModel();

        try {

            $delete_result = $model->destroy(['id' => $cart_id]);
            $this->writeJson(200, ['delete_status'=>$delete_result]);
        } catch (Exception $e) {
            $this->writeJson(503, null, '购物车列表删除失败', false, $e);
            return;
        } catch (\Throwable $e) {
            $this->writeJson(503, null, '购物车列表删除失败', false, $e);
            return;
        }
    }
}