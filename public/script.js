

$( document ).ready(function() {
	$('#minus-mach').click(minusMach);
	$('#plus-mach').click(plusMach);
	$('#add-sing').click(addSing);
	$('#view-transition').click(viewTransition);
	$('#save-count').click(saveCount);
	$('#reload-data').click(reloadData);
	$('.radio-curs').click(changeCurs);
	$('.auto-link').click(saveTransition);
	$('#hide-menu').click(hideMenu);
	$('#clear-all').click(clearAll);
    $('[data-toggle="tooltip"]').tooltip(); 
});

var sizeStatus = 'second';
var isSing = true;

var changePhoto = function (image) {
    image.onerror = "";
    image.src = "./public/images/default.png";
    // if(isNeadTitle) image.title = "Не указано";
    return true;
}

var minusMach = function (event) {
	if(sizeStatus == 'second'){
		$('.address-col').removeClass('col-md-1 col-lg-1').addClass('col-md-2 col-lg-2');
		$('.text-col').removeClass('col-md-2 col-lg-2').addClass('col-md-3 col-lg-3');
		$('.photo-col').css({'display': 'none'});
	} else if(sizeStatus == 'third'){
		$('.container').removeClass( "container" ).addClass("container-fluid");
	}
	changeStatus(false);
}
var plusMach  = function (event) {
	if(sizeStatus == 'first'){
		$('.address-col').removeClass('col-md-2 col-lg-2').addClass('col-md-1 col-lg-1');
		$('.text-col').removeClass('col-md-3 col-lg-3').addClass('col-md-2 col-lg-2');
		$('.photo-col').css({'display': 'block'});
	} else if(sizeStatus == 'second'){
		$('.container-fluid').removeClass( "container-fluid" ).addClass("container");
	}
	changeStatus(true);
}

var changeStatus = function (isMultiple) {
	if(isMultiple){
		switch (sizeStatus){
			case 'first': sizeStatus = 'second'; break;
			case 'second': sizeStatus = 'third'; break;
			case 'third': sizeStatus = 'third'; break;
		} 
	} else {
		switch (sizeStatus){
			case 'first': sizeStatus = 'first'; break;
			case 'second': sizeStatus = 'first'; break;
			case 'third': sizeStatus = 'second'; break;
		}
	}
}

var addSing = function (event) {
	isSing = !isSing;
	$(this).text(isSing ? 'Отключить звук' : 'Включить звук');
}

var changeCurs = function (event) {
	prices = $('.price');
	switch ($(this).val()){
		case '1': cursToStart(prices); break;
		case '2': cursToDolar(prices); break;
		case '3': cursToGrivna(prices); break;
	}
}

var cursToStart = function (prices) {
	for (var i = 0; i < prices.length; i++) {
		$(prices[i]).text($(prices[i]).attr('data-price'));
	}
}

var cursToGrivna = function (prices) {
	var curs = parseInt(Number($('#number').val())); 
	curs = curs == NaN ? 25 : curs;
	for (var i = 0; i < prices.length; i++) {
		var currentPrice = $(prices[i]);
		if(currentPrice.attr('data-price').indexOf('грн') == -1) {
			var newPrice = currentPrice.attr('data-price').replace('$', '').split(' ').join('');
			currentPrice.text(toMoneyFormat(String(parseInt(Number(newPrice) * curs))) + ' грн');
		} else{
			currentPrice.text(currentPrice.attr('data-price'));
		}
	}
	$('#number').val(curs);
}

var cursToDolar = function (prices) {
	var curs = parseInt(Number($('#number').val())); 
	curs = curs == NaN ? 25 : curs;
	for (var i = 0; i < prices.length; i++) {
		var currentPrice = $(prices[i]);
		if(currentPrice.attr('data-price').indexOf('$') == -1) {
			var newPrice = currentPrice.attr('data-price').replace('грн', '').split(' ').join('');
			currentPrice.text(toMoneyFormat(String(parseInt(Number(newPrice) / curs))) + ' $');
		} else{
			currentPrice.text(currentPrice.attr('data-price'));
		}
	}
	$('#number').val(curs);
}


