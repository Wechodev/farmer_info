<?php


namespace App\HttpController\Api\Shop;


use App\Model\Farm\ProductModel;
use App\Model\Farm\SupplyModel;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ORM\Exception\Exception;
class Supply extends ShopBase
{
    /**
     * 供需列表
     * @Param (name="type",alias="type", optional="", integer="")
     * @Param (name="page",alias="page", optional="", integer="")
     * @Param (name="limit",alias="limit", optional="", integer="")
     * @Param (name="is_my",alias="my", optional="", integer="")
     * @Param(name="keyword", alias="关键字", optional="", lengthMax="32")
     */
    public function supplyList()
    {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $is_main = (int)$this->input('is_my', 0);

        $user_id = $this->login_user['account']['id'];
        $model = new SupplyModel();

        try {
            $data = $model->getAll($page, $this->input('keyword'), $limit, ($account_id= $is_main?$user_id:null));
            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询列表失败', false, $e->getMessage());
        } catch (\Throwable $e) {
            $this->writeJson(404, null, '查询列表失败', false, $e->getMessage());
        }

    }

    public function supplyInfo()
    {
        $user_id = $this->login_user['account']['id'];
        $supply_id = $this->input('supply_id');

        $supply = new SupplyModel();
        $supply->getInfo($supply_id);

        if ($supply->account_id==$user_id) {
            $supply->is_owner = true;
        }

        $this->writeJson(200, $supply);
    }

    public function createSupply()
    {
        $data = $this->request()->getParsedBody();
        $data['account_id'] = $this->login_user['account']['id'];

        $supply_model = new SupplyModel();
        try {
            $supply_model->createSupply($data);
            $this->writeJson(200, $supply_model);
        }  catch (\Exception $e) {
            $this->writeJson(503, null, '创建需求失败', false, $e->getMessage());
        }
    }

    public function updateSupply()
    {
        $data = $this->request()->getParsedBody();
        $data['account_id'] = $this->login_user['account']['id'];

        $supply_model = new SupplyModel();
        try {
            $supply_model->updateSupply($data);
            $this->writeJson(200, $supply_model);
        }  catch (\Exception $e) {
            $this->writeJson(503, null, '创建需求失败', false, $e->getMessage());
        }
    }

    public function deleteSupply()
    {
        $product_id = $this->request()->getBody()['supply_id'];

        $product_model = new SupplyModel();
        try {
            $result = $product_model->updateSupply(['status'=>0, 'supply_id'=>$product_id]);
            $this->writeJson(200, ['delete_status'=>$result]);
        } catch (Exception $e) {
            $this->writeJson(503, null, '删除产品失败', false, $e->getMessage());
        } catch (\Throwable $e) {
            $this->writeJson(503, null, '删除产品失败', false, $e->getMessage());
        }
    }
}