<?php

namespace app\api\model\xylease\recharge;

use think\Model;


class Recharge extends Model
{

    

    

    // 表名
    protected $name = 'xylease_recharge';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    /**
     * 列表
     */
    public static function getLists($params)
    {
        extract($params);
        $where = [
            'status' => 'normal',
            
        ];
        
        $order = 'facevalue asc';

        $data = self::where($where)->order($order);

        if (isset($page)) {
            $data = $data->paginate();
        } else {
            if(isset($limit)){
                $data = $data->limit($limit)->select();
            }else{
                $data = $data->select();
            }
        }

        return $data;
    }

    
    public function getStatusList()
    {
        return ['normal' => __('Status normal'), 'hidden' => __('Status hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    


}
