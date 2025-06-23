/**
 * 
 * 通用方法
 * @author 湖南行云网络科技有限公司
 * 
 **/
 
import http_config, { image_url } from '../config/http';
import store from '../store';
class xyfun {
	
	/**
	 * 获取http配置信息
	 */
	http_config(name){
		return http_config[name];
	}
	
	/**
	 * 页面返回
	 */
	back(tips = '',num = 1){
		if(tips != ''){
		this.msg(tips);
			setTimeout(()=>{
				uni.navigateBack({
					delta: num
				});
			},3000)
		}else{
			uni.navigateBack({
				delta: num
			});
		}
		
	}
	
	/**
	 * 图片处理
	 */
	image(url){
		if(!(/^(http|https):\/\/.+/.test(url))){
			url = http_config.image_url + url;
		}
		return url;
	}
	
	
	//scene解码
	sceneDecode(e) {
		var scene = decodeURIComponent(e),params = scene.split(','),data = {};
		for (var i in params) {
		   var val = params[i].split(':');
		   val.length > 0 && val[0] && (data[val[0]] = val[1] || null)
		}
		return data;
	}
	
	/**
	 * 提示信息
	 */
	msg(title, duration = 1500, mask = false, icon = 'none'){
		if (Boolean(title) === false) {
			return;
		}
		uni.showToast({title,duration,mask,icon});
	}
	
	/**
	 * 时间格式化
	 */
	timeFormat(timestamp = null, fmt = 'yyyy-mm-dd hh:MM:ss'){
		// 其他更多是格式化有如下:
		// yyyy:mm:dd|yyyy:mm|yyyy年mm月dd日|yyyy年mm月dd日 hh时MM分等,可自定义组合
		timestamp = parseInt(timestamp);
		// 如果为null,则格式化当前时间
		if (!timestamp) timestamp = Number(new Date());
		// 判断用户输入的时间戳是秒还是毫秒,一般前端js获取的时间戳是毫秒(13位),后端传过来的为秒(10位)
		if (timestamp.toString().length == 10) timestamp *= 1000;
		let date = new Date(timestamp);
		let ret;
		let opt = {
			"y+": date.getFullYear().toString(), // 年
			"m+": (date.getMonth() + 1).toString(), // 月
			"d+": date.getDate().toString(), // 日
			"h+": date.getHours().toString(), // 时
			"M+": date.getMinutes().toString(), // 分
			"s+": date.getSeconds().toString() // 秒
		};
		for (let k in opt) {
			ret = new RegExp("(" + k + ")").exec(fmt);
			if (ret) {
				fmt = fmt.replace(ret[1], (ret[1].length == 1) ? (opt[k]) : (opt[k].padStart(ret[1].length, "0")))
			};
		};
		return fmt;
	}
	
	/**
	 * 将时间戳转化为时分秒的格式，用作倒计时
	 */
	intervalTime(endTime) {
		// console.log(222)
		// var timestamp=new Date().getTime(); //计算当前时间戳
		var date1 = (Date.parse(new Date()))/1000;//计算当前时间戳 (毫秒级)
		var date2 = endTime; //结束时间
		// var date3 = date2.getTime() - date1.getTime(); //时间差的毫秒数
		var date3 =  (date2- date1)*1000; //时间差的毫秒数
		//计算出相差天数
		var days = Math.floor(date3 / (24 * 3600 * 1000));
		//计算出小时数
	 
		var leave1 = date3 % (24 * 3600 * 1000); //计算天数后剩余的毫秒数
		var hours = Math.floor(leave1 / (3600 * 1000));
		//计算相差分钟数
		var leave2 = leave1 % (3600 * 1000); //计算小时数后剩余的毫秒数
		var minutes = Math.floor(leave2 / (60 * 1000));
	 
		//计算相差秒数
	 
		var leave3 = leave2 % (60 * 1000); //计算分钟数后剩余的毫秒数
		var seconds = Math.round(leave3 / 1000);
	    // console.log(days + "天 " + hours + "小时 ")
		var sun = days*24 + hours;
		var min = minutes;
		var sec = seconds;
		if(sun == 0){
			sun = '00';
		}else if(sun < 10){
			sun = '0'+sun;
		}
		if(min < 10){
			min = '0'+minutes;
		}
		if(sec < 10){
			sec = '0'+seconds;
		}
		
		if((days*24 + hours)<= 0 && minutes <= 0 && seconds <= 0){
			return '00:00:00'
		}else{
			return   (sun) + ":" + min + ":" + sec;
		}
		
		// return   days + "天 " + hours + "小时 "
		
	}
	 
