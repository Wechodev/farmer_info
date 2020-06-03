<?php


namespace App\HttpController\Api\Shop;


use App\Model\Farm\StyleModel;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ORM\Exception\Exception;

class Style extends ShopBase
{
    /**
     * 获取类型列表
     * @throws Exception
     */
    public function styleList()
    {
        $style = new StyleModel();

        try {
            $data = $style->getAll();
            $this->writeJson(200, $data);
        } catch (\Exception $e) {
            $this->writeJson(503, null, '获取列表失败', false, $e->getMessage());
        }
    }

    /**
     * 创建类型
     * @Param (name="name",alias="name", reqiured="名字必须上传", string="")
     * @Param (name="file",alias="file", required="logo必须上传", string="")
     * @Param (name="parent_id",alias="parent_id", optional="", integer="")
     */
    public function createStyle()
    {
        $param = $this->request()->getParsedBody();
        $style_model = new StyleModel();
        try {
            $style_model->createInfo($param);
            $this->writeJson(200, $style_model);
        }catch (\Exception $e) {
            $this->writeJson(503, null, '创建类型失败', false, $e->getMessage());
        }
    }

    /**
     * 更新类型
     * @Param (name="style_id",alias="style_id", reqiured="type_id必须上传", integer="")
     * @Param (name="name",alias="name", reqiured="名字必须上传", string="")
     * @Param (name="file",alias="file", optional="", string="")
     * @Param (name="parent_id",alias="parent_id", optional="", integer="")
     */
    public function updateStyle()
    {
        $data = $this->request()->getParsedBody();

        $style_model = new StyleModel();
        try {
            $update_data = $style_model->updateInfo($data);
            $this->writeJson(200, $update_data);
        } catch (\Exception $e) {
            $this->writeJson(503, null, '更新类型失败', false, $e->getMessage());
        }
    }

    /**
     * 获取店铺列表
     * @Param (name="style_id",alias="style_id", reqiured="type_id必须上传", integer="")
     */
    public function deleteStyle()
    {
        $style_id = $this->request()->getBody()['style_id'];

        $style_model = new StyleModel();

        try {
            $result = $style_model->destroy(['id' => $style_id]);

            $this->writeJson(200, ['delete_status'=>$result]);
        } catch (Exception $e) {
            $this->writeJson(503, null, '删除类型失败', false, $e->getMessage());
        } catch (\Throwable $e) {
            $this->writeJson(503, null, '删除类型失败', false, $e->getMessage());
        }
    }

}