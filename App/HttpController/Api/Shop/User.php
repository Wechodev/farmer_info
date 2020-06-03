<?php


namespace App\HttpController\Api\Shop;


use App\Model\Farm\CollectModel;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\Exception\Exception;
class User extends ShopBase
{
    public function getAllInfo()
    {
        //暂时不写
    }

    /**
     * 获取店铺列表
     * @Param (name="page",alias="page", optional="", integer="")
     * @Param (name="limit",alias="limit", optional="", integer="")
     * @Param(name="mode", alias="mode", optional="", string="")
     */
    public function getCollect()
    {
        //店铺收藏和商品收藏
        $user_id = $this->login_user['account']['id'];

        $mode = $this->request()->getQueryParam('mode');

        $collect_model = new CollectModel();

        try {
            $data = $collect_model->getAll($user_id, $mode);
            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询列表失败', false);
            return;
        } catch (\Throwable $e) {
            $this->writeJson(404, null, '查询列表失败', false);
            return;
        }

    }

    /**
     * 增加收藏
     * @Param (name="collect_id" alias="collect_id", required="", integer="")
     * @Param (name="mode" alias="mode", required="", string="")
     */
    public function createCollect()
    {
        $insert_param = $this->request()->getParsedBody();
        $collect_mode = new CollectModel();

        try {
            $collect_mode->insertData($insert_param);

            $this->writeJson(200, $collect_mode);
        } catch (InvalidUrl $e) {
            $this->writeJson(503, null, '增加收藏失败了', false, $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->writeJson(503, null, '增加收藏失败了', false, $e->getMessage());
            return;
        }
    }

    /**
     * 删除收藏
     * @Param (name="collect_id" alias="collect_id", required="", integer="")
     */
    public function deleteCollect()
    {
        $collect_id = $this->request()->getBody()['collect_id'];

        $shop_model = new CollectModel();
        try {
            $result = $shop_model->destroy(['id' => $collect_id]);
            $this->writeJson(200, ['delete_status'=>$result]);
        } catch (Exception $e) {
            $this->writeJson(503, null, '删除收藏失败了', false, $e->getMessage());
            return;
        } catch (\Throwable $e) {
            $this->writeJson(503, null, '删除收藏失败了', false, $e->getMessage());
            return;
        }
    }
}