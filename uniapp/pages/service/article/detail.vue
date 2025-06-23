<template>
	<view class="article-detail bc-w" :style="css.wpage+'min-height:'+$xyfun.xysys().windowHeight+'px'" v-if="!isLoading">
		<view class="content p-30">
			<rich-text :nodes="article.content"></rich-text>
		</view>
	</view>
</template>

<script>
	
	export default {
		
		data() {
			return {
				css:{},
				isLoading:true,
				id: 103,
				article:{},
			}
		},
		onLoad() {
			this.css = this.$xyfun.css();
			this.loadData();
		},
		methods: {
			
			loadData(){
				this.$api.get({
					url: '/article/detail',
					loadingTip:'加载中...',
					data: {
						id:this.$Route.query.id,
					},
					success: res => {
						this.isLoading = false;
						this.article = res;
						this.article.content = this.article.content.replace(/\<img/g, "<img style='max-width: 100%;vertical-align: middle;'");
						this.$xyfun.setNavBar(res.title);
					}
				});
			},
			
			
			
		}
	}
</script>

<style scoped lang="scss">
	.article-detail{
		.content{text-align: justify;}
	}
</style>
