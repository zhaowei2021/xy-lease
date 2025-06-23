import store from '@/store'
import $platform from './platform'
export default {

	setShareInfo(scene = {
		title: '', // 自定义分享标题
		image: '', // 自定义分享图片
		params: {} // 自定义分享参数
	}) {
		

		var sysShareConfig = store.state.common.sysShareConfig;
		if (sysShareConfig.title === '' || sysShareConfig.image === '') {
			throw '请在系统配置分享配置中设置分享信息'
		}

		// 设置自定义分享信息
		var shareConfig = {};
		shareConfig.title = scene.title !== '' ? scene.title : sysShareConfig.title;
		shareConfig.image = scene.image !== '' ? scene.image : sysShareConfig.image;
		
		// 分享用户参数
		var spm = this.setSpm(scene.params);
		shareConfig.path = this.getPagePath(scene.params,spm);
		shareConfig.spm = spm;
		
		store.dispatch('common/share', shareConfig);
		
		return shareConfig;
	},

	// 分享参数拼接
	setSpm(params) {
		
		var shareUserId = '0'; 
		if (params.shareId === undefined) {
			if (store.state.user.isLogin) {
				shareUserId = store.state.user.info.id;
			}
		}
		var page = '1'; // 页面类型: 1=首页(默认),2=商品详情页
		if (params.page !== undefined) {
			page = params.page;
		}
		var pageId = '0'; // 设置页面ID: 如商品ID
		if (params.pageId !== undefined) {
			pageId = params.pageId;
		}
		var platform = ['wxMiniProgram'].indexOf($platform.get()) + 1; // 设置分享的平台渠道: 1=微信小程序
		
		return `${shareUserId}.${page}.${pageId}.${platform}`;
	},
	
	getPagePath(params,spm){
		var path = '/pages/index';
		
		switch(params.page) {
			case 2: //商品详情
				path = '/pages/goods/detail';
				break
		}
		path += '?spm='+spm;
		if (params.pageId !== undefined){
			path += '&id='+params.pageId;
		}
		return path;
	}
}
