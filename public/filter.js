 
$( document ).ready(function() {
	$('#get-find').click(getStarted);
});

var errors = '';

var getStarted = function (event) {
	var path = errors = '';
	path = addFrom(path);
	path = addRegion(path);
	path = addMark(path);
	path = addYear(path);
	path = addMileage(path);
	path = addPrice(path);
	path = addKpp(path);
	path = path.substring(0, path.length-1);
	// console.log(path);
	goToIndex(path);
}

var getChecked = function (data, selector, path) {
	var rows = $(selector).next();
	if(rows.length != 0) {
		for (var i = 0; i < rows.length; i++) {
			data += $(rows[i]).text().trim().replace(' ', '+')
			if(i < rows.length - 1) data += '_';
		}
		return path + data + '&';
	} else return path;
}

var getRange = function (path, data, startRange, endRange, errorMessage) {
	var start = Number($(startRange).val());
	var end = Number($(endRange).val());
	$(startRange).val(start == 0 ? '' : start);
	$(endRange).val(end == 0 ? '' : end);
	if(start == end && start == 0){
		return path;
	} else if( 
			(start < parseInt(Number($(startRange).attr('min'))) && start != 0) || 
		    start > parseInt(Number($(startRange).attr('max'))) || 
		    (end < parseInt(Number($(endRange).attr('min'))) && end != 0)  || 
		    end > parseInt(Number($(endRange).attr('max'))) ||
		    (start > end && end != 0)
	    ){
		errors += errorMessage + '; ';
		return path;
	} else {
		data += parseInt(start) + '_' + parseInt(end);
		return path + data + '&';
	}
}

var addFrom = function (path) {
	return getChecked('from=', '#from input:checked', path);
}

var addRegion = function (path) {
	return getChecked('region=', '#region input:checked', path);
}

var addMark = function (path) {
	return getChecked('mark=', '#mark input:checked', path);
}

var addYear = function (path) {
	return getRange(path, 'year=', '#fromYear', '#toYear', 'Год некорректен');
}

var addMileage = function (path) {
	return getRange(path, 'mileage=', '#fromMileage', '#toMileage', 'Пробег некорректен');
}

var addPrice = function (path) {
	return getRange(path, 'price=', '#fromPrice', '#toPrice', 'Цена некорректена');
}

var addKpp = function (path) {
	return getChecked('kpp=', '#kpp input:checked', path);
}

var goToIndex = function (path) {
	if(errors != '') {
		alert(errors);
	} else {
		document.location.href = "/index.php?" + path;
	}
}