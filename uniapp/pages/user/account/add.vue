<template>
	<view class="account-add" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		
		<form @submit="formSubmit">
			<view class="form-item lh-70 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg">
				<text class="item-name">姓名</text>
				<view class="flex r m-l-auto">
					<input class="br-10 p-lr-20 lh-30" :style="css.pbg" placeholder="请输入姓名" :placeholder-style="css.tcl"  v-model="accountInfo.realname" />
				</view>
			</view>
			<view class="form-item lh-70 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg">
				<text class="item-name">手机号</text>
				<view class="flex r m-l-auto">
					<input class="br-10 p-lr-20 lh-30" :style="css.pbg" placeholder="请输入手机号" :placeholder-style="css.tcl" v-model="accountInfo.mobile" />
				</view>
			</view>
			
			<view class="form-item lh-70 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg">
				<text class="item-name">账号类型</text>
				<view class="flex r m-l-auto">
					<picker @change="accountTypeChange" :value="value" :range="accountType" range-key="name" >
						<view class="picker br-10 p-20 lh-30 flex" :style="css.pbg">
							<view :style="accountTypeIndex > -1 ? css.tcm : css.tcl">{{accountTypeIndex > -1 ? accountType[accountTypeIndex].name : '请选择账号类型'}}</view>
							<text :style="css.tcl" class="xyicon icon-right m-l-auto"></text>
						</view>
					</picker>
				</view>
			</view>
			
			<view class="form-item lh-70 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg" v-if="accountTypeIndex == 0">
				<text class="item-name">银行名称</text>
				<view class="flex r m-l-auto">
					<input class="br-10 p-lr-20 lh-30" :style="css.pbg" placeholder="请输入银行名称" :placeholder-style="css.tcl" v-model="accountInfo.accountname" maxlength="50" />
				</view>
			</view>
			
			<view class="form-item lh-70 flex p-lr-30 p-tb-15 m-b-2" :style="css.mbg">
				<text class="item-name">提现账号</text>
				<view class="flex r m-l-auto">
					<input class="br-10 p-lr-20 lh-30" :style="css.pbg" placeholder="请输入提现账号" :placeholder-style="css.tcl" v-model="accountInfo.accountno" maxlength="50" />
				</view>
			</view>
			
			<view class="bottom-fixed p-b-50" :style="css.pbg">
				<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-50" form-type="submit" :disabled="submitDisabled">保存</button>
			</view>
		</form>
		
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import graceChecker from '@/utils/graceChecker';
	export default {
		
		data() {
			return {
				css:{},
				submitDisabled:false,
				accountType:[{name:'银行卡',value:'bank'},{name:'支付宝',value:'alipay'}],
				accountTypeIndex:-1,
				accountInfo:{
					id:0,
					realname:'',
					mobile:'',
					type:'',
					accountname:'',
					accountno:''
				},
			}
		},
		computed: {
			...mapState(['common','user'])
		},
		onLoad(options) {
			this.accountInfo.id = options.id ? options.id :0;
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			if(options.id){
				this.loadData();
			}
		},
		methods: {
			
			
			loadData(){
				this.$api.post({
					url: '/user/account/detail',
					loadingTip:'加载中...',
					data: {
						id:this.accountInfo.id,
					},
					success: res => {
						this.accountInfo = res;
						this.accountType.forEach((item,index)=>{
							if(item.value == this.accountInfo.type){
								this.accountTypeIndex = index;
							}
						});
					}
				});
			},
			
			accountTypeChange(e){
				this.accountTypeIndex = e.detail.value;
				this.accountInfo.type = this.accountType[e.detail.value].value;
				if(this.accountTypeIndex == 1){
					this.accountInfo.accountname = '支付宝';
				}else{
					this.accountInfo.accountname = '';
				}
				
			},
			
			// 保存
			formSubmit() {
				var accountInfo = this.accountInfo;
				console.log(accountInfo);
				if(this.submitDisabled){
					return false;
				}
				this.submitDisabled = true;
				
				//定义表单规则
				var rule = [
					{ name: 'realname', checkType: 'string', checkRule: '2,10', errorMsg: '请输入姓名' },
					{ name: 'mobile', checkType: 'phoneno', errorMsg: '请输入正确的手机号' },
					{ name: 'type', checkType: 'string', checkRule: '2,50', errorMsg: '请选择账号类型' },
					
				];
				if(accountInfo.type == 'bank'){
					rule.push({ name: 'accountname', checkType: 'string', checkRule: '2,50', errorMsg: '请输入银行名称' });
				}
				rule.push({ name: 'accountno', checkType: 'string', checkRule: '2,50', errorMsg: '请输入提现账号' });
				
				//进行表单检查
				var checkRes = graceChecker.check(accountInfo, rule);
				
				if (checkRes) {
					
					this.$api.post({
						url: '/user/account/add',
						loadingTip:'提交中...',
						data: accountInfo,
						success: (res) => {
							this.$xyfun.prePage().loadData(true);
							this.$xyfun.back('保存成功');
						},
						fail(){
							this.submitDisabled = false;
						}
					});
				} else {
					this.$xyfun.msg(graceChecker.error);
					this.submitDisabled = false;
				}
				
			},
		}
	}
</script>

<style scoped lang="scss">
	.avatar{width: 100rpx;height: 100rpx;}
	.form-item .r{
		width: 70%;
		input,picker{width: 100%;height: 70rpx;}
	}
</style>
