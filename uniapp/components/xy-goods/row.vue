<template>
	<view class="xy-goods-row">
		<view class="item flex bl br-20 ovh" :style="css.mbg+css.blpc" @tap="$xyfun.to('/pages/goods/detail?id='+item.id)">
			<view class="l "><image :src="$xyfun.image(item.image)" class="br-10" /></view>
			<view class="r m-l-20 tl">
				<view class="name lh-35 p-r-20">{{item.name}}</view>
				
				<view class="price" :style="css.tcp">
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
				<view class="bottom flex m-t-10">
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
		name: "xyGoodsRow",
		props: {
			item: {
				type: Object,
			}
		},
		mounted() {
			console.log(this.item);
		},
		data() {
			return {
				css:this.$xyfun.css(),
			};
		}
	}
</script>
<style lang="scss">
	.xy-goods-row{
		.item{
			flex-wrap: nowrap;
			.l{
				flex-shrink: 0;
				image{width: 180rpx;height: 180rpx;}
			}
			.r{
				.name{height: 70rpx;text-align: justify;margin-top: 8rpx;}
				width: 100%;
			}
		}
	}
</style>
