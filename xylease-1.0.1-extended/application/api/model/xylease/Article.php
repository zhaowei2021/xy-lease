<?php

namespace app\api\model\xylease;

use think\Model;

class Article extends Model
{

    // 表名
    protected $name = 'xylease_article';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text',
    ];


    public static function getDetail($id)
    {

        $detail = (new self)->where('id', $id)->find();

        if (!$detail || $detail->status == 'hidden') {
            return null;
        }
        
        return $detail;
    }
    

    public function getImageAttr($value, $data)
    {
        if (!empty($value)) return cdnurl($value, true);

    }
    

    public function getStatusList()
    {
        return ['normal' => __('Normal'),  'hidden' => __('Hidden')];
    }
    

    public function getContentAttr($value, $data)
    {
        $content = $data['content'];
        $content = str_replace("<img src=\"/uploads", "<img style=\"width: 100%;!important\" src=\"" . cdnurl("/uploads", true), $content);
        $content = str_replace("<video src=\"/uploads", "<video style=\"width: 100%;!important\" src=\"" . cdnurl("/uploads", true), $content);
        return $content;
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }



}
