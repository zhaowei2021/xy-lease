
//数据验证（表单验证）
export default {
	error:'',
	check : function (data, rule){
		for(var i = 0; i < rule.length; i++){
			if (!rule[i].checkType){return true;}
			if (!rule[i].name) {return true;}
			if (!rule[i].errorMsg) {return true;}
			if (!data[rule[i].name]) {this.error = rule[i].errorMsg; return false;}
			switch (rule[i].checkType){
				case 'string':   //类型判断
					var reg = new RegExp('^.{' + rule[i].checkRule + '}$');
					if(!reg.test(data[rule[i].name])) {this.error = rule[i].errorMsg; return false;}
				break;
				case 'int':     
					var reg = new RegExp('^(-[1-9]|[1-9])[0-9]{' + rule[i].checkRule + '}$');
					if(!reg.test(data[rule[i].name])) {this.error = rule[i].errorMsg; return false;}
					break;
				break;
				case 'between':    //区间判断
					if (!this.isNumber(data[rule[i].name])){
						this.error = rule[i].errorMsg;
						return false;
					}
					var minMax = rule[i].checkRule.split(',');
					minMax[0] = Number(minMax[0]);
					minMax[1] = Number(minMax[1]);
					if (data[rule[i].name] > minMax[1] || data[rule[i].name] < minMax[0]) {
						this.error = rule[i].errorMsg;
						return false;
					}
				break;
				case 'betweenD':      //Double验证
					var reg = /^-?[1-9][0-9]?$/;
					if (!reg.test(data[rule[i].name])) { 
						this.error = rule[i].errorMsg;
						return false;
					}
					var minMax = rule[i].checkRule.split(',');
					minMax[0] = Number(minMax[0]);
					minMax[1] = Number(minMax[1]);
					if (data[rule[i].name] > minMax[1] || data[rule[i].name] < minMax[0]) {
						this.error = rule[i].errorMsg;
						return false;
					}
				break;
				case 'betweenF':       //Float验证
					var reg = /^-?[0-9][0-9]?.+[0-9]+$/;
					if (!reg.test(data[rule[i].name])){
						this.error = rule[i].errorMsg; 
						return false;
					}
					var minMax = rule[i].checkRule.split(',');
					minMax[0] = Number(minMax[0]);
					minMax[1] = Number(minMax[1]);
					if (data[rule[i].name] > minMax[1] || data[rule[i].name] < minMax[0]) {
						this.error = rule[i].errorMsg;
						return false;
					}
				break;
				case 'same':      //一致性验证                    
					if (data[rule[i].name] != rule[i].checkRule) { 
						this.error = rule[i].errorMsg; 
						return false;
					}
				break;
				case 'notsame':    
					if (data[rule[i].name] == rule[i].checkRule) { 
						this.error = rule[i].errorMsg; 
						return false; 
					}
				break;
				case 'email':    //邮箱验证
					var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
					if (!reg.test(data[rule[i].name])) { 
						this.error = rule[i].errorMsg; 
						return false; 
					}
				break;
				case 'phoneno':    //手机号码验证
					var reg = /^1[0-9]{10,10}$/;
					if (!reg.test(data[rule[i].name])) { 
						this.error = rule[i].errorMsg; 
						return false; 
					}
				break;
				case 'zipcode':    
					var reg = /^[0-9]{6}$/;
					if (!reg.test(data[rule[i].name])) { 
						this.error = rule[i].errorMsg; 
						return false; 
					}
				break;
				case 'reg':  //正则
					var reg = new RegExp(rule[i].checkRule);
					if (!reg.test(data[rule[i].name])) { 
						this.error = rule[i].errorMsg; 
						return false; 
					}
				break;
				case 'in':    //包含验证
					if(rule[i].checkRule.indexOf(data[rule[i].name]) == -1){
						this.error = rule[i].errorMsg; return false;
					}
				break;
				case 'notnull':    //非空
					if(data[rule[i].name] == null || data[rule[i].name].length < 1){
						this.error = rule[i].errorMsg; 
						return false;
					}
				break;
			}
		}
		return true;
	},
	isNumber : function (checkVal){
		var reg = /^-?[1-9][0-9]?.?[0-9]*$/;
		return reg.test(checkVal);
	}
}
