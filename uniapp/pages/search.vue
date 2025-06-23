<template>
	<view class="search" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<viw class="top p-30 flex">
			<view class="search-type br-10 m-r-30" :style="css.mbg">
				<picker  @change="changeType" :value="searchTypeIndex" :range="searchType" range-key="name">
					<view class="p-lr-20">
						{{searchType[searchTypeIndex]['name']}}
						<text class="xyicon icon-xiala m-l-15"></text>
					</view>
				</picker>
			</view>
			<view class="search-box m-l-auto ovh" :style="css.mbg" >
				<input v-model="search" placeholder="请输入搜索内容" :placeholder-style="css.tcl" />
				<text class="xyicon icon-search" :style="css.tcmc" @tap="loadData"></text>
			</view>
		</viw>
		<view class="search-list p-b-50" v-if="!isEmpty">
			<block v-if="searchTypeIndex == 0" >
				<view class="flex">
					<view class="m-l-30 m-b-30 br-10" v-for="item in searchList" :key="item.id" :style="css.mbg">
						<xy-goods-row :item="item" />
					</view>
				</view>
			</block>
			
			<block v-if="searchTypeIndex == 1" v-for="item in searchList" :key="item.id">
				<view class="m-30 p-30 br-10" :style="css.mbg">
					<xy-notice-row :item="item" />
				</view>
			</block>
			
		</view>
		<view v-if="isEmpty && !isLoading">
			<xy-empty text="无搜索结果,换个词试试吧～" />
		</view>
	</view>
</template>

<script>
	import xyGoodsRow from '@/components/xy-goods/grad';
	import xyNoticeRow from '@/components/xy-notice/row';
	import xyEmpty from '@/components/xy-empty';
	export default {
		components: {
			xyGoodsRow,
			xyNoticeRow,
			xyEmpty
		},
		data() {
			return {
				css:{},
				search:'',
				searchType:[{name:'商品',value:'goods'},{name:'公告',value:'notice'}],
				searchTypeIndex:0,
				searchList: [],
				isEmpty: true,
				isLoading:true,
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
				lat: 0,
				lng: 0,
			}
		},
		async onLoad() {
			var that = this;
			var options = this.$Route.query;
			
			if(options?.index){
				this.searchTypeIndex = options.index;
			}
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
		},
		
		onReachBottom() {
			if(this.currentPage < this.lastPage) {
				this.currentPage += 1;
				this.loadData(false);
			}
		},
		methods: {
			//搜索
			async loadData(isSearch = true){
				var search = this.search,url = '/goods/lists',type =  this.searchTypeIndex;
				if(search == ''){
					this.$xyfun.msg('请输入搜索词');
					return
				}
				
				if(type == 0){
					url = '/goods/goods/lists';
				}
				
				if(type == 1){
					url = '/service/notice/lists';
				}
				if(isSearch){
					this.currentPage = 1;
					this.searchList = [];
				}
				
				this.$api.get({
					url: url,
					loadingTip:'加载中...',
					data: {
						lat: this.lat,
						lng: this.lng,
						search:this.search,
						coach:0,
						page: this.currentPage,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.searchList = [...this.searchList, ...res.data];
						this.isEmpty = !this.searchList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					}
				});
			},
			
			changeType(e){
				this.searchTypeIndex = e.target.value;
				this.currentPage = 1;
				this.searchList = [];
				if(this.search != ''){
					this.loadData()
				}
			}

			
		}
	}
</script>

<style scoped lang="scss">
	.search{
		.search-type{
			height: 64rpx;
			line-height: 64rpx;
		}
		.search-box{
			position: relative;
			width: 485rpx;
			padding-left: 32rpx;
			height: 64rpx;
			border-radius: 32rpx;
			input{width: 100%;height: 64rpx;margin: 0;line-height: 64rpx;}
			.icon-search{position: absolute;right: 32rpx;top: 17rpx;z-index: 100;}
		}
		
	}
</style>
