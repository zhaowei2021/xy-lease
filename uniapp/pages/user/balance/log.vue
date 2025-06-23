<template>
	<view class="log" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<block v-if="!isLoading">
			<view class="log-list" v-if="!isEmpty">
				<view class="item m-b-20 flex p-30" :style="css.mbg" v-for="item in logList" :key="item.id">
					<view class="l">
						<view class="ts-32 tb">{{item.type_text}}</view>
						<view :style="css.tcl" class="ts-28 m-t-15">{{$xyfun.timeFormat(item.createtime)}}</view>
					</view>
					<view class="m-l-auto">
						<view class="tb p-t-25 ts-34 tb" :style="css.tcp">{{item.money > 0 ? "+" :""}}{{item.money}}</view>
					</view>
				</view>
			</view>
			<view v-else>
				<xy-empty text="暂无数据" />
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
				logList: [],
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
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
			this.logList = [];
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
					url: '/user/money/lists',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.logList = [...this.logList, ...res.data];
						this.isEmpty = !this.logList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					}
				});
			},
			
		}
	}
</script>

<style scoped lang="scss">
	.log-list{
		.item .l{
			image{width: 96rpx;height: 96rpx;border-radius: 48rpx;}
		}
	}
</style>