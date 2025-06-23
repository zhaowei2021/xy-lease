<template>
	<view class="xy-share">
		
		<!-- 海报弹窗 -->
		<block>
			<view class="xy-modal-box xy-modal-box-center device-model-box" :class="[showPoster&&posterImage?'xy-modal-show':'']">
				<view class="poster-img-box"><image class="poster-img" :src="posterImage" mode="widthFix"></image></view>
				<view class="flex p-b-30" v-show="posterImage">
					<!-- #ifdef MP-WEIXIN -->
					<button :style="css.mcbg" class="pbtn ts-30 lh-30 p-20 tc-w" @tap="saveImage">保存海报</button>
					<!-- #endif -->
				</view>
			</view>
			<view class="xy-modal-mask" :class="[showPoster?'xy-mask-show':'']" @tap="showPoster = !showPoster"></view>
		</block>
		
		<!-- 分享按钮 -->
		<view class="xy-modal-box bottom-fixed xy-modal-box-bottom share-btn-model-box ovh" :style="css.pbg" :class="[showShare?'xy-modal-show':'']" @tap="showShare = false">
			<view class="share-box" :style="css.mbg">
				<view class="share-list-box flex p-t-50 p-b-30">
					<!-- #ifdef MP-WEIXIN -->
					<button class="share-btn" open-type="share">
						<image class="share-img" src="/static/images/share_wx.png" />
						<view class="share-title ts-28" :style="css.tcm">微信好友</view>
					</button>
					<!-- #endif -->
					
					<button class="share-btn" @tap="onPoster">
						<image class="share-img" src="/static/images/share_poster.png" />
						<view class="share-title ts-28 lh-28 m-t-20" :style="css.tcm">生成海报</view>
					</button>
				</view>
				<view class="share-foot tc p-30 bl-t" @tap="showShare = false" :style="css.tcl+css.blpc">取消</view>
			</view>
		</view>
		<view class="xy-modal-mask" :class="[showShare?'xy-mask-show':'']" @tap="showShare =!showShare"></view>
		
		<!-- 海报模块 -->
		<xy-canvas v-if="showPoster" ref="xyCanvas" :canvasParams="canvasParams" @success="onSuccess"></xy-canvas>
		
	</view>
</template>
<script>


