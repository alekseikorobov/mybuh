$( function() {

	var year  = (new Date()).getFullYear();
	var month = (new Date()).getMonth();
	var day   = 1; //not using

	var MassMonth = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];

	updateValYear =  function() {$('#sYear').html(year);};
	updateValMonth = function() {$('#sMonth').html(MassMonth[month]);};

	decrimentYear= function() {year--;updateValYear();getStatistic();};
	incrimentYear= function() {year++;updateValYear();getStatistic();};
	decrimentMonth= function() {if(month==0 ) {month=11;decrimentYear();}else{month--;} updateValMonth();getStatistic();};
	incrimentMonth= function() {if(month==11) {month=0 ;incrimentYear();}else{month++;} updateValMonth();getStatistic();};

	createRow  = function(row) {
		$tr = $("<tr>");
		$tr.append($("<td>").html(row.name));
		$tr.append($("<td>").html(row.c));
		//.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g,'$1')

		$tdprice=$("<td>");
		$tdprice.html(parseFloat(row.prise).toFixed(2));
		$tdprice.priceFormat({
		    prefix: '',
		    centsSeparator: ',',
		    thousandsSeparator: ' ',
		    insertPlusSign: false
		});
		$tr.append($tdprice);
		return $tr;
	};
	createDate = function(list) {
		var table = $('#statList');
		var stat ={"name":"Итого","c":0,"prise":0};
		table.html('');

		for (var i = 0; i < list.length; i++) {
			
			stat.c+=parseFloat(list[i].c);
			stat.prise+=parseFloat(list[i].prise);

			var row = createRow(list[i]);
			table.append(row);
		}

		var $statRow = createRow(stat);
		$statRow.css({"font-weight":"bold"});
		table.append($statRow)
	};

	getStatistic  = function() {
		
		var data = {'getStatistic':1,'date':year +'.'+ (month+1)+'.'+day};
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
				if(data){
					data = JSON.parse(data);
					//console.log("data ",data);
					createDate(data.list);
				}
			},
			error:function(jqXHR,textStatus,errorThrown ){
				console.error(jqXHR);
			}
		});
	};

	updateValYear();
	updateValMonth();
	getStatistic();

})