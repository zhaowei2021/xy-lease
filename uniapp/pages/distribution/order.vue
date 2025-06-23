<template>
	<view class="order" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<view class="tab flex tc tb p-tb-25" :style="css.mbg">
			<view v-for="(item,index) in orderStatus" :class="'col-'+orderStatus.length" @tap="setTab(index)">
				<view :style="orderStatusIndex == index ? css.tcmc : css.tcm">
					{{item.name}}
					<view class="line" :style="orderStatusIndex == index ? css.mcbg : css.mbg"></view>
				</view>
			</view>
		</view>
		<block v-if="!isLoading">
			<view class="order-list p-tb-30" v-if="!isEmpty">
				<view class="item br-10 m-lr-30 m-b-30 p-30 p-t" :style="css.mbg" v-for="item in orderList" :key="item.id">
					<view class="flex tb">
						<view>{{item.ordersn}}</view>
						<view class="m-l-auto" :style="css.tcmc">{{item.status_text}}</view>
					</view>
					<view class="goods m-t-40 flex" v-if="item.ordertype == 'order'">
						<view class="l">
							<image :src="item.service_order.item[0].goodsimage" class="br-10" />
						</view>
						<view class="r m-l-20">
							<view><text class="tb lh-42">{{item.service_order.item[0].goodsname}}</text></view>
							<view class="m-l-auto lh-34 m-t-25"><text :style="css.tcl">返佣金：</text><text :style="css.tcp">¥ </text><text :style="css.tcp" class="ts-34 tb">{{ item.dis_level == 1 ? item.one_commission : item.two_commission}}</text></view>
						</view>
					</view>
					
					<view class="flex lh-40 m-t-40">
						<view class="ts-26" :style="css.tcl">{{$xyfun.timeFormat(item.createtime)}}</view>
						<view class="m-l-auto ts-26">合计：<text :style="css.tcp">¥ </text><text :style="css.tcp" class="ts-34 tb">{{item.service_order.totalfee}}</text></view>
					</view>
				</view>
			</view>
			<view v-else>
				<xy-empty :text="'暂无'+(orderStatusIndex>0?orderStatus[orderStatusIndex].name:'')+'订单'" />
			</view>
		</block>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import xyEmpty from '@/components/xy-empty';
	export default {
		components: {
			xyEmpty,
		},
		data() {
			return {
				css:{},
				isLoading:true,
				isEmpty: true,
				orderList: [],
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
				orderStatus:[
					{value:'all',name:'全部'},
					{value:'1',name:'已结算'},
					{value:'0',name:'未结算'},
					{value:'-2',name:'已取消'},
					{value:'-1',name:'已退回'},
					
				],
				orderStatusIndex:0,
			}
		},
		computed: {
			...mapState(['common'])
		},
		async onLoad() {
			
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.currentPage = 1;
			this.orderList = [];
			this.loadData();
		},
		onReachBottom() {
			if(this.currentPage < this.lastPage) {
				this.currentPage += 1;
				this.loadData();
			}
		},
		methods: {
			async loadData(){
				this.$api.post({
					url: '/distribution/center/orders',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
						status: this.orderStatus[this.orderStatusIndex]['value']
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
			
			
			setTab(index){
				this.orderStatusIndex = index;
				this.currentPage = 1;
				this.orderList = [];
				this.loadData();
			},
			
		}
	}
</script>

<style scoped lang="scss">
	.tab{
		width: 100%;
		.line{height: 4rpx;width: 60rpx;margin: 10rpx auto 0;}
	}
	
	.goods{
		.l{
			image{height: 135rpx;width: 202rpx;}
		}
		.r{width: 410rpx;}
	}
</style>