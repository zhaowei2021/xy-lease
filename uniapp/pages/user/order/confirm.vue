<template>
	<view class="confirm p-t-30" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		
		<view class="delivery-box m-lr-30 p-30 br-10" :style="css.mbg">
			<view v-if="deliveryType == 'pickup'">
				<view class="flex" @tap="$xyfun.to('/pages/store/detail')">
					<view>
						<view class="tb m-b-15">
							<text class="m-l-auto tn ts-24 p-lr-15 bl br-5 m-r-10" :style="css.blc+css.tcmc">门店自提</text>
							<text>{{orderDetail.storeinfo.name}}</text>
						</view>
						<view class="ts-28" :style="css.tcl">开放时间：{{orderDetail.storeinfo.businesshours}}</view>
						<view class="ts-28 m-t-10" :style="css.tcl">{{orderDetail.storeinfo.address}}</view>
					</view>
					<text class="m-l-auto xyicon icon-right m-t-60" :style="css.tcl"></text>
				</view>
			</view>
		</view>
		
		<view class="lease-box m-lr-30 m-t-20 p-lr-30 p-tb-15 br-10" :style="css.mbg">
			<view class="form-item lh-80 flex" v-if="$xyfun.strSearch(type,'lease')">
				<text class="item-name">租用方式</text>
				<view class="flex r m-l-auto bl-b" :style="css.blpc">
					<picker @change="leaseTypeChange" :value="leaseType" :range="leaseTypeList" range-key="name">
						<view class="picker">{{leaseTypeList[leaseTypeIndex]['name']}}</view>
					</picker>
					<text class="m-l-auto xyicon icon-right" :style="css.tcl"></text>
				</view>
			</view>
			<view class="form-item lh-80 flex" v-if="$xyfun.strSearch(type,'lease')">
				<text class="item-name">租用时间</text>
				<view class="flex r m-l-auto bl-b" :style="css.blpc" @tap="showLeaseTimeModel()">
					<input disabled="true" :placeholder-style="css.tcl"  v-model="leaseTime" />
					<text class="m-l-auto xyicon icon-right" :style="css.tcl"></text>
				</view>
			</view>
			<view class="form-item lh-80 flex" v-if="$xyfun.strSearch(type,'lease')">
				<text class="item-name">自提时间</text>
				<view class="flex r m-l-auto bl-b" :style="css.blpc">
					<input disabled="true" placeholder="请选择自提时间" :placeholder-style="css.tcl"  v-model="pickupTime" />
				</view>
			</view>
			
			<view class="form-item lh-80 flex" >
				<text class="item-name">联系人</text>
				<view class="flex r m-l-auto bl-b" :style="css.blpc">
					<input placeholder="请输入联系人" :placeholder-style="css.tcl"  v-model="consignee" />
				</view>
			</view>
			<view class="form-item lh-80 flex">
				<text class="item-name">联系电话</text>
				<view class="flex r m-l-auto bl-b" :style="css.blpc">
					<input placeholder="请输入联系电话" :placeholder-style="css.tcl"  v-model="mobile" />
				</view>
			</view>
			<view class="form-item lh-80 flex">
				<text class="item-name">备注说明</text>
				<view class="flex r m-l-auto">
					<input placeholder="请输入备注可留空" :placeholder-style="css.tcl"  v-model="remark"></textarea>
				</view>
			</view>
		</view>
		
		<view class="goods-box m-lr-30 m-t-20 br-10" :style="css.mbg" v-if="orderDetail.totalamount > 0">
			<view class="flex bl-b p-lr-30 p-tb-20 tb ts-34" :style="css.blpc">
				<text>租赁商品</text>
				<view class="m-l-auto" :style="css.tcp"><text :style="css.tcl" class="ts-28 tn">租金合计：</text>￥{{ orderDetail.totalamount || "0.00" }}</view>
			</view>
			<view class="goods-list p-lr-30">
				<view v-for="(g,index) in orderDetail.ordergoodslist" :key="index" :style="css.blpc" :class="'item p-tb-30 flex '+(index == orderDetail.ordergoodslist.length-1?'':'bl-b')">
					<view class="l ovh br-10">
						<image :src="g.detail.current_sku_price.image ? g.detail.current_sku_price.image : g.detail.image" />
					</view>
					<view class="r psr m-l-20">
						<view class="tb lh-40 goods-title">
							{{g.detail.name}}
						</view>
						<view class="sku-text m-t-5" v-if="g.detail.current_sku_price && g.detail.current_sku_price.goodsskutext">
							<text class="sku-text ts-24 lh-24 br-10 p-lr-25 p-tb-" :style="css.pbg+css.tcl">
							  {{g.detail.current_sku_price && g.detail.current_sku_price.goodsskutext ? g.detail.current_sku_price.goodsskutext : ""}}
							</text>
						</view>
						<view class="price-box psa lh-40">
							<view class="m-b-10 flex">
								<text :style="css.tcl" class="ts-28">租金单价:</text>
								<text :style="css.tcp">￥</text>
								<text :style="css.tcp" class="ts-32 tb">{{ g.detail.current_sku_price.showprice }}</text>
								<text :style="css.tcl" class="ts-28">/{{ g.detail.current_sku_price.showtypetext }}</text>
								<text class="m-l-auto">x{{ g.detail.current_sku_price.shownum}}</text>
							</view>
							
							<view class="flex">
								<text :style="css.tcl" class="ts-28">押金单价:</text>
								<text :style="css.tcp">￥</text>
								<text :style="css.tcp" class="ts-32 tb">{{ g.detail.current_sku_price.deposit }}</text>
								<text class="m-l-auto">x{{ g.buynum }}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			
			<view class="p-lr-30 p-tb-20 br-10" :style="css.mbg">
				<view class="flex tb">
					<view>
						押金合计
						<view class="ts-26 tn" :style="css.tcl">订单结束后将退还押金</view>
					</view>
					<view class="m-l-auto m-t-15 ts-34" :style="css.tcp">￥{{ orderDetail.totaldeposit || "0.00" }}</view>
				</view>
			</view>
			
		</view>
		
		<view class="coupon flex p-30 m-b-2 lh-50 bc-w m-tb-20 m-lr-30 m-t-20 br-10" :style="css.mbg">
			<view class="l">优惠券：</view>
			<view class="m-l-auto" @tap="showCoupon()" :style="css.tcp">抵扣¥{{orderDetail.couponfee}}元 <text class="xyicon icon-right"></text></view>
		</view>
		
		<view class="bottom-fixed flex tc" :style="css.tcmc">
			<view class="p-lr-30 p-tb-15 flex tc wa">
				<view class="action flex m-l-auto">
					<view class="buy m-l-auto" :style="css.mcbg+css.tcm" @tap="subOrder()">提交订单</view>
				</view>
			</view>
		</view>
		
		<view class="bottom-fixed flex tc" :style="css.mbg">
			<view class="p-lr-30 p-tb-15 flex tc wa">
				<view class="flex lh-34 m-t-20">
					<text :style="css.tcl">共{{ orderDetail.totalnum }}件</text>
				</view>
				
				<view class="action flex m-l-auto">
					<view class="lh-42 p-t-15 ts-36 m-r-30"><text>合计：</text><text class="ts-26" :style="css.tcp">¥</text><text :style="css.tcp">{{ orderDetail.totalfee || "0.00" }}</text></view>
					<view class="sub-order m-l-auto tc-w" :style="css.mcbg" @tap="subOrder()">
						提交订单
					</view>
				</view>
				
			</view>
		</view>
		
		<!--优惠券弹窗-->
		<block v-if="couponModelShow">
			<view class="xy-modal-box bottom-fixed xy-modal-box-bottom coupon-model-box ovh" :style="css.pbg" :class="[couponModelShow?'xy-modal-show':'']">
				<view class="title p-tb-50 tb tc" :style="css.mbg">可用优惠券</view>
				
				<view class="coupon-list p-t-30" v-if="couponList.length > 0">
					<view class="item br-10 m-lr-30 m-b-30 p-t flex ovh" :style="css.mbg" v-for="item in couponList" :key="item.id">
						<view class="l tc tc-w p-30" :style="css.mcbg">
							<view v-if="item.type == 'reward'">
								¥<text class="tb ts-46">{{$xyfun.pe(item.money)}}</text>
							</view>
							<view v-else>
								<text class="tb ts-46">{{$xyfun.pe(item.discount)}}</text>折
							</view>
							<view class="ts-26 m-t-15">
								<text v-if="item.atleast > 0">满{{$xyfun.pe(item.atleast)}}元可用</text>
								<text v-else>无限制</text>
							</view>
						</view>
						<view class="r p-30 psr">
							<view class="tb">{{item.name}}</view>
							<view class="ts-24 m-t-50">
								有效期:{{ $xyfun.timeFormat(item.endtime)}}
							</view>
							<view v-if="orderDetail.user_coupon_id == item.id" class="btn ts-26 br-20 p-lr-25" :style="css.pbg+css.tcl">已使用</view>
							<view v-else class="btn tc-w ts-26 br-20 p-lr-25" :style="css.mcbg" @tap="useCoupon(item.id)">使用</view>
						</view>
					</view>
				</view>
				<view class="tc m-40 p-40" :style="css.tcl" v-else>无可用优惠券</view>
				
				<view class="close ts-40" :style="css.tcl" @tap="couponModelShow = false"><text class="xyicon icon-close"></text></view>
			</view>
			<view class="xy-modal-mask" :class="[couponModelShow?'xy-mask-show':'']" @tap="couponModelShow =!couponModelShow"></view>
		</block>
		
		<!--支付弹窗-->
		<block v-if="payModelShow">
			<view class="xy-modal-box xy-modal-box-center pay-model-box p-t-30 br-10" :style="css.mbg" :class="[payModelShow?'xy-modal-show':'']">
				<view class="title m-b-50 ts-32 tc bl-b p-b-30" :style="css.blpc">支付方式</view>
				<view class="tc tb m-tb-40 ts-36">支付金额{{ orderDetail.totalfee || "0.00" }}元</view>
				<view class="pay-list">
					<view class="item flex p-30 m-b-2 lh-40" :style="css.mbg" v-for="(item, index) in payList" :key="index" v-if="item.state">
						<view class="l flex">
							<text :class="'xyicon icon-'+item.icon+' ts-40 m-r-15'"></text>
							<text class="lh-40">{{item.name}}</text>
						</view>
						<view class="r tb m-l-auto" @tap="payMethodSelect(index)">
							<text class="xyicon icon-radio-a ts-32 lh-40" v-if="item.select"></text>
							<text class="xyicon icon-radio ts-32 lh-40" v-else></text>
						</view>
					</view>
				</view>
				<view class="close" :style="css.tcl" @tap="closePay()"><text class="xyicon icon-close"></text></view>
				<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-30" @tap="subPay()">确认支付</button>
			</view>
			<view class="xy-modal-mask" :class="[payModelShow?'xy-mask-show':'']"></view>
		</block>
		
		<!-- 全日租日期选择弹窗 -->
		<date-calendar :minDate="minDate" :activeBgColor="common.appStyle.mainColor" activeColor="#ffffff" :rangeBgColor="common.appStyle.mainColor" v-model="showDaysModel" @change="leaseTimeChange" :isRange="true" :isTime="false"></date-calendar>
		
		<!-- 小时租日期选择弹窗 -->
		<date-calendar :minDate="minDate" :activeBgColor="common.appStyle.mainColor" activeColor="#ffffff" :rangeBgColor="common.appStyle.mainColor" v-model="showHourModel" @change="leaseTimeChange" :isRange="false" :isTime="true"></date-calendar>
		
		<!-- 过夜租日期选择弹窗 -->
		<date-calendar :minDate="minDate" :activeBgColor="common.appStyle.mainColor" activeColor="#ffffff" :rangeBgColor="common.appStyle.mainColor" v-model="showNightModel" @change="leaseTimeChange" :isRange="false" :isTime="false"></date-calendar>
		
	</view>
