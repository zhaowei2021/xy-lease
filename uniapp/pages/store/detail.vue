<template>
	<view class="store-detail" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="top ovh">
			<swiper :circular="true" :autoplay="false" interval="5000" duration="500">
				<swiper-item v-for="(item, keys) in storeInfo.images" :key="keys" >
					<image :src="item" @tap="$xyfun.pi(keys,storeInfo.images)" />
					<view class="total ts-28 tc-w"><text>{{keys+1}}</text>/<text>{{storeInfo.images.length}}</text></view>
				</swiper-item>
			</swiper>
		</view>
		<view class="info-box m-30 p-tb-30 p-lr-30 br-10" :style="css.mbg">
			<view class="name-box flex">
				<view class="name m-r-20 ts-32 tb">
					{{storeInfo.name}}
				</view>
			</view>
			<view class="open-box m-t-30 flex ts-28" :style="css.tcl">
				<view class="l">
					<view><text class="xyicon icon-time m-r-10"></text> 开放时间：{{storeInfo.businesshours}}</view>
					<view class="m-t-15 address">
						<text class="xyicon icon-loc m-r-10"></text>
						{{storeInfo.address}}
					</view>
				</view>
				<view class="r m-l-auto tc m-t-10" @tap="$xyfun.openLoc(storeInfo.latitude,storeInfo.longitude,storeInfo.name,storeInfo.address)">
					<view class="xyicon icon-dh ts-40 m-t-20" :style="css.tcmc"></view>
				</view>
			</view>
		</view>
		
		<view class="service-box m-30 p-t-30 p-lr-30 br-10" :style="css.mbg" v-if="storeInfo.service && storeInfo.service.length">
			<view class="ts-32 tb flex lh-32"><view class="line m-r-10" :style="css.mcbg"></view>基础服务</view>
			<view class="service-list m-t-30 flex">
				<view class="item tc m-b-30" v-for="(item,index) in storeInfo.service" :key="item.id">
					<view><image :src="item.logoimage" /></view>
					<view class="ts-28 m-t-10">{{item.name}}</view>
				</view>
			</view>
		</view>
		
		<view class="service-box m-30 p-t-30 p-lr-30 br-10" :style="css.mbg" v-if="storeInfo.videofile">
			<view class="ts-32 tb flex lh-32"><view class="line m-r-10" :style="css.mcbg"></view>介绍视频</view>
			<view class="service-list m-t-30 p-b-20">
				<video :src="$xyfun.image(storeInfo.videofile)" style="width: 100%;"></video>
			</view>
		</view>
		
		<view class="content m-30 p-tb-30 p-lr-30 br-10" :style="css.mbg">
			<view class="ts-32 tb flex lh-32"><view class="line m-r-10" :style="css.mcbg"></view>门店介绍</view>
			<view class="venue-list m-t-30 flex">
				<rich-text :nodes="storeInfo.content"></rich-text>
			</view>
		</view>
		
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	export default {
		
		data() {
			return {
				css:{},
				isLoading:true,
				storeInfo:{},
			}
		},
		computed: {
			...mapState(['user'])
		},
		onPullDownRefresh() {
			this.loadData();
		},
		onShareAppMessage() {
			var shareobj = {
				title: this.storeInfo.name,
				path: '/pages/index',
				imageUrl: this.storeInfo.images[0]
			}
			return shareobj;
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			loadData(){
				this.$api.get({
					url: '/store/store/detail',
					loadingTip:'加载中...',
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.storeInfo = res;
					}
				});
			}
		}
	}
</script>

<style scoped lang="scss">
	.store-detail{
		padding-bottom: 120rpx;
		.line{width: 6rpx;border-radius: 5rpx;height: 30rpx;}
		.top,.top swiper{
			width: 750rpx;height: 500rpx;position: relative;position: relative;
			image{
				width: 750rpx;height: 500rpx;
			}
			.total{
				background: rgba(0, 0, 0, 0.5);
				position: absolute; right: 30rpx; bottom: 30rpx;
				padding: 3rpx 15rpx;border-radius: 20rpx;
				text{margin: 0 10rpx;}
			}
		}
		.info-box{
			.name image{height: 34rpx;vertical-align: middle;}
			.open-box{
				.l{
					width:495rpx;
					.address{height: 30rpx;line-height: 30rpx;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;-o-text-overflow:ellipsis;}
				}
			}
		}
		
		.service-box{
			.item{
				width: 25%;
				image{width: 60rpx;height: 60rpx;}
			}
			
		}
		
		.venue-list{
			.item{
				width: 300rpx;
			}
			.item:nth-child(2n){
				margin-right: 0;
				margin-left: auto;
			}
		}
	}
</style>
