<template>
	<view class="order" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="tab flex tc tb p-tb-25" :style="css.mbg">
			<view v-for="(item,index) in orderStatus" :class="'col-'+orderStatus.length" @tap="setTab(index)">
				<view :style="orderStatusIndex == index ? css.tcmc : css.tcm">
					{{item.name}}
					<view class="line" :style="orderStatusIndex == index ? css.mcbg : css.mbg"></view>
				</view>
			</view>
		</view>
		<view class="order-list p-tb-30" v-if="!isEmpty">
			<view class="item br-10 m-lr-30 m-b-30 p-30 p-t" :style="css.mbg" v-for="item in orderList" :key="item.id" @tap="$xyfun.to('/pages/user/order/detail?id='+item.id)">
				<view class="flex">
					<view>订单号：{{item.ordersn}}</view>
					<view class="m-l-auto tb" :style="css.tcmc">{{item.status_text}}</view>
				</view>
				
				<view class="goods flex m-t-30">
					<view v-for="(goods,index) in item.item" class="ovh br-10 m-r-20 image" v-if="index<3">
						<image :src="goods.goodsimage" />
					</view>
					<text class="xyicon icon-right m-l-auto"></text>
				</view>
				
				<view :style="css.pbg" class="flex p-20 m-t-30 br-10">
					<view :style="css.tcl"><text class="m-r-5" :style="css.tcp">{{item.totalnum}}</text>件商品 合计:<text class="m-l-10" :style="css.tcp">¥{{item.totalfee}}</text></view>
					<view class="m-l-auto lh-30 flex" :style="css.tcmc">详细 <text class="xyicon icon-right"></text></view>
				</view>
				
			</view>
		</view>
		<view v-else>
			<xy-empty :text="'暂无'+(orderStatusIndex>0?orderStatus[orderStatusIndex].name:'')+'订单'" />
		</view>
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
					{value:'0',name:'待付款'},
					{value:'1',name:'待取货'},
					{value:'2',name:'待归还'},
					{value:'3',name:'已完成'},
					{value:'-1',name:'已取消'},
					{value:'-2',name:'已关闭'},
				],
				orderStatusIndex:0,
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad(options) {
			
			console.log(options);
			
			if(options.status != undefined){
				this.orderStatusIndex = options.status;
			}
			
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
			loadData(){
				this.$api.post({
					url: '/order/lists',
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
	
	.order-list{
		.goods{
			line-height: 150rpx;
			.image{width: 150rpx;height: 150rpx;}
			.image image{width: 150rpx;height: 150rpx;}
		}
	}
	
</style>