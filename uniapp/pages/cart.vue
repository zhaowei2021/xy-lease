<template>
	<view class="cart p-t-30" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<block v-if="cart.cartList.length">
			<view class="top flex m-lr-30">
				<view>共{{cart.cartList.length}}种商品</view>
				<view class="m-l-auto bl br-10 ts-26 p-lr-20" @tap="operate">{{cart.operate?'完成':'管理'}}</view>
			</view>
			<view class="goods m-lr-30 p-tb-10 m-t-30 br-10" :style="css.mbg" v-if="leaseCartList.length > 0">
				<view class="flex bl-b p-lr-30 p-tb-20" :style="css.blpc">
					<text>租赁商品</text>
				</view>
				<view class="item p-tb-30 p-lr-30 flex" v-for="(g,index) in leaseCartList" :key="index" :style="css.blpc">
					<view class="checked" @tap="onOne(g)">
						<text class="xyicon icon-radio-a ts-30 flex tb" v-if="g.checked"></text>
						<text class="xyicon icon-radio ts-30 flex tb" v-else></text>
					</view>
					<view class="l ovh br-10">
						<image :src="g.sku_price.image ? g.sku_price.image : g.goods.image" />
					</view>
					<view class="r psr m-l-20">
						<view class="tb lh-40">
							{{g.goods.name}}
						</view>
						<view class="sku-text m-t-20" v-if="g.sku_price.goodsskutext">
							<text class="sku-text ts-24 lh-24 br-10 p-lr-25 p-tb-" :style="css.pbg+css.tcl">
							  {{g.sku_price.goodsskutext}}
							</text>
						</view>
						<view class="price-box flex psa">
							<text :style="css.tcp">￥</text>
							<text :style="css.tcp" class="ts-34 tb">
								<block v-if="$xyfun.lease().defaultlease == 'hour'">
									{{g.sku_price.hourprice}}
								</block>
								<block v-if="$xyfun.lease().defaultlease == 'days'">
									{{g.sku_price.daysprice}}
								</block>
								<block v-if="$xyfun.lease().defaultlease == 'night'">
									{{g.sku_price.nightprice}}
								</block>
							</text>
							<view class="buy-num tb m-l-auto tc flex">
								<view :style="css.mcbg" class="ts-40 br-10 tc-w" @tap="reduce(g)">-</view>
								<input @input="buyNumInput" type="number" :style="css.pbg" class="br-10 m-lr-10" :value="g.buynum" />
								<view :style="css.mcbg" class="ts-40 br-10 tc-w" @tap="add(g)">+</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			
			<view class="bottom-fixed cart-bottom flex tc" :style="css.mbg">
				<view class="p-lr-30 p-tb-15 flex tc wa">
					<view class="flex lh-42 p-t-15" @tap="onAll">
						<text class="xyicon icon-radio-a ts-30 flex tb m-r-10" v-if="cart.allSelected" ></text>
						<text class="xyicon icon-radio ts-30 flex tb m-r-10" v-else></text>全选
					</view>
					
					<view class="action flex m-l-auto">
						<block v-if="cart.operate">
							<view class="del m-l-auto tb tc-w" :style="css.mcbg" @tap="del">
								删除
							</view>
						</block>
						<block v-else>
							<view class=" lh-42 p-t-15 ts-36 m-r-30"><text>合计：</text><text class="ts-26" :style="css.tcp">¥</text><text :style="css.tcp">{{cart.allSum}}</text></view>
							<view class="buy m-l-auto tb tc-w" :style="css.mcbg" @tap="onPay()">
								立即结算
							</view>
						</block>
					</view>
				</view>
			</view>
		</block>
		<block v-else>
			<xy-empty text="租物车为空" />
			<button :style="css.mcbg" class="gobuy ts-30 lh-32 p-20 tc-w" @tap="$Router.pushTab('/pages/category');">去逛逛</button>
		</block>
		
		<xy-tabbar />
	</view>
</template>

