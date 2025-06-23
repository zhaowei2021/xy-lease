/**
 * 
 * 系统初始化通用配置
 * @author 湖南行云网络科技有限公司
 * 
 **/  
  
import api from '../../utils/request'; 

export default {
	namespaced: true,
	state: {
		appStyle: {}, // 通用样式
		appConfig: {}, // 通用配置
		storeInfo: {}, // 门店信息
		shareConfig: {
			title:'',
			path:'/pages/index',
			image:'',
			desc:''
		}, // 分享配置
		leaseConfig:{},	// 租赁配置
		sysShareConfig:{},// 系统设置分享信息
		pageData: {}, // 自定义首页数据
		tabBarList:{}, // 自定义底部导航
		css:{},//通用样式
	},
	mutations: {
		setShareConfig(state, shareInfo) {
			state.shareConfig = shareInfo;
		}
	},
	actions: {
		async update({}) {
			// #ifdef MP
			const mp = uni.getUpdateManager();
			mp.onCheckForUpdate((res) => {
				console.log(res)
			});
			mp.onUpdateReady((res) => {
				console.log(res)
				uni.showModal({
					title: '更新提示',
					content: '新版本已经准备好，是否重启应用？',
					success(show) {
						if (show.confirm) {
							mp.applyUpdate();
						}
					}
				});
			});
			mp.onUpdateFailed((res) => {
				console.log(res)
			});
			// #endif
		},
		async checkToken({rootState}) {
			return new Promise((resolve, reject) => {
				api.post({
					url: '/token/check',
					success: res => {
						if (res.token) {
							rootState.user.isLogin = true;
							rootState.user.info = uni.getStorageSync('xylease:user');
							
							//同步购物车信息
							rootState.cart.cartList = uni.getStorageSync('xylease:cartList');
							rootState.cart.cartNum = uni.getStorageSync('xylease:cartNum');
						}else{
							rootState.user.isLogin = false;
							rootState.user.info = null;
							uni.removeStorageSync('xylease:user');
						}
					
						resolve(res);
					},
					fail: res => {
						reject(res)
					}
				});
			})
		},
		async init({state}){
			
			return new Promise((resolve, reject) => {
				api.get({
					url: '/common/init',
					success: res => {
						state.appConfig = res.appConfig;
						state.appStyle = res.appStyle;
						state.storeInfo = res.storeInfo;
						state.pageData = res.pageData;
						state.sysShareConfig = res.shareConfig;
						state.tabBarList = res.tabBarList;
						state.leaseConfig = res.leaseConfig;
						resolve(res);
					},
					fail: res => {
						reject(res)
					}
				});
			})
			
		},
		async share({commit}, shareInfo) {
			commit('setShareConfig', shareInfo);
		},
	}
};