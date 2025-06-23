<template>
	<view class="user p-b-40" :style="pageStyle+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view v-for="(item, index) in itemList" :key="index">
			<!-- 搜索 -->
			<xy-search v-if="item.type == 'search'" :data="item" />
			<!-- 轮播 -->
			<xy-banner v-if="item.type == 'banner'" :data="item" />
			<!-- 公告 -->
			<xy-notice v-if="item.type == 'notice'" :data="item" />
			<!-- 菜单 -->
			<xy-menu v-if="item.type == 'menu'" :data="item" />
			<!-- 商品 -->
			<xy-goods v-if="item.type == 'goods'" :data="item" />
			<!-- 图片 -->
			<xy-image v-if="item.type == 'image'" :data="item" />
			<!-- 视频 -->
			<xy-video v-if="item.type == 'video'" :data="item" />
			<!-- 标题 -->
			<xy-title v-if="item.type == 'title'" :data="item" /> 
			<!-- 空白行 --> 
			<view v-if="item.type == 'empty'" :style="'height:'+item.params.height+'rpx'" />
			<!-- 门店 -->
			<xy-store v-if="item.type == 'store'" :data="item.params" :lat="lat" :lng="lng" /> 
			<!-- 文字 -->
			<xy-text v-if="item.type == 'text'" :data="item" />
			<!-- 用户卡片 -->
			<xy-user-card v-if="item.type == 'user-card'" :data="item" v-on:showEwm="showEwm" />
			<!-- 钱包模块 -->
			<xy-user-wallet v-if="item.type == 'user-money'" :data="item" />
		</view>
		<xy-tabbar />
		<!-- 二维码弹窗 -->
		<block v-if="ewmModelShow">
			<view class="xy-modal-box bottom-fixed xy-modal-box-bottom ewm-model-box ovh" :style="css.pbg" :class="[ewmModelShow?'xy-modal-show':'']">
				<view class="title p-tb-50 tb tc" :style="css.mbg">我的会员二维码</view>
				<view class="ts-26 tc m-t-50" :style="css.tcl">点击二维码刷新</view>
				<view class="tc ewm m-50" @tap="showEwm()"><img :src="qrcodepath" /></view>
				<view class="ts-26 tc m-t-50" :style="css.tcl">出示会员码给员工扫码核销</view>
				<view class="close ts-40" :style="css.tcl" @tap="ewmModelShow = false"><text class="xyicon icon-close"></text></view>
			</view>
			<view class="xy-modal-mask" :class="[ewmModelShow?'xy-mask-show':'']" @tap="ewmModelShow =!ewmModelShow"></view>
		</block>
		<!-- 二维码绘制 -->
		<view class="hide-canvas">
			<canvas canvas-id="qrcode" style="width: 300px;height: 300px;"></canvas>
		</view>
	</view>
</template>
<script>
	import { mapState } from 'vuex';
	import xySearch from '@/components/xy-search';
	import xyBanner from '@/components/xy-banner';
	import xyNotice from '@/components/xy-notice';
	import xyMenu from '@/components/xy-menu';
	import xyImage from '@/components/xy-image';
	import xyVideo from '@/components/xy-video';
	import xyTitle from '@/components/xy-title';
	import xyTabbar from '@/components/xy-tabbar';
	import xyText from '@/components/xy-text';
	import xyStore from '@/components/xy-store';
	import xyGoods from '@/components/xy-goods';
	import xyUserCard from '@/components/xy-user/card';
	import xyUserWallet from '@/components/xy-user/wallet';
	import uQRCode from '@/utils/uqrcode';
	export default {
		components: {
			xyTabbar,
			xySearch,
			xyBanner,
			xyNotice,
			xyMenu,
			xyImage,
			xyVideo,
			xyTitle,
			xyStore,
			xyText,
			xyGoods,
			xyUserCard,
			xyUserWallet,
		},
		data() {
			return {
				css: {},
				isLoading:true,
				itemList:[],
				pageStyle:{},
				ewmModelShow:false,
				qrcodepath:'',
			}
		},
		onPullDownRefresh() {
			this.loadData()
		},
		onLoad() {
			this.css = this.$xyfun.css();
			this.loadData();
		},
		computed: {
			...mapState(['user'])
		},
		methods: {
			loadData(){
				this.$api.get({
					url: '/common/template',
					loadingTip:'加载中...',
					data: {
						type:'user',
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.itemList = res.item;
						this.pageStyle = this.$xyfun.tcss(res.page)
						this.$xyfun.setNavBar(res.page.params.navigationBarTitleText,res.page.style.navigationBarBackgroundColor,res.page.style.navigationBarTextStyle);
					}
				});
			},
			
			showEwm(){
				if(this.user.isLogin){
					var text = this.user.info.id+"_"+Date.parse(new Date())/1000;
					uni.showLoading({
						title:'二维码绘制中...'
					})
					uQRCode.make({
						canvasId: 'qrcode',
						text: text,
						size: 300,
						success: res => {
							uni.hideLoading();
							this.qrcodepath = res;
							this.ewmModelShow = true;
						},
						fail: res => {
							console.log(res);
						}
					});
				}else{
					this.$xyfun.toLogin();
				}
			},
		}
	}


</script>

<style scoped lang="scss">
	.ewm-model-box{
		height: 900rpx;border-radius: 30rpx 30rpx 0 0;
		.ewm image{ width: 300rpx;height: 300rpx;}
		.close{position: absolute; right:30rpx;top: 30rpx;}
	}
</style>