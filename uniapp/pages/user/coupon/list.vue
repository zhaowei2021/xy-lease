<template>
	<view class="order" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="tab flex tc tb p-tb-25" :style="css.mbg">
			<view v-for="(item,index) in status" :class="'col-'+status.length" @tap="setTab(index)">
				<view :style="statusIndex == index ? css.tcmc : css.tcm">
					{{item.name}}
					<view class="line" :style="statusIndex == index ? css.mcbg : 'bc-w'"></view>
				</view>
			</view>
		</view>
		<block v-if="!isLoading">
			<view class="list p-tb-30" v-if="!isEmpty">
				<view class="item br-10 m-lr-30 m-b-30 p-t flex ovh" :style="css.mbg" v-for="item in couponList" :key="item.id">
					<view class="l tc tc-w p-30" :style="css.mcbg">
						<view v-if="item.type == 'reward'">
							¥<text class="tb ts-46">{{$xyfun.pe(item.money)}}</text>
						</view>
						<view v-else>
							<text>{{item.discount}}</text>折
						</view>
						<view class="ts-26 m-t-15">
							<text v-if="item.atleast > 0">满{{$xyfun.pe(item.atleast)}}元可用</text>
							<text v-else>无限制</text>
						</view>
					</view>
					<view class="r p-30 psr">
						<view class="tb">{{item.name}}</view>
						<view class="ts-24 m-t-50">有效期:{{ $xyfun.timeFormat(item.endtime)}}</view>
						<view class="btn tc-w ts-26 br-20 p-lr-25" :style="css.mcbg" v-if="item.status == 0" @tap="$xyfun.to('/pages/index')">使用</view>
						<view class="btn tc-w ts-26 br-20 p-lr-25" :style="css.mcbg" v-if="item.ishandsel == 1 && item.status == 0" @tap="showHand(item)">转增</view>
					</view>
				</view>
			</view>
			<view v-else>
				<xy-empty text="暂无优惠券" />
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
				couponList: [],
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
				showHandsel:false,
				status:[
					{value:0,name:'未使用'},
					{value:1,name:'已使用'},
				],
				statusIndex:0,
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
			this.currentPage = 1;
			this.couponList = [];
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
					url: '/user/coupon/lists',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
						status: this.status[this.statusIndex]['value']
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.couponList = [...this.couponList, ...res.data];
						this.isEmpty = !this.couponList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					}
				});
				
			},
			
			setTab(index){
				this.statusIndex = index;
				this.currentPage = 1;
				this.couponList = [];
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
	
	.return{line-height: 40rpx;height: 40rpx;}
	
	.list{
		.item{
			.l{
				width: 180rpx;
			}
			.r{
				width: 390rpx;
				.btn{position: absolute; right: 30rpx;top: 30rpx;}
			}
		}
	}
	
	.handsel-model-box{
		.btn{margin-top: 150rpx;}
		height: 600rpx;border-radius: 30rpx 30rpx 0 0;
		.ewm image{ width: 300rpx;height: 300rpx;}
		.close{position: absolute; right:30rpx;top: 30rpx;}
	}
	
</style>