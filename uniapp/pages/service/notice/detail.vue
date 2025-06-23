<template>
	<view class="notice-detail p-40" :style="css.wpage+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!empty">
		<view class="tb ts-32">{{notice.title}}</view>
		<view class="ta m-b-30 m-t-20 flex ts-26" :style="css.tcl"><text>{{$xyfun.timeFormat(notice.createtime)}}</text> <text class="m-l-40">浏览 {{notice.views}}</text></view>
		<view class="content">
			<rich-text :nodes="notice.content"></rich-text>
		</view>
	</view>
</template>

<script>
	
	export default {
		
		data() {
			return {
				css:{},
				empty:true,
				notice:{},
			}
		},
		onLoad() {
			this.$xyfun.setNavBar();
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			
			loadData(){
				this.$api.get({
					url: '/notice/detail',
					loadingTip:'加载中...',
					data: {
						id:this.$Route.query.id,
					},
					success: res => {
						this.empty = false;
						this.notice = res;
						this.notice.content = this.notice.content.replace(/\<img/g, "<img style='max-width: 100%;vertical-align: middle;'");
					}
				});
			},
			
			
			
		}
	}
</script>

<style scoped lang="scss">
	.notice-detail{
		.content{text-align: justify;}
	}
</style>
