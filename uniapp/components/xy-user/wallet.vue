<template>
	<view class="xy-wallet flex" :style="{'backgroundColor':data.params.bgColor,'border-radius': data.params.borderRadius+'rpx','margin':'0 '+ data.params.lrmargin+'rpx','padding':data.params.njj+'rpx 0'}">
		<view class="item yue tc" @tap="$xyfun.to('/pages/user/balance/detail')">
			<view class="num tb ts-32">{{user.isLogin && user.info.money||0.00}}</view>
			<view class="m-t-10 ts-28" :style="css.tcl">账户余额</view>
		</view>
		<view class="item yue tc bl-l" :style="css.blpc" @tap="$xyfun.to('/pages/user/balance/recharge')">
			<view class="num"><image :src="$xyfun.image(data.params.rechargeicon)" /></view>
			<view class="m-t-10 ts-28" :style="css.tcl">充值</view>
		</view>
		<view class="item wallet m-l-auto tc bl-l" :style="css.blpc" @tap="$xyfun.to('/pages/user/balance/detail')">
			<view class="num"><image :src="$xyfun.image(data.params.walleticon)" /></view>
			<view class="m-t-10 ts-28" :style="css.tcl">钱包</view>
		</view>
	</view>
</template>
<script>
	import { mapState } from 'vuex';
	export default {
		name: "xyWallet",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '钱包组件',
						type: 'wallet',
						params: [],
					}
				}
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
			async onLink(url){
				this.$xyfun.to(url);
			}
		}
	}
</script>
<style lang="scss">
	.xy-wallet{
		.item{width: 33%;}
		.item image{width: 40rpx;height: 40rpx;}
		.item .num{height: 40rpx;line-height: 40rpx;}
	}
</style>