</template>

<script>
	import { mapState,mapActions } from 'vuex';
	import graceChecker from '@/utils/graceChecker';
	import Pay from '@/utils/pay';
	import timePicker from '@/components/xy-date/time-picker'
	import dateCalendar from '@/components/xy-date/lease-calendar';
	export default {
		components: {
			timePicker,
			dateCalendar
		},
		data() {
			return {
				css:{},
				disabled:false,
				isLoading:true,
				leaseconfig:{},
				showHourModel:false, 
				showDaysModel:false,
				showNightModel:false,
				type:'',//订单类型
				leaseType:'',//当前租赁方式
				leaseTypeList:[],//租赁方式列表
				leaseTypeIndex:0,//当前租赁方式索引
				startTime:'',//开始时间
				endTime:'',//结束时间
				leaseTime:'',//租用时间
				pickupTime:'',//租赁自提时间
				goodsList:[], //商品列表
				from:"", //来源:buynow=立即租,cart=租物车
				orderDetail:[], //订单详情
				deliveryType:'', //发货方式
				consignee:'', //提货人姓名
				mobile:'',	//提货人手机号
				remark:'',//备注
				payModelShow:false,
				order:null,//支付订单
				minDate:this.$xyfun.timeFormat(null,'yyyy-mm-dd'),
				user_coupon_id:0,
				couponList:[],
				couponModelShow:false,
				payList:[{
					name: '余额支付',
					method: 'balance',
					icon: 'balance',
					state: true,
					select: false
				},
				{
					name: '微信支付',
					method: 'wechat',
					icon: 'wechat',
					state: true,
					select: true
				}]
			}
		},
		computed: {
			...mapState(['common','user'])
		},
		onLoad() {
			var query = this.$Route.query;
			this.goodsList = query.goodsList;
			this.from = query.from;
			this.leaseType = query.leasetype;
			this.type = query.type;
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			
			...mapActions('cart',{'getCartList':'getList'}),
			
			loadData(){
				this.$api.post({
					url: '/order/init',
					loadingTip:'加载中...',
					data: {
						from:this.from,
						type: this.type,
						goodslist:this.goodsList,
						leasetype:this.leaseType,
						starttime:this.startTime,
						user_coupon_id:this.user_coupon_id,
						endtime:this.endTime
					},
					success: res => {
						this.isLoading = false;
						this.orderDetail = res;
						this.deliveryType = res.deliverytype;
						this.type = res.type;
						this.leaseType = res.leasetype;
						this.leaseTypeList = res.leasetypelist;
						this.startTime = res.starttime;
						this.endTime = res.endtime;
						this.leaseTime = res.leasetime;
						this.pickupTime = res.pickuptime;
						this.leaseTypeIndex = res.leasetypeindex;
						this.leaseconfig = res.leaseconfig;
					},
					fail: res =>{
						setTimeout(()=>{
							uni.navigateBack();
						},2000)
					}
				});
			},
			
			//租赁时间选择
			leaseTimeChange(e){
				console.log('this.leaseType',this.leaseType);
				console.log(e);
				if(this.leaseType == 'days'){
					this.startTime = this.$xyfun.strtotime(e.startDate);
					this.endTime = this.$xyfun.strtotime(e.endDate+' 23:59');
				}
				if(this.leaseType == 'night'){
					this.startTime = this.$xyfun.strtotime(e.result+' '+this.leaseconfig.nightst);
					this.endTime = '';
				}
				if(this.leaseType == 'hour'){
					this.startTime = this.$xyfun.strtotime(e.result+' '+e.startTime);
					this.endTime = this.$xyfun.strtotime(e.result+' '+e.endTime);;
				}
				this.loadData();
			},
			
			showLeaseTimeModel(){
				if(this.leaseType == 'hour'){
					this.showHourModel = true;
				}
				if(this.leaseType == 'days'){
					this.showDaysModel = true;
				}
				if(this.leaseType == 'night'){
					this.showNightModel = true;
				}
			},
			
			//切换配送方式
			changeExpress(express){
				this.deliveryType = express;
			},
			
			//支付方式选择
			payMethodSelect(key){
				
				this.payList.map((value,index) => {
				　　if(index == key){
						value.select = !value.select;
					}else{
						value.select = false;
					}
				});
			},
			
			//确认支付
			subPay(){
				if(this.order != null && this.disabled){
					var pay_type = '';
					this.payList.map((value) => {
					　　if(value.select){
							pay_type = value.method;
						}
					});
					if (!pay_type) {
						this.$xyfun.msg('请选择支付方式');
					}else{
						//发起支付
						var pay = new Pay(pay_type, this.order, 'order');
						pay.payMehod().then((res)=>{
							this.disabled = false;
							pay.payResult(res);
						},(res)=>{
							this.disabled = true;
							if(pay_type != 'balance'){
								pay.payResult(res);
							}
						});
					}
				}
			},
			
			//关闭支付
			closePay(){
				this.$Router.push({
					path: '/pages/user/pay/result',
					query: {
						orderId: this.order.id,
						payment: 'wechat',
						payState: 'fail',
						orderType: 'order'
					}
				});
			},
			
			//提交订单
			subOrder(){
				if (this.disabled && this.order != null) {
					this.payModelShow = true;
					return;
				}
				this.disabled = true;
				
				var data = {
						goodslist: this.goodsList,
						from: this.from,
						type: this.type,
						leasetype: this.leaseType,
						deliverytype: this.deliveryType,
						starttime: this.startTime,
						endtime: this.endTime,
						consignee: this.consignee,
						mobile: this.mobile,
						user_coupon_id: this.user_coupon_id,
						remark: this.remark
					};
				
				//定义表单规则
				var rule = [
					{ name: 'consignee', checkType: 'string', checkRule: '1,50', errorMsg: '请输入联系人姓名' },
					{ name: 'mobile', checkType: 'phoneno', errorMsg: '请输入正确的手机号' },
				];
				
				//进行表单检查
				var checkRes = graceChecker.check(data, rule);
				
				if (checkRes) {
					this.$api.post({
						url: '/order/add',
						data: data,
						success: res => {
							this.order = res;
							this.payModelShow = true;
							if(this.from == 'cart'){
								this.getCartList();
							}
						}
					});
				} else {
					this.$xyfun.msg(graceChecker.error);
					this.disabled = false;
				}
				
				
			},
			
			//切换租赁类型
			leaseTypeChange(e){
				if(this.leaseTypeIndex != e.detail.value){
					this.leaseTypeIndex = e.detail.value;
					this.leaseType = this.leaseTypeList[this.leaseTypeIndex]['value'];
					this.startTime = '';
					this.endTime = '';
					this.loadData();
				}
			},
			
			//用户可用优惠券
			showCoupon(){
				this.$api.post({
					url: '/user/coupon/lists',
					loadingTip:'加载中...',
					data: {
						store_id:this.store_id,
						atleast: this.orderDetail.totalamount,
						status:0
					},
					success: res => {
						this.couponModelShow = true;
						this.couponList = res.data;
					}
					
				});
			},
			
			//使用优惠券
			useCoupon(user_coupon_id){
				this.user_coupon_id = user_coupon_id;
				this.loadData();
				this.couponModelShow = false;
			},
			
		}
	}
