<?php


namespace App\HttpController\Api\Shop;


use App\Model\Farm\CartModel;
use EasySwoole\ORM\Exception\Exception;

class Cart extends ShopBase
{
    public function cartList()
    {
        $user_id = $this->login_user['account']['id'];

        $model = new CartModel();

        try {
            $data = $model->getAll($user_id);
            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询购物车列表失败', false, $e->getMessage());
            return;
        } catch (\Throwable $e) {
            $this->writeJson(404, null, '查询购物车列表失败', false, $e->getMessage());
            return;
        }
    }

    public function createCart()
    {
        $data = $this->request()->getParsedBody();
        $data['user_id'] = $this->login_user['account']['id'];

        $model = new CartModel();

        try {
            $model->createInfo($data);
        }catch (\Exception $e) {
            $this->writeJson(404, null, '查询购物车列表失败', false, $e->getMessage());
            return;
        }
    }

    public function updateCart()
    {
        $data = $this->request()->getParsedBody();
        $data['user_id'] = $this->login_user['account']['id'];

        $model = new CartModel();

        try {
            $model->updateInfo($data);
        }catch (\Exception $e) {
            $this->writeJson(404, null, '购物车列表更新失败', false, $e->getMessage());
            return;
        }

    }

    public function deleteCart()
    {
        $cart_id = $this->request()->getParsedBody()['cart_id'];

        $model = new CartModel();

        try {
            $model->destroy(['id' => $cart_id]);
        } catch (Exception $e) {
            $this->writeJson(503, null, '购物车列表删除失败', false, $e->getMessage());
            return;
        } catch (\Throwable $e) {
            $this->writeJson(503, null, '购物车列表删除失败', false, $e->getMessage());
            return;
        }
    }
}