<?php

namespace app\api\model\xylease;

use think\Model;

class Notice extends Model
{


    // 表名
    protected $name = 'xylease_notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];

    /**
     * 详情
     */
    public static function getDetail($id)
    {
        $detail = (new self)->where('id', $id)->find();
        if (!$detail || $detail->status == 'hidden') {
            return null;
        }
        return $detail;
    }

    /**
     * 列表
     */
    public static function getLists($params)
    {
        extract($params);
        $where = [
            'status' => 'normal'
        ];

        $order = 'weigh desc,id desc';

        if (isset($ids) && $ids !== '') {
            $idsArray = explode(',', $ids);
            $where['id'] = ['in', $idsArray];
        }

        if (isset($search) && $search !== '') {
            $where['title'] = ['like', "%$search%"];
        }

        $notice = self::where($where)->order($order);

        if (isset($page)) {
            $notice = $notice->paginate();
        } else {
            if(isset($limit)){
                $notice = $notice->limit($limit)->select();
            }else{
                $notice = $notice->select();
            }
        }

        return $notice;
    }

    public function getContentAttr($value, $data)
    {
        $content = $data['content'];
        $content = str_replace("<img src=\"/uploads", "<img src=\"" . cdnurl("/uploads", true), $content);
        $content = str_replace("<video src=\"/uploads", "<video src=\"" . cdnurl("/uploads", true), $content);
        $content = str_replace("<img src=\"/assets", "<img src=\"" . cdnurl("/assets", true), $content);
        $content = str_replace("<video src=\"/assets", "<video src=\"" . cdnurl("/assets", true), $content);
        return $content;
    }


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
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