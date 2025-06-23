import $platform from './platform';
import $api from './request';
import {router as $Router} from '@/router/index';
// #ifdef H5
import wxsdk from './wxsdk'
// #endif

/**
 * 支付
 */
export default class xyleasePay {

	constructor(payment, order, orderType) {
		this.payment = payment;
		this.order = order;
		this.orderType = orderType;
		this.platform = $platform.get();
		this.payMehod = this.getPayMethod();						
	}

	getPayMethod() {
		var payMethod = {
			'wxMiniProgram': {
				'wechat': async () => {
					return new Promise((resolve, reject) => {
						resolve(this.wxMiniProgramPay());
					})
					
				},
				'balance': async() => {
					return new Promise((resolve, reject) => {
						resolve(this.balancePay());
					})
				}
			},
			'wxOfficialAccount': {
				'wechat': async () => {
					return new Promise((resolve, reject) => {
						resolve(this.wxOfficialAccountPay());
					})
					
				},
				'balance': async() => {
					return new Promise((resolve, reject) => {
						resolve(this.balancePay());
					})
				}
			},
		}
		return payMethod[this.platform][this.payment];
	}
	
	// 预支付
	prepay() {
		var that = this;
		return new Promise((resolve, reject) => {
			var params = {
				ordersn: that.order.ordersn,
				payment: that.payment,
				ordertype: that.orderType,
			}
			$api.post({
				url: '/pay/prepay',
				loadingTip:'支付中...',
				data: params,
				success: res => {
					resolve(res);
				},
				fail(res){
					reject(res);
				}
			});
		});
	}
	
	// 余额支付
	async balancePay() {
		var that = this;
		return new Promise((resolve, reject) => {
			that.prepay().then((res)=>{
				resolve('success')
			},(res)=>{
				reject('fail'); 
			});
		});
	}
	
	// 微信小程序支付
	async wxMiniProgramPay() {
		var result = await this.prepay();
		return new Promise((resolve, reject) => {
			uni.requestPayment({
				provider: 'wxpay',
				...result.pay_data,
				success: () => {
					resolve('success')
				},
				fail: () => {
					reject('fail'); 
				}
			});
		});
	}
	
	// 微信公众号支付
	async wxOfficialAccountPay() {
		var result = await this.prepay();
		return new Promise((resolve, reject) => {
			wxsdk.wxpay(result.pay_data, (res) => {
				res ? resolve('success') : reject('fail'); 
			});
		});
	}

	// 支付结果跳转
	payResult(resultType) {
		$Router.replace({
			path: '/pages/user/pay/result',
			query: {
				orderId: this.order.id,
				payment: this.payment,
				payState: resultType,
				orderType: this.orderType
			}
		});
	}

}