</script>

<style scoped lang="scss">
	
	.confirm{
		padding-bottom: 200rpx;
	}
	
	.line{height: 4rpx;width: 34%;margin: 0 auto;}
	
	.servicetime-model-box{
		height: 900rpx;
		border-radius: 30rpx 30rpx 0 0;
	}
	
	.goods-box{
		.item{
			.l{
				width: 178rpx;height: 178rpx;
				image{width: 178rpx;}
			}
			.r{
				width: 432rpx;
				.price-box{bottom: 3rpx;width: 100%;}
			}
		}
		.goods-title{max-height: 80rpx;overflow: hidden;}
	}
	.bottom-fixed{
		.action{width: 580rpx;}
		.sub-order{width: 260rpx;height: 74rpx;border-radius: 37rpx;line-height: 74rpx;}
	}
	.pay-model-box{
		width: 80%;
		.close{position: absolute; right:30rpx;top: 30rpx;}
	}
	.form-item .r{
		width: 74%;
		flex-wrap: nowrap;
		input,picker,.picker{width: 100%;height: 80rpx;}
		textarea{height: 100rpx;}
	}
	
	.coupon-model-box{
		height: 900rpx;border-radius: 30rpx 30rpx 0 0;
		.ewm image{ width: 300rpx;height: 300rpx;}
		.coupon-list{height: 700rpx;overflow-y: scroll;}
		.close{position: absolute; right:30rpx;top: 30rpx;}
		
		.coupon-list{
			.item{
				.l{
					width: 180rpx;
				}
				.r{
					width: 390rpx;
					.btn{position: absolute; right: 30rpx;top: 30rpx;}
				}
			}
		}
		
	}
	
</style>