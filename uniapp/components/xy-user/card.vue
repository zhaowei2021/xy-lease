<template>
	<view class="xy-user-card flex" :style="{'backgroundColor':data.params.bgColor,'border-radius': data.params.borderRadius+'rpx','margin':'0 '+ data.params.lrmargin+'rpx',padding:data.params.njj+'rpx'}">
		
		<view class="l m-r-15">
			<image v-if="user.isLogin" :src="$xyfun.image(user.info.avatar?user.info.avatar:common.appConfig.useravatar)" @tap="$xyfun.to('/pages/user/info')" />
			<image v-else :src="$xyfun.image($xyfun.config().useravatar)" @tap="$xyfun.toLogin()" />
		</view>
		<view class="c">
			<view v-if="user.isLogin" @tap="$xyfun.to('/pages/user/info')">
				<view class="ts-30 flex lh-32 p-t-20 tb" :style="css.tcm">
					<text>
						{{user.info.nickname}}
					</text>
				</view>
				<view class="ts-28 lh-28 m-t-15" :style="css.tcl">个人信息 <text class="xyicon icon-edit m-l-10"></text></view>
			</view>
			<view v-else @tap="$xyfun.toLogin()">
				<view class="ts-30 lh-32 p-t-20 tb m-t-25" :style="css.tcm">点击登录</view>
			</view>
		</view>
		
		<view class="r m-l-auto ts-28 flex" @tap="$emit('showEwm')">
			<view class="ewm br-5 tc-w p-lr-20 ts-24 flex" :style="css.mcbg"><text class="xyicon icon-qrcode ts-26 m-r-10"></text> 会员码</view>
		</view>
		
	</view>
</template>
<script>
	import { mapState } from 'vuex';
	export default {
		name: "xyUserCard",
		props: {
			data:{
				type: Object,
			}
		},
		data() {
			return {
				css:this.$xyfun.css(),
				
			}
		},
		computed: {
			...mapState(['user'])
		},
		methods:{
			
			
		}
	}
</script>
<style lang="scss">
	.xy-user-card{
		.l{
			image{width: 110rpx;height: 110rpx;border-radius: 55rpx;}
		}
		.c{
			width: 300rpx;
		}
		.r{
			position: relative;
			.ewm{height: 54rpx;line-height: 54rpx;margin-top: 28rpx;}
		}
	}
</style>
