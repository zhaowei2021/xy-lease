<template>
	<view class="goods-detail" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="top ovh">
			<swiper :circular="true" :autoplay="false" interval="5000" duration="500">
				<swiper-item v-for="(item, keys) in goods.images" :key="keys" >
					<image :src="item" @tap="$xyfun.pi(keys,goods.images)" />
					<view class="total ts-28"><text>{{keys+1}}</text>/<text>{{goods.images.length}}</text></view>
				</swiper-item>
			</swiper>
		</view>
		
		<view class="info-box m-lr-30 p-b-30 br-10" :style="css.mbg">
			<view class="name-box p-30 flex bl-b" :style="css.blpc">
				<view class="name ts-34 tb flex">
					{{goods.name}}
				</view>
				<view class="r m-l-auto ts-34 " :style="css.tcm">
					<text class="xyicon m-r-30 ts-32 icon-share1" :style="css.tcl" @tap="showShare = !showShare"></text>
					<text class="xyicon icon-favorite" :style="goods.favorite == null || goods.favorite == 0 ? css.tcl : css.tcmc" @tap="onFavorite()"></text>
				</view>
			</view>
			<view class="leasetype-box p-30 flex bl-b" :style="css.blpc">
				<view class="p-t-10">租赁类型</view>
				<view class="m-l-auto flex">
					<view class="item br-10 p-lr-30 p-tb-10 m-l-10 ts-28" :style="leaseType == item ? css.mcbg+'color:#fff' : css.pbg" v-for="(item,index) in $xyfun.lease().leasetype" :key="index" @tap="changegoodsType(item)">
						<text v-if="item=='hour'">{{ $xyfun.lease().starthour + '小时租' }}</text>
						<text v-if="item=='days'">全日租</text>
						<text v-if="item=='night'">隔夜租</text>
					</view>
				</view>
			</view>
			<view class="price-box flex p-t-30 tc">
				<view class="col-3 tb ts-38 lh-100" :style="css.tcp">
					¥<text v-if="leaseType == 'hour'">{{ $xyfun.pea($xyfun.bcmul($xyfun.lease().starthour,goods.sku_price[0].hourprice))}}</text>
					<text v-if="leaseType == 'days'">{{goods.sku_price[0].daysprice}}</text>
					<text v-if="leaseType == 'night'">{{goods.sku_price[0].nightprice}}</text>
				</view>
				<view class="col-4 bl-l" :style="css.blpc">
					<view class="m-t-10 ts-32">¥{{goods.sku_price[0].deposit}}</view>
					<view class="m-t-5 ts-26" :style="css.tcl">押金</view>
				</view>
				<view class="col-4 bl-l" :style="css.blpc">
					<view class="m-t-10 ts-32">{{goods.total_sales}}</view>
					<view class="m-t-5 ts-26" :style="css.tcl">已租</view>
				</view>
				<view class="col-4 bl-l" :style="css.blpc">
					<view class="m-t-10 ts-32">{{goods.views}}</view>
					<view class="m-t-5 ts-26" :style="css.tcl">浏览</view>
				</view>
			</view>
			<view class="bl-t p-lr-30 p-t-30 m-t-30 flex package-item" :style="css.blpc" v-if="goods.type == 'package'">
				<view class="p-t-10">清单配置</view>
				<view class="r m-l-auto ts-26">
					<text v-for="(item,index) in goods.item" :key="index" :style="css.tcl" @tap="$xyfun.to('/pages/goods/detail?id='+item.goods_id)">
						{{item.goodsname}}<text v-if="item.goodsskutext.length">（{{item.goodsskutext}}）</text>*{{item.nums}} <text v-if="index+1 < goods.item.length">、</text>
					</text>
				</view>
			</view>
		</view>
		
		<!-- 规格选择 -->
		<view class="sku-box m-30 flex p-tb-30 p-lr-30 br-10" :style="css.mbg" @tap="user.isLogin ? showSku = true : $xyfun.toLogin()" v-if="goods.issku == 1">
			<view class="l">
				<text class="title m-r-20" :style="css.tcl">规格</text>
				<text class="tip">{{ currentSkuText || '请选择规格' }}</text>
			</view>
			<view class="xyicon icon-right ts-34 m-l-auto" :style="css.tcl"></view>
		</view>
		
		<view class="detail-box m-30 p-t-30 br-10" :style="css.mbg">
			<view class="tab flex tc tb " :style="css.mbg">
				<view v-for="(item,index) in detailBoxTab" :key="index" :class="'col-'+detailBoxTab.length" @tap="setDetailBoxTab(index)">
					<view :style="detailBoxTabIndex == index ? css.tcmc : css.tcm">
						{{item}}
						<view class="line m-t-10" :style="detailBoxTabIndex == index ? css.mcbg : css.mbg"></view>
					</view>
				</view>
			</view>
			
			<view class="p-25 content" v-if="detailBoxTabIndex == 0">
				
				<view class="plist flex" :style="css.blpc" v-if="goods.type == 'package'">
					<view class="item m-b-30" v-for="(item,index) in goods.item" :key="index" :style="css.tcl" @tap="$xyfun.to('/pages/goods/detail?id='+item.goods_id)">
						<view class="image m-b-10">
							<image :src="item.goodsimage" class="br-10" />
						</view>
						<view>
							{{item.goodsname}}<text v-if="item.goodsskutext.length">（{{item.goodsskutext}}）</text>*{{item.nums}}
						</view>
					</view>
				</view>
				
				<rich-text :nodes="goods.content"></rich-text>
			</view>
			
			<view class="p-25 rule" v-if="detailBoxTabIndex == 1">
				<rich-text :nodes="goods.lease_rule.content"></rich-text>
			</view>
		</view>
		
		<view class="bottom-action bottom-fixed flex tc" :style="css.tcmc+css.mbg">
			<view class="p-lr-30 p-tb-15 flex tc wa">
				<button class="home m-r-25 tc lh-32" @tap="$xyfun.to('/pages/index',true)" :style="css.tcl">
					<text class="xyicon icon-home ts-36 lh-36"></text>
					<view class="text ts-26 m-t-10 lh-28">首页</view>
				</button>
				<button class="kefu m-r-25 tc lh-32" :style="css.tcl" @tap="$xyfun.phone(common.storeInfo.phone)">
					<text class="xyicon icon-kefu ts-36 lh-36"></text>
					<view class="text ts-26 m-t-10 lh-28">客服</view>
				</button>
				<button class="kefu psr tc lh-32" :style="css.tcl" @tap="$xyfun.to('/pages/cart');">
					<text class="xyicon icon-cart ts-36 lh-36"></text>
					<view class="psa cart-num ts-24 lh-24 tc-w" :style="css.prbg" v-if="cart.cartNum > 0">{{cart.cartNum}}</view>
					<view class="text ts-26 m-t-10 lh-28">租物车</view>
				</button>
				
				<view class="action flex m-l-auto">
					<view class="add-cart ts-28 m-r-20 tc-w br-10" :style="css.mcbg" @tap="addCart">
						加入租物车
					</view>
					<view class="buy ts-28 m-l-auto tc-w br-10" :style="css.mcbg" @tap="goBuy">
						立即租
					</view>
				</view>
				
			</view>
		</view>
		
		<!-- 规格弹窗 -->
		<xy-goods-sku
			v-if="showSku && goods.id"
			v-model="showSku"
			:goodsInfo="goods"
			:buyType="buyType"
			:ltype="leaseType"
			:goodsType="goods.type"
			@changeType="changeType"
			@getSkuText="getSkuText"
		/>
		
		<!--分享组件 -->
		<xy-share v-model="showShare" posterType="goods" :posterInfo='goods' />
		
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import xyEmpty from '@/components/xy-empty/index';
	import xyGoodsSku from '@/components/xy-goods/sku';
	import xyShare from '@/components/xy-share';
	import share from '@/utils/share';
	export default {
		components: {
			xyEmpty,
			xyGoodsSku,
			xyShare
		},
		data() {
			return {
				css:{},
				isLoading:true,
				id: 0,
				goods:{},
				showSku:false,
				buyType: 'buy',
				leaseType:'', // 租赁类型
				detailBoxTab:[
					'商品介绍',
				],
				detailBoxTabIndex:0,
				currentSkuText:'',
				showShare:false
			}
		},
		computed: {
			...mapState(['common','user','cart'])
		},
		onLoad() {
			var options = this.$Route.query;
			// #ifdef MP
			if (options?.scene) {
				options = this.$xyfun.sceneDecode(options.scene);
			}
			// #endif
			if(options?.id){
				this.id = options.id;
			}
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.loadData();
		},
		onUnload() {
			share.setShareInfo();
		},
		methods: {
			
			loadData(){
				this.$api.get({
					url: '/goods/goods/detail',
					loadingTip:'加载中...',
					data: {
						id:this.id,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.goods = res;
						if(res.type == 'single' || res.type == 'package'){
							this.leaseType = this.$xyfun.lease().defaultlease;
							this.detailBoxTab = [ res.type =='package' ? '套餐介绍':'商品介绍','租赁规则'];
						}
						if(res.type == 'service'){
							this.detailBoxTab = ['服务详情'];
						}
						
						this.goods.content = this.goods.content.replace(/\<img/g, "<img style='max-width: 100%;vertical-align: middle;'");
						share.setShareInfo({
							title: res.name,
							desc: res.name,
							image: res.image,
							params: {
								page: 2,
								pageId: this.id
							}
						});
					}
				});
			},
			
			setDetailBoxTab(index){
				this.detailBoxTabIndex = index;
			},
			
			// 收藏
			onFavorite() {
				if (this.user.isLogin) {
					this.$api.post({
						url: '/user/favorite/add',
						data: {
							target_id:this.id,
							type:'goods'
						},
						success: res => {
							this.goods.favorite = res;
							this.$xyfun.msg(res?'收藏成功' : '取消收藏');
						}
					});
				} else {
					this.$xyfun.toLogin();
				}
			},
			
			// 加入购物车
			addCart() {
				if (this.user.isLogin) {
					this.buyType = 'cart';
					this.showSku = true;
				} else {
					this.$xyfun.toLogin();
				}
			},
			
			// 立即购买
			goBuy() {
				if (this.user.isLogin) {
					this.buyType = 'buy';
					this.showSku = true;
				} else {
					this.$xyfun.toLogin();
				}
			},
			
			// 组件返回的type;
			changeType(e) {
				this.buyType = e;
			},
			
			// 组件返回的规格;
			getSkuText(e) {
				this.currentSkuText = e;
			},
			
			// 切换租赁类型
			changegoodsType(leaseType){
				this.leaseType = leaseType;
			}
			
		}
	}