<script>
	import { mapState,mapActions } from 'vuex';
	import xyEmpty from '@/components/xy-empty';
	import xyTabbar from '@/components/xy-tabbar';
	export default {
		components: {
			xyTabbar,
			xyEmpty,
		},
		data() {
			return {
				css:{},
				buyCartList:[],
				leaseCartList:[],
				serviceCartList:[],
				isLoading:true,
			}
		},
		computed: {
			...mapState(['user','cart'])
		},
		onPullDownRefresh() {
			this.loadData()
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			
		},
		onShow() {
			if(this.user.isLogin){
				this.loadData();
			}else{
				this.$xyfun.toLogin();
			}
		},
		methods: {
			...mapActions('cart',{'getCartList':'getList','getiSSel':'isSel'}),
			
			
			loadData(){
				this.getCartList().then((res)=>{
					console.log(res);
					uni.stopPullDownRefresh();
					var tempBuyList = [];
					var tempLeaseList = [];
					var tempServiceList = [];
					res.forEach((item)=>{
						
						if(item.goodstype == 'sell'){
							tempBuyList.push(item);
						}else if(item.goodstype == 'service'){
							tempServiceList.push(item);
						}else{
							tempLeaseList.push(item);
						}
					})
					console.log(tempBuyList);
					console.log(tempLeaseList);
					this.buyCartList = tempBuyList;
					this.leaseCartList = tempLeaseList;
					this.serviceCartList = tempServiceList;
					this.isLoading = false;
				});
				
			},
			
			// 结算
			onPay() {
				var that = this,cartList = this.cart.cartList;
				this.getiSSel().then((res)=>{
					if(res){
						var confirmcartList = [];
						cartList.forEach((item)=>{
							if (item.checked) {
								if (item.cart_type === 'invalid') {
									that.$xyfun.msg('商品已失效');
									return false;
								}
								confirmcartList.push({
									goods_id: item.goods_id,
									sku_price_id: item.sku_price_id,
									goodstype: item.goodstype,
									buynum: item.buynum
								});
							}
						})
						
						that.$Router.push({
							path:'/pages/user/order/confirm', 
							query:{ goodsList: confirmcartList, from: 'cart',leasetype:this.$xyfun.lease().defaultlease }
						});
						
					}else{
						that.$xyfun.msg('请选择商品');
					}
				})
				
			},
			
			// 管理
			operate(){
				this.$store.dispatch('cart/operate');
			},
			
			// 全选
			onAll() {
				this.$store.dispatch('cart/onAll');
			},
			
			// 单选
			onOne(g) {
				this.$store.dispatch('cart/onOne',g);
			},
			
			// 增加
			add(g){
				this.$store.dispatch('cart/add',g);
			},
			
			// 减少
			reduce(g){
				this.$store.dispatch('cart/reduce',g);
			},
			
			// 删除
			del(){
				this.$store.dispatch('cart/del');
			}
		}
	}
</script>

<style scoped lang="scss">
	.cart{padding-bottom: 150rpx;}
	.goods{
		.item{
			.checked{width: 50rpx;line-height: 178rpx;}
			.l{
				width: 178rpx;height: 178rpx;
				image{width: 178rpx;height: 178rpx;}
			}
			.r{
				width: 382rpx;
				.price-box{
					bottom: 3rpx;width: 100%;line-height: 52rpx;
					.buy-num view{width: 52rpx;height: 52rpx;line-height: 52rpx;}
					.buy-num input{width: 80rpx;height: 52rpx;line-height: 52rpx;}
				}
			}
		}
	}
	
	.gobuy{width: 240rpx;margin: 0 auto;}
	
	.bottom-fixed{
		.buy{width: 260rpx;height: 72rpx;border-radius: 36rpx;line-height: 72rpx;}
		.del{width: 180rpx;height: 72rpx;border-radius: 36rpx;line-height: 72rpx;}
	}
	.cart-bottom{bottom: 110rpx;z-index: 100;}
</style>