<template>
	<view class="team" :style="css.page+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="tab flex tc tb p-tb-25" :style="css.mbg">
			<view v-for="(item,index) in teamLevel" :class="'col-'+teamLevel.length" @tap="setTab(index)">
				<view :style="teamLevelIndex == index ? css.tcmc : css.tcm">
					{{item.name}}
					<view class="line" :style="teamLevelIndex == index ? css.mcbg : css.mbg"></view>
				</view>
			</view>
		</view>
		<view class="team-list p-tb-50" v-if="!isEmpty">
			<view class="item br-10 m-lr-30 m-b-30 flex p-30" :style="css.mbg" v-for="item in teamList" :key="item.id">
				<view class="l">
					<image :src="item.avatar" />
				</view>
				<view class="r m-l-20">
					<view class="m-t-5 lh-30 m-t-10">{{item.nickname}}</view>
					<view class="ts-26 m-t-20 lh-26" :style="css.tcl">{{$xyfun.timeFormat(item.createtime)}}</view>
				</view>
				<view class="m-l-auto ts-28 lh-28 tr">
					<view class="m-t-10">消费</view>
					<view class="m-t-20">{{item.xylease_consume}}元</view>
				</view>
			</view>
		</view>
		<view v-else>
			<xy-empty text="暂无成员" />
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
				isLoading:true,
				isEmpty: true,
				teamList: [],
				currentPage: 1,
				lastPage: 1,
				loadStatus: 'loadmore',
				teamLevel:[{name:'一级',vaule:1},{name:'二级',value:2}],
				teamLevelIndex:0,
			}
		},
		computed: {
			...mapState(['common'])
		},
		async onLoad() {
			
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		onPullDownRefresh() {
			this.currentPage = 1;
			this.teamList = [];
			this.loadData();
		},
		onReachBottom() {
			if(this.currentPage < this.lastPage) {
				this.currentPage += 1;
				this.loadData();
			}
		},
		methods: {
			async loadData(){
				var level = this.teamLevel[this.teamLevelIndex]['value'];
				
				this.$api.post({
					url: '/distribution/center/team',
					loadingTip:'加载中...',
					data: {
						page: this.currentPage,
						level:level
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.isLoading = false;
						this.teamList = [...this.teamList, ...res.data];
						this.isEmpty = !this.teamList.length;
						this.currentPage = res.current_page; 
						this.lastPage = res.last_page;
						this.loadStatus = this.currentPage < res.last_page ? 'loadmore' : 'nomore';
					}
				});
			},
			
			setTab(index){
				console.log(index)
				this.teamLevelIndex = index;
				this.currentPage = 1;
				this.teamList = [];
				this.loadData();
			},
			
		}
	}
</script>

<style scoped lang="scss">
	.tab{
		width: 100%;
		.line{height: 4rpx;width: 60rpx;margin: 10rpx auto 0;}
	}
	.team-list{
		.item .l{
			image{width: 96rpx;height: 96rpx;border-radius: 48rpx;}
		}
	}
</style>