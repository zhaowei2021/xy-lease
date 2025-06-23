<template>
	<view class="hide-canvas">
		<canvas canvas-id="poster_canvas" :style="{ width: (poster.width || 1) + 'px', height: (poster.height || 1) + 'px' }"></canvas>
	</view>
</template>

<script>
import { getSharePoster } from '@/utils/poster/QS-SharePoster/QS-SharePoster.js';
import tools from '@/utils/poster/tools.js';
var ctx = null;
export default {
	components: {},
	data() {
		return {
			poster: {},
			canvasId: 'poster_canvas'
		};
	},
	props: {
		canvasParams: {
			type: Object,
			default: () => {}
		},
	},
	computed: {
	},
	async mounted() {
		console.log('canvas 海报')
		ctx = uni.createCanvasContext(this.canvasId, this);
		ctx && this.createPoster();
	},
	methods: {
		async createPoster() {
			var that = this;
			uni.showLoading({
				title:"海报生成中..."
			});
			var config = {};
			if (that.canvasParams.backgroundImage) {
				config = {
					backgroundImage: tools.checkImgHttp(that.canvasParams.backgroundImage, 'bgImage')
				};
			}
			if (that.canvasParams.background) {
				config = {
					background: that.canvasParams.background
				};
			}
			try {
				console.log('开始生成时间:' + new Date());
				const d = await getSharePoster({
					_this: that, //若在组件中使用 必传
					...config,
					posterCanvasId: that.canvasId, //canvasId
					Context: ctx,
					delayTimeScale: 10, //延时系数
					draw: false, //是否执行ctx.draw方法, 推荐false，自己去draw
					drawArray: ({ bgObj, type, bgScale }) => {
						var arr = tools.initDrawArray(bgObj, that.canvasParams.drawArray);
						return new Promise((rs, rj) => {
							rs(arr);
						});
					},
					setCanvasWH: ({ bgObj, type, bgScale }) => {
						// 为动态设置画布宽高的方法，
						this.poster = bgObj;
					}
				});
				await that.drawPoster();
			} catch (e) {
				uni.hideLoading();
				uni.showToast(JSON.stringify(e));
				console.log(JSON.stringify(e));
			}
		},
		async drawPoster() {
			var that = this;
			ctx.draw(false, () => {
				uni.canvasToTempFilePath(
					{
						canvasId: that.canvasId,
						success: res => {
							uni.hideLoading();
							that.$emit('success', res.tempFilePath);
							console.log('海报生成成功, 时间:' + new Date() + '， 临时路径: ' + res.tempFilePath);
						},
						fail: err => {
							uni.hideLoading();
							console.log('生成异常', err);
						}
					},
					that
				);
			});
		}
	}
};
</script>
