<template>
	<view class="staff" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="top p-50 flex" :style="css.mcbg">
			<view class="staff-info flex tc-w">
				<image class="headimage" :src="staffInfo.headimage" />
				<view class="m-l-15">
					<view class="tb m-t-10">{{staffInfo.realname}}</view>
					<view>{{staffInfo.mobile}}</view>
				</view>
			</view>
			<view class="home bc-w p-lr-25 flex m-l-auto" :style="css.tcmc" @tap="$xyfun.to('/pages/index')"><text class="xyicon icon-home m-r-10"></text>返回</view>
		</view>
		
		<view class="menu-list m-50 flex p-t-20 br-20" :style="css.mbg">
			<view class="item tc bc-w col-2 p-tb-40 br-20 tb" v-for="(item,index) in menuList" :key="index">
				<view v-if="item.url == 'scan'" @tap="scan()">
					<image :src="item.image" />
					<view class="m-tb-20">{{item.name}}</view>
				</view>
				<view @tap="$xyfun.to(item.url)" v-else>
					<image :src="item.image" />
					<view class="m-tb-20">{{item.name}}</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				css:{},
				role:'staff',
				isLoading:true,
				staffInfo:{},
				menuList:[
					{name:'扫会员码',url:'scan',image:'/static/images/staff/menu0.png'},
					{name:'租赁订单',url:'/pages/staff/order/list',image:'/static/images/staff/menu3.png'},
				],
			}
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			
			loadData(){
					
				this.$api.post({
					url: '/staff/clerk/info',
					success: res => {
						this.isLoading = false;
						this.staffInfo = res;
					},
					fail: res => {
						setTimeout(()=>{
							//uni.navigateBack();
						},3000)
					},
				});
				
			},
			
			//扫会员码
			scan(){
				
				var that = this;
				uni.scanCode({
					success: function (res) {
						if(res.scanType == 'QR_CODE' && res.result !=''){
							
							var result = res.result.split("_");
							var user_id = result.length ? result[0] : 0;
							
							if(user_id > 0){
								var curTime =Date.parse(new Date()) / 1000;
								var time = result[1];
								//if(curTime - time > 10*60){
									//that.$xyfun.msg('会员码已过期！');
								//}else{
								that.$xyfun.to('/pages/staff/user/info?user_id='+user_id);
								//}
							}else{
								that.$xyfun.msg('错误的会员码！');
							}
						}else{
							that.$xyfun.msg('错误的会员码！');
						}
					}
				});
			}
		}
	}
</script>

<style scoped lang="scss">
	.staff{
		
		.top{
			.staff-info{
				.headimage{width: 100rpx;height: 100rpx;border-radius: 50rpx;}
			}
			.home{height: 54rpx;border-radius: 27rpx;line-height: 54rpx;margin-top: 23rpx;}
		}
		
		.menu-list{
			image{width: 100rpx;height: 100rpx;}
		}
		
	}
</style>