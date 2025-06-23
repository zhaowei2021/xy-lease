<template>
	<view class="result" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<view class="tips tc">
			<view class="p-15">
				<image src="/static/images/fail.png" v-if="payState=='fail'"/>
				<image src="/static/images/success.png" v-if="payState=='success'"/>
			</view>
			<text class="notice ts-34 tb">{{ payText[payState] }}</text>
		</view>
		<view class="action m-30 tc flex ts-28">
			<view class="btn bl" :style="css.bltc" @tap="$xyfun.to('/pages/index',true)">返回首页</view>
			<view class="btn bl" :style="css.bltc" @tap="goOrder()" v-if="orderType != 'recharge'">查看订单</view>
			<view class="btn bl tc-w" :style="css.mcbg+blc" v-if="payState == 'fail'" @tap="onPay()">重新支付</view>
		</view>
		
		<!--支付弹窗-->
		<block v-if="payModelShow">
			<view class="xy-modal-box xy-modal-box-center pay-model-box p-t-30 br-10 bc-w" :class="[payModelShow?'xy-modal-show':'']">
				<view class="title m-b-50 ts-32 tc bl-b p-b-30" :style="css.blpc">支付方式</view>
				<view class="tc tb m-tb-40 ts-36">支付金额{{ orderDetail.totalfee || "0.00" }}元</view>
				<view class="pay-list">
					<view class="item flex p-30 m-b-2 lh-40 bc-w" v-for="(item, index) in payList" :key="index" v-if="item.state">
						<view class="l flex">
							<text :class="'xyicon icon-'+item.icon+' ts-40 m-r-15'"></text>
							<text class="lh-40">{{item.name}}</text>
						</view>
						<view class="r tb m-l-auto" @tap="payMethodSelect(index)">
							<text class="xyicon icon-radio-a ts-32 lh-40" v-if="item.select"></text>
							<text class="xyicon icon-radio ts-32 lh-40" v-else></text>
						</view>
					</view>
				</view>
				<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-30" @tap="subPay()">确认支付</button>
			</view>
			<view class="xy-modal-mask" :class="[payModelShow?'xy-mask-show':'']" @tap="payModelShow =!payModelShow"></view>
		</block>
	</view>
</template>

<script>
	import Pay from '@/utils/pay';
	export default {
		data() {
			return {
				css:{},
				orderId: 0,
				disabled:false,
				payment: 'wechat',
				payState: 'fail',
				orderType: '',
				orderDetail:{},
				payModelShow:false,
				disabled:false,
				payText: {
					fail: '支付失败',
					success: '支付成功',
				},
				payList:[
				{
					name: '微信支付',
					method: 'wechat',
					icon: 'wechat',
					state: true,
					select: true
				}]
			}
		},
		onLoad() {
			var options = this.$Route.query;
			if(options.orderId){
				this.orderId = options.orderId;
				this.payment = options.payment;
				this.payState = options.payState;
				this.orderType = options.orderType;
			}
			if(this.orderType != 'recharge'){
				this.payList.push({
					name: '余额支付',
					method: 'balance',
					icon: 'balance',
					state: true,
					select: false
				})
			}
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			
			loadData() {
				switch (this.orderType) {
					case 'order':
						this.getOrderDetail()
						break;
					case 'recharge':
						this.getRechargeOrderDetail()
						break;
					default:
						break;
				}
			},
			
			// 充值订单详情
			getRechargeOrderDetail(){
				this.$api.post({
					url: '/recharge/order/detail',
					data: {
						id:this.orderId,
					},
					success: res => {
						this.orderDetail = res;
					}
				});
			},
			
			// 租赁订单详情
			getOrderDetail(){
				this.$api.post({
					url: '/order/detail',
					data: {
						id:this.orderId,
					},
					success: res => {
						this.orderDetail = res;
					}
				});
			},
			
			// 重新支付
			onPay() {
				this.payModelShow = true;
			},
			
			
			
			// 支付方式选择
			payMethodSelect(key){
				
				this.payList.map((value,index) => {
				　　if(index == key){
						value.select = !value.select;
					}else{
						value.select = false;
					}
				});
			},
			
			// 查看订单
			goOrder(){
				switch (this.orderType) {
					case 'order':
						this.$xyfun.to('/pages/user/order/detail?id='+this.orderDetail.id);
						break;
					case 'recharge':
						this.$xyfun.to('/pages/recharge/order/detail?id='+this.orderDetail.id);
						break;
					default:
						break;
				}
			},
			
			// 确认支付
			subPay(){
				if(this.disabled){
					return false;
				}
				this.disabled = true;
				var pay_type = '';
				this.payList.map((value) => {
				　　if(value.select){
						pay_type = value.method;
					}
				});
				if (!pay_type) {
					this.$xyfun.msg('请选择支付方式');
				}else{
					var pay = new Pay(pay_type, this.orderDetail, this.orderType);
					pay.payMehod().then((res)=>{
						this.disabled = true;
						pay.payResult(res);
					},()=>{
						this.disabled = false;
					});
				}
			},
		}
	}
</script>

<style scoped lang="scss">
	.result{
		.tips{padding: 150rpx 0 30rpx;
			image{width: 140rpx;height: 140rpx;}
		}
		.action{
			.btn{flex: 1;height: 70rpx;border-radius: 35rpx;margin: 0 15rpx;line-height: 70rpx;}
		}
		.pay-model-box{width: 80%;}
	}
</style>