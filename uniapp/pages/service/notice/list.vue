<template>
	<view class="notice-list" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<view class="list-box" v-if="!isEmpty">
			<view v-for="item in noticeList" :key="item.id" class="notice-item p-40" :style="css.mbg">
				<xy-notice-row :item="item" />
			</view>
		</view>
		<view v-if="!isLoading && isEmpty">
			<xy-empty text="暂无公告" />
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import xyNoticeRow from '@/components/xy-notice/row';
	import xyEmpty from '@/components/xy-empty';
	export default {
		components: {
			xyNoticeRow,
			xyEmpty
		},
		data() {
			return {
				css:{},
				search:'',
				isEmpty: true,
				noticeList: [],
				currentPage: 1,
				isLoading:true,
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
			this.noticeList = [];
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
				this.$api.get({
					url: '/notice/lists',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.noticeList = [...this.noticeList, ...res.data];
						this.isEmpty = !this.noticeList.length;
						this.isLoading = false;
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
	.notice-item{margin-bottom: 4rpx;}
</style>
