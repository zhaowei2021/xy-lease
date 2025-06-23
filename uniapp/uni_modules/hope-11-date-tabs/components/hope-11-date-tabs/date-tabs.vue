<template>
	<view class="date-tabs-container" :style="{backgroundColor: bgColor ? bgColor : ''}">
		<view class="tabs-wrapper">
			<scroll-view scroll-x :show-scrollbar="false" :scroll-left="scrollLeft" scroll-with-animation class="scroll-view">
				<view class="date-wrapper">
					<view v-for="(item, index) in list" :key="index" class="date-item" @click="onItemClick(index)">
						<view class="week" :style="{color: index === current ? color : ''}">{{ item.w }}</view>
						<view class="date" :class="{current : index === current}" :style="{
								backgroundColor: index === current && !plain ? color : '',
								borderColor: index === current ? color : '',
								color: index === current && plain ? color : '',
								borderRadius: index === current && circle ? '50%' : ''
							}">{{ item.d }}</view>
					</view>
				</view>
			</scroll-view>
		</view>
		<view class="calendar-button" @click="onOpenCalendar">
			<Icons v-if="plain" type="calendar" size="36" :color="color"></Icons>
			<Icons v-else type="calendar-filled" size="36" :color="color"></Icons>
		</view>

		<Calendar ref="calendar" :insert="false" :date="pickerValue" :startDate="calendarStartDate"
			:endDate="calendarEndDate" :color="color" :plain="plain" :circle="circle" @confirm="onCalendarConfirm"></Calendar>
	</view>
</template>
<script>
	import dayjs from './dayjs.min.js'
	import Calendar from './uni-calendar/uni-calendar.vue'
	import Icons from './uni-icons/uni-icons.vue'

	export default {
		name: "DateTabs",
		components: {
			Calendar,
			Icons,
		},
		props: {
			value: {
				type: String,
				default: ''
			},
			startDate: {
				type: String,
				default: ''
			},
			endDate: {
				type: String,
				default: ''
			},
			color: {
				type: String,
				default: '#007aff'
			},
			bgColor: {
				type: String,
				default: 'white'
			},
			plain: {
				type: Boolean,
				default: false
			},
			circle: {
				type: Boolean,
				default: false,
			}
		},
		data() {
			return {
				pickerValue: '',
				list: [],
				current: 0,
				scrollLeft: 0,
				dateItemWidth: 0,
				weekdays: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
			}
		},
		computed: {
			calendarStartDate() {
				return this.startDate || dayjs().format('YYYY-MM-DD')
			},
			calendarEndDate() {
				return this.endDate || dayjs(this.calendarStartDate).add(27, 'd').format('YYYY-MM-DD')
			}
		},
		watch: {
			dateItemWidth(newVal, oldVal) {
				for (let i = 0; i < this.list.length; i++) {
					if (this.list[i].dd === this.value) {
						this.scrollLeft = this.dateItemWidth * i + Math.random()
						break
					}
				}
			}
		},
		created() {
			this.initList()
		},
		mounted() {
			setTimeout(()=>{
				const query = uni.createSelectorQuery().in(this)
				query.select('.date-item').boundingClientRect(res => {
					this.dateItemWidth = res.width
				}).exec()
			},500)
		},
		methods: {
			initList() {
				const length = dayjs(this.calendarEndDate).diff(this.calendarStartDate, 'day')
				for (let i = 0; i <= length; i++) {
					const date = dayjs(this.calendarStartDate).add(i, 'd')
					this.list.push({
						date: date.toDate(),
						dd: date.format('YYYY-MM-DD'),
						d: date.format('M-D'),
						w: date.isSame(dayjs(), 'day') ? '今' : (date.format('D') === '1' ? date.format('M月') : this.weekdays[date.day()])
					})
				}
				this.onCalendarConfirm({
					fulldate: this.value || dayjs().format('YYYY-MM-DD')
				})
			},
			onItemClick(index) {
				this.current = index
				this.$emit('update:value', this.list[this.current].dd)
				this.$emit('change', this.list[this.current])
			},
			onOpenCalendar() {
				this.pickerValue = this.list[this.current].dd
				this.$refs.calendar.open()
			},
			onCalendarConfirm(e) {
				for (let i = 0; i < this.list.length; i++) {
					if (this.list[i].dd === e.fulldate) {
						this.onItemClick(i)
						this.scrollLeft = this.dateItemWidth * i + Math.random()
						break
					}
				}
			}
		}
	}
</script>
<style lang="scss">
	.date-tabs-container {
		width: 100vw;
		height: 120rpx;
		box-shadow: 0 10rpx 10rpx $uni-bg-color-grey;
		display: flex;
		justify-content: space-between;
		align-items: center;

		.tabs-wrapper {
			width: calc(100% - 100rpx);

			scroll-view {
				height: 100%;
				padding-bottom: 4px;

				// 滚动条的宽度
				::-webkit-scrollbar {
					height: 6px !important;
				}

				::-webkit-scrollbar-track-piece {
					background-color: rgba(144, 147, 153, 0);
				}

				// 滚动条的设置
				::-webkit-scrollbar-thumb {
					background-color: rgba(144, 147, 153, 0.3);
					background-clip: padding-box;
					min-height: 28px;
					border-radius: 3px;
					transition: 0.3s background-color;
				}

				::-webkit-scrollbar-thumb:hover {
					background-color: rgba(144, 147, 153, 0.5);
				}

				.date-wrapper {
					display: flex;

					.date-item {
						height: 120rpx;
						display: flex;
						flex-direction: column;
						justify-content: center;
						align-items: center;

						view {
							width: 90rpx;
							margin: 5rpx 20rpx;
							display: flex;
							justify-content: center;
							align-items: center;
						}

						.week {
							font-size: 24rpx;
							color: grey;
						}

						.date {
							height: 60rpx;
							white-space: nowrap;
						}

						.current {
							box-sizing: border-box;
							border-width: 2px;
							border-style: solid;
							border-radius: 4px;
							color: white;
							font-weight: bold;
						}
					}
				}
			}
		}

		.calendar-button {
			width: 120rpx;
			height: 100%;
			box-shadow: -10rpx 0 10rpx $uni-bg-color-grey;
			display: flex;
			justify-content: center;
			align-items: center;
		}
	}
</style>