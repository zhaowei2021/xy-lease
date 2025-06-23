<template>
	<view class="staff-user" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="xy-user-card" :style="css.mcbg">
			<view class="user-info flex br-20 bc-w p-30">
				<view class="l m-r-15">
					<image :src="$xyfun.image(userInfo.avatar)" />
				</view>
				<view class="c tc-w">
					<view>
						<view class="ts-30 flex lh-32 p-t-20 tb" >
							<text>
								{{userInfo.nickname}}
							</text>
						</view>
						<view class="ts-28 lh-28 m-t-15" >{{userInfo.mobile}}</view>
					</view>
				</view>
				
			</view>
		</view>
		
		
		
		
		
		<view class="item m-b-30 p-t" :style="css.mbg" v-if="orderList.length > 0">
			<view class="p-lr-30 p-tb-25 bl-b flex" :style="css.blpc">
				<view class="tb">待处理订单</view>
				<view class="m-l-auto" :style="css.tcmc">{{orderList.length}}个</view>
			</view>
			<view class="goods-order-list p-t-20" :style="css.pbg" >
				<view class="item br-10 m-lr-30 m-b-20 p-t" :style="css.mbg" v-for="item in orderList" :key="item.id">
					<view class="p-lr-30 p-tb-25 bl-b" :style="css.blpc">
						<view class="m-b-15 flex">{{item.consignee}} {{item.mobile}} <text class="m-l-auto" :style="css.tcmc">{{item.status_text}}</text></view>
						<view v-if="item.remark != ''" class="m-t-15"><text :style="css.tcl">客户备注：</text>{{item.remark}}</view>
					</view>
					
					<view class="flex br-10 camp-list" :style="css.mbg">
							
						<view class="br-10 goods-list" :style="css.mbg">
							<view class="flex bl-b p-lr-30 p-tb-20" :style="css.blpc">
								<text>租赁商品</text>
								<text class="m-l-auto" :style="css.tcl">数量：<text :style="css.tcmc">{{item.totalnum}}</text></text>
							</view>
							<view :style="css.blpc" class="item p-lr-30 p-t-30 flex " v-for="(g,index) in item.item" :key="index" v-if="g.goodstype == 'single' || g.goodstype =='package'">
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
											<text :style="css.tcl">/{{ item.leasetype == 'days' ? '天' : item.leasetype == 'hour' ? '小时' : '夜' }}</text>
											<text class="m-l-auto">x{{ item.leasetimenum }}</text>
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
							
							<view class="flex p-30 bl-t bl-b m-t-30 br-10" :style="css.blpc">
								<view :style="css.tcl" class="ts-28 m-tb-10">押金:<text class="m-r-20 m-l-10" :style="css.tcp">¥{{item.totaldeposit}}</text>租金:<text class="m-l-10" :style="css.tcp">¥{{item.totalamount}}</text></view>
								<view v-if="item.status == 3" class="m-l-auto m-tb-10" :style="css.tcmc">已归还</view>
								<view class="m-l-auto flex tc-w br-10 ts-28 p-lr-30 p-tb-10" :style="css.mcbg" @tap="leasePickup(item)" v-if="item.status == 1">确认取货</view>
								<view class="m-l-auto flex tc-w br-10 ts-28 p-lr-30 p-tb-10" :style="css.mcbg" @tap="completeOrder = item,returnDeposit = completeOrder.totaldeposit,completeOrderType = 'order',showCompleteModel = true" v-if="item.status == 2">确认归还</view>
							</view>
							
						</view>
		
			
					</view>
				</view>
			</view>
			
		</view>
		
		<view v-if="orderList.length == 0">
			<xy-empty text="暂无待处理订单" />
		</view>
		
		<!--租赁完成弹窗-->
		<block v-if="showCompleteModel">
			<view class="xy-modal-box xy-modal-box-center complete-model-box p-t-40 br-10 ovh" :style="css.mbg" :class="[showCompleteModel?'xy-modal-show':'']">
				<view class="title ts-32 tc bl-b m-lr-40 p-b-40" :style="css.blpc">{{completeOrderType == 'lease' ? '租赁' : '租赁'}}完成退押金</view>
				<view class="c m-lr-40">
					<view class="flex p-tb-40 bl-b" :style="css.blpc">
						<view>用户支付押金</view>
						<view class="m-l-auto">¥{{completeOrder.totaldeposit}}</view>
					</view>
					<view class="flex p-tb-40 bl-b" :style="css.blpc">
						<view>实际退款押金(可修改)</view>
						<view class="m-l-auto">
							<input class="tr" v-model="returnDeposit" type="number" />
						</view>
					</view>
				</view>
				<view class="bottom p-tb-50" :style="css.mbg">
					<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w" @tap="complete()">确认</button>
				</view>
				<view class="close" :style="css.tcl" @tap="showCompleteModel = false,completeOrder = null,returnDeposit = 0">
					<text class="xyicon icon-close"></text>
				</view>
			</view>
			<view class="xy-modal-mask" :class="[showCompleteModel?'xy-mask-show':'']" @tap="showCompleteModel = false,completeOrder = null,returnDeposit = 0"></view>
		</block>
		
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import DateTabs from '@/uni_modules/hope-11-date-tabs/components/hope-11-date-tabs/date-tabs.vue'
	import xyEmpty from '@/components/xy-empty';
	export default {
		components: {
			DateTabs,
			xyEmpty
		},
		data() {
			return {
				css:{},
				userInfo:{},
				orderList:[],//营订单
				isLoading:true,
				showCompleteModel:false,
				completeOrder:null, //租赁完成订单
				completeOrderType:'order',//租赁完成订单类型
				returnDeposit:0,//退押金
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.loadData();
		},
		methods: {
			loadData(){
				this.$api.post({
					url: '/staff/clerk/userInfo',
					loadingTip:'加载中...',
					data: {
						user_id: this.$Route.query.user_id,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.userInfo = res.userInfo;
						this.orderList = res.orderList;
					}
				});
			},
			
			
			//取货
			leasePickup(order){
				var that = this;
				uni.showModal({
					title:'温馨提示',
					content:'确定客户已取货吗？',
					success(e) {
						if(e.confirm){
							that.$api.post({
								url: '/staff/clerk/leasePickup',
								data: {
									id: order.id,
								},
								success: res => {
									that.$xyfun.msg('操作成功');
									that.loadData();
								}
							});
						}
					}
				})
			},
			
			// 完成
			complete(){
				
				this.$api.post({
					url: '/staff/clerk/leaseComplete',
					data: {
						id: this.completeOrder.id,
						deposit: this.returnDeposit,
					},
					success: res => {
						this.$xyfun.msg('操作成功');
						this.showCompleteModel = false;
						this.loadData();
					}
				});
			},
		
			
		}
	}
</script>

<style scoped lang="scss">
	
	.staff-user{
		padding-bottom: 120rpx;
	}
	
	.xy-user-card{
		.user-info{
			width: 690rpx;
			.l{
				image{width: 110rpx;height: 110rpx;border-radius: 55rpx;}
			}
			.c{
				width: 300rpx;
			}
		}
	}
	
	.order-list{
		.camp-list{
			.item{
				.l,.l image{
					width: 170rpx;height: 170rpx;
				}
				.r{
					width: 440rpx;
					.price-box{position: absolute; left: 0;bottom: 5rpx;}
				}
			}
			.seat-total{justify-content: space-between;width: 100%;}
		}
	}
	
	.goods-order-list{
		.goods-list{
			width: 100%;
			.item{
				.l,.l image{
					width: 170rpx;height: 170rpx;
				}
				.r{
					width: 440rpx;
					.price-box{position: absolute; left: 0;bottom: 5rpx;width: 100%;}
				}
			}
			.seat-total{justify-content: space-between;width: 100%;}
		}
	}
	
	.menu{
		margin-top: 100rpx;
		.item{
			.btn{height: 54rpx;border-radius: 27rpx;}
		}
	}
	
	.complete-model-box{
		width: 690rpx;
		
		.bottom{
			button{border-radius: 40rpx;width: 360rpx; margin-left: 50rpx;margin:0 auto}
			button::after{border: none;}
		}
		input{width: 200rpx;}
		.close{position: absolute; right:30rpx;top: 30rpx;}
	}
	
</style>