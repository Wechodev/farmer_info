<?php


namespace App\HttpController\Api\Shop;


use App\Model\Farm\CollectModel;
use App\Model\Farm\ShopModel;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\ORM\Exception\Exception;
class Shop extends ShopBase
{
    /**
     * 获取店铺列表
     * @Param (name="type",alias="type", optional="", integer="")
     * @Param (name="pre",alias="pre", optional="", integer="")
     * @Param (name="iat",alias="iat", optional="", integer="")
     * @Param(name="keyword", alias="关键字", optional="", lengthMax="32")
     */
    public function shopList()
    {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $lng = (float)$this->input('lng', 0);
        $iat = (float)$this->input('iat', 0);

        $account_id = $this->who['id'];
        $model = new ShopModel();
        $collect_mode = new CollectModel();

        try {
            $data = $model->getAll($page, $this->input('keyword'), $limit, $lng, $iat);

            $collect_list = $collect_mode->getAllMan($account_id, 'shop');

            foreach ($data['list'] as $datum) {
                if (in_array($datum->id, $collect_list)) {
                    $datum->is_collect = true;
                } else {
                    $datum->is_collect = false;
                }
            }

            $this->writeJson(200, $data);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询列表失败1', false, $e);
        } catch (\Throwable $e) {
            $this->writeJson(404, null, '查询列表失败2', false, $e);
        }
    }

    /**
     * 查询单店铺详情
     * @Param (name="shop_id", alias="shop_id", required="shop_id必须上传", integer="")
     */
    public function shopInfo()
    {
        $user_id = $this->who['id'];

        $shop_id = $this->input('shop_id');
        $model = new ShopModel();
        try {
            $find_data = $model->get($shop_id);
            if ($find_data->account_id == $user_id) {
                $find_data->is_owner = true;
            }

            $collect_mode = new CollectModel();
            $collect_info = $collect_mode->get(['account_id'=>$user_id, 'shop_id'=>$shop_id]);
            $find_data->is_collect = $collect_info->id??false;

            $this->writeJson(200, $find_data);
        } catch (\EasySwoole\Mysqli\Exception\Exception $e) {
            $this->writeJson(404, null,'查询店铺详情失败', false, $e);
        } catch (Exception $e) {
            $this->writeJson(404, null,'查询店铺详情失败', false, $e);
        } catch (\Throwable $e) {
            $this->writeJson(404, null,'查询店铺详情失败', false, $e);
        }

    }

    /**
     * 创建店铺
     * @Param (name="picture", alias="picture", required="logo必须上传", string="")
     * @Param (name="boss_name", alias="boss_name", required="所有者必须上传", string="")
     * @Param (name="address", alias="address", required="地址必须上传", string="")
     * @Param (name="phone", alias="phone", required="电话必须上传", string="")
     * @Param (name="name", alias="name", required="店铺名必须上传", string="")
     * @Param (name="tag", alias="tag", required="经营类型必须上传", string="")
     */
    public function createShop()
    {
        $param = $this->dealShopData();
        $param['user_id'] = $this->who['id'];
        $shop_model = new ShopModel();

        try {
            $shop_model->createInfo($param);

            $this->writeJson(200, $shop_model);
        } catch (InvalidUrl $e) {
            $this->writeJson(503, null, '请求地图经纬度失败了', false, $e);
        } catch (\Exception $e) {
            $this->writeJson(503, null, '创建店铺失败', false, $e);
        }
    }

    /**
     * 更新店铺
     * @Param (name="picture", alias="logo", optional="", string="")
     * @Param (name="boss_name", alias="boss_name", required="所有者必须上传", string="")
     * @Param (name="address", alias="address", required="地址必须上传", string="")
     * @Param (name="phone", alias="phone", required="电话必须上传", string="")
     * @Param (name="name", alias="name", required="店铺名必须上传", string="")
     * @Param (name="name", alias="name", required="店铺名必须上传", string="")
     * @Param (name="tag", alias="tag", required="经营类型必须上传", string="")
     * @Param (name="shop_id", alias="shop_id", reqiured="shop_id必须提交", integer="")
     */
    public function updateShop()
    {
        $data = $this->dealShopData();

        $shop_model = new ShopModel();

        try {

            $new_shop = $shop_model->updateInfo($data);

            $this->writeJson(200, $new_shop);
        } catch (InvalidUrl $e) {
            $this->writeJson(503, null, '请求地图经纬度失败了', false, $e);
        } catch (\Exception $e) {
            $this->writeJson(503, null, '创建店铺失败', false, $e);
        }
    }

    /**
     * 删除店铺
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function deleteShop()
    {
        $shop_id = $this->input('shop_id');

        $shop_model = new ShopModel();

        try {
            $result = $shop_model->destroy(['id'=>$shop_id]);
            $this->writeJson(200, ['delete_status'=>$result]);
        } catch (\Exception $e) {
            $this->writeJson(503, null, '删除店铺失败', false, $e);
        }

    }

    /**
     * 处理店铺经纬度
     * @return array
     */
    public function dealShopData() :array
    {
        $update_param = $this->request()->getParsedBody();
        $address = $update_param['address'];
        $instance = \EasySwoole\EasySwoole\Config::getInstance();
        $app_id = $instance->getConf('MAP.key');
        $url = $instance->getConf('MAP.url');
        $param['lng'] = 0;
        $param['lat'] = 0;

       /* try {
            $client = new HttpClient($url . 'geocoder/v1/?address=' . $address . '&key=' . $app_id);
            $get_result = $client->get();
            $map_data = json_decode($get_result->getBody());

            if ($map_data->status===0) {
                $update_param['lng'] = $map_data->localtion->lng;
                $update_param['lat'] = $map_data->localtion->lat;
            }
        } catch (InvalidUrl $e) {
            $this->writeJson(503, null, '请求地图经纬度失败了', false);
        }*/
        return  $update_param;
    }
}