<template>
	<view class="xy-notice-row">
		<view class="item flex" @tap="$xyfun.to('/pages/service/notice/detail?id='+item.id)">
			<view class="image ovh"><image :src="$xyfun.image(item.image)" class="br-10" /></view>
			<view class="r m-l-30 psr">
				<view class="title tb lh-40">{{item.title}}</view>
				<view class="ba flex psa ts-26" :style="css.tcl">
					<text>{{$xyfun.timeFormat(item.createtime)}}</text>
					<text class="m-l-auto">浏览 {{item.views}}</text>
				</view>
				
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyNoticeRow",
		props: {
			item: {
				type: Object,
			}
		},
		data() {
			return {
				css:this.$xyfun.css(),
			};
		},
	}
</script>
<style lang="scss">
	.xy-notice-row{
		.item{
			flex-wrap: nowrap;
			.name{height: 40rpx;}
			.image{
				flex-shrink: 0;
				width: 160rpx;height: 160rpx;
				image{width: 160rpx;height: 160rpx;}
			}
			.r{
				width: 100%;
				.ba{bottom: 0;left:0;width: 100%;}
			}
		}
	}
</style>
