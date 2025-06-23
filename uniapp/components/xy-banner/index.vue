<template>
	<view class="xy-banner" :style="{'border-radius': data.params.borderRadius+'rpx','margin':'0 '+ data.params.lrmargin+'rpx','height':data.params.height+'rpx','margin-bottom':data.params.showfloat > 0 ? '140rpx' : 0}">
		<swiper :style="{height: data.params.height+'rpx'}" :indicator-dots="data.params.indicatorDots==1" :circular="true" :autoplay="data.params.autoplay==1" :interval="data.params.interval" :indicator-color="data.params.indicatorColor" :indicator-active-color="data.params.indicatorActiveColor" duration="500">
			<swiper-item v-for="(item, keys) in data.data" :key="keys">
				<view v-if="item.link =='contact'">
					<button open-type="contact"><image :src="$xyfun.image(item.image)" :style="{'height':data.params.height+'rpx','border-radius': data.params.borderRadius+'rpx'}" /></button>
				</view>
				<view v-else-if="item.link == 'share'">
					<button open-type="share"> <image :src="$xyfun.image(item.image)" :style="{'height':data.params.height+'rpx','border-radius': data.params.borderRadius+'rpx'}" /></button>
				</view>
				<view @tap="$xyfun.to(item.link)" v-else>
					<image :src="$xyfun.image(item.image)" :style="{'height':data.params.height+'rpx','border-radius': data.params.borderRadius+'rpx'}" />
				</view>
			</swiper-item>
		</swiper>
		
		<view class="floatimg flex p-lr-30" v-if="data.params.showfloat > 0">
			<image :src="$xyfun.image(data.params.floatimg1)" :style="{'width':(750-data.params.lrmargin-(data.params.showfloat == 3 ? 120 : 90))/data.params.showfloat+'rpx','height':(750-data.params.lrmargin-(data.params.showfloat == 3 ? 120 : 90))/data.params.showfloat+'rpx','margin-right':'30rpx'}" @tap="$xyfun.to(data.params.floatlink1)" />
			<image :src="$xyfun.image(data.params.floatimg2)" :style="{'width':(750-data.params.lrmargin-(data.params.showfloat == 3 ? 120 : 90))/data.params.showfloat+'rpx','height':(750-data.params.lrmargin-(data.params.showfloat == 3 ? 120 : 90))/data.params.showfloat+'rpx','margin-right':data.params.showfloat==2?0:'30rpx'}" @tap="$xyfun.to(data.params.floatlink2)" />
			<image :src="$xyfun.image(data.params.floatimg3)" v-if="data.params.showfloat == 3" :style="{'width':(750-data.params.lrmargin-(data.params.showfloat == 3 ? 120 : 90))/data.params.showfloat+'rpx','height':(750-data.params.lrmargin-(data.params.showfloat == 3 ? 120 : 90))/data.params.showfloat+'rpx','margin-right':'0px'}" @tap="$xyfun.to(data.params.floatlink3)" />
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyBanner",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '',
						type: '',
						params: [],
						style: [],
						data: []
					}
				}
			}
		}
	}
</script>
<style lang="scss">
	.xy-banner{
		position: relative;
		button{background: none;padding: 0;border-radius: 0;}
		swiper image{
			width: 100%;height: 100%;
		}
		
		.floatimg{position: absolute;bottom: -140rpx;left: 0;}
	}
</style>