	/**
	 * 字符串时间转时间戳
	 */
	strtotime(dateString){
		return Date.parse(dateString)/1000;
	}
	 
	getDate(){
		var dd = new Date();
		var y = dd.getFullYear();
		var m = dd.getMonth() + 1;
		if( m <10){
				  m = '0'+m;
		}
		var d = dd.getDate();
		if( d <10){
				  d = '0'+d;
		}
		return y + "-" + m + "-" + d;
	}
	
	dateAdd(addDate = 7,dateStr = '') {
	  var dd = new Date(dateStr);
	  dd.setDate(dd.getDate() + addDate);
	  var y = dd.getFullYear();
	  var m = dd.getMonth() + 1;//获取当前月份的日期
	  if( m <10){
		  m = '0'+m;
	  }
	  var d = dd.getDate();
	  if( d <10){
		  d = '0'+d;
	  }
	  return y + "-" + m + "-" + d;
	}
	
	/**
	 * 获取当前页面栈
	 */
	prePage(){
		let pages = getCurrentPages();
		let prePage = pages[pages.length - 2];
		
		console.log(pages,prePage);
		
		// #ifdef H5
		return prePage;
		// #endif
		return prePage.$vm;
	}
	
	
	/**
	 * 跳转登录页面
	 */
	toLogin(){
		this.to('/pages/user/login');
	}
	
	/**
	 * 页面跳转
	 */
	to(url, isTab = false, animationType = 'pop-in', animationDuration = 300){
		
		if(isTab == false && this.isTabbar(url)){
			isTab = true;
		}
		
		if(isTab){
			uni.switchTab({
				url:url
			})
		}else{
			uni.navigateTo({
				url,
				animationType,
				animationDuration
			})
		}
	}
	
	/**
	 * 是否Tabbar 页面
	 */
	isTabbar(url){
		var tablist = [
			'/pages/index',
			'/pages/cart',
			'/pages/category',
			'/pages/user',
		];
		
		return tablist.indexOf(url) != -1
	}
	
	/**
	 * 系统屏幕参数
	 */
	xysys() {
		var sys = uni.getSystemInfoSync();
		var data = {
			top: sys.statusBarHeight,
			height: sys.statusBarHeight + uni.upx2px(80),
			screenHeight: sys.screenHeight,
			platform: sys.platform,
			model: sys.model,
			windowHeight: sys.windowHeight,
			windowBottom: sys.windowBottom,
			clientHeight: sys.windowHeight-uni.upx2px(80)-sys.statusBarHeight,
			deviceId: sys.deviceId,
			safeAreaInsets:sys.safeAreaInsets
		};
		return data;
	}
	
	/**
	 * 设置导航栏
	 */
	setNavBar(text = '',backgroundColor = '',frontColor = ''){
		
		if(backgroundColor == ''){
			backgroundColor = store.state.common.appStyle.navBarFrontColor;
		}
		
		if(frontColor == ''){
			backgroundColor = store.state.common.appStyle.frontColor;
		}
		
		if (backgroundColor && frontColor) {
			uni.setNavigationBarColor({
				frontColor: frontColor == '#ffffff' ? frontColor : '#000000',  
				backgroundColor: backgroundColor,
			});
		}
		
		if (text) {
			uni.setNavigationBarTitle({
				title: text
			});
		}
	}
	
	// rpx转px
	rpxToPx(rpx) {
		const screenWidth = uni.getSystemInfoSync().screenWidth
		return (screenWidth * Number.parseInt(rpx)) / 750
	}
	 
	// px转rpx
	pxToRpx(px) {
		const screenWidth = uni.getSystemInfoSync().screenWidth
		return (750 * Number.parseInt(px)) / screenWidth
	}
	
	/**
	 * 分享配置
	 */
	
	shareConfig(){
		return store.state.common.shareConfig;
	}
	
	/**
	 * 通用配置
	 */
	config(){
		return store.state.common.appConfig;
	}
	
