<template>
	<view class="balance" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<view class="head-wrap" :style="css.mcbg">
			<view :style="{ background: 'url(/static/images/balance_bg.png) no-repeat right bottom/auto 260rpx' }">
				<view class="money p-lr-50 tc-w">
					<view class="ts-40 tb p-t-50">{{user.info.money||0.00}}</view>
					<view class="title ts-26 m-t-15">账户余额（元）</view>
				</view>
			</view>
		</view>
		<view class="menu br-10 p-30">
			<view class="item flex" @tap="$xyfun.to('/pages/user/balance/log')">余额明细 <text class="xyicon m-l-auto icon-right"></text> </view>
		</view>
		
		<view class="bottom-fixed p-b-50 m-b-40" :style="css.pbg">
			<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-b-30 m-lr-30" @tap="$xyfun.to('/pages/user/balance/recharge')">充值</button>
			<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-b-50 m-lr-30" @tap="$xyfun.to('/pages/user/withdraw/apply?type=balance')">提现</button>
		</view>
	</view>
</template>

<script>
	import { mapState,mapActions } from 'vuex';
	export default {
		data() {
			return {
				css:{},
			}
		},
		computed: {
			...mapState(['common','user'])
		},
		onPullDownRefresh() {
			this.loadData()
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			...mapActions('user',{'getUserInfo':'getInfo'}),
			
			loadData(){
				//刷新用户信息
				if(this.user.isLogin){
					this.getUserInfo();
				}
				uni.stopPullDownRefresh();
			}
		}
	}
</script>

<style scoped lang="scss">
	.head-wrap{
		border-radius: 0 0 30% 30%;height: 300rpx;
		.money{height: 300rpx;}
	}
	.menu{position: absolute;width: 630rpx;top: 220rpx;left: 30rpx;background: #fff;}
</style>