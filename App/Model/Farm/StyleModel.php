<?php


namespace App\Model\Farm;

/**
 * 类型style
 * @property $id
 * @property $name
 * @property $picture
 * @property $parent_id
 * Class StyleModel
 * @package App\Model\Farm
 */
class StyleModel extends BaseModel
{
    protected $tableName = 'types';

    protected $primaryKey = 'id';

    public function getAll()
    {
        return $this->order('id', 'DESC')->all();
    }

    public function createInfo(array $data):StyleModel
    {
        $this->name = $data['name'];
        $this->picture = $data['file'];
        $this->parent_id = $data['parent_id']??0;

        $this->id = $this->save();

        return $this;
    }

    public function updateInfo(array $data):StyleModel
    {
        $style_id = $data['style_id'];
        unset($data['style_id']);
        if (isset($data['file'])) {
            $data['picture'] = $data['file'];
        }

        $this->where(['id'=>$style_id])->update($data);

        return  $this->get($style_id);
    }


}