	/**
	 * tabbar 数据
	 */
	tabBarData(){
		return store.state.common.tabBarList;
	}
	
	/**
	 * 租赁配置
	 */
	lease(){
		return store.state.common.leaseConfig;
	}
	
	/**
	 * 通用样式
	 */
	css(page =''){
		
		var common = store.state.common;
		
		//页面背景颜色
		var pageBgColor = (page != '') ? common.pageData[page].page.style.pageBackgroundColor : common.appStyle.pageBgColor;
		//页面背景图片
		var pageBgImage = (page != '' && common.pageData[page].page.style.pageBackgroundImage != undefined) ? this.image(common.pageData[page].page.style.pageBackgroundImage) : '';
		//文字主色调
		var textMainColor = common.appStyle.textMainColor;
		//模块背景颜色
		var pageModuleBgColor = common.appStyle.pageModuleBgColor;
		//文字浅色调
		var textLightColor = common.appStyle.textLightColor;
		//主色调
		var mainColor = common.appStyle.mainColor;
		//价格色调
		var priceColor = common.appStyle.textPriceColor;
		//导航栏背景演示
		var navBarBgColor = common.appStyle.navBarBgColor;
		
		return {
			style:common.appStyle,
			page:"background-color: "+pageBgColor+";color:"+textMainColor+";"+(pageBgImage? "background-image: url('"+pageBgImage+"');background-repeat: repeat;background-size: 100% auto;":""),//页面通用
			mbg:"background-color: "+pageModuleBgColor+";",//模块背景
			pbg:"background-color: "+pageBgColor+";",//页面背景
			prbg:"background-color: "+priceColor+";",//价格色调背景
			tcm:"color:"+textMainColor+";",//文字主色调
			tcl:"color:"+textLightColor+";",//文字浅色调
			mcbg:"background-color: "+mainColor+";",//主色调背景
			tcmc:"color:"+mainColor+";",//文字颜色用主色调
			tcp:"color:"+priceColor+";",//价格文字颜色
			tcmbg:"color:"+pageModuleBgColor+";",//文字颜色用模块背景色
			bl:"border-color:"+textLightColor +";",//文字浅色调边框
			blc:"border-color:"+mainColor +";",//主色调边框
			bltc:"border-color:"+textMainColor +";",//文字主色调边框
			blp:"border-color:"+priceColor +";",//价格色调边框
			blpc:"border-color:"+pageBgColor +";",//页面背景色调边框
			blmc:"border-color:"+pageModuleBgColor +";",//模块背景色调边框
			nbg:"background-color: "+navBarBgColor+";",//导航栏颜色背景
		}
		
	}
	
	/**
	 * 装修页面样式
	 */
	tcss(page){
		
		var common = store.state.common;
		//页面背景颜色
		var pageBgColor = page.style.pageBackgroundColor;
		//页面背景图片
		var pageBgImage = (page.style.pageBackgroundImage != undefined) ? this.image(page.style.pageBackgroundImage) : '';
		//文字主色调
		var textMainColor = common.appStyle.textMainColor;
		
		return "background-color: "+pageBgColor+";color:"+textMainColor+";"+(pageBgImage? "background-image: url('"+pageBgImage+"');background-repeat: repeat;background-size: 100% auto;":"");
		
	}
	
	
	/**
	 * 复制
	 */
	copy(text){
		uni.setClipboardData({
			data:text
		})
	}
	
	/**
	 * 字符串包含
	 */
	strSearch(str1,str2){
		return str1.indexOf(str2) != -1;
	}
	
	/**
	 * 拨打电话
	 */
	phone(num){
		uni.makePhoneCall({
			phoneNumber:num,
		})
	}
	
	/**
	 * 获取用户定位经纬度
	 */
	loc() {
		return new Promise((resolve, reject) => {
			var that = this;
			uni.getStorage({
				key: 'user:loc',
				success: function(res) {
					resolve(res.data);
				},
				fail: function() {
					uni.getLocation({
						type: 'wgs84',
						success: function(res) {
							console.log('定位经纬度：',res);
							uni.setStorageSync('user:loc',res);
							resolve(res);
						},
						fail: function(res) {
							reject(res);
						}
					});
				}
			});
		})
	}
	
