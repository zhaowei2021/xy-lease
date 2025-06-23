<template>
	<view class="distribution" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="top">
			<view class="level p-30 flex tc-w" :style="css.mcbg">
				<view class="l"><image :src="distributionInfo.levelInfo.image" /></view>
				<view class="c m-l-15">
					<view class="tb lh-30 m-t-15">{{distributionInfo.realname}}</view>
					<view class="ts-28 m-t-10">{{distributionInfo.levelInfo.name}}</view>
				</view>
			</view>
		</view>
		
		<view class="money m-30 p-30 br-10" :style="css.mbg">
			<view class="flex">
				<view>
					<view :style="css.tcl">可提现（元）</view>
					<view class="ts-40 m-t-15 " :style="css.tcp">{{distributionInfo.commission||0.00}}</view>
				</view>
				<view class="m-l-auto" :style="css.tcl" @tap="$xyfun.to('/pages/user/withdraw/list?type=distribution')">
					提现记录
					<text class="xyicon icon-right"></text>
				</view>
			</view>
			<view class="flex m-t-40">
				<view class="col-3">
					<view :style="css.tcl">总佣金</view>
					<view class="ts-40 m-t-15">{{distributionInfo.total_commission||0.00}}</view>
				</view>
				<view class="col-3">
					<view :style="css.tcl">已提现佣金</view>
					<view class="ts-40 m-t-15">{{distributionInfo.withdrawn_commission||0.00}}</view>
				</view>
				<view class="col-3">
					<view :style="css.tcl">提现中佣金</view>
					<view class="ts-40 m-t-15">{{distributionInfo.withdrawing_commission||0.00}}</view>
				</view>
			</view>
			<button :style="css.mcbg" class="withdraw-btn ts-30 lh-30 p-25 tc-w m-t-50" @tap="$xyfun.to('/pages/user/withdraw/apply?type=distribution')">申请提现</button>
		</view>
		
		<view class="menu-list m-30 p-lr-30 p-t-50 flex br-10" :style="css.mbg">
			<view class="item col-2 flex m-b-50" v-for="(item,index) in menuList" :key="index" @tap="menuLink(index,item.url)">
				<image :src="'/static/images/distribution/menu'+index+'.png'" />
				<view class="m-l-20 lh-30">
					<view class="tb m-t-15">{{item.name}}</view>
					<view :style="css.tcl" class="ts-26 m-t-10">{{ index == 0 ? (distributionInfo.teamNum.all_num||0)+'人'  : item.des}}</view>
				</view>
			</view>
		</view>
		
		<!--分享组件 -->
		<xy-share v-model="showShare" posterType="user" />
		
	</view>
</template>

<script>
	import { mapState,mapActions } from 'vuex';
	import xyShare from '@/components/xy-share';
	export default {
		components: {
			xyShare
		},
		data() {
			return {
				css:{},
				role:'distribution',
				distributionInfo:{},
				storeInfo:{},
				isLoading:true,
				showShare:false,
				menuList:[
					{name:'我的团队',url:'/pages/distribution/team'},
					{name:'推广海报',url:'',des:'邀请好友'},
					{name:'分销订单',url:'/pages/distribution/order',des:'分销订单明细'},
					{name:'账单报表',url:'/pages/distribution/bill',des:'佣金变更明细'},
				],
			}
		},
		computed: {
			...mapState(['common','user'])
		},
		onLoad() {
			
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.loadData();
		},
		methods: {
			
			...mapActions('user',{'getDistributionInfo':'getDistributionInfo'}),
			
			loadData(){
				this.getDistributionInfo().then(res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.distributionInfo = res;
					},()=>{
						setTimeout(()=>{
							uni.navigateBack();
						},3000)
					});
				
			},
			
			menuLink(index,path){
				if(index == 1){
					this.showShare = true;
				}else{
					this.$xyfun.to(path);
				}
			},
			
		}
	}
</script>

<style scoped lang="scss">
	.distribution{
		.top{
			.level{
				.l{
					image{width: 100rpx;height: 100rpx;border-radius: 50%;}
				}
				.r{height: 60rpx;line-height: 60rpx;border-radius: 30rpx;}
			}
		}
		
		.money{
			.withdraw-btn{border-radius: 40rpx; width: 500rpx;}
		}
		
		.menu-list{
			image{width: 90rpx;height: 90rpx;}
		}
		
	}
</style>