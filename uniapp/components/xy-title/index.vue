<template>
	<view class="xy-title flex m-lr-30 m-b-20">
		<view class="l m-r-10" :style="css.mcbg"></view>
		<view class="c ts-32">{{data.params.title}}</view>
		<view class="r ts-26 flex lh-30" :style="css.tcl" v-if="data.params.linktitle" >
			<block v-if="data.params.link == 'share'">
				<button open-type="share" class="ts-26 lh-30" :style="css.tcl">
					 {{data.params.linktitle}}
					 <text class="xyicon icon-right ts-30"></text>
				</button>
			</block>
			<block v-else-if="data.params.link == 'contact'" :style="css.tcl">
				<button open-type="contact lh-30" class="ts-26">
					 {{data.params.linktitle}}
					 <text class="xyicon icon-right ts-30"></text>
				</button>
			</block>
			<block v-else>
				<view class="lh-30 flex" @tap="onLink(data.params.link)">
					{{data.params.linktitle}}
					<text class="xyicon icon-right ts-30"></text>
				</view>
			</block>
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyTitle",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '标题组件',
						type: 'title',
						params: [],
						data: []
					}
				}
			}
		},
		data() {
			return {
				css:this.$xyfun.css(),
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
	.xy-title {
		height: 36rpx;line-height: 36rpx;
		.l{width: 6rpx;border-radius: 3rpx;}
		.r{margin-left: auto;}
	}
	button{
		background: none; padding: 0;margin: 0;
	}
	button::after{border: none;}
</style>
