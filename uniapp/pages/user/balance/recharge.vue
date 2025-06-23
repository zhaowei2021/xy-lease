<template>
	<view class="recharge p-t-30" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		
		<view class="recharge-box m-lr-30 p-tb-40 p-lr-30 br-10" :style="css.mbg" v-if="rechargeList && rechargeList.length">
			<view class="title tb ts-32 lh-32 flex">
				<view class="line m-r-10" :style="css.mcbg"></view>充值套餐
			</view>
			
			<view class="recharge-list flex">
				<view class="item br-10 tc m-t-30 p-tb-40 bl" v-for="(item,index) in rechargeList" :key="item.id" :style="(item.id == recharge_id)?css.blc+css.pbg:css.bl" @tap="changeRecharge(item.id,index)">
					<view class="ts-38 tb" :style="css.tcp">{{item.facevalue}}元</view>
					<view class="m-t-15 ts-26">售价{{item.buyprice}}元</view>
				</view>
			</view>
		</view>
		
		<view class="bottom-fixed flex tc" :style="css.mbg" v-if="recharge_id > 0">
			<view class="p-lr-30 p-tb-20 flex tc wa">
				<view class="l flex p-t-20 lh-40">
					<view :style="css.tcp" class="ts-40 tb"><text class="ts-26">¥</text>{{rechargeList[rechargeIndex].buyprice}}</view>
				</view>
				<view class="action flex m-l-auto">
					<view class="buy m-l-auto tc-w" :style="css.mcbg" @tap="createOrder">
						确认充值
					</view>
				</view>
			</view>
		</view>
		
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import Pay from '@/utils/pay';
	export default {
		components: {
		},
		data() {
			return {
				css:{},
				isLoading: true,
				rechargeList:[],
				recharge_id: 0,
				rechargeIndex: 0,
				disabled: false,
				payList:[{
					name: '微信支付',
					method: 'wechat',
					icon: 'wechat',
					state: true,
					select: true
				}]
			}
		},
		computed: {
			...mapState(['common','user'])
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			
			loadData(){
				this.recharge_id = 0;
				
				this.$api.get({
					url: '/recharge/recharge/lists',
					loadingTip:'加载中...',
					success: res => {
						this.isLoading = false;
						this.rechargeList = res;
						if(res && res.length > 0){
							if(this.$Route.query.recharge_id != undefined){
								this.recharge_id = this.$Route.query.recharge_id;
							}
							
							if(this.recharge_id > 0){
								res.forEach((item,index)=>{
									if(item.id == this.recharge_id){
										this.rechargeIndex = index;
									}
								})
							}else{
								this.recharge_id = res&&res.length ? res[0].id : 0;
							}
							
						}
					}
				});
				
				
			},
			
			//Recharge切换
			changeRecharge(recharge_id,index){
				this.recharge_id = recharge_id;
				this.rechargeIndex = index;
			},
			
			//创建订单
			createOrder(){
				if (this.disabled) {
					return;
				}
				this.disabled = true;
				
				var paytype = '';
				this.payList.map((value) => {
				　　if(value.select){
						paytype = value.method;
					}
				});
				if (!paytype) {
					this.$xyfun.msg('请选择支付方式');
					this.disabled = false;
				}else{
					// 提交订单
					this.$api.post({
						url: '/recharge/order/add',
						data: {
							recharge_id:this.recharge_id,
							paytype:paytype,
						},
						loadingTip: '提交订单中...',
						success: res => {
							//发起支付
							var pay = new Pay(paytype, res, 'recharge');
							pay.payMehod().then((res)=>{
								this.disabled = false;
								pay.payResult(res);
							},(res)=>{
								this.disabled = false;
								pay.payResult(res);
							});
						},
						fail: res => {
							console.log(res);
							this.disabled = false;
						}
					});
				}
			},
			
			//支付方式选择
			payMethodSelect(key){
				this.payList.map((value,index) => {
				　　if(index == key){
						value.select = !value.select;
					}else{
						value.select = false;
					}
				});
			},
			
		}
	}
</script>

<style scoped lang="scss">
	.recharge{
		
		.recharge-box{
			
			.recharge-list{
				.item{
					width: 296rpx;margin-right: 30rpx;
					border-size:4rpx;
					.buy{width: 146rpx;height: 56rpx;line-height: 56rpx;border-radius: 28rpx;margin: 0 auto;}
				}
				.item:nth-child(2n){
					margin-right: 0;
				}
			}
		}
		.bottom-fixed{
			.buy{width: 240rpx;height: 80rpx;border-radius: 40rpx;line-height: 80rpx;}
		}
		
		.buy-model-box{
			width: 100%;border-radius: 20rpx 20rpx 0 0;
			.confirm{width: 240rpx;height: 80rpx;border-radius: 40rpx;line-height: 80rpx;}
		}
		
	}
</style>