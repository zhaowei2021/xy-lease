<template>
	<view class="xy-image" :style="{'margin':'0 '+ data.params.lrmargin+'rpx'}">
		<view class="image" :class="[`layout-${data.params.imgLayout}`]">
			<view class="item" v-for="(image, index) in data.data" :key="index">
				<block v-if="image.link == 'share'">
					<button open-type="share">
						 <image :src="$xyfun.image(image.image)" mode="widthFix" :style="{'borderRadius': data.params.imgRadius+'rpx'}" />
					</button>
				</block>
				<block v-else-if="image.link == 'contact'">
					<button open-type="contact">
						 <image :src="$xyfun.image(image.image)" mode="widthFix" :style="{'borderRadius': data.params.imgRadius+'rpx'}" />
					</button>
				</block>
				<block v-else>
					<image :src="$xyfun.image(image.image)" mode="widthFix" @tap="onLink(image.link)" :style="{'borderRadius': data.params.imgRadius+'rpx'}" />
				</block>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyImage",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '图片橱窗',
						type: 'image',
						params: [],
						data: []
					}
				}
			}
		},
		methods:{
			async onLink(url){
				this.$xyfun.to(url);
			}
		}
	}
</script>
<style lang="scss">
	.xy-image {
		image{width: 100%;}
		.image {
			display: flex;
			overflow: hidden;
			.item{
				display: flex;
				button{
					background: none; padding: 0;margin: 0;width: 100%;
				}
				button::after{border: none;}
			}
			&.layout-1 {
				display: block;
			}
			&.layout-2 {
				flex-wrap: wrap;

				&>view {
					width: 48.5%;
				}
				&>view:nth-child(2n){margin-left: auto;}
			}
			&.layout-3 {
				display: block;
				&>view {
					width: 48.5%;float: left;
				}
				&>view:nth-child(2),&>view:nth-child(3){float: right;}
				&>view:nth-child(3){margin-top: 20rpx;}
			}
			&.layout-4 {
				display: block;
				&>view {
					width: 48.5%;float: left;
				}
				&>view:nth-child(2){float: right;}
				&>view:nth-child(3){margin-top: 20rpx;}
			}
			&.layout-5 {
				display: block;
				&>view {
					width: 25%;float: left;
				}
			}
		}
	}
</style>