import { mapState } from 'vuex';
import $platform from '@/utils/platform';
import xyCanvas from '@/components/xy-canvas';
import xyfun from '../../utils/xyfun';
export default {
	name: 'xyShare',
	components: {
		xyCanvas
	},
	data() {
		return {
			css:this.$xyfun.css(),
			shareConfig: {},
			showPoster: false,
			platform: $platform.get(),
			posterImage: '',
			canvasParams: {}
		};
	},
	props: {
		posterType: {
			type: String,
			default: ''
		},
		posterInfo: {
			type: Object,
			default: () => {}
		},
		value: {}
	},
	computed: {
		...mapState(['user','common']),
		showShare: {
			get(val) {
				return val.value;
			},
			set(val) {
				this.$emit('input', val);
			}
		}
	},
	methods: {
		// 关闭弹窗
		onClosePoster() {
			this.showPoster = false;
			uni.showTabBar();
		},
		// 绘制成功
		onSuccess(e) {
			console.log('绘制成功',e);
			this.posterImage = e;
		},
		// 开始绘制
		onPoster() {
			this.posterImage = '';
			uni.hideTabBar();
			if (this.user.isLogin) {
				this.canvasParams = this.getPosterParams();
				this.showPoster = true;
				
			} else {
				this.$xyfun.toLogin();
			}
			this.showShare = false;
		},

		// 保存图片
		async saveImage() {
			var that = this;
			
			uni.saveImageToPhotosAlbum({
				filePath: that.posterImage,
				success: () => {
					that.$xyfun.msg('保存成功');
					that.showPoster = false;
				},
				fail: err => {
					console.log(`保存失败:`, err);
					that.$xyfun.msg('保存失败');
				}
			});
		},
		
		// 获取海报参数
		getPosterParams() {
			var that = this, data = {},shareConfig = this.common.shareConfig,userInfo = this.user.info,user_poster_bg = this.common.sysShareConfig.user_poster_bg;
			console.log('posterInfo',that.posterInfo);
			switch (this.posterType) {
				case 'user':
					data = {
						backgroundImage: xyfun.image(user_poster_bg),
						drawArray: [
							{
								name: '用户昵称',
								type: 'text',
								text: userInfo.nickname,
								size: 40,
								dx:70,
								dy: 1020,
								color: that.css.style.mainColor,
								textAlign: 'middle',
								textBaseLine: 'middle'
							},
							{
								name: 'avatar',
								type: 'image',
								url: that.$xyfun.image(userInfo.avatar),
								alpha: 1,
								dx:50,
								dy: 875,
								dWidth: 132,
								dHeight: 132,
								circleSet: {}
							},
							{
								name: '提示文字',
								type: 'text',
								text: '邀您一起赚佣金',
								size: 28,
								dx:70,
								dy: 1100,
								color: '#333',
								textAlign: 'middle',
								textBaseLine: 'middle'
							},
							// #ifdef MP-WEIXIN
							{
								name: 'wxCode',
								type: 'image',
								url: `${that.$xyfun.http_config('api_url')}/wechat/wxacode?spm=${shareConfig.spm}`,
								alpha: 1,
								dx: 505,
								dy: 985,
								dWidth: 180,
								dHeight: 180
							},
							// #endif
							// #ifndef  MP-WEIXIN
							{
								name: '普通二维码',
								type: 'qrcode',
								text: shareConfig.shareLink,
								dx: 490,
								dy: 970,
								dWidth: 160,
								dHeight: 160
							}
							// #endif
						]
					};
					break;
				case 'goods':
				
					var price = that.posterInfo.sku_price[0].daysprice + '/天';
					
					if(that.$xyfun.lease().defaultlease == 'hour'){
						price = that.posterInfo.sku_price[0].hourprice + '/小时';
					}
					
					if(that.$xyfun.lease().defaultlease == 'night'){
						price = that.posterInfo.sku_price[0].nightprice + '/夜';
					}
				
					data = {
						background: {
							width: 750, //画布宽度
							height: 1200, //画布高度
							backgroundColor: '#ffffff', //画布背景颜色
						},
						drawArray: [
							{
								name: '图片',
								type: 'image',
								url: that.$xyfun.image(that.posterInfo.image),
								alpha: 1,
								dx:0,
								dy: 0,
								dWidth: 750,
								dHeight: 750,
							},
							{
								name: 'avatar',
								type: 'image',
								url: that.$xyfun.image(userInfo.avatar),
								alpha: 1,
								dx:30,
								dy: 800,
								dWidth: 132,
								dHeight: 132,
								circleSet: {}
							},
							{
								name: '用户昵称',
								type: 'text',
								text: userInfo.nickname,
								size: 40,
								dx:172,
								dy: 805,
								color: that.css.style.mainColor,
								textAlign: 'middle',
								textBaseLine: 'middle'
							},
							{
								name: '提示文字',
								type: 'text',
								text: '分享给您一个租赁商品',
								size: 32,
								dx:172,
								dy: 868,
								textAlign: 'middle',
								textBaseLine: 'middle'
							},
							{
								name: '名称',
								type: 'text',
								text: that.posterInfo.name,
								size: 34,
								dx:30,
								dy: 970,
								lineFeed:{maxWidth:510},
								textAlign: 'middle',
								textBaseLine: 'middle'
							},
							{
								name: '价格',
								type: 'text',
								text: '¥'+price,
								size: 40,
								dx:30,
								dy: 1030,
								color: that.css.style.textPriceColor,
								textAlign: 'middle',
								textBaseLine: 'middle'
							},
							// #ifdef MP-WEIXIN
							{
								name: 'wxCode',
								type: 'image',
								url: `${that.$xyfun.http_config('api_url')}/wechat/wxacode?spm=${shareConfig.spm}`,
								alpha: 1,
								dx: 540,
								dy: 945,
								dWidth: 180,
								dHeight: 180
							},
							// #endif
							// #ifndef  MP-WEIXIN
							{
								name: '普通二维码',
								type: 'qrcode',
								text: shareConfig.shareLink,
								dx: 520,
								dy: 930,
								dWidth: 160,
								dHeight: 160
							}
							// #endif
						]
					};
					break;
				default:
					break;
			}
			
			return data;
		}
	}
};
</script>

<style lang="scss">
	.share-box {
		.share-btn {
			background: none;
			&::after {
				border: none;
				padding: 0;
			}
			padding: 0;
			.share-img {
				width: 80rpx;
				height: 80rpx;
				border-radius: 50%;
			}
		}
	}
	
	.pbtn{width: 220rpx;}
	.pbtn.cl{background: #f7f7f7;color: #222;}
</style>
