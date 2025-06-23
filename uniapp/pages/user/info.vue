<template>
	<view class="info" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		
		<view class="user-list lh-60 flex p-lr-30 p-tb-40 m-b-2">
			<view class="list-name tb">基本信息</view>
			<view class="save-btn ts-30 tc lh-30 p-15 m-l-auto" :style="css.mbg" @tap="logout()">退出</view>
		</view>
		
		<view class="user-list lh-100 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg">
			<text class="list-name">头像</text>
			<view class="r m-l-auto">
				<button open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
					<image class="avatar" :src="$xyfun.image(userInfo.avatar)" />
					<text class="xyicon icon-right m-l-15"></text>
				</button>
			</view>
		</view>
		
		<view class="user-list lh-70 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg">
			<text class="list-name">昵称</text>
			<view class="flex r m-l-auto">
				<input class="br-10 p-20 lh-30" :style="css.pbg" placeholder="请输入昵称~" maxlength="50" v-model="userInfo.nickname" @input="onChangeNickName" />
			</view>
		</view>
		<view class="user-list lh-70 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg">
			<text class="list-name">手机号</text>
			<view class="flex r m-l-auto">
				<input class="br-10 p-20 lh-30" :style="css.pbg" disabled="true" :value="userInfo.mobile" maxlength="50" />
			</view>
		</view>
		
		<view class="bottom-fixed p-b-50" :style="css.pbg">
			<button :style="!editInfoDisabled ? css.mbg+css.tcl :css.mcbg" class="ts-30 lh-30 p-25 tc-w m-t-50 m-50" @tap="editUserInfo()">保存</button>
		</view>
		
	</view>
</template>

<script>
	import { mapState,mapActions } from 'vuex';
	import http_config from '@/config/http'; 
	export default {
		
		data() {
			return {
				css:{},
				editInfoDisabled:false,
				userInfo:{},
				userData:{},
			}
		},
		computed: {
			...mapState(['common','user'])
		},
		onLoad() {
			
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.getUserInfo().
				then(res => {
					this.userInfo = res.userInfo;
					this.userData = res.userInfo;
				}).catch(e => {
					console.log(e);
				});
		},
		onPullDownRefresh() {
			
		},
		methods: {
			...mapActions('user',{'getUserInfo':'getInfo'}),
			
			logout(){
				this.$api.post({
					url: '/user/user/logout',
					success: () => {
						this.$store.dispatch('user/logout');
						this.$xyfun.msg('退出成功');
						this.$xyfun.to('/pages/index',true);
					}
				});
			},
			
			onChooseAvatar(e) {
				this.userInfo.avatar = e.detail.avatarUrl;
				this.uploadImage(e.detail.avatarUrl);
				this.editInfoDisabled = true;
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
						header: { token: that.user.info.token },
						success: res => {
							console.log('res',res);
							res = JSON.parse(res.data);
							console.log(res.data.fullurl)
							that.userData.avatar = res.data.fullurl;
							resolve(res.data.fullurl);
						},
						complete: e => {
							uni.hideLoading();
						}
					});
				}).catch(e => {
					console.log(e);
				});
			},
			
			// 修改昵称
			onChangeNickName() {
				this.editInfoDisabled = this.userInfo.nickname == this.userData.nickname;
			},
			
			// 修改信息
			editUserInfo() {
				let that = this;
				
				if(!this.editInfoDisabled){
					return false;
				}
				
				if (!that.userInfo.nickname) {
					this.$xyfun.to('昵称不能为空');
					return false;
				}
				
				this.$api.post({
					url: '/user/user/profile',
					loadingTip:'加载中...',
					data: {
						nickname: that.userData.nickname,
						avatar:that.userData.avatar,
					},
					success: () => {
						this.$xyfun.msg('信息修改成功');
						uni.navigateBack();
					}
				});
				
			},
		}
	}
</script>

<style scoped lang="scss">
	.avatar{width: 100rpx;height: 100rpx;}
	.user-list .r{
		width: 70%;
		input{width: 100%;}
		button{padding: 0;margin: 0;background: none;float: right;border-radius: 0;}
		button::after{border: none;}
	}
	.save-btn{width: 100rpx;border-radius: 35rpx;}
</style>
