

// #ifdef H5
var jweixin = require("jweixin-module");
// #endif
import $platform from './platform';
import $api from './request';
export default {
	//判断是否在微信中    
	isWechat: function() {
		var ua = window.navigator.userAgent.toLowerCase();
		if (ua.match(/micromessenger/i) == 'micromessenger') {
			return true;
		} else {
			return false;
		}
	},
	
	// 初始化鉴权
	initJssdk(callback) {
		$api.post({
			url: '/wechat/jssdk',
			data: {
				uri: encodeURIComponent($platform.entry())
			},
			success: res => {
				jweixin.config(res);
				jweixin.ready(function() {
					if (callback) {
						callback();
					}
				})
			}
		});
	},
	
	//微信支付
	wxpay(data, callback) {
		if(this.isWechat()){
			this.initJssdk(function(res) {
				jweixin.ready(function() {
					WeixinJSBridge.invoke('getBrandWCPayRequest',{
						"appId": data.appId,
						"timeStamp": data.timeStamp,
						"nonceStr": data.nonceStr,
						"package": data.package,
						"signType": data.signType,
						"paySign": data.paySign,
					},function(res) {
						if (res.err_msg == "get_brand_wcpay_request:ok") {
							callback(true);
						} else {
							callback(false);
						}
					});
					
				});
			});
		}
	}
	
}
