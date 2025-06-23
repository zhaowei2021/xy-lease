<template>
	<view v-if="tabBarList && currentRoute != ''">
		<view class="tab-bar" v-if="tabBarList.style == 1" :style="{ backgroundColor: tabBarList.backgroundColor }">
			<view class="tabbar-border"></view>
			<view class="item" v-for="(item, index) in tabBarList.list" :key="index" @click="$xyfun.to(item.link)" v-if="item.show == 1">
				<view class="bd psr">
					<view class="icon">
						<block v-if="verify(item.link)">
							<image :src="$xyfun.image(item.selectedIconPath)" />
						</block>
						<block v-else>
							<image :src="$xyfun.image(item.iconPath)" />
						</block>
					</view>
					<view class="label" :style="{ color: verify(item.link) ? tabBarList.textHoverColor : tabBarList.textColor }">
						{{ item.title }} 
					</view>
					<view class="psa cart-num ts-24 lh-24 tc-w" v-if="cart.cartNum > 0 && item.link =='/pages/cart'">{{cart.cartNum}}</view>
				</view>
			</view>
		</view>
		
		<view class="tab-bar2" v-if="tabBarList.style == 2">
			<view class="tab-list" :style="{ backgroundColor: tabBarList.backgroundColor }">
				<view class="item" v-for="(item, index) in tabBarList.list" :key="index" @click="$xyfun.to(item.link)" v-if="item.show == 1">
					<view class="center" v-if="index == 2 && tabBarList.list.length == 5">
						<view class="icon" :style="{ backgroundColor: tabBarList.textHoverColor }">
							<block v-if="verify(item.link)">
								<image :src="$xyfun.image(item.selectedIconPath)" />
							</block>
							<block v-else>
								<image :src="$xyfun.image(item.iconPath)" />
							</block>
						</view>
					</view>
					<view class="bd" v-else>
						<view class="icon">
							<block v-if="verify(item.link)">
								<image :src="$xyfun.image(item.selectedIconPath)" />
							</block>
							<block v-else>
								<image :src="$xyfun.image(item.iconPath)" />
							</block>
						</view>
						<view class="label" :style="{ color: verify(item.link) ? tabBarList.textHoverColor : tabBarList.textColor }">
							{{ item.title }}
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="tab-bar-placeholder"></view>
	</view>
</template>

<script>
import { mapState } from 'vuex';
export default {
	name: 'xyTabbar',
	props: {
	},
	data() {
		return {
			css:this.$xyfun.css(),
			currentRoute: '', //当前页面路径
			tabBarList:{},
		};
	},
	mounted() {
		let currentPage = getCurrentPages()[getCurrentPages().length - 1];
		this.currentRoute = '/' + currentPage.route;
		this.tabBarList = this.$xyfun.tabBarData();
	},
	computed: {
		...mapState(['cart'])
	},
	methods: {
		verify(link) {
			if (link == null || link == '') return false;
			var url = this.currentRoute;
			if (link == url) {
				return true;
			}
			return false;
		}
	}
};
</script>

<style lang="scss">

.safe-area {
	padding-bottom: 0;
	padding-bottom: constant(safe-area-inset-bottom);
	padding-bottom: env(safe-area-inset-bottom);
}

.tab-bar {
	background-color: #fff;
	box-sizing: border-box;
	position: fixed;
	left: 0;
	bottom: 0;
	width: 100%;
	z-index: 998;
	display: flex;
	border-top: 2rpx solid #f5f5f5;
	padding-bottom: 0;
	padding-bottom: constant(safe-area-inset-bottom);
	padding-bottom: env(safe-area-inset-bottom);

	.tabbar-border {
		background-color: rgba(255, 255, 255, 0.329412);
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 2rpx;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
	}

	.item {
		display: flex;
		align-items: center;
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		flex: 1;
		flex-direction: column;
		padding-bottom: 10rpx;
		box-sizing: border-box;

		.bd {
			position: relative;
			height: 100rpx;
			flex-direction: column;
			text-align: center;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;

			.icon {
				position: relative;
				display: inline-block;
				margin-top: 10rpx;
				width: 44rpx;
				height: 44rpx;
				font-size: 44rpx;
				image {
					width: 100%;
					height: 100%;
				}
				> view {
					height: inherit;
					display: flex;
					align-items: center;
				}
				.bar-icon {
					font-size: 42rpx;
				}
			}

			.cart-num{right: 0rpx;top: 15rpx;border-radius: 50%;padding: 4rpx;min-width: 24rpx;z-index: 100;min-height: 24rpx;background-color: #ff5335;}

			.label {
				position: relative;
				text-align: center;
				font-size: 24rpx;
				line-height: 1;
				margin-top: 18rpx;
			}
		}

	}
}

.tab-bar2{
	
	box-sizing: border-box;
	position: fixed;
	left: 30rpx;
	bottom: 0rpx;
	width: 690rpx;
	z-index: 998;
	padding-bottom: 0;
	padding-bottom: constant(safe-area-inset-bottom);
	padding-bottom: env(safe-area-inset-bottom);
	
	.tab-list{
		display: flex;
		width: 100%;
		border-radius: 61rpx;
		background-color: #fff;
		box-shadow: 0 0 6rpx 3rpx #fff; 
	}
	
	.item {
		display: flex;
		align-items: center;
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		flex: 1;
		flex-direction: column;
		box-sizing: border-box;
		
		.center{
			background-color: #fff;
			width: 152rpx;
			border-radius: 50%;
			box-shadow: 0 0 6rpx 3rpx #fff; 
			position: absolute;
			top: -32rpx;
			.icon{
				margin-top: 48rpx;
				margin-left: 35rpx;
				width: 84rpx;height: 84rpx;line-height: 84rpx;text-align: center;border-radius: 42rpx;
				image{width: 44rpx;height: 44rpx;vertical-align: middle;}
			}
		}
	
		.bd {
			position: relative;
			flex-direction: column;
			text-align: center;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			padding: 20rpx 0;
			.icon {
				position: relative;
				display: inline-block;
				width: 43rpx;
				height: 43rpx;
				image {
					width: 100%;
					height: 100%;
					vertical-align: unset;
				}
				> view {
					height: inherit;
					display: flex;
					align-items: center;
				}
				
			}
	
			.label {
				position: relative;
				text-align: center;
				font-size: 24rpx;
				line-height: 24rpx;
				line-height: 1;
				margin-top: 15rpx;
			}
		}
	
	}
}

.tab-bar-placeholder {
	padding-bottom: calc(constant(safe-area-inset-bottom) + 112rpx);
	padding-bottom: calc(env(safe-area-inset-bottom) + 112rpx);
}
</style>