</script>

<style scoped lang="scss">
	.goods-detail{
		padding-bottom: 200rpx;
		padding-top: 720rpx;
		.top{position: absolute;left: 0;top: 0;}
		.top,.top swiper{
			width: 750rpx;height: 750rpx;
			image{
				width: 750rpx;min-height: 750rpx;
			}
			.total{
				color: #fff;
				background: rgba(0, 0, 0, 0.5);
				position: absolute; right: 30rpx; bottom: 60rpx;
				padding: 3rpx 20rpx;border-radius: 20rpx;
				text{margin: 0 5rpx;}
			}
		}
		
		.info-box{
			z-index: 8;width: 690rpx;position: relative;
			.name{
				width: 526rpx;flex-wrap: nowrap;
			}
			.ss{
				.item{width: 33%;}
			}
		}
		
		.plist{
			.item{width: 300rpx;}
			.item image{width: 300rpx;height: 300rpx;}
			.item:nth-child(2n){margin-left: auto;}
		}
		
		.detail-box{
			.line{
				height: 2px;width: 30%;margin-left: 35%
			}
			.package{
				.item{
					image{width: 300rpx;height: 300rpx;}
				}
				.item:nth-child(2n){margin-right: 0;}
			}
		}
		
		.price-box{
			flex-wrap: nowrap;
		}
		
		.bottom-fixed{
			button{background: none;padding: 0;margin-left: 0;}
			button::after{border: none;}
			
			.cart-num{right: 0rpx;top: 0rpx;border-radius: 50%;padding: 4rpx;min-width: 24rpx;z-index: 100;min-height: 24rpx;}
			.buy,.add-cart{width: 200rpx;height: 72rpx;line-height: 72rpx;}
		}
		
		.package-item{
			.r{width: 470rpx;}
		}
		
		.bottom-action{
			z-index: 10;
		}
	}
	
</style>
