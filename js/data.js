///задачи:
///Защита от добавления пустых строк
///Горячие клавиши :
///	ctrl+enter - добавить
///	стрелочками передвигаться
///
///ускорить загрузку

$( function() {
	var IsDebugInfo = 0;
	var alphEng = "qwertyuiop[]asdfghjkl;'zxcvbnm,./";
	var alphRu = "йцукенгшщзхъфывапролджэячсмитьбю.";
	var availableTags = [];
	var lastType = {"id":-1,"text":''};
	var DataListObject = [];
	var FullData =  {};
	var nowDataId = 0;
	var tab = document.location.href.substr(document.location.href.lastIndexOf("/")+1);	
	var d = new Date();
	var strDate = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	if(IsDebugInfo){ console.log(strDate);}
	FullData = {
		"DataListObject":[{"date":0,"list":[]}],
		"types":[{"id":0,"name":""}],
		"products":[{"id":0,"name":""}],
		"metens":[{"id":0,"name":""}],
		"dates":[{"id":0,"name":strDate}],
		"statCount":0,
		"statSum":0,
	};

	getFullDataIndex = function(pole,val){
		//получение индекса по определенному полю и значения
		for (var i = 0; i < FullData[pole].length; i++) {
			if(FullData[pole][i].name == val){
				return i;
			}
		}
		return -1;
	},
	getFullDataListIndex = function(id){
		//получение индекса по определенному полю и значения		
		for (var i = 0; i < FullData.DataListObject[nowDataId].list.length; i++) {
			if(FullData.DataListObject[nowDataId].list[i].id == id){
				return i;
			}
		}
		return -1;
	},
	maxFullData = function(pole){
		var res = -1;
		if(!FullData[pole]) console.error("нет поля",pole);
		for (var i = 0; i < FullData[pole].length; i++) {
			res = Math.max(res,FullData[pole][i].id);
		}
		return res;
	},	
	getValueFromid = function(pole,id){
		for (var i = 0; i < FullData[pole].length; i++) {
			if(FullData[pole][i].id == id){
				return FullData[pole][i].name;
			}
		}
		return "";
	},
	getIdFromValue = function(pole,val){
		for (var i = 0; i < FullData[pole].length; i++) {
			if(FullData[pole][i].name == val){
				return FullData[pole][i].id;
			}
		}
		return -1;
	},
	getDataFromValue = function(pole,val){
		for (var i = 0; i < FullData[pole].length; i++) {
			if(FullData[pole][i].name == val){
				return FullData[pole][i];
			}
		}
		return -1;
	},
	getDataList = function(date){
		
		$("#dataList").html('');
		FullData.statCount  =0;
		FullData.statSum  =0;

		var strDate = date.Year + "/" + date.Month+"/"+date.Day;

		var id = getFullDataIndex("dates",strDate);
		if(id == -1){
			id = FullData.dates.length;

			var idDate = getData(strDate);

			//var idDate = maxFullData("dates");
			FullData.dates.push({"id":idDate,"name":strDate});
		}else{nowDataId = id;}
		
		if(IsDebugInfo){ console.log('nowDataId - ',nowDataId);}
		for (var i = 0; i < FullData.DataListObject[nowDataId].list.length; i++) {
			createRow(FullData.DataListObject[nowDataId].list[i]);
		}
		createRow();		
	},
	sourcefunction = function( request, response ) {
		var str0 = $.ui.autocomplete.escapeRegex(request.term);
      	var d = getMyReg(str0);
      	var str = $.ui.autocomplete.escapeRegex(convertLeng(request.term));
      	var d1 = getMyReg(str);
		var matcher = new RegExp( d, "i" );
		var matcher1 = new RegExp( d1, "i" );
		response( $.grep( availableTags, function( item ){
			return matcher.test( item ) || matcher1.test( item );
		}));
	},
	selectfunction = function( event, ui ) {
		var strt = '';
		if(event.toElement){			
			strt = event.toElement.innerText
		}
		else{
			//console.log();
			strt = event.target.value;
		}
		var prod = getDataFromValue("products",strt);
		if(prod){
			var meten = getValueFromid("metens",prod.lastidmeten);
			$(".custom-metens-input").eq(0).val(meten);
			var val = prod.lastval;
			$("input[name=spinner]").eq(0).val(val);
			var prise = prod.lastprise;
			$("input[name=spinner1]").eq(0).val(prise);
		}
	},
	keypressfunction = function(ee){		
		if(ee.keyCode == 13){
			if(availableTags.indexOf($(this).val()) == -1)
				availableTags.push($(this).val());
		}
	},
	blurfunction = function(){
		if(availableTags.indexOf($( this ).val()) == -1)
				availableTags.push($( this ).val());
	},
	getMyReg = function(str){
  	//составляет регулярное выражение из переданных символов,
  	//так чтобы  было любое совпадение передаваемых символов идущих подряд
  	var str_res = '';
  	for (var i = 0; i < str.length; i++) {
  		var s = str[ i ];
  		if(s == "\\"){
  			i++
  			s +=str[i];
  		}

  		str_res += s + '(|.*)';
  	}
  	return str_res;
  },    
  convertLeng = function(str){
  	//перевод языка из русского в английский или наоборот
  	var result = '';
  	for (var index = 0; index < str.length; index++) {
  		var i = alphRu.indexOf(str[index]);
  		if(i != -1){ result += alphEng[i];}
  		else{
  			i = alphEng.indexOf(str[index]);
  			if(i != -1){ result += alphRu[i];}
  			else{
  				result += str[index];
  			}
  		}			    		
  	}
  	return result;
  },
  initCombobox = function(comboboxName){
  	$.widget( "custom."+comboboxName, {
  		_create: function() {
	        this.wrapper = $( "<span>" )
	          .addClass( "custom-"+comboboxName )
	          .insertAfter( this.element );
	 
	        this.element.hide();
	        this._createAutocomplete();
	        this._createShowAllButton();
	    },
	    _createAutocomplete: function() {
	      	var selected = this.element.children( ":selected" ),
	      	value = selected.val() ? selected.text() : "";
	      	this.input = $( "<input>" )
	      		.appendTo( this.wrapper )
	      		.val( value )
	      		.attr( "title", "" )
	      		.addClass( "custom-"+comboboxName+"-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
	      		.autocomplete({
	      			delay: 0,
	      			minLength: 0,
	      			source: $.proxy( this, "_source" )
	      		})
	      		/*.on("blur",function(){ //после ухода с данного поля
	      			getSaveRow(this);
	      		})*/
	      		;
	      	this._on( this.input, {
	      		autocompleteselect: function( event, ui ) { //возникает после выбора
	      			ui.item.option.selected = true;
	      			this._trigger( "select", event, {
	      				item: ui.item.option
	      			});
	      			//getSaveRow(this);
	      		}
	      	});
	    },
	    _createShowAllButton: function() {
	    	var input = this.input,wasOpen = false;
	    	$( "<a>" )
	    		.attr( "tabIndex", -1 )
	    		.appendTo( this.wrapper )
	    		.button({
	    			icons: {
	    				primary: "ui-icon-triangle-1-s"
	    			},
	    			text: false
	    		})
	    		.removeClass( "ui-corner-all" )
	    		.addClass( "custom-"+comboboxName+"-toggle ui-corner-right" )
	    		.on( "mousedown", function() {
	    			wasOpen = input.autocomplete( "widget" ).is( ":visible" );
	    		})
	    		.on( "click", function() {
	    			input.trigger( "focus" );
	    			// Close if already visible
	    			if ( wasOpen ) {return;} 
            		// Pass empty string as value to search for, displaying all results
            		input.autocomplete( "search", "" );	            		
            	});
    	},
    	_source: function( request, response ) {
    		var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
    		response( this.element.children( "option" ).map(function() {
    			var text = $( this ).text();
    			if ( this.value && ( !request.term || matcher.test(text) ) )
    				return {
    					label: text,
    					value: text,
    					option: this
    				};
    			}) 
    		);
    	},
    	_destroy: function() {
    		this.wrapper.remove();
    		this.element.show();
    	}/*,
    	_blur: function(){
    		console.log("getSaveRow111");
    	}*/
    });				    
	},
	DeleteDataFunction = function(){
			console.log('delete');
	},
	ModifDataFunction = function(){
		//$(this).loading(true);
		//
		if("Добавить" == $(this).data("mod")){
			$(this).data("mod","Удалить");
			$(this).removeClass('glyphicon-plus-sign');
			$(this).addClass('glyphicon-minus-sign');
			var i = loadDataRow();
			if(i != -1){
				deleteRow(0);
				if(IsDebugInfo){ console.log(FullData,i);}
				//console.log(FullData.DataListObject[nowDataId].list[i]);
				createRow(FullData.DataListObject[nowDataId].list[i]);
				createRow();
				$(".tags").eq(0).focus();
			}
		}else if("Удалить" == $(this).data("mod")){
			
			deleteOrRecoveRowList(this,true);
			
			if(IsDebugInfo){ console.log($(this).data("id"));}
		}else if("Восстановить" == $(this).data("mod")){
			deleteOrRecoveRowList(this,false);
		}

		return false;
		
		//$(this).loading(false);
	},
	deleteOrRecoveRowList = function(obj,isdelete){
		var id = $(obj).data('id');
		var act = isdelete?"setArchiveRow":"recoveRow";
		var btn = isdelete?"Восстановить":"Удалить";
		var classleft = "glyphicon-arrow-left";
		var classminus = "glyphicon-minus-sign";
		var data = {ModDataRow: act,"id":id};
		if(IsDebugInfo){ console.log(data);}
		$.ajax({
			url: "php/srv.php",
			type: 'POST',
			//dataType: 'application/json',
			//async:true,
			beforeSend: function( jqXHR, settings ){
				//проверка перед отправкой, если return false, то не отправлять!
				return true;
			},
			data:data,
			success: function (data, textStatus, jqXHR) {
				if(IsDebugInfo){ console.log("data ",data);}
				//data = JSON.parse(data);
				$(obj).data("mod",btn);

				setDataObjectActual(id,isdelete);
					
				$(obj).removeClass(isdelete?classminus:classleft);
				$(obj).addClass(isdelete?classleft:classminus);

				$(obj).attr("title",btn);
			},
			error:function(jqXHR,textStatus,errorThrown ){
				console.error(jqXHR);
			}
		});
	},
	setDataObjectActual = function(id,isdelete){
		var isAct = isdelete?-1:1;
		for (var i = 0; i < FullData.DataListObject[nowDataId].list.length; i++) {
			if(FullData.DataListObject[nowDataId].list[i].id == id){
				FullData.DataListObject[nowDataId].list[i].isActual = isAct;
				return;
			}
		}
	},
	UpdateDataFunction = function(){
		var id = $(this).data('id');
		var index = loadDataRow(id);
		updateStat();

		$(this).animate({"color": "grey"}, 500,"linear",function(){
			$(this).animate({"color": "#337ab7"}, 500);
		});
		return false;
	},
	deleteRow = function(id){
		$("#dataList tr").eq(id).remove(); 
	},
	insertFullData = function(pole,name,id,idmeten,val,prise){
		var idLock = getIdFromValue(pole,name);
		if(idLock == -1){
			id = id != -1? id : (maxFullData(pole) + 1);
			if(IsDebugInfo){ console.log(pole,val,id);}
			if(pole == "products"){
				FullData[pole].push({"id":id,"name":name,
					"lastidmeten":idmeten,
					"lastval":val,
					"lastprise":prise
				});
			}
			else{
				FullData[pole].push({"id":id,"name":name}); 	
			}
		}
		return id;
	},
	loadDataRow = function(id){
		id = id?id:-1;
		var type = $("#type_"+id).next().children().val();
		var meten = $("#meten_"+id).next().children().val();
		var product = $("#product_"+id).val();
		var date = '';
		var val = $("#val_"+id).val();
		var prise = $("#prise_"+id).val();

		if(tab !== 'data_find.html'){
			date = FullData.dates[nowDataId].name;
		}
		else{
			date = $("#date_"+id).val();
		}

		var data = {"insert":id,"val":val,"prise":prise};
		///если  id  = -1, то только вставляем новые строки, а иначе изменяем строки
		var idtype = getIdFromValue("types",type);
		var idproduct = getIdFromValue("products",product);
		var idmeten = getIdFromValue("metens",meten);
		var iddate = getIdFromValue("dates",date);
		if(IsDebugInfo){ console.log("idmeten",idmeten,"meten",meten);}

		data["idtype"] = idtype;  data["type"] = type; 
		data["idproduct"] = idproduct;  data["product"] = product; 
		data["idmeten"] = idmeten; data["meten"] = meten; 
		data["iddate"] = iddate; data["date"] = date; 

		var DataListId = -1;
		if(IsDebugInfo){ console.log(data);}
		$.ajax({
			url: "php/srv.php",
			type: 'POST',
			async:false,
			beforeSend: function( jqXHR, settings ){
				//проверка перед отправкой, если return false, то не отправлять!
				//console.log("jqXHR",jqXHR,"settings",settings);
				return true;
			},
			data:data,
			success: function (data, textStatus, jqXHR) {
				if(IsDebugInfo){ console.log("data ",data);}
				try{
					data = JSON.parse(data);
					DataListId = data && data.DataListId?data.DataListId:-1;
					idtype = data && data.idtype?data.idtype:-1;
					idproduct = data && data.idproduct?data.idproduct:-1;
					idmeten = data && data.idmeten?data.idmeten:-1;
					iddate = data && data.iddate?data.iddate:-1;
				}
				catch(err){
					console.error(err);
					console.log(data);
				}				
			},
			error:function(jqXHR,textStatus,errorThrown ){
				console.error(jqXHR);
				//по хорошему мы должны получить новые id с сервера,
				//но возможно что сервер лежит, тогда мы должны отобразить 
				//хоть какую-то информацию в таблице, поэтому мы заменем id 
				//на последние номера в группе
			}
		});
		idtype = insertFullData("types",type,idtype);		
		idmeten = insertFullData("metens",meten,idmeten);
		idproduct = insertFullData("products",product,idproduct,idmeten,val,prise);

		lastType = {"id":idtype,"text":type};

		if(DataListId == -1){
			DataListId = FullData.DataListObject[nowDataId].list.length;
		}

		var DataList = {
			"id":DataListId,
			"idtype":idtype,
			"idproduct":idproduct,
			"idmeten":idmeten,
			"val":val,
			"prise":strToFloat(prise),
			"isActual":1
		};
		if(IsDebugInfo){ console.log(DataList);}
		if(id == -1){
			FullData.DataListObject[nowDataId].list.push(DataList);
			return FullData.DataListObject[nowDataId].list.length-1;
		}
		else{
			if(IsDebugInfo){ console.log("DataList",DataList,"id",id);}
			var index = getFullDataListIndex(id);
			if(tab !== 'data_find.html'){
				var oldp = FullData.DataListObject[nowDataId].list[index].prise;
				FullData.statSum -= oldp?oldp:0;
				FullData.DataListObject[nowDataId].list[index] = DataList;
				FullData.statSum += DataList.prise;
			}
			return index;
		}
	},
	strToFloat = function(str){
		//преобразовывает из текста в float
		if(Number(str) !== str){
			if(str.indexOf(",") != -1){
				do{
					str = str.replace(",",".");
				}while(str.indexOf(",")  != -1);
			}
			var reg = new RegExp("[^0-9.]","i");
			if(reg.test(str)){
				do{
					str = str.replace(reg,"");
				}while(reg.test(str));
			}
		}
		return parseFloat(str);
	},
	createRow = function(DataObject){
		var index = 0;
		$tr = $("<tr>");
		$tds = [$("<td>"),$("<td>"),$("<td>"),$("<td>"),$("<td>"),$("<td>")];
		
		var id =(DataObject?DataObject.id:-1);

		if(tab === 'data_find.html'){
			$tds.push($("<td/>"));
			var nameDate;
			if(DataObject){				
				nameDate = getValueFromid("dates",DataObject.iddate);
			}
			var $tags='';
			if(nameDate){
				$tags = $("<input/>",{type:"text",class:"form-control",value:nameDate,id:"date_"+id});				
			}
			$tds[index++].append($tags);
		}

		///-----тип 
		$selecttypes = $("<select>",{class:"types",id:"type_"+id});		
		if(FullData && FullData.types){
			$selecttypes.append($("<option>",{value:lastType.id,text:lastType.text}));
			for (var i = 0; i < FullData.types.length; i++) {
				var t = FullData.types[i];
				if(DataObject && DataObject.idtype == t.id){
					$selecttypes.append($("<option>",{value:t.id,text:t.name,selected:"selected"}));
				}
				else{
					$selecttypes.append($("<option>",{value:t.id,text:t.name}));
				}
			}
		}
		$tds[index++].append($("<div>",{class:"ui-widget",style:"width: 100%"}).append($selecttypes));
		$selecttypes.types(); //обязательно после append чтобы класс  ui-widget уже был
		
		///-----end тип 
		
		///-----продукт 
		var nameProduct = '';
		if(DataObject){
			nameProduct = getValueFromid("products",DataObject.idproduct);
		}
		var $tags = $("<input/>",{type:"text",class:"tags form-control",value:nameProduct,id:"product_"+id});
		$tags.autocomplete({source: sourcefunction, select: selectfunction });
		$tags.keypress(keypressfunction);
		$tags.blur(blurfunction);
		$tds[index++].append($tags);
		///-----end продукт 

		///-----единицы измерения
		var idm=-1;
		if(!DataObject){
			idm = getIdFromValue("metens","шт");
		}
		$selectmetens = $("<select>",{class:"metens",id:"meten_"+id});
		if(FullData && FullData.metens){
			$selectmetens.append($("<option>",{value:-1,text:''}));
			for (var i = 0; i < FullData.metens.length; i++) {
				var m = FullData.metens[i];
				if(DataObject && DataObject.idmeten == m.id){
					$selectmetens.append($("<option>",{value:m.id,text:m.name,selected:"selected"}));
				} else{
					if(idm == m.id){
						$selectmetens.append($("<option>",{value:m.id,text:m.name,selected:"selected"}));
					} else{
						$selectmetens.append($("<option>",{value:m.id,text:m.name}));
					}
				}
			}
		}
		$tds[index++].append($("<div>",{class:"ui-widget",style:"width: 100px"}).append($selectmetens));
		$selectmetens.metens();
		///-----end единицы измерения 

		///-----количество 
		var nameval = '1';
		if(DataObject){
			nameval = DataObject.val;
		}
		$spinner = $("<input/>",{class:"spinner", name:"spinner",value:nameval,id:"val_"+id});
		$tds[index++].append($spinner);				
		$spinner.spinner({ step: 1, numberFormat: "n",spin:spinVal });
		$spinner.css({"width":"55px"});
		///-----end количество 
		
		///-----цена
		var nameprise = '1';
		if(DataObject){
			FullData.statCount++;
			FullData.statSum += strToFloat(DataObject.prise);
			nameprise = DataObject.prise;
		}
		$spinner1 = $("<input/>",{class:"spinner1", name:"spinner1",value:nameprise,id:"prise_"+id});
		$tds[index++].append($spinner1);
		$spinner1.spinner({ step: 1, numberFormat: "n" });
		$spinner1.css({"width":"55px"});
		///-----end цена 
		
		var textvalue = "Добавить";
		if(DataObject){			
			if(DataObject.isActual == 1) {textvalue = "Удалить"};
			if(DataObject.isActual == -1) {textvalue = "Восстановить"};
		}		
		$btnaddData = $("<a/>",{class:"glyphicon buttAction",href:'#',title:textvalue});
		$btnaddData.data("mod",textvalue);		
		if(DataObject){
			$btnaddData.data("id",DataObject.id);
			if(DataObject.isActual == 1){
				$btnaddData.addClass('glyphicon-minus-sign');
			}
			if(DataObject.isActual == -1){
				$btnaddData.addClass('glyphicon-arrow-left');	
			}
		}
		else{
			$btnaddData.addClass('glyphicon-plus-sign');
		}
		$btnaddData.click(ModifDataFunction);
		
		$tds[index].append($btnaddData);
		if(DataObject){			
			//glyphicon-saved
			$btnSaveData = $("<a/>",{class:"glyphicon glyphicon-save buttAction",
				id:"saveRow"
				,href:'#'});
			$btnSaveData.click(UpdateDataFunction);
			$btnSaveData.data("id",DataObject.id);
			$tds[index].append($btnSaveData);
		}
		$tr.append($tds);
		$("#dataList").prepend($tr);

		updateStat();
	},
	updateStat = function(){
		$("#statCount").text(FullData.statCount + " шт.");
		$("#statSum").text(FullData.statSum.toFixed(2) + " руб.");		
		$("#statSum").priceFormat({
		    prefix: '',
		    centsSeparator: ',',
		    thousandsSeparator: ' ',
		    insertPlusSign: false
		});
	},
	spinVal = function(event, ui){
		if(IsDebugInfo){ console.log(event);}
		var idval = $(this).attr("id");
		var idprise= idval.replace("val_","prise_");
		//var oldval = $(this).data("oldval");
		var oldval = $(this).val();

		var oldprice = $("#"+idprise).val();
		//var newval = $(this).val();
		var newval = ui.value;//event.target.value;
		var newprice = (strToFloat(newval)*strToFloat(oldprice)) / strToFloat(oldval);

		if(IsDebugInfo){ console.log(newval,oldprice,oldval);}

		$("#"+idprise).val(newprice.toFixed(2));
		$("#"+idprise);
	},
	getDataAll = function(){
		///получение списка из базы данных

		var data = {"getDataAll": true};
		$.ajax({
			url: "php/srv.php",
			type: 'POST',
			//dataType: 'application/json',
			async:false,
			beforeSend: function( jqXHR, settings ){
				//проверка перед отправкой, если return false, то не отправлять!
				return true;
			},
			data:data,
			success: function (data, textStatus, jqXHR) {
				if(IsDebugInfo){ console.log("data ",data);}
				data = JSON.parse(data);

				FullData.DataListObject[nowDataId]= {
																			list:data.list,
																			date: data.list && data.list.length>0? data.list[0].iddate:-1
																		};
				FullData.types =  data.types;
				FullData.products =  data.products;
				FullData.metens = data.metens;
				FullData.dates = data.dates;
				if(FullData.dates.length == 0){
					FullData.dates=[{"id":0,"name":strDate}];
				}
				$('#userName').html(data.userName);
				if(IsDebugInfo){ console.log(FullData);}

			},
			error:function(jqXHR,textStatus,errorThrown ){
				console.error(jqXHR);
			}
		});

		for (var i = 0; i < FullData.products.length; i++) {			
			if(availableTags.indexOf(FullData.products[i].name) == -1)
				availableTags.push(FullData.products[i].name);
		}
		var c = FullData.DataListObject[nowDataId].list.length;
		if(IsDebugInfo){ console.log(c);}
		for (var i = 0; i < FullData.DataListObject[nowDataId].list.length; i++) {			
			if(FullData.DataListObject[nowDataId].list[i])
				createRow(FullData.DataListObject[nowDataId].list[i]);
		}
	},
	dataFind = function(){
		var value = $('#data_find').val();
		
		if(!value) return;
		if(value === '') return;
		
		$("#dataList").html('');
		FullData.statCount  =0;
		FullData.statSum =0;

		//console.log(val);
		var data = {"dataFind":true,"value":value};
		$.ajax({
			url: "php/srv.php",
			type: 'POST',
			async:false,
			beforeSend: function( jqXHR, settings ){
				//проверка перед отправкой, если return false, то не отправлять!
				return true;
			},
			data:data,
			success: function (data, textStatus, jqXHR) {
				if(IsDebugInfo){ console.log("data ",data);}
				data = JSON.parse(data);

				var list = data.list;
				if(IsDebugInfo){ console.log(list);}

				for (var i = 0; i < list.length; i++) {
					createRow(list[i]);
				}
			},
			error:function(jqXHR,textStatus,errorThrown ){
				console.error(jqXHR);
			}
		});
	},
	getMetaDataAll = function(){
		///получение cправочников из базы данных
		var data = {"getMetaDataAll": true};
		$.ajax({
			url: "php/srv.php",
			type: 'POST',
			//dataType: 'application/json',
			async:false,
			beforeSend: function( jqXHR, settings ){
				//проверка перед отправкой, если return false, то не отправлять!
				return true;
			},
			data:data,
			success: function (data, textStatus, jqXHR) {
				if(IsDebugInfo){ console.log("data ",data);}
				data = JSON.parse(data);

				FullData.types =  data.types;
				FullData.products =  data.products;
				FullData.metens = data.metens;
				FullData.dates = data.dates;
				if(FullData.dates.length == 0){
					FullData.dates=[{"id":0,"name":strDate}];
				}
				$('#userName').html(data.userName);
				if(IsDebugInfo){ console.log(FullData);}

			},
			error:function(jqXHR,textStatus,errorThrown ){
				console.error(jqXHR);
			}
		});

		for (var i = 0; i < FullData.products.length; i++) {			
			if(availableTags.indexOf(FullData.products[i].name) == -1)
				availableTags.push(FullData.products[i].name);
		}
	},
	getData = function(strDate){
		var data = {"getData": true,"strDate":strDate};
		$.ajax({
			url: "php/srv.php",
			type: 'POST',
			//dataType: 'application/json',
			async:false,
			beforeSend: function( jqXHR, settings ){
				//проверка перед отправкой, если return false, 
				//то не отправлять!
				return true;
			},
			data:data,
			success: function (data, textStatus, jqXHR) {
				if(IsDebugInfo){ console.log("data ",data);}
				data = JSON.parse(data);

				nowDataId = FullData.DataListObject.length;

				FullData.DataListObject[nowDataId] = {
																			list:data.list,
																			date:data.iddate?data.iddate:-1
																		};
				if(IsDebugInfo){ console.log(FullData);}
			},
			error:function(jqXHR,textStatus,errorThrown ){
				console.error(jqXHR);
			}
		});
		return FullData.DataListObject[nowDataId].date;
	},



	$("#datepicker").datepicker({
		onSelect: function(dateText, inst){
			getDataList({
				Day:inst.selectedDay,
				Month:inst.selectedMonth + 1,
				Year:inst.selectedYear
			});
		}
	});
	initCombobox("types");
  initCombobox("metens");
  if(tab !== 'data_find.html'){
  	getDataAll();
  	createRow();
  }else{
  	getMetaDataAll();

  	$('#data_find').autocomplete({source: sourcefunction, select: selectfunction });
		$('#data_find').keypress(function(ee){
			if(ee.keyCode == 13){
				dataFind();
			}
		});
		$('#data_find').blur(blurfunction);
  	/*
  	FullData = {
			"DataListObject":[{				
				"list":[{
						"id":1,
						"iddate":1,
						"idtype":1,
						"idproduct":1,
						"idmeten":1,
						"val":200,
						"prise":90},
					{
						"id":2,
						"iddate":1,
						"idtype":1,
						"idproduct":2,
						"idmeten":1,
						"val":100,
						"prise":10}
						]
			}],
			"types":[{"id":0,"name":""},{"id":1,"name":"Продукты"}],
			"products":[{"id":1,"name":"Шоколад"
			,"lastidmeten":1
			,"lastval":100
			,"lastprice":10
			},{"id":2,"name":"Хлеб"}],
			"metens":[{"id":0,"name":""},{"id":1,"name":"кг"},
					  {"id":2,"name":"г"},{"id":3,"name":"литр"},
					  {"id":4,"name":"шт"}],
			"dates":[{"id":1,"name":"2016/10/28"},{"id":2,"name":"2016/10/28"}],
			"statCount":100, //шт.
			"statSum":2000, //руб.
		};
		*/
		//createRow();
		/*
		for (var j = 0; j < FullData.DataListObject.length; j++) {
			for (var i = 0; i < FullData.DataListObject[j].list.length; i++) {
				createRow(FullData.DataListObject[j].list[i]);
			}
		}
		*/
  }
});