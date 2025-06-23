// 路由
import { RouterMount,createRouter } from './uni-simple-router.js'
import store from '@/store'
import share from '@/utils/share'
import xyfun from '@/utils/xyfun'
const router = createRouter({
	platform: process.env.VUE_APP_PLATFORM,
	applet: {
		animationDuration: 0 //默认 300ms 
	},
	routes: [
		...ROUTES,
	]
});


var appOnLaunch = 0;
//全局路由前置守卫
router.beforeEach((to, from, next) => {
	appOnLaunch ++;
	
	if(appOnLaunch == 1){
		// 检测版本更新
		store.dispatch('common/update')
		
		// 加载通用数据,检测token
		Promise.all([
			store.dispatch('common/init'),
			store.dispatch('common/checkToken')
		]).then((res)=>{
			//console.log('加载完成：',res);
			//设置通用分享信息
			share.setShareInfo();
			
			// 权限控制登录
			if (to.meta && to.meta.auth && !store.state.user.isLogin) {
				next({
					name:'login',
					NAVTYPE:'push'
				});
			}else {
				next()
			}
		});
	}else{
		// 权限控制登录
		if (to.meta && to.meta.auth && !store.state.user.isLogin) {
			next({
				name:'login',
				NAVTYPE:'push'
			});
		}else {
			next()
		}
	}
	
	
});

export {
	router,
	RouterMount
}
