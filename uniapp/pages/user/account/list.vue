<template>
	<view class="account" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'">
		<block v-if="!isLoading">
			<view class="account-list" v-if="!isEmpty">
				<view class="item m-b-30 p-40" :style="css.mbg" v-for="item in accountList" :key="item.id">
					<view><text class="m-r-15 ts-34" :class="'xyicon icon-'+item.type"></text><text class="tb ts-32">{{item.accountname}}</text></view>
					<view class="m-t-25 m-b-15 flex" @tap="$xyfun.to('/pages/user/account/add?id='+item.id)">
						<text @tap="select(item)">{{item.realname}}</text>
						<text class="m-l-20" @tap="select(item)">{{item.mobile}}</text>
						<text class="m-l-auto xyicon icon-edit"></text>
					</view>
					<view @tap="select(item)">提现账号：{{item.accountno}}</view>
					<view :style="css.blpc" class="bl-t flex m-t-40 p-t-30">
						<view class="l" :style="css.tcl" @tap="select(item)">
							<text class="xyicon icon-radio-a tb m-r-15" :style="css.tcp" v-if="item.is_default"></text>
							<text class="xyicon icon-radio tb m-r-15" v-else></text>
							<text>{{item.is_default ? '默认账户' : '设为默认'}}</text>
						</view>
						<view class="m-l-auto" v-if="!item.is_default" @tap="del(item.id)">
							<text class="xyicon icon-del"></text>
						</view>
					</view>
				</view>
			</view>
			<view v-else>
				<xy-empty text="暂无账户信息,请添加" />
			</view>
			<view class="bottom-fixed p-b-50" :style="css.pbg">
				<button :style="css.mcbg" class="ts-30 lh-30 p-25 tc-w m-t-50 m-50" @tap="$xyfun.to('/pages/user/account/add')">新增账户</button>
			</view>
		</block>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import xyEmpty from '@/components/xy-empty';
	export default {
		components: {
			xyEmpty,
		},
		data() {
			return {
				css:{},
				isLoading:true,
				isEmpty: true,
				accountList: [],
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad() {
			
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.currentPage = 1;
			this.accountList = [];
			this.loadData();
		},
		onReachBottom() {
			if(this.currentPage < this.lastPage) {
				this.currentPage += 1;
				this.loadData();
			}
		},
		methods: {
			loadData(isReload = false){
				if(isReload){
					this.currentPage = 1;
					this.accountList = [];
				}
				this.$api.post({
					url: '/user/account/lists',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.accountList = [...this.accountList, ...res.data];
						this.isEmpty = !this.accountList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					}
				});
			},
			
			del(id){
				var that = this;
				uni.showModal({
					title:'确定要删除该账户吗？',
					success(res) {
						if(res.confirm){
							that.$api.post({
								url: '/user/account/del',
								data: {
									id: id,
								},
								success: () => {
									that.$xyfun.msg('删除成功');
									that.loadData(true);
								}
							});
						}
					}
				})
			},
			
			select(item){
				if(item.is_default){
					this.$xyfun.prePage().loadData();
					this.$xyfun.back();
				}else{
					this.accountList.forEach((item1,index)=>{
						if(item1.id == item.id){
							this.accountList[index]['is_default'] = 1;
						}else{
							this.accountList[index]['is_default'] = 0;
						}
					})
					this.$api.post({
						url: '/user/account/default',
						data: {
							id: item.id,
						},
						success: () => {
							this.$xyfun.prePage().loadData();
							this.$xyfun.back();
						}
					});
				}
			}
			
			
		}
	}
</script>

<style scoped lang="scss">
	.account-list{padding-bottom: 220rpx;}
</style>
