import store from '@/store'
import $api from './request'
import xyfun from './xyfun'

export default {
	onLoad() {
		
		
		var options = this.$Route.query;
		
		// #ifdef MP
		if (options?.scene) {
			options = xyfun.sceneDecode(options.scene);
		}
		// #endif
		
		//console.log('分享optinos1：',options);
		
		if (options?.spm) {
			var spmArr = (options.spm).split('.');
			if (spmArr[0] != '0') {
				if (store.state.user.isLogin) {
					$api.post({
						url: '/share/add',
						data: {spm: options.spm},
						success: res => {
							console.log(res);
						},
						fail: res =>{
							console.log(res);
						}
					});
				} else {
					uni.setStorageSync('xylease:spm', options.spm);
				}
			}
			
		}
	},
	
	
	// #ifdef MP-WEIXIN
	onShareAppMessage() {
		
		var shareInfo = store.state.common.shareConfig;
		
		var imageUrl = xyfun.image(shareInfo.image);
		
		console.log(imageUrl);
		
		return {
			title: shareInfo.title,
			path: shareInfo.path,
			imageUrl: imageUrl,
			success(res) {
				console.log(res);
			},
			fail(res) {
				console.log(res);
			}
		}
		
	},
	
	// #endif
}
