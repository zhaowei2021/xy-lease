<template>
	<view class="apply" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="user-list lh-70 flex p-lr-30 p-tb-25 m-b-10" :style="css.mbg" @tap="$xyfun.to('/pages/user/account/list')">
			<text class="list-name">{{withdrawInfo.account?'提现到':'请添加提现账户'}}</text>
			<view class="flex m-l-auto">
				<view v-if="withdrawInfo.account">
					<text class="m-r-15" :class="'xyicon icon-'+withdrawInfo.account.type"></text>
					{{withdrawInfo.account.accountno}}
				</view>
				<text class="xyicon icon-right m-l-15"></text>
			</view>
		</view>
		<view class="user-list p-lr-30 p-tb-25 m-b-10" :style="css.mbg">
			<view class="list-name">提现金额：</view>
			<view class="flex ts-42 tb m-t-25">
				<text class="p-tb-20 lh-42">¥</text>
				<input class="p-tb-20 lh-42 m-l-20" type="number" v-model="applyMoney" />
			</view>
		</view>
		<view class="user-list p-lr-30 lh-30 p-tb-25 m-b-10 ts-26" :style="css.mbg+css.tcl">
			<view>可提现: ￥{{withdrawInfo.able}} <text class="ts-28 m-l-30" :style="css.tcmc" @tap="allMoney()">全部提现</text></view>
			<view class="m-tb-15">最小提现金额为￥{{withdrawInfo.min}}，最大提现金额为￥{{withdrawInfo.max}}</view>
			<view>提现手续费为{{withdrawInfo.rate}}%</view>
		</view>
		<view class="m-50" :style="css.pbg">
			<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-t-50" @tap="apply()">提现</button>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	export default {
		data() {
			return {
				css:{},
				disabled:false,
				isLoading:true,
				store_id:0,
				type:'',//提现类型 distribution=分销 coach=老师
				withdrawInfo:{
					able:0.00, //可提现金额
					min:0.00, //最小提现金额
					max:0.00, //最大提现金额
					rate:0, //手续费比例
					account:null, //提现账号
				},
				applyMoney:'',//提现申请金额
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad(options) {
			this.type = options.type ? options.type : '';
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		
		methods: {
			
			loadData(){
				this.$api.post({
					url: '/user/withdraw/init',
					loadingTip:'加载中...',
					data: {
						type:this.type,
					},
					success: res => {
						this.withdrawInfo = res.data;
						this.isLoading = false;
					}
				});
			},
			
			// 全部提现
			allMoney() {
				this.applyMoney = this.withdrawInfo.able;
			},
			
			// 提交申请
			apply() {
				
				if(this.disabled){
					return false;
				}
				
				if (this.withdrawInfo.account == null) {
					this.$xyfun.msg('请添加提现账户');
					return false;
				}
				
				if (this.applyMoney == '') {
					this.$xyfun.msg('请输入提现金额');
					return false;
				}
				
				
				if(this.applyMoney > parseFloat(this.withdrawInfo.able)){
					this.$xyfun.msg('提现金额超出可提现金额');
					return false;
				}
				
				if(this.applyMoney < parseFloat(this.withdrawInfo.min)){
					this.$xyfun.msg('最小提现金额为'+this.withdrawInfo.min);
					return false;
				}
				
				if(this.applyMoney > parseFloat(this.withdrawInfo.max)){
					this.$xyfun.msg('最大提现金额为'+this.withdrawInfo.max);
					return false;
				}
				
				this.disabled = true;
				this.$api.post({
					url: '/user/withdraw/add',
					loadingTip:'提交中...',
					data: {
						type:this.type,
						applymoney:this.applyMoney,
						realname:this.withdrawInfo.account.realname,
						mobile:this.withdrawInfo.account.mobile,
						accounttype:this.withdrawInfo.account.type,
						accountname:this.withdrawInfo.account.accountname,
						accountno:this.withdrawInfo.account.accountno
					},
					success: () => {
						this.$xyfun.prePage().loadData();
						this.$xyfun.back('申请成功');
					},
					fail(){
						this.disabled = false;
					}
				});
				
			},
		}
	}
</script>

<style scoped lang="scss">
	.user-list {
		input{width: 90%;}
	}
</style>
