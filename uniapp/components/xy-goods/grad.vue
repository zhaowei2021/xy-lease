<template>
	<view class="xy-lease-grad">
		<view class="item br-10 ovh m-b-30" :style="css.mbg" @tap="onLink(item.id)">
			<view class="image ovh"><image :src="item.image" mode="widthFix" /></view>
			<view class="p-lr-15">
				<view class="name m-t-20 ovh">{{item.name}}</view>
				<view class="price m-t-20" :style="css.tcp">
					<view v-if="$xyfun.lease().defaultlease == 'hour'">
						<text class="ts-28">¥</text>
						<text class="tb ts-32 m-r-5">{{item.sku_price[0].hourprice}}</text>
						<text class="ts-28" :style="css.tcm">/小时</text>
					</view>
					<view v-if="$xyfun.lease().defaultlease == 'days'">
						<text class="ts-28">¥</text>
						<text class="tb ts-32 m-r-5">{{item.sku_price[0].daysprice}}</text>
						<text class="ts-28" :style="css.tcm">/天</text>
					</view>
					<view v-if="$xyfun.lease().defaultlease == 'night'">
						<text class="ts-28">¥</text>
						<text class="tb ts-32 m-r-5">{{item.sku_price[0].nightprice}}</text>
						<text class="ts-28" :style="css.tcm">/晚</text>
					</view>
				</view>
				<view class="bottom flex m-tb-20">
					<view v-if="$xyfun.lease().defaultlease == 'hour'">
						<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">{{$xyfun.lease().starthour+' 小时起租'}}</text>
					</view>
					<view v-if="$xyfun.lease().defaultlease == 'days'">
						<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">全日租</text>
					</view>
					<view v-if="$xyfun.lease().defaultlease == 'night'">
						<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">过夜租</text>
					</view>
					<view class="buy m-l-auto"><text class="xyicon icon-cart ts-36 m-r-20" :style="css.tcmc"></text></view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyLeaseGrad",
		props: {
			item: {
				type: Object,
			}
		},
		data() {
			return {
				css:this.$xyfun.css()
			};
		},
		methods: {
			onLink(id){
				this.$xyfun.to('/pages/goods/detail?id='+id);
			}
		}
	}
</script>
<style lang="scss">
	.xy-lease-grad{
		.item{
			.name{height: 80rpx;line-height: 40rpx;}
			width: 330rpx;
			.image{
				width: 330rpx;height: 330rpx;
				image{width: 330rpx;min-height: 330rpx;}
			}
			.price-box{line-height: 36rpx;}
		}
	}
</style>
