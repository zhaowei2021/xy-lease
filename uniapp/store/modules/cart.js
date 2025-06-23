/**
 * 
 * 购物车
 * @author 湖南行云网络科技有限公司
 * 
 **/  
  
import api from '../../utils/request';  
import xyfun from '../../utils/xyfun';
export default {
	namespaced: true,
	state: {
		cartList: [], //购物车列表
		cartNum: 0, //购物车数量
		allSum: 0, //总计价格
		allSelected: true, //全选选中状态
		operate: false ,//管理购物车状态
	},
	
	mutations: {
		setCartList(state, data) {
			state.cartList = data;
			state.cartNum = data.length;
			uni.setStorageSync("xylease:cartList", data);
			uni.setStorageSync("xylease:cartNum", data.length);
		},
		setAllSum(state){
			var allSum = 0;
			state.cartList.map(item => {
				if(item.checked){
					if(item.goodstype == 'sell' || item.goodstype == 'service'){
						allSum += xyfun.bcmul(item.buynum,(item.sku_price ? item.sku_price.price : 0));
					}else{
						var tempPrice = item.sku_price.hourprice;
						if(xyfun.lease().defaultlease == 'days'){
							tempPrice = item.sku_price.daysprice;
						}
						if(xyfun.lease().defaultlease == 'night'){
							tempPrice = item.sku_price.nightprice;
						}
						allSum += xyfun.bcmul(item.buynum,tempPrice);
					}
					
				}
			})
			state.allSum = xyfun.pea(allSum);
		}
	},
	actions: {
		
		// 修改|删除
		change({
			dispatch
		}, data) {
			return new Promise((resolve, reject) => {
				
				api.post({
					url: '/cart/edit',
					data:data,
					success: res => {
						if (data.act == 'del') {
							dispatch('getList');
						}
						resolve(res)
					},
					fail: res =>{
						console.log(res);
						reject(res);
					}
				});
				
			})
		},
		
		// 是否选择了商品
		async isSel({state}){
			
			return new Promise((resolve) => {
				var isSel = false;
				state.cartList.map(item => {
					if (item.checked) {
						isSel = true
					}
				})
				resolve(isSel)
			})
		},
		
		// 删除
		del({state,dispatch}){
			var ids = [];
			state.cartList.map(item => {
				if(item.checked){
					ids.push(item.id);
				}
			})
			dispatch('change',{act:'del',ids:ids});
		},
		
		// 管理购物车状态
		async operate({state}) {
			state.operate = !state.operate //取反改变状态
		},
		
		// 全选
		async onAll({state,commit}) {
			state.allSelected = !state.allSelected //取反改变状态
			state.cartList.map(item => {
				item.checked = state.allSelected;
			})
			
			commit('setAllSum');
		},
		
		//单选
		async onOne({state, commit},cartItem) {
			var all = true;
			state.cartList.map(item => {
				if(item.id == cartItem.id){
					item.checked = !item.checked;
				}
				
				if(!item.checked){
					all = false;
				}
			})
			state.allSelected = all;
			commit('setAllSum');
		},
		
		//增加
		async add({state, commit},cartItem) {
			state.cartList.map(item => {
				if(item.id == cartItem.id){
					item.buynum ++;
				}
			})
			commit('setAllSum');
		},
		
		//减少
		async reduce({state, commit},cartItem) {
			state.cartList.map(item => {
				if(item.id == cartItem.id){
					if(item.buynum >1){
						item.buynum --;
					}else{
						xyfun.msg('数量不能小于1');
					}
					
				}
			})
			commit('setAllSum');
		},
		
		// 商品列表
		getList({
			commit
		}) {
			return new Promise((resolve, reject) => {
				api.post({
					url: '/cart/lists',
					success: res => {
						res.length && res.map(item => {
							item.checked = true;
						})
						commit('setCartList', res);
						commit('setAllSum',res);
						resolve(res)
					},
					fail: res =>{
						reject(res);
					}
				});
			})
		},
		
		// 添加商品
		async addGoods({ dispatch }, data) {
			return new Promise((resolve, reject) => {
				api.post({
					url: '/cart/add',
					data: data.list,
					success: res => {
						dispatch('getList');
						resolve(res)
					},
					fail: res =>{
						console.log(res);
						reject(res);
					}
				});
				
			})
		},
	}
};