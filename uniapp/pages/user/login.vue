<template>
	<view class="login p-50" :style="css.cpage+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<view class="title ts-48 m-t-50" :style="css.tcm">{{isInfo?'完善信息':'欢迎登录'}}</view>
		<view class="logo tc">
			<image :src="$xyfun.image(common.storeInfo.logo)" />
		</view>
		
		<block v-if="isInfo">
			<view class="user-list lh-100 flex p-b-15 m-b-2 bc-w">
				<text class="list-name">头像</text>
				<view class="r m-l-auto">
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
						<image class="avatar" :src="avatar" v-if="avatar != ''" />
						<text class="xyicon icon-right m-l-15"></text>
					</button>
					<!-- #endif -->
				</view>
			</view>
			<view class="user-list lh-70 flex p-tb-15 m-b-2 bc-w">
				<text class="list-name">昵称</text>
				<view class="flex r m-l-auto">
					<input class="br-10" :style="css.pbg" type="nickname" placeholder="请输入昵称~" @blur="blurNickName" maxlength="50" v-model="nickname" />
				</view>
			</view>
			<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-t-50" @tap="profile()">确定</button>
			<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-t-50" @tap="$Router.back()">跳过</button>
		</block>
		
		<block v-if="!isLogin">
			
			<!-- #ifdef MP-WEIXIN -->
			<view class="member">
				<view class="m-t-30">
					<button v-if="isAgree" :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w" open-type="getPhoneNumber" @getphonenumber="decryptPhoneNumber">手机快捷登录</button>
					<button :style="css.mcbg" v-else class="ts-30 lh-30 p-25 tc-w" @tap="$xyfun.msg('请阅读并同意《隐私政策》')">手机快捷登录</button>
				</view>
				<view class="tips flex p-tb-30 tc m-t-30 lh-50" :style="css.tcl">
					<view class="agree flex " @tap="isAgree=!isAgree">
						<text class="xyicon icon-radio-a ts-30 flex tb" v-if="isAgree"></text>
						<text class="xyicon icon-radio ts-30 flex tb" v-else></text>
						<text class="m-l-10">我已阅读并同意</text>
					</view>
					<text :style="css.tcmc" @tap="see(1)">《用户协议》</text> 和 <text :style="css.tcmc" @tap="see(2)">《隐私政策》</text>
				</view>
			</view>
			<!-- #endif -->
			
		</block>
	</view>
</template>
<script>
	import { mapState} from 'vuex';
	import graceChecker from '@/utils/graceChecker';
	import http_config from '@/config/http'; 
	export default {
		data() {
			return {
				loginType:'',
				isLogin:false,
				isInfo:false,
				isAgree:false,
				css:{},
				mobile:'',
				avatar:'',
				nickname:'',
				captcha:'',
				userInfo:{},
				submitDisabled:false,
				disabledCode:false,
				codeText:'获取验证码',
				loginRes: {},
			};
		},
		computed: {
			...mapState(['common','user'])
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			
			if(this.user.isLogin){
				this.$xyfun.back('已经登录');
			}
			
			// 微信小程序code提前获取
			// #ifdef MP-WEIXIN
			uni.login({
				provider: 'weixin',
				success: res => {
					console.log(res);
					this.loginRes = res;
				},
				fail: err => {
					this.$xyfun.msg(err.msg);
				}
			});
			// #endif
			
		},
		methods: {
			
			blurNickName(e) {
				if (e.detail.value) this.nickname = e.detail.value;
			},
			
			
			// 微信小程序手机号授权登录
			decryptPhoneNumber(e){
				this.$api.post({
					url: '/user/user/phone',
					data: {
						encryptedData: e.detail.encryptedData,
						iv: e.detail.iv,
						code: this.loginRes.code,
					},
					success: res => {
						if(res.userInfo.is_info == 1){
							this.userInfo = res.userInfo;
							this.isLogin = true;
							this.isInfo = res.userInfo.is_info;
							this.$store.dispatch('user/login', res);
						}else{
							this.$store.dispatch('user/login', res);
							this.$Router.back();
						}
						
					}
				});
			},
			
			onChooseAvatar(e) {
				this.uploadImage(e.detail.avatarUrl);
			},
			
			// 图片处理-选择图片
			async chooseImage() {
				var that = this;
				console.log('选择图片');
				uni.chooseImage({
					count: 1, 
					sizeType: ["original", "compressed"], 
					sourceType: ["album"], 
					success: res => {
						that.uploadImage(res.tempFilePaths[0])
					}
				});
			},
			
			// 上传图片
			async uploadImage(url) {
				var that = this;
				uni.showLoading({
					title:'图片上传中...'
				})
				return new Promise((resolve, reject) => {
					uni.uploadFile({
						url: http_config.base_url + "/api/common/upload",
						filePath: url,
						name: "file",
						header: { token: that.userInfo.token},
						success: res => {
							res = JSON.parse(res.data);
							that.avatar = res.data.fullurl;
							resolve(res.data.fullurl);
						},
						fail: res =>{
							uni.hideLoading();
							that.$xyfun.msg('图片上传失败！');
							this.avatar = '';
							console.log(res);
						},
						complete: e => {
							uni.hideLoading();
						}
					});
				}).catch(e => {
					console.log(e);
				});
			},
			
			see(type){
				if(type==1 && this.common.appConfig.agreement > 0){
					this.$xyfun.to('/pages/service/article/detail?id='+this.common.appConfig.agreement);
				}
				if(type==2 && this.common.appConfig.privacy > 0){
					this.$xyfun.to('/pages/service/article/detail?id='+this.common.appConfig.privacy);
				}
			},
			
			//完善信息
			profile(){
				
				var data = {
					avatar:this.avatar,
					nickname:this.nickname,
				}
				
				//定义表单规则
				var rule = [
					{ name: 'avatar', checkType: 'string', checkRule: '1,300', errorMsg: '头像不能为空' },
					{ name: 'nickname', checkType: 'string', checkRule: '2,20', errorMsg: '昵称为2-20个字符' },
					
				];
				
				//进行表单检查
				var checkRes = graceChecker.check(data, rule);
				
				if (checkRes) {
					this.$api.post({
						url: '/user/user/profile',
						loadingTip:'加载中...',
						data: {
							nickname: this.nickname,
							avatar: this.avatar,
						},
						success: res => {
							this.$store.dispatch('user/login', res);
							this.$Router.back();
						}
					});
				}else {
					this.$xyfun.msg(graceChecker.error);
					this.submitDisabled = false;
				}
			},
			
		}
	};
</script>

<style scoped lang="scss">
	.logo image{width: 180rpx;height: 180rpx;margin: 100rpx;}
	button::after{border: none;}
	.avatar{width: 100rpx;height: 100rpx;}
	.user-list{
		flex-wrap: nowrap;
		.list-name{width: 20%;flex-shrink: 0;}
		.code{flex-shrink: 0;width: 180rpx;height: 78rpx;line-height: 78rpx;text-align: center;margin-left: 20rpx;}
		.r{
			width: 100%;
			input{width: 100%;vertical-align: middle;line-height: 78rpx;height: 78rpx;padding-left: 20rpx;}
			button{padding: 0;margin: 0;background: none;float: right;border-radius: 0;width: 100%;text-align: right;}
		}
	}
	.tips{justify-content: center;}
</style>
