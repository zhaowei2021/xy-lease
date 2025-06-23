

import wxsdk from './wxsdk'
export default {

	// 获取当前运行平台
	get() {
		let platform = '';
	
		// #ifdef H5
		wxsdk.isWechat() ? (platform = 'wxOfficialAccount') : (platform = 'H5');
		// #endif
		
		// #ifdef MP-WEIXIN
		platform = 'wxMiniProgram';
		// #endif
		
		if (platform !== '') {
			uni.setStorageSync('platform', platform);
		} else {
			uni.showToast({
				title: '暂不支持该平台',
				icon: 'none'
			});
		}
		return platform;
	},
	
	// wechat jssdk 签名网址
	entry() {
		var entryUrl = location.href;
		if (this.device() === 'ios') {
			if (typeof(location.entryUrl) !== 'undefined') {
				entryUrl = location.entryUrl;
			} else {
				location.entryUrl = entryUrl;
			}
		}
		return entryUrl;
	},
	
	// 运行机型
	device() {
		return uni.getSystemInfoSync().platform;
	},

}
