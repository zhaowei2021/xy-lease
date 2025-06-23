<template>
	<view class="xy-search flex ovh" :style="{'background':data.params.bgColor,'height':data.params.height+'rpx','line-height':data.params.height+'rpx','margin':'0 '+data.params.lrmargin+'rpx','border-radius':data.params.borderRadius+'rpx;padding: 0 30rpx'}" @tap="$xyfun.to('/pages/search')">
		<view :style="css.tcl">{{data.params.tiptext}}</view>
		<text class="xyicon icon-search m-l-auto" :style="css.tcmc"></text>
	</view>
</template>
<script>
	export default {
		name: "xySearch",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '搜索组件',
						type: 'search',
						params: [],
					}
				}
			},
			type:{
				type:String,
				default:'store'
			},
			shopData: {
				type: Object,
				default () {}
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
	.xy-search{
		flex-wrap: nowrap;
		input{width: 100%;height: 64rpx;margin: 0;line-height: 64rpx;}
	}
	
</style>