	/**
	 * 打开设置
	 */
	oset(){
		return new Promise((resolve, reject) => {
			uni.openSetting({
				success: (res) => {
					if (res.authSetting['scope.userLocation']) {
						resolve(res);
					} else {
						reject(res)
					}
				},
				fail: (err) => {
					reject(err)
				}
			})
		})
	}
		
	//距离格式化
	distance(num,isNum = false){
		
		if(isNum){
			return (num/1000).toFixed(1)+'km';
		}
		return '距离'+(num/1000).toFixed(1)+'km';
	}
	
	// 多张图片预览
	pi(index,photoList) { 
		uni.previewImage({
			current: index,
			urls: photoList,
			loop:true,
		})
	}
	
	//经纬度计算距离
	algorithm (point1, point2) {
		
		console.log(point1);
		console.log(point2);
		
		//经纬度：113.08514 , 27.83180 
		
		let [x1, y1] = point1;
		let [x2, y2] = point2;
	    let Lat1 = this.rad(x1); // 纬度
	    let Lat2 = this.rad(x2);
	    let a = Lat1 - Lat2;//	两点纬度之差
	    let b = this.rad(y1) - this.rad(y2); //	经度之差
	    let s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) 
	        	+ Math.cos(Lat1) * Math.cos(Lat2) * Math.pow(Math.sin(b / 2), 2)));
	        //	计算两点距离的公式
	    s = s * 6378137.0;//	弧长等于弧度乘地球半径（半径为米）
	    s = Math.round(s * 10000) / 10000;//	精确距离的数值
	    return s;
	
	}
	
	//角度转换成弧度
	rad(d){
	    return d * Math.PI / 180.00;
	};
	
	//价格简化显示
	pe(price){
		return price.replace(".00","")
	}
	
	//价格完整显示
	pea(x) {
	  var f = parseFloat(x);
	  if (isNaN(f)) {
		return false;
	  }
	  var f = Math.round(x * 100) / 100;
	  var s = f.toString();
	  var rs = s.indexOf(".");
	  if (rs < 0) {
		rs = s.length;
		s += ".";
	  }
	  while (s.length <= rs + 2) {
		s += "0";
	  }
	  return s;
	}
	
	//导航
	openLoc(latitude,longitude,name,address){
		uni.openLocation({
			latitude:parseFloat(latitude),
			longitude:parseFloat(longitude),
			name:name,
			address:address
		})
	}
	
	
	/**
	 * 加法精度计算
	 */
	bcadd(a, b){
	    var c, d, e;
	    try {
	        c = a.toString().split(".")[1].length
	    } catch(f) {
	        c = 0
	    }
	    try {
	        d = b.toString().split(".")[1].length
	    } catch(f) {
	        d = 0
	    }
	    return e = Math.pow(10, Math.max(c, d)),(this.bcmul(a, e) + this.bcmul(b, e)) / e
	}
	
	/**
	 * 减法精度计算
	 */
	bcsub(a, b){
	    var c, d, e;
	    try {
	        c = a.toString().split(".")[1].length
	    } catch(f) {
	        c = 0
	    }
	    try {
	        d = b.toString().split(".")[1].length
	    } catch(f) {
	        d = 0
	    }
	    return e = Math.pow(10, Math.max(c, d)),(this.bcmul(a, e) - this.bcmul(b, e)) / e
	}
	
	/**
	 * 乘法精度计算
	 */
	bcmul(a, b){
	    var c = 0,
	    d = a.toString(),
	    e = b.toString();
	    try {
	        c += d.split(".")[1].length
	    } catch(f) {}
	    try {
	        c += e.split(".")[1].length
	    } catch(f) {}
	    return Number(d.replace(".", "")) * Number(e.replace(".", "")) / Math.pow(10, c)
	}
	
	/**
	 * 除法精度计算
	 */
	bcdiv(a, b){
	    var c, d, e = 0,
	    f = 0;
	    try {
	        e = a.toString().split(".")[1].length
	    } catch(g) {}
	    try {
	        f = b.toString().split(".")[1].length
	    } catch(g) {}
	    return c = Number(a.toString().replace(".", "")),d = Number(b.toString().replace(".", "")),this.bcmul(c / d, Math.pow(10, f - e))
	}
	
}
export default new xyfun();
