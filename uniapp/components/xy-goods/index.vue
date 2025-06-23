<template>
	<view class="xy-goods" :style="{'backgroundColor':data.params.bgColor,'border-radius': data.params.borderRadius+'rpx','margin':'0 '+ data.params.lrmargin+'rpx',padding:data.params.njj+'rpx'}">
		<view class="title flex" v-if="data.params.title" :style="{color:data.params.textColor}">
			<view class="l tb">{{data.params.title}}</view>
			<view class="r ts-26 flex m-l-auto" :style="css.tcl" v-if="data.params.linktitle" @tap="$xyfun.to(data.params.link)">
				{{data.params.linktitle}}
				<text class="xyicon icon-right ts-34"></text>
			</view>
		</view>
		<div class="list" v-if="data.params.showStyle == '1'">
			<view class="item flex bl ovh" v-for="item in data.data" :key="item.id" :style="'margin-top:'+data.params.njj+'rpx;border-radius:'+data.params.itemBorderRadius+'rpx;background-color:'+data.params.itemBgColor+';'+css.blpc" @tap="$xyfun.to('/pages/goods/detail?id='+item.id)">
				<view class="l "><image :src="$xyfun.image(item.image)" :style="{'border-radius':data.params.itemBorderRadius+'rpx'}" /></view>
				<view class="r m-l-20 tl">
					<view class="name lh-35 p-r-20">{{item.name}}</view>
					
					<view class="price" :style="css.tcp">
						<block v-if="item.type == 'sell' || item.type == 'service'">
							<text class="ts-28">¥</text>
							<text class="tb ts-32 m-r-5">{{item.sku_price[0].price}}</text>
						</block>
						<block v-else>
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
						</block>
					</view>
					<view class="bottom flex m-t-10">
						<block v-if="item.type == 'service'">
							<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">管家服务</text>
						</block>
						<block v-else-if="item.type == 'sell'">
							<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">零售商品</text>
						</block>
						<block v-else>
							<view v-if="$xyfun.lease().defaultlease == 'hour'">
								<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">{{$xyfun.lease().starthour+' 小时起租'}}</text>
							</view>
							<view v-if="$xyfun.lease().defaultlease == 'days'">
								<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">全日租</text>
							</view>
							<view v-if="$xyfun.lease().defaultlease == 'night'">
								<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">过夜租</text>
							</view>
						</block>
						<view class="buy m-l-auto"><text class="xyicon icon-cart ts-36 m-r-20" :style="css.tcmc"></text></view>
					</view>
				</view>
			</view>
			
		</div>
		<div class="list-grad flex" v-if="data.params.showStyle == '2'">
			<view class="item bl ovh" v-for="item in data.data" :key="item.id" :style="'margin-top:'+data.params.njj+'rpx;border-radius:'+data.params.itemBorderRadius+'rpx;background-color:'+data.params.itemBgColor+';'+css.blpc+'width:'+(750-data.params.lrmargin*2 - data.params.njj*2 - data.params.itemjj)/2+'rpx'" @tap="$xyfun.to('/pages/goods/detail?id='+item.id)">
				<view class="image"><image :src="$xyfun.image(item.image)" :style="{'width':(750-data.params.lrmargin*2 - data.params.njj*2 - data.params.itemjj)/2+'rpx','height':(750-data.params.lrmargin*2 - data.params.njj*2 - data.params.itemjj)/2+'rpx','border-radius':data.params.itemBorderRadius+'rpx','background-color':data.params.itemBgColor}" /></view>
				
				<view class="p-20">
					<view class="name ovh">{{item.name}}</view>
					<view class="price m-t-20" :style="css.tcp">
						<block v-if="item.type == 'sell' || item.type == 'service'">
							<text class="ts-28">¥</text>
							<text class="tb ts-32 m-r-5">{{item.sku_price[0].price}}</text>
						</block>
						<block v-else>
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
						</block>
					</view>
					<view class="bottom flex m-t-20">
						<block v-if="item.type == 'service'">
							<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">管家服务</text>
						</block>
						<block v-else-if="item.type == 'sell'">
							<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">零售商品</text>
						</block>
						<block v-else>
							<view v-if="$xyfun.lease().defaultlease == 'hour'">
								<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">{{$xyfun.lease().starthour+' 小时起租'}}</text>
							</view>
							<view v-if="$xyfun.lease().defaultlease == 'days'">
								<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">全日租</text>
							</view>
							<view v-if="$xyfun.lease().defaultlease == 'night'">
								<text :style="css.mcbg" class="ts-24 tc-w br-10 p-tb-5 p-lr-15">过夜租</text>
							</view>
						</block>
						<view class="buy m-l-auto"><text class="xyicon icon-cart ts-36 m-r-20" :style="css.tcmc"></text></view>
					</view>
				</view>
				
			</view>
		</div>
	</view>
</template>
<script>
	import xyGoodsRow from './row';
	export default {
		name: "xyGoods",
		components: {
			xyGoodsRow,
		},
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '商品组件',
						type: 'goods',
						params: [],
						data: []
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
	.list{
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
	
	.list-grad{
		.item{
			.name{height: 80rpx;line-height: 40rpx;}
		}
		.item:nth-child(2n){margin-left: auto;}
	}
</style>
