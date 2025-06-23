/**
 * 
 * 状态管理器
 * @author 湖南行云网络科技有限公司
 * 
 **/
   
import Vue from 'vue';
import Vuex	from 'vuex';
import common from './modules/common';
import user	from './modules/user';
import cart	from './modules/cart';

Vue.use(Vuex);

const store = new Vuex.Store({
	modules: {
		common, // 系统通用
		user, // 用户中心
		cart, // 购物车
	}
});

export default store;
