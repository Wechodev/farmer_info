<?php
namespace App\Model\Farm;

/**
 * Class ShopModel
 * @property $id
 * @property $name
 * @property $phone
 * @property $address
 * @property $boss_name
 * @property $lng
 * @property $lat
 * @property $picture
 * @property $account_id
 * @property $is_owner
 * @property $tag
 * @property $is_collect
 * @package App\Model\Farm
 */
class ShopModel extends BaseModel
{
    protected $tableName = 'shops';

    protected $primaryKey = 'id';

    public function accounts()
    {
        return $this->hasOne(AccountModel::class, null, 'id', 'account_id');
    }

    /**
     * @param int $page
     * @param string|null $keyword
     * @param int $pageSize
     * @param float $lng
     * @param float $lat
     * @return array
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10, float $lng=0, float $lat=0):array
    {
        $where = [];
        if (!empty($keyword))
        {
            $where['name'] = ['%' . $keyword . '%','like'];
        }
        $list  = $this->field(['*', 'ROUND(
        6378.138 * 2 * ASIN(
            SQRT(
                POW(
                    SIN(
                        (
                            '.$lat.' * PI() / 180 - lat * PI() / 180
                        ) / 2
                    ),
                    2
                ) + COS('.$lat.' * PI() / 180) * COS(lat * PI() / 180) * POW(
                    SIN(
                        (
                            '.$lng.' * PI() / 180 - lng * PI() / 180
                        ) / 2
                    ),
                    2
                )
            )
        ) * 1000
    ) AS distant'])->limit($pageSize * ($page - 1), $pageSize)->order('distant', 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        
        return ['total' => $total, 'list' => $list];
    }

    public function createInfo(array $data):ShopModel
    {
        $this->picture = $data['picture'];
        $this->name = $data['name'];
        $this->phone = $data['phone'];
        $this->address = $data['address'];
        $this->boss_name = $data['boss_name'];
        $this->lat = $data['lat']??0;
        $this->lng = $data['lng']??0;
        $this->account_id = $data['user_id'];
        $this->tag = $data['tag'];
        $this->id = $this->save();

        return  $this;
    }

    public function updateInfo(array $data):ShopModel
    {
        $shop_id = $data['shop_id'];
        unset($data['shop_id']);
        if (isset($data['file'])) {
            $data['picture'] = $data['file'];
        }

        $this->where(['id'=>$shop_id])->update($data);

        return  $this->get($shop_id);
    }
}
