<?php

namespace app\api\controller\xylease;

use app\common\controller\Api;
use app\api\model\xylease\Config;
use app\api\model\xylease\store\Store;

/**
 * 通用接口
 */
class Common extends Api
{
	protected $noNeedLogin = ['init','template'];
	protected $noNeedRight = ['*'];


	/**
	 * 加载通用配置
	 */
	public function init()
	{
		 // 判断缓存
		 $cacheKey = 'common-init';
		 $data = cache($cacheKey);
 
		 if (!$data) {
			 $data = [
				'storeInfo'	=> Store::get(1),
				 "appStyle" => Config::getValueByName('appstyle'),
				 "appConfig" => Config::getValueByName('xylease'),
				 "shareConfig" => Config::getValueByName('share'),
				 "leaseConfig"	=> Config::getValueByName('lease'),
				 "tabBarList"  => Config::getValueByName('tabbar'),
			 ];
			 cache($cacheKey, $data, 300);
		 }
 
		 $this->success('init',$data);
	}

	/**
	 * 加载模板页面数据
	 */
	public function template($type = 'index'){

		// 首页
		$page = model('app\api\model\xylease\Page')
			->where(['type'=>$type,'is_use'=>1])
			->field('page, item')
			->find();			
		if(!$page){
			$this->error(__('模板尚未发布，请到后台【装修管理】中发布模板'));
		}
		
        $this->success('首页数据', $page);
	}

}
