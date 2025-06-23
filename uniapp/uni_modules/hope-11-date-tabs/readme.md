# date-tabs

### 使用
`<DateTabs :value.sync="value1" @change='onDateTabsChange'></DateTabs>`

### 属性
参数|说明|类型|默认值
---|:--|:--|:---:
value|绑定值，加.sync同步，格式：YYYY-MM-DD|String|当前日期
startDate|开始日期，格式：YYYY-MM-DD|String|当前日期
endDate|结束日期，格式：YYYY-MM-DD|String|开始日期后第28天
color|主题颜色|String|#007aff
bgColor|背景颜色|String|white
plain|朴素样式|Boolean|false
circle|圆形样式|Boolean|false

### 事件
事件名称|说明|回调参数
---|:--|:---:
change|绑定值变化时触发的事件|date：原生Date对象，dd：日期，d：日，w：星期

### 说明
该组件还未进行多平台详尽测试，可自行尝试使用，如有问题请在评论区指出或联系作者，多谢支持！