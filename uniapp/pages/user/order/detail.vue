<template>
	<view class="order-detail p-b-40" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="top psr" :style="css.mcbg">
			<image class="bg" src="/static/images/status-bg.png" />
			<view class="status-left flex">
				<image :src="'/static/images/order-status'+orderDetail.status+'.png'" mode="widthFix" v-if="orderDetail.status >=0" />
				<image src="/static/images/order-status-close.png" mode="widthFix" v-else />
				<view class="status-box m-l-20 tc-w">
					<view :class="'name tb ts-34 ' + (orderDetail.status == -2 ? 'p-t-10':'p-t-40')">
						{{orderDetail.status_text}}
					</view>
					<view class="tips ts-28 lh-28 m-t-10 tc-w" v-if="orderDetail.status == -2">超时未支付，订单自动关闭</view>
				</view>
			</view>
			<view class="status-right ts-28 tc-w" v-if="orderDetail.status ==0">
				<view class="flex">
					<text>剩余时间：</text>
					<view class="time">{{time}}</view>
				</view>
			</view>
		</view>
		
		<view class="m-30 br-10 goods-list p-b-30" :style="css.mbg" v-if="$xyfun.strSearch(orderDetail.type,'lease')">
			<view class="flex bl-b p-lr-30 p-tb-20" :style="css.blpc">
				<text>租赁商品</text>
			</view>
			<view :style="css.blpc" class="item p-lr-30 p-t-30 flex " v-for="(g,index) in orderDetail.item" :key="index" v-if="g.goodstype == 'single' || g.goodstype =='package'">
				<view class="l ovh br-10">
					<image :src="g.goodsimage" />
				</view>
				<view class="r psr m-l-20">
					<view class="tb lh-40 goods-name">
						{{g.goodsname}}
					</view>
					<view class="sku-text m-t-5" v-if="g.goodsskutext">
						<text class="sku-text ts-24 lh-24 br-10 p-lr-25 p-tb-" :style="css.pbg+css.tcl">
						  {{g.goodsskutext ? g.goodsskutext : ""}}
						</text>
					</view>
					<view class="price-box psa lh-34">
						<view class="m-b-10 flex">
							<text :style="css.tcl">租金单价:</text>
							<text :style="css.tcp">￥</text>
							<text :style="css.tcp" class="ts-34 tb">{{ g.price }}</text>
							<text :style="css.tcl">/{{ orderDetail.leasetype == 'days' ? '天' : orderDetail.leasetype == 'hour' ? '小时' : '夜' }}</text>
							<text class="m-l-auto">x{{ orderDetail.leasetimenum }}</text>
						</view>
						<view class="flex">
							<text :style="css.tcl">押金单价:</text>
							<text :style="css.tcp">￥</text>
							<text :style="css.tcp" class="ts-34 tb">{{ g.deposit }}</text>
							<text class="m-l-auto">x{{ g.buynum }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		
		<view class="m-t-30 br-10 m-lr-30 m-b-30 p-30" :style="css.mbg">
			<view class="flex">
				<text>订单编号：</text>
				<text>{{orderDetail.ordersn}}</text>
				<text @tap="$xyfun.copy(orderDetail.ordersn)" class="ts-24 bl br-10 m-l-25 p-lr-10" :style="css.pbg">复制</text>
			</view>
			<block v-if="$xyfun.strSearch(orderDetail.type,'lease')">
				<view class="flex m-t-20">
					<text>租赁时间：</text>
					<text>{{orderDetail.leasetime}}</text>
				</view>
				<view class="flex m-t-20">
					<text>自提时间：</text>
					<text>{{orderDetail.pickuptime}}</text>
				</view>
			</block>
			<view class="flex m-t-20">
				<text>下单时间：</text>
				<text>{{$xyfun.timeFormat(orderDetail.createtime)}}</text>
			</view>
			
			<view class="flex m-t-20">
				<text>配送方式：</text>
				<text>{{orderDetail.deliverytype_text}}</text>
			</view>
		</view>
		<view class="m-t-30 br-10 m-lr-30 m-b-30 p-30" :style="css.mbg">
			
			<view class="flex">
				<text>租赁金额：</text>
				<view class="m-l-auto tb"><text>¥</text>{{orderDetail.totalamount}}</view>
			</view>
			
			<view class="flex m-t-25">
				<text>租赁押金：</text>
				<view class="m-l-auto tb"><text>¥</text>{{orderDetail.totaldeposit}}</view>
			</view>
			
			<view class="flex m-t-25">
				<text>优惠金额：</text>
				<view class="m-l-auto tb"><text>¥</text>{{orderDetail.couponfee}}</view>
			</view>
			<view class="flex m-t-25">
				<text>需付金额：</text>
				<view class="m-l-auto tb"><text>¥</text>{{orderDetail.totalfee}}</view>
			</view>
				
			<view class="flex m-t-30" v-if="orderDetail.payfee > 0">
				<view class="m-l-auto tb lh-34">
					<text>实付金额：</text>
					<text :style="css.tcp">¥</text>
					<text class="ts-34" :style="css.tcp">{{orderDetail.payfee}}</text>
				</view>
			</view>
		</view>
		<view class="bottom-fixed flex tc" :style="css.mbg" v-if="orderDetail.status == 0">
			<view class="p-lr-30 p-tb-15 flex tc wa">
				<view class="action flex m-l-auto">
					<view class="close m-r-40" :style="css.pbg+css.tcl" @tap="cancelOrder()">取消订单</view>
					<view class="pay m-l-auto tc-w" :style="css.mcbg" @tap="onPay()">支付</view>
				</view>
			</view>
		</view>
		
		<!--支付弹窗-->
		<block v-if="payModelShow">
			<view class="xy-modal-box xy-modal-box-center pay-model-box p-t-30 br-10" :style="css.mbg" :class="[payModelShow?'xy-modal-show':'']">
				<view class="title m-b-50 ts-32 tc bl-b p-b-30" :style="css.blpc">支付方式</view>
				<view class="tc tb m-tb-40 ts-36">支付金额{{ orderDetail.totalfee || "0.00" }}元</view>
				<view class="pay-list">
					<view class="item flex p-30 m-b-2 lh-40" :style="css.mbg" v-for="(item, index) in payList" :key="index" v-if="item.state">
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
	import { mapState } from 'vuex';
	import Pay from '@/utils/pay';
	export default {
		data() {
			return {
				css:{},
				isLoading:true,
				id: 0,
				orderDetail:{},
				time:'',
				timer:null,
				payModelShow:false,
				disabled:false,
				payList:[{
					name: '余额支付',
					method: 'balance',
					icon: 'balance',
					state: true,
					select: false
				},
				{
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
		onUnload() {
			clearInterval(this.timer);
		},
		methods: {
			
			loadData(){
				this.$api.post({
					url: '/order/detail',
					loadingTip:'加载中...',
					data: {
						id: this.$Route.query.id,
					},
					success: res => {
						this.isLoading = false;
						this.orderDetail = res;
						if(res.status == 0){
							this.timer=setInterval(()=>{
								this.countDown(res.ext_arr.expired_time);
							},1000)
						}
					}
				});
			},
			
			//倒计时
			countDown(endtime){
				if(this.$xyfun.intervalTime(endtime) == '00:00:00'){
					clearInterval(this.timer)
				}
				this.time = this.$xyfun.intervalTime(endtime);
			},
			
			//取消订单
			cancelOrder(){
				this.$api.post({
					url: '/order/cancel',
					data: {
						id: this.orderDetail.id,
					},
					success: res => {
						this.loadData();
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
					//发起支付
					var pay = new Pay(pay_type, this.orderDetail, 'order');
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
	.order-detail{
		padding-bottom: 180rpx;
		.top{
			.bg{
				width: 750rpx;height: 210rpx;
			}
			
			.status-left{
				position: absolute;left: 50rpx; top: 50rpx;
				image{width: 120rpx;}
			}
			
			.status-right{
				position: absolute;right: 30rpx;top: 90rpx;
			}
		}
		
		.time{width: 115rpx;}
		
		.bottom-fixed{
			.close{width: 220rpx;height: 74rpx;border-radius: 37rpx;line-height: 74rpx;}
			.pay{width: 180rpx;height: 74rpx;border-radius: 37rpx;line-height: 74rpx;}
		}
		
		.logistics-model-box{
			width: 660rpx;
		}
		
		.pay-model-box{width: 80%;}
		
	}
	
	.camp-list{
		.item{
			.l,.l image{
				width: 180rpx;height: 180rpx;
			}
			.r{
				width: 490rpx;
				.price-box{position: absolute; right: 0;top: 0;}
			}
		}
	}
	
	.goods-list{
		.item{
			.l{
				width: 178rpx;height: 178rpx;
				image{width: 178rpx;}
			}
			.r{
				width: 432rpx;
				
			}
			.goods-name{max-height: 80rpx;overflow: hidden;}
		}
		.price-box{bottom: 3rpx;width: 100%;}
	}
</style>