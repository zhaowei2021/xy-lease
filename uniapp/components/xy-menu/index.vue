<template>
	<view class="xy-menu" :style="{'backgroundColor':data.params.bgColor,'border-radius': data.params.borderRadius+'rpx','margin':'0 '+ data.params.lrmargin+'rpx',padding:data.params.upnjj+'rpx ' + data.params.lrnjj+'rpx'}"> 
		<view class="title flex" v-if="data.params.title" :style="{color:data.params.textColor}">
			<view class="l tb">{{data.params.title}}</view>
			<view class="r ts-26 flex m-l-auto" :style="css.tcl" v-if="data.params.linktitle" @tap="onLink(data.params.link)">
				{{data.params.linktitle}}
				<text class="xyicon icon-right ts-34"></text>
			</view>
		</view>
		<div class="list flex">
			<view class="item" v-for="(menu, keys) in data.data" :key="keys" :style="{'width':(750-data.params.lrmargin*2-data.params.lrnjj*2-(data.params.colnum-1)*(data.params.itemjj))/data.params.colnum+'rpx','background':data.params.itembgColor,'border-radius':data.params.itemborderRadius+'px','margin-right':(keys+1)%data.params.colnum == 0 || data.params.textimgpl == 0 ? 0 : data.params.itemjj+'rpx','margin-top':data.params.itemjj+'rpx'}">
				<block v-if="menu.link == 'share'">
					<button open-type="share" :class="'inner'+(data.params.textimgpl==1?' flex lr':' ud')" :style="{padding:data.params.itemnjj+'rpx'}">
						<image :src="$xyfun.image(menu.image)" :style="{'height':data.params.imgwh+'rpx','width':data.params.imgwh+'rpx',}" />
						<view :style="{color:data.params.textColor,'font-weight':data.params.textbold==1?'bold':'normal','font-size':data.params.textsize+'rpx','line-height':data.params.textimgpl==1?data.params.imgwh+'rpx':data.params.textsize+'rpx','height':data.params.textimgpl==1?data.params.imgwh+'rpx':data.params.textsize+'rpx'}">{{menu.name}}</view>
					</button>
				</block>
				<block v-else-if="menu.link == 'contact'">
					
					<button open-type="contact" :class="'inner'+(data.params.textimgpl==1?' flex lr':' ud')" :style="{padding:data.params.itemnjj+'rpx'}">
						<image :src="$xyfun.image(menu.image)" :style="{'height':data.params.imgwh+'rpx','width':data.params.imgwh+'rpx',}" />
						<view :style="{color:data.params.textColor,'font-weight':data.params.textbold==1?'bold':'normal','font-size':data.params.textsize+'rpx','line-height':data.params.textimgpl==1?data.params.imgwh+'rpx':data.params.textsize+'rpx','height':data.params.textimgpl==1?data.params.imgwh+'rpx':data.params.textsize+'rpx'}">{{menu.name}}</view>
					</button>
					
				</block>
				<block v-else>
					<button @tap="onLink(menu.link)" :class="'inner'+(data.params.textimgpl==1?' flex lr':' ud')" :style="{padding:data.params.itemnjj+'rpx'}">
						<image :src="$xyfun.image(menu.image)" :style="{'height':data.params.imgwh+'rpx','width':data.params.imgwh+'rpx',}" />
						<view :style="{color:data.params.textColor,'font-weight':data.params.textbold==1?'bold':'normal','font-size':data.params.textsize+'rpx','line-height':data.params.textimgpl==1?data.params.imgwh+'rpx':data.params.textsize+'rpx','height':data.params.textimgpl==1?data.params.imgwh+'rpx':data.params.textsize+'rpx'}">{{menu.name}}</view>
					</button>
				</block>
			</view>
		</div>
	</view>
</template>
<script>
	export default {
		name: "xyMenu",
		props: {
			data: {
				type: Object,
				default: function() {
					return {
						name: '菜单组件',
						type: 'menu',
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
	.xy-menu{
		.item{
			button{
				background: none; padding: 0;margin: 0;
			}
			button image{vertical-align: top;}
			button.lr{display: flex;width: 100%;text-align: center;margin: 0;justify-content: center;}
			button.lr image{margin-right: 20rpx;}
			button::after{border: none;}
			.ud view{margin-top: 10rpx;}
		}
	}
	
</style>