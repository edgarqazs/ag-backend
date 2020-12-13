var Ws = (function() {
	var instances = null;
	var _log = true;
	var close_able=false;
	var log = function(text) {
		if(_log) {
			console.log(text);
		}
	};
	return {
		init: function(domain, port, onmessage, onopen, showlog) {
			close_able=false;
			if(typeof showlog != "undefined") {
				_log = !!showlog;
			}
			
			instances = new WebSocket("ws://" + domain + ":" + port);
			instances.onclose = function() {
				setTimeout(function(){
					if(!close_able){
						log("断线重连..");
						Ws.init(domain, port, onmessage, onopen, showlog);
					}
				},1000);
			};
			instances.onopen = function() {
				log("Web Socket 已连接上");
				err_count = 0;
				if(typeof onopen == "function") {
					onopen.call(Ws);
				}
			};
			instances.onmessage = function(evt) {
				var received_msg = evt.data;
				if(typeof received_msg == 'string') {
					try {
						received_msg = JSON.parse(received_msg);
						if(received_msg.type && received_msg.type == 'ping') {
							return;
						}
						onmessage.call(Ws, received_msg);
					} catch(e) {
						onmessage.call(Ws, evt.data);
						log(JSON.stringify(e));
					}
				} else if(typeof received_msg == "object") {
					onmessage.call(Ws, received_msg);
				}
			};
		},
		send: function(data) {
			if(!instances) {
				return log("请先初始化WS");
			}
			if(typeof data != "string") {
				data = JSON.stringify(data);
			}
			log('发送：' + data);
			instances.send(data);
		},
		joinGroup: function(group) {
			if(!instances) {
				return log("请先初始化WS");
			}
			log('加入组：' + group);
			instances.send(JSON.stringify({
				join_group: group
			}));
		},
		close:function(){
			if(instances){
				instances.close();
				instances=null;
				close_able=true;
			}
		}
	};
})();