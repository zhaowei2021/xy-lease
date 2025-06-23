<template>
	<view class="xy-video" :style="{'margin':'0 '+ data.params.lrmargin+'rpx','borderRadius': data.params.borderRadius+'rpx'}">
		<view class="video" >
			<video :src="$xyfun.image(data.params.videourl)" :poster="data.params.poster?$xyfun.image(data.params.poster):''" :style="{'borderRadius': data.params.borderRadius+'rpx'}" objectFit="cover" />
		</view>
	</view>
</template>
<script>
	export default {
		name: "xyVideo",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '视频组件',
						type: 'video',
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
	.xy-video {
		video{width: 100%;}
	}
</style>
