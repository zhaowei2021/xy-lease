<template>
	<view class="store-info" :style="styleStr">
		<view class="br-10">
			<view class="top flex" @tap="$xyfun.to('/pages/store/detail')">
				<view class="logo m-r-20">
					<image :src="$xyfun.image(data.logo)" class="br-10" />
				</view>
				<view>
					<view class="ts-32 tb flex">
						{{data.name}}
					</view>
				</view>
				<view class="m-l-auto tn ts-30" :style="css.tcmc">
					{{$xyfun.distance($xyfun.algorithm([data.latitude,data.longitude],[lat,lng]),true)}}
				</view>
			</view>
			<view class="address ovh m-t-30">
				<text class="xyicon icon-loc m-r-10"></text> {{data.address}}
			</view>
			<view class="time flex m-t-30">
				<view class="h ts-32">开放时间：{{data.businesshours}}</view>
				<view class="m-l-auto flex" :style="css.tcl">
					<view class="weixin tc m-r-30" @tap="$xyfun.phone(data.phone)">
						<view class="c" :style="css.mcbg"><text class="xyicon icon-phone tc-w"></text></view>
						<view class="ts-24 h-24 m-t-5">咨询</view>
					</view>
					<view class="weixin tc m-r-30" @tap="$xyfun.copy(data.weixin,'已复制微信号，去添加好友吧')">
						<view class="c" :style="css.mcbg"><text class="xyicon icon-weixin tc-w"></text></view>
						<view class="ts-24 h-24 m-t-5">微信</view>
					</view>
					<view class="weixin tc"  @tap="$xyfun.openLoc(data.latitude,data.longitude,data.name,data.address)">
						<view class="c" :style="css.mcbg"><text class="xyicon icon-dh tc-w"></text></view>
						<view class="ts-24 h-24 m-t-5">导航</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyStore",
		props: {
			data: {
				type: Object,
			},
			lat:{
				type:String,
			},
			lng:{
				type:String,
			}
		},
		data() {
			return {
				css:this.$xyfun.css(),
				styleStr:''
			};
		},
		created() {
			this.setStyle();
		},
		methods: {
			setStyle(){
				console.log(this.data,this.data.bgColor)
				if(this.data.bgColor != undefined){
					this.styleStr = 'background:'+this.data.bgColor+';border-radius:'+this.data.borderRadius+'rpx'+';margin:0 '+ this.data.lrmargin+'rpx;'+'padding:'+this.data.njj+'rpx';
				}
			}
		}
	}
</script>
<style lang="scss">
	.store-info{
		.top{line-height: 80rpx;}
		.logo,.logo image{width: 80rpx;height: 80rpx;line-height: 80rpx;}
		.time{
			.h{line-height: 79rpx;}
			.c{
				width: 50rpx;
				height: 50rpx;
				line-height: 50rpx;
				border-radius: 25rpx;
			}
		}
	}
</style>
