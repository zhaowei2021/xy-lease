<?php

namespace app\api\model\xylease\goods;

use think\Model;

/**
 * 分类模型
 */
class Category extends Model
{

    // 表名
    protected $name = 'xylease_goods_category';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $hidden = ['createtime', 'updatetime', 'status'];

    // 追加属性
    protected $append = [

    ];

    /**
     * 缓存递归获取子分类 id
     */
    public static function getCategoryIds($id)
    {
        // 判断缓存
        $cacheKey = 'goods-category-' . $id . '-child-ids';
        $categoryIds = cache($cacheKey);

        if (!$categoryIds) {
            $categoryIds = self::recursionGetCategoryIds($id);
            cache($cacheKey, $categoryIds, (600 + mt_rand(0, 300)));
        }

        return $categoryIds;
    }


    /**
     * 递归获取子分类 id
     */
    public static function recursionGetCategoryIds($id) {
        $ids = [];
        $category_ids = self::where(['pid' => $id])->column('id');
        if ($category_ids) {
            foreach ($category_ids as $k => $v) {
                $childrenCategoryIds = self::recursionGetCategoryIds($v);
                if ($childrenCategoryIds && count($childrenCategoryIds) > 0) $ids = array_merge($ids, $childrenCategoryIds);
            }
        }

        return array_merge($ids, [intval($id)]);
    }

    public function getImageAttr($value, $data)
    {
        if (!empty($value)) return cdnurl($value, true);
    }

    /**
     * 缓存获取所有分类
     */
    public static function getAllCategory($params){
        extract($params);
        // 判断缓存
        $cacheKey = 'goods-category-all';
        $categoryData = cache($cacheKey);

        if (!$categoryData) {
            $categoryData = self::with(['children'])->where(['status'=>'normal','pid'=>0])->order('weigh DESC,id DESC')->select();
            cache($cacheKey, $categoryData, (300 + mt_rand(300, 800)));
        }

        return $categoryData;
    }

    public function children()
    {
        return $this->hasMany(self::class, 'pid', 'id')->where(['status' => 'normal'])->order('weigh DESC,id DESC');
    }


}
