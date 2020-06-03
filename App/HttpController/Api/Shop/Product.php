<?php


namespace App\HttpController\Api\Shop;


use App\Model\Farm\ProductModel;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ORM\Exception\Exception;
class Product extends ShopBase
{

    /**
     * 产品列表
     * @Param (name="page",alias="page", optional="", integer="")
     * @Param (name="limit",alias="limit", optional="", integer="")
     * @Param(name="keyword", alias="关键字", optional="", lengthMax="32")
     */
    public function productList()
    {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);

        $model = new ProductModel();
        try {
            $data = $model->getAll($page, $this->input('keyword'), $limit);
            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询列表失败', false, $e->getMessage());
        } catch (\Throwable $e) {
            $this->writeJson(404, null, '查询列表失败', false, $e->getMessage());
        }
    }

    /**
     * 获取商品
     * @Param (name="product_id", alias="product_id", required="product_id必须上传", integer="")
     * @throws Exception
     */
    public function productInfo()
    {
        $model = new ProductModel();
        $product_id = $this->input('product_id');
        $user_id = $this->login_user['account']['id'];
        $model->getInfo($product_id);

        if (isset($model->shop) && $model->shop->account_id==$user_id) {
            $model->is_owner = true;
        }
        $this->writeJson(200, $model);
    }

    /**
     * 创建产品
     * @Param (name="picture" alias="picture", required="产品正图必须上传", string="")
     * @Param (name="detail_picture" alias="detail_picture", required="描述图片必须上传", string="")
     * @Param (name="price" alias="price", required="产品价格必须上传", float="")
     * @Param (name="weight" alias="weight", required="产品重量必须上传", float="")
     * @Param (name="unit" alias="unit", required="产品重量单位必须上传", float="")
     * @Param (name="type_id" alias="type_id", required="产品类型必须上传", integer="")
     * @Param (name="describe" alias="describe", required="产品描述必须上传", string="")
     * @Param (name="name" alias="name", required="产品名称必须上传", string="")
     */
    public function createdProduct()
    {
        $param = $this->request()->getParsedBody();

        try {
            $product_model = new ProductModel();
            $product_model->createInfo($param);

            $this->writeJson(200, $product_model);
        }  catch (\Exception $e) {
            $this->writeJson(503, null, '创建店铺失败', false, $e->getMessage());
        }
    }

    /**
     * 更新产品
     * @Param (name="picture" alias="picture", optional="", string="")
     * @Param (name="detail_picture" alias="detail_picture", optional="", string="")
     * @Param (name="price" alias="price", required="产品价格必须上传", float="")
     * @Param (name="weight" alias="weight", required="产品重量必须上传", float="")
     * @Param (name="unit" alias="unit", required="产品重量单位必须上传", float="")
     * @Param (name="type_id" alias="type_id", required="产品类型必须上传", integer="")
     * @Param (name="describe" alias="describe", required="产品描述必须上传", string="")
     * @Param (name="name" alias="name", required="产品名称必须上传", string="")
     * @Param (name="product_id",alias="product_id", reqiured="product_id必须提交", integer="")
     */
    public function updateProduct()
    {
        $data = $this->request()->getParsedBody();

        try {
            $product_model = new ProductModel();
            $product_model->updateInfo($data);

            $this->writeJson(200, $product_model);
        } catch (\Exception $e) {
            $this->writeJson(503, null, '创建店铺失败', false, $e->getMessage());
        }
    }

    /**
     * 删除产品
     * @Param (name="product_id" alias="proudct_id", required="proudct_id必须上传", string="")
     * @throws Exception
     */
    public function deleteProduct()
    {
        $product_id = $this->request()->getBody()['product_id'];

        $product_model = new ProductModel();
        try {
            $result = $product_model->updateInfo(['status'=>0, 'product_id'=>$product_id]);
            $this->writeJson(200, ['delete_status'=>$result]);
        } catch (Exception $e) {
            $this->writeJson(503, null, '删除产品失败', false, $e->getMessage());
        } catch (\Throwable $e) {
            $this->writeJson(503, null, '删除产品失败', false, $e->getMessage());
        }
    }
}