<template>
	<view class="xy-notice flex m-lr-30 p-lr-20" :style="{'border-radius': data.params.borderRadius+'rpx','background':data.params.bgColor}">
		<view class="l m-r-10" @tap="$xyfun.to('/pages/service/notice/list')">
			<image :src="$xyfun.image(data.params.lefticon)" />
		</view>
		<view class="c bl-l m-l-20" :style="css.bl" >
			<swiper autoplay="true" interval="5000" duration="500" :vertical="data.params.scroll == 'tb'">
				<swiper-item v-for="(item, keys) in data.data" :key="keys" @tap="$xyfun.to('/pages/service/notice/detail?id='+item.id)">
					<view class="notice-title m-l-25">{{item.title}}</view>
				</swiper-item>
			</swiper>
		</view>
		<view class="r m-l-auto" :style="css.tcl" @tap="$xyfun.to('/pages/service/notice/list')">
			<text class="xyicon icon-right"></text>
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyNotice",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '公告组件',
						type: 'notice',
						params: [],
					}
				}
			}
		},
		data() {
			return {
				css:this.$xyfun.css(),
			};
		},
		methods: {
			
		}
	}
</script>
<style lang="scss">
	.xy-notice {
		.l{
			width: 40rpx;
			image{width: 40rpx;height: 40rpx;margin-top: 23rpx;}
		}
		.c{
			width: 500rpx;
			height: 36rpx;line-height: 36rpx;margin-top: 25rpx;
			swiper{height: 36rpx;line-height: 36rpx;}
			.notice-title{height: 36rpx;line-height: 36rpx;width: 470rpx;text-overflow:ellipsis;white-space:nowrap;overflow: hidden;}
		}
		.r{height: 86rpx;line-height: 86rpx;}
	}
</style>
