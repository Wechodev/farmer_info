<?php
namespace App\Model\Farm;

/**
 * 产品model ProductModel
 * @property $id
 * @property $name
 * @property $price
 * @property $weight
 * @property $unit
 * @property $type_id
 * @property $is_hot
 * @property $is_recommend
 * @property $picture
 * @property $status
 * @property $describe
 * @property $detail_picture
 * @property $shop_id
 * @property $is_owner
 * @property $shops
 * @property $styles
 * @property $is_collect
 */
class ProductModel extends BaseModel
{
    protected $tableName = 'goods';

    protected $primaryKey = 'id';


    public function shops()
    {
        return $this->hasOne(ShopModel::class, null, 'shop_id', 'id');
    }

    public function styles()
    {
        return $this->hasOne(StyleModel::class, null, 'type_id', 'id');
    }

    /**
     * 获取全部商品
     * @param int $page
     * @param string|null $keyword
     * @param int $pageSize
     * @return array
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10)
    {
        $where = [];
        if (!empty($keyword))
        {
            $where['name'] = ['%' . $keyword . '%','like'];
        }
        $where['status'] = 1;
        $list  = $this->with(['styles', 'shops'])
            ->limit($pageSize * ($page - 1), $pageSize)
            ->order('id', 'DESC')
            ->withTotalCount()
            ->all($where);

        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }


    public function getInfo(int $product_id):ProductModel
    {
        return $this->with(['styles', 'shops'])->get($product_id);
    }

    public function createInfo(array $data):ProductModel
    {
        $this->name = $data['name'];
        $this->unit = $data['unit'];
        $this->weight = $data['weight'];
        $this->type_id = $data['type_id'];
        $this->describe = $data['describe'];
        $this->shop_id = $data['shop_id'];
        $this->picture = $data['picture'];
        $this->price = $data['price'];
        $this->detail_picture = $data['detail_picture'];
        $this->status = 1;

        $this->id = $this->save();

        return $this;
    }

    public function updateInfo(array $data):ProductModel
    {
        $product_id = $data['product_id'];
        unset($data['product_id']);

        $this->where(['id'=>$product_id])->update($data);

        return  $this->getInfo($product_id);
    }

}
