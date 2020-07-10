	Form = { name : {} }; 
	var forms = document.forms;
	for(indexf in forms){
		if(typeof forms[indexf].id != "undefined" && forms[indexf].id != '' && forms[indexf].id != null){
			Form.name[forms[indexf].id] = {};
			var elements = document.forms[forms[indexf].id].elements;
			for(indexe in elements){
				if(typeof elements[indexe].id != "undefined" && elements[indexe].id != '' && elements[indexe].id != null){
					Form.name[forms[indexf].id][elements[indexe].id] = elements[indexe].id;
				}
			}
		}
	}

	Bind = {
		_event : { change : 'onchange', click : 'onclick'},
		_request : { get : 'get'},
		has : { request :'request' }, 
		tag : { select : 'select'},
		collection : document.forms,
		element : function(attr){
			var collection = Bind.collection[attr.form];
			var element = null; 
			for(attrf in Form.name[attr.form]){
				switch(attr.name){
					case attrf: element = collection[attrf]; break;
				}
			}
			return element;
		},
		initElement : function(attr){
			switch(attr.node){
				case Bind.tag.select:	
					length = Bind.element({
										   form : attr.form,
										   name : attr.listener  
										}).options.length;
					while(length--){
						if(length>0){
							Bind.element({
							form : attr.form,
							name : attr.listener  
							}).remove(length);
						}
					}
				break;
			}
		},
		event : function (attr){
			switch(attr.type){
				case Bind._event.change: 
					for(attrf in Form.name[attr.form]){
						switch(attrf){
							case attr.trigger:
								Bind.element({
											  form : attr.form,
											  name : attrf  
											 }).onchange = 
								function(){
									if(Bind.element({
										form : attr.form,
										name : attr.trigger  
									   }).value == ''){ 	
											Bind.initElement(attr);
										}
									if(Bind.element({
													 form : attr.form,
													 name : this.id  
												    }).value != ''){
										switch(attr.request){
											case Bind._request.get:
											Bind.request({
														form : attr.form,
														type : attr.type,
														request : attr.request,
														url : attr.url,
														trigger : this,
														node : attr.node,
														listener : attr.listener,
														});
											break;
										}
									}
								((typeof attr.finally != "undefined" )?eval(attr.finally(attr.form)):null);	 
								}
							break;
						}
					}
				break;
				case Bind._event.click:
					for(attrf in Form.name[attr.form]){
						switch(attrf){
							case attr.trigger:	
								Bind.element({
										form : attr.form,
										name : attrf  
									}).onclick = 
									function(){
										if(Bind.element({
											form : attr.form,
											name : this.id  
										   }).value != ''){
											switch(attr.request){
												case Bind._request.get:
												Bind.request({
															form : attr.form,
															type : attr.type,
															request : attr.request,
															url : attr.url,
															trigger : this,
															node : attr.node,
															listener : attr.listener,
															});
												break;
											}
										}
									}
							break;
						}
					}
				break;
			}	
		},
		request : function (attr){
			var ajax = new XMLHttpRequest();
			var url = '';
			switch(attr.request){
				case Bind._request.get:
					for(attrf in Form.name[attr.form]){
							switch(attr.type){
								case Bind.has.request:
									switch(attrf){
										case attr.trigger:
											url = attr.url;
										break;
									}
								break;
								case Bind._event.change:
									switch(attr.trigger.id){
										case attrf: 
											url = attr.url.replace(attrf, attr.trigger.value);
											break;
									}
								break;
								case Bind._event.click: break;
							}
					}
					ajax.open(Bind._request.get.toUpperCase(), url, true);
					ajax.send();
				break;
			}
			ajax.onreadystatechange = function() {
				if (ajax.readyState == 4 && ajax.status == 200) {
					var data = JSON.parse(ajax.responseText);
					Bind.response({
								   form : attr.form,
								   type : attr.type,  
								   response : data,
								   node : attr.node,
								   listener : attr.listener,
								   value : ((typeof attr.value != "undefined" )?attr.value:0)
								   });
				}
			}
		},
		response: function(attr){
			switch(attr.node){
				case Bind.tag.select:
					Bind.initElement(attr);
					var select = Bind.element({
											   form : attr.form,
											   name : attr.listener 
											  });
					for(data in attr.response){
						var opt = document.createElement('option');
						opt.value = attr.response[data].code;
						if(attr.value > 0){
							opt.selected = ((attr.value == attr.response[data].code)?'selected=\'selected\'':'')
						}
						opt.innerHTML = attr.response[data].name;
						select.appendChild(opt);
					}
			break;
			}
		}
	};