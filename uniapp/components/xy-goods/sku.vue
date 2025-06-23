<template>
	<view class="xy-goods-sku" @touchmove.stop.prevent="">
		<view class="xy-modal-box bottom-fixed xy-modal-box-bottom share-btn-model-box ovh" :style="css.pbg" :class="[showModal?'xy-modal-show':'']" v-if="goodsInfo.sku_price">
			<view class="sku-box p-30" style="height: 900rpx;">
				
				<!--商品-->
				<view class="goods-box bl-b flex p-b-30" :style="css.blmc">
					<view class="l ovh br-10" :style="css.mbg">
						<image :src="currentSkuPrice.image ? currentSkuPrice.image : goodsInfo.image" />
					</view>
					<view class="r m-l-20">
						<view class="title tb lh-40 m-t-5 ovh">{{ goodsInfo.name }}</view>
						<view class="flex m-t-10">
							<block v-if="goodsType == 'sell' || goodsType == 'service'">
								<view class="tb ts-40" :style="css.tcp" v-if="currentSkuPrice.id">
									¥{{currentSkuPrice.price}}
								</view>
								<view class="tb ts-40" :style="css.tcp" v-else>
									¥{{skuPrice[0].price}}
								</view>
							</block>
							<block v-else>
								<view class="tb ts-40" :style="css.tcp" v-if="currentSkuPrice.id">
									¥<text v-if="leaseType == 'hour'">{{ $xyfun.bcmul(parseInt($xyfun.lease().starthour),parseFloat(currentSkuPrice.hourprice))}}</text>
									<text v-if="leaseType == 'days'">{{currentSkuPrice.daysprice}}</text>
									<text v-if="leaseType == 'night'">{{currentSkuPrice.nightprice}}</text>
								</view>
								<view class="tb ts-40" :style="css.tcp" v-else>
									¥<text v-if="leaseType == 'hour'">{{ $xyfun.bcmul(parseInt($xyfun.lease().starthour),parseFloat(skuPrice[0].hourprice))}}</text>
									<text v-if="leaseType == 'days'">{{skuPrice[0].daysprice}}</text>
									<text v-if="leaseType == 'night'">{{skuPrice[0].nightprice}}</text>
								</view>
							</block>
							
							<!--view class="m-l-auto p-t-15" :style="css.tcl">剩余：{{ currentSkuPrice.stock || goodsInfo.stock }}</view-->
						</view>
					</view>
				</view>
			
				<view class="select-box m-t-30" v-if="goodsType == 'single' || goodsType == 'package'">
					<view class="tb">租赁类型</view>
					<view class="m-t-20 flex">
						<view class="item br-10 p-lr-30 p-tb-10 m-r-20 ts-28" :style="leaseType == item ? css.mcbg+'color:#fff' : css.mbg" v-for="(item,index) in $xyfun.lease().leasetype" :key="index" @tap="changeLeaseType(item)">
							<text v-if="item=='hour'">{{ $xyfun.lease().starthour + '小时租' }}</text>
							<text v-if="item=='days'">全日租</text>
							<text v-if="item=='night'">隔夜租</text>
						</view>
					</view>
				</view>
			
				<!--规格选项 -->
				<scroll-view scroll-y class="sku-item-box m-tb-30" :style="{height:goodsType == 'sell' ||goodsType == 'service' ? '460rpx' : '320rpx'}">
					<view class="select-box m-b-30" v-for="(item, index) in skuList" :key="index">
						<view class="tb">{{ item.name }}</view>
						<view class="flex m-t-20">
							<view class="item br-10 p-lr-30 p-tb-10 m-r-20 ts-28" v-for="(item1, index1) in item.content" :key="index1"
								:style="currentSkuArray[item1.pid] == item1.id ? css.mcbg+'color:#fff' : css.mbg"
								:disabled="item1.disabled == true" @tap="chooseSku(item1.pid, item1.id)">
								{{ item1.name }}
							</view>
						</view>
					</view>
				</scroll-view>
				<view class="sales lh-60 flex">
					<view class="tb">租赁数量</view>
					<view class="r tb m-l-auto tc flex">
						<view :style="css.mbg" class="ts-42 br-10" @tap="reduce">-</view>
						<input @input="buyNumInput" type="number" :style="css.mbg" class="br-10 m-lr-10" :value="buyNum" />
						<view :style="css.mbg" class="ts-42 br-10" @tap="add">+</view>
					</view>
				</view>
				<!--租按钮 -->
				<view class="btn-box bottom-fixed tc" :style="css.mbg">
					<view class="flex m-30">
						<view class="add-cart m-r-30 br-10 tc-w" :style="css.mcbg" @tap="confirmCart">
							加入立即租
						</view>
						<view class="buy m-l-auto tc-w br-10" :style="css.mcbg" @tap="confirmBuy">
							立即租
						</view>
					</view>
				</view>
				
			</view>
		</view>
		
		<view class="xy-modal-mask" :class="[showModal?'xy-mask-show':'']" @tap="showModal =!showModal"></view>
	</view>
	
