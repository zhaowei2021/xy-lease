<template>
	<view class="withdraw" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="withdraw-list" v-if="!isEmpty">
			<view class="item m-b-20 flex p-30" :style="css.mbg" v-for="item in withdrawList" :key="item.id">
				<view class="l">
					<view>{{item.accountname}}</view>
					<view :style="css.tcl" class="ts-28 m-t-15">{{$xyfun.timeFormat(item.createtime)}}</view>
					<view :style="css.tcl" class="ts-28 m-t-15" v-if="item.status == -1">拒绝原因：{{item.refusereason}}</view>
				</view>
				<view class="m-l-auto">
					<view class="tb" :style="css.tcp">{{item.applymoney}}</view>
					<view class="m-t-15">{{item.status_text}}</view>
				</view>
			</view>
		</view>
		<view v-else>
			<xy-empty text="暂无提现数据" />
		</view>
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
				type:'',
				isLoading:true,
				isEmpty: true,
				withdrawList: [],
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
			}
		},
		computed: {
			...mapState(['common'])
		},
		onLoad(options) {
			var options = this.$Route.query;
			this.type = options.type ? options.type : '';
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.currentPage = 1;
			this.withdrawList = [];
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
					this.withdrawList = [];
				}
				this.$api.post({
					url: '/user/withdraw/lists',
					loadingTip:'加载中...',
					data: {
						type:this.type,
						page: this.currentPage,
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.withdrawList = [...this.withdrawList, ...res.data];
						this.isEmpty = !this.withdrawList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					},
					fail: res =>{
						this.isLoading = false;
					}
				});
			},
			
		}
	}
</script>

<style scoped lang="scss">
</style>
