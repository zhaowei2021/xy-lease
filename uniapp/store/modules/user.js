/**
 * 
 * xylease 用户管理
 * @author 湖南行云网络科技有限公司
 * 
 **/
  
import api from '../../utils/request';  
import share from '@/utils/share';
export default {
	namespaced: true,
	state: {
		isLogin: false, // 登录状态
		info: null, // 用户信息
		coach: null, // 老师信息
		distribution: null, //分销信息
	},
	mutations: {
		setUserInfo(state, data) {
			state.info = data.userInfo;
			uni.setStorageSync("xylease:user", data.userInfo);
		},
		setDistributionInfo(state, data) {
			state.distribution = data;
		},
	},
	actions: {
		// 登录
		async login({state,commit}, data) {
			
			state.isLogin = true;
			commit('setUserInfo', data);
			
			// 设置分享信息
			share.setShareInfo();
			
			// 存在分享信息，添加分享记录
			var spm = uni.getStorageSync('xylease:spm');
			
			if (spm) {
				api.post({
					url: '/share/add',
					data: {spm: spm},
					success: res => {
						uni.removeStorageSync('xylease:spm');
					},
					fail: res =>{
						console.log(res);
					}
				});
			}
		},
		
		async info({commit}, data) {
			commit('setUserInfo', data);
		},
		
		
		// 退出登录
		async logout({state}) {
			state.isLogin = false;
			state.info = null;
			
			// 设置分享信息
			share.setShareInfo();
			uni.removeStorageSync('xylease:user');
		},
		
		// 获取用户信息
		async getInfo({commit}) {
			return new Promise((resolve, reject) => {
				api.post({
					url: '/user/user/refresh',
					success: res => {
						commit('setUserInfo', res);
						resolve(res)
					},
					fail: res => {
						reject(res)
					},
				});
			})
		},
		
		// 获取分销商信息
		async getDistributionInfo({commit}) {
			return new Promise((resolve, reject) => {
				api.post({
					url: '/distribution/center/info',
					success: res => {
						commit('setDistributionInfo', res.data);
						resolve(res)
					},
					fail: res => {
						reject(res)
					},
				});
			})
		},
		
	}
};