var toMoneyFormat = function ($string) {
	if($string.length == 4) {
		return $string.substr(0, 1) + ' ' + $string.substr(1);
	} else if($string.length == 5) {
		return $string.substr(0, 2) + ' ' + $string.substr(2);
	} else if($string.length == 6) {
		return $string.substr(0, 3) + ' ' + $string.substr(3);
	} if($string.length == 7) {
		return $string.substr(0, 1) + ' ' + $string.substr(1, 3) + ' ' + $string.substr(4);
	} else if($string.length == 8) {
		return $string.substr(0, 2) + ' ' + $string.substr(2, 3) + ' ' + $string.substr(5);
	} else if($string.length == 9) {
		return $string.substr(0, 3) + ' ' + $string.substr(3, 3) + ' ' + $string.substr(6);
	} if($string.length = 10) {
		return $string.substr(0, 1) + ' ' + $string.substr(1, 3) + ' ' + $string.substr(4, 3) + ' ' + $string.substr(7);
	} else if($string.length == 11) {
		return $string.substr(0, 2) + ' ' + $string.substr(2, 3) + ' ' + $string.substr(5, 3) + ' ' + $string.substr(8);
	} else if($string.length == 12) {
		return $string.substr(0, 3) + ' ' + $string.substr(3, 3) + ' ' + $string.substr(6, 3) + ' ' + $string.substr(9);
	}

}

var saveTransition = function (event) {
	var parent = $(this).parent().parent().parent();
	var transition = {
		'date': parent.find('.car-date').text(),
		'time': parent.find('.car-time').text().replace('(', '').replace(')', ''),
		'car': $(this).text(),
		'link': $(this).attr('href'),
		'price': parent.find('.price').text()
	} 
	console.log(transition);
	sendAjax('/log.php', transition, addTransitionId);
}


var sendAjax = function (path, postData, callback) {
	$.post(
	  	path,
	  	postData,
	  	callback
	);
}

var addTransitionId = function (data) {
	var ids = localStorage.getItem("ids");
	ids = ids == null ? '' : ids;
	localStorage.removeItem("ids");
	localStorage.setItem("ids", ids + data + 'a')
	console.log(localStorage.getItem("ids"))
}

var viewTransition = function (event) {
	sendAjax('/getlog.php', {'ids': localStorage.getItem("ids")}, showModal);
}

var reloadData = function (event) {
    $(document.body).css({'cursor' : 'wait'});
    $('a, button, input, label').attr('style', 'cursor: wait !important;');
	sendAjax('/update.php', {}, function () {
		if(isSing) {
			var audio = new Audio(); 
			audio.src = './public/sound.mp3'; 
			audio.autoplay = true; 
		}
		setTimeout(function () {
			window.location.reload();
		}, 1500) ;
	});
}

var saveCount = function (event) {
	var quantity = parseInt($('#count').val());
	var links = $('#olx-link').val();
	if(quantity && quantity >= 1){
		sendAjax('/save.php', {
			'quantity': quantity,
			'links': links
		}, function (data) {
			window.location.reload();
		});
	} else {
		quantity = '1';
	}
	$('#count').val(quantity)
}

var showModal = function (data) {
	var table = $('#modal-transitions');
	table.empty();
	if(data != null) {
		for (var i = 0; i < data.length; i++) {
			var row = $('<tr/>', {'text': ''});
			row.append('<td>' + data[i]['created'] + '</td>');
			row.append('<td>' + data[i]['date'] + '</td>');
			row.append('<td>' + data[i]['time'] + '</td>');
			row.append('<td>' + data[i]['car'] + '</td>');
			row.append('<td>' + data[i]['price'] + '</td>');
			row.append('<td><a href="' + data[i]['link'] + '" target="_blank">Ссылка</a></td>');
			table.append(row);
		}
		$('#exampleModal').modal('show');
	} else {
		alert('История пуста!');
	}

}

var hidden = false;
var hideMenu = function (event) {
	event.preventDefault();
	hidden = !hidden;
	if(hidden) {
		$('.tool-bar').css({'display': 'none'});
		$('.main-space').css({'height': '93.5vh'});
	} else {
		$('.tool-bar').css({'display': 'block'});
		$('.main-space').css({'height': '63.5vh'});
	}
}

var clearAll = function (event) {
	sendAjax('/clear.php', {}, function () {
		window.location.reload();
	});
}