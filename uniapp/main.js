/**
 * 
 * XYlease租赁
 * 
 **/

import App from './App';
import Vue from 'vue';
import {router,RouterMount} from './router/index.js'  //路由
Vue.use(router)

import store from './store';
import xyfun from "./utils/xyfun";
import api from './utils/request';

// #ifdef H5
import wxsdk from './utils/wxsdk';
// #endif

import mixin from "./utils/mixin"
Vue.mixin(mixin)

Vue.prototype.$xyfun = xyfun;  // 通用方法
Vue.prototype.$api = api;      // api请求
Vue.prototype.$store = store;  // store
// #ifdef H5
Vue.prototype.$wxsdk = wxsdk;
// #endif
App.mpType = 'app'

const app = new Vue({
	store,
    ...App
})

//v1.3.5起 H5端 你应该去除原有的app.$mount();使用路由自带的渲染方式
// #ifdef H5
	RouterMount(app,router,'#app')
// #endif
 
// #ifndef H5
	app.$mount(); //为了兼容小程序及app端必须这样写才有效果
// #endif