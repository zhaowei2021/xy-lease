<template>
	<view class="appointment" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
	
		<view class="tab flex tc tb p-tb-25" :style="css.mbg">
			<view v-for="(item,index) in status" :class="'col-'+status.length" @tap="setTab(index)">
				<view :style="statusIndex == index ? css.tcmc : css.tcm">
					{{item.name}}
					<view class="line" :style="statusIndex == index ? css.mcbg : 'bc-w'"></view>
				</view>
			</view>
		</view>
		
		<view class="order-list p-tb-30" v-if="!isEmpty">
			<view class="item br-10 m-lr-30 m-b-20 p-t" :style="css.mbg" v-for="item in orderList" :key="item.id">
				<view class="p-lr-30 p-tb-25 bl-b" :style="css.blpc">
					<view class="m-b-15 flex">{{item.consignee}} {{item.mobile}} <text class="m-l-auto" :style="css.tcmc">{{item.status_text}}</text></view>
					<view v-if="item.remark != ''" class="m-t-15"><text :style="css.tcl">客户备注：</text>{{item.remark}}</view>
				</view>
				
				<view class="flex br-10 camp-list" :style="css.mbg">
						
					<view class="br-10 goods-list" :style="css.mbg" v-if="$xyfun.strSearch(item.type,'lease')">
						<view class="flex bl-b p-lr-30 p-tb-20" :style="css.blpc">
							<text>租赁商品</text>
							<text class="m-l-auto" :style="css.tcl">数量：<text :style="css.tcmc">{{item.totalleasenum}}</text></text>
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
							<view class="m-l-auto flex tc-w br-10 ts-28 p-lr-30 p-tb-10" :style="css.mcbg" @tap="completeOrder = item,returnDeposit = completeOrder.totaldeposit,showCompleteModel = true" v-if="item.status == 2">确认归还</view>
						</view>
						
					</view>
						
					
						
					
				</view>
			</view>
		</view>
		<view v-else>
			<xy-empty :text="'暂无'+status[statusIndex].name+'订单'" />
		</view>
		
		<!--租赁完成弹窗-->
		<block v-if="showCompleteModel">
			<view class="xy-modal-box xy-modal-box-center complete-model-box p-t-40 br-10 ovh" :style="css.mbg" :class="[showCompleteModel?'xy-modal-show':'']">
				<view class="title ts-32 tc bl-b m-lr-40 p-b-40" :style="css.blpc">租赁完成退押金</view>
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
				status:[
					{value:'1',name:'待取货'},
					{value:'2',name:'待归还'},
					{value:'3',name:'已完成'},
				],
				statusIndex:0,
				orderList: [],
				isEmpty:false,
				isLoading:true,
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
				curDate:'',
				startDate:'',
				endDate:'',
				completeOrder:null, //完成订单
				showCompleteModel:false, //完成弹窗
				returnDeposit:0,//退押金
				
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad(options) {
			this.$xyfun.setNavBar();
			var curDate = this.$xyfun.getDate();
			this.curDate = curDate;
			this.startDate = this.$xyfun.dateAdd(-30,curDate);
			this.endDate = this.$xyfun.dateAdd(30,curDate)
			this.css = this.$xyfun.css();
			if(options){
				this.curappointmentId = options.appointment_id;
			}
			this.loadData();
		},
		onPullDownRefresh() {
			this.currentPage = 1;
			this.orderList = [];
			this.loadData();
		},
		methods: {
			loadData(){
				
				this.$api.post({
					url: '/staff/clerk/orderList',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
						date: this.curDate,
						status: this.status[this.statusIndex]['value']
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.orderList = [...this.orderList, ...res.data];
						this.isEmpty = !this.orderList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					}
				});
				
			},
			
			//购买取货
			buyPickup(order){
				var that = this;
				uni.showModal({
					title:'温馨提示',
					content:'确定客户已取货吗？',
					success(e) {
						if(e.confirm){
							that.$api.post({
								url: '/staff/clerk/buyPickup',
								data: {
									id: order.id,
								},
								success: res => {
									that.$xyfun.msg('操作成功');
									that.orderList = [];
									that.currentPage = 1;
									that.loadData();
								}
							});
						}
					}
				})
			},
			
			//完成服务
			serviceComplete(order){
				var that = this;
				uni.showModal({
					title:'温馨提示',
					content:'确定服务已完成吗？',
					success(e) {
						if(e.confirm){
							that.$api.post({
								url: '/staff/clerk/serviceComplete',
								data: {
									id: order.id,
								},
								success: res => {
									that.$xyfun.msg('操作成功');
									that.orderList = [];
									that.currentPage = 1;
									that.loadData();
								}
							});
						}
					}
				})
			},
			
			//租赁取货
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
									that.orderList = [];
									that.currentPage = 1;
									that.loadData();
								}
							});
						}
					}
				})
			},
			
			// 租赁完成
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
						this.orderList = [];
						this.currentPage = 1;
						this.loadData();
					}
				});
			},
			
			//切换日期
			changeDate(e) {
				this.curDate = e.dd;
				this.startDate = this.$xyfun.dateAdd(-30,e.dd);
				this.endDate = this.$xyfun.dateAdd(30,e.dd)
				this.currentPage = 1;
				this.orderList = [];
				this.loadData();
			},
			
			setTab(index){
				this.statusIndex = index;
				this.currentPage = 1;
				this.orderList = [];
				this.loadData();
			},
			
		}
	}
</script>

<style scoped lang="scss">
	.appointment{padding-bottom: 200rpx;}
	
	.tab{
		width: 100%;
		.line{height: 4rpx;width: 60rpx;margin: 10rpx auto 0;}
	}
	
	.order-list{
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
	
	.complete-model-box{
		width: 690rpx;
		input{width:150rpx}
		.bottom{
			button{border-radius: 40rpx;width: 360rpx; margin-left: 50rpx;margin:0 auto}
			button::after{border: none;}
		}
		.close{position: absolute; right:30rpx;top: 30rpx;}
	}
	
</style>