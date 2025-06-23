<template>
	<view class="favorite" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		
		<block v-if="!isLoading">
			<view class="favorite-list p-tb-50" v-if="!isEmpty">
				<view class="item br-10 m-lr-30 m-b-30 p-30" :style="css.mbg" v-for="item in favoriteList" :key="item.id">
					<view v-if="item.type =='goods' && item.target">
						<xy-goods-row :item="item.target" />
					</view>
				</view>
			</view>
			<view v-else>
				<xy-empty text="您暂未收藏商品" />
			</view>
		</block>
	</view>
</template>


<script>
	import { mapState } from 'vuex';
	import xyEmpty from '@/components/xy-empty';
	import xyGoodsRow from '@/components/xy-goods/row';
	export default {
		components: {
			xyEmpty,
			xyGoodsRow
		},
		data() {
			return {
				css:{},
				isLoading:true,
				isEmpty: true,
				favoriteList: [],
				currentPage: 1,
				lastPage: 1,
				lat:0,
				lng:0,
				loadStatus: 'loadmore',
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad() {
			var that = this;
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			that.loadData();
		},
		onPullDownRefresh() {
			this.currentPage = 1;
			this.favoriteList = [];
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
					url: '/user/favorite/lists',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.favoriteList = [...this.favoriteList, ...res.data];
						this.isEmpty = !this.favoriteList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					}
				});
			}
			
		}
	}
</script>

<style scoped lang="scss">
	.tab{
		width: 100%;
		.line{height: 4rpx;width: 60rpx;margin: 10rpx auto 0;}
	}
</style>