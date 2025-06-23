<template>
	<view class="goodCate" :style="css.page">
		<view class="conter psr" :style="'height:'+($xyfun.xysys().windowHeight-10) +'px'">
			<view class='aside'>
				<scroll-view scroll-y="true" scroll-with-animation='true'>
					<view class='item flex p-tb-20 tc'v-for="(item,index) in categoryList" :style="index==navActive?'background:#fff':''" :key="index" @click="tapNav(index,item)">
						<view class="line m-r-10" :style="index==navActive?css.mcbg:{backgroundColor:'#f7f7f7'}"></view> <text>{{item.name}}</text>
					</view>
				</scroll-view>
			</view>
			
			<view class="wrapper">
				<view class="two-cate p-lr-25 flex" v-if="categoryErList.length>1">
					<view class="item tc ts-26" :style="index===tabClick?css.mcbg+';color:#fff':'background:#f7f7f7'"
						v-for="(item,index) in categoryErList" :key="index" @click="longClick(index)">
						{{item.name}}
					</view>
				</view>
				
				<view class="goods-list p-lr-25 m-t-25">
					<scroll-view :style="'height:'+($xyfun.xysys().windowHeight - Math.ceil((categoryErList.length)/3)*40 - 25) +'px'" scroll-top="scrollTop" class="goods-list-scroll" show-scrollbar="false" scroll-y="true" scroll-with-animation='true' @scrolltolower="lower" v-if="goodsList.length">
									
						<view class="item tc p-b-30 ts-26" v-for="(item,index) in goodsList" :key="index">
							<xy-goods-row :item="item" />
						</view>
									
					</scroll-view>
					<view v-if="!isLoading && isEmpty">
						<xy-empty :text="'暂无商品'" />
					</view>
				</view>
			</view>
		</view>
		<xy-tabbar />
	</view>
</template>
<script>
	import { mapState } from 'vuex';
	import xyGoodsRow from '@/components/xy-goods/row';
	import xyTabbar from '@/components/xy-tabbar';
	import xyEmpty from '@/components/xy-empty';
	export default {
		components: {
			xyGoodsRow,
			xyTabbar,
			xyEmpty
		},
		data() {
			return {
				css:{},
				images:['https://toss.xyunku.com/uploads/20240122/2b209866c0d526f4502de8f9d648c387.png'],
				isEmpty:true,
				isLoading:true,
				currentPage: 1,
				categoryList: [],
				navActive: 0,
				tabClick:0,
				categoryTitle: '',
				categoryErList: [],
				goodsList: [],
				isLoading:true,
				scrollTop:0,
				lastPage: 1,
				loadStatus: 'loadmore',
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad() {
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.loadData()
		},
		methods: {
			loadData(){
				var that = this;
				this.$api.get({
					url: '/goods/category/lists',
					success: (data) => {
						data.forEach(item => {
							item.children.unshift({
								'id': 0,
								'name': '全部'
							})
						})
						if(data[0]){
							that.categoryTitle = data[0].name;
							that.cid = data[0].id;
							
						}
						that.sid = 0;
						that.navActive = 0;
						that.tabClick = 0;
						that.categoryList = data;
						that.categoryErList = data[0] && data[0].children ? data[0].children : [];
						that.currentPage = 1;
						that.goodsList = [];
						that.getGoodsList();
					}
				});
			},
			
			lower(){
				if(this.currentPage < this.lastPage) {
					this.currentPage += 1;
					this.getGoodsList();
				}
			},
			// 产品列表
			getGoodsList: function() {
				var that = this;
				
				this.$api.get({
					url: '/goods/goods/lists',
					loadingTip:'加载中...',
					data: {
						page: that.currentPage,
						category_id: that.sid ? that.sid : that.cid,
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
			
			tapNav(index, item) {
				var list = this.categoryList[index];
				this.isLoading = true;
				this.scrollTop = 0;
				this.navActive = index;
				this.categoryTitle = list.cate_name;
				this.categoryErList = item.children ? item.children : [];
				this.tabClick = 0;
				this.cid = list.id;
				this.sid = 0;
				this.currentPage = 1;
				this.goodsList = [];
				uni.pageScrollTo({
					scrollTop: this.scrollTop,
					duration: 300
				});

				this.getGoodsList();
			},
			
			// 导航栏点击
			longClick(index) {
				this.tabClick = index; //设置导航点击了哪一个
				this.sid = this.categoryErList[index].id;
				this.currentPage = 1;
				this.goodsList = [];
				this.getGoodsList();
			},
		}
	}
</script>

<style scoped lang="scss">
	
	page {
		height: 100vh;
		overflow: hidden;
	}
	
	scroll-view ::-webkit-scrollbar {  
	    display: none !important;  
	    width: 0 !important;  
	    height: 0 !important;  
	    -webkit-appearance: none;  
	    background: transparent;  
	}
	
	.header{
		flex-wrap: nowrap;
		.search-box{
			.search-input{
				position: relative;
				width: 420rpx;
				padding: 0 30rpx;
				height: 32px;border-radius: 16px;line-height: 32px;
			}
			input{width: 100%;margin: 0;height: 100%;width: 100%;}
			.icon-search{position: absolute;right: 30rpx;top: 18rpx;}
		}
	}
	
	.conter {
		background-color: #fff;
		overflow: hidden;
		.aside {
			background-color: #f7f7f7;
			position: absolute;
			width: 23%;
			left: 0;
			bottom: 0;
			top: 0;
			overflow-y: auto;
			overflow-x: hidden;
			z-index: 99;
			padding-bottom: 140rpx;
	
			.item {
				height: 60rpx;
				line-height: 60rpx;
				width: 100%;
				.line{width: 6rpx;height: 60rpx;}
			}
		}
		
		.goods-list-scroll{height: 100vh;}
	
		.wrapper {
			width: 77%;
			float: right;
	
			.two-cate {
	
				.item {
					width: 24%;
					padding: 0 3%;
					margin-right: 5%;
					height: 27px;
					line-height: 27px;
					margin-top: 13px;
					overflow: hidden;
					border-radius: 27rpx;
				}
				.item:nth-child(3n){margin-right: 0;}
				
			}
		}
	}
</style>
