<template>
	<view class="xy-empty p-50 tc">
		<view class="xyicon icon-empty p-t-50" :style="'font-size:'+size+';'+css.tcl"></view>
		<view class="text-30 margin-top-30 ts-28 m-20 p-b-30" :style="css.tcl">{{ text }}</view>
	</view>
</template>

<script>
	export default {
		name: 'xyEmpty',
		data() {
			return {
				css:this.$xyfun.css()
			}
		},
		props: {
			size: {
				type: String,
				default: '200rpx'
			},
			text: {
				type: String,
				default: '没有找到任何内容'
			}
		}
	};
</script>