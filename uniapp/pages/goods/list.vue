<template>
	<view class="goods-list p-b-50" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<view class="list-box p-t-30" v-if="!isEmpty">
			<view class="xy-goods m-lr-30 flex">
				<block v-for="item in goodsList" :key="item.id">
					<xy-goods-grad :item="item" />
				</block>
			</view>
		</view>
		<view v-if="!isLoading && isEmpty">
			<xy-empty :text="'暂无商品'" />
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import xyGoodsGrad from '@/components/xy-goods/grad';
	import xyEmpty from '@/components/xy-empty';
	export default {
		components: {
			xyGoodsGrad,
			xyEmpty
		},
		data() {
			return {
				css:{},
				search:'',
				isEmpty: true,
				category_id:0,
				goodsList: [],
				currentPage: 1,
				isLoading:true,
				lastPage: 1,
				name:'商品列表',
				loadStatus: 'loadmore',
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad() {
			var options = this.$Route.query;
			if(options){
				this.category_id = options.cid;
				this.name = options.name;
			}
			this.$xyfun.setNavBar(this.name);
			this.css = this.$xyfun.css();
			this.loadData();
			
		},
		onPullDownRefresh() {
			this.currentPage = 1;
			this.goodsList = [];
			this.loadData();
		},
		onReachBottom() {
			if(this.currentPage < this.lastPage) {
				this.currentPage += 1;
				this.loadData();
			}
		},
		methods: {
			//门店及教练列表
			loadData(){
				this.$api.get({
					url: '/goods/goods/lists',
					loadingTip:'加载中...',
					data: {
						category_id:this.category_id,
						page: this.currentPage,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.goodsList = [...this.goodsList, ...res.data];
						this.isEmpty = !this.goodsList.length;
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
	.no-data{padding-top: 300rpx;}
	.xy-goods{
		xy-goods-grad:nth-child(2n){margin-left: auto;}
		.xy-goods-grad:nth-child(2n){margin-left: auto;}
	}
</style>