</template>

<script>
	import { mapActions } from 'vuex';
	export default {
		components: {},
		data() {
			return {
				css:this.$xyfun.css(),
				skuList: [],
				currentSkuPrice: {},
				currentSkuArray: [],
				buyNum: 1,
				confirmGoodsInfo: {},
				type: this.buyType,
				leaseType:this.ltype,
			};
		},
		props: {
			goodsInfo: {},
			value: {},
			buyType: {
				type: String,
				default: 'sku'
			},
			goodsType: {
				type: String,
				default: 'sell'
			},
			ltype:{
				type: String,
				default: ''
			}
		},
		created() {
			this.skuList = this.goodsInfo.sku;
			this.changeDisabled(false);
		},
		mounted() {
			//单规格
			if (this.goodsInfo.issku == 0) {
				this.currentSkuPrice = this.skuPrice[0];
			}
		},
		watch: {
			type(nweVal, oldVal) {
				return newVal;
			}
		},
		computed: {
			skuPrice() {
				return this.goodsInfo.sku_price;
			},
			showModal: {
				get() {
					return this.value;
				},
				set(val) {
					val ? uni.hideTabBar() : uni.showTabBar();
					this.$emit('input', val);
					return val;
				}
			},
			currentSkuText() {
				var that = this;
				var str = '';
				var currentSkuArray = this.currentSkuArray;
				currentSkuArray.forEach(v => {
					that.skuList.forEach(s => {
						s.content.forEach(u => {
							if (u.id === v) {
								str += ' ' + u.name;
							}
						});
					});
				});
				that.$emit('getSkuText', str);
				return str;
			},
			
		},

		methods: {
			...mapActions('cart',{'addCartGoods':'addGoods'}),
			//减
			reduce(){
				this.buyNum --;
				if(this.buyNum < 1){
					this.buyNum = 1;
					this.$xyfun.msg('租数量不能小于1');
				}
			},
			//输入
			buyNumInput(e){
				this.buyNum = e.detail.value;
				if (this.buyNum >= this.currentSkuPrice.stock && this.currentSkuPrice.type != 'package') {
					this.buyNum = this.currentSkuPrice.stock;
					this.$xyfun.msg('库存不足');
					return;
				}
			},
			//加
			add(){
				this.buyNum ++;
				if (this.buyNum >= this.currentSkuPrice.stock && this.currentSkuPrice.type != 'package') {
					this.buyNum = this.currentSkuPrice.stock;
					this.$xyfun.msg('库存不足');
					return;
				}
			},

			// 选择规格
			chooseSku(pid, skuId) {
				
				var that = this;
				var isChecked = true; // 选中 or 取消选中
				this.goodsNum = 1; //选择规格时，数量重置为1.
				this.maxStep = 999; //选择其他规格时，取消上个规格库存限制

				if (that.currentSkuArray[pid] != undefined && that.currentSkuArray[pid] == skuId) {
					// 点击已被选中的，删除并填充 ''
					isChecked = false;
					that.currentSkuArray.splice(pid, 1, '');
				} else {
					// 选中
					that.$set(that.currentSkuArray, pid, skuId);
				}

				var chooseSkuId = []; // 选中的规格大类
				that.currentSkuArray.forEach(sku => {
					if (sku != '') {
						// sku 为空是反选 填充的
						chooseSkuId.push(sku);
					}
				});

				// 当前所选规格下，所有可以选择的 skuPric
				var newPrice = this.getCanUseSkuPrice();

				// 判断所有规格大类是否选择完成
				if (chooseSkuId.length == that.skuList.length && newPrice.length) {
					that.currentSkuPrice = newPrice[0];
				} else {
					that.currentSkuPrice = {};
				}

				// 改变规格项禁用状态
				this.changeDisabled(isChecked, pid, skuId);
			},

			// 改变禁用状态
			changeDisabled(isChecked = false, pid = 0, skuId = 0) {
				var newPrice = []; // 所有可以选择的 skuPrice

				if (isChecked) {
					// 当前点击选中规格下的 所有可用 skuPrice
					for (var price of this.skuPrice) {
						if (price.stock <= 0) {
							// this.goodsNum 不判断是否大于当前选择数量，在 uni-number-box 判断，并且 取 stock 和 buynum 的小值
							continue;
						}
						if (price.goods_sku_id_arr.indexOf(skuId.toString()) >= 0) {
							newPrice.push(price);
						}
					}
				} else {
					newPrice = this.getCanUseSkuPrice();
				}

				// 所有存在并且有库存未选择的规格项 的 子项 id
				var noChooseSkuIds = [];
				for (var price of newPrice) {
					noChooseSkuIds = noChooseSkuIds.concat(price.goods_sku_id_arr);
				}

				// 去重
				noChooseSkuIds = Array.from(new Set(noChooseSkuIds));

				if (isChecked) {
					// 去除当前选中的规格项
					var index = noChooseSkuIds.indexOf(skuId.toString());
					noChooseSkuIds.splice(index, 1);
				} else {
					// 循环去除当前已选择的规格项
					this.currentSkuArray.forEach(sku => {
						if (sku.toString() != '') {
							// sku 为空是反选 填充的
							var index = noChooseSkuIds.indexOf(sku.toString());
							if (index >= 0) {
								// sku 存在于 noChooseSkuIds
								noChooseSkuIds.splice(index, 1);
							}
						}
					});
				}

				// 当前已选择的规格大类
				var chooseSkuKey = [];
				if (!isChecked) {
					// 当前已选择的规格大类
					this.currentSkuArray.forEach((sku, key) => {
						if (sku != '') {
							// sku 为空是反选 填充的
							chooseSkuKey.push(key);
						}
					});
				} else {
					// 当前点击选择的规格大类
					chooseSkuKey = [pid];
				}

				for (var i in this.skuList) {
					// 当前点击的规格，或者取消选择时候 已选中的规格 不进行处理
					if (chooseSkuKey.indexOf(this.skuList[i]['id']) >= 0) {
						continue;
					}

					for (var j in this.skuList[i]['content']) {
						// 如果当前规格项 id 不存在于有库存的规格项中，则禁用
						if (noChooseSkuIds.indexOf(this.skuList[i]['content'][j]['id'].toString()) >= 0) {
							this.skuList[i]['content'][j]['disabled'] = false;
						} else {
							this.skuList[i]['content'][j]['disabled'] = true;
						}
					}
				}
			},
			
			// 获取所有有库存的 skuPrice
			getCanUseSkuPrice() {
				var newPrice = [];

				for (var price of this.skuPrice) {
					if (price.stock <= 0) {
						continue;
					}
					var isOk = true;

					this.currentSkuArray.forEach(sku => {
						if (sku.toString() != '' && price.goods_sku_id_arr.indexOf(sku.toString()) < 0) {
							isOk = false;
						}
					});

					if (isOk) {
						newPrice.push(price);
					}
				}

				return newPrice;
			},

			// 加入购物车确定
			confirmCart() {
				if (this.confirmSku()) {
					var confirmGoodsList = {
						list: [this.confirmGoodsInfo],
						from: 'goods'
					};
					this.addCartGoods(confirmGoodsList).then(res => {
						this.showModal = false;
						this.$xyfun.msg('加入购物车成功');
					});
				}
			},
			
			// 立即租｜买
			confirmBuy() {
				if (this.confirmSku()) {
					var confirmGoodsList = [];
					confirmGoodsList.push(this.confirmGoodsInfo);
					var params = {
						goodsList: confirmGoodsList,
						from: 'buynow', //立即租
						leasetype: this.leaseType,
						type: this.goodsType == 'sell' ? 'buy' : this.goodsType == 'service' ? 'service' : 'lease'
					};
					this.$Router.push({
						path: '/pages/user/order/confirm',
						query: params
					});
				}
			},
			
			// 确定规格
			confirmSku() {
				if (this.currentSkuPrice.type != 'package' && (this.currentSkuPrice.stock == 0 || this.currentSkuPrice.stock < this.goodsNum)) {
					this.$xyfun.msg('库存不足');
					this.showModal = false;
					return false;
				}
				
				//检查套餐库存--
				this.currentSkuPrice.buynum = this.buyNum;
				this.confirmGoodsInfo = {
					goods_id: this.currentSkuPrice.goods_id,
					buynum: this.currentSkuPrice.buynum,
					goodstype: this.currentSkuPrice.type,
					sku_price_id: this.currentSkuPrice.id,
				};
				if (!this.confirmGoodsInfo.sku_price_id) {
					this.$xyfun.msg('请选择规格');
					return false;
				} else {
					this.showModal = false;
					return true;
				}
			},
			
			// 切换租赁类型
			changeLeaseType(type){
				this.leaseType = type;
			}
		}
	};
</script>

<style lang="scss">
	.sku-box{
		.icon-close{right: 30rpx;top: 20rpx;}
		
		.goods-box{
			.l{
				width: 150rpx;height: 150rpx;
				image{width: 150rpx;height: 150rpx;}
			}
			.r{
				.title{height: 80rpx;}
				width: 520rpx;
			}
		}
		.sales{
			.r view{height: 60rpx;width: 60rpx;}
			.r input{width: 100rpx;height: 60rpx;}
		}
		
		.btn-box{
			.buy,.add-cart{width: 300rpx;height: 76rpx;line-height: 72rpx;}
		}
	}
</style>
