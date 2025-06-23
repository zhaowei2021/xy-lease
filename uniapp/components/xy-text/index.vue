<template>
	<view class="xy-text">
		<view :style="{'color':data.params.textColor,'font-size':data.params.textSize+'rpx','text-align':data.params.textAlign,'margin':'0 '+data.params.lrmargin+'rpx'}">{{data.params.textContent}}</view>
	</view>
</template>
<script>
	export default {
		name: "xyText",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '文字组件',
						type: 'text',
						params: [],
						data: []
					}
				}
			}
		},
		methods:{
			
		}
	}
</script>
<style lang="scss">
	.xy-text {
		height: 36rpx;line-height: 36rpx;
		.l{width: 6rpx;border-radius: 3rpx;}
		.r{margin-left: auto;}
	}
	button{
		background: none; padding: 0;margin: 0;
	}
	button::after{border: none;}
</style>
