<?php


namespace App\HttpController\Api\Shop;

use App\Model\Farm\CartModel;
use App\Model\Farm\OrderInfoModel;
use App\Model\Farm\OrderModel;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ORM\Exception\Exception;
class Order extends ShopBase
{
    /**
     * 获取订单列表
     * @Param (name="type",alias="type", optional="", integer="")
     * @Param (name="page",alias="page", optional="", integer="")
     * @Param (name="limit",alias="limit", optional="", integer="")
     * @Param (name="is_my",alias="my", optional="", integer="")
     */
    public function orderList()
    {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $is_main = (int)$this->input('is_my', 0);

        $user_id = $this->login_user['account']['id'];
        $model = new OrderModel();

        try {
            $data = $model->getAll($page, $limit, ($account_id= $is_main?$user_id:null));
            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询列表失败', false, $e->getMessage());
        } catch (\Throwable $e) {
            $this->writeJson(404, null, '查询列表失败', false, $e->getMessage());
        }

    }

    public function createOrder()
    {
        $order_info = $this->request()->getParsedBody();

        $cat_id_array = explode(',', $order_info['cart_id']);
        $user_id = $this->login_user['account']['id'];

        $cart_model = new CartModel();
        $order_model = new OrderModel();
        $order_info_model = new OrderInfoModel();
        $pay_amount = 0;
        $order_amount = 0;
        $order_no = $this->createOrderNo();

        try {
            $cart_list = $cart_model->getAll($cat_id_array);
            $cart_list->each(function ($item) use (&$pay_amount, &$order_amount, $order_no, $order_info_model) {
                $pay_amount_single = $item->product->price * $item->quantity;
                $order_amount_single = $item->product->price * $item->quantity;

                $info_data = [
                    'good_id' => $item->good_id,
                    'quantity' => $item->quantity,
                    'pay_amount' => $pay_amount_single,
                    'order_amount' => $order_amount_single,
                ];

                $pay_amount += $pay_amount_single;
                $order_amount += $order_amount_single;

                $order_info_model->createInfo($info_data);
            });

            $order_array = [
                'address' => $order_info['address'],
                'out_trade_no' => $order_no,
                'account_id' => $user_id,
                'order_amount' => $order_amount,
                'pay_amount' => $pay_amount,
            ];

            $order_create_result = $order_model->createOrder($order_array);
            $cart_model->destroy($cat_id_array);

            $this->writeJson(200, $order_create_result);
        } catch (Exception $e) {
            $this->writeJson(404, null,'创建订单失败', false, $e->getMessage());
        } catch (\Throwable $e) {
            $this->writeJson(404, null,'创建订单失败', false, $e->getMessage());
        }

    }

    public function updateOrder()
    {
        try {
            $update_data = $this->request()->getParsedBody();

            $order_model = new OrderModel();
            $order_info = $order_model->updateOrder($update_data);

            $this->writeJson(200, $order_info);
        } catch  (Exception $e) {
            $this->writeJson(404, null,'修改订单失败失败', false, $e->getMessage());
        }

    }

}