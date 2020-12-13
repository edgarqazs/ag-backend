/**
 * 
 * 三级联动
 * 
 * <div id="im_is_a_box"></div> <script src="/static/js/select3.js"></script>
 * <script> $("#im_is_a_box").select3({ url:'<?=Url::create('xxx')?>'
 * ,reqParamter:{ name:'parent_id' //请求时 字段名称 ,value:0 //默认值 } ,names:[
 * 'area1','area2','area3' //这里是select 的name值 ] ,resFileds:{ text:
 * 'option里面的用于显示的字段名称 例如: text ' value :'option 的value='' 的字段名称 例如:id ' } });
 * 
 * 
 * 
 * $("#im_is_a_box").selectedValue('area1');//获取area1选中的值
 * $("#im_is_a_box").selectedText('area1');//获取area1选中的文本
 * 
 * 
 * <script>
 * 
 * @author wang QQ1057451212
 * 
 * @returns {___anonymous1590_2050}
 */

$.fn.extend((function() {
	var config = {
		url : '我是请求地址',
		reqParamter : {
			name : 'pid',
			value : '0'
		},
		names : []// 各个select的name值
		,
		resFileds : {
			text : 'text',
			value : 'id'
		},
		values : [],
		cls : ''
	// 如果设置的话是为了初始
	};
	function createHtml(name, data, idx) {
		var html = [];
		var resFields = config.resFileds;
		html.push("<select name='" + name + "' idx='" + idx + "' class='"
				+ config.cls + "'>");
		html.push("<option value=''>请选择</option>");
		for (var i = 0; i < data.length; i++) {
			var item = data[i];
			html.push("<option value='" + item[resFields.value] + "'>"
					+ item[resFields.text] + "</option>");
		}
		html.push("</select>");
		return html.join('');
	}

	function getData(idx, pramas) {
		var that = this;
		while ($(that).children("select").length > idx) {
			$(that).children("select").last().remove();
		}

		if (config.names[idx]) {
			var name = config.names[idx];
			var key = config.reqParamter.name;
			if (pramas[key] !== '') {
				$.post(config.url, pramas, function(json) {
					
					if (json.data && json.data.length) {
						var html = createHtml(name, json.data, idx);
						that.append(html);
						if (config.values.length) {
							that.find("select").last().val(
									config.values.shift()).trigger('change');
						}
					} else {
						that.html('');
					}
				},'json');
			}
		}
	}

	function init(cfg) {
		
		$.extend(config, cfg);
		if (config.names.length) {
			var that = this;
			var pramas = {};
			pramas[config.reqParamter.name] = config.reqParamter.value;
			getData.call(that, 0, pramas);
			$(this).on("change", "select", function() {
				var value = this.value;
				var idx = $(this).attr('idx') * 1;
				if ((idx + 1) < config.names.length) {
					var pramas = {};

					pramas[config.reqParamter.name] = value;
					getData.call(that, ++idx, pramas);
				}
			});
		}
	}

	return {
		select3 : init,
		selectedValue : function(name) {
			var value = $(this).find("select[name=" + name + "]").val();
			return value || null;
		},
		selectedText : function(name) {
			var text = $(this).find("select[name=" + name + "]").find(
					"option:selected").text();
			return text || null;
		}
	};
})());