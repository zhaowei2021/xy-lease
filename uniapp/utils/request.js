 'use strict';
 
 import http_config from '../config/http';
 import platform from './platform';
 import wxsdk from './wxsdk';
 
 class Request {

 	/**
	 * 网络请求的默认配置
	 */
 	config = {
 		baseUrl: http_config.api_url,
		debug:http_config.debug,
 		business: 'data',
 	}

	/**
	 * 判断url是否为绝对路径
	 * @param {Object} url
	 */
 	static posUrl(url) {
 		return /(http|https):\/\/([\w.]+\/?)\S*/.test(url)
 	}

 	static getUrl(config) {
 		var url = config.url || ''
 		var abs = Request.posUrl(url);
 		return abs ? url : (config.baseUrl + url)
 	}

 	static getContentType(config) {
 		var type = config.contentType || 'json'
 		var charset = config.encoding || 'UTF-8'
 		if (type === 'json') {
 			return 'application/json;charset=' + charset
 		} else if (type === 'form') {
 			return 'application/x-www-form-urlencoded;charset=' + charset
 		} else if (type === 'file') {
 			return 'multipart/form-data;charset=' + charset
 		} else if (type === 'text') {
 			return 'text/plain;charset=' + charset
 		} else if (type === 'html') {
 			return 'text/html;charset=' + charset
 		} else {
 			throw new Error('unsupported content type : ' + type)
 		}
 	}

 	/**
	 * 拦截器
	 */
 	interceptor = {
 		request: (config)=>{
			// 给header添加全局请求参数token
			if (!config.header.token || !config.header.xylease) {
				var xyleaseLogin = uni.getStorageSync("xylease:user");
				if (xyleaseLogin) {
					config.header.token = uni.getStorageSync("xylease:user").token;
				}
				// #ifdef H5
				if(wxsdk.isWechat()){
					config.header['App-Client'] = 'wechat-xylease';
				}else{
					config.header['App-Client'] = 'h5-xylease';
				}
				// #endif
				
				// #ifdef MP
				config.header['App-Client'] = 'mp-xylease';
				// #endif
				
				// 设置语言
				config.header['Accept-Language'] = 'zh-CN,zh;q=0.9';
			}
			// 添加一个自定义的参数，默认异常请求都弹出一个toast提示
			if (config.toastError === undefined) {
				config.toastError = true
			}
			return config;
		},
 		response: (res, config) => {
			if (res.code === 1) {
				res.success = true;
			}else if (res.code === 401) { // token失效，需要重新登录
				uni.navigateTo({
					url:'/pages/user/login'
				})
			}
			return res;
		},
 		fail: (res, config) => {
			
			var error = '';
			//业务错误、没有登录、没有权限
			if (res.statusCode === 200) {
				error = res.data.msg;
			} else if (res.statusCode === 401) {
				error = res.data.msg;
			} else if (res.statusCode === 403) {
				error = res.data.msg;
			} else if (res.statusCode === 404) {
				error = 'API接口不存在';
			} else if (res.statusCode === 500) {
				error = '服务器繁忙';
			} else {
				error = 'API接口异常';
			}
			
			uni.showToast({
				icon:'none',
				duration:5000,
				title:error
			});
		}
 	}


 	request(options = {}) {
 		var that = this;
 		if (options.data === undefined) {
 			options.data = {}
 		}
 		if (options.header === undefined) {
 			options.header = {}
 		}

 		var _options = Object.assign({}, this.config, options)
 		_options = Object.assign(options, _options)

 		_options.url = Request.getUrl(_options)
 		if (!_options.header['Content-Type']) {
 			_options.header['Content-Type'] = Request.getContentType(_options)
 		}
		
		if (!_options.header['platform']) {
			_options.header['platform'] = platform.get();
		}
		
 		var _config = _options
 		if (that.interceptor.request && typeof that.interceptor.request === 'function') {
 			_config = that.interceptor.request(_options)
 		}
 		var task = undefined
 		var promise = new Promise((resolve, reject) => {

 			var extras = {

 			}
 			that._prepare(that, _config, extras)

 			if (_config.contentType === 'file') {
 				task = uni.uploadFile({
 					..._config,
 					success: res => {
 						that._success(that, _config, res, resolve, reject)
 					},
 					fail: res => {
 						that._fail(that, _config, res, resolve, reject)
 					},
 					complete: (res) => {
 						that._complete(that, _config, res, extras)
 					}
 				})
 				if (_config.progress && typeof _config.progress === 'function') {
 					task.onProgressUpdate(_res => {
 						_config.progress(_res, task)
 					})
 				}
 			} else {
 				task = uni.request({
 					..._config,
					timeout: 6000,
 					success: res => {
 						that._success(that, _config, res, resolve, reject)
 					},
 					fail: res => {
 						that._fail(that, _config, res, resolve, reject)
 					},
 					complete: (res) => {
 						that._complete(that, _config, res, extras)
 					}
 				})
 			}
 		})
 		if (_config.success || _config.fail || _config.complete) {
 			return task;
 		}
 		return promise;
 	}
 	
 	get(options = {}) {
 		options.method = 'GET'
 		return this.request(options)
 	}

 	post(options = {}) {
 		options.method = 'POST'
 		return this.request(options)
 	}
 	
 	upload(options = {}) {
 		options.method = 'POST'
 		options.contentType = 'file'
 		return this.request(options)
 	}

 	_success = function(that, _config, res, resolve, reject) {
 		if (res.statusCode >= 200 && res.statusCode <= 401) { // http ok
 			var result = res.data // 全局的拦截器
 			var parseFileJson = _config.contentType === 'file' && typeof result === 'string' && (_config.dataType === undefined || _config.dataType === 'json')
 			if (parseFileJson) {
 				result = JSON.parse(res.data);
 			}
 			var skip = _config.skipInterceptorResponse
 			if (that.interceptor.response && typeof that.interceptor.response === 'function' && !skip) {
 				result = that.interceptor.response(result, _config)
 			}
 			if (skip || result.success) { // 接口调用业务成功
 				var _data = _config.business ? result[_config.business] : result;
 				if (_config.debug) {
 					console.log('请求成功: ', _config.url);
					console.log(result.msg, _data)
 				}
 				_config.success ? _config.success(_data) : resolve(_data)
 				return;
 			}
 		}
 		that._fail(that, _config, res, resolve, reject)
 	}

 	_fail = function(that, _config, res, resolve, reject) {
 		if (_config.debug) {
 			console.error('response failure: ', res)
 		}
 		if (res.errMsg === 'request:fail abort') {
 			return
 		}
 		var result = res
 		if (that.interceptor.fail && typeof that.interceptor.fail === 'function') {
 			result = that.interceptor.fail(res, _config)
 		}
		_config.fail ? _config.fail(result) : reject(result)
 	}

 	_prepare = function(that, _config, obj = {}) {
 		obj.startTime = Date.now()
 		if (_config.loadingTip) {
 			uni.showLoading({
 				title: _config.loadingTip
 			})
 		}
 		if (_config.contentType === 'file') {
 			if (_config.formData === undefined || _config.formData === null) {
 				_config.formData = _config.data
 				delete _config.data
 			}
 			delete _config.header['Content-Type']
 			delete _config.header['Referer']
 			_config.method = 'POST'
 		}
 		if (_config.debug) {
 			//console.log('request: ', _config)
 		}
 	}

 	_complete = function(that, _config, res, obj = {}) {
 		obj.endTime = Date.now()
 		if (_config.debug) {
 			console.log('请求用时 ' + (obj.endTime - obj.startTime) + ' 毫秒')
 		}
 		if (_config.loadingTip) {
 			var diff = obj.endTime - obj.startTime;
 			var duration = _config.loadingDuration || 500
 			if (diff < duration) {
 				diff = duration - diff
 			} else {
 				diff = 0
 			}
 			setTimeout(function() {
 				uni.hideLoading()
 			}, diff)
 		}
 	}

 }

 var request = new Request()
 
 export default request
